#!/usr/bin/perl
#===============================================================================
#
#--Change Control---------------------------------------------------------------
#===============================================================================

# include Perl Modules
use strict;
use util;

# get some objects to use later
my $util = util->new;
my ($sth, $sql, $dbh, $errmsg ) ;
my $cname;
my $aname;
my $sname;

#------ connect to the util database ------------------
my $dbhq;
my $dbhu;
($dbhq,$dbhu)=$util->get_dbh();
open (MAIL,"| /usr/sbin/sendmail -t");
my $from_addr = "Assets About to be set Inactive <info\@zetainteractive.com>";
print MAIL "From: $from_addr\n";
print MAIL "To: alerts\@zetainteractive.com\n";
print MAIL "Cc: dpezas\@zetainteractive.com,jtom\@zetainteractive.com\n";
print MAIL "Subject: CFS Inactive Alert\n";
my $date_str = $util->date(6,6);
print MAIL "Date: $date_str\n";
print MAIL "X-Priority: 1\n";
print MAIL "X-MSMail-Priority: High\n";
$sql="select creative_name,advertiser_name from creative,advertiser_info where creative.inactive_date is not null and creative.inactive_date != '0000-00-00' and creative.inactive_date = date_add(curdate(),interval 1 day) and creative.advertiser_id=advertiser_info.advertiser_id and creative.status='A'";
my $sthq = $dbhq->prepare($sql);
$sthq->execute();
while (($cname,$aname) = $sthq->fetchrow_array())
{
	print MAIL "Advertiser $aname - Creative $cname\n";
}
$sthq->finish();
$sql="select advertiser_subject,advertiser_name from advertiser_subject,advertiser_info where advertiser_subject.inactive_date is not null and advertiser_subject.inactive_date != '0000-00-00' and advertiser_subject.advertiser_id=advertiser_info.advertiser_id and advertiser_subject.inactive_date = date_add(curdate(),interval 1 day) and advertiser_subject.status='A'";
my $sthq = $dbhq->prepare($sql);
$sthq->execute();
while (($sname,$aname) = $sthq->fetchrow_array())
{
	print MAIL "Advertiser $aname - Subject $sname\n";
}
$sthq->finish();
$sql="select advertiser_from,advertiser_name from advertiser_from,advertiser_info where advertiser_from.inactive_date is not null and advertiser_from.inactive_date != '0000-00-00' and advertiser_from.advertiser_id=advertiser_info.advertiser_id and advertiser_from.inactive_date = date_add(curdate(),interval 1 day) and advertiser_from.status='A'";
my $sthq = $dbhq->prepare($sql);
$sthq->execute();
while (($sname,$aname) = $sthq->fetchrow_array())
{
	print MAIL "Advertiser $aname - From $sname\n";
}
$sthq->finish();
close MAIL;
