#!/usr/bin/perl

# *****************************************************************************************
# dataexport_list.cgi
#
# this page displays the list of Data Exports and lets the user edit / add
#
# History
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;
use Net::FTP;

# get some objects to use later

my $pms = util->new;
my $query = CGI->new;
my $sth;
my $sql;
my $dbh;
my $errmsg;
my $user_id;
my $bgcolor;
my $reccnt=0;
my $images = $pms->get_images_url;
my $alt_light_table_bg = $pms->get_alt_light_table_bg;
my $light_table_bg = $pms->get_light_table_bg;
my $table_text_color = $pms->get_table_text_color;
my ($exportID,$fileName,$group_name,$profile_name,$lastUpdated,$lastUpdatedTime,$advName,$recordCount);
my $lastChunkSize;
my $fieldsToExport;
my $includeHeaders;
my $otherField;
my $otherValue;
my $SendToImpressionwiseDays;
my ($filename,$client_group_id,$profile_id);
my $IronCladGroupID;
my $sendBluehornet;
my $fullPostalOnly;
my $addressOnly;
my $doubleQuoteFields;
my $frequency;
my $ftpFolder;
my $NumberOfFiles;
my $ftpServer;
my $ftpUser;
my $ftpPassword;
my $SendToEmail;
my $cstr="Update";
my $pid=$query->param('pid');
my $ExportCategory;
my $ExportCountry;
if ($pid eq "")
{
	$pid=0;
}
my $exportType=$query->param('exportType');
if ($exportType eq "")
{
	$exportType="Regular";
}
if ($pid == 0)
{
	$cstr="Add";
}

# connect to the pms database
my ($dbhq,$dbhu)=$pms->get_dbh();

# check for login
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $pms->clean_up();
    exit(0);
}
my $username;
my $dataExportTool;
my $ctime;
my $BusinessUnit;
$sql = "select username, dataExportTool,now(),BusinessUnit from UserAccounts where user_id = ?";
$sth = $dbhq->prepare($sql) ;
$sth->execute($user_id);
($username, $dataExportTool,$ctime,$BusinessUnit) = $sth->fetchrow_array();
$sth->finish();
if ($dataExportTool eq "N")
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
if ($pid > 0)
{
	$sql = "select fileName,client_group_id,profile_id,fieldsToExport,ftpFolder,includeHeaders,otherField,otherValue,SendToImpressionwiseDays,IronCladGroupID,sendBluehornet,SendToEmail,NumberOfFiles,ftpServer,ftpUser,ftpPassword,fullPostalOnly,addressOnly,doubleQuoteFields,frequency from DataExport where exportID=?";
	$sth = $dbhq->prepare($sql);
	$sth->execute($pid);
	($filename,$client_group_id,$profile_id,$fieldsToExport,$ftpFolder,$includeHeaders,$otherField,$otherValue,$SendToImpressionwiseDays,$IronCladGroupID,$sendBluehornet,$SendToEmail,$NumberOfFiles,$ftpServer,$ftpUser,$ftpPassword,$fullPostalOnly,$addressOnly,$doubleQuoteFields,$frequency) = $sth->fetchrow_array();
	$sth->finish();
	#
	my $tcid;
	$sql="select categoryID from DataExportCategory where exportID=?";
	$sth = $dbhq->prepare($sql);
	$sth->execute($pid);
	while (($tcid)=$sth->fetchrow_array())
	{
		$ExportCategory->{$tcid}=1;
	}
	$sth->finish();
	$sql="select countryID from DataExportCountry where exportID=?";
	$sth = $dbhq->prepare($sql);
	$sth->execute($pid);
	while (($tcid)=$sth->fetchrow_array())
	{
		$ExportCountry->{$tcid}=1;
	}
	$sth->finish();
}
else
{
	#$fieldsToExport="eid,email_addr,first_name,last_name,sdate";
	$fieldsToExport="email_addr,eid,first_name,last_name,sdate";
	$includeHeaders="N";
	$SendToImpressionwiseDays="NNNNNNN";
	$otherField="";
	$otherValue="";
	$IronCladGroupID=0;
	$sendBluehornet="N";
	$fullPostalOnly="N";
	$addressOnly="N";
	$doubleQuoteFields="N";
	$frequency="Daily";
	$ftpServer="ftp.aspiremail.com";
	$ftpUser="espdata";
	$ftpPassword="ch3frexA";
}

