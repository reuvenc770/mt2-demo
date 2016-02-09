#!/usr/bin/perl
# *****************************************************************************************
# preprocess_send_mail.pl
#
# Batch program that runs from cron to send the emails
# schedule the email
#
# History
# Jim Sobeck,   08/07/01,   Created
# Jim Sobeck,   04/23/02,   Added Special Logic for Campaign 28
# Jim Sobeck,	04/30/02,	Added logic for max_emails and first_email_user_id
# Jim Sobeck,	05/06/02,	Added logic for creating files per server
# Jim Sobeck,	09/30/02,	Add logic for permission pass for offersforu
# *****************************************************************************************

# send_email.pl 

use strict;
use lib "/var/www/pms/src";
use pms;
use pms_mail;

my $pms = pms->new;
my $EDEALSDIRECT_USER = 33;
my $IMEDIA_USER = 37;
my $OFFERSFORU_CID = 226;
my $DEALSEEKER_CID = 43;
my $TEST_CID = 635;
my $STATE_USER = 51;
my $sth;
my $sth1;
my $sth2;
my $sth3;
my $dbh;
my $sql;
my $rows;
my $cdate = localtime();
my $program = "send_email.pl";
my $errmsg;
my $email_mgr_addr;
my $bin_dir_http;
my $records_per_file = 10000;
my $max_files = 40000;
my $cnt;
my $total_cnt;
my $aol_cnt;
my $list_aol_cnt;
my $list_cnt;
my $last_email_user_id;
my $max_emails;
my $first_email_user_id;
#
#  Set up array for servers
#
my $sarr_cnt = 9;
my $cnt2;
my @sarry = (
	["jjdb","2"],
	["dbbox","2"],
	["mail1","2"],
	["mail2","2"],
	["mail3","2"],
	["mail4","2"],
	["mail5","2"],
	["mail6","2"],
	["jjweb1","2"]
);
#
#  Set up array for bigcoop
#
my $arr_cnt = 86;
my $cnt1;

# connect to the pms database

$| = 1;

$pms->db_connect();
$dbh = $pms->get_dbh;


mail_send();

$pms->clean_up();
exit(0);

sub mail_send
{
#	my ($camp_id,$max_emails,$first_email_user_id,$user_id) = @_;
	my $camp_id = 0;
	my $subject;
	my $from_addr;
	my $read_receipt;
	my $list_id;
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
	$cnt1 = 0;
	$list_id = 93;	
	print "Getting addresses for list $list_id\n";
	$list_aol_cnt = 0;
	$list_cnt = 0;
	$sql = "select SQL_BUFFER_RESULT email_addr, email_user_id from member_list where list_id = $list_id and status='A'";
	$email_type="H";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	while (($cemail,$email_user_id) = $sth->fetchrow_array())
	{
		# if email_type is blank - then default it to H just in case
		$last_email_user_id = $email_user_id;

		if ($email_type eq "")
		{
			$email_type = "H";
		}
		$cemail =~ tr/[A-Z]/[a-z]/;
		$_ = $cemail;
		if ((/aol.com/) && ($camp_id != $TEST_CID))
		{	
			$aol_cnt++;
			$list_aol_cnt++;
			$list_cnt++;
#			print "AOL - $cemail\n";
		}
		else
		{
			if ($email_user_id > 0)
			{
				$list_cnt++;
				$cnt++;
				$total_cnt++;
				$last_email_user_id = $email_user_id;
			}
		}
	}
	$sth->finish();
	$sql = "update list set member_cnt=$list_cnt,aol_cnt = $list_aol_cnt where list_id = $list_id";
	$rows = $dbh->do($sql);
	if ($dbh->err() != 0)
	{
   		$errmsg = $dbh->errstr();
   		print "Error updating list: $sql : $errmsg";
	}
	$list_aol_cnt = 0;
	$list_cnt = 0;
	$cdate = localtime();
}
