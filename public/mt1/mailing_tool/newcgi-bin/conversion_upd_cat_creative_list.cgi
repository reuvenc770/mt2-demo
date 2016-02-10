#!/usr/bin/perl

# *****************************************************************************************
# conversion_upd_cat_creative_list.cgi
#
# this page updates select list based on category_id and client_id
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
my $incoming_cid;
my $aname;
my $errmsg;
my $images = $util->get_images_url;
my $cfrom;
my @from_array;
my $trigger_creative;
my $trigger1;
my $trigger2;
my $trigger_creative2;
my $catid1;
my $catid2;
my $advertiser_id1;
my $advertiser_id2;
my $sid;
my $aflag;
my $oflag;
my $fid;

# connect to the util database
my ($dbhq,$dbhu)=$util->get_dbh();

my $cid = $query->param('cid');
my $client_id = $query->param('client_id');
my $temp_str;
my $copen;
my $cclick;
my $cindex;
my $sth1;
#
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>CREATE EMAIL</title>
<script language="JavaScript">
</script>
</head>
<body>
<script language="JavaScript">
end_of_html
#
if ($client_id > 0)
{
	$sql = "select trigger1,trigger2 from conversion_category_trigger where category_id=$cid and client_id in (0,$client_id) order by client_id desc"; 
}
else
{
	$sql = "select trigger1,trigger2 from conversion_category_trigger where category_id=$cid and client_id = $client_id order by client_id desc"; 
}
	$sth = $dbhq->prepare($sql) ;
	$sth->execute();
	($trigger1,$trigger2) = $sth->fetchrow_array();
	$sth->finish();
#
#	Get advertiser for trigger creative
#
	$catid1 = -1;
	$catid2 = -1;
	$advertiser_id1 = -1;
	$advertiser_id2 = -1;
	if ($trigger1 > 0)
	{
		$sql = "select creative.advertiser_id,category_id from creative,advertiser_info where creative.advertiser_id=advertiser_info.advertiser_id and creative_id=$trigger1";
		$sth = $dbhq->prepare($sql) ;
		$sth->execute();
		($advertiser_id1,$catid1) = $sth->fetchrow_array();
		$sth->finish();

	$sql="select creative_id,creative_name from creative where advertiser_id=(select advertiser_id from creative where creative_id=$trigger1) and status='A' and trigger_flag='Y' order by creative_name";
$sth = $dbhq->prepare($sql) ;
$sth->execute();
while (($cid,$aname) = $sth->fetchrow_array())
{
    print "parent.main.addTriggerOption($cid,\"$aname\");\n";
}
$sth->finish();
	}
	if ($trigger2 > 0)
	{
		$sql = "select creative.advertiser_id,category_id from creative,advertiser_info where creative.advertiser_id=advertiser_info.advertiser_id and creative_id=$trigger2";
		$sth = $dbhq->prepare($sql) ;
		$sth->execute();
		($advertiser_id2,$catid2) = $sth->fetchrow_array();
		$sth->finish();
		$sql="select creative_id,creative_name from creative where advertiser_id=(select advertiser_id from creative where creative_id=$trigger2) and status='A' and trigger_flag='Y' order by creative_name";
		$sth = $dbhq->prepare($sql) ;
		$sth->execute();
		while (($cid,$aname) = $sth->fetchrow_array())
		{
    		print "parent.main.addTriggerOption2($cid,\"$aname\");\n";
		}
		$sth->finish();
	}
	print "parent.main.set_fields('$trigger1','$trigger2','$catid1','$catid2','$advertiser_id1','$advertiser_id2');\n";
print "</script>\n";
print "</body>\n";
print "</html>\n";
$util->clean_up();
exit(0);
