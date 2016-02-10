#!/usr/bin/perl
#-----  include Perl Modules ---------
use strict;
use CGI;
use util;

#------  get some objects to use later ---------
my $util = util->new;
my $query = CGI->new;
my $sth;
my $sql;

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}
my ($dbhq,$dbhu)=$util->get_dbh();
print "Content-type: text/html\n\n";
print<<"end_of_html";
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Add Email Classes</title>
<style>
body{
	background-color:#99d1f4;
	font-family:"Trebuchet MS", Arial, Helvetica, sans-serif;
	font-size:14px;
	color:#000;}
	
a{
	color:#000;}
	
#topbuttons{
	padding-left: 250px;
	padding-top:25px;
	padding-bottom:25px;}
</style>
</head>

<body>
<table width="1024" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td width="517" align="right"><a href="#" style="color:#397c00; text-decoration:none;"><b>Logout</a>&nbsp;&nbsp;&nbsp;<a href="#" style="color:#397c00; text-decoration:none;">Customer Assistance</a></b></td>
  </tr>
</table>
<form method=post action=upd_emailclass.cgi>
<table width="1024" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td>
    <div id="topbuttons">
    <a href="/cgi-bin/mainmenu.cgi"><img src="/images/but_home.gif" border="0" width="169" height="34" style="padding-right:10px;"></a>
    </div>
	</td>
	</tr>
<tr><td>Class Name: <input type=text name=emailclass size=30></td></tr>
<tr><td align=middle><input type=submit value="Add Class"></td></tr>
</table>
</form>
</body>
</html>
end_of_html
