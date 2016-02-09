#!/usr/bin/perl
# *****************************************************************************************
# mta_list.cgi
#
# this page lists all mta settings 
#
# History
# Jim Sobeck, 03/28/07, Creation
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
my $mta_id;
my $mname;

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


my $userDataRestrictionWhereClause = '';

$util->getUserData({'userID' => $user_id});

if($util->getUserData()->{'isExternalUser'} == 1)
{
	$userDataRestrictionWhereClause = qq|
        userID = $user_id AND
    |;
}


print "Content-Type: text/html\n\n";
print<<"end_of_html";
<!-- saved from url=(0022)http://internet.e-mail -->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html><head><title>MTA Settings</title>



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
	border: 1px solid #aaa;
	width: 100%;
	margin: 0 auto;
	margin-top: .5em;
	margin-bottom: .5em;
  }

#form td {
	padding: .25em;
  }

tr.inactive {
	color: #aaa;
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
<h1>List of MTA Settings Profiles</h1>

<h2><a href="/client_schedule.html">view clients schedule page</a> | <a href="/newcgi-bin/mainmenu.cgi">go home</a></h2>

	<div id="form">
	  <table>
		  <tr>
			<td><strong>MTA Settings Profile Name</strong></td>
			<td><strong>Actions</strong></td>
		  </tr>
end_of_html
$sql="select mta_id,name from mta where $userDataRestrictionWhereClause 1 order by name";
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($mta_id,$mname)=$sth->fetchrow_array())
{
	if ($mta_id == 1)
	{
		print "<tr><td><a href=\"/cgi-bin/mta_setup.cgi?mta_id=$mta_id\">$mname</a></td><td><a href=mta_copy.cgi?mta_id=$mta_id>Copy</a></td></tr>\n";
	}
	else
	{
		print "<tr><td><a href=\"/cgi-bin/mta_setup.cgi?mta_id=$mta_id\">$mname</a></td><td><a href=mta_copy.cgi?mta_id=$mta_id>Copy</a>&nbsp;&nbsp;<a href=\"/cgi-bin/mta_delete.cgi?mta_id=$mta_id\">Delete</a></td></tr>\n";
	}
}
$sth->finish();
print<<"end_of_html";
	  </table>

	</div>

</div>
</body></html>
end_of_html
exit(0);
