#!/usr/bin/perl

# *****************************************************************************************
# uniqueprofile_checkmain.cgi
#
# this page display pages to allow checking to see how much a profile would mail 
#
# History
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use Net::FTP;
use util;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $sth1;
my $dbh;
my $sql;
my $time=30;
my $cstatus;
my $DETAILS;
my $cid= $query->param('cid');
my $url="http://mailingtool.routename.com:83/cgi-bin/uniqueprofile_checkmain.cgi?cid=$cid";
#------ connect to the util database ------------------
my ($dbhq,$dbhu)=$util->get_dbh();
#
$sql="select * from UniqueCheck where check_id=?";
$sth=$dbhu->prepare($sql);
$sth->execute($cid);
$DETAILS=$sth->fetchrow_hashref();
$sth->finish();

if (($DETAILS->{status} eq "A") or ($DETAILS->{status} eq "P"))
{
print "Content-Type: text/html\n\n";
print << "end_of_html";
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<meta http-equiv="refresh" content="$time;URL=$url">
<title>Mailing System EMail System</title>
</head>
<body><center><h4>Calculating...<h3>
</body>
</html>
end_of_html
exit
}
else
{
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html><head><title>Check Deploy Mailing Count</title>

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
<center><a href="/cgi-bin/mainmenu.cgi"><img src="/images/home_blkline.gif" border=0></a></center>
<br>
  <div id="form">
	<form method="post" name="campform" action="uniqueprofile_check_add.cgi">
	<input type=hidden name=cid value=$cid>
	  <table>
end_of_html
if ($DETAILS->{client_group_id} > 0)
{
	my $groupname;
	$sql="select group_name from ClientGroup where client_group_id=$DETAILS->{client_group_id}"; 
	$sth=$dbhu->prepare($sql);
	$sth->execute();
	($groupname)=$sth->fetchrow_array();
	$sth->finish();
	print "<tr><td class=label>Client Group</td><td class=field>$groupname</td></tr>\n";
}
print<<"end_of_html";
	<tr><td class="label">Clients</td>
	<td class="field">
end_of_html
my $fname;
my $client;
$sql="select user_id,username from user,UniqueCheckClient ucc where ucc.check_id=$cid and ucc.client_id=user.user_id order by username";
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($client,$fname)=$sth->fetchrow_array())
{
	print "$fname<br>";
}
$sth->finish();
print<<"end_of_html";
</td></tr>
		  <tr>
			<td class="label">Openers Date Range(yyyy-mm-dd):</td>
			<td class="field">
				$DETAILS->{opener_start_date} to $DETAILS->{opener_end_date}
			</td>
		  </tr>
		  <tr>
			<td class="label">Openers Range:</td>
			<td class="field">
				$DETAILS->{opener_start} to $DETAILS->{opener_end}
			</td>
		  </tr>
		  <tr>
			<td class="label">Clickers Date Range(yyyy-mm-dd):</td>
			<td class="field">
				$DETAILS->{clicker_start_date} to $DETAILS->{clicker_end_date}
			</td>
		  </tr>
		  <tr>
			<td class="label">Clickers Range:</td>
			<td class="field">
				$DETAILS->{clicker_start} to $DETAILS->{clicker_end}
			</td>
		  </tr>
		  <tr>
			<td class="label">Deliverable Date Range(yyyy-mm-dd):</td>
			<td class="field">
				$DETAILS->{deliverable_start_date} to $DETAILS->{deliverable_end_date}
			</td>
		  </tr>
		  <tr>
			<td class="label">Deliverable Range:</td>
			<td class="field">
				$DETAILS->{deliverable_start} to $DETAILS->{deliverable_end}
			</td>
		  </tr>
		  <tr>
			<td class="label">Convert Date Range(yyyy-mm-dd):</td>
			<td class="field">
				$DETAILS->{convert_start_date} to $DETAILS->{convert_end_date}
			</td>
		  </tr>
		  <tr>
			<td class="label">Convert Range:</td>
			<td class="field">
				$DETAILS->{convert_start} to $DETAILS->{convert_end}
			</td>
		  </tr>
		  <tr>
			<td class="label">Deliverable Factor:</td>
			<td class="field">
				$DETAILS->{deliverable_factor}
			</td></tr>
			<tr>
			<td class="label">Send International:</td>
			<td class="field">$DETAILS->{send_international}</td></tr>
