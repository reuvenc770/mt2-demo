#!/usr/bin/perl
# *****************************************************************************************
# complaint_setup.cgi
#
# this page display main page for editing complaint/deliverable setting 
#
# History
# Jim Sobeck, 12/17/08, Creation
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
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<HTML>
<TITLE>Complaint/Deliverable Settings</TITLE>
<frameset rows="245,*" border=0 width=0 frameborder=no framespacing=0>
  <frame src="complaint_setup_top.cgi?classid=4" name="top1" marginwidth=0 marginheight=0 scrolling=auto>
  <frame src="complaint_setup_bot.cgi?classid=4" name="bottom" marginwidth=0 marginheight=0 scrolling=yes>
</frameset>
</html>
end_of_html
exit(0);
