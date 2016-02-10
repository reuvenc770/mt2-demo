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
<title>Email Classes</title>
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
<form method=post action=move_domain.cgi>
<table width="1024" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td>
    <div id="topbuttons">
    <a href="/cgi-bin/mainmenu.cgi"><img src="/images/but_home.gif" border="0" width="169" height="34" style="padding-right:10px;"></a>
    <a href="/cgi-bin/add_emailclass.cgi"><img src="/images/but_addclass.gif" border="0" width="169" height="34" style="padding-right:10px;"></a>
    <input type=image src="/images/but_update.gif" border="0" width="168" height="34">
    </div>
    <table width="700" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td colspan=2><b style="color:#FFF; font-size:18px;">Class to move selected/entered to: <select name=moveclass>
end_of_html
my $cid;
my $cname;
$sql="select class_id,class_name from email_class where status='Active' order by class_name";
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($cid,$cname)=$sth->fetchrow_array())
{
	print "<option value=$cid>$cname</option>\n";
}
$sth->finish();
print<<"end_of_html";
</select>&nbsp;&nbsp;Domain: <input type=text name=cdomain size=20><br><br></td></tr>
  <tr>
    <td bgcolor="#509c10"><b style="color:#FFF; font-size:18px;"><center>Email Classes</center></b></td>
  </tr>
end_of_html
my $cnt=0;
my $bgcolor;
$sql="select class_id,class_name from email_class where status='Active' and class_id not in (4,45,7,49,21) order by class_name";
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($cid,$cname)=$sth->fetchrow_array())
{
	if ($cnt % 2)
	{	
		$bgcolor="#d6c6ff";
	}
	else
	{
		$bgcolor="#ebfad1";
	}
	$cnt++;
print<<"end_of_html";
  <tr>
    <td colspan=5 bgcolor=$bgcolor style="padding-left:10px;"><b style="font-size:18px;">$cname</b>
<!--    <a href="add_domain.cgi?cid=$cid" style="padding-left:60px;">Add Domain</a>  -->
	<td></tr>
	<tr>
end_of_html
	my $rcnt=0;
	$sql="select domain_id,domain_name from email_domains where domain_class=? and suppressed=0 order by domain_name";
	my $sth1=$dbhu->prepare($sql);
	$sth1->execute($cid);
	my $did;
	my $dname;
	while (($did,$dname)=$sth1->fetchrow_array())
	{
    	print "<td bgcolor=$bgcolor><input type=checkbox name=option1 value=$did>$dname</td>";
		$rcnt++;
		if ($rcnt >= 4)
		{
			$rcnt=0;
			print "</tr><tr>";
		}
	}
	while ($rcnt <= 5)
	{
		print "<td bgcolor=$bgcolor>&nbsp;</td>";
		$rcnt++;
	}
	$sth1->finish();
    print "</tr>";
}
$sth->finish();
print<<"end_of_html";
    </table>
<div id="topbuttons">
    <a href="/cgi-bin/mainmenu.cgi"><img src="/images/but_home.gif" border="0" width="169" height="34" style="padding-right:10px;"></a>
    <a href="/cgi-bin/add_emailclass.cgi"><img src="/images/but_addclass.gif" border="0" width="169" height="34" style="padding-right:10px;"></a>
    <input type=image src="/images/but_update.gif" border="0" width="168" height="34">
    </div>
    </td>
  </tr>
</table>
</form>
</body>
</html>
end_of_html
