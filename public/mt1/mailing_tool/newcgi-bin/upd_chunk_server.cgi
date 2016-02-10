#!/usr/bin/perl

# *****************************************************************************************
# upd_chunk_server.cgi
#
# this page updates select list based on brand_id
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
	parent.main.addSERVER(value,text);
}
</script>
</head>
<body>
<script language="JavaScript">
end_of_html
$sql="select distinct server_config.id,server from server_config,server_ip_config,brand_ip bi where server_config.id=server_ip_config.id and type in ('mailer','strmail') and inService=1 and bi.ip=server_ip_config.ip and bi.brandID=$bid order by server";
$sth = $dbhq->prepare($sql) ;
$sth->execute();
my $id;
my $sname;
while (($id,$sname) = $sth->fetchrow_array())
{
	print "doit(\"$id\",\"$sname\");\n";
}
$sth->finish();
print "parent.main.update_ip();\n";
print "</script>\n";
print "</body>\n";
print "</html>\n";
$util->clean_up();
exit(0);
