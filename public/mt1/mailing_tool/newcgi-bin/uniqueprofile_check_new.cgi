#!/usr/bin/perl

# *****************************************************************************************
# uniqueprofile_check_new.cgi
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
my $DETAILS;
my $sql;
my $cid;
my $cname;
my $classid;
#------ connect to the util database ------------------
my ($dbhq,$dbhu)=$util->get_dbh();
#
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
<script language="JavaScript" type="text/javascript">
<!--

var NS4 = (navigator.appName == "Netscape" && parseInt(navigator.appVersion) < 5);

function updcnt(theSelFrom)
{
  	var selLength = theSelFrom.length;
  var selectedText = new Array();
  var selectedValues = new Array();
  	var selectedCount = 0;
  	var i;
  for(i=selLength-1; i>=0; i--)
  {
    if(theSelFrom.options[i].selected)
    {
	  var str = theSelFrom.options[i].text;
	  var str1_array=str.split(" ");
      theSelFrom.options[i].text=str1_array[0]+" "+document.campform.clientcnt.value;
    }
  }
}
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
  	var str = selObj.options[i].text;
	var str1_array=str.split(" ");
	if (str1_array[1] == "")
	{
		str1_array[1]=0;
	}
	selObj.options[i].value=selObj.options[i].value+"|"+str1_array[1];
    selObj.options[i].selected = true;
  }
}


//-->
</script>
</head>

<body>
  <div id="form">
	<form method="get" name="campform" action="uniqueprofile_check_save_new.cgi" onsubmit="selectAllOptions('sel2');">
	  <table>
	<tr><td class="label">Available Clients</td><td></td><td>Clients in Group</td><td></td></tr>
	<tr>
		<td class="label">
			<select name=sel1 id=sel1 size="20" multiple="multiple">
end_of_html
my $fname;
my $client;
$sql="select user_id,username from user where status='A' order by username";
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($client,$fname)=$sth->fetchrow_array())
{
	print "<option value=\"$client\">$fname</option>";
}
$sth->finish();
print<<"end_of_html";
			</select>
		</td>
		<td align="center" valign="middle">
			<input type="button" value="--&gt;"
			 onclick="moveOptions(this.form.sel1, this.form.sel2);" /><br />
			<input type="button" value="&lt;--"
			 onclick="moveOptions(this.form.sel2, this.form.sel1);" />
		</td>
		<td>
			<select name=sel2 id=sel2 size="20" multiple="multiple">
			</select>
		</td>
		<td>Count: <input type=text name=clientcnt>&nbsp;&nbsp;<input type=button onClick="javascript:updcnt(this.form.sel2);" value="Update"></td>
	</tr>
		  <tr>
			<td class="label">End Day Count:</td>
			<td colspan=3 class="field">
				<input class="field" size="7" value="0" name=end1 /> 
			</td>
		  </tr>
		  <tr>
			<td class="label">Send International:</td>
			<td colspan=3 class="field">
end_of_html
			if ($DETAILS->{send_international} eq "Y")
			{
				print "<input type=radio checked value=Y name=send_international />Yes&nbsp;&nbsp;&nbsp;<input type=radio value=N name=send_international />No\n";
			}
			else
			{
				print "<input type=radio value=Y name=send_international />Yes&nbsp;&nbsp;&nbsp;<input type=radio checked value=N name=send_international />No\n";
			}
print<<"end_of_html";
			</td>
		  </tr>
		  <tr>
			<td class="label">Send Confirmed:</td>
			<td colspan=3 class="field">
end_of_html
			if ($DETAILS->{send_confirmed} eq "Y")
			{
				print "<input type=radio checked value=Y name=send_confirmed />Yes&nbsp;&nbsp;&nbsp;<input type=radio value=N name=send_confirmed />No\n";
			}
			else
			{
				print "<input type=radio value=Y name=send_confirmed />Yes&nbsp;&nbsp;&nbsp;<input type=radio checked value=N name=send_confirmed />No\n";
			}
print<<"end_of_html";
			</td>
		  </tr>
		  <tr>
			<td class="label"><br />ISPs to Send:</td>
			<td colspan=3 class="field"><br />
end_of_html
$sql="select class_id,class_name from email_class where status='Active' order by class_name";
$sth=$dbhq->prepare($sql);
$sth->execute();
while (($cid,$cname)=$sth->fetchrow_array())
{
	if ($cid == $classid)
	{
		print "<input class=radio type=checkbox checked name=isps value=$cid />$cname/\n";
	}
	else
	{
		print "<input class=radio type=checkbox name=isps value=$cid />$cname/\n";
	}		
}
$sth->finish();
print<<"end_of_html";
			</td>
		  </tr>

		</table>

		<div class="submit">
			<input class="submit" value="Calculate" type="submit" name=submit>
<br>
		</div>
	</form></div>
<center>
<a href="/cgi-bin/mainmenu.cgi"><img src="/images/home_blkline.gif" border=0></a>

</body></html>
end_of_html
exit(0);
