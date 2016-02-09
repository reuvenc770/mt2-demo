#!/usr/bin/perl

# *****************************************************************************************
# sysparm_save.cgi
#
# this page saves the system parameter
#
# History
# Grady Nash, 11/09/01, Creation
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
my $count;

# connect to the util database

###$util->db_connect();

my ($dbhq,$dbhu)=$util->get_dbh();
###$dbh = $util->get_dbh;

# check for login

my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}

# get fields from the form

my $parmkey = $dbhq->quote($query->param('parmkey'));
my $parmval = $dbhq->quote($query->param('parmval'));

$sql = "update sysparm set parmval = $parmval where parmkey = $parmkey";
$rows = $dbhu->do($sql);
if ($dbhu->err() != 0)
{
	$errmsg = $dbhu->errstr();
	util::logerror("Updating sysparm record: $sql : $errmsg");
	exit(0);
}

print "Location: sysparm_list.cgi\n\n";

# exit function

$util->clean_up();
exit(0);
