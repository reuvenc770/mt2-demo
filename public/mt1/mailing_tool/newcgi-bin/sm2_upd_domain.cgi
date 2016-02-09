#!/usr/bin/perl

# *****************************************************************************************
# sm2_upd_domain.cgi
#
# this page updates select domains and ips based on brand_id 
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
my $aid;
my $aname;
my $errmsg;
my $images = $util->get_images_url;
my $cfrom;
my @from_array;
my $sth1;
my $bid;
my $bname;

# connect to the util database
my ($dbhq,$dbhu)=$util->get_dbh();

my $bid = $query->param('bid');
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
	parent.main.addDomain(value,text);
}
function doit1(value,text)
{
	parent.main.addIP(value,text);
}
</script>
</head>
<body>
<script language="JavaScript">
end_of_html
	print "doit(\"ALL\",\"ROTATE ALL\");\n";
#$sql="select distinct domain from brand_available_domains where brandID=$bid union select distinct url from brand_url_info where brand_id=$bid and url_type in ('O','Y') order by 1";
$sql="select domainName from DomainProxyGroup dpg, Domain d where dpg.domainID=d.domainID and dpg.proxyGroupID=$bid order by domainName"; 
$sth = $dbhq->prepare($sql) ;
$sth->execute();
my $domain;
while (($domain) = $sth->fetchrow_array())
{
	print "doit(\"$domain\",\"$domain\");\n";
}
$sth->finish();
print "</script>\n";
print "</body>\n";
print "</html>\n";
$util->clean_up();
exit(0);
