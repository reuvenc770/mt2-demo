#!/usr/bin/perl
# *****************************************************************************************
# user_page_save.cgi
#
# this page saves the user pages
#
# History
# Grady Nash, 11/1/2001, Creation
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
my $mode = $query->param('mode');

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

my $page_name = $query->param('page_name');
if ($page_name eq "")
{
	util::logerror("Page Name cannot be blank");
    $util->clean_up();
    exit(0);
}

$page_name = $dbhq->quote($page_name);
my $status = $dbhq->quote($query->param('status'));

if ($mode eq "EDIT")
{
	my $user_page_id = $query->param('user_page_id');

	$sql = "update user_page set page_name = $page_name,
		status = $status
		where user_page_id = $user_page_id";
	$rows = $dbhu->do($sql);
	if ($dbhu->err() != 0)
	{
		$errmsg = $dbhu->errstr();
		util::logerror("Updating record $sql: $errmsg");
		exit(0);
	}
}
elsif ($mode eq "ADD")
{
	# Add the record

	$sql = "insert into user_page (page_name, user_id, status) 
		values ($page_name, $user_id, $status)";
	$rows = $dbhu->do($sql);
	if ($dbhu->err() != 0)
	{
		$errmsg = $dbhu->errstr();
		util::logerror("Inserting record $sql: $errmsg");
		exit(0);
	}
}

print "Location: user_page_list.cgi\n\n";

# exit function

$util->clean_up();
exit(0);
