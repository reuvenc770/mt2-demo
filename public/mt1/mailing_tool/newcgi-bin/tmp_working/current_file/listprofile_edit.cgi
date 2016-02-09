#!/usr/bin/perl
# *****************************************************************************************
# listprofile_edit.cgi
#
# this page is used to edit a List Profile 
#
# History
# Jim Sobeck, 5/31/05, Creation
# Jim Sobeck, 01/17/2006, Added 3rd party mailer logic
#*****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $sql;
my $dbh;
my $chklist_str;
my $errmsg;
my $user_id;
my $list_name;
my %checkit = ( 'Y' => 'CHECKED', 'N' => '' );
my ($list_id,$list_name,$member_cnt,$aol_cnt,$yahoo_cnt,$hotmail_cnt);
my $images = $util->get_images_url;
my $pid = $query->param('pid');
my $mesg= $query->param('mesg');
my ($company,$profile_name,$day_flag,$aol_flag,$yahoo_flag,$other_flag,$hotmail_flag);
my $last7_flag = "";
my $lastall_flag = "";
my $last30_flag = "";
my $last60_flag = "";
my $last90_flag = "";
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
my $list_to_add_from;
my $amount_to_add;

# connect to the util database

$util->db_connect();
$dbh = $util->get_dbh;

# check for login

$user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}
$sql = "select company,profile_name,day_flag,aol_flag,yahoo_flag,other_flag,hotmail_flag,client_id,max_emails,loop_flag,third_party_id,list_to_add_from,amount_to_add,unique_id from list_profile,user where profile_id=$pid and list_profile.client_id=user.user_id";
$sth = $dbh->prepare($sql) ;
$sth->execute();
($company,$profile_name,$day_flag,$aol_flag,$yahoo_flag,$other_flag,$hotmail_flag,$client_id,$max_emails,$loop_flag,$ptid,$list_to_add_from,$amount_to_add,$unique_id) = $sth->fetchrow_array();
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
function setlistcnt(h,nonaol,aolcnt,yahoocnt,othercnt,hotmailcnt)
{
    cntarr[h] = nonaol;
    aolcntarr[h] = aolcnt;
    yahoocntarr[h] = yahoocnt;
    othercntarr[h] = othercnt;
    hotmailcntarr[h] = hotmailcnt;
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
		return true;
	}
}
</SCRIPT>

				<form name="adform" method="post" action="/cgi-bin/listprofile_save.cgi" onsubmit="return chk();">
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
$sth = $dbh->prepare($sql);
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
									&nbsp;</td>
							<tr>
							<td vAlign="center" align="left" colSpan="9">
							<font face="Arial" size="2" color="#509C10">
									<b>Unique ID#:</b>&nbsp;<input type=text size=30 name=unique_id value='$unique_id'></font></td>
						</tr>
										<tr>
							<td vAlign="center" align="left" colSpan="9">&nbsp;</td>
						</tr>
						<tr>
							<td vAlign="center" align="left" colSpan="9"><font face="Arial" size="2" color="#509C10">
									<b>Note: Do not select AOL for this network. </b>
							(pulled from a notes field in setup)</font></td>
						</tr>
						<tr>
							<td vAlign="center" align="left" colSpan="9">
									<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
									Send To:<input type="radio" value="N" name="clast60" $lastall_flag>All&nbsp;&nbsp;&nbsp;<input type="radio" value="7" name="clast60" $last7_flag>Last 7 Days&nbsp;&nbsp;&nbsp;<input type="radio" value="M" name="clast60" $last30_flag>Last 30 Days&nbsp;&nbsp;&nbsp;<input type="radio" value="Y" name="clast60" $last60_flag>Last 60 Days&nbsp;&nbsp;&nbsp;<input type="radio" value="9" name="clast60" $last90_flag>Last 90 Days</font></td>
						</tr>
						<tr>
							<td vAlign="center" align="left" colSpan="9">
									<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
									Send To: 
