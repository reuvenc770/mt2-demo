#!/usr/bin/perl

# *****************************************************************************************
# ins_brandsubdomain.cgi
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
my @brand_array;

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
#
# Get the information about the user from the form 
#
my $brand_list = $query->param('cbrand');
$brand_list =~ s/[\n\r\f\t]/\|/g ;
$brand_list =~ s/\|{2,999}/\|/g ;
@brand_array = split '\|', $brand_list;
foreach $cbrand (@brand_array)
{
$cbrand =~ s/'/''/g;
$cbrand =~ s/\x96/-/g;
#
# Insert record into brandsubdomain_info 
#
$sql = "insert into brandsubdomain_info(category_id,subdomain_name) values($cid,'$cbrand')";
$sth = $dbh->do($sql);
if ($dbh->err() != 0)
{
	my $errmsg = $dbh->errstr();
    util::logerror("Updating brandsubdomain info record for $user_id: $errmsg");
}
}
#
# Display the confirmation page
#
print "Location: /cgi-bin/new_list_category.cgi\n\n";
$util->clean_up();
exit(0);
