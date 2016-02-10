#!/usr/bin/perl
#===============================================================================
# Purpose: Top frame of sm2 deploy page 
# Name   : sm2_deploy_main.cgi 
#
#--Change Control---------------------------------------------------------------
# 12/08/08  Jim Sobeck  Creation
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
my $tid=$query->param('tid');
#------  connect to the util database -----------
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<frameset rows="*,0" border=1 width=0 frameborder=no framespacing=0>
  <frame src="/cgi-bin/sm2_deploy.cgi?tid=$tid" name="main" marginwidth=0 marginheight=0 scrolling=auto>
  <frame src="/blank.html" name="hidden" marginwidth=0 marginheight=0 scrolling=no resize=no>
</frameset>
</html>
end_of_html
