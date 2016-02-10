#!/usr/bin/perl
# *****************************************************************************************
# listprofile_cook.cgi
#
# set a cookie and redisplay the listprofile_list.cgi 
#
# History
# *****************************************************************************************

use strict;
use CGI;

my $query = CGI->new();
my $brand_type= $query->param('brand_type');
my $third_party_id= $query->param('third_party_id');
my $tag= $query->param('tag');
my $exclude_subD= $query->param('ex_subdomain');

# save the users current campaign filter option in a cookie that essentially never expires

my $cookie = "brand_type=$brand_type; path=/; expires=Sun, 27-Dec-2099 01:01:01 GMT";
my $cookie1 = "ctid=$third_party_id; path=/; expires=Sun, 27-Dec-2099 01:01:01 GMT";
my $cookie2 = "tag=$tag; path=/; expires=Sun, 27-Dec-2099 01:01:01 GMT";
my $cookie3 = "ex_subdomain=$exclude_subD; path=/; expires=Sun, 27-Dec-2099 01:01:01 GMT";

# set the cookie
print "Set-Cookie: $cookie\n";
print "Set-Cookie: $cookie1\n";
print "Set-Cookie: $cookie2\n";
print "Set-Cookie: $cookie3\n";

# go to back to main menu page

print "Location: client_list.cgi\n\n";
exit(0);
