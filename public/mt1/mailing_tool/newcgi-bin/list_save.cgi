#!/usr/bin/perl
# *****************************************************************************************
# list_save.cgi
#
# this page saves the list changes
#
# History
# Grady Nash, 8/30/01, Creation
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

my $list_name = $query->param('list_name');
if ($list_name eq "")
{
	util::logerror("List Name cannot be blank");
    $util->clean_up();
    exit(0);
}

$list_name = $dbhq->quote($list_name);
my $status = $dbhq->quote($query->param('status'));
my $optin_flag = $dbhq->quote($query->param('optin_flag'));
my $double_mail_template = $dbhq->quote($query->param('double_mail_template'));
my $thankyou_mail_template = $dbhq->quote($query->param('thankyou_mail_template'));

if ($mode eq "EDIT")
{
	# update the list record

	my $list_id = $query->param('list_id');

	$sql = "update list set list_name = $list_name,
		status = $status,
		optin_flag = $optin_flag,
		thankyou_mail_template = $thankyou_mail_template,
		double_mail_template = $double_mail_template
		where list_id = $list_id";
	$rows = $dbhu->do($sql);
	if ($dbhu->err() != 0)
	{
		$errmsg = $dbhu->errstr();
		util::logerror("Updating list record $sql: $errmsg");
		exit(0);
	}
}
elsif ($mode eq "ADD")
{
	# Add the list record

	$sql = "insert into list (list_name, user_id, status, optin_flag, thankyou_mail_template,
		double_mail_template) values ($list_name, 1, $status, $optin_flag, 
		$thankyou_mail_template, $double_mail_template)";
	$rows = $dbhu->do($sql);
	if ($dbhu->err() != 0)
	{
		$errmsg = $dbhu->errstr();
		util::logerror("Inserting list record $sql: $errmsg");
		exit(0);
	}
}

print "Location: list_list.cgi\n\n";

# exit function

$util->clean_up();
exit(0);
