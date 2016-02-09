#!/usr/bin/perl
#===============================================================================
# Purpose: Builds frames for unique campaigns 
# Name   : unique_main.cgi 
#
#--Change Control---------------------------------------------------------------
# 05/19/08  Jim Sobeck  Creation
#===============================================================================

#-----  include Perl Modules ---------
use strict;
use CGI;
use util;

#------  get some objects to use later ---------
my $util = util->new;
my $query = CGI->new;

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}
my $usa_id=$query->param('usa_id');
#------  connect to the util database -----------
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<frameset rows="*,0" border=1 width=0 frameborder=no framespacing=0>
  <frame src="/cgi-bin/unique_advertiser_edit.cgi?usa_id=$usa_id" name="main" marginwidth=0 marginheight=0 scrolling=auto>
  <frame src="/blank.html" name="hidden" marginwidth=0 marginheight=0 scrolling=no resize=no>
</frameset>
</html>
end_of_html
