#!/usr/bin/perl

# *****************************************************************************************
# mta_copy.cgi
#
# this page copies an mta setting 
#
# History
# Jim Sobeck, 03/28/08, Creation
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
my $mta_id = $query->param('mta_id');
my $cid;
my $sql;
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


#------ connect to the util database ------------------
$sql="select name from mta where mta_id=?";
$sth=$dbhq->prepare($sql);
$sth->execute($mta_id);
($mname)=$sth->fetchrow_array();
$sth->finish();
#



print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>MTA Copy </title>
</head>

<body>
<center>
<!-- saved from url=(0022)http://internet.e-mail -->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html><head><title>Copy MTA Settings</title>

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
  <h1>Edit MTA Settings</h1>
	<h2><a href="mta_list.cgi" target=_top>view all MTA settings profiles</a> | <a href="/client_schedule.html" target=_top>view clients schedule page</a>| <a href="/newcgi-bin/mainmenu.cgi" target=_top>go home</a></h2>

  <div id="form">
	<form method="get" name="campform" action="mta_copy_save.cgi">
	<input type=hidden name="mta_id" value="$mta_id"> 
	  <table>

		  <tr>
			<td class="label">Copy From:</td>
			<td class="field">$mname</td>
		  </tr>
		  <tr>
			<td class="label">To:</td>
			<td class="field"><input class="field" size="35" maxlength="35" name="mta_name" value="" /></td>
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
