#!/usr/bin/perl
#===============================================================================
# Purpose: Adds URLs for a Brand
# Name   : add_host_main.cgi
#
#--Change Control---------------------------------------------------------------
# 06/07/06  Jim Sobeck  Creation
#===============================================================================

#-----  include Perl Modules ---------
use strict;
use CGI;
use util;

#------  get some objects to use later ---------
my $util = util->new;
my $query = CGI->new;
my $name;
my $sql;
my $sth;
my $bid = $query->param('bid');
my $utype= $query->param('type');
print "Content-type: text/html\n\n";
print<<"end_of_html";
<HTML>
<TITLE>Add Test AOL Host</TITLE>
<frameset rows="*,0" border=1 width=0 frameborder=no framespacing=0>
  <frame src="/newcgi-bin/add_host.cgi?bid=$bid&type=$utype" name="main" marginwidth=0 marginheight=0 scrolling=no>
  <frame src="/blank.html" name="bottom" marginwidth=0 marginheight=0 scrolling=auto resize=no>
</frameset>
</html>
end_of_html
