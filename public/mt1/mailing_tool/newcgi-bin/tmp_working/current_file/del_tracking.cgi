#!/usr/bin/perl

# *****************************************************************************************
# del_tracking.cgi
#
# this page deletes information in the advertiser_tracking table
#
# History
# Jim Sobeck, 01/04/05, Creation
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
my $tid = $query->param('tid');
my $aid = $query->param('aid');
#
# Delete record into advertiser_tracking 
#
$sql="delete from advertiser_tracking where tracking_id=$tid";
$sth = $dbh->do($sql);
if ($dbh->err() != 0)
{
	my $errmsg = $dbh->errstr();
    util::logerror("Updating advertiser tracking info record $sql : $errmsg");
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
