#!/usr/bin/perl

# *****************************************************************************************
# sm2_upd_surl.cgi
#
# this page updates source url 
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

# connect to the util database
my ($dbhq,$dbhu)=$util->get_dbh();

my $gid = $query->param('gid');
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
	parent.main.addSurl(value,text);
}
</script>
</head>
<body>
<script language="JavaScript">
end_of_html
my $cnt;
my $cid;
$sql = "select count(*) from ClientGroupClients where client_group_id=?"; 
$sth = $dbhq->prepare($sql);
$sth->execute($gid);
($cnt)=$sth->fetchrow_array();
$sth->finish();
print "doit(\"ALL\",\"ALL\");\n";
if ($cnt == 1)
{
	$sql = "select client_id from ClientGroupClients where client_group_id=?"; 
	$sth = $dbhq->prepare($sql);
	$sth->execute($gid);
	($cid)=$sth->fetchrow_array();
	$sth->finish();

	my $turl;
	my $tcnt;
	$sql = "select url,count(*) from SourceUrlSummary sus, source_url su where sus.url_id=su.url_id and sus.client_id=? and sus.effectiveDate >= date_sub(curdate(),interval 30 day) and su.url != '' and su.url != '.' group by 1 order by 2 desc limit 5";
	$sth = $dbhq->prepare($sql);
	$sth->execute($cid);
	while (($turl,$tcnt) = $sth->fetchrow_array())
	{
		my $temp_str=$turl." - ".$tcnt;
		print "doit(\"$turl\",\"$temp_str\");\n";
	}
	$sth->finish();
}
print "</script>\n";
print "</body>\n";
print "</html>\n";
exit(0);
