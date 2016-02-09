#!/usr/bin/perl

# *****************************************************************************************
# chk_creative_trigger.cgi
#
#
# History
# ******************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $sth1;
my $sql;
my $dbh;
my $aid;
my $cid;
my $aname;
my $errmsg;
my $prompt_cnt=0;
my $images = $util->get_images_url;
my $cfrom;
my @from_array;
my $trigger_creative_str;
my $trigger_creative2_str;
my $alt_trigger_creative_str;
my $company;
my $t1;
my $msg_str;
my $t2;
my $altt;
my $t1_str;
my $t2_str;
my $altt_str;

# connect to the util database
$util->db_connect();
$dbh = $util->get_dbh;

my $cid = $query->param('cid');
my $trigger_creative = $query->param('trigger1');
my $trigger_creative2 = $query->param('trigger2');
my $alt_trigger = $query->param('alt_trigger');
#
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>CREATE EMAIL</title>
<script language="JavaScript">
function doit(text)
{
	parent.main.displaymsg(text);
}
</script>
</head>
<body>
<script language="JavaScript">
end_of_html
open(LOG,">/tmp/chk.log");
	$sql = "select advertiser_name from advertiser_info where advertiser_id = (select advertiser_id from creative where creative_id=$trigger_creative)"; 
    $sth = $dbh->prepare($sql);
    $sth->execute();
    ($trigger_creative_str) = $sth->fetchrow_array();
	$sth->finish();
	$sql = "select advertiser_name from advertiser_info where advertiser_id = (select advertiser_id from creative where creative_id=$trigger_creative2)"; 
    $sth = $dbh->prepare($sql);
    $sth->execute();
    ($trigger_creative2_str) = $sth->fetchrow_array();
	$sth->finish();
	$sql = "select advertiser_name from advertiser_info where advertiser_id = (select advertiser_id from creative where creative_id=$alt_trigger)"; 
    $sth = $dbh->prepare($sql);
    $sth->execute();
    ($alt_trigger_creative_str) = $sth->fetchrow_array();
	$sth->finish();
	print LOG "$trigger_creative - $trigger_creative_str\n";
	print LOG "$trigger_creative2 - $trigger_creative2_str\n";
	print LOG "$alt_trigger - $alt_trigger_creative_str\n";

	$sql = "select company,trigger1,trigger2,alt_trigger from category_trigger,user where category_id=$cid and user.user_id=category_trigger.client_id and client_id != 0";
	print LOG "<$sql>\n";
    $sth1 = $dbh->prepare($sql);
    $sth1->execute();
    while (($company,$t1,$t2,$altt) = $sth1->fetchrow_array())
	{
		print LOG "$company - $t1 - $t2 - $altt\n";
		$sql = "select advertiser_name from advertiser_info where advertiser_id = (select advertiser_id from creative where creative_id=$t1)"; 
    	$sth = $dbh->prepare($sql);
    	$sth->execute();
    	($t1_str) = $sth->fetchrow_array();
		$sth->finish();
		if ($t2 > 0)
		{
			$sql = "select advertiser_name from advertiser_info where advertiser_id = (select advertiser_id from creative where creative_id=$t2)"; 
    	$sth = $dbh->prepare($sql);
    	$sth->execute();
    	($t2_str) = $sth->fetchrow_array();
		$sth->finish();
		}
		if ($altt > 0)
		{
			$sql = "select advertiser_name from advertiser_info where advertiser_id = (select advertiser_id from creative where creative_id=$altt)"; 
    	$sth = $dbh->prepare($sql);
    	$sth->execute();
    	($altt_str) = $sth->fetchrow_array();
		$sth->finish();
		}
		if (($trigger_creative_str ne "") || ($t1_str ne ""))
		{
			$msg_str = "Are you sure you want to replace " . $t1_str . " with " . $trigger_creative_str . " for " . $company . "?";
			$prompt_cnt++;
			print "doit(\"$msg_str\");\n";
		}
		if (($trigger_creative2_str ne "") || ($t2_str ne ""))
		{
			$msg_str = "Are you sure you want to replace " . $t2_str . " with " . $trigger_creative2_str . " for " . $company . "?";
			$prompt_cnt++;
			print "doit(\"$msg_str\");\n";
		}
		if (($alt_trigger_creative_str ne "") || ($altt_str ne ""))
		{
			$msg_str = "Are you sure you want to replace " . $altt_str . " with " . $alt_trigger_creative_str . " for " . $company . "?";
			$prompt_cnt++;
			print "doit(\"$msg_str\");\n";
		}
	}
	$sth1->finish();
print LOG "Cnt - $prompt_cnt\n";
	close(LOG);
print "parent.main.finished(\"$prompt_cnt\");\n";
print "</script>\n";
print "</body>\n";
print "</html>\n";
$util->clean_up();
exit(0);
