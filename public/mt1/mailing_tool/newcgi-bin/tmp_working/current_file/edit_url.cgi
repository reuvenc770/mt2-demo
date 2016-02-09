#!/usr/bin/perl
#===============================================================================
# Purpose: Edit URLs for a Brand 
# Name   : edit_url.cgi 
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
$sql = "select url from brand_url_info where url_id=$uid and url_type='$utype'"; 
$sth = $dbh->prepare($sql);
$sth->execute();
($url) = $sth->fetchrow_array();
$sth->finish();

if ($utype eq "O")
{
	$url_str = "Others";
}
elsif ($utype eq "Y")
{
	$url_str = "Yahoo";
}
elsif ($utype eq "OI")
{
	$url_str = "Others Image";
}
elsif ($utype eq "YI")
{
	$url_str = "Yahoo Image";
}
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Edit Brand URLs</title>
</head>
<body>
<form method=post action="/cgi-bin/upd_url.cgi">
<input type=hidden name=uid value="$uid">
<p><b>$url_str URL: </b><input type=text name=curl value="$url" maxlength=255><br>
											<input type=image height="22" src="/images/save_rev.gif" width="81" border="0">
</form>
</body>
</html>
end_of_html
