#!/usr/bin/perl

# *****************************************************************************************
# complaint_setup_bot.cgi
#
# this page display bottom frame for setting up complaint/deliverables 
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
my $classid = $query->param('classid');
my $cid;
my $cname;
my $isp_name;
#------ connect to the util database ------------------
my ($dbhq,$dbhu)=$util->get_dbh();
#
my $sql="select class_name from email_class where class_id=? and status='Active'";
$sth=$dbhq->prepare($sql);
$sth->execute($classid);
($isp_name)=$sth->fetchrow_array();
$sth->finish();
$sql="select id,first_name,start,end from DeliverableAdd da, user u where da.client_id=u.user_id and da.class_id=? order by id";
$sth=$dbhq->prepare($sql);
$sth->execute($classid);
my $DETAILS=$sth->fetchrow_hashref();
$sth->finish();

print "Content-Type: text/html\n\n";
print<<"end_of_html";
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html><head><title>Edit Complaint/Deliverable Settings</title>

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
<script language="JavaScript">
function chkform()
{
    if (campform.start.value == '')
    {
        alert('You must enter a start value');
        campform.start.focus();
        return false;
    }
    if (campform.end.value == '')
    {
        alert('You must enter a end value');
        campform.end.focus();
        return false;
    }
	if (campform.start.value > campform.end.value)
	{
        alert('Start value must be less than or equal to End value');
        campform.start.focus();
        return false;
    }
	return true;
}
</script>
</head>

<body>
<center><b>$isp_name</b></center>
	<center>
	<form method="post" name="campform" action="complaint_save_settings.cgi" onSubmit="return chkform();">
	<input type=hidden name=classid value=$classid>
	  <table width=50% cellpadding=0>
<tr><td>Client: <select name=clientid>
end_of_html
$sql="select user_id,first_name from user where status='A' order by first_name";
$sth=$dbhu->prepare($sql);
$sth->execute();
my $cid;
my $cname;
while (($cid,$cname)=$sth->fetchrow_array())
{
	print "<option value=$cid>$cname</option>\n";
}
$sth->finish();
print<<"end_of_html";
</select></td><td>Start: <input type=text id=start name=start size=5></td><td>End: <input type=text name=end id=end size=5></td><td><input type=submit value="Add"></td></tr>	
		</table>
	</form>
<br><br>
<table width=50% border=1>
<tr><th>Client</th><th>Start</th><th>End</th><th></th></tr>
end_of_html
$sql="select da.id,first_name,start,end from DeliverableAdd da, user u where class_id=? and da.client_id=u.user_id order by id";
$sth=$dbhu->prepare($sql);
$sth->execute($classid);
my $fname;
my $start;
my $end;
my $id;
while (($id,$fname,$start,$end)=$sth->fetchrow_array())
{
	print "<tr><td>$fname</td><td>$start</td><td>$end</td><td><a href=complaint_delete.cgi?id=$id&classid=$classid>Delete</a></td></tr>\n";
}
$sth->finish();
print<<"end_of_html";
</body></html>
end_of_html
exit(0);
