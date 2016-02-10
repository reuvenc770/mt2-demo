#!/usr/bin/perl

# *****************************************************************************************
# 3rdparty_view_schedule.cgi
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
my $aid;
my $cid;
my $aname;
my $errmsg;
my $images = $util->get_images_url;
my $cfrom;
my @from_array;

print "Content-Type: text/html\n\n";
print<<"end_of_html";
<HTML>
<TITLE>View 3rdParty Schedule</TITLE>
<frameset rows="100,*,0" border=0 width=0 frameborder=no framespacing=0>
  <frame src="/cgi-bin/3rdparty_view_schedulea.cgi" name="main" marginwidth=0 marginheight=0 scrolling=no>
  <frame src="/blank.html" name="bottom" marginwidth=0 marginheight=0 scrolling=auto resize=no>
  <frame src="/blank.html" name="hidden" marginwidth=0 marginheight=0 scrolling=no resize=no>
</frameset>
</html>
end_of_html
exit(0);
