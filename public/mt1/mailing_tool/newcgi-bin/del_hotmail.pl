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

$sql = "select max(email_user_id) from mail.member_list";
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
		
	$begin = 0;
	$list_cnt = 0;
	while ($begin < $end)
	{	
		$bend = $begin + 999999;
		if ($bend > $end)
		{
			$bend = $end;
		}
		$sql = "select email_addr,email_user_id,capture_date from mail.member_list where email_user_id between $begin and $bend"; 
			$sth = $dbhq->prepare($sql);
			$sth->execute();
			while (($cemail,$email_user_id,$cdate) = $sth->fetchrow_array())
			{
				# if email_type is blank - then default it to H just in case
				$cemail =~ tr/[A-Z]/[a-z]/;
				$_ = $cemail;
				$addrec = 0;
				print "Email $cemail - $cdate\n";
				if ((/\@hotmail.com/) || (/\@msn.com/) || (/\@email.msn.com/))
				{
					$sql = "delete from mail.member_list where email_addr='$cemail'";
					$rows=$dbhu->do($sql);
				}
			}
			$sth->finish();
			$begin = $begin + 1000000;	
		}
		print "Done\n";
}
