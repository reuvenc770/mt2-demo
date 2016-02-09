#!/usr/bin/perl
#===============================================================================
# Purpose: Resume all hotmail campaigns 
# Name   : unique_resumeall.cgi 
#
#--Change Control---------------------------------------------------------------
# 07/10/09  Jim Sobeck  Creation
#===============================================================================

#-----  include Perl Modules ---------
use strict;
use CGI;
use util;

#------  get some objects to use later ---------
my $util = util->new;
my $query = CGI->new;

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}
my $sql;
my $sth;
my $dbh;
my $old_template_id;
#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}
#------  connect to the util database -----------
my ($dbhq,$dbhu)=$util->get_dbh();
$sql="select mailing_template from unique_campaign where send_date=curdate() and slot_type in ('Hotmail','Chunking') and status='PAUSED' order by rand() limit 1";
$sth=$dbhu->prepare($sql);
$sth->execute();
($old_template_id)=$sth->fetchrow_array();
$sth->finish();

print "Content-Type: text/html\n\n";
print<<"end_of_html";
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
<title>Resume Hotmail/Chunking Unique Campaign</title>

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
</head>

<body>
<div id="container">

	<h1>Resume Hotmail/Chunking Unique Campaigns</h1>
	<h2><a href="unique_list.cgi" target=_top>view/edit/deploy uniques campaigns</a> | <a href="unique_deploy_list.cgi" target=_top>deployed unique campaigns</a> | <a href="unique_deploy_list.cgi?gsm=2">Hotmail deploys</a> | <a href="/cgi-bin/unique_deploy_list.cgi?gsm=3">Chunking</a> | <a href="/cgi-bin/mainmenu.cgi" target=_top>go home</a></h2>

	<div id="form">
	<form name="campform" method=post action="/cgi-bin/unique_resumeall_save.cgi" target=_top>
		<table>
		  <tr>
			<td class="label">Mailing Template:</td>
			<td>
				<select name="template_id">
<option value=0 checked>--SELECT ONE--</option>
end_of_html
$sql="select template_id,template_name from brand_template where status='A' order by template_name";
$sth=$dbhq->prepare($sql);
$sth->execute();
my $template_id;
my $tname;
while (($template_id,$tname)=$sth->fetchrow_array())
{
	if ($old_template_id == $template_id)
	{
		print "<option selected value=$template_id>$tname</option>\n";
	}
	else
	{
		print "<option value=$template_id>$tname</option>\n";
	}
}
$sth->finish();
print<<"end_of_html";
				</select>
			</td>
			</tr>
		  <tr>
			<td class="label">Domain(if left blank, then domain is not changed:</td>
			<td><input type=text name=mdomain size=50 maxlength=50></td></tr>
		</table>

		<div class="submit">
			<input class="submit" type="submit" name="submit" value="resume" />
		</div>
	</div>

</div>
</form>
</body>
</html>
end_of_html
