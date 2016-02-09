#!/usr/bin/perl
#===============================================================================
# Purpose: Adds URLs for a Brand 
# Name   : add_host.cgi 
#
#--Change Control---------------------------------------------------------------
# 06/15/05  Jim Sobeck  Creation
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
my $website;
my $bid = $query->param('bid');
my $utype= $query->param('type');
my $url;
my $url_str;
my $ip_addr;

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}
#------  connect to the util database -----------
$util->db_connect();
$dbh = $util->get_dbh;
#
$sql = "select server_name,ip_addr from brand_host where brand_id=$bid and server_type='$utype'"; 
$sth = $dbh->prepare($sql);

if ($utype eq "O")
{
	$url_str = "Others";
}
elsif ($utype eq "Y")
{
	$url_str = "Yahoo";
}
elsif ($utype eq "C")
{
	$url_str = "Cleanser";
}
elsif ($utype eq "A")
{
	$url_str = "AOL";
}
elsif ($utype eq "T")
{
	$url_str = "Test AOL";
}
elsif ($utype eq "H")
{
	$url_str = "Hotmail";
}
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Add Hosts</title>
</head>
<body>
<p><b>Current $url_str Hosts: </b></br>
end_of_html
$sth->execute();
while (($url,$ip_addr) = $sth->fetchrow_array())
{
	if (($utype eq "A") or ($utype eq "T"))
	{
		print "&nbsp;&nbsp;&nbsp;$url - $ip_addr<br>\n";
	}
	else
	{
		print "&nbsp;&nbsp;&nbsp;$url<br>\n";
	}
}
$sth->finish();
print<<"end_of_html";
<form action="/cgi-bin/ins_host.cgi" method="post">
<input type=hidden name=bid value="$bid">
<input type=hidden name=type value="$utype">
end_of_html
if (($utype eq "A") or ($utype eq "T"))
{
	print "Hostname: <input type=text name=curl maxlength=20></br>\n";
	print "IP Addr: <input type=text name=ip_addr maxlength=20></br>\n";
}
else
{
print<<"end_of_html";
<p><b>Hosts: (Hit ENTER after each one) </b><br>
<textarea name="curl" rows="7" cols="82"></textarea></p>
<p>
end_of_html
}
print<<"end_of_html";
<input type=image height="22" src="/images/save_rev.gif" width="81" border="0">
</form>
</body>
</html>
end_of_html
