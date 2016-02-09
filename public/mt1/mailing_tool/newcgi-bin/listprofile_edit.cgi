#!/usr/bin/perl
# *****************************************************************************************
# listprofile_edit.cgi
#
# this page is used to edit a List Profile 
#
# History
# Jim Sobeck, 5/31/05, Creation
# Jim Sobeck, 01/17/2006, Added 3rd party mailer logic
# Jim Sobeck, 05/22/2006, Added add frequency logic for chunking profiles
# Jim Sobeck, 09/25/2006, Added master flag
# Jim Sobeck, 04/30/2007, Added comcast flag
#*****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use DBI;
use util;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $add_new_month;
my $sql;
my $dbh;
my $chklist_str;
my $errmsg;
my $user_id;
my $list_name;
my $server_name;
my %checkit = ( 'Y' => 'CHECKED', 'N' => '' );
my ($list_id,$list_name,$member_cnt,$aol_cnt,$yahoo_cnt,$hotmail_cnt,$aol_cnt_p);
my $images = $util->get_images_url;
my $pid = $query->param('pid');
my $mesg= $query->param('mesg');
my ($company,$profile_name,$day_flag,$aol_flag,$yahoo_flag,$other_flag,$hotmail_flag,$comcast_flag);
my $add_freq;
my $last7_flag = "";
my $last3_flag = "";
my $last15_flag = "";
my $lastall_flag = "";
my $last30_flag = "";
my $last60_flag = "";
my $last90_flag = "";
my $last120_flag = "";
my $last150_flag = "";
my $last180_flag = "";
my $client_id;
my $alllists;
my $aolalllists;
my $max_emails;
my $loop_flag;
my $hotmailalllists;
my $otheralllists;
my $yahooalllists;
my $totalall = 0;
my $totalaol = 0;
my $totalhotmail = 0;
my $totalother = 0;
my $totalyahoo = 0;
my $loop_yes;
my $loop_no;
my $ptid;
my $unique_id;
my $percent_sub;
my $list_to_add_from;
my $amount_to_add;
my $clean_add;
my $randomize_records;
my $master_flag;
my $profile_type;
my $open_click_ignore;
my $send_dmseeds;
my $dmseed_cnt;
my $old_nl_id;
my $old_nl_send;
my $c1_str;
my $c2_str;
my $c3_str;
my $dupes_flag;
my $start_day;
my $end_day;
my $opener_start;
my $opener_end;
my $clicker_start;
my $clicker_end;
my $start_date;
my $end_date;

# connect to the util database
my ($dbhq,$dbhu)=$util->get_dbh();

# check for login

$user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}
$sql = "select company,profile_name,day_flag,aol_flag,yahoo_flag,other_flag,hotmail_flag,client_id,max_emails,loop_flag,third_party_id,list_to_add_from,amount_to_add,unique_id,percent_sub,profile_type,add_freq,add_new_month,clean_add,randomize_records,master,nl_id,nl_send,comcast_flag,open_clickers_ignore_date,send_dmseeds,dmseed_cnt,dupes_flag,start_day,end_day,opener_start,opener_end,clicker_start,clicker_end,start_date,end_date from list_profile,user where profile_id=$pid and list_profile.client_id=user.user_id";
$sth = $dbhq->prepare($sql) ;
$sth->execute();
($company,$profile_name,$day_flag,$aol_flag,$yahoo_flag,$other_flag,$hotmail_flag,$client_id,$max_emails,$loop_flag,$ptid,$list_to_add_from,$amount_to_add,$unique_id,$percent_sub,$profile_type,$add_freq,$add_new_month,$clean_add,$randomize_records,$master_flag,$old_nl_id,$old_nl_send,$comcast_flag,$open_click_ignore,$send_dmseeds,$dmseed_cnt,$dupes_flag,$start_day,$end_day,$opener_start,$opener_end,$clicker_start,$clicker_end,$start_date,$end_date) = $sth->fetchrow_array();
$sth->finish();
#
if ($loop_flag eq "")
{
	$loop_flag = "N";
}
$loop_yes = "";
$loop_no = "checked";
if ($loop_flag eq "Y")
{
	$loop_yes = "checked";
	$loop_no = "";
}
if ($day_flag eq "7")
{
	$last7_flag = "checked";
}
elsif ($day_flag eq "T")
{
	$last3_flag = "checked";
}
elsif ($day_flag eq "F")
{
	$last15_flag = "checked";
}
elsif ($day_flag eq "N")
{
	$lastall_flag = "checked";
}
elsif ($day_flag eq "M")
{
	$last30_flag = "checked";
}
elsif ($day_flag eq "Y")
{
	$last60_flag = "checked";
}
elsif ($day_flag eq "9")
{
	$last90_flag = "checked";
}
elsif ($day_flag eq "3")
{
	$last120_flag = "checked";
}
elsif ($day_flag eq "5")
{
	$last150_flag = "checked";
}
elsif ($day_flag eq "O")
{
	$last180_flag = "checked";
}
print "Content-Type: text/html\n\n";
print << "end_of_html";
<html>

<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Edit List Profile</title>
</head>

<body>
end_of_html
if ($mesg ne "")
{
print<<"end_of_html";
	<script language=Javascript>
	alert('$mesg');
	</script>
end_of_html
}

