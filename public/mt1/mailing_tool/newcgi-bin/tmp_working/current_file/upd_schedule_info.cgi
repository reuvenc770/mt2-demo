#!/usr/bin/perl

# *****************************************************************************************
# upd_adv_creative_list.cgi
#
# this page updates select list based on advertiser_id
#
# History
# Jim Sobeck, 01/19/05, creation
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
my $cid;
my $images = $util->get_images_url;
my ($camp_cnt,$aol_cnt,$daily_cnt,$rotate_cnt,$third_cnt);
my $max_id;

# connect to the util database
$util->db_connect();
$dbh = $util->get_dbh;

my $cid = $query->param('cid');
#
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>CREATE EMAIL</title>
</head>
<body>
<script language="JavaScript">
end_of_html
$sql="select campaign_cnt,aol_cnt,daily_cnt,rotating_cnt,3rdparty_cnt from network_schedule where client_id=$cid"; 
$sth = $dbh->prepare($sql) ;
$sth->execute();
if (($camp_cnt,$aol_cnt,$daily_cnt,$rotate_cnt,$third_cnt) = $sth->fetchrow_array())
{
}
else
{
	$camp_cnt = 0;
	$aol_cnt = 0;
	$daily_cnt = 0;
	$rotate_cnt = 0;
	$third_cnt = 0;
}
$sth->finish();
print "parent.main.set_lists($camp_cnt,$aol_cnt,$daily_cnt,$rotate_cnt,$third_cnt);\n";
print "</script>\n";
print "</body>\n";
print "</html>\n";
#
$util->clean_up();
exit(0);
