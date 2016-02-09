#!/usr/bin/perl
#===============================================================================
# Purpose: Add advertiser domain for a brand 
# Name   : add_adv_domain.cgi 
#
#--Change Control---------------------------------------------------------------
# 02/27/08  Jim Sobeck  Creation
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
my $dbh;
my $bid = $query->param('bid');
my $upd= $query->param('upd');
my $aid;
my $aname;

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}
#------  connect to the util database -----------
my ($dbhq,$dbhu)=$util->get_dbh();
#

#--------------------------------
# get CGI Form fields
#--------------------------------
        print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>
  <head>
	<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
	<title>Add Advertiser Mailing Domains</title>
  </head>
  <body>
<form action="/cgi-bin/sav_adv_domain.cgi" method="post">
<input type=hidden name=bid value=$bid>
<input type=hidden name=upd value=$upd>
	<p><b>Advertiser:</b></br>

<select name="adv_id">
end_of_html
$sql="select advertiser_id,advertiser_name from advertiser_info where status='A' and allow_strongmail='Y' order by advertiser_name";
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($aid,$aname)=$sth->fetchrow_array())
{
	print "<option value=$aid>$aname</option>\n";
}
$sth->finish();
print<<"end_of_html";
</select>

<p><b>URLs: (Hit ENTER after each one) </b><br>
<textarea name="domain" rows="7" cols="82"></textarea></p>

<p>
<input type=image name="add_url" height="22" src="/images/save_rev.gif" width="81" border="0">
</form>
</body>
</html>
end_of_html
