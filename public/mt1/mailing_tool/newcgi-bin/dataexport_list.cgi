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
my $cstatus;
my $ftpUser;
my ($exportID,$fileName,$group_name,$profile_name,$lastUpdated,$lastUpdatedTime,$advName,$recordCount);
my $lastChunkSize;

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

# print out the html page
my $showsupp=$query->param('showsupp');
$showsupp = $showsupp || 0;
my $exportType=$query->param('exportType');
my $ctype="Regular Data Export";
if ($exportType eq "ESP")
{
	$ctype="ESP Responder Data Export";
}
elsif ($exportType eq "Cleanse")
{
	$ctype="Data Cleanse";
	$exportType="Cleanse";
}
else
{
	$exportType="Regular";
}


my $ctitle="Mailing Tool";
print "Content-Type: text/html;charset-utf-8\n\n";
print << "end_of_html";
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>$ctitle - Email Tool</title>
</head>
<body link="#000000" vlink="#000000" alink="#000000">
<TABLE cellSpacing=0 cellPadding=0 border=0 align='center' bgcolor='#FFFFFF' width=100%>
<TBODY>
<TR bgcolor='#FFFFFF'>
<TD align='center'>
	<table border="0" cellpadding="0" cellspacing="0" width="800" align='center' border=0>
	<tr>
	<TD width=248 bgcolor='#FFFFFF'>
		<img border="0" src="/mail-images/header.gif"></TD>
	<TD width=328 bgcolor='#FFFFFF'>&nbsp;</TD>
	</tr>
	<tr>
	<td colspan=3>
		<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
		<td align='center'><b><font face="Arial" size="2" color='#FFFFFF'>&nbsp;Data Export</FONT></b></td>
		</tr>
		<tr>
		<td>
		</td>
		</tr>
		</table>
	</td>
	</tr>
	</table>
</TD>
</TR>
<TR>
<TD vAlign=top align=left bgColor=#FFFFFF>
<script language="JavaScript">
function Remove(gid)
{
	if (confirm("Are you sure you want to Delete this Data Export?"))
    {
    	document.location.href="/cgi-bin/dataexport_del.cgi?exportType=$exportType&gid="+gid;
        return true;
    }
	return false;
}
function Pause(gid)
{
   	document.location.href="/cgi-bin/dataexport_pause.cgi?exportType=$exportType&gid="+gid;
   return true;
}
function Copy(gid)
{
   	document.location.href="/cgi-bin/dataexport_copy.cgi?exportType=$exportType&gid="+gid;
   return true;
}
function Activate(gid)
{
   	document.location.href="/cgi-bin/dataexport_activate.cgi?exportType=$exportType&gid="+gid;
   return true;
}
function selectall()
{
    refno=/exarr/;
    for (var x=0; x < document.exportform.length; x++)
    {
        if ((document.exportform.elements[x].type=="checkbox") && (refno.test(document.exportform.elements[x].name)))
        {
            document.exportform.elements[x].checked = true;
        }
    }
}
function unselectall()
{
    refno=/exarr/;
    for (var x=0; x < document.exportform.length; x++)
    {
        if ((document.exportform.elements[x].type=="checkbox") && (refno.test(document.exportform.elements[x].name)))
        {
            document.exportform.elements[x].checked = false;
        }
    }
}
</script>
</script>
<center>
<h3>$ctype</h3>
<a href=dataexport_main.cgi?pid=0&exportType=$exportType>Add Data Export</a>
end_of_html
if ($exportType eq "Cleanse")
{
	print qq^&nbsp;&nbsp;<a href=upload_datacleanse.cgi?test=1>Test Upload Data Cleanse</a>^;
	print qq^&nbsp;&nbsp;<a href=upload_datacleanse.cgi>Upload Data Cleanse</a>^;
}
if ($showsupp == 1)
{
	print "&nbsp;&nbsp;<a href=dataexport_list.cgi?exportType=$exportType>Show Without Suppression</a>";
	if ($exportType ne "Cleanse")
	{
		print "&nbsp;&nbsp;<a href=dataexport_list.cgi?showsupp=2&exportType=$exportType>Show Paused</a>";
	}
}
elsif ($showsupp == 2)
{
	print "&nbsp;&nbsp;<a href=dataexport_list.cgi?exportType=$exportType>Show Without Suppression</a>";
	print "&nbsp;&nbsp;<a href=dataexport_list.cgi?showsupp=1&exportType=$exportType>Show With Suppression</a>";
}
else
{
	print "&nbsp;&nbsp;<a href=dataexport_list.cgi?showsupp=1&exportType=$exportType>Show With Suppression</a>";
	if ($exportType ne "Cleanse")
	{
		print "&nbsp;&nbsp;<a href=dataexport_list.cgi?showsupp=2&exportType=$exportType>Show Paused</a>";
	}
}
print<<"end_of_html";
<br><a href="mainmenu.cgi"><img src="$images/home_blkline.gif" border=0></a><br>

