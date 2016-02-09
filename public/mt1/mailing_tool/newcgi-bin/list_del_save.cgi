#!/usr/bin/perl
# *****************************************************************************************
# list_del_save.cgi
#
# Delete a list
#
# History
# Grady Nash	10/10/2001		Creation 
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
my $list_id = $query->param('list_id');

# connect to the util database

###$util->db_connect();

my ($dbhq,$dbhu)=$util->get_dbh();
###$dbh = $util->get_dbh;

# check for login

$user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}

# update this list's record

$sql = "update list set status = 'D' where list_id = $list_id";
$rows = $dbhu->do($sql);
if ($dbhu->err() != 0)
{
	$errmsg = $dbhu->errstr();
	util::logerror("Updating list record: $sql: $errmsg");
	exit(0);
}

print "Location: mainmenu.cgi\n\n";

$util->clean_up();
exit(0);
