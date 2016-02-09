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

#use Lib::Database::Perl::Interface::Administration;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $sql;
my $dbh;

# connect to the util database
my ($dbhq,$dbhu)=$util->get_dbh();

# lookup the username/password in the UserAccounts table
if ( $util->authenticateUser({'username' => $query->param('username'), 'password' => $query->param('password')}) )
{
	print "Location: mainmenu.cgi\n\n";
}

else
{
	# user login entered incorrect username/password
	# go back to login page

	print $query->redirect("login_form.cgi");
}

$util->clean_up();
exit(0);