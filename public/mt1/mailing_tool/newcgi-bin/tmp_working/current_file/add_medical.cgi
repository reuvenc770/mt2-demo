#!/usr/bin/perl
# *****************************************************************************************
# add_medical.cgi
#
# this page saves the medical condition changes
#
# History
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $pms = util->new;
my $query = CGI->new;
my $sth;
my $sql;
my $dbh;
my $rows;
my $errmsg;
my $cat_id;
my $cname;
my $dname;

# connect to the pms database

$pms->db_connect();
$dbh = $pms->get_dbh;

# check for login

my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $pms->clean_up();
    exit(0);
}

# get fields from the form

$cname = $query->param('cname');
if ($cname eq "")
{
	util::logerror("Medical Condition cannot be blank");
    $pms->clean_up();
    exit(0);
}

$cname = $dbh->quote($cname);

$sql = "insert into medical_condition(name) values ($cname)";
$rows = $dbh->do($sql);
if ($dbh->err() != 0)
{
	$errmsg = $dbh->errstr();
	util::logerror("Inserting medical condition record $sql: $errmsg");
	exit(0);
}
print "Location: list_medical.cgi\n\n";
$pms->clean_up();
exit(0);
