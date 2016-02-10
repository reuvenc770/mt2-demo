#!/usr/bin/perl

# *****************************************************************************************
# upd_ip.cgi
#

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
###$util->db_connect();

my ($dbhq,$dbhu)=$util->get_dbh();
###$dbh = $util->get_dbh;

my $ahost = $query->param('ahost');
#
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>CREATE EMAIL</title>
<script language="JavaScript">
function doit2(value,text)
{
	parent.main.addIP(value,text);
}
</script>
</head>
<body>
<script language="JavaScript">
end_of_html
##$sql="select ip_addr from aol_server_ip where id = (select id from server_config where server='$ahost') order by ip_addr"; 
## deprecating use of aol_server_ip since we have server_ip_config - jp Thu Dec 29 11:22:43 EST 2005
$sql="SELECT ip FROM server_config sc, server_ip_config sic WHERE sc.id=sic.id AND sc.server=? AND mail=1 ORDER BY ip ASC";
$sth = $dbhq->prepare($sql) ;
$sth->execute($ahost);
while (($aname) = $sth->fetchrow_array())
{
	print "doit2(\"$aname\",\"$aname\");\n";
}
$sth->finish();
print "</script>\n";
print "</body>\n";
print "</html>\n";
$util->clean_up();
exit(0);
