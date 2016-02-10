#!/usr/bin/perl
#-----  include Perl Modules ---------
use strict;
use CGI;
use lib "/var/www/html/newcgi-bin";
use util;

#------  get some objects to use later ---------
my $util = util->new;
my $query = CGI->new;
my $sql;
my $sth;
my $cid;
my $aname;
my $cday;
#------  connect to the util database -----------
my $dbhq=DBI->connect('DBI:mysql:new_mail:slavedb.routename.com', 'db_readuser', 'Tr33Wat3r');

my $notify_email_addr="ebushway\@zetainteractive.com";
my $cc_email_addr="wshen\@zetainteractive.com";
my $mail_mgr_addr="info\@zetainteractive.com";
    open (MAIL,"| /usr/sbin/sendmail -t");
    print MAIL "From: $mail_mgr_addr\n";
    print MAIL "To: $notify_email_addr\n";
    print MAIL "Cc: $cc_email_addr\n";
    print MAIL "Subject: Daily Deal - Inactive Advertisers Report\n";
$sql="select dd.client_id,advertiser_name,cday from daily_deals dd,campaign c,advertiser_info ai,user,camp_schedule_info csi where dd.client_id=user.user_id and dd.campaign_id=c.campaign_id and c.advertiser_id=ai.advertiser_id and c.deleted_date is null and ai.status != 'A' and user.status='A' and csi.campaign_id=c.campaign_id";
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($cid,$aname,$cday)=$sth->fetchrow_array())
{
	print MAIL "$cid,$aname,$cday\n";
}
$sth->finish();
close(MAIL);
