#!/usr/bin/perl
# *****************************************************************************************
# dbloptin_list.cgi
#
# this page lists all Double Option campaigns 
#
# History
# Jim Sobeck, 03/31/08, Creation
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $sql;
my $uid;
my $fname;
my $company;
my $old_id=$query->param('cid');

#------- check for login ------------------
my $user_id = util::check_security();
if ($user_id == 0)
{
        print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}
my ($dbhq,$dbhu)=$util->get_dbh();
#
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html><head><title>Double Opt-in Confirmation Campaigns</title>



<style type="text/css">

body {
	background: top center repeat-x #99D1F4;
	font-family: "Trebuchet MS", Tahoma, Arial, sans-serif;
	font-size: .9em;
	color: #4d4d4d;
	text-align: center;
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

h4 {
	font-weight: normal;
	font-size: .8em;
	padding-top: 1em;
	margin: 0;
	text-align: center;
  }

h4 input {
	font-size: .8em;
  }

#container {
	width: 80%;
	padding-top: 5%;
	margin: 0 auto;
  }

#form {
	margin: 0 auto;
	width: 100%;
	text-align: left;
  }

#form table {
	background: #fff;
	border: 1px solid #aaa;
	width: 100%;
	margin: 0 auto;
	margin-top: .5em;
	margin-bottom: .5em;
	font-size: .85em;
  }

#form td {
	padding: .25em;
  }

tr.alt {
	background: #eee;
  }

tr.label {
	background: #ccc;
	font-size: 1.2em;
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

.submit {
	text-align: center;
	margin-bottom: .3em;
  }

input.submit {
	margin-top: 1em;
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
<h1>List of Double Opt-in Confirmation Campaigns</h1>

<h2><a href="dbloptin.cgi?id=0">build a new double opt-in campaign</a> | <a href="/newcgi-bin/mainmenu.cgi">go home</a></h2>

<h2>
	<div class="filter">
<form method=post action=dbloptin_list.cgi>
		<select name=cid>
end_of_html
if ($old_id == 0)
{
	print "<option selected value=0>ALL</option>\n";
}
else
{
	print "<option value=0>ALL</option>\n";
}
$sql="select user_id,first_name,company from user where status='A' and double_optin='Y' order by company";
$sth=$dbhq->prepare($sql);
$sth->execute();
while (($uid,$fname,$company)=$sth->fetchrow_array())
{
	if ($uid == $old_id)
	{		
		print "<option selected value=$uid>$company ($fname)</option>\n";
	}		
	else
	{
		print "<option value=$uid>$company ($fname)</option>\n";
	}
}
$sth->finish();
print<<"end_of_html";
		</select>
		<input type="submit" value="show selected client" />
</form>
</h2>

	<div id="form">
	  <table cellspacing="0" cellpadding="0">
		  <tr class="label">
			<td><strong>Double Opt-in Campaign Name</strong></td>
			<td><strong>Client</strong></td>
			<td><strong>Confirmation Day #</strong></td>
			<td><strong>Actions</strong></td>
		  </tr>
end_of_html
if ($old_id == 0)
{
	$sql="select id,campaign_name,company,cday from double_optin,user where double_optin.client_id=user.user_id and user.status='A' order by company,cday"; 
}
else
{
	$sql="select id,campaign_name,company,cday from double_optin,user where double_optin.client_id=user.user_id and user.user_id=$old_id order by company,cday"; 
}
$sth=$dbhu->prepare($sql);
$sth->execute();
my $id;
my $cname;
my $cday;
my $i=0;
while (($id,$cname,$company,$cday)=$sth->fetchrow_array())
{
	if ($i % 2)
	{
		print "<tr>";
	}
	else
	{
		print "<tr class=alt>";
	}
	print "<td><a href=\"dbloptin.cgi?id=$id\">$cname</a></td><td>$company</td><td>$cday</td><td><a href=\"dbloptin_copy.cgi?id=$id\">Copy</a><br /><a href=\"dbloptin_del.cgi?id=$id\">Delete</a></td></tr>\n";
	$i++;
}
$sth->finish();
print<<"end_of_html";
	  </table>

	</div>

</div>

</body></html>
end_of_html
exit(0);