</center>
end_of_html
if ($exportType ne "Cleanse")
{
	print qq^<p><center><a href="javascript:selectall();">Select All</a>&nbsp;&nbsp;&nbsp;<a href="javascript:unselectall();">Unselect All</a><br><br>^;
	print qq^<form method=post action=dataexport_multi.cgi name=exportform>^;
	print qq^<input type=hidden name=showsupp value="$showsupp">^;
	print qq^<input type=hidden name=exportType value="$exportType">^;
}
print<<"end_of_html";
	<TABLE cellSpacing=0 cellPadding=10 bgColor=#999999 border=0 width="100%">
	<TBODY>
	<TR>
	<TD vAlign=top align=left bgColor=#ffffff>
		<TABLE cellSpacing=0 cellPadding=3 width="100%" border=0>
		<TBODY>
		<TR bgColor="#509C10" height=15>
end_of_html
if ($showsupp == 1)
{
	if ($exportType ne "Cleanse")
	{
		print "<th></th><th>Filename</th><th>Client Group</th><th>Profile</th><th>FTP Username</th><th width=10%>Last Pulled</th><th>Advertiser Suppression</th><th>Records</th><th></th></tr>";
	}
	else
	{
		print "<th>Filename</th><th width=10%>Last Pulled</th><th>Advertiser Suppression</th><th>Records</th><th></th></tr>";
	}
}
else
{
	if ($exportType ne "Cleanse")
	{
		print "<th></th><th>Filename</th><th>Profile</th><th>FTP Username</th><th>Last Pulled</th><th>Records</th><th></th></tr>";
	}
	else
	{
		print "<th>Filename</th><th>Last Pulled</th><th>Records</th><th></th></tr>";
	}
}
print "</TR>";

