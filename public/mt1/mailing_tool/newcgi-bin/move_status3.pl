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

my $util = util->new;
my $sth;
my $sth1;
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

# connect to the util database

$| = 1;
my $dbhq;
my $dbhu;
($dbhq,$dbhu)=$util->get_dbh();

$sql = "select max(email_user_id) from email_list";
$sth = $dbhq->prepare($sql);
$sth->execute();
($end) = $sth->fetchrow_array();
$sth->finish();

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
	my $temp_id;
	my $email_type;
	my $the_email;
	my $filename;
	my $filecnt;
	my $curcnt;

	# Get the mail information for the campaign being used
	$filecnt = 1;

	$curcnt = 1;
	$cnt = 0;
	$total_cnt = 0;
	$aol_cnt = 0;
		
	$begin = 187200000;
	$list_cnt = 0;
	while ($begin < $end)
	{	
		$bend = $begin + 99999;
		if ($bend > $end)
		{
			$bend = $end;
		}
		$sql = "select email_user_id from email_list where email_user_id between $begin and $bend and status='3'"; 
		print "$begin $bend\n";
unless ($dbhq && $dbhq->ping) {
print "connecting\n";
($dbhq,$dbhu)=$util->get_dbh();
   }
			$sth = $dbhq->prepare($sql);
			$sth->execute();
			while (($email_user_id) = $sth->fetchrow_array())
			{
				$sql = "update email_list set status='A',unsubscribe_date=null,unsubscribe_time=null where email_user_id=$email_user_id and status='3'";
unless ($dbhu && $dbhu->ping) {
print "connecting\n";
($dbhq,$dbhu)=$util->get_dbh();
   }
				$rows=$dbhu->do($sql);
			}
			$sth->finish();
			$begin = $begin + 100000;	
		}
		print "Done\n";
}
sub get_list_id
{
	my ($date_str) = @_;	
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
my $rest_str;
my $sth;
my $list_id;
my @mon_str = (
'','Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec' );
	print "input date string <$date_str>\n";
	if (($date_str eq "") || ($date_str eq "0000-00-00"))
	{
		($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime();
    	$year = $year + 1900;
    	$mon = $mon + 1;
	}
	else
	{
		($year,$mon,$rest_str) = split("-",$date_str,3);
	}
	if ($year < 2005)
	{
		$list_str = $year;
	}
	else
	{
		$list_str = $mon_str[$mon] . " " . $year;
	}
	$sql = "select list_id from list where list_name='$list_str' and user_id=2";
    $sth = $dbhq->prepare($sql);
    $sth->execute();
    ($list_id) = $sth->fetchrow_array();
    $sth->finish();
	return $list_id;
}
