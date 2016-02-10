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
my $sth2;
my $dbh;
my $sql;
my $rows;
my $cdate = localtime();
my $program = "update_list_cnt.pl";
my $errmsg;
my $email_mgr_addr;
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
"katysweet.com"
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
($dbhq,$dbhu)=$util->get_dbh();

$sql = "select max(email_user_id) from member_list";
$sth = $dbhq->prepare($sql);
$sth->execute();
($end) = $sth->fetchrow_array();
$sth->finish();

# lookup the system mail address

$sql = "select parmval from sysparm where parmkey = 'SYSTEM_MGR_ADDR'";
$sth = $dbhq->prepare($sql);
$sth->execute();
($email_mgr_addr) = $sth->fetchrow_array();
$sth->finish();

$sql = "select parmval from sysparm where parmkey = 'BIN_DIR_HTTP'"; 
$sth = $dbhq->prepare($sql); 
$sth->execute();
($bin_dir_http) = $sth->fetchrow_array();
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
	my $email_type;
	my $the_email;
	my $filename;
	my $filecnt;
	my $curcnt;

	# Get the mail information for the campaign being used
	$filecnt = 1;

	$cnt2 = 0;
	$curcnt = 1;
	$cnt = 0;
	$total_cnt = 0;
	$aol_cnt = 0;
		
	$sql = "select list.list_id,list_name from list where status='A' and user_id = 1 order by list.list_id desc";
	$sth2 = $dbhq->prepare($sql);
	$sth2->execute();
	while (($list_id,$list_name) = $sth2->fetchrow_array())
	{
		$begin = 0;
		$list_aol_cnt = 0;
		$list_hotmail_cnt = 0;
		$list_msn_cnt = 0;
		$list_yahoo_cnt = 0;
		$list_foreign_cnt = 0;
		$list_cnt = 0;
		while ($begin < $end)
		{	
			$bend = $begin + 999999;
			if ($bend > $end)
			{
				$bend = $end;
			}
			$sql = "select email_addr,email_user_id,gender,first_name,last_name,birth_date,address,address2,city,state,zip,country,phone,capture_date,member_source,source_url from member_list where list_id = $list_id and status = 'R' and email_user_id between $begin and $bend and unsubscribe_datetime >= '2005-04-14 11:00'";
			$email_type="H";
			$sth = $dbhq->prepare($sql);
			$sth->execute();
			while (($cemail,$email_user_id,$gender,$first_name,$last_name,$birth_date,$address,$address2,$city,$state,$zip,$country,$phone,$date_captured,$member_source,$source_url) = $sth->fetchrow_array())
			{
				$sql="update member_list set status='A',unsubscribe_datetime=null where email_user_id=$email_user_id";
				my $rows = $dbhu->do($sql);
				print "Updating $cemail\n";
			}
			$sth->finish();
			$begin = $begin + 1000000;	
		}
	}
	$sth2->finish();
}
