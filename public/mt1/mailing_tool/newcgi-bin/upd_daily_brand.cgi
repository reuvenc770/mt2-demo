#!/usr/bin/perl

# *****************************************************************************************
# upd_daily_brand.cgi
#
# this page updates select list based on advertiser_id
#
# History
# Jim Sobeck, 01/19/05, Creation
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
my $company;
my @from_array;

# connect to the util database
###$util->db_connect();

my ($dbhq,$dbhu)=$util->get_dbh();
###$dbh = $util->get_dbh;

my $cid = $query->param('cid');
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
	parent.main.addBrand(value,text);
}
</script>
</head>
<body>
<script language="JavaScript">
end_of_html
$sql="select distinct brand_id,brand_name from client_brand_info where client_id in (1,$cid) and status='A' and purpose='Daily' order by brand_name";
$sth = $dbhq->prepare($sql) ;
$sth->execute();
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
