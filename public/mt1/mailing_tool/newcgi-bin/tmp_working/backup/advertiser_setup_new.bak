#!/usr/bin/perl

# *****************************************************************************************
# advertiser_setup.cgi
#
# this page display main page for editing a campaign 
#
# History
# Jim Sobeck, 01/24/05, Creation
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

my $aid = $query->param('aid');
#
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<HTML>
<TITLE>Advertiser Setup</TITLE>
<frameset rows="100,*,0" border=1 width=0 frameborder=no framespacing=0>
  <frame src="advertiser_setup_top.cgi?aid=$aid" name="top1" marginwidth=0 marginheight=0 scrolling=auto>
  <frame src="advertiser_setupa_new.cgi?aid=$aid&did=4" name="main" marginwidth=0 marginheight=0 scrolling=yes>
  <frame src="/blank.html" name="hidden" marginwidth=0 marginheight=0 scrolling=no resize=no>
</frameset>
</html>
end_of_html
exit(0);
