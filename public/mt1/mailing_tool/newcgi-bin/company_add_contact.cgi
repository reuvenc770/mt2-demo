#!/usr/bin/perl

# *****************************************************************************************
# company_add_contact.cgi
#
# this page updates information in the company_info_contact table
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
my $cid= $query->param('cid');
my $name= $query->param('name');
my $email_addr = $query->param('email_addr');
my $phone= $query->param('phone');
my $aim= $query->param('aim');
my $default_flag = $query->param('default_flag');
if ($default_flag eq "")
{
	$default_flag="N";
}

if ($default_flag eq "Y")
{
	$sql="update company_info_contact set default_flag='N' where company_id=$company_id";
	my $rows=$dbhu->do($sql);
}
if ($cid ne "")
{
	$sql="update company_info_contact set contact_name='$name',contact_phone='$phone',contact_email='$email_addr',contact_aim='$aim',default_flag='$default_flag' where contact_id=$cid";
}
else
{
	$sql="insert into company_info_contact(company_id,contact_name,contact_phone,contact_email,contact_aim,default_flag) values($company_id,'$name','$phone','$email_addr','$aim','$default_flag')";
}
$sth = $dbhu->do($sql);
if ($dbhu->err() != 0)
{
	my $errmsg = $dbhu->errstr();
    util::logerror("Inserting companyinfo_contact record for $sql $user_id: $errmsg");
}
# Display the confirmation page
#
print "Location: /cgi-bin/company_contact.cgi?company_id=$company_id\n\n";
$util->clean_up();
exit(0);
