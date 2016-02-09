#!/usr/bin/perl

# *****************************************************************************************
# delete_unsubimg.cgi
#
# this page updates information in the advertiser_info table
#
# History
# Jim Sobeck, 05/09/05, Creation
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
my $sid;
my $errmsg;
my $images = $util->get_images_url;
my $csubject;
my @subject_array;

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
# Remove old unsub image information
#
my $aid = $query->param('aid');
#
# Delete record from advertiser_subject
#
$sql = "update advertiser_info set unsub_image='' where advertiser_id=$aid";
$sth = $dbh->do($sql);
if ($dbh->err() != 0)
{
	my $errmsg = $dbh->errstr();
    util::logerror("Updating advertiser info record for $user_id: $errmsg");
}
#
# Display the confirmation page
#
print "Location: /cgi-bin/advertiser_disp2.cgi?puserid=$aid\n\n";
$util->clean_up();
exit(0);
