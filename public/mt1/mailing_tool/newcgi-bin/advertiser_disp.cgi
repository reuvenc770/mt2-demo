#!/usr/bin/perl

# *****************************************************************************************
# advertiser_disp.cgi
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
my $pmode=$query->param('pmode');

print "Content-Type: text/html\n\n";
print<<"end_of_html";
<HTML>
<TITLE>Advertiser Company Info</TITLE>
<frameset rows="*,0" border=0 width=0 frameborder=no framespacing=0>
  <frame src="/cgi-bin/advertiser_dispa.cgi?pmode=$pmode" name="main" marginwidth=0 marginheight=0 scrolling=no>
  <frame src="/blank.html" name="hidden" marginwidth=0 marginheight=0 scrolling=no resize=no>
</frameset>
</html>
end_of_html
exit(0);
