#!/usr/bin/perl

# *****************************************************************************************
# ipgroupprofile_list.cgi
#
# this page displays the list of Ip Group Profiles and lets the user edit / add
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

# print out the html page

util::header("IP Group Profiles");

print << "end_of_html";
</TD>
</TR>
<TR>
<TD vAlign=top align=left bgColor=#999999>
<script language="JavaScript">
function Remove(gid)
{
    if (confirm("Are you sure you want to Delete this Ip Group Profile?"))
    {
        document.location.href="/cgi-bin/ipgroupprofile_del.cgi?gid="+gid;
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
		<td><a href="ipgroupprofile_edit.cgi?gid=0"><img src=/images/add.gif border=0></a>&nbsp;&nbsp;&nbsp;<a href="run_bldipgroup.cgi">Run Build IPGroups</a></td>
		<td>
                <a href="mainmenu.cgi">
                <img src="$images/home_blkline.gif" border=0></a></TD>
		</tr>
		<tr><td><img src=/images/spacer.gif height=10></td></tr>
		<TR>
		<TD><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
			Select a IP Group profile to edit or add a new IP Group Profile</FONT></TD>
		</TR>
		<TR>
		<TD><IMG height=15 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>
		<TABLE cellSpacing=0 cellPadding=3 width="50%" border=0>
		<TBODY>
		<TR bgColor="#509C10" height=15>
		<TD colspan="3" align=center height=15>
			<font face="Verdana,Arial,Helvetica,sans-serif" color="white" size="3">
			<b>IP Group Profiles</b></font></TD>
		</TR>
end_of_html

# read info about the lists

my $gid;
my $gname;
my $ptype;
$sql = "select IpProfileID,profileName,pType from IpGroupProfile where profileStatus='Active' order by profileName"; 
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($gid,$gname,$ptype) = $sth->fetchrow_array())
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

	print qq { <TR bgColor=$bgcolor><TD align=left><font color="#509C10" face="verdana,arial,helvetica,sans serif" size="2"><A HREF="ipgroupprofile_edit.cgi?gid=$gid">$gname</a><td>$ptype</td><td align=right><input type=button value="Delete" onClick="return Remove($gid);"</td></font></TD></TR> \n };
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
