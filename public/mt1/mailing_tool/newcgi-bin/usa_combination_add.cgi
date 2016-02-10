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
<title>USA Composition Add</title>
<style>
body{
	background-color:#99d1f4;
	font-family:"Trebuchet MS", Arial, Helvetica, sans-serif;
	font-size:14px;
	color:#000;}
	
a{
	color:#000;}
	
#topbuttons{
	padding-left: 450px;
	padding-top:25px;
	padding-bottom:10px;}

#topinfo{
	padding-left:375px;}
</style>
</head>

<body>
<table width="1024" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td width="517" style="color:#397c00;" align="right"><a href="#" style="color:#397c00; text-decoration:none;"><b>Logout</a>&nbsp;&nbsp;&nbsp;<a href="#" style="color:#397c00; text-decoration:none;">Customer Assistance</a></b></td>
  </tr>
</table>
<table width="1024" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td>
    <div id="topbuttons">
    <a href="mainmenu.cgi"><img src="/images/but_home.gif" border="0" width="169" height="34"></a>
    </div>
    <div id="topinfo">
	<form method=post action=usa_combination_ins.cgi>
    <table width="300" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="62" style="padding-bottom:5px;">Name:</td>
        <td width="238" style="padding-bottom:5px;"><INPUT TYPE="text" NAME="name" SIZE="30" maxlength=100></td>
      </tr>
      <tr>
        <td style="padding-bottom:20px;">Advertiser: </td>
        <td style="padding-bottom:20px;"><select name=aid>
end_of_html
my $aname;
my $aid;
$sql="select advertiser_id,advertiser_name from advertiser_info where status='A' and test_flag='N' order by advertiser_name";
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($aid,$aname)=$sth->fetchrow_array())
{
	print "<option value=$aid>$aname</option>";
}
$sth->finish();
print<<"end_of_html";
	  </select></td>
      </tr>
	  <tr><td colspan=2 align=middle><input type=submit value=Add></td></tr>
    </table>
	</form>
    </div>
<div id="topbuttons">
    <a href="mainmenu.cgi"><img src="/images/but_home.gif" border="0" width="169" height="34" style="padding-right:10px;"></a>
    </div>
    </td>
  </tr>
</table>
</body>
</html>
end_of_html
