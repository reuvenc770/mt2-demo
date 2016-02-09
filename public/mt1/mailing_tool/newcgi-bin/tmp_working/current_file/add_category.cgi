#!/usr/bin/perl
# *****************************************************************************************
# add_category.cgi
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
my $mode = $query->param('mode');
my $cat_id;
my $cname;
my $userid;
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
$dname = $query->param('dname');
$userid = $query->param('userid');
if ($cname eq "")
{
	util::logerror("Category cannot be blank");
    $pms->clean_up();
    exit(0);
}

$cname = $dbh->quote($cname);

if ($mode eq "EDIT")
{
	# update the category record

	$cat_id = $query->param('cat_id');
	$sql = "update category_info set category_name = $cname where category_id = $cat_id";
	$rows = $dbh->do($sql);
	if ($dbh->err() != 0)
	{
		$errmsg = $dbh->errstr();
		util::logerror("Updating category record $sql: $errmsg");
		exit(0);
	}
	$sql = "update client_category_info set domain_name='$dname' where category_id = $cat_id and user_id=$userid";
	$rows = $dbh->do($sql);
}
else
{
	# Add the category record

	$sql = "insert into category_info(category_name) values ($cname)";
	$rows = $dbh->do($sql);
	if ($dbh->err() != 0)
	{
		$errmsg = $dbh->errstr();
		util::logerror("Inserting category record $sql: $errmsg");
		exit(0);
	}
	#
	# Get category_id just added
	#
	my $cid;
	$sql = "select category_id from category_info where category_name=$cname";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	($cid) = $sth->fetchrow_array();
	$sth->finish();
	$sql = "insert into client_category_info select $cid,user_id,'$dname' from user";
	$rows = $dbh->do($sql);
}
print "Location: list_category.cgi?userid=$userid\n\n";
$pms->clean_up();
exit(0);
