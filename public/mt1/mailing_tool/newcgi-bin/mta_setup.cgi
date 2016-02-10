#!/usr/bin/perl
# *****************************************************************************************
# mta_setup.cgi
#
# this page display main page for editing a mta setting 
#
# History
# Jim Sobeck, 03/27/07, Creation
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

#------- check for login ------------------
my $user_id = util::check_security();
if ($user_id == 0)
{
        print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}
my $mta_id= $query->param('mta_id');
#
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<HTML>
<TITLE>MTA Settings</TITLE>
<frameset rows="245,*" border=0 width=0 frameborder=no framespacing=0>
  <frame src="mta_setup_top.cgi?mta_id=$mta_id&classid=3" name="top1" marginwidth=0 marginheight=0 scrolling=auto>
  <frame src="mta_isp_setup.cgi?mta_id=$mta_id&classid=3" name="bottom" marginwidth=0 marginheight=0 scrolling=yes>
</frameset>
</html>
end_of_html
exit(0);
