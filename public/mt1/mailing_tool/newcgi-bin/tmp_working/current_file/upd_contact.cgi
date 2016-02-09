#!/usr/bin/perl

# *****************************************************************************************
# upd_contact.cgi
#
# this page updates information in the advertiser_contact_info table
#
# History
# Jim Sobeck, 12/16/04, Creation
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
my $aid = $query->param('aid');
my $name = $query->param('name');
my $phone = $query->param('phone');
my $email = $query->param('email');
my $company = $query->param('company');
my $aim = $query->param('aim');
my $website = $query->param('website');
my $username = $query->param('username');
my $password = $query->param('password');
my $notes = $query->param('notes');
if ($notes ne "")
{
	$notes = $dbh->quote($notes);
}
else
{
	$notes='';
}
#
# Remove old contact information
#
$sql = "delete from advertiser_contact_info where advertiser_id=$aid";
$sth = $dbh->do($sql);
#
# Insert record into advertiser_contact_info 
#
if ($notes ne "")
{
	$notes = $dbh->quote($notes);
	$sql = "insert into advertiser_contact_info(advertiser_id,contact_name,contact_phone,contact_email,contact_company,contact_aim,contact_website,contact_username,contact_password,contact_notes) values($aid,'$name','$phone','$email','$company','$aim','$website','$username','$password',$notes)";
}
else
{
	$sql = "insert into advertiser_contact_info(advertiser_id,contact_name,contact_phone,contact_email,contact_company,contact_aim,contact_website,contact_username,contact_password,contact_notes) values($aid,'$name','$phone','$email','$company','$aim','$website','$username','$password','$notes')";
}
$sth = $dbh->do($sql);
if ($dbh->err() != 0)
{
	my $errmsg = $dbh->errstr();
    util::logerror("Updating advertiser contact info record for $sql $user_id: $errmsg");
}
else
{
#
# Display the confirmation page
#
	print "Location: /cgi-bin/advertiser_disp2.cgi?puserid=$aid\n\n";
}
$util->clean_up();
exit(0);
