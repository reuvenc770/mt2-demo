#!/usr/bin/perl

# *****************************************************************************************
# sm2_upd_profile.cgi
#
# this page updates select profiles based on client_id 
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
my $pid;
my $pname;

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
	parent.main.addProfile(value,text);
}
</script>
</head>
<body>
<script language="JavaScript">
end_of_html
$sql="select profile_id,profile_name from list_profile where status='A' and third_party_id=10 and profile_type='3RDPARTY' and client_id=$cid order by profile_name";
$sth = $dbhq->prepare($sql) ;
$sth->execute();
while (($pid,$pname) = $sth->fetchrow_array())
{
	print "doit($pid,\"$pname\");\n";
}
$sth->finish();

print "</script>\n";
print "</body>\n";
print "</html>\n";
$util->clean_up();
exit(0);
