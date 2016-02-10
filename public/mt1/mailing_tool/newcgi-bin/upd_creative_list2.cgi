#!/usr/bin/perl

# *****************************************************************************************
# upd_subject_list2.cgi
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

my $aid = $query->param('aid');
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
	parent.main.addTriggerOption2(value,text);
}
</script>
</head>
<body>
<script language="JavaScript">
doit2(0,"None");
end_of_html
$sql="select creative_id,creative_name from creative where advertiser_id=$aid and ((inactive_date = '0000-00-00') or (inactive_date >= curdate())) and trigger_flag='Y' order by creative_name";
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
