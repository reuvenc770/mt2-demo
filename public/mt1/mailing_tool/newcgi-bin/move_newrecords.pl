#!/usr/bin/perl
# *****************************************************************************************
# move_newrecords.pl
#
# History
# Jim Sobeck,   01/18/06,   Created
# *****************************************************************************************

use strict;
use lib "/var/www/html/newcgi-bin";
use util;

my $util = util->new;
my $sth;
my $sth1;
my $sth2;
my $dbh;
my $dbh1;
my $sql;
my $wait_days = 3;
my $rows;
my $cdate = localtime();
my $sdate;
my $errmsg;
my $cnt;
my $total_cnt;
my $aol_cnt;
my $list_aol_cnt;
my $list_hotmail_cnt;
my $list_msn_cnt;
my $list_cnt;
my $cstatus;
my $email_addr;
my $list_yahoo_cnt;
my $list_foreign_cnt;
my $last_email_user_id;
my $max_emails;
my $clast60;
my $aolflag;
my $openflag;
my $first_email_user_id;
my $addrec;
my $begin;
my $end;
my $list_str;
my $bend;
my $program;
my $max_class;
my $filename="move_newrecords.pl";
#
#  Set up array for servers
#
my $cnt2;

# connect to the util database

$| = 1;

my $check_string="/bin/ps -elf | /bin/grep -v grep | /bin/grep -v $$ | /bin/grep -v vi | grep -v pipe_w | /bin/grep -c $filename";
my $alreadyRunning=`$check_string`;
chomp($alreadyRunning);
print "Count <$alreadyRunning>\n";
exit if $alreadyRunning;

$util->db_connect();
$dbh = $util->get_dbh;
$util->db_connect1();
$dbh1 = $util->get_dbh1;
my $dbh2=DBI->connect('DBI:mysql:new_mail:', 'db_user', 'sp1r3V') or die "can't connect to db: $!";

$sql="select max(class_id) from email_class"; 
$sth1 = $dbh2->prepare($sql);
$sth1->execute();
($max_class)=$sth1->fetchrow_array();
$sth1->finish();

# Send any mail that needs to be sent
mail_send();

$util->clean_up();
exit(0);

