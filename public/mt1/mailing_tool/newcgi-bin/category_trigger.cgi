#!/usr/bin/perl

# *****************************************************************************************
# category_trigger.cgi
#
# this page display main page for setting up triggers for category 
#
# History
# Jim Sobeck, 10/03/05, Creation
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

my $cid = $query->param('cid');
my $client_id= $query->param('userid');
my $trigger_type= $query->param('trigger_type');
#
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<HTML>
<TITLE>Category Trigger Setup</TITLE>
<frameset rows="*,0" border=1 width=0 frameborder=no framespacing=0>
  <frame src="category_triggera.cgi?cid=$cid&client_id=$client_id&trigger_type=$trigger_type" name="main" marginwidth=0 marginheight=0 scrolling=yes>
  <frame src="/blank.html" name="hidden" marginwidth=0 marginheight=0 scrolling=no resize=no>
</frameset>
</html>
end_of_html
exit(0);
