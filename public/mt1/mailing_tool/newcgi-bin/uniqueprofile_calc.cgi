#!/usr/bin/perl

# *****************************************************************************************
# uniqueprofile_cal.cgi
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
	text-align: left;
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
	<form method="post" name="campform" action="uniqueprofile_calc_save.cgi">
	<center>
	  <table border=1>
	<tr><th>Client</th><th>Openers</th><th>Clickers</th><th>Deliverables</th><th>Newest/Oldest</th></tr>
end_of_html
my $client_cnt=15;
my $fname;
my $client;
my $i=1;
while ($i <= $client_cnt)
{
	print "<tr><td><select name=client$i><option value=\"\" selected><--Select One--></option>";
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
		<td><input type=text name=opencount$i size=7 value=0></td>
		<td><input type=text name=clickcount$i size=7 value=0></td>
		<td><input type=text name=count$i size=7 value=0></td>
		<td><select name=order$i><option value=Newest selected>Newest</option><option value=Oldest>Oldest</option></select></td>
	</tr>
end_of_html
$i++;
}
print<<"end_of_html";
</table>
<table>
		  <tr>
			<td class="label">Openers Date Range(yyyy-mm-dd):</td>
			<td class="field">
				<input class="field" size="8" value="$DETAILS->{opener_start_date}" name=ostart_date /> to <input class="field" size="8" value="$DETAILS->{opener_end_date}" name=oend_date />
			</td>
		  </tr>
		  <tr>
			<td class="label">Openers Range:</td>
			<td class="field">
				<input class="field" size="7" value="$DETAILS->{opener_start}" name=ostart /> to <input class="field" size="7" value="$DETAILS->{opener_end}" name=oend />
			</td>
		  </tr>
		  <tr>
			<td class="label">Clickers Date Range(yyyy-mm-dd):</td>
			<td class="field">
				<input class="field" size="8" value="$DETAILS->{clicker_start_date}" name=cstart_date /> to <input class="field" size="8" value="$DETAILS->{clicker_end_date}" name=cend_date />
			</td>
		  </tr>
		  <tr>
			<td class="label">Clickers Range:</td>
			<td class="field">
				<input class="field" size="7" value="$DETAILS->{clicker_start}" name=cstart /> to <input class="field" size="7" value="$DETAILS->{clicker_end}" name=cend />
			</td>
		  </tr>
		  <tr>
			<td class="label">Deliverable Date Range(yyyy-mm-dd):</td>
			<td class="field">
				<input class="field" size="8" value="$DETAILS->{deliverable_start_date}" name=dstart_date /> to <input class="field" size="8" value="$DETAILS->{deliverable_end_date}" name=dend_date />
			</td>
		  </tr>
		  <tr>
			<td class="label">Deliverable Range:</td>
			<td class="field">
				<input class="field" size="7" value="$DETAILS->{deliverable_start}" name=dstart /> to <input class="field" size="7" value="$DETAILS->{deliverable_end}" name=dend />
			</td>
		  </tr>
		  <tr>
			<td class="label">Send International:</td>
			<td class="field">
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
			<td class="label"><br />ISPs to Send:</td>
			<td class="field"><br />
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
	</form>
<center>
<a href="/cgi-bin/mainmenu.cgi"><img src="/images/home_blkline.gif" border=0></a>

</body></html>
end_of_html
exit(0);
