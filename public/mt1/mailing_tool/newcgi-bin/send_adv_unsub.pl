#!/usr/bin/perl
# *****************************************************************************************
# send_adv_unsub.pl
#
# Batch program that runs from cron to send the unsubscribes to the advertisers
#
# History
# Jim Sobeck,   01/07/04,   Created
# *****************************************************************************************

use strict;
use MIME::Lite;
use lib "/var/www/util/src";
use util;
use util_mail;

my $util = util->new;
my $sth;
my $sth1;
my $sth2;
my $sth3;
my $dbh;
my $sql;
my $rows;
my $cdate = localtime();
my $program = "send_adv_unsub.pl";
my $errmsg;
my $cname;
my $min_cnt;
my $max_cnt;
my $transfer_method;
my $total_reccnt;
my $file_date;
my $delimit_char;
my $aol_flag;
my $cnt;

# connect to the util database

$| = 1;
$ENV{PATH} = '/bin:/usr/bin';
$ENV{BASH_ENV} = '';
MIME::Lite->send("sendmail", "/usr/sbin/sendmail.bak -t -oi");
my $dbhq;
my $dbhu;
($dbhq,$dbhu)=$util->get_dbh();

$sql = "select DATE_FORMAT(date_sub(curdate(),INTERVAL 1 DAY),'%m%d%Y')";
$sth = $dbhq->prepare($sql);
$sth->execute();
($file_date) = $sth->fetchrow_array();
$sth->finish();

send_adv();
#$sql = "delete from partner_data";
#$rows = $dbhu->do($sql);
print "Done Processing\n";
$util->clean_up();
exit(0);

# ***********************************************************************
# sub send_adv
# ***********************************************************************

sub send_adv
{
	my $sth2;
	my $aid;
	my $aname;
	my $email_addr;
	my $iemail;

	$sql="select advertiser_id,advertiser_name,email_addr,internal_email_addr from advertiser_info where advertiser_id > 1 and email_addr is not null and email_addr != '' and status='A' order by advertiser_id";
	$sth2=$dbhq->prepare($sql);
	$sth2->execute();
	while (($aid,$aname,$email_addr,$iemail) = $sth2->fetchrow_array())
	{
		$cdate = localtime();
		print "Sending data for Advertiser $aname to $email_addr at $cdate\n";
		send_data($aid,$aname,$email_addr,$iemail);
	}
	$sth2->finish();
}
# ***********************************************************************
# This routine is used for sending data for a partner 
# ***********************************************************************
sub send_data 
{
	my ($aid,$aname,$email_addr,$iemail) = @_;
	my $cemail;
	my $filename;
	my $local_dir;
	my $file_prefix;
	my $subject;
	my $msg;
	
	$subject = "Unsubscribes for $aname for $file_date";
	$filename = "/var/www/util/logs/" . $aid. "_" . $file_date . ".txt";

	open (OUTFILE, "> $filename");
	# Get records from the partner_data table 
	$sql = "select email_addr,DATE_FORMAT(unsub_date,'%m/%d/%Y') from advertiser_unsub where advertiser_id=$aid and unsub_date < curdate() order by unsub_date";
	$sth3 = $dbhq->prepare($sql);
	$sth3->execute();
	$cnt = 0;
	while (($cemail,$cdate) = $sth3->fetchrow_array()) 
	{
		printf OUTFILE "%s,%s\r\n",$cemail,$cdate;
		$cnt++;
	}
	$sth3->finish();
	$cdate = localtime();
	print "Finished sending data for $aname at $cdate\n";
	close OUTFILE;

	if ($cnt > 0)
	{
		if ($iemail eq "")
		{
			$iemail="jsobeck\@lead-dog.net";
		}
		$msg   = MIME::Lite->new(
		From	=> 'Unsubs from Tranzact <info@emaildealz.com>',
 		To      => $email_addr,
		CC		=> $iemail,
  		Subject => $subject,
  		Type    => 'TEXT',
		Data    => ["New data file attached with $cnt records.\n"]);
  		$msg->attach( Type    => 'AUTO', Path    => $filename);
		$msg->send;
		$sql = "delete from advertiser_unsub where advertiser_id=$aid and unsub_date < curdate()";
		$rows = $dbhu->do($sql);
	}
}
