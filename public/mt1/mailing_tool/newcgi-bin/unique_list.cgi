#!/usr/bin/perl
#===============================================================================
# Name   : unique_list.cgi - lists all unique_campaigns 
#
#--Change Control---------------------------------------------------------------
# 05/19/08  Jim Sobeck  Creation
#===============================================================================

#-----  include Perl Modules ---------
use strict;
use CGI;
use util;

#------  get some objects to use later ---------
my $util = util->new;
my $query = CGI->new;
my $sql;
my $sth;
my $dbh;
my $uid;
my $cid;
my $cname;
my $sdate;

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}



my $userDataRestrictionWhereClause = '';

if($util->getUserData()->{'isExternalUser'} == 1)
{
	$userDataRestrictionWhereClause = qq|
        userID = $user_id AND
    |;
}

#------  connect to the util database -----------
my ($dbhq,$dbhu)=$util->get_dbh();
my $type=$query->param('type');
if ($type eq "")
{
	$type="T";
}
        print "Content-Type: text/html\n\n";
print<<"end_of_html";
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
<title>List of Uniques Campaigns</title>

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

<script language="JavaScript">
function edit_camp(uid)
{
	document.location.href="/cgi-bin/unique_main.cgi?uid="+uid;
}	
function deploy_camp(uid)
{
	document.location.href="/cgi-bin/unique_deploy.cgi?uid="+uid;
}	
function preview_camp(uid)
{
    var newwin = window.open("/cgi-bin/unique_preview.cgi?uid="+uid, "Preview","toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,width=900,height=500,left=25,top=50");
    newwin.focus();
}	
</script>
</head>

<body>
<div id="container">

	<h1>List of Saved Uniques Campaigns</h1>
	<h2><a href="/newcgi-bin/unique_main.cgi?uid=0">build a new uniques campaign</a> | <a href="/newcgi-bin/unique_deploy_list.cgi">deployed unique campaigns</a> | <a href="/newcgi-bin/unique_deploy_list.cgi?gsm=1">GSM/DD/CHA deploys</a> | <a href="/newcgi-bin/unique_deploy_list.cgi?gsm=2">Hotmail</a> | <a href="/newcgi-bin/unique_deploy_list.cgi?gsm=3">Chunking</a> | <a href="/newcgi-bin/unique_deploy_list.cgi?gsm=4">Time Based</a> | <a href="/newcgi-bin/upload_unique.cgi">Upload Uniques</a> | <a href="/newcgi-bin/upload_unique.cgi?utype=test">Test Upload Uniques</a> | <a href="/newcgi-bin/mainmenu.cgi">go home</a></h2>

	<div id="form">
		<table style="border: 1px solid #999; ">
		  <tr>
		  	<td width="10%"><strong>Date Sent</strong></td>
			<td width="10%"><strong>Campaign ID</strong></td>
			<td width="50%"><strong>Campaign Name</strong></td>
			<td width="30%"><strong>Actions</strong></td>
		  </tr>
end_of_html
$sql="select unq_id,campaign_name,campaign_id,send_date from unique_campaign where $userDataRestrictionWhereClause campaign_type='TEST' and send_date >= date_sub(curdate(),interval 30 day) order by unq_id";
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($uid,$cname,$cid,$sdate)=$sth->fetchrow_array())
{
	print "<tr><td width=\"10%\">$sdate</td><td>$uid</td><td>$cname</td><td><input type=button value=preview onClick=\"preview_camp($uid);\"/><input type=button value=edit onClick=\"edit_camp($uid);\"/><input type=button value=deploy onClick=\"deploy_camp($uid);\"/></td></tr>\n";
}
$sth->finish();
print<<"end_of_html";
		</table>

	</div>

</div>
</body>
</html>
end_of_html
