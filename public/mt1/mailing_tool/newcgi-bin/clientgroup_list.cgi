#!/usr/bin/perl

# *****************************************************************************************
# clientgroup_list.cgi
#
# this page displays the list of Client Groups and lets the user edit / add
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

$pms->getUserData({'userID' => $user_id});

if($pms->getUserData()->{'isExternalUser'} == 1)
{
	$userDataRestrictionWhereClause = qq|
        userID = $user_id AND
    |;
}
my $BusinessUnit;
$sql="select BusinessUnit from UserAccounts where user_id=?";
$sth=$dbhu->prepare($sql);
$sth->execute($user_id);
($BusinessUnit)=$sth->fetchrow_array();
$sth->finish();


# print out the html page

util::header("Client Groups");

print << "end_of_html";
</TD>
</TR>
<TR>
<TD vAlign=top align=left bgColor=#999999>
<script language="JavaScript">
function Remove(gid)
{
	if (confirm("Are you sure you want to Delete this Client Group?"))
    {
    	document.location.href="/cgi-bin/clientgroup_del.cgi?gid="+gid;
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
		<td><form method=POST action=clientgroup_add.cgi>Client Group Name: <input type=text name=gname size=25 maxlength=25>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=checkbox name="excludeFromSuper" value="Y">Exclude From Super&nbsp;&nbsp;&nbsp;&nbsp;<input type=submit value="Add Client Group"></form>
		</td>
		</tr>
		<tr>
		<td><form method="post" action="upload_clientgroup.cgi" encType=multipart/form-data>
Unique File: <input type=file name=upload_file><input type=submit value=Load></form></td></tr>
		<tr>
<td align="center" valign="top">
                <a href="mainmenu.cgi">
                <img src="$images/home_blkline.gif" border=0></a></TD>
		</tr>
		<TR>
		<TD><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
			Select a Client Group to edit or add a new Client Group</FONT></TD>
		</TR>
		<TR>
		<TD><IMG height=15 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>
		<form method=POST action=clientgroup_mdelete.cgi>
		<select name=addclientid>
end_of_html
my $fname;
my $tuid;
$sql="select user_id,username from user where status='A' order by username";
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($tuid,$fname)=$sth->fetchrow_array())
{
	print "<option value=$user_id>$fname</option>\n";
}
$sth->finish();
print<<"end_of_html";
		</select>
		<input type=submit name=submit value="Update Multiple Groups">&nbsp;&nbsp;Note:<i>Select multiple checkboxes and select this button to add to multiple groups</i><br>
		<input type=submit name=submit value="Delete Multiple Groups">&nbsp;&nbsp;Note:<i>Select multiple checkboxes and select this button to delete multiple groups</i>
		<TABLE cellSpacing=0 cellPadding=3 width="70%" border=0>
		<TBODY>
		<TR bgColor="#509C10" height=15>
		<TD colspan=2 align=center height=15>
			<font face="Verdana,Arial,Helvetica,sans-serif" color="white" size="3">
			<b>Client Groups</b></font></TD>
		</TR>
end_of_html

# read info about the lists

my $gid;
my $gname;
my $excludeFromSuper;
$sql = "select client_group_id,group_name,excludeFromSuper from ClientGroup where $userDataRestrictionWhereClause status='A' and BusinessUnit='$BusinessUnit' order by group_name"; 
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($gid,$gname,$excludeFromSuper) = $sth->fetchrow_array())
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
	if ($excludeFromSuper eq "Y")
	{
		$gname.=" (Exclude from Super)";
	}
	print qq { <TR bgColor=$bgcolor><TD align=left><input type=checkbox name="cdel" value="$gid"><font color="#509C10" face="verdana,arial,helvetica,sans serif" size="2"><A HREF="clientgroup_edit.cgi?gid=$gid">$gname</a></font></TD><td align=right><a href="clientgroup_display.cgi?gid=$gid">Display</a>&nbsp;&nbsp;&nbsp;<a href="clientgroup_profile.cgi?group_id=$gid">Setup Profiles</a>&nbsp;&nbsp;<a href="clientgroup_copy.cgi?gid=$gid">Copy</a>&nbsp;&nbsp;<input type=button value="Delete" onClick="return Remove($gid);"></td></TR> \n };
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
		</form>
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
