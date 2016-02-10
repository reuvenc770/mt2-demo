#!/usr/bin/perl

# *****************************************************************************************
# advertiser_exclusion_search.cgi
#
# this page updates select list based on advertiser_id
#
# History
# Jim Sobeck, 03/28/06, Creation
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
my $cid;
my $aname;
my $errmsg;
my $images = $util->get_images_url;
my $cfrom;
my @from_array;

# connect to the util database
my ($dbhq,$dbhu)=$util->get_dbh();
my $aname = $query->param('advertiser_name');
#
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title></title>
<script language="JavaScript">
function doit2(value,text)
{
	parent.main.addaid(value,text);
}
</script>
</head>
<body>
<script language="JavaScript">
parent.main.clear_advertiser();
end_of_html
$sql="select advertiser_id,advertiser_name from advertiser_info where advertiser_name like '%$aname%' and status in ('A','I','S') order by advertiser_name";
$sth = $dbhq->prepare($sql) ;
$sth->execute();
while (($cid,$aname) = $sth->fetchrow_array())
{
	print "doit2($cid,\"$aname\");\n";
}
$sth->finish();
print "</script>\n";
print "</body>\n";
print "</html>\n";
$util->clean_up();
exit(0);
