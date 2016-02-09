#!/usr/bin/perl
#===============================================================================
# Name   : replace_advertiser.cgi 
#
#--Change Control---------------------------------------------------------------
#===============================================================================

# include Perl Modules
use strict;
use CGI;
use util;

# get some objects to use later
my $util = util->new;
my $query = CGI->new;
my ($sth, $sql, $dbh, $errmsg ) ;
my $sth1;
my $aname;
my $new_aname;
my $taid;

#----------- check for login ------------------
my $user_id = util::check_security();
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}

my $aid= $query->param('aid');
my $new_aid= $query->param('new_aid');
#
#------ connect to the util database ------------------
$util->db_connect();
$dbh = 0;
while (!$dbh)
{
print LOG "Connecting to db\n";
$dbh = $util->get_dbh;
}
$dbh->{mysql_auto_reconnect}=1;
$sql = "select advertiser_name from advertiser_info where advertiser_id=$aid";
$sth1 = $dbh->prepare($sql);
$sth1->execute();
($aname) = $sth1->fetchrow_array();
$sth1->finish;
$sql = "select advertiser_name from advertiser_info where advertiser_id=$new_aid";
$sth1 = $dbh->prepare($sql);
$sth1->execute();
($new_aname) = $sth1->fetchrow_array();
$sth1->finish;
#
#	Change all Triggers, Daily Deals, and Scheduled Deals
#
$sql = "update campaign set advertiser_id=$new_aid,campaign_name='$new_aname' where advertiser_id=$aid and status='T' and deleted_date is null";
my $rows = $dbh->do($sql);
$sql = "update campaign set advertiser_id=$new_aid,campaign_name='$new_aname' where advertiser_id=$aid and status='W' and deleted_date is null";
my $rows = $dbh->do($sql);
$sql = "update campaign set advertiser_id=$new_aid,campaign_name='$new_aname' where advertiser_id=$aid and status='S' and deleted_date is null and scheduled_datetime > now()";
my $rows = $dbh->do($sql);

$util->clean_up();
print "Content-Type: text/plain\n\n";
print<<"end_of_html";
<html>
<head>
</head>
<body>
<center>
<h3>Replace Advertiser - Confirm</h3>
<br>
Advertiser <b>$aname</b> replaced with <b>$new_aname</b> in all Daily, Trigger, and Scheduled Campaigns
<br>
<a href="/cgi-bin/advertiser_list.cgi"><img src="/images/advertisers.gif" border=0></a>&nbsp;&nbsp;&nbsp;<a href="/cgi-bin/mainmenu.cgi"><img src="/images/home.gif" border=0></a>
</body>
</html>
end_of_html
exit(0);

