#!/usr/bin/perl

# *****************************************************************************************
# add_client_url.cgi
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
my $url= $query->param('url');
#
# Get the information about the user from the form 
#
$sql = "insert into client_urls(client_id,url) values($cid,'$url')"; 
$sth = $dbh->do($sql);
#
# Display the confirmation page
#
print "Location: /cgi-bin/client_urls.cgi?cid=$cid\n\n";
$util->clean_up();
exit(0);
