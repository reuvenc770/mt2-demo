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
my @sarry = (
	["mail11","2"],
	["mail12","2"],
	["mail13","2"],
	["dbbox1","2"],
	["dbbox2","2"],
	["dbbox3","2"]
);

# connect to the util database

$| = 1;
my $dbhq;
my $dbhu;
my $domain_id;
my $camp_id;
my $camp_name;
my $sdate;
my $tcamp_id;
my $tdate;
($dbhq,$dbhu)=$util->get_dbh();

$sql = "select campaign_id,campaign_name,scheduled_datetime from campaign where status='$ARGV[0]' and scheduled_date >= curdate() and deleted_date is null";
my $sth1a = $dbhu->prepare($sql);
$sth1a->execute();
while (($camp_id,$camp_name,$sdate) = $sth1a->fetchrow_array())
{
	$sql="select campaign_id from camp_schedule_info where campaign_id=$camp_id";
	my $sth1 = $dbhu->prepare($sql);
	$sth1->execute();
	if (($tcamp_id) = $sth1->fetchrow_array())
	{
	}
	else
	{
		print "Campaign $camp_id <$camp_name> <$sdate>\n";
		$sql="update campaign set deleted_date=now() where campaign_id=$camp_id";
		my $rows=$dbhu->do($sql);
	}
	$sth1->finish();
}
$sth1a->finish();
exit(0);

