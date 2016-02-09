#!/usr/bin/perl

# ******************************************************************************
# dd_send_seeds.cgi
#
# this page loads daily deal seeds
#
# History
# ******************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

my $util = util->new;
my $query = CGI->new;

my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}
my $dd_id=$query->param('dd_id');
my $seedlist=$query->param('seedlist');

my @args = ("/bin/csh /var/www/html/newcgi-bin/load_dailydeal_seeds.csh $dd_id $seedlist");
system(@args) == 0 or print "system @args failed: $?";
print "Content-type: text/html\n\n";
print<<"end_of_html";
<html><head><title>Send Daily Deal Seeds</title></head>
<body>
<center>
<h3>Daily Deal Seeds have been loaded.</h3>
<br>
<a href=mainmenu.cgi>Home</a>
</body>
</html>
end_of_html
