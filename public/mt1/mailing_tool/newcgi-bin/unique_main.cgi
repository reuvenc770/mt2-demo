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
my $uid=$query->param('uid');
my $cflag=$query->param('cflag');
my $sid=$query->param('sid');
my $diffcnt=$query->param('diffcnt');
if ($sid eq "")
{
	$sid=0;
}
my $sdate=$query->param('sdate');
my $stime=$query->param('stime');
my $am_pm=$query->param('am_pm');
my $aid=$query->param('aid');
my $log_camp=$query->param('log_camp');
my $group_id=$query->param('group_id');
my $creative_id=$query->param('creative_id');
my $client_group_id=$query->param('client_group_id');
my $dup_client_group_id=$query->param('dup_client_group_id');
my $utype=$query->param('utype');
my $mta_id=$query->param('mta_id');
my $cname=$query->param('cname');
my $profile_id=$query->param('profile_id');
#------  connect to the util database -----------
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<frameset rows="*,0" border=1 width=0 frameborder=no framespacing=0>
  <frame src="/cgi-bin/unique_build.cgi?uid=$uid&cflag=$cflag&sid=$sid&diffcnt=$diffcnt&sdate=$sdate&stime=$stime&am_pm=$am_pm&aid=$aid&group_id=$group_id&client_group_id=$client_group_id&log_camp=$log_camp&utype=$utype&creative_id=$creative_id&dup_client_group_id=$dup_client_group_id&mta_id=$mta_id&profile_id=$profile_id&cname=$cname" name="main" marginwidth=0 marginheight=0 scrolling=auto>
  <frame src="/blank.html" name="hidden" marginwidth=0 marginheight=0 scrolling=no resize=no>
</frameset>
</html>
end_of_html
