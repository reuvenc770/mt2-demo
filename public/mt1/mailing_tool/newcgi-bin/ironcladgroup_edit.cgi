#!/usr/bin/perl

# *****************************************************************************************
# ironcladgroup_edit.cgi
#
# this page allows setup of Lists for IronCladGroup 
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
my $link_id;
my $refurl;
my $bgcolor;
my $reccnt;
my $images = $pms->get_images_url;
my $alt_light_table_bg = $pms->get_alt_light_table_bg;
my $light_table_bg = $pms->get_light_table_bg;
my $table_text_color = $pms->get_table_text_color;
my $SERVER;
my $groupName;

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
my $gid=$query->param('gid');

# print out the html page
$sql="select groupName from IronCladGroup where IronCladGroupID=$gid";
$sth = $dbhu->prepare($sql);
$sth->execute();
($groupName)=$sth->fetchrow_array();
$sth->finish();
#
my $id;
my $name;
$sql="select adkServerID,serverName from ADKServer"; 
$sth = $dbhu->prepare($sql);
$sth->execute();
while (($id,$name)=$sth->fetchrow_array())
{
	$SERVER->{$id}=$name;
}
$sth->finish();

my $ctitle;

$ctitle="Mailing Tool";
print "Content-Type: text/html;charset-utf-8\n\n";
print << "end_of_html";
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>$ctitle - Email Tool</title>
</head>
<body link="#000000" vlink="#000000" alink="#000000">
<TABLE cellSpacing=0 cellPadding=0 border=0 align='center' bgcolor='#FFFFFF'>
<TBODY>
<TR bgcolor='#FFFFFF'>
<TD align='center'>
	<table border="0" cellpadding="0" cellspacing="0" width="1200" align='center' border=0>
	<tr>
	<TD width=248 bgcolor='#FFFFFF'>
		<img border="0" src="/mail-images/header.gif"></TD>
	<TD width=328 bgcolor='#FFFFFF'>&nbsp;</TD>
	</tr>
	<tr>
	<td colspan=3>
		<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
		<td align='center'><b><font face="Arial" size="2" color='#FFFFFF'>&nbsp;Ironclad Group Edit</FONT></b></td>
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
<TD vAlign=top align=left bgColor=#999999>
<script language="JavaScript">
function Remove(gid)
{
	if (confirm("Are you sure you want to Delete this IronClad Group?"))
    {
    	document.location.href="/cgi-bin/ironcladgroup_del.cgi?gid="+gid;
        return true;
    }
	return false;
}
</script>

	<TABLE cellSpacing=0 cellPadding=10 bgColor=#999999 border=0 width="100%">
	<TBODY>
	<TR>
	<TD vAlign=top align=left bgColor=#ffffff>
		<center>
                <a href="ironcladgroup_list.cgi"><img src="$images/home_blkline.gif" border=0></a>
		<br>
		Group Name: &nbsp;&nbsp;<b>$groupName</b>
		<br>
		<br>
		<form method=post action=ironcladlist_add.cgi>
		<input type=hidden name=gid value=$gid>
		<table cellSpacing=0 cellPadding=3 width=100% border=0>
		<tr>
		<td>List Name: </td><td><input name=listName size=40 maxlength=50 type=text></td>
		<td>List Group ID: </td><td><input name=listGroupID size=10 type=text></td>
		<td>First IP: </td><td><input name=firstip size=3 maxlength=3 type=text></td>
		<td>Last IP: </td><td><input name=lastip size=3 maxlength=3 type=text></td>
		<td>Domain: </td><td><input name=domain size=30 maxlength=30 type=text></td></tr>
		<tr>
		<td>Xperip: </td><td><input name=xperip size=3 maxlength=3 type=text></td>
		<td>IPC: </td><td><input name=ipc size=20 maxlength=20 type=text></td>
		<td>ADK Hours: </td><td><input name=listadkhours size=30 maxlength=30 type=text></td>
		<td>Server: </td><td><select name=adkServerID>
end_of_html
foreach my $s (sort keys %$SERVER)
{
	print "<option value=$s>$SERVER->{$s}</option>\n";
}
print<<"end_of_html";
		</select></td><td><input type=submit value="Add List"></td></tr>
		</table>
		</form>
		<p>
		<TABLE cellSpacing=0 cellPadding=3 width="100%" border=1>
		<TBODY>
		<TR bgColor="#509C10" height=15>
		<TD align=center height=15 colspan=10>
			<font face="Verdana,Arial,Helvetica,sans-serif" color="white" size="3">
			<b>Lists</b></font></TD>
		</TR>
		<tr><th>ListName</th><th>List Group ID</th><th>First IP</th><th>Last IP</th><th>Domain</th><th>Xperip</th><th>IPC</th><th>ADK Hours</th><th>Server</th><th></th></tr>
end_of_html

# read info about the lists
my ($listID,$listName,$listGroupID,$firstip,$lastip,$domain,$xperip,$ipc,$listadkhours,$adkServerID);
$sql="select icl.listID,listName,listGroupID,firstip,lastip,domain,xperip,ipc,listadkhours,adkServerID from IronCladList icl join IronCladGroupLists icgl on icgl.listID=icl.listID where icgl.IronCladGroupID=$gid order by listName";
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($listID,$listName,$listGroupID,$firstip,$lastip,$domain,$xperip,$ipc,$listadkhours,$adkServerID) = $sth->fetchrow_array())
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
	print "<tr colspan=$bgcolor><td>$listName</td><td>$listGroupID</td><td>$firstip</td><td>$lastip</td><td>$domain</td><td>$xperip</td><td>$ipc</td><td>$listadkhours</td><td>$SERVER->{$adkServerID}</td><td><a href=ironcladlist_del.cgi?listID=$listID&gid=$gid>Delete</a></td></tr>\n";
}

$sth->finish();

print << "end_of_html";
		</TBODY>
		</TABLE>
<p>
<center>
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
