#!/usr/bin/perl

# *****************************************************************************************
# camp_lock.cgi
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
my $lock_flag = $query->param('lock_flag');
my $stype= $query->param('stype');

# connect to the util database
my ($dbhq,$dbhu)=$util->get_dbh();

# check for login
$user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}

# update this campaign's record

$sql = "update campaign set auto_lock='$lock_flag' where campaign_id = $campaign_id";
$rows = $dbhu->do($sql);
if ($dbhu->err() != 0)
{
	$errmsg = $dbhu->errstr();
	util::logerror("Updating campaign record for $campaign_id: $errmsg");
	exit(0);
}

print "Location: /blank.html\n\n";

$util->clean_up();
exit(0);
