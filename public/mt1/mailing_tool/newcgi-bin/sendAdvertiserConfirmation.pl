#!/usr/bin/perl
#
use strict;
use DBI;
use MIME::Lite;
use lib "/var/www/html/newcgi-bin";
use util;
my $util = util->new;

my $dbhq;
my $dbhu;
my $sql;
my $sth;
my $aid;
my $aname;
my $confirm_email;
my $cc_email_addr="offerczar\@zetainteractive.com";
my $mail_mgr_addr="info\@zetainteractive.com";
($dbhq,$dbhu)=$util->get_dbh();
$|=1;

$sql="select ai.advertiser_id,advertiser_name,confirmation_email from advertiser_info ai, AdvertiserConfirmation ac where ac.advertiser_id=ai.advertiser_id and ac.send_datetime <= now()";
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($aid,$aname,$confirm_email)=$sth->fetchrow_array())
{
	if ($confirm_email eq "")
	{
		next;
	}
	print "Sending email for $aname($aid) to $confirm_email\n";
	$sql="delete from AdvertiserConfirmation where advertiser_id=$aid";
	my $rows=$dbhu->do($sql);
   	open (MAIL,"| /usr/sbin/sendmail -t");
   	print MAIL "From: OfferCzar <$mail_mgr_addr>\n";
   	print MAIL "To: $confirm_email\n";
  	print MAIL "Cc: $cc_email_addr\n";
   	print MAIL "Subject: Your requested campaign: $aname was sent 2 days ago and is not ready for review\n";
	print MAIL "Your requested campaign: $aname was sent 2 days ago and is now ready for review. You can access the detailed stats at our tableau reporting site: (http://bireporting01.i.routename.com/auth?destination=%2F) under the workbook Summary-Sale, NewOfferDash.\n\nThank You.\nOfferCzar\n";
	close(MAIL);
}
$sth->finish();
