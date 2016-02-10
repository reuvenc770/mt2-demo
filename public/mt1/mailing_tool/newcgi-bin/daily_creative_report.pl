#!/usr/bin/perl
#-----  include Perl Modules ---------
use strict;
use MIME::Lite;
use DBI;

#------  get some objects to use later ---------
my $sql;
my $sth;
my $sth1;
my $rows;
my ($aname,$cname,$cid,$opens,$revenue,$delivered,$clicks);
my $sales;

my $dbhu=DBI->connect('DBI:mysql:new_mail:db20.i.routename.com', 'db_user', 'sp1r3V');
my $notify_email_addr="despaillat\@zetainteractive.com";
#my $notify_email_addr="jsobeck\@zetainteractive.com";
my $mail_mgr_addr="info\@zetainteractive.com";
my $tfile="/tmp/daily_creative_report_".$$.".csv";
open(MAIL,">$tfile");
print MAIL "OfferName,Creative_name,Creative_ID,Opens,cakeClicks,Revenue,Delivered,Sales\n";

$sql=qq^select
ai.advertiser_name as offerName,
ac.creative_name,
ac.creative_id,
sum(e.opens) as opens,
sum(c.priceReceived * coalesce(cc2.currencyConversionRate,1) ) as earned,
sum(e.delivered) as delivered,
count(*) as cakeClicks,
sum(isSale) as sales

from
new_mail.CakeApiData c
LEFT OUTER JOIN new_mail.CakeCurrency cc1 on c.pricePaidCurrencyID =
cc1.currencyID
LEFT OUTER JOIN new_mail.CakeCurrency cc2 on c.priceReceivedCurrencyID =
cc2.currencyID
JOIN new_mail.EspAdditionalData ed on c.clickID = ed.hitId
JOIN Reporting.EspData e on c.s1 = e.subAffiliateID
LEFT OUTER JOIN new_mail.EspAdvertiserJoin eaj on e.subAffiliateID =
eaj.subAffiliateID
LEFT OUTER JOIN new_mail.advertiser_info ai on eaj.advertiserID = ai.advertiser_id
LEFT OUTER JOIN new_mail.creative ac on c.emailCreativeID = ac.creative_id

where
(clickDate >= date_sub(current_date(), interval 1 day)
or
conversionDate >= date_sub(current_date(), interval 1 day))
and affiliateID = 309
and ac.creative_id is not null
group by
ai.advertiser_name,
ac.creative_name,
ac.creative_id^;
$sth = $dbhu->prepare($sql);
$sth->execute();
while (($aname,$cname,$cid,$opens,$revenue,$delivered,$clicks,$sales) = $sth->fetchrow_array())
{
	print MAIL "$aname,$cname,$cid,$opens,$clicks,$revenue,$delivered,$sales\n";
}
$sth->finish();

close(MAIL);
my $subject="Daily Creative Report";
my $msg = MIME::Lite->new(
    From    => $mail_mgr_addr,
    To      => $notify_email_addr,
    Subject => $subject,
    Type    => 'multipart/mixed',
);

$msg->attach(
    Type     => 'text/csv',
    Path     => $tfile,
    Filename => 'daily_creative_report.csv'
);
$msg->send;
