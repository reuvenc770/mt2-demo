#!/usr/bin/perl
# *****************************************************************************************
# partner_list.cgi
#
# this page lists all Partner settings 
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
my $sql;
my $partner_id;
my $partner_name;
my $cstatus;
my $pause_flag;

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
<!-- saved from url=(0022)http://internet.e-mail -->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html><head><title>Partner Info</title>


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
<h1>List of Partner Settings</h1>

<h2><a href="/newcgi-bin/mainmenu.cgi">go home</a></h2>

	<div id="form">
	  <table>
		  <tr>
			<td><strong>Partner</strong></td>
			<td><strong>Actions</strong></td>
			<td><strong>Client(s)</strong></td>
			<td><strong>Delay (Hours)</strong></td>
			<td><strong>Dupes?</strong></td>
		  </tr>
end_of_html
my $delay;
my $dupes;
$sql="select partner_id,partner_name,enable_flag,dupes_only,pause_flag from PartnerInfo order by enable_flag asc, partner_name";
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($partner_id,$partner_name,$cstatus,$dupes,$pause_flag)=$sth->fetchrow_array())
{
	$sql="select delay/3600 from PartnerClientInfo where partner_id=? limit 1"; 
	my $sth1=$dbhu->prepare($sql);
	$sth1->execute($partner_id);
	($delay)=$sth1->fetchrow_array();
	$sth1->finish();
	if ($delay eq "")
	{
		$delay="0.00";
	}

	print "<tr><td><a href=\"/cgi-bin/partner_setup.cgi?partner_id=$partner_id\">$partner_name</a></td>";
	if ($cstatus eq "Y")
	{
		print "<td><a href=\"/cgi-bin/partner_status.cgi?partner_id=$partner_id&tflag=N\">Disable</a>\n";
	}
	else
	{
		print "<td><a href=\"/cgi-bin/partner_status.cgi?partner_id=$partner_id&tflag=Y\">Enable</a>\n";
	}
	if ($pause_flag eq "Y")
	{
		print "&nbsp;&nbsp;<a href=\"/cgi-bin/partner_status.cgi?partner_id=$partner_id&tflag=U\">Unpause</a></td>\n";
	}
	else
	{
		print "&nbsp;&nbsp;<a href=\"/cgi-bin/partner_status.cgi?partner_id=$partner_id&tflag=P\">Pause</a></td>\n";
	}
	my $cid;
	my $cidstr="";
	$sql="select client_id from PartnerClientInfo where partner_id=? order by client_id"; 
	$sth1=$dbhu->prepare($sql);
	$sth1->execute($partner_id);
	while (($cid)=$sth1->fetchrow_array())
	{
		$cidstr=$cidstr.$cid.",";
	}
	$sth1->finish();
	chop($cidstr);

	print "<td>$cidstr</td>";
	print "<td>$delay</td>\n";
	print "<td>$dupes</td>\n";
	print "</tr>\n";
}
$sth->finish();
print<<"end_of_html";
	  </table>

	</div>

</div>
</body></html>
end_of_html
exit(0);
