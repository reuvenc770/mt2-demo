#!/usr/bin/perl

# *****************************************************************************************
# dataexport_updadv.cgi
#
# History
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $sql;
my $dbh;
my $aname;
my $copywriter;
my $crid;
my $errmsg;
my $images = $util->get_images_url;
my $cfrom;
my @from_array;
my $sth1;

# connect to the util database
my ($dbhq,$dbhu)=$util->get_dbh();

my @catid = $query->param('catid');
my $BusinessUnit = $query->param('BusinessUnit');
my @countryID = $query->param('countryID');
#
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>CREATE EMAIL</title>
<script language="JavaScript">
function doit(value,text)
{
	parent.main.addAdv(value,text);
}
function doit1()
{
	parent.main.clearAdv();
}
</script>
</head>
<body>
<script language="JavaScript">
end_of_html
print "doit1();\n";
my $cstr="";
my $gb_actives=0;
foreach my $c (@catid)
{
	$cstr.=$c.",";
}
chop($cstr);
my $countrystr="";
foreach my $c (@countryID)
{
	if ($c eq "GB_Actives")
	{
		$gb_actives=1;
	}
	else
	{
		$countrystr.=$c.",";
	}
}
chop($countrystr);
$sql = "select advertiser_id,advertiser_name from advertiser_info where status='A'";
if ($cstr ne "")
{
	$sql.=" and category_id in ($cstr)";
}
if ($countrystr ne "")
{
	$sql.=" and countryID in ($countrystr)";
}
if ($gb_actives)
{
	$sql=$sql." and (advertiser_name in (
'UK AccidentCompensationAlliance Virtuoso',
'UK AccidentsDirect2 Virtuoso',
'UK ActiveYouCruise Virtuoso',
'UK AltonTowers Virtuoso',
'UK ASDA Virtuoso',
'UK Butlins Virtuoso',
'UK CAKE_AccidentAdviceHelplineShortForm Virtuoso',
'UK CAKE_CharterhouseClaims-PPI Virtuoso',
'UK CAKE_ECOExpertsSolar Virtuoso',
'UK CAKE_LifeInsuranceQuotes Virtuoso',
'UK CAKE_OpticalExpress Virtuoso',
'UK CAKE_Train2Game Virtuoso ',
'UK CAKE_VanquisCreditCard Virtuoso',
'UK Claim4 Virtuoso',
'UK ClaimLawyers Virtuoso',
'UK CPC Barclaycard Virtuoso',
'UK Cyprus Virtuoso',
'UK ExpertsinMoney Virtuoso',
'UK Homebase Virtuoso',
'UK InjuryClaimNow2 Virtuoso',
'UK JohnLewis Virtuoso',
'UK LouisVuitton Virtuoso',
'UK Majorca Virtuoso',
'UK MarksAndSpencer Virtuoso',
'UK MyPPIQuote Virtuoso',
'UK NAH2 Virtuoso',
'UK PPIRepayment Virtuoso',
'UK Rayban Virtuoso',
'UK RosettaStoneLanguageCourse Virtuoso',
'UK TradeCareer Virtuoso',
'UK UnderdogNAH Virtuoso',
'UK Wowcher Virtuoso'))";
}
$sql=$sql." or (advertiser_name='NA001ComplainerSuppression')";
if ($BusinessUnit eq "Orange")
{
	$sql=$sql." union select 99999999,'Legacy Global Suppression'";
}
$sql=$sql." order by 2";
$sth = $dbhq->prepare($sql);
$sth->execute();
my $aid;
my $aname;
while (($aid,$aname) = $sth->fetchrow_array())
{
	print "doit($aid,\"$aname\");\n";
}
$sth->finish();

print "</script>\n";
print "</body>\n";
print "</html>\n";
$util->clean_up();
exit(0);
