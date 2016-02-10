#!/usr/bin/perl

# *****************************************************************************************
# deploypool.cgi
#
# this page displays the list of Deploy Pools and lets the user edit / add
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
my ($poolid,$pname,$cgname,$profilename,$recs,$totalcnt);
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

# print out the html page

util::header("Deploy Pools");

print << "end_of_html";
</TD>
</TR>
<TR>
<TD vAlign=top align=left bgColor=#999999>
<script language="JavaScript">
function Remove(gid)
{
	if (confirm("Are you sure you want to Delete this Deploy Pool?"))
    {
    	document.location.href="/cgi-bin/deploypool_del.cgi?gid="+gid;
        return true;
    }
	return false;
}
function popup(pid)
{
    var newwin = window.open("/cgi-bin/deploypool_chunk.cgi?pid="+pid,"PoolChunk", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,width=700,height=300,left=25,top=50");
    newwin.focus();
}
</script>

	<TABLE cellSpacing=0 cellPadding=10 bgColor=#999999 border=0 width="100%">
	<TBODY>
	<TR>
	<TD vAlign=top align=left bgColor=#ffffff colSpan=10>
		<form method=post action=deploypool_add.cgi>
		<TABLE cellSpacing=0 cellPadding=0 width=860 bgColor=#ffffff border=0>
		<TBODY>
		<tr>
		<td>Pool Name: <input type=text name=pname size=25 maxlength=25>&nbsp;&nbsp;Client Group: <select name=gid>
end_of_html
$sql="select client_group_id,group_name from ClientGroup where status='A' order by group_name";
$sth=$dbhu->prepare($sql);
$sth->execute();
my $cid;
my $cname;
while (($cid,$cname)=$sth->fetchrow_array())
{
	print "<option value=$cid>$cname</option>";
}
$sth->finish();
print "</select></td></tr><tr><td>Profile: <select name=profileid>";
$sql="select profile_id,profile_name from UniqueProfile where status='A' order by profile_name";
$sth=$dbhu->prepare($sql);
$sth->execute();
my $pid;
my $pname;
while (($pid,$pname)=$sth->fetchrow_array())
{
	print "<option value=$pid>$pname</option>";
}
$sth->finish();
print "</select>&nbsp;&nbsp;Chunk Size: <input type=text name=chunksize size=10>";
print<<"end_of_html";
&nbsp;<input type=submit value="Add Deploy Pool">
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
		</form>
		<TABLE cellSpacing=0 cellPadding=3 width="100%" border=0>
		<TBODY>
		<TR bgColor="#509C10" height=15>
		<th>Pool name</th><th>Client Group</th><th>Profile</th><th>Chunk Size</th><th>Total Chunks</th><th>Last Chunk Size</th><th></th></tr>
		</TR>
end_of_html

$sql="select deployPoolID,deployPoolName,cg.group_name,up.profile_name,recsPerChunk,totalChunks,lastChunkSize from DeployPool dp,ClientGroup cg, UniqueProfile up  where dp.client_group_id=cg.client_group_id and dp.profile_id=up.profile_id and dp.status='Active' order by deployPoolName";
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($poolid,$pname,$cgname,$profilename,$recs,$totalcnt,$lastChunkSize) = $sth->fetchrow_array())
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
	print "<tr bgcolor=$bgcolor><td><a href=\"deploypool_edit.cgi?pid=$poolid\">$pname</a></td><td>$cgname</td><td>$profilename</td><td align=center>$recs</td><td align=center>$totalcnt</td><td>$lastChunkSize</td><td><a href=\"javascript:popup($poolid);\">Chunk Info</a>&nbsp;&nbsp;<input type=button value=Delete onClick=\"return Remove($poolid);\"></td></tr>";
}

$sth->finish();

print << "end_of_html";
		<TR>
		<TD colspan=3><IMG height=7 src="$images/spacer.gif"></TD>
		</TR>
		<TR>
		<TD colspan=3>

			<table cellpadding="0" cellspacing="0" border="0" width="80%">
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
