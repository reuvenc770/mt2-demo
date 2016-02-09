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
my $sort_option = $query->param('sort_option');
my $networkopt = $query->param('client_id');
my $tid = $query->param('tid');
my $tflag = $query->param('tflag');

# save the users current campaign filter option in a cookie that essentially never expires

my $cookie = "soption=$sort_option; path=/; expires=Sun, 27-Dec-2099 01:01:01 GMT";
my $cookie2 = "lnetworkopt=$networkopt; path=/; expires=Sun, 27-Dec-2099 01:01:01 GMT";
my $cookie1 = "tid=$tid; path=/; expires=Sun, 27-Dec-2099 01:01:01 GMT";

# set the cookie
print "Set-Cookie: $cookie\n";
print "Set-Cookie: $cookie1\n";
print "Set-Cookie: $cookie2\n";

# go to back to main menu page

print "Location: listprofile_list.cgi?tflag=$tflag\n\n";
exit(0);
