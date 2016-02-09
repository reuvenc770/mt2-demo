#!/usr/bin/perl

# *****************************************************************************************
# camp_del_save.cgi
#
# Delete a campaign information 
#
# History
# Grady Nash	08/14/2001		Creation 
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
my $rows;
my $errmsg;
my $user_id;
my $campaign_id = $query->param('campaign_id');

# connect to the util database

$util->db_connect();
$dbh = $util->get_dbh;

# check for login

$user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}

# update this campaign's record

$sql = "update campaign set deleted_date = now() where campaign_id = $campaign_id";
$rows = $dbh->do($sql);
if ($dbh->err() != 0)
{
	$errmsg = $dbh->errstr();
	util::logerror("Updating campaign record for $campaign_id: $errmsg");
	exit(0);
}
$sql="delete from camp_schedule_info where campaign_id=$campaign_id";
$rows = $dbh->do($sql);
$sql="delete from 3rdparty_campaign where campaign_id=$campaign_id";
$rows = $dbh->do($sql);
$sql="delete from daily_deals where campaign_id=$campaign_id";
$rows = $dbh->do($sql);

print "Location: mainmenu.cgi\n\n";

$util->clean_up();
exit(0);
