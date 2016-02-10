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
($dbhq,$dbhu)=$util->get_dbh();
my $dbh3 = DBI->connect("DBI:mysql:new_mail:sv-db-9.routename.com","db_user","sp1r3V");

open (FILE, "$ARGV[0]") or die "Can't open file to read\n";
while (<FILE>) {
	chomp;
	$_=~s/\n|\r//;

$sql = "select domain_id from email_domains where domain_name='$_'";
$sth = $dbhq->prepare($sql);
$sth->execute();
($domain_id) = $sth->fetchrow_array();
$sth->finish();
print "Domain <$domain_id>\n";
if ($domain_id > 0)
{
	$sql="update email_domains set suppressed=1,dateSupp=curdate() where domain_id=$domain_id and suppressed=0";
	my $rows=$dbhu->do($sql);
	mail_send();
}
else
{
	$sql="insert into email_domains(domain_name,suppressed,dateSupp) values('$ARGV[0]',1,curdate())";
	my $rows=$dbhu->do($sql);
}
} ## end while loop
close FILE;
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
	my $rows;

	# Get the mail information for the campaign being used
	$filecnt = 1;

	$sql = "select list.list_id,list_name,user_id from list where status='A' order by list.list_id desc";
	$sth2 = $dbhq->prepare($sql);
	$sth2->execute();
	while (($list_id,$list_name,$user_id) = $sth2->fetchrow_array())
	{
		$sql = "select email_addr,email_user_id from email_list where list_id=? and domain_id=? and status='A'";
		$sth = $dbhq->prepare($sql);
		$sth->execute($list_id,$domain_id);
		while (($cemail,$email_user_id) = $sth->fetchrow_array())
		{
			print "Removing $cemail\n";
			$sql="update email_list set status='U',unsubscribe_date=curdate(),unsubscribe_time=curtime() where email_user_id=$email_user_id";
			$rows=$dbhu->do($sql);
			$sql="insert into unsub_log(email_addr,unsub_date,client_id) values('$cemail',now(),$user_id)";
			$rows=$dbhu->do($sql);
		}
		$sth->finish();
	}
	$sth2->finish();
}
