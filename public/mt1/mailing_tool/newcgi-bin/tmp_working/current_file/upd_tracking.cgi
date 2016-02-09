#!/usr/bin/perl

# *****************************************************************************************
# upd_tracking.cgi
#
# this page updates information in the advertiser_tracking table
#
# History
# Jim Sobeck, 01/04/05, Creation
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
my $errmsg;
my $images = $util->get_images_url;

# connect to the util database
$util->db_connect();
$dbh = $util->get_dbh;

# check for login
my $user_id = util::check_security();
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
	$util->clean_up();
    exit(0);
}
#
# Get the information about the user from the form 
#
my $aid = $query->param('aid');
my $url = $query->param('tracking_url');
my $code = $query->param('tracking_code');
my $client_id = $query->param('client_id');
my $dailydeal = $query->param('dailydeal');
if ($dailydeal eq "")
{
	$dailydeal = "N";
}
#
# Insert record into links table
#
$sql="insert into links(refurl,date_added) values('$url',now())";
$sth = $dbh->do($sql);
#
# Get id just added
#
my $lid;
$sql="select max(link_id) from links where refurl='$url'";
$sth = $dbh->prepare($sql) ;
$sth->execute();
($lid) = $sth->fetchrow_array();
$sth->finish();
#
# Insert record into advertiser_tracking 
#
$sql="insert into advertiser_tracking(advertiser_id,url,code,date_added,client_id,link_id,daily_deal) values($aid,'$url','$code',curdate(),$client_id,$lid,'$dailydeal')";
$sth = $dbh->do($sql);
if ($dbh->err() != 0)
{
	my $errmsg = $dbh->errstr();
    util::logerror("Updating advertiser tracking info record $sql : $errmsg");
}
else
{
   $sql = "update advertiser_info set url_count=(select count(*) from advertiser_tracking where advertiser_tracking.advertiser_id=advertiser_info.advertiser_id and advertiser_tracking.advertiser_id=$aid)";
   my $rows = $dbh->do($sql);
#
# Display the confirmation page
#
	if (($dailydeal eq "Y") or ($dailydeal eq "T"))
	{
		print "Location: /cgi-bin/tracking.cgi?aid=$aid\n\n";
	}
	else
	{
		print "Location: /cgi-bin/advertiser_disp2.cgi?puserid=$aid\n\n";
	}
}
my $sth1;
my $BASE_DIR;
my $link_id;
my $refurl;
$sql = "select parmval from sysparm where parmkey='BASE_DIR'";
$sth1 = $dbh->prepare($sql);
$sth1->execute();
($BASE_DIR) = $sth1->fetchrow_array();
$sth1->finish;
open(FILE,"> ${BASE_DIR}logs/redir.dat") or die "can't open file : $!";
$sql = "select link_id,refurl from links order by link_id";
$sth1 = $dbh->prepare($sql);
$sth1->execute();
while (($link_id,$refurl) = $sth1->fetchrow_array())
{
	print FILE "$link_id|$refurl\n";
}
$sth1->finish();
close(FILE);
my @args = ("${BASE_DIR}newcgi-bin/cp_redir_tmp.sh");
system(@args) == 0 or die "system @args failed: $?";
$util->clean_up();
exit(0);
