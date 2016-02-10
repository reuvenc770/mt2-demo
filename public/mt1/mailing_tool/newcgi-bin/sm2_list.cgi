#!/usr/bin/perl
#===============================================================================
# Name   : sm2_list.cgi - lists all test_campaigns of campaign_type='TEST' 
#
#--Change Control---------------------------------------------------------------
# 07/23/07  Jim Sobeck  Creation
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
my $INJ;

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}

$util->getUserData({'userID' => $user_id});

#------  connect to the util database -----------
my ($dbhq,$dbhu)=$util->get_dbh();
my $type=$query->param('type');
if ($type eq "")
{
	$type="T";
}
        print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>
<head>
<title>Build a StrongMail Test Campaign</title>

<style type="text/css">
end_of_html
if ($type eq "H")
{
print<<"end_of_html";
BODY {
    FONT-SIZE: 0.9em; BACKGROUND: url(http://www.affiliateimages.com/temp/green_bg.jpg) #B9F795 repeat-x center top; COLOR: #4d4d4d; F
ONT-FAMILY: "Trebuchet MS", Tahoma, Arial, sans-serif
}
end_of_html
}
else
{
print<<"end_of_html";
body {
	background: url(http://www.affiliateimages.com/temp/bg.jpg) top center repeat-x #99D1F4;
	font-family: "Trebuchet MS", Tahoma, Arial, sans-serif;
	font-size: .9em;
	color: #4d4d4d;
  }
end_of_html
}
print<<"end_of_html";
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
</style>
<script language=JavaScript>
function preview(tid)
{
    var cpage = "/cgi-bin/sm2_preview.cgi?tid="+tid;
    var newwin = window.open(cpage, "Preview", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,width=900,height=500,left=25,top=50");
}

function selectall()
{
    refno=/active_/;
    for (var x=0; x < document.adform.length; x++)
    {
        if ((document.adform.elements[x].type=="checkbox") && (refno.test(document.adform.elements[x].name)))
        {
            document.adform.elements[x].checked = true;
        }
    }
    refno=/inactive_/;
    for (var x=0; x < document.adform.length; x++)
    {
        if ((document.adform.elements[x].type=="checkbox") && (refno.test(document.adform.elements[x].name)))
        {
            document.adform.elements[x].checked = true;
        }
    }
}
function unselectall()
{
    refno=/active_/;
    for (var x=0; x < document.adform.length; x++)
    {
        if ((document.adform.elements[x].type=="checkbox") && (refno.test(document.adform.elements[x].name)))
        {
            document.adform.elements[x].checked = false;
        }
    }
    refno=/inactive_/;
    for (var x=0; x < document.adform.length; x++)
    {
        if ((document.adform.elements[x].type=="checkbox") && (refno.test(document.adform.elements[x].name)))
        {
            document.adform.elements[x].checked = false;
        }
    }
}
function selectactive()
{
    refno=/active_/;
    for (var x=0; x < document.adform.length; x++)
    {
        if ((document.adform.elements[x].type=="checkbox") && (refno.test(document.adform.elements[x].name)))
        {
            document.adform.elements[x].checked = true;
        }
    }
}
function selectinactive()
{
    refno=/inactive_/;
    for (var x=0; x < document.adform.length; x++)
    {
        if ((document.adform.elements[x].type=="checkbox") && (refno.test(document.adform.elements[x].name)))
        {
            document.adform.elements[x].checked = true;
        }
    }
}
</script>
</head>

<body>
<center>
<div id="container">
end_of_html
if ($type eq "T")
{
	print "<h1>List of StrongMail Test Campaigns</h1>\n";
	print "<h2><a href=\"/sm2_build_test.html\">build a test campaign</a> | <a href=\"sm2_list.cgi?type=D\">see all deployed campaigns</a> | <a href=\"sm2_list.cgi?type=F\">see all freestyle campaigns</a> | <a href=\"/sm2_send_all.html\">Send All Test</a> | <a href=\"/sm2_send_all_free.html\">Send All FreeStyle Test</a> | <a href=\"sm2_seedlist.cgi\"> SM2 Seeds</a> | <a href=\"mainmenu.cgi\">go home</a></h2>\n";
}
elsif ($type eq "F")
{
	print "<h1>List of FreeStyle Campaigns</h1>\n";
	print "<h2><a href=\"/sm2_build_free.html\">build a freestyle campaign</a> | <a href=\"sm2_list.cgi?type=D\">see all deployed campaigns</a> | <a href=\"sm2_list.cgi?type=T\">see all test campaigns</a> | <a href=\"/sm2_send_all.html\">Send All Test</a> | <a href=\"/sm2_send_all_free.html\">Send All FreeStyle Test</a>  | <a href=\"sm2_seedlist.cgi\"> SM2 Seeds</a> | <a href=\"mainmenu.cgi\">go home</a></h2>\n";
}
elsif ($type eq "D")
{
	print "<h1>List of StrongMail Deployed Campaigns</h1>\n";
	print "<h2><a href=\"/sm2_build_test.html\">build a test campaign</a> | <a href=\"sm2_list.cgi\">see all test campaigns</a> | <a href=\"sm2_list.cgi?type=F\">see all freestyle campaigns</a> | <a href=\"/sm2_send_all.html\">Send All Test</a> | <a href=\"/sm2_send_all_free.html\">Send All FreeStyle Test</a> | <a href=\"sm2_seedlist.cgi\"> SM2 Seeds</a> | <a href=\"mainmenu.cgi\">go home</a></h2>\n";
}
elsif ($type eq "H")
{
	exit();
	print "<h1>List of History Building Campaigns</h1>\n";
	print "<h2><a href=\"/sm2_build_history.html\">build a history campaign</a> | <a href=\"sm2_history_schedule.cgi\" target=_top>view the scheduler</a> | <a href=\"sm2_list.cgi\">see all test campaigns</a> | <a href=\"sm2_list.cgi?type=D\">see all deployed campaigns</a> | <a href=\"sm2_list.cgi?type=F\">see all freestyle campaigns</a> | <a href=\"sm2_seedlist.cgi\"> SM2 Seeds</a> | <a href=\"mainmenu.cgi\">go home</a></h2>\n";
	print "<form method=post name=adform action=sm2_function.cgi>\n";
	print "<h4><strong>select:</strong> <a href=\"javascript:selectall()\">all</a>, <a href=\"javascript:unselectall()\">none</a>, <a href=\"javascript:selectactive()\">all active</a>, <a href=\"javascript:selectinactive()\">all inactive</a> | <input value=\"activate selected\" name=submit type=\"submit\" /> <input value=\"deactivate selected\" name=submit type=\"submit\" /> </h4>\n";
}

print<<"end_of_html";
	<div id="form">
		<table style="border: 1px solid #999; ">
		  <tr>
end_of_html
if ($type eq "T")
{
		  	print "<td width=\"10%\"><strong>Date Sent</strong></td>\n";
			print "<td width=\"10%\"><strong>Test ID</strong></td>\n";
}
elsif ($type eq "F")
{
		  	print "<td width=\"10%\"><strong>Date Sent</strong></td>\n";
			print "<td width=\"10%\"><strong>Test ID</strong></td>\n";
}
elsif ($type eq "H")
{
			print "<td width=\"10%\"><strong>ID</strong></td>\n";
}
elsif ($type eq "D")
{
		  	print "<td width=\"10%\"><strong>Date Sent</strong></td>\n";
			print "<td width=\"10%\"><strong>Campaign ID</strong></td>\n";
}
if ($type eq "H")
{
print<<"end_of_html";
			<td><strong>Last Sent</strong></td>
			<td><strong>Campaign Name</strong></td>
			<td><strong>Scheduling Status</strong></td>
			<td><strong>Actions</strong></td>
		  </tr>
end_of_html
}
else
{
print<<"end_of_html";
			<td width="30%"><strong>Campaign Name</strong></td>
			<td><strong>Last Injector Used</strong></td>
			<td><strong>Completion Status</strong></td>
			<td width="20%"><strong>Actions</strong></td>
		  </tr>
end_of_html
}

my $userDataRestrictionWhereClause = '';

if($util->getUserData()->{'isExternalUser'} == 1)
{
	$userDataRestrictionWhereClause = qq|
        userID = $user_id AND
    |;
}
$sql="SELECT s.serverID,coalesce(privateHostname, hostname)
    FROM Server s
    JOIN ServerTypeJoin stj on s.serverID = stj.serverID
    JOIN ServerType st on stj.serverTypeID = st.serverTypeID
    WHERE serverTypeLabel = 'injector' and active = 1 order by privateHostname";
$sth=$dbhq->prepare($sql);
$sth->execute();
my $sid;
my $sname;
while (($sid,$sname)=$sth->fetchrow_array())
{
	$INJ->{$sid}=$sname;
}
$sth->finish();


if ($type eq "T")
{
	$sql="select test_id,date_format(send_date,'%m/%d/%Y'),campaign_name,campaign_id,history_status, continuous_flag,UsedInjectorID,completion_status from test_campaign where $userDataRestrictionWhereClause campaign_type='TEST' and campaign_name not like 'ALL Test%' and (send_date >= date_sub(curdate(),interval 7 day) or (send_date is null)) order by send_date";
}
elsif ($type eq "F")
{
	$sql="select test_id,date_format(send_date,'%m/%d/%Y'),campaign_name,campaign_id,history_status, continuous_flag,UsedInjectorID,completion_status from test_campaign where $userDataRestrictionWhereClause campaign_type='FREESTYLE' and campaign_name not like 'ALL Test%' and (send_date >= date_sub(curdate(),interval 7 day) or (send_date is null)) order by send_date";
}
elsif ($type eq "D")
{
	$sql="select test_id,date_format(send_date,'%m/%d/%Y'),campaign_name,campaign_id,history_status, continuous_flag,UsedInjectorID,completion_status from test_campaign where $userDataRestrictionWhereClause campaign_type='DEPLOYED' order by send_date";
}
elsif ($type eq "H")
{
	$sql="select test_id,date_format(send_date,'%m/%d/%Y'),campaign_name,campaign_id,history_status, continuous_flag,UsedInjectorID,completion_status from test_campaign where $userDataRestrictionWhereClause campaign_type='HISTORY' order by send_date";
}
$sth=$dbhq->prepare($sql);
$sth->execute();
my $test_id;
my $cdate;
my $cname;
my $cid;
my $hstatus;
my $continuous_flag;
my $UsedInjectorID;
my $completion_status;
while (($test_id,$cdate,$cname,$cid,$hstatus, $continuous_flag,$UsedInjectorID,$completion_status)=$sth->fetchrow_array())
{
if ($type eq "H")
{
if ($hstatus eq "Active")
{
		  print "<tr>";
}
else
{
		  print "<tr class=inactive>";
}
print<<"end_of_html";
			<td>$test_id</td>
		  	<td width="10%">$cdate</td>
			<td>$cname</td>
end_of_html
if ($hstatus eq "Active")
{
	print "<td><input type=checkbox name=active_box value=$test_id>Active</td>\n";
}
else
{
	print "<td><input type=checkbox name=inactive_box value=$test_id>Inactive</td>\n";
}
print<<"end_of_html";
			<td>
				<a href="sm2_function.cgi?tid=$test_id&submit=edit">edit</a>
				<a href="sm2_function.cgi?tid=$test_id&submit=delete">delete</a>
				<a href="sm2_function.cgi?tid=$test_id&submit=cancel">cancel</a>
end_of_html
print<<"end_of_html";
			</td>
		  </tr>
end_of_html
}
else
{
	
	#set warning row if the deploy is on all the time
	my $color = '#99D1F4';
	if($continuous_flag eq 'Y'){
		$color = 'red';
	}
	
print<<"end_of_html";
<form method=post action="sm2_function.cgi">
<input type=hidden name=tid value=$test_id>
		  <tr bgcolor="$color">
		  	<td width="10%">$cdate</td>
end_of_html

if ($type eq 'D')
{
	print "<td>$cid</td>\n";
}
else
{
	print "<td>$test_id</td>\n";
}
print<<"end_of_html";
			<td>$cname</td>
			<td>$INJ->{$UsedInjectorID}</td>
			<td>$completion_status</td>
			<td>
				<input type="button" value="preview" onClick="preview($test_id);">
				<input type="submit" name="submit" value="edit" />
end_of_html
if ($type eq "T")
{
				print "<input type=submit name=submit value=delete />\n";
				print "<input type=submit name=submit value=cancel />\n";
}	
elsif ($type eq "F")
{
				print "<input type=submit name=submit value=delete />\n";
				print "<input type=submit name=submit value=cancel />\n";
}	
print<<"end_of_html";
			</td>
		  </tr>
</form>
end_of_html
}
}
$sth->finish();
if ($type eq "H")
{
	print "</form>";
}
print<<"end_of_html";
		</table>

	</div>

</div>
</center>
</body>
</html>
end_of_html
