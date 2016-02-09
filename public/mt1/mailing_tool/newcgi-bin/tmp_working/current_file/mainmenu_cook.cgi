#!/usr/bin/perl
# *****************************************************************************************
# mainmenu_cook.cgi
#
# set a cookie and redisplay the main menu 
#
# History
# Grady Nash, 10/02/2001, Creation
# *****************************************************************************************

use strict;
use CGI;

my $query = CGI->new();
my $filteropt = $query->param('filteropt');
my $networkopt = $query->param('networkopt');
my $cstring = $query->param('cstr');

# save the users current campaign filter option in a cookie that essentially never expires

my $cookie = "filteropt=$filteropt; path=/; expires=Sun, 27-Dec-2009 01:01:01 GMT";
my $cookie2 = "networkopt=$networkopt; path=/; expires=Sun, 27-Dec-2009 01:01:01 GMT";
my $cookie1 = "cstring=$cstring; path=/; expires=Sun, 27-Dec-2009 01:01:01 GMT";

# set the cookie
print "Set-Cookie: $cookie\n";
print "Set-Cookie: $cookie1\n";
print "Set-Cookie: $cookie2\n";

# go to back to main menu page

print "Location: mainmenu.cgi\n\n";
exit(0);
