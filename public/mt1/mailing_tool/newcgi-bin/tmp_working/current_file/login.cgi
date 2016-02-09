#!/usr/bin/perl

# *****************************************************************************************
# login.cgi
#
# this page checks the db to see if the user is a valid login
#
# History
# Grady Nash, 8/3/01, Creation
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
my $userid;
my $cookie;

# connect to the util database

$util->db_connect();
$dbh = $util->get_dbh;

my $username = $query->param('username');
my $password = $query->param('password');

# lookup the username/password in the user table

$sql = "select user_id from user where username='$username' and password='$password'";
$sth = $dbh->prepare($sql);
$sth->execute();
$userid = $sth->fetchrow_array();
$sth->finish();

if ($userid)
{
	# Login is OK, set the cookie

  	$cookie = "utillogin=$userid; path=/;";
  	print "Set-Cookie: $cookie\n";

	# go to the main menu

	print "Location: mainmenu.cgi\n\n";
}
else
{
	# user login entered incorrect username/password
	# go back to login page

	print "Location: index.html\n\n";
}

$util->clean_up();
exit(0);
