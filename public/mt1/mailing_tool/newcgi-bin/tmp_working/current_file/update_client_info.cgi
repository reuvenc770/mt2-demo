#!/usr/bin/perl

# *****************************************************************************************
# update_client_info.cgi
#
# this page updates information in the user table
#
# History
# Jim Sobeck, 8/03/01, Creation
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
my $errmsg;
my ($fname,$lname,$address,$address2,$city,$state,$zip,$phone,$email_addr);
my $images = $util->get_images_url;

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
#
# Get the information about the user from the form 
#
$fname = $query->param('fname');
$lname = $query->param('lname');
$address = $query->param('address');
$address2 = $query->param('address2');
$city = $query->param('city');
$state = $query->param('state');
$zip = $query->param('zip');
$phone = $query->param('phone');
$email_addr = $query->param('email_addr');
#
# Update the user table with the information
#
$sql = "update user set first_name='$fname',last_name='$lname',address='$address',address2='$address2',city='$city',state='$state',zip='$zip',phone='$phone',email_addr='$email_addr' where user_id = $user_id";
$sth = $dbh->do($sql);
if ($dbh->err() != 0)
{
	my $errmsg = $dbh->errstr();
    util::logerror("Updating user record for $user_id: $errmsg");
}
else
{
#
# Display the confirmation page
#
	util::confirmation_page('Edit Contact Info','Contact Information Has Successfully Been Updated.');
}
$util->clean_up();
exit(0);
