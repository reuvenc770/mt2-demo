#!/usr/bin/perl

# *****************************************************************************************
# clientgroup_lookup.cgi
#
# this page displays the list of Client Groups that a user belongs to 
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
my $reccnt;
my $tid;
my $tname;
my $images = $pms->get_images_url;
my $alt_light_table_bg = $pms->get_alt_light_table_bg;
my $light_table_bg = $pms->get_light_table_bg;
my $table_text_color = $pms->get_table_text_color;
my $fname;

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
my $cid=$query->param('cid');
if ($cid eq "")
{
	$cid=0;
}

# print out the html page

util::header("Client Group Lookup");

print << "end_of_html";
</TD>
</TR>
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
<TR>
<TD vAlign=top align=left bgColor=#999999>

	<TABLE cellSpacing=0 cellPadding=10 bgColor=#999999 border=0 width="100%">
	<TBODY>
	<TR>
	<TD vAlign=top align=left bgColor=#ffffff colSpan=10>
		<TABLE cellSpacing=0 cellPadding=0 width=760 bgColor=#ffffff border=0>
		<TBODY>
		<tr>
		<td><form method=POST action=clientgroup_lookup.cgi>Client : <select name=cid>
end_of_html
$sql="select user_id,username from user where status='A' order by username";
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($tid,$tname)=$sth->fetchrow_array())
{
	if ($tid == $cid)
	{
		print "<option value=$tid selected>$tname</option>\n";
	}
	else
	{
		print "<option value=$tid>$tname</option>\n";
	}
}
$sth->finish();
print<<"end_of_html";
</select> <input type=submit value="Find"></form>
		</td>
		</tr>
		<tr>
<td align="center" valign="top">
                <a href="mainmenu.cgi">
                <img src="$images/home_blkline.gif" border=0></a></TD>
		</tr>
		<TR>
		<TD><IMG height=15 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>
		<form action=clientgroup_del_mult.cgi method=post>
		<input type=hidden name=cid value=$cid>
		<TABLE cellSpacing=0 cellPadding=3 width="60%" border=0>
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
$sql = "select cg.client_group_id,group_name from ClientGroup cg, ClientGroupClients cgc where cg.client_group_id=cgc.client_group_id and cgc.client_id=$cid and cg.status='A' order by group_name"; 
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($gid,$gname) = $sth->fetchrow_array())
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

	print qq { <TR bgColor=$bgcolor><TD><input type=checkbox name=gid value=$gid></td><TD align=left><font color="#509C10" face="verdana,arial,helvetica,sans serif" size="2"><A HREF="clientgroup_edit.cgi?gid=$gid">$gname</a></font></TD><td align=right><a href="clientgroup_profile.cgi?group_id=$gid">Setup Profiles</a>&nbsp;&nbsp;<a href="clientgroup_copy.cgi?gid=$gid">Copy</a>&nbsp;&nbsp;<input type=button value="Delete" onClick="return Remove($gid);">};
}

$sth->finish();

print << "end_of_html";
		<TR>
		<TD colspan=3><IMG height=7 src="$images/spacer.gif"></TD>
		</TR>
		<TR>
		<TD colspan=3><input type=submit value="Multiple Delete Client Groups"></td>
		</TR>
		</form>
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
