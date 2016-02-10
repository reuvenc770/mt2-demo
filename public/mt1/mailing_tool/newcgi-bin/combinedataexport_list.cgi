#!/usr/bin/perl

# *****************************************************************************************
# combinedataexport_list.cgi
#
# this page displays the list of Combine Data Exports and lets the user edit / add
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
my ($combineID,$fileName,$lastUpdated,$lastUpdatedTime,$recordCount,$exportfile);

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

# print out the html page
my $showfile=$query->param('showfile');
$showfile = $showfile || 0;
my $ctype="Combine Data Export";


my $ctitle="Mailing Tool";
print "Content-Type: text/html;charset-utf-8\n\n";
print << "end_of_html";
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>$ctitle - Email Tool</title>
</head>
<body link="#000000" vlink="#000000" alink="#000000">
<TABLE cellSpacing=0 cellPadding=0 border=0 align='center' bgcolor='#FFFFFF' width=80%>
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
	if (confirm("Are you sure you want to Delete this Combin eData Export?"))
    {
    	document.location.href="/cgi-bin/combinedataexport_del.cgi?gid="+gid;
        return true;
    }
	return false;
}
function Pause(gid)
{
   	document.location.href="/cgi-bin/combinedataexport_pause.cgi?gid="+gid;
   return true;
}
function Activate(gid)
{
   	document.location.href="/cgi-bin/combinedataexport_activate.cgi?gid="+gid;
   return true;
}
</script>
<center>
<h3>$ctype</h3>
<a href=combinedataexport_main.cgi?pid=0>Add Combine Data Export</a>
end_of_html
if ($showfile == 1)
{
	print "&nbsp;&nbsp;<a href=combinedataexport_list.cgi>Show Without Files</a>";
	print "&nbsp;&nbsp;<a href=combinedataexport_list.cgi?showfile=2>Show Paused</a>";
}
elsif ($showfile == 2)
{
	print "&nbsp;&nbsp;<a href=combinedataexport_list.cgi>Show Without Files</a>";
	print "&nbsp;&nbsp;<a href=combinedataexport_list.cgi?showfile=1>Show With Files</a>";
}
else
{
	print "&nbsp;&nbsp;<a href=combinedataexport_list.cgi?showfile=1>Show With Files</a>";
	print "&nbsp;&nbsp;<a href=combinedataexport_list.cgi?showfile=2>Show Paused</a>";
}
print<<"end_of_html";
<br><a href="mainmenu.cgi"><img src="$images/home_blkline.gif" border=0></a>

</center>
	<TABLE cellSpacing=0 cellPadding=10 bgColor=#999999 border=0 width="100%">
	<TBODY>
	<TR>
	<TD vAlign=top align=left bgColor=#ffffff>
		<TABLE cellSpacing=0 cellPadding=3 width="100%" border=0>
		<TBODY>
		<TR bgColor="#509C10" height=15>
end_of_html
if ($showfile == 1)
{
		print "<th>Filename</th><th>Files</th><th>Last Pulled</th><th>Records</th><th></th></tr>";
}
else
{
		print "<th>Filename</th><th># of Files</th><th>Last Pulled</th><th>Records</th><th></th></tr>";
}
print "</TR>";

