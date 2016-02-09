#!/usr/bin/perl
# *****************************************************************************************
# update_supplist_cnt.pl
#
# History
# Jim Sobeck,   01/30/04,   Created
# *****************************************************************************************

use strict;
use lib "/var/www/html/newcgi-bin";
use util;
use util_mail;

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
my $email_mgr_addr;
my $bin_dir_http;
my $cnt;
my $total_cnt;
my $aol_cnt;
my $list_aol_cnt;
my $list_hotmail_cnt;
my $list_msn_cnt;
my $list_cnt;
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
my $dbh3 = DBI->connect("DBI:mysql:supp:suppress.routename.com","db_user","sp1r3V");

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
	my $reccnt;

	# Get the mail information for the campaign being used
	$filecnt = 1;

	$cnt2 = 0;
	$curcnt = 1;
	$cnt = 0;
	$total_cnt = 0;
	$aol_cnt = 0;
		
	$sql = "select list_id,list_name from vendor_supp_list_info where last_updated >= date_sub(curdate(),interval 1 day) order by list_id";
	$sth2 = $dbhu->prepare($sql);
	$sth2->execute();
	while (($list_id,$list_name) = $sth2->fetchrow_array())
	{
        print "Processing $list_id - $list_name\n";
		$sql = "select count(*) from vendor_supp_list where list_id=$list_id"; 
		$sth = $dbh3->prepare($sql);
		$sth->execute();
		($reccnt) = $sth->fetchrow_array();
		$sth->finish();
		$sql = "update vendor_supp_list_info set list_cnt=$reccnt where list_id=$list_id"; 
		$rows = $dbhu->do($sql);
        print "List $list_id updated to $reccnt\n";
	}
	$sth2->finish();
}
