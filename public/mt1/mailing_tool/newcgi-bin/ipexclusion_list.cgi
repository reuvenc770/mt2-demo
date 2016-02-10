#!/usr/bin/perl

# *****************************************************************************************
# ipexlusion_list.cgi
#
# this page displays the list of Ip Exclusion lists 
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

util::header("IP Exclusions");

print << "end_of_html";
</TD>
</TR>
<TR>
<TD vAlign=top align=left bgColor=#999999>
<script language="JavaScript">
function Remove(gid)
{
    if (confirm("Are you sure you want to Delete this Ip Exclusion?"))
    {
        document.location.href="/cgi-bin/ipexclusion_del.cgi?gid="+gid;
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
        <td><form method=POST action=ipexclusion_add.cgi>IP Exclusion Name: <input type=text name=ipname size=25 maxlength=25>&nbsp;&nbsp;&nbsp;<input type=submit value="Add IP Exclusion"></form>
        </td>
        </tr>
		<tr>
<td align="center" valign="top">
                <a href="mainmenu.cgi">
                <img src="$images/home_blkline.gif" border=0></a></TD>
		</tr>
		<TR>
		<TD><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
			Select a IP Exclusion to edit or add a new IP Exclusion</FONT></TD>
		</TR>
		<TR>
		<TD><IMG height=15 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>
		<TABLE cellSpacing=0 cellPadding=3 width="50%" border=0>
		<TBODY>
		<TR bgColor="#509C10" height=15>
		<TD colspan="2" align=center height=15>
			<font face="Verdana,Arial,Helvetica,sans-serif" color="white" size="3">
			<b>IP Exclusions</b></font></TD>
		</TR>
end_of_html

# read info about the lists

my $gid;
my $gname;
$sql = "select IpExclusionID,IpExclusion_name from IpExclusion order by IpExclusion_name"; 
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($gid,$gname) = $sth->fetchrow_array())
{
	if (($gid == 1) and ($user_id != 17) and ($user_id != 23) and ($user_id != 2))
	{
		next;
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
	if ($gid != 1)
	{
		print qq { <TR bgColor=$bgcolor><TD align=left><font color="#509C10" face="verdana,arial,helvetica,sans serif" size="2"><A HREF="ipexclusion_edit.cgi?gid=$gid">$gname</a><td align=right><input type=button value="Delete" onClick="return Remove($gid);"></td></font></TD></TR> \n };
	}
	else
	{
		print qq { <TR bgColor=$bgcolor><TD align=left><font color="#509C10" face="verdana,arial,helvetica,sans serif" size="2"><A HREF="ipexclusion_edit.cgi?gid=$gid">$gname</a><td align=right></td></font></TD></TR> \n };
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
