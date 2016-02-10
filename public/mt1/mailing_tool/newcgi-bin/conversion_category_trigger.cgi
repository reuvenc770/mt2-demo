#!/usr/bin/perl

# *****************************************************************************************
# conversion_category_trigger.cgi
#
# this page display main page for setting up conversion triggers for category 
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

my $cid = $query->param('cid');
my $client_id= $query->param('userid');
#
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<HTML>
<TITLE>Conversion Category Trigger Setup</TITLE>
<frameset rows="*,0" border=1 width=0 frameborder=no framespacing=0>
  <frame src="conversion_category_triggera.cgi?cid=$cid&client_id=$client_id" name="main" marginwidth=0 marginheight=0 scrolling=yes>
  <frame src="/blank.html" name="hidden" marginwidth=0 marginheight=0 scrolling=no resize=no>
</frameset>
</html>
end_of_html
exit(0);
