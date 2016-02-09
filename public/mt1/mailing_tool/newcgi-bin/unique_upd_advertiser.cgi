#!/usr/bin/perl

# *****************************************************************************************
# unique_upd_advertiser.cgi
#
# this page updates advertisers
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

my $country= $query->param('country');
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
</script>
</head>
<body>
<script language="JavaScript">
end_of_html
my $cnt;
my $cid;
my $aid;
my $aname;
if ($country eq "")
{
	$sql = "select advertiser_id,advertiser_name from advertiser_info where status='A' and test_flag='N' order by advertiser_name";
}
else
{
	$sql = "select distinct ai.advertiser_id,ai.advertiser_name from advertiser_info ai,Country c where ai.status='A' and ai.test_flag='N' and ai.countryID=$country order by advertiser_name";
}
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($aid,$aname) = $sth->fetchrow_array())
{
	print "doit(\"$aid\",\"$aname\");\n";
}
$sth->finish();
print "</script>\n";
print "</body>\n";
print "</html>\n";
exit(0);
