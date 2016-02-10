#!/usr/bin/perl

# *****************************************************************************************
# company_info_upd.cgi
#
# this page updates select contact and websites based on company_id 
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
	parent.main.addContact(value,text);
}
function doit1(value,text)
{
	parent.main.addWebsite(value,text);
}
</script>
</head>
<body>
<script language="JavaScript">
end_of_html
$sql="select contact_id,contact_name from company_info_contact where company_id=? order by default_flag desc,contact_name asc";
$sth = $dbhq->prepare($sql) ;
$sth->execute($cid);
while (($pid,$pname) = $sth->fetchrow_array())
{
	print "doit($pid,\"$pname\");\n";
}
$sth->finish();
$sql="select website_id,website from company_info_website where company_id=? order by default_flag desc,website asc";
$sth = $dbhq->prepare($sql) ;
$sth->execute($cid);
while (($pid,$pname) = $sth->fetchrow_array())
{
	print "doit1($pid,\"$pname\");\n";
}
$sth->finish();

print "</script>\n";
print "</body>\n";
print "</html>\n";
$util->clean_up();
exit(0);
