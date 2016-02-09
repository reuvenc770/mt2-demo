#!/usr/bin/perl
# *****************************************************************************************
# link_save.cgi
#
# this page saves the redirect changes
#
# History
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $sth1;
my $sql;
my $dbh;
my $rows;
my $errmsg;
my $mode = $query->param('mode');
my $link_id;
my $refurl;
my $BASE_DIR;

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

# get fields from the form

$refurl= $query->param('refurl');
if ($refurl eq "")
{
	util::logerror("URL cannot be blank");
    $util->clean_up();
    exit(0);
}

$refurl = $dbh->quote($refurl);

if ($mode eq "EDIT")
{
	# update the links record

	$link_id = $query->param('link_id');

	$sql = "update links set refurl = $refurl, date_added=now() where link_id = $link_id";
	$rows = $dbh->do($sql);
	if ($dbh->err() != 0)
	{
		$errmsg = $dbh->errstr();
		util::logerror("Updating links record $sql: $errmsg");
		exit(0);
	}
}
elsif ($mode eq "ADD")
{
	# Add the link record

	$sql = "insert into links(refurl,date_added) values ($refurl,now())";
	$rows = $dbh->do($sql);
	if ($dbh->err() != 0)
	{
		$errmsg = $dbh->errstr();
		util::logerror("Inserting links record $sql: $errmsg");
		exit(0);
	}
}
$sql = "select parmval from sysparm where parmkey='BASE_DIR'";
$sth1 = $dbh->prepare($sql);
$sth1->execute();
($BASE_DIR) = $sth1->fetchrow_array();
$sth1->finish;

open(FILE,"> ${BASE_DIR}logs/redir.dat") or die "can't open file: $!";
$sql = "select link_id,refurl from links order by link_id";
$sth = $dbh->prepare($sql);
$sth->execute();
while (($link_id,$refurl) = $sth->fetchrow_array())
{
	print FILE "$link_id|$refurl\n";
}
$sth->finish();
close(FILE);
my @args = ("${BASE_DIR}newcgi-bin/cp_redir_tmp.sh");
system(@args) == 0 or die "system @args failed: $?";

print "Location: list_refurl.cgi\n\n";

# exit function

$util->clean_up();
exit(0);
