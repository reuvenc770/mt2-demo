#!/usr/bin/perl
#
#------------------------------------------------------------------------
# Purpose: This program checks each scheduled campaign to make sure C/S/F exists 
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
my $uid;
my $uname;
($dbhq,$dbhu)=$util->get_dbh();

my $notify_email_addr="setup.mailing\@zetainteractive.com";
my $cc_email_addr="jsobeck\@zetainteractive.com";
my $mail_mgr_addr="info\@zetainteractive.com";
open (MAIL,"| /usr/sbin/sendmail -t");
print MAIL "From: $mail_mgr_addr\n";
print MAIL "To: $notify_email_addr\n";
print MAIL "Cc: $cc_email_addr\n";
print MAIL "Subject: Inactive Scheduled CSF Alert\n";

$sql="select unq_id,campaign_name from unique_campaign where send_date=date_add(curdate(),interval 1 day) and status!='CANCELLED'";
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($uid,$uname)=$sth->fetchrow_array())
{
	$sql="select count(*) from UniqueCreative uc,creative c where uc.creative_id=c.creative_id and c.status='A' and uc.unq_id=?"; 
	$sth1=$dbhu->prepare($sql);
	$sth1->execute($uid);
	($ccnt)=$sth1->fetchrow_array();
	$sth1->finish();
	$sql="select count(*) from UniqueSubject uc,advertiser_subject c where uc.subject_id=c.subject_id and c.status='A' and uc.unq_id=?"; 
	$sth1=$dbhu->prepare($sql);
	$sth1->execute($uid);
	($scnt)=$sth1->fetchrow_array();
	$sth1->finish();
	$sql="select count(*) from UniqueFrom uc,advertiser_from c where uc.from_id=c.from_id and c.status='A' and uc.unq_id=?"; 
	$sth1=$dbhu->prepare($sql);
	$sth1->execute($uid);
	($fcnt)=$sth1->fetchrow_array();
	$sth1->finish();
	if (($ccnt == 0) or ($scnt == 0) or ($fcnt == 0))
	{
		print MAIL "No Active Creative, Subject, or From  for $uname ($uid)\n";
	}
}
$sth->finish();
close(MAIL);
