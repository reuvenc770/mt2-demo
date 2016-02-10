#!/usr/bin/perl

# *****************************************************************************************
# sm2_upd_brand.cgi
#
# this page updates select brand based on client_id 
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
$sql="select brand_id,brand_name from client_brand_info where client_id=$cid and status='A' and third_party_id=10 and brand_type='3rd Party' order by brand_name"; 
$sth = $dbhq->prepare($sql) ;
$sth->execute();
while (($bid,$bname) = $sth->fetchrow_array())
{
	print "doit($bid,\"$bname\");\n";
}
$sth->finish();

print "parent.main.update_domain();\n";
print "</script>\n";
print "</body>\n";
print "</html>\n";
$util->clean_up();
exit(0);