end_of_html
if ($aol_flag eq "Y")
{
	print "<input type=\"checkbox\" value=Y name=aolflag checked>AOL&nbsp;";
}
else
{
	print "<input type=\"checkbox\" value=Y name=aolflag>AOL&nbsp;";
}
if ($yahoo_flag eq "Y")
{
	print "<input type=\"checkbox\" value=Y name=yahooflag checked>Yahoo&nbsp;";
}
else
{
	print "<input type=\"checkbox\" value=Y name=yahooflag>Yahoo&nbsp;";
}
if ($other_flag eq "Y")
{
	print "<input type=\"checkbox\" value=Y name=otherflag checked>Other Domains&nbsp;";
}
else
{
	print "<input type=\"checkbox\" value=Y name=otherflag>Other Domains&nbsp;";
}
if ($hotmail_flag eq "Y")
{
	print "<input type=\"checkbox\" value=Y name=hotmailflag checked>Hotmail/MSN&nbsp;";
}
else
{
	print "<input type=\"checkbox\" value=Y name=hotmailflag>Hotmail/MSN&nbsp;";
}
if ($yahoo_flag eq "M")
{
	print "<input type=\"checkbox\" value=M name=yahooflag1 checked>Yahoo Last 30/Openers&nbsp;";
}
else
{
	print "<input type=\"checkbox\" value=M name=yahooflag1>Yahoo Last 30/Openers&nbsp;";
}
print<<"end_of_html";
									&nbsp;&nbsp;</font></td>
						</tr>
<tr><td vAlign="center" align="left" colspan=9><font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">Max E-mails To Send (-1 means all): <input style="BACKGROUND-COLOR: #ffffa0" value="$max_emails" name="max_emails"></font></td>
						</tr>
<tr><td vAlign="ceter" align="left" colspan=9><font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">Loop <input type=radio name=loop_flag value="Y" $loop_yes>Yes&nbsp;&nbsp;<input type=radio name=loop_flag value="N" $loop_no>No</font></td>
						</tr>
<tr><td vAlign="center" align="left" colspan=9><font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">List to Add From <select name=add_from>
<option value=0 selected>None</option>
end_of_html
my $qsel="select list_id,list_name from list where status='A' and user_id=$client_id order by list_name";
my $qsth = $dbh->prepare($qsel) ;
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
<tr><td vAlign="center" align="left" colspan=9><font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">Amount to Add(zero for none): <input style="BACKGROUND-COLOR: #ffffa0" value="$amount_to_add" name="amount_to_add"></font></td>
						</tr>
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
$sql = "select list_id,list_name,member_cnt,aol_cnt,yahoo_cnt,hotmail_cnt+msn_cnt from list where status='A' and user_id=$client_id order by list_name";
$sth = $dbh->prepare($sql);
$sth->execute();
$alllists = 0;
$aolalllists = 0;
$otheralllists = 0;
$hotmailalllists = 0;
$yahooalllists = 0;
my $other_cnt;
my $include_flag;
while (($list_id,$list_name,$member_cnt,$aol_cnt,$yahoo_cnt,$hotmail_cnt) = $sth->fetchrow_array())
{
	$other_cnt = $member_cnt - $aol_cnt - $yahoo_cnt - $hotmail_cnt;
	$alllists = $alllists + $member_cnt;
	$aolalllists = $aolalllists + $aol_cnt;
	$yahooalllists = $yahooalllists + $yahoo_cnt;
	$otheralllists = $otheralllists + $other_cnt;
	$hotmailalllists = $hotmailalllists + $hotmail_cnt;
    $sql = "select count(*) from list_profile_list where profile_id=$pid and list_id=$list_id";
    my $sth1 = $dbh->prepare($sql);
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
	print "<tr><td align=\"left\"><input type=\"checkbox\" name=\"list_$list_id\" value=Y $chklist_str onClick=\"listselected($list_id);\"><font face=Arial size=2 color=\"#509C10\">$list_name</td><td width=138>$member_cnt</font></td><td><font face=Arial size=2 color=\"#509C10\">$aol_cnt</font></td><td><font face=Arial size=2 color=\"#509C10\">$yahoo_cnt</font></td><td><font face=Arial size=2 color=\"#509C10\">$other_cnt</font></td><td><font face=Arial size=2 color=\"#509C10\">$hotmail_cnt</font></td></tr>\n";
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
