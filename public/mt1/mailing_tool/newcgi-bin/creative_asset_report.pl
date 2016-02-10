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
my ($aid,$aname,$cid,$cname,$cdate);
my $notify_email_addr="bsirikonda\@zetainteractive.com,amohd\@zetainteractive.com,cakerequest\@zetainteractive.com";
#my $notify_email_addr="jsobeck\@zetainteractive.com";
my $mail_mgr_addr="info\@zetainteractive.com";
($dbhq,$dbhu)=$util->get_dbh();
$|=1;

my $tfile="/tmp/creative_asset_".$$.".csv";
open(OUT,">$tfile");
print OUT "Advertiser ID,Advertiser Name,Creative ID,Creative Name,Approved\n";
$sql=qq^select ai.advertiser_id,advertiser_name,creative_id,creative_name,c.date_approved from advertiser_info ai join creative c on c.advertiser_id=ai.advertiser_id where c.status='A' and ai.status='A' order by ai.advertiser_id,c.creative_id^;
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($aid,$aname,$cid,$cname,$cdate)=$sth->fetchrow_array())
{
	print OUT "$aid,$aname,$cid,$cname,$cdate\n";
}
$sth->finish();
close(OUT);
my $subject="Report: Creative Asset Query";
my $msg = MIME::Lite->new(
    From    => $mail_mgr_addr, 
    To      => $notify_email_addr, 
    Subject => $subject, 
    Type    => 'multipart/mixed',
);

$msg->attach(
    Type     => 'text/csv',
    Path     => $tfile, 
    Filename => 'report.csv'
);
$msg->send;
