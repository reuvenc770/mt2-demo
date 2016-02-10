#!/usr/bin/perl

# *****************************************************************************************
# auto_popup.cgi
#
# this page pops up page for auto populate 
#
# History
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $aid = $query->param('aid');
print "Content-type: text/html\n\n";
print<<"end_of_html";
<html>
<head><title>Auto Populate Popup</title>
</head>
<body>
<center>
<h3>Select Creative/Subject/From to Include:</h3>
<form method=post action="/cgi-bin/adv_auto_populate.cgi">
<input type=hidden name=aid value=$aid>
<input type=radio name=ctype value="I">Internally Approved&nbsp;&nbsp;<input type=radio name=ctype value="E">Externally Approved&nbsp;&nbsp;<input type=radio name=ctype value="A" checked>ALL<br>
<input type=submit value="Submit">
</form>
</body>
</html>
end_of_html
