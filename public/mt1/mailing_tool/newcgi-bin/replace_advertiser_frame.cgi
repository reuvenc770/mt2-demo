#!/usr/bin/perl
#===============================================================================
# Name   : replace_advertiser_frame.cgi 
#
#--Change Control---------------------------------------------------------------
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
my $aid=$query->param('aid');
#------  connect to the util database -----------
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<frameset rows="50,*" border=1 width=0 frameborder=no framespacing=0>
  <frame src="/cgi-bin/replace_advertiser_top.cgi?aid=$aid" name="main" marginwidth=0 marginheight=0 scrolling=auto>
  <frame src="/cgi-bin/replace_advertiser.cgi?aid=$aid" name="bottom" marginwidth=0 marginheight=0 scrolling=auto resize=no>
</frameset>
</html>
end_of_html