if ($showsupp == 2)
{
	if ($exportType ne "Cleanse")
	{
		$sql="select exportID,fileName,cg.group_name,up.profile_name,lastUpdated,lastUpdatedTime,recordCount,de.status,ftpUser from DataExport de,ClientGroup cg, UniqueProfile up where de.status='Paused' and de.client_group_id=cg.client_group_id and de.profile_id=up.profile_id and exportType='$exportType' and de.BusinessUnit='$BusinessUnit' order by fileName";
	}
	else
	{
		$sql="select exportID,fileName,'','',lastUpdated,lastUpdatedTime,recordCount,de.status,ftpUser from DataExport de where de.status='Paused' and exportType='$exportType' and (lastUpdated is null or lastUpdated >= date_sub(curdate(),interval 2 day)) order by fileName";
	}
}
else
{
	if ($exportType ne "Cleanse")
	{
		$sql="select exportID,fileName,cg.group_name,up.profile_name,lastUpdated,lastUpdatedTime,recordCount,de.status,ftpUser from DataExport de,ClientGroup cg, UniqueProfile up where de.status ='Active' and de.client_group_id=cg.client_group_id and de.profile_id=up.profile_id and exportType='$exportType' and de.BusinessUnit='$BusinessUnit' order by fileName";
	}
	else
	{
		$sql="select exportID,fileName,'','',lastUpdated,lastUpdatedTime,recordCount,de.status,ftpUser from DataExport de where de.status ='Active' and exportType='$exportType' and (lastUpdated is null or lastUpdated >= date_sub(curdate(),interval 2 day))  order by fileName";
	}
}
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($exportID,$fileName,$group_name,$profile_name,$lastUpdated,$lastUpdatedTime,$recordCount,$cstatus,$ftpUser) = $sth->fetchrow_array())
{
	$advName="";
	if ($showsupp == 1)
	{
		$sql="select advertiser_name from advertiser_info ai,DataExportAdvertiser dea where ai.advertiser_id=dea.advertiser_id and dea.exportID=$exportID";
		my $sth1=$dbhu->prepare($sql);
		$sth1->execute();
		my $aname;
		while (($aname)=$sth1->fetchrow_array())
		{
			if ($advName ne "")
			{
				$advName.="<br>";
			}
			$advName.=$aname;
		}
		$sth1->finish();
		if ($advName eq "")
		{
			$advName="None";
		}
	}
	$reccnt++;
    if ( ($reccnt % 2) == 0 )
    {
        $bgcolor = "$light_table_bg";
    }
    else
    {
        $bgcolor = "$alt_light_table_bg";
    }
	if ($showsupp == 1)
	{
		if ($exportType ne "Cleanse")
		{
			print "<tr bgcolor=$bgcolor><td><input type=checkbox name=exarr value=$exportID></td><td><a href=\"dataexport_main.cgi?pid=$exportID&exportType=$exportType\">$fileName</a></td><td>$group_name</td><td>$profile_name</td><td>$ftpUser</td><td>$lastUpdated $lastUpdatedTime</td><td>$advName</td><td>$recordCount</td><td><input type=button value=Delete onClick=\"return Remove($exportID);\">";
		}
		else
		{
			print "<tr bgcolor=$bgcolor><td>$fileName</td><td>$lastUpdated $lastUpdatedTime</td><td>$advName</td><td>$recordCount</td>";
		}
	}
	else
	{
		if ($exportType ne "Cleanse")
		{
			print "<tr bgcolor=$bgcolor><td><input type=checkbox name=exarr value=$exportID></td><td><a href=\"dataexport_main.cgi?pid=$exportID&exportType=$exportType\">$fileName</a></td><td>$profile_name</td><td>$ftpUser</td><td>$lastUpdated $lastUpdatedTime</td><td>$recordCount</td><td><input type=button value=Delete onClick=\"return Remove($exportID);\">";
		}
		else
		{
			print "<tr bgcolor=$bgcolor><td>$fileName</td><td>$lastUpdated $lastUpdatedTime</td><td>$recordCount</td><td>";
		}
	}
	if ($exportType ne "Cleanse")
	{
	if ($cstatus eq "Active")
	{
		print "&nbsp;&nbsp;<input type=button value=Pause onClick=\"return Pause($exportID);\">";
	}
	elsif ($cstatus eq "Paused")
	{
		print "&nbsp;&nbsp;<input type=button value=Activate onClick=\"return Activate ($exportID);\">";
	}
	print "&nbsp;&nbsp;<input type=button value=Copy onClick=\"return Copy($exportID);\">";
	print "</td></tr>";
	}
	else
	{
		print "</tr>";
	}
}

$sth->finish();

print << "end_of_html";
		<TR>
		<TD colspan=3><IMG height=7 src="$images/spacer.gif"></TD>
		</TR>
		<TR>
		<TD colspan=3>


		</TD>
		</TR>
		</TBODY>
		</TABLE>
	</TD>
	</TR>
	</TBODY>
	</TABLE>
end_of_html
if ($exportType ne "Cleanse")
{
	if ($showsupp == 2)
	{
		print qq^<input type=submit name=submit value="Activate">^;
	}
	else
	{
		print qq^<input type=submit name=submit value="Pause">&nbsp;&nbsp;<input type=submit name=submit value="Re-pull Immediately">^;
	}
	print qq^</form>^;
}
print<<"end_of_html";
</TD>
</TR>
<tr><td>
			<table cellpadding="0" cellspacing="0" border="0" width="80%">
			<tr>
			<td width=100% align="center">
				<a href="mainmenu.cgi">
				<img src="$images/home_blkline.gif" border=0></a></TD>
			</tr>
			</table>
</td></tr>

<TR>
<TD noWrap align=left height=17>
end_of_html

$pms->footer();

# exit function

$pms->clean_up();
exit(0);
