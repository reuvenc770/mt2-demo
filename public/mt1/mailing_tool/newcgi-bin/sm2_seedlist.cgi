#!/usr/bin/perl
#===============================================================================
# Name   : sm2_seedlist.cgi - lists all seedlists for each mta 
#
#--Change Control---------------------------------------------------------------
#===============================================================================

#-----  include Perl Modules ---------
use strict;
use CGI;
use util;
use Lib::Database::Perl::Interface::Server;

#------  get some objects to use later ---------
my $util = util->new;
my $query = CGI->new;
my $sql;
my $sth;
my $dbh;
my $id;
my $server;
my $em;
my $oldgid=$query->param('seedgroup');
if ($oldgid eq "")
{
	$oldgid=1;
}

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}
#------  connect to the util database -----------
my ($dbhq,$dbhu)=$util->get_dbh();
my $serverInterface     = Lib::Database::Perl::Interface::Server->new();
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
<title>Setup Seeds for PAIR UP ALL Send All</title>
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
	<h1>Setup Seed Addresses</h1>
	<h2><a href="sm2_send_all_list.cgi">View Send All Tests</a> | <a href="sm2_send_all_list.cgi?everyday=1">View Send Everyday</a> | <a href="sm2_list.cgi" target=_top>view/deploy tests</a> | <a href="sm2_list.cgi?type=D" target=_top>view deployed campaigns</a> | <a href="mainmenu.cgi" target=_top>go home</a></h2>
	<div id="form">
	<form method=post action="sm2_seedlist.cgi" target=_top>
	Seed Group Name: <select name=seedgroup>
end_of_html
my $gid;
my $gname;
$sql="select SeedGroupID,SeedGroupName from SM2SeedsGroup order by SeedGroupName";
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($gid,$gname)=$sth->fetchrow_array())
{
	if ($gid == $oldgid)
	{
		print "<option selected value=$gid>$gname</option>\n";
	}
	else
	{
		print "<option value=$gid>$gname</option>\n";
	}
}
$sth->finish();
print<<"end_of_html";
	</select>&nbsp;&nbsp;<input type=submit value="Go">
	</form>
	</div>
	<div id="form">
	<form method=post name="campform" id="campform" action="sm2_seedlist_save.cgi" target=_top>
	<input type=hidden name=seedgroup value=$oldgid>
		<table><tr><th>Server</th><th>Seeds</th></tr>
end_of_html
my $errors;
my $results;
my $params;
$params->{active}=1;
($errors, $results) = $serverInterface->getMtaServers($params);
for my $tserver (@$results)
{
	$id=$tserver->{'serverID'};
	$server=$tserver->{'hostname'};
	$sql="select email_addr from SM2Seeds where server_id=? and SeedGroupID=?";
	my $sth1=$dbhu->prepare($sql);
	$sth1->execute($id,$oldgid);
	($em)=$sth1->fetchrow_array();
	$sth1->finish();
	print "<tr><td>$server</td><td><input type=text name=seed_$id size=60 value=\"$em\"></td></tr>\n";
}
$sth->finish();
print<<"end_of_html";
		<tr><td>&nbsp;</td></tr>
		<tr><td colspan=2>New Group Name(<b>Leave Blank to update group)</b>: <input type=text size=30 maxlength=30 name="seed_group_name"></td></tr>
		</table>

		<div class="submit">
			<input class="submit" type="submit" name="submit" value="Save" />
		</div>
	</div>
</div>
</form>
</body>
</html>
end_of_html
