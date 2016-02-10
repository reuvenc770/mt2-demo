#!/usr/bin/perl
# *****************************************************************************************
# process_camp_log.pl
#
# History
# Jim Sobeck,   08/03/06,   Created
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
my $errmsg;
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
my $program;
#
#  Set up array for servers
#
my $cnt2;

# connect to the util database

$| = 1;

$util->db_connect();
$dbh = $util->get_dbh;
$dbh1=DBI->connect('DBI:mysql:new_mail:update2.routename.com', 'db_user', 'sp1r3V') or die "can't connect to db: $!";

# Send any mail that needs to be sent
mail_send();

$util->clean_up();
exit(0);

# ***********************************************************************
# This routine is used for sending all email for a single campaign
# ***********************************************************************
sub mail_send
{

	my $log_id;
	my $camp_id;
	my $open_cnt;
	my $click_cnt;
	my $old_camp;
	my $ocnt;
	my $ccnt;

	$old_camp=0;
	$sql = "select log_id,campaign_id,open_cnt,click_cnt from tmp_camp_log order by campaign_id";
	$sth1 = $dbh1->prepare($sql);
	$sth1->execute();
	$ocnt=0;
	$ccnt=0;
	while (($log_id,$camp_id,$open_cnt,$click_cnt) = $sth1->fetchrow_array())
	{
		if ($old_camp == 0)
		{
			$old_camp = $camp_id;
		}		
		if ($old_camp != $camp_id)
		{
			$sql="update campaign_log set open_cnt=open_cnt+$ocnt,click_cnt=click_cnt+$ccnt where campaign_id=$old_camp";
			print "$sql\n";		
			$rows=$dbh->do($sql);
                        if ($dbh->err() != 0)
                        {
                        	$errmsg = $dbh->errstr();
                        	print "Error updating campaign: $sql : $errmsg";
			}		
			$old_camp=$camp_id;
			$ocnt=0;
			$ccnt=0;
		}
		$ocnt=$ocnt+$open_cnt;
		$ccnt=$ccnt+$click_cnt;
		$sql="delete from tmp_camp_log where log_id=$log_id";
		$rows=$dbh1->do($sql);
	}
	$sth1->finish();
	if ($ocnt > 0 or $ccnt > 0)
	{
		$sql="update campaign_log set open_cnt=open_cnt+$ocnt,click_cnt=click_cnt+$ccnt where campaign_id=$old_camp";
		$rows=$dbh->do($sql);
                        if ($dbh->err() != 0)
                        {
                        	$errmsg = $dbh->errstr();
                        	print "Error updating campaign: $sql : $errmsg";
			}		
	}
}
