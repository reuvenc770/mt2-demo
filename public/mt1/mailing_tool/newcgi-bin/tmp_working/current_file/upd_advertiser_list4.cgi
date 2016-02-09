#!/usr/bin/perl

# *****************************************************************************************
# upd_advertiser_list4.cgi
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
my @from_array;

# connect to the util database
$util->db_connect();
$dbh = $util->get_dbh;

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
	parent.main.addOption3(value,text);
}
</script>
</head>
<body>
<script language="JavaScript">
doit(0,"Select One");
end_of_html
if ($cid == -1)
{
	$sql="select advertiser_id,advertiser_name from advertiser_info where status in ('A','S') order by advertiser_name";
}
else
{
	$sql="select advertiser_id,advertiser_name from advertiser_info where category_id=$cid and status in ('A','S') order by advertiser_name";
}
$sth = $dbh->prepare($sql) ;
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
