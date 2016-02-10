#!/usr/bin/perl

# *****************************************************************************************
# daily_upd_creative.cgi
#
# this page updates select creatives based on advertiser_id
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
my $aname;
my $copywriter;
my $crid;
my $errmsg;
my $images = $util->get_images_url;
my $cfrom;
my @from_array;
my $sth1;
my $temp_str;

# connect to the util database
my ($dbhq,$dbhu)=$util->get_dbh();

my $aid = $query->param('aid');
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
	parent.bottom.addCreative(value,text);
}
function doit1(value,text)
{
	parent.bottom.addSubject(value,text);
}
function doit2(value,text)
{
	parent.bottom.addFrom(value,text);
}
</script>
</head>
<body>
<script language="JavaScript">
end_of_html
$sql = "select creative_id, creative_name from creative where status='A' and advertiser_id=$aid order by creative_name";
$sth = $dbhq->prepare($sql);
$sth->execute();
my $cid;
my $cname;
print "doit(0,\"Default\");\n";
while (($cid,$cname) = $sth->fetchrow_array())
{
	$cname=~s/"/\\"/g;
	$temp_str = $cid . " - " . $cname; 
	print "doit($cid,\"$temp_str\");\n";
}
$sth->finish();
$sql = "select subject_id, advertiser_subject from advertiser_subject where status='A' and advertiser_id=$aid order by advertiser_subject";
$sth = $dbhq->prepare($sql);
$sth->execute();
my $cid;
my $cname;
print "doit1(0,\"Default\");\n";
while (($cid,$cname) = $sth->fetchrow_array())
{
	$cname=~s/"/\\"/g;
	$temp_str = $cid . " - " . $cname; 
	print "doit1($cid,\"$temp_str\");\n";
}
$sth->finish();

$sql = "select from_id, advertiser_from from advertiser_from  where status='A' and advertiser_id=$aid order by advertiser_from";
$sth = $dbhq->prepare($sql);
$sth->execute();
my $cid;
my $cname;
print "doit2(0,\"Default\");\n";
while (($cid,$cname) = $sth->fetchrow_array())
{
	$cname=~s/"/\\"/g;
	$temp_str = $cid . " - " . $cname; 
	print "doit2($cid,\"$temp_str\");\n";
}
$sth->finish();
print "</script>\n";
print "</body>\n";
print "</html>\n";
$util->clean_up();
exit(0);
