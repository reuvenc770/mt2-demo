#!/usr/bin/perl
# *****************************************************************************************
# update_list_cnt.pl
#
# History
# Jim Sobeck,   04/09/03,   Created
# *****************************************************************************************

use strict;
use lib "/var/www/html/newcgi-bin";
use util;
use util_mail;

my $util = util->new;
my $notfound;
my $i;
my $sth;
my $sth1;
my $user_id;
my $sth2;
my $dbh;
my $sql;
my $rows;
my $cdate = localtime();
my $program = "update_list_cnt.pl";
my $errmsg;
my $bin_dir_http;
my $cnt;
my $total_cnt;
my $aol_cnt;
my $list_aol_cnt;
my $list_hotmail_cnt;
my $list_msn_cnt;
my $list_cnt;
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
my ($gender);
my ($first_name,  $middle_name,      $last_name);
my ($birth_date,  $address,          $address2);
my ($city,        $state,            $zip);
my ($country,     $marital_status,   $occupation);
my ($job_status,  $household_income, $education_level);
my ($date_captured, $member_source, $phone, $source_url);
my @BAD_WORDS = (
"thelocalgig.com",
"brightyellow.net"
);
#
#  Set up array for servers
#
my $sarr_cnt = 6;
my $cnt2;
my $EM;
my $EM1;
my $cnt;
my $eid;
my $email;

# connect to the util database

$| = 1;
my $dbhq;
my $dbhu;
my $domain_id;
($dbhq,$dbhu)=$util->get_dbh();
#
# go through each eid to see if any revenue
#
my $sid_str="";
$sql="select sid from advertiser_info where advertiser_name like '%op cpc%' or advertiser_name like '%ppc%' and sid > 0";
my $sth1a=$dbhu->prepare($sql);
$sth1a->execute();
my $sid;
while (($sid)=$sth1a->fetchrow_array())
{
	$sid_str=$sid_str.$sid.",";
}
$sth1a->finish();
chop($sid_str);

$sql = "select domain_id from email_domains where suppressed=1 and datesupp=date_sub(curdate(),interval 1 day)";
$sth1a = $dbhu->prepare($sql);
$sth1a->execute();
while (($domain_id) = $sth1a->fetchrow_array())
{
	$EM={};
	$EM1={};
	print "Domain <$domain_id>\n";
	mail_send();
	$cnt=0;
	foreach (keys %$EM) 
	{
		$eid=$_;
		$email=$EM->{$eid};
		if ($cnt > 0)
		{
			next;
		}
		print "<$eid> <$email>\n";
		$sql="select count(*) from HitpathApiData where effectiveDate > '2009-02-01' and amount > 0 and sid not in ('$sid_str') and email_user_id=?";
		$sth=$dbhu->prepare($sql);
		$sth->execute($eid);
		($cnt)=$sth->fetchrow_array();
		$sth->finish();
		if ($cnt > 0)
		{
			print "Got revenue - dont remove domain - $domain_id\n";
			next;
		}
	}
	if ($cnt > 0)
	{
		$sql="update email_domains set suppressed=0,dateSupp=null where domain_id=$domain_id";
		print "Adding domain back <$sql>\n";
		my $rows=$dbhu->do($sql);
	}
	else
	{
		foreach (keys %$EM) 
		{
			$eid=$_;
			$email=$EM->{$eid};
			$sql="update email_list set status='U',unsubscribe_date=curdate(),unsubscribe_time=curtime() where email_user_id=$eid";
			$rows=$dbhu->do($sql);
			$sql="insert into unsub_log(email_addr,unsub_date,client_id) values('$email',now(),$EM1->{$eid})";
			$rows=$dbhu->do($sql);
		}
	}
}
$sth1a->finish();
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
	my $rows;
	my $table;

	# Get the mail information for the campaign being used
	$filecnt = 1;

	$sql = "select list.list_id,list_name,user_id from list where status='A' order by list.list_id desc";
	$sth2 = $dbhq->prepare($sql);
	$sth2->execute();
	while (($list_id,$list_name,$user_id) = $sth2->fetchrow_array())
	{
		if ($user_id == 276)
		{
			$table="sv_email_list";
		}
		else
		{
			$table="email_list";
		}
		$sql = "select email_addr,email_user_id from $table where list_id=? and domain_id=? and status='A'";
		$sth = $dbhq->prepare($sql);
		$sth->execute($list_id,$domain_id);
		while (($cemail,$email_user_id) = $sth->fetchrow_array())
		{
			if ($user_id !=276)
			{
				$EM->{$email_user_id}=$cemail;
				$EM1->{$email_user_id}=$user_id;
			}
			else
			{
				$sql="update $table set status='U',unsubscribe_date=curdate(),unsubscribe_time=curtime() where email_user_id=$email_user_id";
				$rows=$dbhu->do($sql);
				$sql="insert into unsub_log(email_addr,unsub_date,client_id) values('$cemail',now(),$user_id)";
				$rows=$dbhu->do($sql);
			}
		}
		$sth->finish();
	}
	$sth2->finish();
}