print<<"end_of_html";
<table cellSpacing="0" cellPadding="0" align="left" bgColor="#ffffff" border="0" id="table10">
	<tr vAlign="top">
		<td noWrap align="left">
		<table cellSpacing="0" cellPadding="0" width="900" border="0" id="table11">
			<tr>
				<td width="248" bgColor="#ffffff" rowSpan="2">
				<img src="/images/header.gif" border="0"></td>
				<td width="328" bgColor="#ffffff">&nbsp;</td>
			</tr>
			<tr>
				<td width="468">
				<table cellSpacing="0" cellPadding="0" width="100%" border="0" id="table12">
	
					<tr>
						<td align="right"><b>
						<a style="TEXT-DECORATION: none" href="/cgi-bin/logout.cgi">
						<font face="Arial" color="#509c10" size="2">Logout</font></a>&nbsp;&nbsp;&nbsp;
						<a style="TEXT-DECORATION: none" href="/cgi-bin/wss_support_form.cgi">
						<font face="Arial" color="#509c10" size="2">Customer 
						Assistance</font></a></b> 
						</td>
					</tr>
				</table>
				</td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td vAlign="top" align="left" bgColor="#999999">
		<table cellSpacing="0" cellPadding="10" width="100%" bgColor="#999999" border="0" id="table13">
			<tr>
				<td vAlign="top" align="left" bgColor="#ffffff" colSpan="10">
				<table cellSpacing="0" cellPadding="0" width="660" bgColor="#ffffff" border="0" id="table14">
					
					<tr>
						<td><font face="Arial">
						<img height="5" src="images/spacer.gif"></font></td>
					</tr>
				</table>
				</td>
			</tr>
			<tr bgColor="#509c10">
				<td>
<SCRIPT language=JavaScript>
var cntarr=new Array(500)
var aolcntarr=new Array(500)
var yahoocntarr=new Array(500)
var othercntarr=new Array(500)
var hotmailcntarr=new Array(500)

for (x=0; x < 500; x++)
{
    cntarr[x] = 0;
    aolcntarr[x] = 0;
    yahoocntarr[x] = 0;
    othercntarr[x] = 0;
    hotmailcntarr[x] = 0;
}
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
function setlistcnt(h,nonaol,aolcnt,yahoocnt,othercnt,hotmailcnt)
{
    cntarr[h] = nonaol;
    aolcntarr[h] = aolcnt;
    yahoocntarr[h] = yahoocnt;
    othercntarr[h] = othercnt;
    hotmailcntarr[h] = hotmailcnt;
}

function selectall1()
{
    refno=/domains/;
    for (var x=0; x < document.adform.length; x++)
    {
        if ((document.adform.elements[x].type=="checkbox") && (refno.test(document.adform.elements[x].name)))
        {
            document.adform.elements[x].checked = true;
        }
    }
}
function unselectall1()
{
    refno=/domains/;
    for (var x=0; x < document.adform.length; x++)
    {
        if ((document.adform.elements[x].type=="checkbox") && (refno.test(document.adform.elements[x].name)))
        {
            document.adform.elements[x].checked = false;
        }
    }
}
function selectall()
{
	refno=/list_/;
    for (var x=0; x < document.adform.length; x++)
    {
        if ((document.adform.elements[x].type=="checkbox") && (refno.test(document.adform.elements[x].name)))
        {
           	document.adform.elements[x].checked = true;
        }
    }
    document.adform.totalcnt.value = document.adform.htotal.value;
    document.adform.totalaolcnt.value = document.adform.haol.value;
    document.adform.totalyahoocnt.value = document.adform.hyahoo.value;
    document.adform.totalothercnt.value = document.adform.hother.value;
    document.adform.totalhotmailcnt.value = document.adform.hhotmail.value;
}
function unselectall()
{
	refno=/list_/;
    for (var x=0; x < document.adform.length; x++)
    {
        if ((document.adform.elements[x].type=="checkbox") && (refno.test(document.adform.elements[x].name)))
        {
            document.adform.elements[x].checked = false;
        }
    }
    document.adform.totalcnt.value = 0; 
    document.adform.totalaolcnt.value = 0;
    document.adform.totalyahoocnt.value = 0;
    document.adform.totalothercnt.value = 0;
    document.adform.totalhotmailcnt.value = 0;
}
function ctselectall()
{
    refno=/clienttype/;
    for (var x=0; x < document.adform.length; x++)
    {
        if ((document.adform.elements[x].type=="checkbox") && (refno.test(document.adform.elements[x].name)))
        {
            document.adform.elements[x].checked = true;
        }
    }
}
function ctunselectall()
{
    refno=/clienttype/;
    for (var x=0; x < document.adform.length; x++)
    {
        if ((document.adform.elements[x].type=="checkbox") && (refno.test(document.adform.elements[x].name)))
        {
            document.adform.elements[x].checked = false;
        }
    }
}
function listselected(h)
{
    var cnt1 = 0;
    var cnt2 = 0;
    var cnt3 = 0;
    var cnt4 = 0;
    var cnt5 = 0;
    cnt1 = parseInt(document.adform.totalcnt.value);
    cnt2 = parseInt(document.adform.totalaolcnt.value);
    cnt3 = parseInt(document.adform.totalyahoocnt.value);
    cnt4 = parseInt(document.adform.totalothercnt.value);
    cnt5 = parseInt(document.adform.totalhotmailcnt.value);
    for (var x=0; x < document.adform.length; x++)
    {
        if (document.adform.elements[x].type=="checkbox")
        {
            if (document.adform.elements[x].name=="list_"+h)
            {
                if (document.adform.elements[x].checked == false)
                {
                    cnt1 -= parseInt(cntarr[h]);
                    cnt2 -= parseInt(aolcntarr[h]);
                    cnt3 -= parseInt(yahoocntarr[h]);
                    cnt4 -= parseInt(othercntarr[h]);
                    cnt5 -= parseInt(hotmailcntarr[h]);
                }
                else
                {
                    cnt1 += parseInt(cntarr[h]);
                    cnt2 += parseInt(aolcntarr[h]);
                    cnt3 += parseInt(yahoocntarr[h]);
                    cnt4 += parseInt(othercntarr[h]);
                    cnt5 += parseInt(hotmailcntarr[h]);
                }
                document.adform.totalcnt.value = cnt1;
                document.adform.totalaolcnt.value = cnt2;
                document.adform.totalyahoocnt.value = cnt3;
                document.adform.totalothercnt.value = cnt4;
                document.adform.totalhotmailcnt.value = cnt5;
            }
        }
    }
}
function selectAllOptions(selStr)
{
  var selObj = document.getElementById(selStr);
  for (var i=0; i<selObj.options.length; i++) {
    selObj.options[i].selected = true;
  }
}
function chk()
{
    var refno = "list_" + document.adform.add_from.value;
	if (document.adform.add_from.value > 0)
	{
    	for (var x=0; x < document.adform.length; x++)
    	{
        	if ((document.adform.elements[x].type=="checkbox") && (refno == document.adform.elements[x].name))
        	{
				if (document.adform.elements[x].checked)
				{
					selectAllOptions('sel2');
					return true;
				}
				else
				{
					alert('The List To Add From must be selected.');
					return false;
				}
        	}
    	}
	}
	else
	{
		selectAllOptions('sel2');
		return true;
	}
}
</SCRIPT>

				<form name="adform" id="adform" method="post" action="/cgi-bin/listprofile_save.cgi" onsubmit="return chk();">
							<input type="hidden" value="$pid" name="pid">
					<table cellSpacing="0" cellPadding="0" width="100%" bgColor="#e3fad1" border="0" id="table16">
						<tr bgColor="#509c10" height="15">
							<td width="14%">&nbsp;</td>
							<td align="left" width="138" height="15">&nbsp;</td>
							<td align="left" width="61" height="15">&nbsp;</td>
							<td width="75">&nbsp;</td>
							<td width="68">&nbsp;</td>
							<td width="63">&nbsp;</td>
							<td width="103">&nbsp;</td>
							<td width="118">&nbsp;</td>
							<td width="131">&nbsp;</td>
						</tr>
						<tr bgColor="#e3fad1">
							<td align="middle">
							<img height="3" src="images/spacer.gif" width="3" colspan="7"></td>
						</tr>
						<tr>
							<td><font face="Arial" color="#509c10" size="2"><b>&nbsp;&nbsp;&nbsp;&nbsp; </b></font></td>
						</tr>
						<tr>
							<td vAlign="center" align="left" colSpan="9">
									<font face="Arial" size="2" color="#509C10">
									<b>Network:</b>&nbsp;$company</font></td>
						</tr>
						</tr>
						</tr>
									<tr>
							<td vAlign="center" align="left" colSpan="9">
									&nbsp;</td>
						</tr>
						<tr>
							<td vAlign="center" align="left" colSpan=9>
							<font face="Arial" size="2" color="#509C10">
									<b>3rd Party Mailer:</b></font>
													<select name="third_party_id">
													<option value="0" selected>None</option>
