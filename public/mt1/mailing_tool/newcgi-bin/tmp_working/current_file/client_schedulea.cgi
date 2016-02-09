#!/usr/bin/perl
#===============================================================================
# Purpose: Top frame of client_schedule.html page 
# Name   : client_schedulea.cgi 
#
#--Change Control---------------------------------------------------------------
# 06/29/05  Jim Sobeck  Creation
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
my $MAX_CAMPS=40;

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
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>
<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Schedule Details</title>
<script language="JavaScript">
function update_client()
{
    var selObj = document.getElementById('client_id');
    var selIndex = selObj.selectedIndex;
    parent.frames[2].location="/cgi-bin/upd_schedule_info.cgi?cid="+selObj.options[selIndex].value;
}
function set_lists(camp_cnt,aol_cnt,daily_cnt,rotate_cnt,third_cnt)
{
    var selObj = document.getElementById('camp_cnt');
	selObj.selectedIndex = camp_cnt;
    var selObj = document.getElementById('aol_cnt');
	selObj.selectedIndex = aol_cnt;
    var selObj = document.getElementById('daily_cnt');
	selObj.selectedIndex = daily_cnt;
    var selObj = document.getElementById('rotating_cnt');
	selObj.selectedIndex = rotate_cnt;
    var selObj = document.getElementById('third_cnt');
	selObj.selectedIndex = third_cnt;
}
</script>
</head>

<body><br><b><font face="Verdana">Schedule Details</font></b><font face="Verdana"><b>:</b></font><br>
<form name=campform method=get action="/cgi-bin/upd_client_schedule.cgi" target="bottom">
<table border="1" width="35%" id="table24">
	<tr>
		<td>
									<b><font face="Verdana" size="2">Network:</font></b></td>
		<td>
											<font face="Arial" color="#509c10" size="2">
											<select name="client_id" onChange="update_client();">
<option value=0>--Select One--</option>
end_of_html
$sql="select user_id,company from user where status='A' order by company";
$sth = $dbh->prepare($sql);
$sth->execute();
while (($id,$company) = $sth->fetchrow_array())
{
	print "<option value=$id>$company</option>\n";
}
$sth->finish();
print<<"end_of_html";
											</select></font><input type="submit" value="Go"> </td>
	</tr>
	<tr>
		<td>
									<b><font face="Verdana" size="2">Campaigns:</font></b></td>
		<td>
									<font size="1">
													<select name="camp_cnt">
					<option value="0" selected>0</option>
end_of_html
my $i=1;
while ($i <= $MAX_CAMPS)
{
	print "<option value=$i>$i</option>\n";
	$i++;
}
print<<"end_of_html";
</select></font></td>
	</tr>
	<tr>
		<td>
									<b><font face="Verdana" size="2">3rd Party:</font></b></td>
		<td>
									<font size="1">
													<select name="third_cnt">
					<option value="0" selected>0</option>
end_of_html
my $i=1;
while ($i <= $MAX_CAMPS)
{
	print "<option value=$i>$i</option>\n";
	$i++;
}
print<<"end_of_html";
</select></font></td>
	</tr>
	<tr>
		<td>
									<b><font face="Verdana" size="2">AOL:</font></b></td>
		<td>
									<font size="1">
													<select name="aol_cnt">
					<option value="0" selected>0</option>
end_of_html
my $i=1;
while ($i <= $MAX_CAMPS)
{
	print "<option value=$i>$i</option>\n";
	$i++;
}
print<<"end_of_html";
</select></font></td>
	</tr>
	<tr>
		<td>
									<b><font face="Verdana" size="2">Daily:</font></b></td>
		<td>
									<font size="1">
													<select name="daily_cnt">
					<option value="0" selected>0</option>
end_of_html
my $i=1;
while ($i <= $MAX_CAMPS)
{
	print "<option value=$i>$i</option>\n";
	$i++;
}
print<<"end_of_html";
</select></font></td>
		</tr>
	<tr>
		<td>
									<b><font face="Verdana" size="2">Rotating:</font></b></td>
		<td>
									<font size="1">
													<select name="rotating_cnt">
					<option value="0" selected>0</option>
end_of_html
my $i=1;
while ($i <= $MAX_CAMPS)
{
	print "<option value=$i>$i</option>\n";
	$i++;
}
print<<"end_of_html";
</select></font></td>
		</tr>
	</table>
<p align="left">
						<a href="/cgi-bin/mainmenu.cgi" target=_top>
						<img border="0" src="/images/home_blkline.gif" width="76" height="23"></a></p>
<script language="JavaScript">
update_client();
</script>
</body>
</html>
end_of_html
