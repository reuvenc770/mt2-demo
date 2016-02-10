#!/usr/bin/perl

# *****************************************************************************************
# unique_setup.cgi
#
# this page display pages to allow editing of Yahoo Parameters 
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
my $DETAILS;
my $sql;
my $cid;
my $cname;
my $classid;
#------ connect to the util database ------------------
my ($dbhq,$dbhu)=$util->get_dbh();
#

print "Content-Type: text/html\n\n";
print<<"end_of_html";
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html><head><title>Edit Transient Setup Parameters</title>

<style type="text/css">

body {
	background: top center repeat-x #99D1F4; font-family: "Trebuchet MS", Tahoma, Arial, sans-serif;
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
	margin-bottom: .5em;
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
	font-weight: normal;
  }

</style>
</head>

<body>
  <div id="form">
	<form method="post" name="campform" action="unique_setup_save.cgi">
	  <table>
		  <tr>
			<td class="label">TS01 Message Count for Pause:</td>
			<td class="field"><input class="field" size="8" value="10" name=ts01_cnt /> </td>
		  </tr>
		  <tr>
			<td class="label">TS01 Time(minutes):</td>
			<td class="field">
				<input class="field" size="8" value="120" name=tso1_time /> 
			</td>
		  </tr>
		  <tr>
			<td class="label">TS02 Cancel Count:</td>
			<td class="field">
				<input class="field" size="8" value="20" name=ts02_cnt /> 
			</td>
		  </tr>
		  <tr>
			<td class="label">Delivery Message Count:</td>
			<td class="field">
				<input class="field" size="8" value="20" name=delivery_cnt /> 
			</td>
		  </tr>
		</table>

		<div class="submit">
			<input class="submit" value="Save" type="submit" name=submit>
<br>
		</div>
	</form></div>

</body></html>
end_of_html
exit(0);