end_of_html
$sql="select third_party_id,mailer_name from third_party_defaults order by mailer_name";
$sth = $dbhq->prepare($sql);
$sth->execute();
my $tid;
my $tname;
while (($tid,$tname) = $sth->fetchrow_array())
{
	if ($tid == $ptid)
	{
		print "<option value=$tid selected>$tname</option>";
	}
	else
	{
		print "<option value=$tid>$tname</option>";
	}
}
print<<"end_of_html";
													</select></td>
						</tr>
									<tr>
							<td vAlign="center" align="left" colSpan="9">
									&nbsp;</td>
							<tr>
							<td vAlign="center" align="left" colSpan="9">
							<font face="Arial" size="2" color="#509C10">
									<b>List Profile Name:</b>&nbsp;<input type=text size=30 name=profile_name value='$profile_name'></font></td>
						</tr>
									<tr>
							<td vAlign="center" align="left" colSpan="9">
									&nbsp;</td></tr>
							<tr>
							<td vAlign="center" align="left" colSpan="9">
							<font face="Arial" size="2" color="#509C10">
									<b>Unique ID#:</b>&nbsp;<input type=text size=30 name=unique_id value='$unique_id'></font></td>
						</tr>
									<tr>
							<td vAlign="center" align="left" colSpan="9">
									&nbsp;</td></tr>
							<tr>
							<td vAlign="center" align="left" colSpan="9">
							<font face="Arial" size="2" color="#509C10">
									<b>Percent Sub(0-100):</b>&nbsp;<input type=text size=4 maxlength=3 name=percent_sub value='$percent_sub'></font></td>
						</tr>
										<tr>
							<td vAlign="center" align="left" colSpan="9">&nbsp;</td>
						</tr>
						<tr>
							<td vAlign="center" align="left" colSpan="9"><font face="Arial" size="2" color="#509C10">
									<b>Note: Do not select AOL for this network. </b>
							(pulled from a notes field in setup)</font></td>
						</tr>
