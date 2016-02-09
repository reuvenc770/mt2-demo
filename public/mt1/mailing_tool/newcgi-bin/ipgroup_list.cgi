#!/usr/bin/perl

# *****************************************************************************************
# ipgroup_list.cgi
#
# this page displays the list of Ip Groups and lets the user edit / add
#
# History
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $pms = util->new;
my $util = $pms;
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
my $status_name;
my $status;
my $cat_id;
my $domain_name;

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

my $userDataRestrictionWhereClause = '';

$util->getUserData({'userID' => $user_id});

if($util->getUserData()->{'isExternalUser'} == 1)
{
	$userDataRestrictionWhereClause = qq|
        userID = $user_id AND
    |;
}

# print out the html page

util::header("IP Groups");

print << "end_of_html";
</TD>
</TR>
<TR>
<TD vAlign=top align=left bgColor=#999999>
<script language="JavaScript">
function Remove(gid)
{
    if (confirm("Are you sure you want to Delete this Ip Group?"))
    {
        document.location.href="/cgi-bin/ipgroup_del.cgi?gid="+gid;
        return true;
    }
    return false;
}
</script>
	<TABLE cellSpacing=0 cellPadding=10 bgColor=#999999 border=0 width="100%">
	<TBODY>
	<TR>
	<TD vAlign=top align=left bgColor=#ffffff colSpan=10>
		<TABLE cellSpacing=0 cellPadding=0 width=860 bgColor=#ffffff border=0>
		<TBODY>
		<tr>
		<td><form method=POST action=ipgroup_add.cgi>IP Group Name: <input type=text name=ipname size=25 maxlength=25>&nbsp;&nbsp;&nbsp;Outbound Throttle(zero to disable):<input type=text name=othrottle size=5 value=0>&nbsp;&nbsp;&nbsp;</td></tr><tr><td>Goodmail Enabled: <select name=goodmail_enabled><option value=N checked>No</option><option value=Y>Yes</option></select>&nbsp;&nbsp;Colo: <select name=colo><option value=FORT>FORT<?option><option value=NAC>NAC</option></select>&nbsp;&nbsp;Chunk Size(Only for Hotmail/Chunking Deploys): <input type=text name=chunk size=3 maxlength=3 value=0>&nbsp;&nbsp;</td></tr><tr><td>Domainkeys Enabled: <select name=domainkeys_enabled><option value=N>No</option><option value=Y checked>Yes</option></select>&nbsp;&nbsp;<input type=submit value="Add IP Group"></form>
		</td>
		</tr>
		<tr><td><form method=POST action="upload_ipgroup.cgi" encType=multipart/form-data>IpGroup CSV File:  <INPUT type=file name="upload_file" size="65">&nbsp;&nbsp;<input type=submit value="Upload"></form></td></tr>
		<tr>
<td align="center" valign="top">
                <a href="mainmenu.cgi">
                <img src="$images/home_blkline.gif" border=0></a></TD>
		</tr>
		<TR>
		<TD><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
			Select a IP Group to edit or add a new IP Group</FONT></TD>
		</TR>
		<TR>
		<TD><IMG height=15 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>
		<TABLE cellSpacing=0 cellPadding=3 width="50%" border=0>
		<TBODY>
		<TR bgColor="#509C10" height=15>
		<TD align=center height=15>
			<font face="Verdana,Arial,Helvetica,sans-serif" color="white" size="3">
			<b>IP Groups</b></font></TD>
		<td align=center>IP Count</td><td></td>
		</TR>
end_of_html

# read info about the lists

my $gid;
my $gname;
my $othrottle;
my $colo;
my $cnt;
#$sql = "select igi.group_id,group_name,outbound_throttle,colo,count(*) from IpGroup ig,IpGroupIps igi where $userDataRestrictionWhereClause status='Active' and ig.group_id=igi.group_id group by igi.group_id order by group_name"; 
$sql = "select ig.group_id,group_name,outbound_throttle,colo from IpGroup ig where $userDataRestrictionWhereClause status='Active' order by group_name"; 
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($gid,$gname,$othrottle,$colo) = $sth->fetchrow_array())
{
	$sql="select count(*) from IpGroupIps where group_id=?";
	my $sth1=$dbhq->prepare($sql);
	$sth1->execute($gid);
	($cnt)=$sth1->fetchrow_array();
	$sth1->finish();
	$reccnt++;
    if ( ($reccnt % 2) == 0 )
    {
        $bgcolor = "$light_table_bg";
    }
    else
    {
        $bgcolor = "$alt_light_table_bg";
    }

	if ($othrottle > 0)
	{
		print qq { <TR bgColor=$bgcolor><TD align=left><font color="#509C10" face="verdana,arial,helvetica,sans serif" size="2"><A HREF="ipgroup_edit.cgi?gid=$gid">$gname</a> - $colo <b>Outbound Throttle: $othrottle</b><td align=center>$cnt</td><td align=right><input type=button value="Delete" onClick="return Remove($gid);"</td></font></TD></TR> \n };
	}
	else
	{
		print qq { <TR bgColor=$bgcolor><TD align=left><font color="#509C10" face="verdana,arial,helvetica,sans serif" size="2"><A HREF="ipgroup_edit.cgi?gid=$gid">$gname</a> - $colo <td align=center>$cnt</td><td align=right><input type=button value="Delete" onClick="return Remove($gid);"</td></font></TD></TR> \n };
	}
}

$sth->finish();

print << "end_of_html";
		<TR>
		<TD colspan=3><IMG height=7 src="$images/spacer.gif"></TD>
		</TR>
		<TR>
		<TD colspan=3>

			<table cellpadding="0" cellspacing="0" border="0" width="100%">
			<tr>
			<td width=50% align="center">
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
