#!/usr/bin/perl
# *****************************************************************************************
# dd_setup.cgi
#
# this page display main page for editing a DailyDeal setting 
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
my $dd_id= $query->param('dd_id');
#
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<HTML>
<TITLE>Daily Deal/Trigger Settings</TITLE>
<frameset rows="245,*" border=0 width=0 frameborder=no framespacing=0>
  <frame src="dd_setup_top.cgi?dd_id=$dd_id&classid=4" name="top1" marginwidth=0 marginheight=0 scrolling=auto>
  <frame src="dd_isp_setup.cgi?dd_id=$dd_id&classid=4" name="bottom" marginwidth=0 marginheight=0 scrolling=yes>
</frameset>
</html>
end_of_html
exit(0);