# ***********************************************************************
# This routine is used for sending all email for a single campaign
# ***********************************************************************
sub mail_send
{
	my $subject;
	my $from_addr;
	my $list_id;
	my $list_name;
	my $email_user_id;
	my $cemail;
	my $email_type;
	my $the_email;
	my $filename;
	my $filecnt;
	my $curcnt;
	my $client_id;
	my $max_clients;
	my $new_list_id;
	my $move_cnt;
	my $tab;
	my $domain_id;
	my $domain_str;

	$sql="select domain_id from email_domains where domain_class != 4";
	$domain_str="";
	$sth1 = $dbh2->prepare($sql);
	$sth1->execute();
	while (($domain_id) = $sth1->fetchrow_array())
	{
		$domain_str = $domain_str . $domain_id . ",";
	}
	$sth1->finish();
	$_=$domain_str;
	chop;
	$domain_str = $_;

	# Get the mail information for the campaign being used
	$sql = "select user_id,tab from user where status='A' and user_id not in(1,92) order by user_id desc"; 
	$sth1 = $dbh2->prepare($sql);
	$sth1->execute();
	while (($client_id,$tab) = $sth1->fetchrow_array())
	{
		$sql = "select list_id from list where user_id=$client_id and list_name='Newest Records'"; 
		$sth2 = $dbh2->prepare($sql);
		$sth2->execute();
		($new_list_id) = $sth2->fetchrow_array();
		$sth2->finish();
		if ($new_list_id eq "")
		{
			print "No list for $client_id\n";
			next;
		}
#
		print "Processing <$new_list_id>\n";
		$sql = "select email_user_id,email_addr,capture_date,status,subscribe_date from email_list where list_id = $new_list_id and subscribe_date < date_sub(curdate(),interval 3 day) and status ='A' and domain_id not in ($domain_str)"; 
unless ($dbh1 && $dbh1->ping) {
print "connecting - dbh1\n";
$util->db_connect1();
$dbh1 = $util->get_dbh1;
   }
		$sth = $dbh1->prepare($sql);
		$sth->{mysql_use_result}=1;
		$sth->execute();
		while (($email_user_id,$email_addr,$cdate,$cstatus,$sdate) = $sth->fetchrow_array())
		{
			if ($cdate eq "0000-00-00 00:00:00")
			{
				$cdate=$sdate;
			}
			$list_id = get_list_id($cdate,$client_id);
			if ($list_id == 0)
			{
				print "NO LIST: $cdate $client_id\n";
			}
			else
			{
#			print "moving $email_addr to $list_id\n";
			$sql = "update email_list set subscribe_date=curdate(),subscribe_time=curtime(),list_id=$list_id,status='$cstatus' where email_user_id=$email_user_id and status in ('A','P')";
unless ($dbh && $dbh->ping) {
print "connecting\n";
$util->db_connect();
$dbh = $util->get_dbh;
   }
			$rows = $dbh->do($sql);
if ($dbh->err() != 0)
{
unless ($dbh && $dbh->ping) {
print "connecting\n";
$util->db_connect();
$dbh = $util->get_dbh;
   }
	$rows=$dbh->do($sql);
}
			}
		}
		$sth->finish();
#
# Now do non-others domains
#
my $i=1;
my $domain_str1;
while ($i <= $max_class)
{
	if ($i == 4)
	{
		$i++;
		next;
	}
	$sql="select domain_id from email_domains where domain_class = $i";
	$domain_str1="";
	my $sth1a = $dbh2->prepare($sql);
	$sth1a->execute();
	while (($domain_id) = $sth1a->fetchrow_array())
	{
		$domain_str1 = $domain_str1 . $domain_id . ",";
	}
	$sth1a->finish();
	$_=$domain_str1;
	chop;
	$domain_str1 = $_;
	$i++;
	if ($domain_str1 eq "")
	{
		next;
	}
		$sql = "select email_user_id,email_addr,capture_date,status,subscribe_date from email_list where list_id = $new_list_id and subscribe_date < date_sub(curdate(),interval 3 day) and status in ('A','P') and domain_id in ($domain_str1)"; 
unless ($dbh1 && $dbh1->ping) {
print "connecting dbh1\n";
$util->db_connect1();
$dbh1 = $util->get_dbh1;
   }
		$sth = $dbh1->prepare($sql);
		$sth->{mysql_use_result}=1;
		$sth->execute();
		while (($email_user_id,$email_addr,$cdate,$cstatus,$sdate) = $sth->fetchrow_array())
		{
			if ($cdate eq "0000-00-00 00:00:00")
			{
				$cdate=$sdate;
			}
			$list_id = get_list_id($cdate,$client_id);
			if ($list_id == 0)
			{
				print "NO LIST: $cdate $client_id\n";
			}
			else
			{
#			print "moving $email_addr to $list_id\n";
			$sql = "update email_list set subscribe_date=curdate(),subscribe_time=curtime(),list_id=$list_id,status='$cstatus' where email_user_id=$email_user_id and status in ('A','P')";
unless ($dbh && $dbh->ping) {
print "connecting\n";
$util->db_connect();
$dbh = $util->get_dbh;
   }
			$rows = $dbh->do($sql);
if ($dbh->err() != 0)
{
unless ($dbh && $dbh->ping) {
print "connecting\n";
$util->db_connect();
$dbh = $util->get_dbh;
   }
	$rows=$dbh->do($sql);
}
			}
		}
		$sth->finish();
	}		
	}
	$sth1->finish();
}

sub get_list_id
{
   my ($date_str,$client_id) = @_;
my $sec;
my $min;
my $hour;
my $mday;
my $mon;
my $year;
my $wday;
my $yday;
my $isdst;
my $rest_str;
my $list_str;
my $sth2f;
my $temp_str;
my $list_id;
	$list_id=0;
    if (($date_str eq "") || ($date_str eq "0000-00-00"))
    {
        ($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime();
        $year = $year + 1900;
        $mon = $mon + 1;
    }
    else
    {
        ($temp_str,$rest_str) = split(" ",$date_str,2);
        ($year,$mon,$mday) = split("-",$temp_str,3);
    }
    if ($year < 2005)
    {
		if (($client_id == 2) && ($year == 2004))
		{
            $list_str = $year . "-" . $mon;
		}
		else
		{
        	$list_str = $year;
		}
    }
    else
    {
        if (($mon < 10) && (length($mon) == 1))
        {
            $list_str = $year . "-0" . $mon;
        }
        else
        {
            $list_str = $year . "-" . $mon;
        }
    }
    $sql = "select list_id from list where list_name='$list_str' and user_id=$client_id";
    $sth2f = $dbh2->prepare($sql);
    $sth2f->execute();
    if (($list_id) = $sth2f->fetchrow_array())
	{
    	$sth2f->finish();
	}
	else
	{
    	$sql = "select list_id from list where user_id=$client_id and list_name like '2%' order by list_name asc limit 1";
    	$sth2f = $dbh2->prepare($sql);
    	$sth2f->execute();
    	($list_id) = $sth2f->fetchrow_array();
		$sth2f->finish();
	}
#    print "Got list $list_id for $list_str\n";
    return $list_id;
}
