#!/usr/bin/perl
#===============================================================================
# Purpose: Edit Hosts for a Brand 
# Name   : edit_host.cgi 
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
my $uid = $query->param('uid');
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
$sql = "select server_name,ip_addr from brand_host where brand_host_id=$uid and server_type='$utype'"; 
$sth = $dbh->prepare($sql);
$sth->execute();
($url,$ip_addr) = $sth->fetchrow_array();
$sth->finish();

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
<title>Edit Brand Host</title>
</head>
<body>
<form method=post action="/cgi-bin/upd_host.cgi">
<input type=hidden name=uid value="$uid">
<p><b>$url_str Host: </b><input type=text name=curl value="$url" maxlength=255><br>
end_of_html
if (($utype eq "A") or ($utype eq "T"))
{
	print "<b>&nbsp;&nbsp;&nbsp;&nbsp;$url_str IP: </b><input type=text name=ip_addr value=\"$ip_addr\" maxlength=20><br>\n";
}
else
{
	print "<input type=hidden name=ip_addr values=\"\">\n";
}
print<<"end_of_html";
<input type=image height="22" src="/images/save_rev.gif" width="81" border="0">
</form>
</body>
</html>
end_of_html
