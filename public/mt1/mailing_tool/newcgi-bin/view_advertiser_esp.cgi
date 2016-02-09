#!/usr/bin/perl
#===============================================================================
# Name   : view_advertiser_esp.cgi 
#
#--Change Control---------------------------------------------------------------
#===============================================================================

# include Perl Modules
use strict;
use CGI;
use util;

# get some objects to use later
my $util = util->new;
my $query = CGI->new;
my ($sth, $sql, $dbh, $errmsg ) ;
my $sth1;
my $aname;
my $taid;

#----------- check for login ------------------
my $user_id = util::check_security();
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}

my ($dbhq,$dbhu)=$util->get_dbh();

print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>
<head>
</head>
<body>
<center>
<h3>View Advertiser</h3>
<br>
<form method=post action="/cgi-bin/view_advertiser_esp_save.cgi" target=_blank>
<table border=0 width=60%>
end_of_html
	print "<tr><td align=right><b>Advertisers</b></td><td><select name=aid multiple=multiple size=15>";
	$sql = "select advertiser_id,advertiser_name from advertiser_info where status='A'";
	$sql = $sql . " order by advertiser_name";
	$sth1 = $dbhq->prepare($sql);
	$sth1->execute();
	while (($taid,$aname) = $sth1->fetchrow_array())
	{
		print "<option value=$taid>$aname ($taid)</option>\n";
	}
	$sth1->finish;
	print "</select></td></tr>";
print<<"end_of_html";
<tr><td align=right>Creatives To User</td><td><select name=ctype><option value="A" selected>Active</option>
<tr><td align=right>Creative name:</td><td><b>-EE</b></td></tr>
<tr><td colspan=2 align=middle><input type=submit value="View Creative"></td></tr>
</table>
</form>
</body>
</html>
end_of_html
exit(0);

