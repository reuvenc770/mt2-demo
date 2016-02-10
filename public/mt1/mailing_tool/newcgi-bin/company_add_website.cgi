#!/usr/bin/perl

# *****************************************************************************************
# company_add_website.cgi
#
# this page updates information in the company_info_website table
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
my $sql;
my $dbh;
my $errmsg;
my $images = $util->get_images_url;

# connect to the util database
my ($dbhq,$dbhu)=$util->get_dbh();

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
my $company_id = $query->param('company_id');
my $website_id= $query->param('website_id');
my $website= $query->param('website');
my $username= $query->param('username');
my $password= $query->param('password');
my $default_flag= $query->param('default_flag');
if ($default_flag eq "")
{
	$default_flag="N";
}
if ($default_flag eq "Y")
{
	$sql="update company_info_website set default_flag='N' where company_id=$company_id";
	$sth = $dbhu->do($sql);
}
if ($website_id ne "")
{
	$sql="update company_info_website set website='$website',username='$username',password='$password',default_flag='$default_flag' where website_id=$website_id";
}
else
{
	$sql="insert into company_info_website(company_id,website,username,password,default_flag) values($company_id,'$website','$username','$password','$default_flag')";
}
$sth = $dbhu->do($sql);
if ($dbhu->err() != 0)
{
	my $errmsg = $dbhu->errstr();
    util::logerror("Inserting company_info_website record for $sql $user_id: $errmsg");
}
# Display the confirmation page
#
print "Location: /cgi-bin/company_website.cgi?company_id=$company_id\n\n";
$util->clean_up();
exit(0);