end_of_html
if ($DETAILS->{client_id} > 0)
{
	print "<tr><td class=label>Custom Data:</td></tr>\n";
	$sql="select clientRecordKeyName,clientRecordValueName from UniqueCheckCustom ucc join ClientRecordKeys crk on ucc.clientRecordKeyID=crk.clientRecordKeyID join ClientRecordValues crv on ucc.clientRecordValueID=crv.clientRecordValueID where check_id=$cid";
	$sth=$dbhu->prepare($sql);
	$sth->execute();
	my $key;
	my $val;
	my $oldkey="";
	while (($key,$val)=$sth->fetchrow_array())
	{
		if ($key ne $oldkey)
		{
			if ($oldkey ne "")
			{
				print "</td></tr>";
			}
			$oldkey=$key;
			print "<tr><td></td><td><b>$key: </b>";
		}
		print "$val ";
	}
	$sth->finish();
	print "</td></tr>\n";
}
$sql="select u.username,ed.class_id,class_name,reccnt from user u,email_class ed, UniqueCheckIsp where ed.class_id=UniqueCheckIsp.class_id and UniqueCheckIsp.check_id=$cid and UniqueCheckIsp.client_id=u.user_id and ed.status='Active' order by class_name,u.username";
$sth=$dbhu->prepare($sql);
$sth->execute();
my $class_id;
my $cname;
my $reccnt;
my $fname;
my $oldclass="";
my $total=0;
my $tcnt=0;
while (($fname,$class_id,$cname,$reccnt)=$sth->fetchrow_array())
{
	if ($cname ne $oldclass)
	{
		if ($oldclass ne "")
		{
			print "<tr><td class=label></td><td class=field><b>TOTAL</b>- $total</td></tr>\n";
		}
		$total=0;	
		print "<tr><td class=label>$cname</td><td class=field>&nbsp;&nbsp;<input type=checkbox name=\"isp_${class_id}\" value=Y></td></tr>\n";
		$oldclass=$cname;
	}
	print "<tr><td class=label></td><td class=field>$fname - $reccnt</td></tr>\n";
	$total=$total+$reccnt;
	$tcnt=$tcnt+$reccnt;
}
$sth->finish();
print "<tr><td class=label></td><td class=field><b>TOTAL</b>- $total</td></tr>\n";
print "<tr><td class=label><b>GRAND TOTAL</b>- $tcnt</td></tr>\n";
print<<"end_of_html";
<tr><td class=label>Profile name</td><td class=field><input type=text name=pname size=40></td></tr>
		</table>

		<div class="submit">
			<input class="submit" value="Create profile" type="submit" name=submit>
<br>
		</div>
	</form></div>
<br>
<center><a href=uniqueprofile_export.cgi?cid=$cid target=_new>Export Results</a></center>
end_of_html
if (($DETAILS->{type} eq "Export") or ($DETAILS->{type} eq "Export Suppression"))
{
print<<"end_of_html";
<br>
<center><b>Files placed on Ftp</b><br>
end_of_html
my $host = "ftp.aspiremail.com";
my $ftp = Net::FTP->new("$host", Timeout => 20, Debug => 0, Passive => 0) or print "Cannot connect to $host: $@\n";
if ($ftp)
{
    $ftp->login('espdata','ch3frexA') or print "Cannot login ", $ftp->message;
    foreach my $file($ftp->ls)
    {
		$_=$file;
		if ((/^${cid}_/) and (/.csv$/))
		{
			print "$file<br>";
		} 
	}
    $ftp->quit;
}
}
print<<"end_of_html";
</body></html>
end_of_html
exit(0);
}
