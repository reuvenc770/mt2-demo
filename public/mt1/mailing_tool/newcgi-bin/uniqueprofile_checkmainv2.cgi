#!/usr/bin/perl

# *****************************************************************************************
# uniqueprofile_checkmainv2.cgi
#
# this page display pages to allow checking to see how much a profile would mail 
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
my $sql;
my $time=30;
my $cstatus;
my $DETAILS;
my $cid= $query->param('cid');
my $url="http://mailingtool.routename.com:83/cgi-bin/uniqueprofile_checkmainv2.cgi?cid=$cid";
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
	width: 20%;
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
    <form method="post" name="campform" action="uniqueprofile_check_addv2.cgi">
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
	<tr><td class="label">Clients</td><td class=field>Count</td></tr>
end_of_html
my $fname;
my $client;
my $record_cnt;
$sql="select user_id,username,record_cnt from user,UniqueCheckClient ucc where ucc.check_id=$cid and ucc.client_id=user.user_id order by username";
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($client,$fname,$record_cnt)=$sth->fetchrow_array())
{
	print "<tr><td class=label>$fname</td><td class=field>$record_cnt<td></tr>";
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
				$DETAILS->{opener_start} to $DETAILS->{opener_end}</td></tr>
				<tr><td class=label>Calculated:</td><td> $DETAILS->{opener_start1} to $DETAILS->{opener_end1}
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
				$DETAILS->{clicker_start} to $DETAILS->{clicker_end}</td></tr>
				<tr><td class=label>Calculated:</td><td> $DETAILS->{clicker_start1} to $DETAILS->{clicker_end1}
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
				$DETAILS->{deliverable_start} to $DETAILS->{deliverable_end}</td></tr>
				<tr><td class=label>Calculated:</td><td> $DETAILS->{deliverable_start1} to $DETAILS->{deliverable_end1}
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
				<tr><td class=label>Calculated:</td><td> $DETAILS->{convert_start1} to $DETAILS->{convert_end1}
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
			<tr>
			<td class="label">Volume Desired:</td>
			<td class="field">$DETAILS->{volume_desired}</td></tr>
			<tr>
			<td class="label">Volume Calculated:</td>
			<td class="field">$DETAILS->{volume_calculated}</td></tr>
<tr><td class=label>Profile name</td><td class=field><input type=text name=pname size=40></td></tr>
        </table>

        <div class="submit">
            <input class="submit" value="Create profile" type="submit" name=submit>
<br>
        </div>
    </form></div>
	</div>
<center><a href="/cgi-bin/uniqueprofile_check.cgi?inchkID=$cid&export=1">Export Profile</a>
</body></html>
end_of_html
exit(0);
}
