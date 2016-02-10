#!/usr/bin/perl

# *****************************************************************************************
# logout.cgi
#
# History
# Grady Nash, 7/30/01, Creation
# *****************************************************************************************

# include Perl Modules

use strict;

# reset the cookie to expired

my $cookie = "utillogin=0; path=/; expires=Thu, 01-Jan-70 00:00:01 GMT";
print "Set-Cookie: $cookie\n";

# go back to the logon page

print "Location: login_form.cgi\n\n";

exit(0);
