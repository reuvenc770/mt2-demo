#!/usr/bin/perl

# *****************************************************************************************
# company_add_tracking.cgi
#
# this page updates information in the company_info_tracking table
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
my $tracking_id= $query->param('tracking_id');
my $affiliate_id= $query->param('affiliate_id');
my $link_params= $query->param('link_params');
my $number_of_params= $query->param('number_of_params');
my $default_flag= $query->param('default_flag');
if ($default_flag eq "")
{
	$default_flag="N";
}
if ($default_flag eq "Y")
{
	$sql="update company_info_tracking set default_flag='N' where company_id=$company_id";
	$sth = $dbhu->do($sql);
	$sql="update company_info set affiliate_id=$affiliate_id where company_id=$company_id";
	$sth = $dbhu->do($sql);
}
if ($tracking_id ne "")
{
	$sql="update company_info_tracking set affiliate_id=$affiliate_id,link_params='$link_params',number_of_params=$number_of_params,default_flag='$default_flag' where tracking_id=$tracking_id";
}
else
{
	$sql="insert into company_info_tracking(company_id,affiliate_id,link_params,number_of_params,default_flag) values($company_id,$affiliate_id,'$link_params',$number_of_params,'$default_flag')";
}
$sth = $dbhu->do($sql);
if ($dbhu->err() != 0)
{
	my $errmsg = $dbhu->errstr();
    util::logerror("Inserting company_info_tracking record for $sql $user_id: $errmsg");
}
# Display the confirmation page
#
print "Location: /cgi-bin/company_tracking.cgi?company_id=$company_id&rid=546723\n\n";
$util->clean_up();
exit(0);
