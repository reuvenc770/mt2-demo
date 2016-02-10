#!/usr/bin/perl
#===============================================================================
# Name   : replace_advertiser_top.cgi 
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
<html>
<body>
<form action=replace_advertiser.cgi target=bottom>
<input type=hidden name=aid value=$aid>
<center>
<table id="table2" border="1" width="50%">
  <tbody>
    <tr>
      <td width="134"><b>Status</b></td>
      <td>
      <select name="cstatus1">
      <option value=""></option>
      <option value="A">Active</option>
      <option value="P">Paused</option>
      <option value="S">Setup</option>
      <option value="T" selected="selected">Testing</option>
      <option value="I">Inactive</option>
      </select>
&nbsp;or&nbsp;
      <select name="cstatus2">
      <option value=""></option>
      <option value="A" selected="selected">Active</option>
      <option value="P">Paused</option>
      <option value="S">Setup</option>
      <option value="T">Testing</option>
      </select>
&nbsp;or&nbsp;
      <select name="cstatus3">
      <option value=""></option>
      <option value="A">Active</option>
      <option value="P" selected="selected">Paused</option>
      <option value="S">Setup</option>
      <option value="T">Testing</option>
      </select>
	<input type=submit value="Filter">
      </td>
    </tr>
  </tbody>
</table>
</form>
</body>
</html>
end_of_html
