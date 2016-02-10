#!/usr/bin/perl

# *****************************************************************************************
# upd_ip_addr.cgi
#
# this page updates select list based on server_id 
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
my $cid;
my $aname;
my $errmsg;
my $images = $util->get_images_url;

# connect to the util database
my ($dbhq,$dbhu)=$util->get_dbh();

my $sid = $query->param('sid');
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
	parent.main.addIP(value,text);
}
doit(0,"Select One or More");
</script>
</head>
<body>
<script language="JavaScript">
end_of_html
$sql="select bi.ip from server_ip_config sic, brand_ip bi where id=$sid and active=1 and mail=1 and bi.ip=sic.ip and bi.brandID=$bid order by bi.ip";
$sth = $dbhq->prepare($sql) ;
$sth->execute();
my $ip;
while (($ip) = $sth->fetchrow_array())
{
	print "doit(\"$ip\",\"$ip\");\n";
}
$sth->finish();
print "</script>\n";
print "</body>\n";
print "</html>\n";
$util->clean_up();
exit(0);
