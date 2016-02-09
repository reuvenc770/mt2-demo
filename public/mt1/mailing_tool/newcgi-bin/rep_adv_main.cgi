#!/usr/bin/perl

# *****************************************************************************************
# rep_adv_main.cgi
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
my $aid=$query->param('aid');
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<HTML>
<TITLE>Advertiser Subject/Creative Raw Report</TITLE>
<frameset rows="100,*" border=0 width=0 frameborder=no framespacing=0>
  <frame src="/cgi-bin/rep_adv_top.cgi?aid=$aid" name="main" marginwidth=0 marginheight=0 scrolling=no>
  <frame src="/blank.html" name="bottom" marginwidth=0 marginheight=0 scrolling=auto resize=no>
</frameset>
</html>
end_of_html
exit(0);
