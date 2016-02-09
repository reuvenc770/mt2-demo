#!/usr/bin/perl
#===============================================================================
# Purpose: Adds URLs for a Brand 
# Name   : add_aol_host.cgi 
#
#--Change Control---------------------------------------------------------------
# 11/11/05  Jim Sobeck  Creation
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
my $url;
my $url_str;

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
while (($url) = $sth->fetchrow_array())
{
	print "&nbsp;&nbsp;&nbsp;$url<br>\n";
}
$sth->finish();
print<<end_of_html
<p><b>Hosts: (Hit ENTER after each one) </b><br>
<form action="/cgi-bin/ins_host.cgi" method="post">
<input type=hidden name=bid value="$bid">
<input type=hidden name=type value="$utype">
<textarea name="curl" rows="7" cols="82"></textarea></p>
<p>
											
											<input type=image height="22" src="/images/save_rev.gif" width="81" border="0">
</form>
</body>
</html>
end_of_html
