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
my $CLASS;
my $seed_str="";
my $url_str="";
my $zip_str="";
my $clientid= $query->param('clientid');
if ($clientid eq "")
{
	$clientid=0;
}
my $submit_str;
my $export= $query->param('export');
if ($export eq "")
{
	$export=0;
}
my $inchkID= $query->param('inchkID');
if ($inchkID eq "")
{
	$inchkID=0;
}
#------ connect to the util database ------------------
my ($dbhq,$dbhu)=$util->get_dbh();
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}
if ($export > 0)
{
	$submit_str="Export";
	my $username;
	my $exportData;
	my $ctime;
	$sql = "select username, exportData,now() from UserAccounts where user_id = ?";
	$sth = $dbhq->prepare($sql) ;
	$sth->execute($user_id);
	($username, $exportData,$ctime) = $sth->fetchrow_array();
	$sth->finish();
	if ($exportData eq "N")
	{
		open(LOG2,">>/tmp/export.log");
		print LOG2 "$ctime - $username\n";
		close(LOG2);
		print "Content-type: text/html\n\n";
		print<<"end_of_html";
<html><head><title>Export Error</title></head>
<body>
<center><h3>You do not have permission to Export Data.  This attempt has been logged.</h3><br>
<a href="/cgi-bin/mainmenu.cgi"><img src="/images/home_blkline.gif" border=0></a>
</center>
</body>
</html>
end_of_html
		exit();
	}
}
else
{
	$submit_str="Calculate";
}
	
#
if ($inchkID > 0)
{
	$sql="select * from UniqueCheck where check_id=?";
	$sth=$dbhu->prepare($sql);
	$sth->execute($inchkID);
	$DETAILS=$sth->fetchrow_hashref();
	$sth->finish();
	$DETAILS->{opener_start}=$DETAILS->{opener_start1};
	$DETAILS->{opener_end}=$DETAILS->{opener_end1};
	$DETAILS->{clicker_start}=$DETAILS->{clicker_start1};
	$DETAILS->{clicker_end}=$DETAILS->{clicker_end1};
	$DETAILS->{convert_start}=$DETAILS->{convert_start1};
	$DETAILS->{convert_end}=$DETAILS->{convert_end1};
	$DETAILS->{deliverable_start}=$DETAILS->{deliverable_start1};
	$DETAILS->{deliverable_end}=$DETAILS->{deliverable_end1};
	#
	my $class_id;
	$sql="select class_id from UniqueCheckIsp where check_id=?";
	$sth=$dbhu->prepare($sql);
	$sth->execute($inchkID);
	while (($class_id)=$sth->fetchrow_array())
	{
		$CLASS->{$class_id}=1;
	}
	$sth->finish();
	#
	my $em;
	$sql="select email_addr from UniqueCheckSeed where check_id=? order by checkSeedID";
	$sth=$dbhu->prepare($sql);
	$sth->execute($inchkID);
	while (($em)=$sth->fetchrow_array())
	{
		$seed_str.=$em."\n";
	}
	$sth->finish();
	my $turl;
	$sql="select source_url from UniqueCheckUrl where check_id=?";
	$sth=$dbhu->prepare($sql);
	$sth->execute($inchkID);
	while (($turl)=$sth->fetchrow_array())
	{
		$url_str.=$turl."\n";
	}
	$sth->finish();
	$sql="select zip from UniqueCheckZip where check_id=?";
	$sth=$dbhu->prepare($sql);
	$sth->execute($inchkID);
	while (($turl)=$sth->fetchrow_array())
	{
		$zip_str.=$turl."\n";
	}
	$sth->finish();

}
else
{
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
	$DETAILS->{dupCnt}=0;
	$DETAILS->{send_international}='Y';
	$DETAILS->{source_url}="";
	$DETAILS->{gender}="";
	$DETAILS->{min_age}=0;
	$DETAILS->{max_age}=0;
	$DETAILS->{volume_desired}=0;
}

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
	width: 50%;
	text-align: right;
	font-weight: bold;
  }

