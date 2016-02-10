#!/usr/bin/perl
# *****************************************************************************************
# partner_setup.cgi
#
# this page is to edit a PartnerClientInfo 
#
# History
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $pms = util->new;
my $query = CGI->new;
my $sth;
my $sql;
my $dbh;
my $errmsg;
my $client;
my $delay;
my $dupes;
my $id= $query->param('id');
my $user_id;
my $gname;
my $images = $pms->get_images_url;

# connect to the pms database
my ($dbhq,$dbhu)=$pms->get_dbh();

# check for login
$user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $pms->clean_up();
    exit(0);
}

# read info for this ClientGroup 
$gname="Workflow ".$id;
# print out the html page
    print "Content-Type: text/html;charset-utf-8\n\n";
print << "end_of_html";
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>$gname- Email Tool</title>
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
<body link="#000000" vlink="#000000" alink="#000000">
<TABLE cellSpacing=0 cellPadding=0 border=0 align='center' bgcolor='#FFFFFF'>
<TR>
<TD vAlign=top align=left bgColor=#999999>

<script language="JavaScript" type="text/javascript">
<!--

var NS4 = (navigator.appName == "Netscape" && parseInt(navigator.appVersion) < 5);

function addOption(theSel, theText, theValue)
{
  var newOpt = new Option(theText, theValue);
  var selLength = theSel.length;
  theSel.options[selLength] = newOpt;
}

function deleteOption(theSel, theIndex)
{ 
  var selLength = theSel.length;
  if(selLength>0)
  {
    theSel.options[theIndex] = null;
  }
}

function moveOptions(theSelFrom, theSelTo)
{
  
  var selLength = theSelFrom.length;
  var selectedText = new Array();
  var selectedValues = new Array();
  var selectedCount = 0;
  
  var i;
  
  // Find the selected Options in reverse order
  // and delete them from the 'from' Select.
  for(i=selLength-1; i>=0; i--)
  {
    if(theSelFrom.options[i].selected)
    {
      selectedText[selectedCount] = theSelFrom.options[i].text;
      selectedValues[selectedCount] = theSelFrom.options[i].value;
      deleteOption(theSelFrom, i);
      selectedCount++;
    }
  }
  
  // Add the selected text/values in reverse order.
  // This will add the Options to the 'to' Select
  // in the same order as they were in the 'from' Select.
  for(i=selectedCount-1; i>=0; i--)
  {
    addOption(theSelTo, selectedText[i], selectedValues[i]);
  }
  
  if(NS4) history.go(0);
}
function selectAllOptions(selStr)
{
  var selObj = document.getElementById(selStr);
  for (var i=0; i<selObj.options.length; i++) {
    selObj.options[i].selected = true;
  }
}


//-->
</script>
		<FORM action="#" method="post"> 
		<input type="hidden" name="id" value="$id">
	<TABLE cellSpacing=0 cellPadding=10 bgColor=#999999 border=0 width="100%">
	<TBODY>
	<TR>
	<TD vAlign=top align=left bgColor=#ffffff>

		<TABLE cellSpacing=0 cellPadding=0 width=900 bgColor=#ffffff border=0>
		<TBODY>
		<TR><TD class="label">Workflow name: </td><td><input type=text name=gname value="$gname" size=30></FONT> </TD></TR>
		<TR><TD class="label">Workflow ID: <b>$id</b></FONT> </TD></TR>
		<TR><TD class="label">Client Group: </FONT></td><td> <select name=cgroup>
end_of_html
$sql="select client_group_id,group_name from ClientGroup where status='A' order by group_name";
$sth=$dbhu->prepare($sql);
$sth->execute();
my $gid;
while (($gid,$gname)=$sth->fetchrow_array())
{
	print "<option value=$gid>$gname</option>\n";
}
$sth->finish();
print<<"end_of_html";
		</select></TD></TR>
		<TR><TD class="label">Worfklow Group: </FONT></td><td> <select name=wgroup multiple=multiple size=5>
