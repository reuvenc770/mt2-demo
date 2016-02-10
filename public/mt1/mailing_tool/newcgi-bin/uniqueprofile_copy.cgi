#!/usr/bin/perl

# *****************************************************************************************
# uniqueprofile_copy.cgi
#
# this page copies a Unique profile 
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
my $pid= $query->param('pid');
my $cid;
my $sql;
my $pname;

#------ connect to the util database ------------------
my ($dbhq,$dbhu)=$util->get_dbh();
$sql="select profile_name from UniqueProfile where profile_id=?";
$sth=$dbhq->prepare($sql);
$sth->execute($pid);
($pname)=$sth->fetchrow_array();
$sth->finish();
#
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Unique Profile Copy </title>
</head>

<body>
<center>
<!-- saved from url=(0022)http://internet.e-mail -->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html><head><title>Copy Unique Profile</title>

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
  <h1>Copy Unique Profile</h1>
	<h2><a href="/newcgi-bin/mainmenu.cgi" target=_top>go home</a></h2>

  <div id="form">
	<form method="post" name="campform" action="uniqueprofile_copy_save.cgi">
	<input type=hidden name="pid" value="$pid"> 
	  <table>

		  <tr>
			<td class="label">Copy From:</td>
			<td class="field">$pname</td>
		  </tr>
		  <tr>
			<td class="label">To:</td>
			<td class="field"><input class="field" size="35" name="pname" value="" /></td>
		  </tr>
<tr>
			<td class="label"></td>
			<td class="field"><input value="Copy" type="submit">
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
