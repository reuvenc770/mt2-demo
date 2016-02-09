#!/usr/bin/perl
# *****************************************************************************************
# delete_category.cgi
#
# this page saves the category changes
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


$cat_id = $query->param('cat_id');

$sql = "update category_info set status='D' where category_id = $cat_id";
$rows = $dbh->do($sql);
if ($dbh->err() != 0)
{
	$errmsg = $dbh->errstr();
	util::logerror("Updating category record $sql: $errmsg");
	exit(0);
}
print "Location: list_category.cgi\n\n";
$pms->clean_up();
exit(0);