# print out the html page

util::header("Data Export");

print << "end_of_html";
</TD>
</TR>
</TBODY>
</TABLE>
<TR>
<TD vAlign=top align=left bgColor=#999999>
<script language="JavaScript">
end_of_html
if ($exportType ne "Cleanse")
{
	print<<"end_of_html";
	function chkConfirm()
	{
		return true;
	}
end_of_html
}
else
{
	print<<"end_of_html";
	function chkConfirm()
	{
		if (document.campform.ConfirmEmail.value == 0)
		{
			alert('You must select a valid value for the Confirmation Email.');
			return false;
		}
		return true;
	}
end_of_html
}
print<<"end_of_html";

function Remove(gid)
{
	if (confirm("Are you sure you want to Delete this Data Export?"))
    {
    	document.location.href="/cgi-bin/dataexport_del.cgi?gid="+gid;
        return true;
    }
	return false;
}
function selectall4()
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
function unselectall4()
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
<script language="JavaScript">
function selectall()
{
    refno=/catid/;
    for (var x=0; x < document.topform.length; x++)
    {
        if ((document.topform.elements[x].type=="checkbox") && (refno.test(document.topform.elements[x].name)))
        {
            document.topform.elements[x].checked = true;
        }
    }
}
function unselectall()
{
    refno=/catid/;
    for (var x=0; x < document.topform.length; x++)
    {
        if ((document.topform.elements[x].type=="checkbox") && (refno.test(document.topform.elements[x].name)))
        {
            document.topform.elements[x].checked = false;
        }
    }
}
function selectall1()
{
    refno=/countryID/;
    for (var x=0; x < document.topform.length; x++)
    {
        if ((document.topform.elements[x].type=="checkbox") && (refno.test(document.topform.elements[x].name)))
        {
            document.topform.elements[x].checked = true;
        }
    }
}
function unselectall1()
{
    refno=/countryID/;
    for (var x=0; x < document.topform.length; x++)
    {
        if ((document.topform.elements[x].type=="checkbox") && (refno.test(document.topform.elements[x].name)))
        {
            document.topform.elements[x].checked = false;
        }
    }
}
function selectall2()
{
    refno=/scatid/;
    for (var x=0; x < document.campform.length; x++)
    {
        if ((document.campform.elements[x].type=="checkbox") && (refno.test(document.campform.elements[x].name)))
        {
            document.campform.elements[x].checked = true;
        }
    }
}
function unselectall2()
{
    refno=/scatid/;
    for (var x=0; x < document.campform.length; x++)
    {
        if ((document.campform.elements[x].type=="checkbox") && (refno.test(document.campform.elements[x].name)))
        {
            document.campform.elements[x].checked = false;
        }
    }
}
function selectall3()
{
    refno=/scountryID/;
    for (var x=0; x < document.campform.length; x++)
    {
        if ((document.campform.elements[x].type=="checkbox") && (refno.test(document.campform.elements[x].name)))
        {
            document.campform.elements[x].checked = true;
        }
    }
}
function unselectall3()
{
    refno=/scountryID/;
    for (var x=0; x < document.campform.length; x++)
    {
        if ((document.campform.elements[x].type=="checkbox") && (refno.test(document.campform.elements[x].name)))
        {
            document.campform.elements[x].checked = false;
        }
    }
}
function clearAdv()
{
    var selLength = campform.aid.length;
    while (selLength>0)
    {
        campform.aid.remove(selLength-1);
        selLength--;
    }
    campform.aid.length=0;
}
function addAdv(value,text)
{
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    campform.aid.add(newOpt);
}
</script>
<center>
		<form method=post name=topform action=dataexport_updadv.cgi target=hidden>
		<input type=hidden name=BusinessUnit value="$BusinessUnit">
		<TABLE cellSpacing=0 cellPadding=0 width=1000 bgColor=#ffffff border=0>
		<TBODY>
