#!/usr/bin/perl
# *****************************************************************************************
# notloggedin.cgi
#
# History
# Grady Nash, 7/30/01, Creation
# *****************************************************************************************

use strict;

print "Content-type: text/html\n\n";
print "<html>\n";
print "<head><title>Error</title></head>\n";
print "<body>Error: You are not logged in, or you do not have the correct permissions to access this page.</body>\n";
print "</html>\n";
exit(0);
