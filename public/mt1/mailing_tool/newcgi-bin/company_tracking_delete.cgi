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
$sql="delete from company_info_tracking where tracking_id=$tracking_id";
$sth = $dbhu->do($sql);
if ($dbhu->err() != 0)
{
	my $errmsg = $dbhu->errstr();
    util::logerror("Deleting company_info_tracking record for $sql $user_id: $errmsg");
}
# Display the confirmation page
#
print "Location: /cgi-bin/company_tracking.cgi?company_id=$company_id\n\n";
$util->clean_up();
exit(0);