<tr><td colspan=2 align=center><a href="javascript:selectall();">Select All</a>&nbsp;&nbsp;&nbsp;<a href="javascript:unselectall();">Unselect All</a><br></td></tr>
<tr><td colspan=2><b>Offer Categories to Include:</b><br>
end_of_html
$sql="select category_id,category_name from category_info where status='A' order by category_name";
$sth=$dbhu->prepare($sql);
$sth->execute();
my $catid;
my $catname;
my $catcnt;
$catcnt=0;
while (($catid,$catname)=$sth->fetchrow_array())
{
	print "<input type=checkbox name=catid id=catid value=$catid>$catname&nbsp;&nbsp;\n";
	$catcnt++;
	if ($catcnt >= 10)
	{
		print "<br>";
		$catcnt=0;
	}

}
$sth->finish();
print<<"end_of_html";
</td></tr>
<tr><td colspan=2 align=center><br><a href="javascript:selectall1();">Select All</a>&nbsp;&nbsp;&nbsp;<a href="javascript:unselectall1();">Unselect All</a><br></td></tr>
<tr><td colspan=2><b>Countries to Include:</b><br>
end_of_html
$sql="select countryID,countryCode from Country where visible=1 order by countryCode"; 
$sth=$dbhu->prepare($sql);
$sth->execute();
$catcnt=0;
while (($catid,$catname)=$sth->fetchrow_array())
{
	print "<input type=checkbox name=countryID id=countryID value=$catid>$catname&nbsp;&nbsp;\n";
	$catcnt++;
	if ($catcnt >= 10)
	{
		print "<br>";
		$catcnt=0;
	}

}
print qq^<input type=checkbox name=countryID id=countryID value="GB_Actives">GB_Actives&nbsp;&nbsp;\n^;
$sth->finish();
print "</td></tr>";
print<<"end_of_html";
<tr><td colspan=2 align=middle><input type=submit value="Update Advertiser List"></td></tr>
	</tbody>
	</table>
	</form>
		<form method=post name="campform" action=dataexport_upd.cgi target=_top onSubmit="return chkConfirm();">
		<input type=hidden name=pid value=$pid>
		<input type=hidden name=exportType value=$exportType>
		<TABLE cellSpacing=0 cellPadding=0 width=1200 bgColor=#ffffff border=0>
		<TBODY>
