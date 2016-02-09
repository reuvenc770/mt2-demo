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
my $dbhq;
my $dbhu;
($dbhq,$dbhu)=$util->get_dbh();
$| = 1;

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

	$curcnt = 1;
	$cnt = 0;
	$total_cnt = 0;
	$aol_cnt = 0;
		
	$sql = "select list_id from list where status='A' and user_id = 23 order by list_id";
	$sth=$dbhq->prepare($sql);
	$sth->execute();
	while (($list_id) = $sth->fetchrow_array())
	{
		$sql = "select email_addr,email_user_id from email_list where list_id=$list_id and status in ('A','P') and domain_id in (6,7,8) and subscribe_datetime >= date_sub(curdate,interval 90 day)";
		$sth1 = $dbhq->prepare($sql);
		$sth1->execute();
		while (($cemail,$email_user_id) = $sth1->fetchrow_array())
		{
			print "$cemail\n";
		}
		$sth1->finish();
	}
	$sth->finish();
}