end_of_html
my $i=1;
while ($i <= 10)
{
	if ($i != $id)
	{
		print "<option value=$i>Workflow $i</option>";
	}
	$i++;
}
print<<"end_of_html";
		  </select></td></tr><tr>
			<td class="label">Openers Date Range(yyyy-mm-dd):</td>
			<td class="field">
				<input class="field" size="8" value="" name=ostart_date /> to <input class="field" size="8" value="" name=oend_date />
			</td>
		  </tr>
		  <tr>
			<td class="label">Openers Range:</td>
			<td class="field">
				<input class="field" size="7" value="" name=ostart /> to <input class="field" size="7" value="" name=oend />
			</td>
		  </tr>
		  <tr>
			<td class="label">Clickers Date Range(yyyy-mm-dd):</td>
			<td class="field">
				<input class="field" size="8" value="" name=cstart_date /> to <input class="field" size="8" value="" name=cend_date /> 
			</td>
		  </tr>
		  <tr>
			<td class="label">Clickers Range:</td>
			<td class="field">
				<input class="field" size="7" value="" name=cstart /> to <input class="field" size="7" value="" name=cend />
			</td>
		  </tr>
		  <tr>
			<td class="label">Deliverable Date Range(yyyy-mm-dd):</td>
			<td class="field">
				<input class="field" size="8" value="" name=dstart_date /> to <input class="field" size="8" value="" name=dend_date />
			</td>
		  </tr>
		  <tr>
			<td class="label">Deliverable Range:</td>
			<td class="field">
				<input class="field" size="7" value="" name=dstart /> to <input class="field" size="7" value="" name=dend />
			</td>
		  </tr>
		  <tr>
			<td class="label">Convert Date Range(yyyy-mm-dd):</td>
			<td class="field">
				<input class="field" size="8" value="" name=convert_start_date /> to <input class="field" size="8" value="" name=convert_end_date />
			</td>
		  </tr>
		  <tr>
			<td class="label">Convert Range:</td>
			<td class="field">
				<input class="field" size="7" value="" name=convert_start /> to <input class="field" size="7" value="" name=convert_end />
			</td>
		  </tr>
		<TR><TD class="label">Source URL: </FONT></td><td><textarea name=surl cols=80 rows=10></textarea></td></tr> 
		<TR><TD class="label">Zips: </FONT></td><td><textarea name=zips cols=80 rows=10></textarea></td></tr> 
		  <tr><td class="label">Age:</td> <td class="field"> <input class="field" size="7" value="" name=age_start /> to <input class="field" size="7" value="" name=age_end /> </td> </tr>
		  <tr><td class="label">Gender:</td> <td class="field"> <input type=radio value="M" selected name=gender>Male&nbsp;&nbsp;<input type=radio value="F" name=gender>Female</td></tr>
		<TR><TD class="label">ISPs: </FONT></td><td> <select name=isps multiple=multiple size=5>
end_of_html
$sql="select class_id,class_name from email_class where status='Active' order by class_name";
$sth=$dbhu->prepare($sql);
$sth->execute();
my $cid;
my $cname;
while (($cid,$cname)=$sth->fetchrow_array())
{
	print "<option value=$cid>$cname</option>";
}
$sth->finish();
print<<"end_of_html";
	</select></td></tr>
	<tr><td colspan=2><img src=/images/spacer.gif height=20></td></tr>
		<TR><TD class="label">Actions: </FONT></td><td> <select name=action multiple=multiple size=4><option value=open>open</option><option value=click>click</option><option value=convert>convert</option><option value=delivered>delivered</option></select>&nbsp;&nbsp:<select name=oaid>
end_of_html
$sql="select advertiser_id,advertiser_name from advertiser_info where status='A' order by advertiser_name"; 
$sth=$dbhu->prepare($sql);
$sth->execute();
my $aid;
my $aname;
while (($aid,$aname)=$sth->fetchrow_array())
{
	print "<option value=\"$aid|$aname|\">$aname ($aid)</option>";
}
$sth->finish();
print<<"end_of_html";
	</select><input type="button" value="add" onClick=""/></td></tr>
	<tr><td colspan=2><img src=/images/spacer.gif height=20></td></tr>
		<TR><TD class="label">Current Actions: </td><td> <select name=caction multiple=multiple size=5><option value="open|182">Openers--DavisonDesign(182)</option></select><input type="button" value="Remove" onClick="" /></td></tr>
	<tr><td colspan=2><img src=/images/spacer.gif height=20></td></tr>
		<TR><TD class="label">Send: </td><td> <select name=aid>
end_of_html
$sql="select advertiser_id,advertiser_name from advertiser_info where status='A' order by advertiser_name"; 
$sth=$dbhu->prepare($sql);
$sth->execute();
my $aid;
my $aname;
while (($aid,$aname)=$sth->fetchrow_array())
{
	print "<option value=$aid>$aname</option>";
}
$sth->finish();
print<<"end_of_html";
	</select></td></tr>
		<TR><TD class="label">Template: </td><td> <select name=tid>
end_of_html
$sql="select template_id,template_name from brand_template where status='A' order by template_name"; 
$sth=$dbhu->prepare($sql);
$sth->execute();
my $tid;
my $tname;
while (($tid,$tname)=$sth->fetchrow_array())
{
	print "<option value=$tid>$tname</option>";
}
$sth->finish();
print<<"end_of_html";
	</select></td></tr>
		<TR><TD class="label">When: </td><td><input type=text size=3 name="minutes">&nbsp;minutes after&nbsp;<select name=afteraction><option value="received">received</option><option value="delivered">delivered</option><option value="opened">opened</option><option value=clicked>clicked</option><option value=converted>converted</option></select></td></tr> 
		  <tr><td class="label">Status:</td> <td class="field"> <input type=radio value="A" selected name=wstatus>Active&nbsp;&nbsp;<input type=radio value="I" name=wstatus>Inactive</td></tr>
	<tr><td colspan=2 align=middle><input type=submit value="Save" name="submit"><input type=submit value="Save as New" name="submit">&nbsp;&nbsp;<a href="mainmenu.cgi"><img src="/images/home_blkline.gif" border=0></a></td></tr>
</table>
	</TD>
	</TR>
	</TBODY>
	</TABLE>
</form>

</TD>
</TR>
<TR>
<TD noWrap align=left height=1>
end_of_html

$pms->footer();
exit(0);
