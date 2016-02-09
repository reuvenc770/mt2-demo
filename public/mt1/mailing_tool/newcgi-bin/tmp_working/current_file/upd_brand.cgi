#!/usr/bin/perl

# *****************************************************************************************
# upd_brand.cgi
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
$util->db_connect();
$dbh = $util->get_dbh;

my $pid = $query->param('pid');
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
doit(0,"N/A");
end_of_html
$sql="select brand_id,brand_name,company from client_brand_info,user where client_id=user_id and user_id in (select client_id from list_profile where profile_id=$pid) order by brand_name";
$sth = $dbh->prepare($sql) ;
$sth->execute();
while (($aid,$aname,$company) = $sth->fetchrow_array())
{
	print "doit($aid,\"$company - $aname\");\n";
}
$sth->finish();
print "</script>\n";
print "</body>\n";
print "</html>\n";
$util->clean_up();
exit(0);
