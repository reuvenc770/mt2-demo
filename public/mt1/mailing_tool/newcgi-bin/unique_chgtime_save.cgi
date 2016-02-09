#!/usr/bin/perl

#******************************************************************************
# unique_chgtime_save.cgi
#
# this page updates time for deploy 
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
my $sql;
my $dbh;
my $sid;
my $rows;
my $errmsg;
my $tracking_id;
my $images = $util->get_images_url;

# connect to the util database
my ($dbhq,$dbhu)=$util->get_dbh();

# check for login
my $user_id = util::check_security();
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
	$util->clean_up();
    exit(0);
}
my $uid = $query->param('uid');
my $stime=$query->param('stime');
my $sdate=$query->param('sdate');
my $smin=$query->param('smin');
my $am_pm=$query->param('am_pm');
if ($am_pm eq "PM")
{
	$stime = $stime + 12;
    if ($stime >= 24)
    {
    	$stime = 12;
    }
}
elsif (($am_pm eq "AM") && ($stime == 12))
{
	$stime = 0;
}
if (length($smin) == 1)
{
	$smin="0".$smin;
}
$stime = $stime . ":".$smin;
my $timestr=$sdate." ".$stime;
$sql="update current_campaigns set scheduled_time='$stime' where campaign_id in (select campaign_id from campaign where  id='$uid' and scheduled_date='$sdate')";
$rows=$dbhu->do($sql);
$sql="update campaign set scheduled_time='$stime',scheduled_datetime='$timestr' where id='$uid' and scheduled_date='$sdate'";
$rows=$dbhu->do($sql);
$sql="update unique_campaign set send_time='$stime' where unq_id=$uid";
$rows=$dbhu->do($sql);
print "Location: /cgi-bin/unique_deploy_list.cgi\n\n";
