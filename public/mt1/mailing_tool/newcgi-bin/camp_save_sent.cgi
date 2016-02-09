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
my $sent_cnt = $query->param('sent_cnt');
my $user_sent_cnt = $query->param('user_cnt');

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
if ($sent_cnt != $user_sent_cnt)
{
	$sql = "update campaign_log set user_sent_cnt=$user_sent_cnt where campaign_id=$campaign_id"; 
}
else
{
	$sql = "update campaign_log set user_sent_cnt=0 where campaign_id=$campaign_id"; 
}
$rows = $dbhu->do($sql);
if ($dbhu->err() != 0)
{
	$errmsg = $dbhu->errstr();
	util::logerror("Updating campaign_log record $sql : $errmsg");
	exit(0);
}

print "Location: camp_history.cgi?campaign_id=$campaign_id\n\n";
$util->clean_up();
exit(0);
