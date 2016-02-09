#!/usr/bin/perl

# *****************************************************************************************
# camp_clear.cgi
#
# this page saves or updates the campaign
#
# History
# Grady Nash	08/26/2001		Creation 
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
my $campaign_id = $query->param('campaign_id');

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

$sql = "update campaign set status='D', sent_datetime=null, scheduled_datetime=null,unsubscribe_cnt=0 where campaign_id = $campaign_id";
$rows = $dbh->do($sql);
if ($dbh->err() != 0)
{
	$errmsg = $dbh->errstr();
	util::logerror("Inserting campaign record $sql : $errmsg");
	exit(0);
}
$sql = "delete from campaign_log where campaign_id=$campaign_id";
$rows = $dbh->do($sql);
$sql = "delete from creative_log where campaign_id=$campaign_id";
$rows = $dbh->do($sql);
$sql = "delete from subject_log where campaign_id=$campaign_id";
$rows = $dbh->do($sql);
$sql = "delete from from_log where campaign_id=$campaign_id";
$rows = $dbh->do($sql);
$sql = "delete from url_log where campaign_id=$campaign_id";
$rows = $dbh->do($sql);

print "Location: mainmenu.cgi\n\n";
$util->clean_up();
exit(0);
