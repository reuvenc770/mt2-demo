#!/usr/bin/perl

# *****************************************************************************************
# dd_add.cgi
#
# this page sets up a Daily Deal setting 
#
# History
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $sth1;
my $dbh;
my $cid;
my $sql;
my $mname;
my $ctype=$query->param('ctype');
my $cid=$query->param('cid');

#------ connect to the util database ------------------
my ($dbhq,$dbhu)=$util->get_dbh();
#
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>$ctype Deal Setting Setup</title>
</head>

<body>
<center>
<!-- saved from url=(0022)http://internet.e-mail -->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html><head><title>$ctype Deal Settings</title>

<style type="text/css">

body {
	background: top center repeat-x #99D1F4;
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
  }

#form td {
	padding: .25em;
  }

td.label {
	width: 40%;
	text-align: right;
	font-weight: bold;
  }

td.field {
	width: 60%;
  }

input.field, select.field, textarea.field {
	padding: .15em;
	border: 1px solid #999;
	font-size: .9em;
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
	font-weight: normal;
  }

</style>
</head>

<body>
<div id="container">
  <h1>Add Daily Deal Settings</h1>
	<h2><a href="dd_list.cgi?ctype=$ctype" target=_top>view all $ctype Deal settings</a> | <a href="/client_schedule.html" target=_top>view clients schedule page</a>| <a href="/newcgi-bin/mainmenu.cgi" target=_top>go home</a></h2>

  <div id="form">
	<form method="post" name="campform" action="dd_add_save.cgi">
	<input type=hidden name="ctype" value="$ctype"> 
	<input type=hidden name="cid" value="$cid"> 
	  <table>

		  <tr>
			<td class="label">Setting Name:</td>
			<td class="field"><input class="field" size="35" name="dd_name" value="" /></td>
		  </tr>
<tr>
			<td class="label"></td>
			<td class="field"><input value="Add" type="submit">
			</td>
		  </tr>
	</table>
	</form>
</div>
</div>
</body>
</html>
end_of_html
exit(0);
