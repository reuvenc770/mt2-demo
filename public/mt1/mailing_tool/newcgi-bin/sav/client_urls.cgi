#!/usr/bin/perl
#===============================================================================
# Purpose: Edit URLs for a client 
# Name   : client_urls.cgi 
#
#--Change Control---------------------------------------------------------------
# 06/08/05  Jim Sobeck  Creation
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
my $aim;
my $website;
my $username;
my $brand_str;
my $category_name;
my $cid = $query->param('cid');
my $url;
my $uid;
my $company;
my $cstatus;

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
$sql = "select company from user where user_id=$cid"; 
$sth = $dbh->prepare($sql);
$sth->execute();
($company) = $sth->fetchrow_array();
$sth->finish();

#--------------------------------
# get CGI Form fields
#--------------------------------
        print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Client URLs</title>
</head>
<body>
<p><b>Network: </b>$company</br>
<form method=post action="/cgi-bin/add_client_url.cgi">
<input type=hidden name=cid value=$cid>
URL: <input type=text name=url maxlength=50 size=50>&nbsp;&nbsp;<input type=image src="/images/add.gif" border=0>
</form>
<p>
<table width=40% border=1><tr><th>URL</th><th>&nbsp;</th></tr>
end_of_html
$sql = "select url_id,url,status from client_urls where client_id=$cid"; 
$sth = $dbh->prepare($sql);
$sth->execute();
while (($uid,$url,$cstatus) = $sth->fetchrow_array())
{
	if ($cstatus eq "A")
	{
		print "<tr><td>$url</td><td><a href=\"/cgi-bin/delete_client_url.cgi?id=$uid&cid=$cid&cstat=D\" onClick=\"return confirm('Are you sure you want to delete this URL?\\nClick OK to delete');\">Delete</a></td></tr>\n";
	}
	else
	{
		print "<tr><td>$url</td><td><a href=\"/cgi-bin/delete_client_url.cgi?id=$uid&cid=$cid&cstat=A\" onClick=\"return confirm('Are you sure you want to activate this URL?\\nClick OK to activate');\">Activate</a></td></tr>\n";
	}
}
$sth->finish();
print<<end_of_html
</table><br><br>
<a href="/cgi-bin/client_list.cgi"><img src="/images/home_blkline.gif" border=0></a>
</body>
</html>
end_of_html
