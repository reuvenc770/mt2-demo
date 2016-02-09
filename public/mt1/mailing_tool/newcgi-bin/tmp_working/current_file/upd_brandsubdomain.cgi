#!/usr/bin/perl

# *****************************************************************************************
# upd_brandsubdomain.cgi
#
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
my $cbrand;

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
my $cid = $query->param('cid');
my $brandid = $query->param('brandid');
my $subdomain = $query->param('subdomain');
#
# Update record into brandsubdomain_info 
#
$sql = "update brandsubdomain_info set subdomain_name='$subdomain' where subdomain_id=$brandid and category_id=$cid";
$sth = $dbh->do($sql);
if ($dbh->err() != 0)
{
	my $errmsg = $dbh->errstr();
    util::logerror("Updating brandsubdomain info record for $user_id: $errmsg");
}
#
# Display the confirmation page
#
print "Location: /cgi-bin/new_list_category.cgi\n\n";
$util->clean_up();
exit(0);