if ($showfile == 2)
{
	$sql="select dec1.combineID,dec1.fileName,dec1.lastUpdated,dec1.lastUpdatedTime,dec1.recordCount,dec1.status,de.fileName from DataExportCombine dec1 join DataExportCombineJoin decj on decj.combineID=dec1.combineID join DataExport de on de.exportID=decj.exportID where dec1.status='Paused' order by dec1.fileName,dec1.combineID";
}
else
{
	$sql="select dec1.combineID,dec1.fileName,dec1.lastUpdated,dec1.lastUpdatedTime,dec1.recordCount,dec1.status,de.fileName from DataExportCombine dec1 join DataExportCombineJoin decj on decj.combineID=dec1.combineID join DataExport de on de.exportID=decj.exportID where dec1.status='Active' order by dec1.fileName,dec1.combineID";
}
$sth=$dbhu->prepare($sql);
$sth->execute();
my $fileCnt;
my $fileStr;
my $oldcombineID=0;
my $oldfileName;
my $oldlastUpdated;
my $oldlastUpdatedTime;
my $oldcstatus;
my $oldrecordCount;
while (($combineID,$fileName,$lastUpdated,$lastUpdatedTime,$recordCount,$cstatus,$exportfile) = $sth->fetchrow_array())
{
	if ($combineID != $oldcombineID)
	{
		if ($oldcombineID != 0)
		{
			$reccnt++;
    		if ( ($reccnt % 2) == 0 )
    		{
        		$bgcolor = "$light_table_bg";
    		}
    		else
    		{
        		$bgcolor = "$alt_light_table_bg";
    		}
			if ($showfile == 1)
			{
				print "<tr bgcolor=$bgcolor><td><a href=\"combinedataexport_main.cgi?pid=$oldcombineID\">$oldfileName</a></td><td>$fileStr</td><td>$oldlastUpdated $oldlastUpdatedTime</td><td>$oldrecordCount</td><td><input type=button value=Delete onClick=\"return Remove($oldcombineID);\">";
			}
			else
			{
				print "<tr bgcolor=$bgcolor><td><a href=\"combinedataexport_main.cgi?pid=$oldcombineID\">$oldfileName</a></td><td>$fileCnt</td><td>$oldlastUpdated $oldlastUpdatedTime</td><td>$oldrecordCount</td><td><input type=button value=Delete onClick=\"return Remove($oldcombineID);\">";
			}
			if ($oldcstatus eq "Active")
			{
				print "&nbsp;&nbsp;<input type=button value=Pause onClick=\"return Pause($oldcombineID);\">";
			}
			elsif ($oldcstatus eq "Paused")
			{
				print "&nbsp;&nbsp;<input type=button value=Activate onClick=\"return Activate ($oldcombineID);\">";
			}
			print "</td></tr>";
		}
		$fileCnt=0;
		$fileStr="";
		$oldcombineID=$combineID;
		$oldcstatus=$cstatus;
		$oldfileName=$fileName;
		$oldrecordCount=$recordCount;
		$oldlastUpdated=$lastUpdated;
		$oldlastUpdatedTime=$lastUpdatedTime;
	}
	$fileCnt++;
	$fileStr.=$exportfile."<br>";
}

$sth->finish();
		if ($oldcombineID != 0)
		{
			$reccnt++;
    		if ( ($reccnt % 2) == 0 )
    		{
        		$bgcolor = "$light_table_bg";
    		}
    		else
    		{
        		$bgcolor = "$alt_light_table_bg";
    		}
			if ($showfile == 1)
			{
				print "<tr bgcolor=$bgcolor><td><a href=\"combinedataexport_main.cgi?pid=$oldcombineID\">$oldfileName</a></td><td>$fileStr</td><td>$oldlastUpdated $oldlastUpdatedTime</td><td>$oldrecordCount</td><td><input type=button value=Delete onClick=\"return Remove($oldcombineID);\">";
			}
			else
			{
				print "<tr bgcolor=$bgcolor><td><a href=\"combinedataexport_main.cgi?pid=$oldcombineID\">$oldfileName</a></td><td>$fileCnt</td><td>$oldlastUpdated $oldlastUpdatedTime</td><td>$oldrecordCount</td><td><input type=button value=Delete onClick=\"return Remove($oldcombineID);\">";
			}
			if ($oldcstatus eq "Active")
			{
				print "&nbsp;&nbsp;<input type=button value=Pause onClick=\"return Pause($oldcombineID);\">";
			}
			elsif ($oldcstatus eq "Paused")
			{
				print "&nbsp;&nbsp;<input type=button value=Activate onClick=\"return Activate ($oldcombineID);\">";
			}
			print "</td></tr>";
		}

print << "end_of_html";
		<TR>
		<TD colspan=3><IMG height=7 src="$images/spacer.gif"></TD>
		</TR>
		<TR>
		<TD colspan=3>

			<table cellpadding="0" cellspacing="0" border="0" width="50%">
			<tr>
			<td width=100% align="center">
				<a href="mainmenu.cgi">
				<img src="$images/home_blkline.gif" border=0></a></TD>
			</tr>
			</table>

		</TD>
		</TR>
		</TBODY>
		</TABLE>
	</TD>
	</TR>
	</TBODY>
	</TABLE>

</TD>
</TR>
<TR>
<TD noWrap align=left height=17>
end_of_html

$pms->footer();

# exit function

$pms->clean_up();
exit(0);