end_of_html
if ($client_id != 276)
{
print<<"end_of_html";
						<tr>
							<td vAlign="center" align="left" colSpan="9">
									<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
									Send To:<input type="radio" value="N" name="clast60" $lastall_flag>All&nbsp;&nbsp;&nbsp;<input type="radio" value="T" name="clast60" $last3_flag>Last 3 Days&nbsp;&nbsp;&nbsp;<input type="radio" value="7" name="clast60" $last7_flag>Last 7 Days&nbsp;&nbsp;&nbsp;<input type="radio" value="F" name="clast60" $last15_flag>Last 15 Days&nbsp;&nbsp;&nbsp;<input type="radio" value="M" name="clast60" $last30_flag>Last 30 Days&nbsp;&nbsp;&nbsp;<input type="radio" value="Y" name="clast60" $last60_flag>Last 60 Days&nbsp;&nbsp;&nbsp;<input type="radio" value="9" name="clast60" $last90_flag>Last 90 Days&nbsp;&nbsp;&nbsp;<input type="radio" value="3" name="clast60" $last120_flag>Last 120 Days&nbsp;&nbsp;&nbsp;<input type="radio" value="5" name="clast60" $last150_flag>Last 150 Days&nbsp;&nbsp;&nbsp;<input type="radio" value="O" name="clast60" $last180_flag>Last 180 Days</font></td>
						</tr>
end_of_html
}
else
{
	if ($dupes_flag eq '')
	{
		$dupes_flag="Duplicates Only";
	}
 	my $sthc = $dbhu->column_info(undef, undef, 'list_profile', '%');
    while ( my $col_info = $sthc->fetchrow_hashref)
    {
        if (($col_info->{'TYPE_NAME'} eq 'ENUM') and ($col_info->{'COLUMN_NAME'} eq "dupes_flag"))
        {
			my $str=join(',',@{$col_info->{'mysql_values'}});
			my @a=split(',',$str);
			print "<tr><td vAlign=center align=left colSpan=9><font face=\"verdana,arial,helvetica,sans serif\" color=\"#509c10\" size=\"2\">Send To:";
			my $i=0;
			while ($i <= $#a)
			{
				if ($dupes_flag eq $a[$i])
				{
					print "<input checked type=radio value=\"$a[$i]\" name=dupes_flag>$a[$i]&nbsp;&nbsp;";
				}
				else
				{
					print "<input type=radio value=\"$a[$i]\" name=dupes_flag>$a[$i]&nbsp;&nbsp;";
				}
				$i++;
			}
			print "</td></tr>";
		}
	}  
	$sthc->finish();
	print "<tr><td colspan=9><table>\n";
	print "<tr><td vAlign=\"center\" align=left><font face=\"verdana,arial,helvetica,sans serif\" color=\"#509c10\" size=\"2\">Start Day: </td><td><input style=\"BACKGROUND-COLOR: #ffffa0\" value=\"$start_day\" name=\"start_day\" size=5></td><td><font face=\"verdana,arial,helvetica,sans serif\" color=\"#509c10\" size=\"2\">End Day:  </td><td><input style=\"BACKGROUND-COLOR: #ffffa0\" value=\"$end_day\" name=\"end_day\" size=5></font></td><td><i>Note: If you enter a Start and End Date it will override this setting</i></td></tr>";
	print "<tr><td vAlign=\"center\" align=left><font face=\"verdana,arial,helvetica,sans serif\" color=\"#509c10\" size=\"2\">Start Date: </td><td><input style=\"BACKGROUND-COLOR: #ffffa0\" value=\"$start_date\" name=\"start_date\" size=10></td><td><font face=\"verdana,arial,helvetica,sans serif\" color=\"#509c10\" size=\"2\">End Date:  </td><td><input style=\"BACKGROUND-COLOR: #ffffa0\" value=\"$end_date\" name=\"end_date\" size=10></font></td></tr>";
	print "<tr><td vAlign=\"center\"><font face=\"verdana,arial,helvetica,sans serif\" color=\"#509c10\" size=\"2\">Openers Start: </td><td><input style=\"BACKGROUND-COLOR: #ffffa0\" value=\"$opener_start\" name=\"opener_start\" size=5></td><td><font face=\"verdana,arial,helvetica,sans serif\" color=\"#509c10\" size=\"2\">Openers End:  </td><td><input style=\"BACKGROUND-COLOR: #ffffa0\" value=\"$opener_end\" name=\"opener_end\" size=5></font></td></tr>";
	print "<tr><td vAlign=\"center\"><font face=\"verdana,arial,helvetica,sans serif\" color=\"#509c10\" size=\"2\">Clickers Start: </td><td><input style=\"BACKGROUND-COLOR: #ffffa0\" value=\"$clicker_start\" name=\"clicker_start\" size=5></td><td><font face=\"verdana,arial,helvetica,sans serif\" color=\"#509c10\" size=\"2\">Clickers End:  </td><td><input style=\"BACKGROUND-COLOR: #ffffa0\" value=\"$clicker_end\" name=\"clicker_end\" size=5></font></td></tr>";
	print "</td></tr></table>\n";
	print "<tr><td>&nbsp;</td></tr>";
 	my $sthc = $dbhu->column_info(undef, undef, 'ListProfileClientType', 'client_type');
	foreach my $col_info ($sthc->fetchrow_hashref)
	{  
		if ($col_info->{'TYPE_NAME'} eq 'ENUM')  
		{    
			my $str=join(',',@{$col_info->{'mysql_values'}});
			my @a=split(',',$str);
			print "<tr><td vAlign=center align=left colSpan=9><font face=\"verdana,arial,helvetica,sans serif\" color=\"#509c10\" size=\"2\">Client Types: <a href=\"javascript:ctselectall();\">Select All</a>&nbsp;&nbsp;<a href=\"javascript:ctunselectall();\">Unselect All</a>&nbsp;&nbsp; ";
			my $i=0;
			while ($i <= $#a)
			{
				$sql="select client_type from ListProfileClientType where profile_id=? and client_type=?";
				my $s1=$dbhu->prepare($sql);
				$s1->execute($pid,$a[$i]);
				my $tid;
				if (($tid)=$s1->fetchrow_array())
				{
					print "<input checked type=checkbox value=\"$a[$i]\" name=clienttype>$a[$i]&nbsp;&nbsp;";
				}
				else
				{
					print "<input type=checkbox value=\"$a[$i]\" name=clienttype>$a[$i]&nbsp;&nbsp;";
				}
				$i++;
			}
			print "</td></tr>";
		}
	}  
	$sthc->finish();
	print<<"end_of_html";
	<tr><td>Available Clients</td><td></td><td>Selected Clients</td></tr>
	<tr>
		<td>
			<select name=sel1 id=sel1 size="10" multiple="multiple">
end_of_html
my $fname;
my $client;
$sql="select user_id,first_name from user where user_id not in (select client_id from ListProfileClient where profile_id=$pid) and status='A' order by first_name";
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
			 onclick="moveOptions(adform.sel1, adform.sel2);" /><br />
			<input type="button" value="&lt;--"
			 onclick="moveOptions(adform.sel2, adform.sel1);" />
		</td>
		<td>
			<select name=sel2 id=sel2 size="10" multiple="multiple">
end_of_html
$sql="select client_id,first_name from ListProfileClient,user u where profile_id=? and ListProfileClient.client_id=u.user_id order by first_name";
$sth=$dbhu->prepare($sql);
$sth->execute($pid);
while (($client,$fname)=$sth->fetchrow_array())
{
	print "<option value=\"$client\">$fname</option>";
}
$sth->finish();
print<<"end_of_html";
			</select>
		</td>
	</tr>
end_of_html
	print "<tr><td>&nbsp;</td></tr>";
}
if ($open_click_ignore eq "Y")
{
print<<"end_of_html";
                        <tr>
                            <td vAlign="center" align="left" colSpan="9">
                            <font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">Send to All Openers/Clickers:<input type="radio" value="Y" name="open_click_ignore" checked>Yes&nbsp;&nbsp;&nbsp;<input type="radio" value="N" name="open_click_ignore" >No</font></td>
                        </tr>
end_of_html
}
else
{
print<<"end_of_html";
                        <tr>
                            <td vAlign="center" align="left" colSpan="9">
                            <font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">Send to All Openers/Clickers:<input type="radio" value="Y" name="open_click_ignore">Yes&nbsp;&nbsp;&nbsp;<input type="radio" value="N" name="open_click_ignore" checked>No</font></td>
                        </tr>
end_of_html
}
print<<"end_of_html";
						<tr>
							<td vAlign="center" align="left" colSpan="9">
									<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
end_of_html
if ($profile_type eq 'CHUNK')
{
print<<"end_of_html";
									Domains: 
end_of_html
	$sql="select domain_id,domain_name from email_domains where chunked=1 order by domain_name";
	my $sthq = $dbhq->prepare($sql);
	$sthq->execute();
	my $did;
	my $dname;
	my $cnt;
	while (($did,$dname) = $sthq->fetchrow_array())
	{
		$sql="select domain_id from profile_chunk_domain where profile_id=? and domain_id=?";
		my $sthq1 = $dbhq->prepare($sql);
		$sthq1->execute($pid,$did);
		my $temp_id;
		if (($temp_id) = $sthq1->fetchrow_array())
		{
			print "<input type=checkbox name=chunkdomain value=$did checked>$dname&nbsp;&nbsp;\n";
		}
		else
		{
			print "<input type=checkbox name=chunkdomain value=$did>$dname&nbsp;&nbsp;\n";
		}
		$cnt++;
		if ($cnt >= 7)
		{
			print "</td></tr><tr><td><font face=\"verdana,arial,helvetica,sans serif\" color=\"#509c10\" size=2>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n";
			$cnt=0;
		} 
	}
	$sthq->finish();
print<<"end_of_html";
									&nbsp;&nbsp;</font></td>
						</tr><tr><td>&nbsp;</td></tr>
end_of_html
}
else
{
print<<"end_of_html";
									Send To: <a href="javascript:selectall1();">Select All</a>&nbsp;&nbsp;<a href="javascript:unselectall1();">Unselect All</a>&nbsp;&nbsp; 
end_of_html
#if ($profile_type eq "NEWSLETTER")
#{
	$sql="select class_id,class_name from email_class where status='Active' order by class_name";
	$sth=$dbhq->prepare($sql);
	$sth->execute();
	my $class_id;
	my $class_name;
	while (($class_id,$class_name)=$sth->fetchrow_array())
	{
		$sql="select domain_id from list_profile_domain where profile_id=? and domain_id=?";
		my $s1=$dbhu->prepare($sql);
		$s1->execute($pid,$class_id);
		my $tid;
		if (($tid)=$s1->fetchrow_array())
		{			
			print "<input type=checkbox checked value=$class_id name=domains>${class_name}&nbsp;";
		}
		else
		{
			print "<input type=checkbox value=$class_id name=domains>${class_name}&nbsp;";
		}
		$s1->finish();
	}
	$sth->finish();
#}
#else
#{
#if ($aol_flag eq "Y")
#{
#	print "<input type=\"checkbox\" value=Y name=aolflag checked>AOL&nbsp;";
#}
#else
#{
#	print "<input type=\"checkbox\" value=Y name=aolflag>AOL&nbsp;";
#}
#if ($yahoo_flag eq "Y")
#{
#	print "<input type=\"checkbox\" value=Y name=yahooflag checked>Yahoo&nbsp;";
#}
#else
#{
#	print "<input type=\"checkbox\" value=Y name=yahooflag>Yahoo&nbsp;";
#}
#if ($other_flag eq "Y")
#{
#	print "<input type=\"checkbox\" value=Y name=otherflag checked>Other Domains&nbsp;";
#}
#else
#{
#	print "<input type=\"checkbox\" value=Y name=otherflag>Other Domains&nbsp;";
#}
#if ($hotmail_flag eq "Y")
#{
#	print "<input type=\"checkbox\" value=Y name=hotmailflag checked>Hotmail/MSN&nbsp;";
#}
#else
#{
#	print "<input type=\"checkbox\" value=Y name=hotmailflag>Hotmail/MSN&nbsp;";
#}
#if ($yahoo_flag eq "M")
#{
#	print "<input type=\"checkbox\" value=M name=yahooflag1 checked>Yahoo Last 30/Openers&nbsp;";
#}
#else
#{
#	print "<input type=\"checkbox\" value=M name=yahooflag1>Yahoo Last 30/Openers&nbsp;";
#}
#if ($comcast_flag eq "Y")
#{
#	print "<input type=\"checkbox\" value=Y name=comcastflag checked>Comcast&nbsp;";
#}
#else
#{
#	print "<input type=\"checkbox\" value=Y name=comcastflag>Comcast&nbsp;";
#}
#print<<"end_of_html";
#									&nbsp;&nbsp;</font></td>
#						</tr>
#end_of_html
#}
}
if ($send_dmseeds eq "Y")
{	
	print "<tr><td vAlign=center align=left colspan=9><font face=\"verdana,arial,helvetica,sans serif\" color=\"#509c10\" size=\"2\">Send DM Seeds: <input type=radio value=\"Y\" checked name=send_dmseeds>Yes&nbsp;&nbsp;&nbsp;<input type=radio value=\"N\" name=send_dmseeds>No&nbsp;&nbsp;&nbsp;# of Sends<select name=dmseed_cnt>";
}
else
{
	print "<tr><td vAlign=center align=left colspan=9><font face=\"verdana,arial,helvetica,sans serif\" color=\"#509c10\" size=\"2\">Send DM Seeds: <input type=radio value=\"Y\" name=send_dmseeds>Yes&nbsp;&nbsp;&nbsp;<input type=radio value=\"N\" checked name=send_dmseeds>No&nbsp;&nbsp;&nbsp;# of Sends<select name=dmseed_cnt>";
}
my $i=1;
while ($i <= 100)
{
	if ($i == $dmseed_cnt)
	{
		print "<option selected value=$i>$i</option>\n";
	}
	else
	{
		print "<option value=$i>$i</option>\n";
	}		
	$i++;
}
print "</select></td></tr>\n";
print<<"end_of_html";
<tr><td vAlign="center" align="left" colspan=9><font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">Max E-mails To Send (-1 means all): <input style="BACKGROUND-COLOR: #ffffa0" value="$max_emails" name="max_emails"></font></td>
						</tr>
end_of_html
if ($profile_type eq "NEWSLETTER")
{
	print "<tr><td vAlign=\"center\" align=\"left\" colSpan=\"9\"><font face=\"verdana,arial,helvetica,sans serif\" color=\"#509c10\" size=2>\n";
	print "NewsLetter: <select name=nl_id>\n";
	$sql="select nl_id,nl_name from newsletter where nl_status='A' order by nl_name";
	my $s1=$dbhq->prepare($sql);
	$s1->execute();
	my $nl_id;
	my $nl_name;
	while (($nl_id,$nl_name)=$s1->fetchrow_array())
	{
		if ($nl_id == $old_nl_id)
		{
			print "<option value=$nl_id selected>$nl_name</option>\n";
		}
		else
		{
			print "<option value=$nl_id>$nl_name</option>\n";
		}
	}
	$s1->finish();
	print "</select></td></tr>\n";
	print "<tr><td vAlign=\"center\" align=\"left\" colSpan=\"9\"><font face=\"verdana,arial,helvetica,sans serif\" color=\"#509c10\" size=2>\n";
	if ($old_nl_send eq "CONFIRMED")
	{
		$c1_str="checked";
		$c2_str="";
		$c3_str="";
	}
	elsif ($old_nl_send eq "UNCONFIRMED")
	{
		$c1_str="";
		$c2_str="checked";
		$c3_str="";
	}
	elsif ($old_nl_send eq "ALL")
	{
		$c1_str="";
		$c2_str="";
		$c3_str="checked";
	}
	print "Send To: <input type=radio value=\"CONFIRMED\" name=nl_send $c1_str>Confirmed&nbsp;&nbsp;&nbsp;<input type=radio value=\"UNCONFIRMED\" name=nl_send $c2_str>Unconfirmed&nbsp;&nbsp;&nbsp;<input type=radio value=\"ALL\" name=nl_send $c3_str>ALL</td></tr>\n";
	print "<tr><td colspan=9><font face=\"verdana,arial,helvetica,sans serif\" color=\"#509c10\" size=2>Update All Profiles: <input type=radio value=Y name=nl_update checked>Yes&nbsp;&nbsp;<input type=radio value=N name=nl_update>No</td></tr>\n";
print<<"end_of_html";
						<tr>
							<td vAlign="center" align="left" colSpan="9">
									&nbsp;</td>
						</tr>
end_of_html
}
if (($profile_type ne "CHUNK") && ($profile_type ne "NEWSLETTER"))
{
print<<"end_of_html";
<tr><td vAlign="center" align="left" colspan=9><font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">Loop <input type=radio name=loop_flag value="Y" $loop_yes>Yes&nbsp;&nbsp;<input type=radio name=loop_flag value="N" $loop_no>No</font></td>
						</tr>
<tr><td vAlign="center" align="left" colspan=9><font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">List to Add From <select name=add_from>
<option value=0 selected>None</option>
end_of_html
my $qsel;
$qsel="select list_id,list_name from list where status='A' and user_id=$client_id and list_type != 'CHUNK' order by list_name";
my $qsth = $dbhq->prepare($qsel) ;
$qsth->execute();
my $tlid;
my $tname;
while (($tlid,$tname) = $qsth->fetchrow_array())
{
	if ($tlid == $list_to_add_from)
	{
		print "<option selected value=$tlid>$tname</option>\n";
	}
	else
	{
		print "<option value=$tlid>$tname</option>\n";
	}
}
$qsth->finish();
print<<"end_of_html";
</select>
</td></tr>
end_of_html
}
if ($profile_type ne "NEWSLETTER")
{
print<<"end_of_html";
<tr><td vAlign="center" align="left" colspan=9><font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">Amount to Add(zero for none): <input style="BACKGROUND-COLOR: #ffffa0" value="$amount_to_add" name="amount_to_add"></font></td>
						</tr>
<tr><td vAlign="center" align="left" colspan=9><font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">Amount of Clean AOL to Add(zero for none): <input style="BACKGROUND-COLOR: #ffffa0" value="$clean_add" name="clean_add"></font></td>
						</tr>
<tr><td vAlign="center" align="left" colspan=9><font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">Randomize Lists Pulled&nbsp;&nbsp;
end_of_html
if ($randomize_records eq "Y")
{
	print "Yes&nbsp;<input type=radio name=randomize_records checked value=Y>&nbsp;&nbsp;No&nbsp;<input type=radio name=randomize_records value=N></td></tr>";
}
else
{
	print "Yes&nbsp;<input type=radio name=randomize_records value=Y>&nbsp;&nbsp;No&nbsp;<input type=radio name=randomize_records checked value=N></td></tr>";
}
print<<"end_of_html";
<tr><td vAlign="center" align="left" colspan=9><font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">Master profile&nbsp;&nbsp;
end_of_html
if ($master_flag eq "Y")
{
	print "Yes&nbsp;<input type=radio name=master_flag checked value=Y>&nbsp;&nbsp;No&nbsp;<input type=radio name=master_flag value=N></td></tr>";
}
else
{
	print "Yes&nbsp;<input type=radio name=master_flag value=Y>&nbsp;&nbsp;No&nbsp;<input type=radio name=master_flag checked value=N></td></tr>";
}
print<<"end_of_html";
<tr><td vAlign="center" align="left" colspan=9><font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">Add Newest Month To Profile&nbsp;&nbsp;
end_of_html
if ($add_new_month eq "Y")
{
	print "Yes&nbsp;<input type=radio name=add_new_month checked value=Y>&nbsp;&nbsp;No&nbsp;<input type=radio name=add_new_month value=N></td></tr>";
}
else
{
	print "Yes&nbsp;<input type=radio name=add_new_month value=Y>&nbsp;&nbsp;No&nbsp;<input type=radio name=add_new_month checked value=N></td></tr>";
}
}
if ($profile_type eq 'CHUNK')
{
	print "<tr><td vAlign=\"center\" align=\"left\" colspan=9><font face=\"verdana,arial,helvetica,sans serif\" color=\"#509c10\" size=2>Add Frequency: <select name=add_freq>\n";
	if ($add_freq eq 'DAILY')
	{
		print "<option value=DAILY selected>DAILY</option>\n";
		print "<option value=WEEKLY>WEEKLY</option>\n";
	}
	else
	{
		print "<option value=DAILY>DAILY</option>\n";
		print "<option value=WEEKLY selected>WEEKLY</option>\n";
	}
	print "</td></tr>\n";	
}
print<<"end_of_html";
						<tr>
							<td vAlign="center" align="left" colSpan="9">
									&nbsp;</td>
						</tr>
						<tr>
							<td vAlign="center" align="left" colSpan="9">
									&nbsp;</td>
						</tr>
						<tr>
							<td vAlign="center" align="left" colSpan="9">
					<input onclick="selectall()" type="button" value="Select All" name="SelectAll2"><input onclick="unselectall()" type="button" value="UnSelect All" name="UnSelectAll0"></td>
						</tr></tr></tr>
						</tr>
						<tr>
							<td align="left">
							<b><font face="Arial" size="2" color="#509C10">Lists</font></b></td>
							<td width="138">
							<b><font face="Arial" color="#509c10" size="2" align="middle">
							Sub </font></b></td>
							<td width="75">
							<b><font face="Arial" color="#509c10" size="2" align="middle">
							AOL</font></b></td>
							<td width="68">
							<b><font face="Arial" color="#509c10" size="2" align="middle">
							Yahoo</font></b></td>
							<td width="63">
							<b><font face="Arial" color="#509c10" size="2" align="middle">
							Other </font></b></td>
							<td width="103"><font face="Arial">&nbsp;</font><b><font face="Arial" color="#509c10" size="2" align="middle">Hotmail/MSN</font></b></td>
							<td width="118">&nbsp;</td>
						</tr>
end_of_html
if ($profile_type eq "CHUNK")
{
	$sql = "select list_id,list_name,member_cnt,aol_cnt,yahoo_cnt,hotmail_cnt+msn_cnt,aol_cnt_p,server from list,server_config where status='A' and user_id=$client_id and list_type='CHUNK' and list.server_id=server_config.id order by list_name";
}
elsif ($profile_type eq "NEWSLETTER")
{
	$sql = "select list_id,list_name,member_cnt,aol_cnt,yahoo_cnt,hotmail_cnt+msn_cnt,aol_cnt_p,'' from list where status='A' and user_id=$client_id and list_type = 'UNIQUE' order by list_name";
}
else
{
	$sql = "select list_id,list_name,member_cnt,aol_cnt,yahoo_cnt,hotmail_cnt+msn_cnt,aol_cnt_p,'' from list where status='A' and user_id=$client_id and list_type != 'CHUNK' and list_type != 'UNIQUE' order by list_name";
}
$sth = $dbhq->prepare($sql);
$sth->execute();
$alllists = 0;
$aolalllists = 0;
$otheralllists = 0;
$hotmailalllists = 0;
$yahooalllists = 0;
my $other_cnt;
my $include_flag;
while (($list_id,$list_name,$member_cnt,$aol_cnt,$yahoo_cnt,$hotmail_cnt,$aol_cnt_p,$server_name) = $sth->fetchrow_array())
{
	if ($profile_type eq "CHUNK")
	{
		$list_name=$list_name." (".$server_name.")";
	}
	$other_cnt = $member_cnt - $aol_cnt - $yahoo_cnt - $hotmail_cnt;
	$alllists = $alllists + $member_cnt;
	$aolalllists = $aolalllists + $aol_cnt;
	$yahooalllists = $yahooalllists + $yahoo_cnt;
	$otheralllists = $otheralllists + $other_cnt;
	$hotmailalllists = $hotmailalllists + $hotmail_cnt;
    $sql = "select count(*) from list_profile_list where profile_id=$pid and list_id=$list_id";
    my $sth1 = $dbhq->prepare($sql);
    $sth1->execute();
    ($include_flag) = $sth1->fetchrow_array();
	$sth1->finish();
	$chklist_str = "";
	if ($include_flag > 0)
	{
		$chklist_str = "checked";
		$totalall = $totalall + $member_cnt;
		$totalaol = $totalaol + $aol_cnt;
		$totalyahoo = $totalyahoo + $yahoo_cnt;
		$totalother = $totalother + $other_cnt;
		$totalhotmail = $totalhotmail + $hotmail_cnt;
	}
    print "<script language=JavaScript>setlistcnt($list_id,$member_cnt,$aol_cnt,$yahoo_cnt,$other_cnt,$hotmail_cnt);</script>\n";
	print "<tr><td align=\"left\"><input type=\"checkbox\" name=\"list_$list_id\" value=Y $chklist_str onClick=\"listselected($list_id);\"><font face=Arial size=2 color=\"#509C10\">$list_name</td><td width=138>$member_cnt</font></td><td><font face=Arial size=2 color=\"#509C10\">$aol_cnt - (P - $aol_cnt_p)</font></td><td><font face=Arial size=2 color=\"#509C10\">$yahoo_cnt</font></td><td><font face=Arial size=2 color=\"#509C10\">$other_cnt</font></td><td><font face=Arial size=2 color=\"#509C10\">$hotmail_cnt</font></td></tr>\n";
}
$sth->finish();
print<<"end_of_html";
						<tr>
							<td align="middle">&nbsp;</td>
							<td width="138">
											<input maxLength="255" size="7" value="$totalall" name="totalcnt"></td>
							<td width="75">
							<input maxLength="255" size="7" value="$totalaol" name="totalaolcnt"></td>
							<td width="68">
							<input maxLength="255" size="7" value="$totalyahoo" name="totalyahoocnt"></td>
							<td width="63">
							<input maxLength="255" size="7" value="$totalother" name="totalothercnt"></td>
							<td width="103"><font face="Arial">&nbsp;</font><input maxLength="255" size="7" value="$totalhotmail" name="totalhotmailcnt"></td>
							<td width="118">
							<input onclick="selectall()" type="button" value="Calculate" name="Calc"></td>
						</tr>
						<tr>
							<td vAlign="center" align="left" colSpan="9">&nbsp;</td>
						</tr>
						<tr>
							<td vAlign="center" align="left" colSpan="9">&nbsp;</td>
						</tr>
						<tr>
							<td vAlign="center" align="left" colSpan="9">
									&nbsp;</td>
						</tr>
					</table>
					&nbsp;&nbsp;</P>
				</td>
			</tr>
			<tr bgColor="white">
				<td height="65">
				<table cellSpacing="0" cellPadding="7" width="100%" bgColor="white" border="0" id="table18">
					<tr>
						<td align="middle">
						<a href="/cgi-bin/mainmenu.cgi">
						<img src="/images/cancel.gif" border="0"></a>
						<input type="image" src="/images/save.gif" border="0" name="I1"></a></td>
					</tr>
				</table>
				</td>
			</tr>
		</table>
		</td>
	</tr>
</table>
<input type=hidden name=htotal value=$alllists>
<input type=hidden name=haol value=$aolalllists>
<input type=hidden name=hyahoo value=$yahooalllists>
<input type=hidden name=hother value=$otheralllists>
<input type=hidden name=hhotmail value=$hotmailalllists>
</form>

</body>

</html>
end_of_html

# exit function
$util->clean_up();
exit(0);