td.field {
	width: 50%;
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
function selectall1()
{
    refno=/fields/;
    for (var x=0; x < document.campform.length; x++)
    {
        if ((document.campform.elements[x].type=="checkbox") && (refno.test(document.campform.elements[x].name)))
        {
            document.campform.elements[x].checked = true;
        }
    }
}
function unselectall1()
{
    refno=/fields/;
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
	<form method="post" name="campform" action="uniqueprofile_check_save.cgi" onsubmit="selectAllOptions('sel2');">
	<input type=hidden name=clientid value=$clientid>
	<input type=hidden name=export value=$export>
	  <table>
	<tr><td class="label">Client Group</td><td colspan=2><select name=cgroupid><option value=0 selected>--Manually Select Clients--</option>
end_of_html
$sql="select client_group_id,group_name from ClientGroup where status='A' order by group_name";
my $sth=$dbhu->prepare($sql);
$sth->execute();
while (($cid,$gname)=$sth->fetchrow_array())
{
	if ($DETAILS->{client_group_id} == $cid)
	{
		print "<option value=$cid selected>$gname</option>\n";
	}
	else
	{
		print "<option value=$cid>$gname</option>\n";
	}
}
$sth->finish();
print<<"end_of_html";
</select></td></tr>
	<tr><td class="label">Available Clients</td><td></td><td>Clients in Group</td>
<td>Manually Entered Client Ids</td></tr>
	<tr>
		<td class="label">
			<select name=sel1 id=sel1 size="20" multiple="multiple">
end_of_html
my $fname;
my $client;
if (($inchkID > 0) and ($DETAILS->{client_group_id} == 0))
{
	$sql="select user_id,username from user where status='A' and user_id != $clientid and user_id not in (select client_id from UniqueCheckClient where check_id=$inchkID) order by username";
}
else
{
	$sql="select user_id,username from user where status='A' and user_id != $clientid order by username";
}
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
end_of_html
my $fname;
my $client;
if (($inchkID > 0) and ($DETAILS->{client_group_id} == 0))
{
	$sql="select user_id,username from user where status='A' and (user_id = $clientid or user_id in (select client_id from UniqueCheckClient where check_id=$inchkID)) order by username";
}
else
{
	$sql="select user_id,username from user where status='A' and user_id = $clientid order by username";
}
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
		<td><textarea name=mcid cols=10 rows=20></textarea></td> 
	</tr>
		  <tr>
			<td class="label">Openers Date Range(yyyy-mm-dd):</td>
			<td colspan=2 class="field">
				<input class="field" size="8" value="$DETAILS->{opener_start_date}" name=ostart_date /> to <input class="field" size="8" value="$DETAILS->{opener_end_date}" name=oend_date />
			</td>
		  </tr>
		  <tr>
			<td class="label">Openers Range:</td>
			<td colspan=2 class="field">
				<input class="field" size="7" value="$DETAILS->{opener_start}" name=ostart /> to <input class="field" size="7" value="$DETAILS->{opener_end}" name=oend />&nbsp;&nbsp;or&nbsp;&nbsp;
				<input class="field" size="7" value="0" name=ostart1 /> to <input class="field" size="7" value="0" name=oend1 />&nbsp;&nbsp;or&nbsp;&nbsp;
				<input class="field" size="7" value="0" name=ostart2 /> to <input class="field" size="7" value="0" name=oend2 />
			</td>
		  </tr>
		  <tr>
			<td class="label">Clickers Date Range(yyyy-mm-dd):</td>
			<td colspan=2 class="field">
				<input class="field" size="8" value="$DETAILS->{clicker_start_date}" name=cstart_date /> to <input class="field" size="8" value="$DETAILS->{clicker_end_date}" name=cend_date />
			</td>
		  </tr>
		  <tr>
			<td class="label">Clickers Range:</td>
			<td colspan=2 class="field">
				<input class="field" size="7" value="$DETAILS->{clicker_start}" name=cstart /> to <input class="field" size="7" value="$DETAILS->{clicker_end}" name=cend />&nbsp;&nbsp;or&nbsp;&nbsp;
				<input class="field" size="7" value="0" name=cstart1 /> to <input class="field" size="7" value="0" name=cend1 />&nbsp;&nbsp;or&nbsp;&nbsp;
				<input class="field" size="7" value="0" name=cstart2 /> to <input class="field" size="7" value="0" name=cend2 />
			</td>
		  </tr>
		  <tr>
			<td class="label">Deliverable Date Range(yyyy-mm-dd):</td>
			<td colspan=2 class="field">
				<input class="field" size="8" value="$DETAILS->{deliverable_start_date}" name=dstart_date /> to <input class="field" size="8" value="$DETAILS->{deliverable_end_date}" name=dend_date />
			</td>
		  </tr>
		  <tr>
			<td class="label">Deliverable Range:</td>
			<td colspan=2 class="field">
				<input class="field" size="7" value="$DETAILS->{deliverable_start}" name=dstart /> to <input class="field" size="7" value="$DETAILS->{deliverable_end}" name=dend />&nbsp;&nbsp;or&nbsp;&nbsp;
				<input class="field" size="7" value="0" name=dstart1 /> to <input class="field" size="7" value="0" name=dend1 />&nbsp;&nbsp;or&nbsp;&nbsp;
				<input class="field" size="7" value="0" name=dstart2 /> to <input class="field" size="7" value="0" name=dend2 />
			</td>
		  </tr>
		  <tr>
			<td class="label">Convert Date Range(yyyy-mm-dd):</td>
			<td colspan=2 class="field">
				<input class="field" size="8" value="$DETAILS->{convert_start_date}" name=convert_start_date /> to <input class="field" size="8" value="$DETAILS->{convert_end_date}" name=convert_end_date />
			</td>
		  </tr>
		  <tr>
			<td class="label">Convert Range:</td>
			<td colspan=2 class="field">
				<input class="field" size="7" value="$DETAILS->{convert_start}" name=convert_start /> to <input class="field" size="7" value="$DETAILS->{convert_end}" name=convert_end />&nbsp;&nbsp;or&nbsp;&nbsp;
				<input class="field" size="7" value="0" name=convert_start1 /> to <input class="field" size="7" value="0" name=convert_end1 />&nbsp;&nbsp;or&nbsp;&nbsp;
				<input class="field" size="7" value="0" name=convert_start2 /> to <input class="field" size="7" value="0" name=convert_end2 />
			</td>
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
	if ($CLASS->{$cid})
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
		<TR><TD class="label">Source URL: </FONT></td><td colspan=2><textarea name=surl cols=80 rows=10>$url_str</textarea></td></tr> 
		<TR><TD class="label">Zips: </FONT></td><td colspan=2><textarea name=zips cols=80 rows=10>$zip_str</textarea></td></tr> 
		  <tr><td class="label">Age:</td> <td class="field"> <input class="field" size="7" value="$DETAILS->{min_age}" name=min_age /> to <input class="field" size="7" value="$DETAILS->{max_age}" name=max_age /> </td> </tr>
<tr><td class=label>Gender:</td> <td class=field>
end_of_html
my @G=("M","F","","Empty");
my @G1=("Male","Female","N/A","Empty");
my $i=0;
while ($i <= $#G)
{
	if ($DETAILS->{gender} eq $G[$i])
	{
		print "<input name=gender type=checkbox value=\"$G[$i]\" checked>$G1[$i]</option>&nbsp;&nbsp;";	
	}
	else
	{
		print "<input name=gender type=checkbox value=\"$G[$i]\">$G1[$i]</option>&nbsp;&nbsp;";	
	}
	$i++;
}
print "</td></tr><tr><td class=label>Dont mail anybody delivered in last</td> <td class=field> <input type=text size=3 maxlength=3 value=\"0\" name=DeliveryDays> days (Specify zero to mail everybody)</td></tr>\n";
if ($export)
{
	print "<tr><td class=label>Records per file:</td> <td class=field> <input type=text size=10 maxlength=10 value=\"$DETAILS->{volume_desired}\" name=recordsFile>(Specify 0 to place all in one file)</td></tr>\n";
    print "<tr><td colspan=3 align=center><a href=\"javascript:selectall1();\">Select All</a>&nbsp;&nbsp;&nbsp;<a href=\"javascript:unselectall1();\">Unselect All</a><br></td></tr>\n";
	print "<tr><td class=label>Fields to Include in File:</td> <td class=field> <input type=checkbox value=email_addr name=fields checked>Email&nbsp;&nbsp;<input type=checkbox value=eid name=fields checked>EID&nbsp;&nbsp;<input type=checkbox value=first_name name=fields checked>First Name&nbsp;&nbsp;<input type=checkbox value=last_name name=fields checked>Last Name&nbsp;&nbsp;<input type=checkbox value=sdate name=fields>Subscribe Date&nbsp;&nbsp;<input type=checkbox value=Status name=fields>Status&nbsp;&nbsp;<input type=checkbox value=ISP name=fields>ISP&nbsp;&nbsp;<br><input type=checkbox value=url name=fields>Source Url&nbsp;&nbsp;<input type=checkbox value=gender name=fields>Gender&nbsp;&nbsp;<input type=checkbox value=IP name=fields>IP&nbsp;&nbsp;<input type=checkbox value=client_id name=fields>Client ID&nbsp;&nbsp;<input type=checkbox value=cdate name=fields>Capture Date&nbsp;&nbsp;<input type=checkbox value=address name=fields>Address&nbsp;&nbsp;<input type=checkbox value=address2 name=fields>Address2&nbsp;&nbsp;<br><input type=checkbox value=city name=fields>City&nbsp;&nbsp;<input type=checkbox value=state name=fields>State&nbsp;&nbsp;<input type=checkbox value=zip name=fields>Zip&nbsp;&nbsp;<input type=checkbox value=dob name=fields>Birth Date&nbsp;&nbsp;<input type=checkbox value=phone name=fields>Phone&nbsp;&nbsp;</td></tr>\n";
	print "<tr><td class=label>Advertiser:</td> <td class=field><select name=aid><option selected value=0>None</option>";
	my $aid;
	my $aname;
	$sql="select advertiser_id,advertiser_name from advertiser_info where status='A' and test_flag='N' order by advertiser_name";
	$sth=$dbhu->prepare($sql);
	$sth->execute();
	while (($aid,$aname)=$sth->fetchrow_array())
	{
		if ($aid == $DETAILS->{advertiser_id})
		{
			print "<option selected value=$aid>$aname</option>";
		}
		else
		{
			print "<option value=$aid>$aname</option>";
		}
	}
	$sth->finish(); 
	print "</select></td></tr>\n";}
print<<"end_of_html";
			</td>
		  </tr>
		  <tr><td class=label>Randomize: </td><td><input type=radio checked value=Y name=randomize_flag>Yes&nbsp;&nbsp;<input type=radio value=N name=randomize_flag>No</td></tr>
		  <tr><td class=label>Is in at least this many clients(zero - to disable): </td><td><input type=text value="$DETAILS->{dupCnt}"  name=dupCnt size=3 maxlength=3></td></tr>
		<TR><TD class="label">Seeds(one per line): </FONT></td><td colspan=2><textarea name=seeds cols=80 rows=10>$seed_str</textarea></td></tr> 
		  <tr>
			<td class="label"><br />Last Action Country:</td>
			<td class="field"><br />
end_of_html
my $classid;
$sql="select countryID,countryCode from Country where visible=1 order by countryCode"; 
$sth=$dbhq->prepare($sql);
$sth->execute();
while (($cid,$cname)=$sth->fetchrow_array())
{
	if ($inchkID >0)
	{
		$sql="select countryID from UniqueCheckCountry where check_id=? and countryID=?";
		my $sth1=$dbhu->prepare($sql);
		$sth1->execute($inchkID,$cid);
		($classid)=$sth1->fetchrow_array();
		$sth1->finish();
	}
	if ($cid == $classid)
	{
		print "<input class=radio type=checkbox checked name=country value=$cid >$cname\n";
	}
	else
	{
		print "<input class=radio type=checkbox name=country value=$cid >$cname\n";
	}		
}
$sth->finish();
print<<"end_of_html";
		  <tr>
			<td class="label"><br />Devices:</td>
			<td class="field"><br />
end_of_html
$sql="select userAgentStringLabelID,userAgentStringLabel from UserAgentStringsLabel order by 2"; 
$sth=$dbhq->prepare($sql);
$sth->execute();
while (($cid,$cname)=$sth->fetchrow_array())
{
	if ($inchkID >0)
	{
		$sql="select userAgentStringLabelID from UniqueCheckUA where check_id=? and userAgentStringLabelID=?";
		my $sth1=$dbhu->prepare($sql);
		$sth1->execute($inchkID,$cid);
		($classid)=$sth1->fetchrow_array();
		$sth1->finish();
	}
	if ($cid == $classid)
	{
		print "<input class=radio type=checkbox checked name=ua value=$cid >$cname\n";
	}
	else
	{
		print "<input class=radio type=checkbox name=ua value=$cid >$cname\n";
	}		
}
$sth->finish();
if ($clientid > 0)
{
	my $keyID;
	my $valueID;
	my $key;
	my $value;
	my $oldkeyID=0;
	$sql="SELECT crk.clientRecordKeyID, crv.clientRecordValueID, clientRecordKeyName, clientRecordValueName FROM ClientRecordCustomData cd JOIN ClientRecordKeys crk on cd.clientRecordKeyID = crk.clientRecordKeyID JOIN ClientRecordValues crv on cd.clientRecordValueID = crv.clientRecordValueID where clientID=? group by crk.clientRecordKeyID, crv.clientRecordValueID, clientRecordKeyName, clientRecordValueName";
	my $sth1=$dbhu->prepare($sql);
	$sth1->execute($clientid);
	while (($keyID,$valueID,$key,$value)=$sth1->fetchrow_array())
	{
		if ($keyID != $oldkeyID)
		{
			if ($oldkeyID != 0)
			{
				print "</select></td></tr>\n";
			}
			else
			{
  				print "<tr><td colspan=2 align=middle><font size=+1>Custom Data</font></td></tr>"; 
			}
  			print "<tr><td class=label>$key</td><td class=field><select name=cdata size=5 multiple>"; 
			$oldkeyID=$keyID;
		}
		print "<option value=\"$keyID|$valueID\">$value</option>";
	}
	$sth1->finish();
	if ($oldkeyID != 0)
	{
		print "</select></td></tr>\n";
	}
}
print<<"end_of_html";
		</table>

		<div class="submit">
			<input class="submit" value="$submit_str" type="submit" name=submit>
<br>
		</div>
	</form></div>
<center>
<a href="/cgi-bin/mainmenu.cgi"><img src="/images/home_blkline.gif" border=0></a>

</body></html>
end_of_html
exit(0);
