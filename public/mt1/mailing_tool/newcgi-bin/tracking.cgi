#!/usr/bin/perl
#===============================================================================
# Purpose: Edit advertiser data (eg 'user' table).
# Name   : advertiser_disp.cgi (edit_advertiser_info.cgi)
#
#--Change Control---------------------------------------------------------------
# 01/05/04  Jim Sobeck  Creation
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
my $phone;
my $email;
my $company;
my $id;
my $aim;
my $website;
my $username;
my $password;
my $notes;
my $url;
my $code;
my $daily_deal;
my $aid = $query->param('aid');
my $ctype = $query->param('ctype');

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}
#------  connect to the util database -----------
my ($dbhq,$dbhu)=$util->get_dbh();
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Tracking</title>
</head>

<body>
<table width=80%><tr><th align=center>Client</th><th align=center>URL</th><th align=center>Code</th><th>URL Type</th></tr>
end_of_html
if ($ctype eq "N")
{
	$sql="select url,code,company,daily_deal from advertiser_tracking,user where advertiser_tracking.client_id=user.user_id and advertiser_id=$aid and daily_deal in ('N','Y','T')";
}
else
{
	$sql="select url,code,company,daily_deal from advertiser_tracking,user where advertiser_tracking.client_id=user.user_id and advertiser_id=$aid and daily_deal = '$ctype'";
}
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($url,$code,$company,$daily_deal) = $sth->fetchrow_array())
{
	print "<tr><td>$company</td><td>$url</td><td>$code</td><td align=middle>$daily_deal</td></tr>\n";
}
$sth->finish();
print<<"end_of_html";
<table>
<br><br>
<b>Tracking: </b><br>
<form method=post action="/cgi-bin/upd_tracking.cgi">
<input type=hidden name=aid value=$aid>
<input type=hidden name=ctype value="$ctype">
<b>List Name:</b>
<select name="client_id">
end_of_html
$sql="select user_id,company from user where status='A' order by company";
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($id,$company) = $sth->fetchrow_array())
{
	if ($id == 1)
	{
		print "<option selected value=$id>$company</option>\n";
	}
	else
	{
		print "<option value=$id>$company</option>\n";
	}
}
$sth->finish();
print<<"end_of_html";
</select><br>
<br><b>URL Type:</b><input type=radio checked value="N" name="dailydeal">Normal&nbsp;&nbsp;<input type=radio value="Y" name="dailydeal">Daily Deal&nbsp;&nbsp;<input type=radio value="T" name="dailydeal">Trigger</br>
											<br><b>URL:</b><br>
											<input maxLength="255" size="50" name="tracking_url"><br><br>
<b>Code: (to tie it back to the network - stat purposes)</b><br>
											<input maxLength="255" size="50" name="tracking_code" value="400001"><br>
<p>
											
											<input type=image height="22" src="/images/save_rev.gif" width="81" border="0"><a href="/cgi-bin/advertiser_disp2.cgi?puserid=$aid"><img src="/images/cancel.gif" border=0></a></p>
</form>
</body>
</html>
end_of_html
