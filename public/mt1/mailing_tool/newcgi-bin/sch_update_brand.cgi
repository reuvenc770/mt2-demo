#!/usr/bin/perl

# *****************************************************************************************
# sch_update_brand.cgi
#
# this page updates select list based on third_party_id 
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
my $sth2;
my $pname;
my $host_cnt;
my $dbh;
my $aid;
my $aname;
my $errmsg;
my $images = $util->get_images_url;
my $cfrom;
my @from_array;
my $sth1;

# connect to the util database
###$util->db_connect();

my ($dbhq,$dbhu)=$util->get_dbh();
###$dbh = $util->get_dbh;

my $pid = $query->param('pid');
my $cid = $query->param('cid');
my $tid;
my $profile_id;
($tid,$profile_id) = split('\|',$pid);
my $sid = $query->param('sid');
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
	parent.bottom.addbrand($sid,value,text);
}
</script>
</head>
<body>
<script language="JavaScript">
end_of_html
my $cdate;
my $aname;
my $offer_type;
my $payout;
my $ecpm;
my $incoming_aid;
my $other_cnt;
my $yahoo_cnt;
$sql="select brand_id,brand_name from client_brand_info where client_id=$cid and status='A' and third_party_id=$tid and brand_type='3rd Party' order by brand_name";
$sth1 = $dbhq->prepare($sql) ;
$sth1->execute();
while (($pid,$pname) = $sth1->fetchrow_array())
{
	$sql="select count(distinct server_name) from brand_host where brand_id=$pid and server_type in ('O','Y')";
	$sth2 = $dbhq->prepare($sql) ;
	$sth2->execute();
	($host_cnt) = $sth2->fetchrow_array();
	$sth2->finish();
	$sql="select count(distinct server_name) from brand_host where brand_id=$pid and server_type='O' and server_name not in (select server_name from brand_host where brand_id=$pid and server_type ='Y')";
	$sth2 = $dbhq->prepare($sql) ;
	$sth2->execute();
	($other_cnt) = $sth2->fetchrow_array();
	$sth2->finish();
	$sql="select count(distinct server_name) from brand_host where brand_id=$pid and server_type='Y' and server_name not in (select server_name from brand_host where brand_id=$pid and server_type ='O')";
	$sth2 = $dbhq->prepare($sql) ;
	$sth2->execute();
	($yahoo_cnt) = $sth2->fetchrow_array();
	$sth2->finish();
	my $tcnt = $other_cnt + $yahoo_cnt;
	if ($tcnt < $host_cnt)
	{
		$other_cnt = $other_cnt + ($host_cnt-$tcnt)/2.0;
		$yahoo_cnt = $yahoo_cnt + ($host_cnt-$tcnt)/2.0;
	}
	print "doit($pid,\"$pname\");\n";
}
$sth1->finish();
print "</script>\n";
print "</body>\n";
print "</html>\n";
$util->clean_up();
exit(0);
