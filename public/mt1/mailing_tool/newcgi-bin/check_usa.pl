#!/usr/bin/perl
#
#------------------------------------------------------------------------
# Purpose: This program checks each scheduled campaign to make sure C/S/F exists for USA
#------------------------------------------------------------------------ 
use strict;
use lib "/var/www/html/newcgi-bin";
use util;

my $util = util->new;
my $dbh;
my $rows;
my $sql;
my $sth;
my $sth1;
my $dbhq;
my $dbhu;
my $ccnt;
my $scnt;
my $fcnt;
my $usaid;
my $uname;
my $aid;
my $creative_id;
my $subject_id;
my $from_id;
($dbhq,$dbhu)=$util->get_dbh();

my $notify_email_addr="setup.mailing\@zetainteractive.com";
my $cc_email_addr="jsobeck\@zetainteractive.com";
my $mail_mgr_addr="info\@zetainteractive.com";
open (MAIL,"| /usr/sbin/sendmail -t");
print MAIL "From: $mail_mgr_addr\n";
print MAIL "To: $notify_email_addr\n";
print MAIL "Subject: Inactive USA CSF Alert\n";

$sql="select usa_id,usa.advertiser_id,name,creative_id,subject_id,from_id from UniqueScheduleAdvertiser usa,advertiser_info ai where usa.advertiser_id=ai.advertiser_id and ai.status='A' and ai.test_flag='N'"; 
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($usaid,$aid,$uname,$creative_id,$subject_id,$from_id)=$sth->fetchrow_array())
{
	if ($creative_id > 0)
	{
		$sql="select count(*) from UniqueAdvertiserCreative uc,creative c where uc.creative_id=c.creative_id and c.status='A' and uc.usa_id=?"; 
		$sth1=$dbhu->prepare($sql);
		$sth1->execute($usaid);
	}
	else
	{
		$sql="select count(*) from creative c where c.status='A' and advertiser_id=?"; 
		$sth1=$dbhu->prepare($sql);
		$sth1->execute($aid);
	}
	($ccnt)=$sth1->fetchrow_array();
	$sth1->finish();

	if ($subject_id > 0)
	{
		$sql="select count(*) from UniqueAdvertiserSubject uc,advertiser_subject c where uc.subject_id=c.subject_id and c.status='A' and uc.usa_id=?"; 
		$sth1=$dbhu->prepare($sql);
		$sth1->execute($usaid);
	}
	else
	{
		$sql="select count(*) from advertiser_subject  where status='A' and advertiser_id=?"; 
		$sth1=$dbhu->prepare($sql);
		$sth1->execute($aid);
	}
	($scnt)=$sth1->fetchrow_array();
	$sth1->finish();

	if ($from_id > 0)
	{
		$sql="select count(*) from UniqueAdvertiserFrom uc,advertiser_from c where uc.from_id=c.from_id and c.status='A' and uc.usa_id=?"; 
		$sth1=$dbhu->prepare($sql);
		$sth1->execute($usaid);
	}
	else
	{
		$sql="select count(*) from advertiser_from where status='A' and advertiser_id=?"; 
		$sth1=$dbhu->prepare($sql);
		$sth1->execute($aid);
	}
	($fcnt)=$sth1->fetchrow_array();
	$sth1->finish();
	if (($ccnt == 0) or ($scnt == 0) or ($fcnt == 0))
	{
		print MAIL "No Active Creative, Subject, or From  for $uname ($usaid)\n";
	}
}
$sth->finish();
close(MAIL);
