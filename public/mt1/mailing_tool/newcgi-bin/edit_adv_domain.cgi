#!/usr/bin/perl
#===============================================================================
# Purpose: Edit advertiser domain for a brand 
# Name   : edit_adv_domain.cgi 
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
my $aid = $query->param('aid');

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
$sql = "select advertiser_name from advertiser_info where advertiser_id=$aid"; 
$sth = $dbhq->prepare($sql);
$sth->execute();
my $aname;
($aname) = $sth->fetchrow_array();
$sth->finish();
        print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Advertiser Domains for a Brand</title>
</head>
<body>
<p>Advertiser: <b>$aname</b></br>
<p><b>Current Domains: </b><b>(Check each box to permanently remove - <font color=red>WARNING - This cannot be undone</font>)</b></br>
<form action="/cgi-bin/sav_adv_domain.cgi" method="post">
<input type=hidden name=adv_id value=$aid>
<input type=hidden name=bid value=$bid>
end_of_html
$sql = "select domain_name from brand_advertiser_info where brand_id=$bid and advertiser_id=$aid order by domain_name";
$sth = $dbhq->prepare($sql);
$sth->execute();
my $csubject;
while (($csubject) = $sth->fetchrow_array())
{
	print "&nbsp;&nbsp;&nbsp;<input type=checkbox name=deldom value=$csubject>$csubject<br>\n";
}
$sth->finish();
print<<end_of_html
<p><b>URL: (Hit ENTER after each one) </b><br>
<textarea name="domain" rows="7" cols="82"></textarea></p>
<p>
											
											<input type=image height="22" src="/images/save_rev.gif" width="81" border="0">&nbsp;&nbsp;
</form>
</body>
</html>
end_of_html