end_of_html
if ($exportType ne "Cleanse")
{
	if ($exportType ne "ESP")
	{
		print "<tr><td colspan=2>Frequency: \n";
my @F=("Daily","Weekly","Bi-Weekly","Monthly");
		foreach my $f1 (@F)
		{
			if ($f1 eq $frequency)
			{
				print "<input type=radio name=frequency value='$f1' checked>$f1&nbsp;&nbsp";
			}
			else
			{
				print "<input type=radio name=frequency value='$f1'>$f1&nbsp;&nbsp";
			}
		}
		print "</td></tr>\n";
	}
		print<<"end_of_html";
		<tr><td>Filename(use {{date}} in name for unique date): </td><td><input type=text name=pname size=60 maxlength=255 value="$filename"></td></tr>
		<tr><td>Ftp Server: </td><td align=left><input type=text name=ftpServer size=30 maxlength=30 value="$ftpServer"></td></tr>
		<tr><td>Ftp Username: </td><td align=left><input type=text name=ftpUser size=30 maxlength=30 value="$ftpUser"></td></tr>
		<tr><td>Ftp Pasword: </td><td align=left><input type=password name=ftpPassword size=30 maxlength=30 value="$ftpPassword"></td></tr>
		<tr><td>Ftp Folder: </td><td align=left><input type=text name=ftpFolder size=20 maxlength=20 value="$ftpFolder"></td></tr>
		<tr><td>Number of Files(default is 1): </td><td align=left><input type=text name=NumberOfFiles size=3 maxlength=3 value="$NumberOfFiles"></td></tr>
end_of_html
if ($BusinessUnit ne "Orange")
{
print<<"end_of_html";
		<tr><td>Send To Email(use comma to seperate emails): </td><td align=left><input type=text name=SendToEmail size=150 maxlength=255 value="$SendToEmail"></td></tr>
end_of_html
}
print<<"end_of_html";
<tr><td>Client Group: </td><td><select name=gid>
end_of_html
$sql="select client_group_id,group_name from ClientGroup where status='A' and BusinessUnit='$BusinessUnit' order by group_name";
$sth=$dbhu->prepare($sql);
$sth->execute();
my $cid;
my $cname;
while (($cid,$cname)=$sth->fetchrow_array())
{
	if ($cid == $client_group_id)
	{
		print "<option selected value=$cid>$cname</option>";
	}
	else
	{
		print "<option value=$cid>$cname</option>";
	}
}
$sth->finish();
if ($exportType eq "ESP")
{
	my $espID;
	my $E;
	if ($pid > 0)
	{
		$sql="select espID from DataExportESP where exportID=$pid";
		$sth=$dbhu->prepare($sql);
		$sth->execute();
		while (($espID)=$sth->fetchrow_array())
		{
			$E->{$espID}=1;
		}
		$sth->finish();
	}

	print "</select></td></tr><tr><td>ESP:</td><td>";
	$sql="select espID,espLabel from ESP where espStatus='A' order by espLabel";
	$sth=$dbhu->prepare($sql);
	$sth->execute();
	my $espName;
	my $ecnt=0;
	while (($espID,$espName)=$sth->fetchrow_array())
	{
		if ($E->{$espID})
		{
			print "<input type=checkbox checked value=$espID name=esp>$espName&nbsp;&nbsp;";
		}
		else
		{
			print "<input type=checkbox value=$espID name=esp>$espName&nbsp;&nbsp;";
		}
		$ecnt++;
		if ($ecnt >= 10)
		{
			print "<br>";
			$ecnt=0;
		}
	}
	$sth->finish();
	print "</td></tr>";
}
print "<tr><td>Profile: </td><td><select name=profileid>";
$sql="select profile_id,profile_name from UniqueProfile where status='A' and BusinessUnit='$BusinessUnit' order by profile_name";
$sth=$dbhu->prepare($sql);
$sth->execute();
my $tpid;
my $pname;
while (($tpid,$pname)=$sth->fetchrow_array())
{
	if ($tpid == $profile_id)
	{
		print "<option selected value=$tpid>$pname</option>";
	}
	else
	{
		print "<option value=$tpid>$pname</option>";
	}
}
$sth->finish();
print<<"end_of_html";
</select>&nbsp;&nbsp;</td></tr>
end_of_html
print "<tr><td>Iron Clad Group: </td><td><select name=IronCladGroupID><option value=0>None</option>";
$sql="select IronCladGroupID,groupName from IronCladGroup where status='Active' order by groupName"; 
$sth=$dbhu->prepare($sql);
$sth->execute();
my $ID;
my $gname;
while (($ID,$gname)=$sth->fetchrow_array())
{
	if ($ID == $IronCladGroupID)
	{
		print "<option selected value=$ID>$gname</option>";
	}
	else
	{
		print "<option value=$ID>$gname</option>";
	}
}
$sth->finish();
print<<"end_of_html";
</select>&nbsp;&nbsp;</td></tr>
end_of_html
if ($exportType ne "ESP")
{
print<<"end_of_html"; 
<tr><td valign=top> <b>Days to Send to ImpressionWise: </b><br></td>
<td>
end_of_html
if (substr($SendToImpressionwiseDays,0,1) eq "Y")
{
	print "<input type=checkbox checked value=1 name=imp_monday>Monday</option>\n";
}
else
{
	print "<input type=checkbox value=1 name=imp_monday>Monday</option>\n";
}
if (substr($SendToImpressionwiseDays,1,1) eq "Y")
{
	print "<input type=checkbox checked value=2 name=imp_tuesday>Tuesday</option>\n";
}
else
{
	print "<input type=checkbox value=2 name=imp_tuesday>Tuesday</option>\n";
}
if (substr($SendToImpressionwiseDays,2,1) eq "Y")
{
	print "<input type=checkbox checked value=3 name=imp_wednesday>Wednesday</option>\n";
}
else
{
	print "<input type=checkbox value=3 name=imp_wednesday>Wednesday</option>\n";
}
if (substr($SendToImpressionwiseDays,3,1) eq "Y")
{
	print "<input type=checkbox checked value=4 name=imp_thursday>Thursday</option>\n";
}
else
{
	print "<input type=checkbox value=4 name=imp_thursday>Thursday</option>\n";
}
if (substr($SendToImpressionwiseDays,4,1) eq "Y")
{
	print "<input type=checkbox checked value=5 name=imp_friday>Friday</option>\n";
}
else
{
	print "<input type=checkbox value=5 name=imp_friday>Friday</option>\n";
}
if (substr($SendToImpressionwiseDays,5,1) eq "Y")
{
	print "<input type=checkbox checked value=6 name=imp_saturday>Saturday</option>\n";
}
else
{
	print "<input type=checkbox value=6 name=imp_saturday>Saturday</option>\n";
}
if (substr($SendToImpressionwiseDays,6,1) eq "Y")
{
	print "<input type=checkbox checked value=7 name=imp_sunday>Sunday</option>\n";
}
else
{
	print "<input type=checkbox value=7 name=imp_sunday>Sunday</option>\n";
}
print<<"end_of_html";
</td></tr>
end_of_html
}
print "<tr><td>Send to BlueHornet:</td>";
if ($sendBluehornet eq "Y")
{
	print "<td><input type=radio value=Y name=sendBluehornet checked>Yes&nbsp;&nbsp;<input type=radio name=sendBluehornet value=N>No</td>";
}
else
{
	print "<td><input type=radio name=sendBluehornet value=Y>Yes&nbsp;&nbsp;<input type=radio name=sendBluehornet value=N checked>No</td>";
}
print "</tr>";
print "<tr><td>Include Header Line:</td>";
if ($includeHeaders eq "Y")
{
	print "<td><input type=radio value=Y name=includeHeaders checked>Yes&nbsp;&nbsp;<input type=radio name=includeHeaders value=N>No</td>";
}
else
{
	print "<td><input type=radio name=includeHeaders value=Y>Yes&nbsp;&nbsp;<input type=radio name=includeHeaders value=N checked>No</td>";
}
print qq^<tr><td colspan=2 align=center><a href="javascript:selectall4();">Select All</a>&nbsp;&nbsp;&nbsp;<a href="javascript:unselectall4();">Unselect All</a><br></td></tr>^;
print "</tr><tr><td>Fields to Include in File:</td><td>";
my $SETFLD;
my @F=split(",",$fieldsToExport);
foreach my $f1 (@F)
{
	$SETFLD->{$f1}=1;
}
my @FLDS=("email_addr","eid","first_name","last_name","sdate","Status","ISP","url","gender","client_id","username","cdate","address","address2","city","state","zip","dob","phone","ip","country","client_network","MD5","UMD5");
my @FLDSVAL=("Email","EID","First Name","Last Name","Subscribe Date","Status","ISP","Source Url","Gender","Client ID","Username/Clientname","Capture Date","Address","Address2","City","State","Zip","Birth_Date","Phone","IP","Country","Client Network","EmailAddressMD5","UpperCaseEmailAddressMD5");
my $i=0;
while ($i <= $#FLDS)
{
	if (($i == 9) or ($i == 18))
	{
		print "</td></tr><td></td><td>";
	}
	if ($SETFLD->{$FLDS[$i]})
	{
		print "<input type=checkbox value=$FLDS[$i] name=fields checked>$FLDSVAL[$i]&nbsp;&nbsp;";
	}
	else
	{
		print "<input type=checkbox value=$FLDS[$i] name=fields>$FLDSVAL[$i]&nbsp;&nbsp;";
	}
	$i++;
}		
print "</td></tr>\n";
print qq^<tr><td>Other Field: </td><td><input type=text name=otherField size=50 maxlength=80 value="$otherField"></td></tr>^;
print qq^<tr><td>Other Value({{date}} for current date): </td><td><input type=text name=otherValue size=50 maxlength=80 value="$otherValue"></td></tr>^;
print "<tr><td>Only Include Records with Full Postal:</td>";
if ($fullPostalOnly eq "Y")
{
	print "<td><input type=radio value=Y name=fullPostalOnly checked>Yes&nbsp;&nbsp;<input type=radio name=fullPostalOnly value=N>No</td>";
}
else
{
	print "<td><input type=radio name=fullPostalOnly value=Y>Yes&nbsp;&nbsp;<input type=radio name=fullPostalOnly value=N checked>No</td>";
}
print "</tr>";
print "<tr><td>Only Include Records with Address:</td>";
if ($addressOnly eq "Y")
{
	print "<td><input type=radio value=Y name=addressOnly  checked>Yes&nbsp;&nbsp;<input type=radio name=addressOnly value=N>No</td>";
}
else
{
	print "<td><input type=radio name=addressOnly value=Y>Yes&nbsp;&nbsp;<input type=radio name=addressOnly value=N checked>No</td>";
}
print "</tr>";
print "<tr><td>Double Quote Fields:</td>";
if ($doubleQuoteFields eq "Y")
{
	print "<td><input type=radio value=Y name=doubleQuoteFields checked>Yes&nbsp;&nbsp;<input type=radio name=doubleQuoteFields value=N>No</td>";
}
else
{
	print "<td><input type=radio name=doubleQuoteFields value=Y>Yes&nbsp;&nbsp;<input type=radio name=doubleQuoteFields value=N checked>No</td>";
}
print "</tr>";
if ($pid > 0)
{
	print qq^<tr><td>Re-pull Immediately: </td><td><input type=radio name=repull value=Y>Yes&nbsp;&nbsp;<input type=radio name=repull value=N checked>No^;
}
my $seed_str="";
if ($pid > 0)
{
    my $em;
    $sql="select email_addr from DataExportSeed where exportID=? order by exportSeedID";
    $sth=$dbhu->prepare($sql);
    $sth->execute($pid);
    while (($em)=$sth->fetchrow_array())
    {
        $seed_str.=$em."\n";
    }
    $sth->finish();
}
print qq^<TR><TD class="label">Seeds(one per line): </FONT></td><td colspan=2><textarea name=seeds cols=80 rows=10>$seed_str</textarea></td></tr>^;
}
else
{
	print qq^<tr><td colspan=2>Filename:&nbsp;&nbsp;<select name=pname>^;
    my $host = "ftp.aspiremail.com";
	my $ftpuser="espkenaspiremail";
	my $ftppass="pHAquv2f";
    my $ftp = Net::FTP->new("$host", Timeout => 120, Debug => 0, Passive => 1) or print "Cannot connect to $host: $@\n";
    if ($ftp)
    {
        $ftp->login($ftpuser,$ftppass) or print "Cannot login ", $ftp->message;
		$ftp->cwd("Incoming");
        my @remote_files = $ftp->ls();
        $ftp->quit;
		foreach my $file (@remote_files)
		{
			print qq^<option value="$file">$file</option>^;
		}
    }
	print qq^</select></td></tr>^;
}
print<<"end_of_html";
		<tr><td colspan=2>Output Filename(use {{date}} in name for unique date): <input type=text name=outname size=60 maxlength=255 value=""></td></tr>
end_of_html
if ($exportType eq "Cleanse")
{
		print qq^<tr><td colspan=2>Output Filename For Suppressed Records(use {{date}} in name for unique date): <input type=text name=suppname size=60 maxlength=255 value=""></td></tr>^;
		print qq^<tr><td colspan=2>Email to Send Confirmation To: <select name=ConfirmEmail><option value=0 selected>-- Select an Email Address --</option><option value="alphateam\@zetainteractive.com">alphateam\@zetainteractive.com</option><option value="betateam\@zetainteractive.com">betateam\@zetainteractive.com</option></select></td></tr>^;
}
print<<"end_of_html";
<tr><td colspan=2 align=center><a href="javascript:selectall2();">Select All</a>&nbsp;&nbsp;&nbsp;<a href="javascript:unselectall2();">Unselect All</a><br></td></tr>
<tr><td colspan=2><b>Offer Categories For Suppression:</b><br>
end_of_html
$sql="select category_id,category_name from category_info where status='A' order by category_name";
$sth=$dbhu->prepare($sql);
$sth->execute();
my $catid;
my $catname;
my $catcnt;
$catcnt=0;
while (($catid,$catname)=$sth->fetchrow_array())
{
	if ($ExportCategory->{$catid})
	{
		print "<input type=checkbox checked name=scatid id=scatid value=$catid>$catname&nbsp;&nbsp;\n";
	}
	else
	{
		print "<input type=checkbox name=scatid id=scatid value=$catid>$catname&nbsp;&nbsp;\n";
	}
	$catcnt++;
	if ($catcnt >= 10)
	{
		print "<br>";
		$catcnt=0;
	}

}
$sth->finish();
print<<"end_of_html";
</td></tr>
<tr><td colspan=2 align=center><br><a href="javascript:selectall3();">Select All</a>&nbsp;&nbsp;&nbsp;<a href="javascript:unselectall3();">Unselect All</a><br></td></tr>
<tr><td colspan=2><b>Countries For Suppression:</b><br>
end_of_html
$sql="select countryID,countryCode from Country where visible=1 order by countryCode"; 
$sth=$dbhu->prepare($sql);
$sth->execute();
$catcnt=0;
while (($catid,$catname)=$sth->fetchrow_array())
{
	if ($ExportCountry->{$catid})
	{
		print "<input type=checkbox checked name=scountryID id=scountryID value=$catid>$catname&nbsp;&nbsp;\n";
	}
	else
	{
		print "<input type=checkbox name=scountryID id=scountryID value=$catid>$catname&nbsp;&nbsp;\n";
	}
	$catcnt++;
	if ($catcnt >= 10)
	{
		print "<br>";
		$catcnt=0;
	}

}
if ($ExportCountry->{0})
{
	print qq^<input type=checkbox checked name=scountryID id=scountryID value=0>GB_Actives&nbsp;&nbsp;\n^;
}
else
{
	print qq^<input type=checkbox name=scountryID id=scountryID value=0>GB_Actives&nbsp;&nbsp;\n^;
}
$sth->finish();
print "</td></tr>";
print "<tr><td colspan=2 valign=top><br>Advertiser Suppression: <select name=aid id=aid multiple=multiple size=10>";
if ($pid > 0)
{
	$sql="select ai.advertiser_id,advertiser_name from advertiser_info ai, DataExportAdvertiser dea where status='A' and test_flag='N' and dea.advertiser_id=ai.advertiser_id and dea.exportID=$pid order by advertiser_name";
}
else
{
	$sql="select advertiser_id,advertiser_name from advertiser_info where status='A' and test_flag='N' order by advertiser_name";
}
$sth=$dbhu->prepare($sql);
$sth->execute();
my $aid;
my $aname;
while (($aid,$aname)=$sth->fetchrow_array())
{
	if ($pid > 0)
	{
		print "<option selected value=$aid>$aname</option>";
	}
	else
	{
		print "<option value=$aid>$aname</option>";
	}
}
$sth->finish();
print<<"end_of_html";
</select>
&nbsp;<input type=submit value="$cstr Data Export">
		</td>
		</tr>
		<tr>
<td align="center" valign="top"><br>
                <a href="dataexport_list.cgi?exportType=$exportType" target=_top>
                <img src="$images/home_blkline.gif" border=0></a></TD>
		</tr>
		<TR>
		<TD><IMG height=15 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>
		</form>
end_of_html

$pms->footer();

# exit function

$pms->clean_up();
exit(0);
