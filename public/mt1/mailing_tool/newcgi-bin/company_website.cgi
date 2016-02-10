#!/usr/bin/perl
#===============================================================================
# Purpose: Edit company website data 
# Name   : company_website.cgi 
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
my $submit;
my $password;
my $notes;
my $company_id = $query->param('company_id');
my $website_id = $query->param('website_id');
my $addr;
my $email_addr;
my $wid;
my $default_flag;

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
$sql = "select company_name from company_info where company_id=$company_id"; 
$sth = $dbhq->prepare($sql);
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
<title>Company Info</title>
</head>
<body>
<b>Company Name:</b>: $company</b><br>
<center>
<table width=70% border=1>
<tr><th>Website</th><th>Username</th><th>Password</th><th>Default</th><th></th></tr>
end_of_html
$sql="select website_id,website,username,password,default_flag from company_info_website where company_id=$company_id order by website";
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($wid,$website,$username,$password,$default_flag) = $sth->fetchrow_array())
{
	print "<tr><td><a href=\"company_website.cgi?company_id=$company_id&website_id=$wid\">$website</a></td><td>$username</td><td>$password</td><td>$default_flag</td><td><a href=\"company_website_delete.cgi?company_id=$company_id&website_id=$wid\">Delete</a></td></tr>\n";
}
$sth->finish();

if ($website_id ne "")
{
	$sql="select website_id,website,username,password,default_flag from company_info_website where website_id=$website_id";
	$sth = $dbhq->prepare($sql);
	$sth->execute();
	($wid,$website,$username,$password,$default_flag) = $sth->fetchrow_array();
	$sth->finish();
	$submit="Update";
}
else
{
	$website="";
	$username="";
	$password="";
	$default_flag="N";
	$submit="Add";
}
print<<"end_of_html";
</table>
<p>
<form method=post name="company" action="/cgi-bin/company_add_website.cgi">
<input type=hidden value=$company_id name="company_id">
<input type=hidden value=$website_id name="website_id">
<table width=70%>
<tr><td><b>Website:</b><input maxLength="255" size="50" name="website" value="$website"></td>
<td><b>Username:</b><input maxLength="50" size="50" name="username" value="$username"></td></tr>
<tr><td><b>Password:</b><input maxLength="20" size="20" name="password" value="$password"></td>
end_of_html
if ($default_flag eq "Y")
{
	print "<td><b>Default: </b><input type=radio value=Y checked name=default_flag>Yes&nbsp;&nbsp;<input type=radio value=N name=default_flag>No</td></tr>\n";
}
else
{
	print "<td><b>Default: </b><input type=radio value=Y name=default_flag>Yes&nbsp;&nbsp;<input type=radio value=N checked name=default_flag>No</td></tr>\n";
}
print<<"end_of_html";
<tr><td colspan=2 align=middle><input type="submit" name=submit value="$submit"></td></tr>
</table>
</form>
<center>
<a href=company_list.cgi>Home</a>
</center>
</body>

</html>
end_of_html
