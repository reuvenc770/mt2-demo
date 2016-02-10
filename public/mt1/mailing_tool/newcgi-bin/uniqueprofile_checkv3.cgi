#!/usr/bin/perl

# *****************************************************************************************
# uniqueprofile_check.cgi
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
my $gname;
my $classid;
my $pid= $query->param('pid');
#------ connect to the util database ------------------
my ($dbhq,$dbhu)=$util->get_dbh();
#
	$DETAILS->{profile_name}="";
	$DETAILS->{opener_start}=0;
	$DETAILS->{opener_end}=0;
	$DETAILS->{clicker_start}=0;
	$DETAILS->{clicker_end}=0;
	$DETAILS->{deliverable_start}=0;
	$DETAILS->{deliverable_end}=0;
	$DETAILS->{convert_start}=0;
	$DETAILS->{convert_end}=0;
	$DETAILS->{deliverable_factor}=0;
	$DETAILS->{complaint_control}='Disable';
	$DETAILS->{cc_aol_send}=0;
	$DETAILS->{cc_yahoo_send}=0;
	$DETAILS->{cc_hotmail_send}=0;
	$DETAILS->{cc_other_send}=0;
	$DETAILS->{send_international}='Y';
	$DETAILS->{send_confirmed}='Y';
	$DETAILS->{ramp_up_freq}=0;
	$DETAILS->{subtract_days}=0;
	$DETAILS->{add_days}=0;
	$DETAILS->{source_url}="";
	$DETAILS->{volume_desired}=0;
	$DETAILS->{gender}="";
	$DETAILS->{min_age}=0;
	$DETAILS->{max_age}=0;

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
	width: 80%;
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
function selectall()
{
    refno=/isps/;
    for (var x=0; x < document.campform.length; x++)
    {
        if ((document.campform.elements[x].type=="checkbox") && (refno.test(document.campform.elements[x].name)))
        {
            document.campform.elements[x].checked = true;
        }
    }
}
function unselectall()
{
    refno=/isps/;
    for (var x=0; x < document.campform.length; x++)
    {
        if ((document.campform.elements[x].type=="checkbox") && (refno.test(document.campform.elements[x].name)))
        {
            document.campform.elements[x].checked = false;
        }
    }
}
</script>
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
</head>

<body>
  <div id="form">
	<form method="post" name="campform" action="uniqueprofile_checkv3_save.cgi" onsubmit="selectAllOptions('sel2');">
	  <table>
	<tr><td class="label">Client Group</td><td colspan=2><select name=cgroupid><option value=0 selected>--Manually Select Clients--</option>
end_of_html
$sql="select client_group_id,group_name from ClientGroup where status='A' order by group_name";
my $sth=$dbhu->prepare($sql);
$sth->execute();
while (($cid,$gname)=$sth->fetchrow_array())
{
	print "<option value=$cid>$gname</option>\n";
}
$sth->finish();
print<<"end_of_html";
</select></td></tr>
	<tr><td class="label">Available Clients</td><td></td><td>Clients in Group</td><td>Manually Entered Client Ids</td></tr>
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
		<td><textarea name=mcid cols=10 rows=20></textarea></td>
	</tr>
		  <tr>
			<td class="label">Deliverable Factor:</td>
			<td colspan=2 class="field">
				<input class="field" size="7" value="$DETAILS->{deliverable_factor}" name=dfactor /><em class="note">(Set to zero to disable)</em>
			</td>
		  </tr>
		  <tr>
			<td class="label">Send International:</td>
			<td colspan=2 class="field">
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
			<td class="label">Volume Desired:</td>
			<td colspan=2 class="field">
				<input class="field" size="10" value="$DETAILS->{volume_desired}" name=volume_desired />
			</td>
		  </tr>
        <tr><td colspan=3 align=center><a href="javascript:selectall();">Select All</a>&nbsp;&nbsp;&nbsp;<a href="javascript:unselectall();">Unselect All</a><br></td></tr>
		  <tr>
			<td class="label"><br />ISPs to Send:</td>
			<td colspan=2 class="field"><br />
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
my $surl="";
my $zips="";
print<<"end_of_html";
			</td>
		  </tr>
		<TR><TD class="label">Source URL: </FONT></td><td class=field colspan=2><textarea name=surl cols=80 rows=10>$surl</textarea></td></tr> 
		<TR><TD class="label">Zips: </FONT></td><td class=field colspan=2><textarea name=zips cols=80 rows=10>$zips</textarea></td></tr> 
		  <tr><td class="label">Age:</td> <td class="field"> <input class="field" size="7" value="$DETAILS->{min_age}" name=min_age /> to <input class="field" size="7" value="$DETAILS->{max_age}" name=max_age /> </td> </tr>
end_of_html
if ($DETAILS->{gender} eq "")
{
	print "<tr><td class=label>Gender:</td> <td class=field> <input type=radio value=\"M\" name=gender>Male&nbsp;&nbsp;<input type=radio value=\"F\" name=gender>Female&nbsp;&nbsp;<input type=radio checked value=\"\" name=gender>N/A&nbsp;&nbsp;<input type=radio value=\"Empty\" name=gender>Empty</td></tr>\n";
}
elsif ($DETAILS->{gender} eq "M")
{
	print "<tr><td class=label>Gender:</td> <td class=field> <input type=radio value=\"M\" checked name=gender>Male&nbsp;&nbsp;<input type=radio value=\"F\" name=gender>Female&nbsp;&nbsp;<input type=radio value=\"\" name=gender>N/A&nbsp;&nbsp;<input type=radio value=\"Empty\" name=gender>Empty</td></tr>\n";
}
elsif ($DETAILS->{gender} eq "F")
{
	print "<tr><td class=label>Gender:</td> <td class=field> <input type=radio value=\"M\" name=gender>Male&nbsp;&nbsp;<input type=radio value=\"F\" checked name=gender>Female&nbsp;&nbsp;<input type=radio value=\"\" name=gender>N/A&nbsp;&nbsp;<input type=radio value=\"Empty\" name=gender>Empty</td></tr>\n";
}
elsif ($DETAILS->{gender} eq "Empty")
{
	print "<tr><td class=label>Gender:</td> <td class=field> <input type=radio value=\"M\" name=gender>Male&nbsp;&nbsp;<input type=radio value=\"F\" name=gender>Female&nbsp;&nbsp;<input type=radio value=\"\" name=gender>N/A&nbsp;&nbsp;<input type=radio value=\"Empty\" checked name=gender>Empty</td></tr>\n";
}
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
