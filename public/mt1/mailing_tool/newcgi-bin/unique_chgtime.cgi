#!/usr/bin/perl
#===============================================================================
# Name   : unique_chgtime.cgi - Change time for a deploy 
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
my $sql;
my $sth;
my $dbh;
my $cname;
my $cstatus;
my $shour;
my $smin;
my $sdate1;
my $uid=$query->param('uid');
#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}
#------  connect to the util database -----------
my ($dbhq,$dbhu)=$util->get_dbh();
$sql="select campaign_name,status,send_date,hour(send_time),minute(send_time) from unique_campaign where unq_id=$uid";
$sth=$dbhu->prepare($sql);
$sth->execute();
($cname,$cstatus,$sdate1,$shour,$smin)=$sth->fetchrow_array();
$sth->finish();

if (($cstatus ne "START") and ($cstatus ne "PRE-PULLING"))
{
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html><head><title>Error</title></head></html>
<body>
<center>
<h3><b>Error:</b> Cant change time of deploy <b>$cname<b> with status <b>$cstatus</b></h3>
<br>
<a href="/cgi-bin/unique_deploy_list.cgi">Back to Deploys</a>
</center>
</body>
</html>
end_of_html
exit();
}

print "Content-Type: text/html\n\n";
print<<"end_of_html";
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
<title>Change Time for Uniques Campaign</title>

<style type="text/css">

body {
	background: url(http://www.affiliateimages.com/temp/bg.jpg) top center repeat-x #99D1F4;
	font-family: "Trebuchet MS", Tahoma, Arial, sans-serif;
	font-size: .9em;
	color: #4d4d4d;
  }

h1 {
	text-align: center;
	font-weight: normal;
	font-size: 1.5em;
  }

h2 {
	text-align: center;
	font-weight: normal;
	font-size: 1em;
  }

.centered {
	text-align: center;
  }

#container {
	width: 70%;
	padding-top: 5%;
	margin: 0 auto;
  }

#form {
	margin: 0 auto;
	width: 100%;
	padding: 1em;
	text-align: left;
  }

#form table {
	width: 100%;
	margin: 0 auto;
	margin-top: .5em;
	margin-bottom: .5em;
  }

#form td {
	padding: .25em;
  }

td.label {
	width: 40%;
	text-align: right;
  }

td.field {
	width: 60%;
  }

input.field, select.field, textarea.field {
	padding: .15em;
	border: 1px solid #999;
	color: #000;
	font-family: Tahoma, Arial, sans-serif;
  }

input.field:hover, select.field:hover, textarea.field:hover {
	background: #F9FFE9;
  }

input.field:focus, select.field:focus, textarea.field:focus {
	background: #F9FFE9;
	border: 1px inset;
  }

div.submit {
	text-align: center;
	padding: 1em 0;
  }

input.submit {
	margin-top: .25em;
	font-size: 2em;
	color: #444;
  }

input.radio {
	border: 0;
  }

.note {
	font-size: .8em;
  }

</style>
</script>
</head>

<body>
<div id="container">

	<h1>Change Time for Uniques Campaign</h1>
	<h2><a href="unique_list.cgi" target=_top>view/edit/deploy uniques campaigns</a> | <a href="unique_deploy_list.cgi" target=_top>deployed unique campaigns</a> | <a href="/cgi-bin/mainmenu.cgi" target=_top>go home</a></h2>

	<div id="form">
	<form name="campform" method=post action="/cgi-bin/unique_chgtime_save.cgi" target=_top>
	<input type=hidden name=uid value=$uid>
	<input type=hidden name=sdate value="$sdate1">
		<table>
		  <tr>
			<td class="label">Uniques Campaign Name:</td>
			<td>$cname</td>
		  </tr>
		  <tr>
			<td class="label">Scheduled For:</td>
											<td>$sdate1&nbsp;&nbsp;
											<select name=stime>
end_of_html
my $i = 1;
my $thour = $shour;
if ($shour > 12)
{
	$thour = $shour - 12;
}
elsif (($shour == 0) or ($shour == 24))
{
	$thour = 12;
}
while ($i < 13)
{
	if ($i == $thour)
	{
		print "<option value=$i selected>$i</option>";
	}
	else
	{
		print "<option value=$i>$i</option>";
	}
	$i++;
}
print<<"end_of_html";
</select>&nbsp;
											<select name=smin>
end_of_html
my $i = 0;
while ($i < 60)
{
	if ($i == $smin)
	{
		print "<option value=$i selected>$i</option>";
	}
	else
	{
		print "<option value=$i>$i</option>";
	}
	$i++;
}
print<<"end_of_html";
</select>&nbsp;
											<select name="am_pm">
end_of_html
if ($shour < 12)
{
	print "<option value=\"AM\" selected>AM</option><option value=\"PM\">PM</option>\n";
}
else
{
	print "<option value=\"AM\">AM</option><option value=\"PM\" selected>PM</option>\n";
}
print<<"end_of_html";
		</select></td></tr>
		</table>

		<div class="submit">
			<input class="submit" type="submit" name="submit" value="Update Time" />
		</div>
	</div>

</div>
</form>
</body>
</html>
end_of_html
