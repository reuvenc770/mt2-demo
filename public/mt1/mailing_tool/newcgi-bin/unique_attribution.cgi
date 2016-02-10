#!/usr/bin/perl

# *****************************************************************************************
# unique_attribution.cgi
#
# this page allows the setup of unique attribution 
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
my $template_id;
my $refurl;
my $bgcolor;
my $reccnt;
my $bid;
my $dname;
my $nl_id=14;
my $images = $pms->get_images_url;
my $alt_light_table_bg = $pms->get_alt_light_table_bg;
my $light_table_bg = $pms->get_light_table_bg;
my $table_text_color = $pms->get_table_text_color;
my $status_name;
my $status;
my $cat_id;
my $domain_name;
my $old_super_client;
my $old_super_profile;

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
my $setAttribution;
my $ctime;
$sql = "select username, setAttribution,now() from UserAccounts where user_id = ?";
$sth = $dbhq->prepare($sql) ;
$sth->execute($user_id);
($username, $setAttribution,$ctime) = $sth->fetchrow_array();
$sth->finish();
if ($setAttribution eq "N")
{
	open(LOG2,">>/tmp/attribution.log");
    print LOG2 "$ctime - $username\n";
    close(LOG2);
    print "Content-type: text/html\n\n";
    print<<"end_of_html";
<html><head><title>Attribution Error</title></head>
<body>
<center><h3>You do not have permission to set Client Attribution.  This attempt has been logged.</h3><br>
<a href="/cgi-bin/mainmenu.cgi"><img src="/images/home_blkline.gif" border=0></a>
</center>
</body>
</html>
end_of_html
	exit(0);
}

# print out the html page

util::header("Client Attribution Setup");

print << "end_of_html";
</TD>
</TR>
<TR>
<TD vAlign=top align=left bgColor=#999999>
<script language="JavaScript">
function Remove(gid)
{
    document.location.href="/cgi-bin/unique_attribution_del.cgi?sid="+gid;
    return true;
}
</script>
	<TABLE cellSpacing=0 cellPadding=10 bgColor=#999999 border=0 width="100%">
	<TBODY>
	<TR>
	<TD vAlign=top align=left bgColor=#ffffff colSpan=10>
		<TABLE cellSpacing=0 cellPadding=0 width=900 bgColor=#ffffff border=0>
		<TBODY>
		<tr>
		<td><form method=POST action=unique_attribution_add.cgi>
		<table cellSpacing=3>
		<tr><td>Client: </td><td><select name=cid>
end_of_html
$sql="select parmval from sysparm where parmkey='SUPER_CLIENT_GROUP'";
$sth=$dbhu->prepare($sql);
$sth->execute();
($old_super_client)=$sth->fetchrow_array();
$sth->finish();
$sql="select parmval from sysparm where parmkey='SUPER_CLIENT_PROFILE'";
$sth=$dbhu->prepare($sql);
$sth->execute();
($old_super_profile)=$sth->fetchrow_array();
$sth->finish();

my $cid;
my $cname;
$sql="select user_id,username from user where status='A' and user_id not in (select client_id from UniqueAttribution) order by username";
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($cid,$cname)=$sth->fetchrow_array())
{
	print "<option value=$cid>$cname</option>\n";
}
$sth->finish();
print<<"end_of_html";
		</select></td>
		<td>&nbsp;&nbsp;Level:</td>
		<td><input type=text size=3 maxlength=3 name=level></td>
		<td><input type=submit value="Add"></td></tr></table>
		</form>
		</td>
		</tr>
		<tr>
		<td><form method=POST action=unique_attribution_super_upd.cgi>
		<table cellSpacing=3>
		<tr><td>Super Client Group: </td><td><select name=cid><option value=0>None</option>
end_of_html
my $cid;
my $cname;
$sql="select client_group_id,group_name from ClientGroup where status='A' order by group_name";
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($cid,$cname)=$sth->fetchrow_array())
{
	if ($cid == $old_super_client)
	{
		print "<option selected value=$cid>$cname</option>\n";
	}
	else
	{
		print "<option value=$cid>$cname</option>\n";
	}
}
$sth->finish();
print<<"end_of_html";
		</select></td>
		<td>&nbsp;&nbsp;Profile:</td><td><select name=pid><option selected value=0>None</option>
end_of_html
my $pid;
my $pname;
$sql="select profile_id,profile_name from UniqueProfile where status='A' order by profile_name";
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($pid,$pname)=$sth->fetchrow_array())
{
	if ($pid == $old_super_profile)
	{
		print "<option selected value=$pid>$pname</option>\n";
	}
	else
	{
		print "<option value=$pid>$pname</option>\n";
	}
}
$sth->finish();
print<<"end_of_html";
		</select></td></tr>
		<tr><td>ESPs(check to turn on super client attribution):</td><td colspan=2>
end_of_html
my $espID;
my $espName;
my $espSuper;
my $checkstr;
$sql="select espID,espName,espSuperAttributionActive from ESP order by espName";
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($espID,$espName,$espSuper)=$sth->fetchrow_array())
{
	$checkstr="";
	if ($espSuper eq "Y")
	{
		$checkstr="checked";
	}
	print "$espName&nbsp;<input type=checkbox value=$espID $checkstr name=esp>&nbsp;&nbsp;\n";
}
$sth->finish();

print<<"end_of_html";
		</td>
		<td><input type=submit value="Update"></td></tr></table>
		</form>
		</td>
		</tr>
		<tr><td>
<form method="post" action="upload_attribution.cgi" encType=multipart/form-data>
Attribution File: <input type=file name=upload_file>&nbsp;&nbsp;<input type=submit value=Load>
</form></td></tr>
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
		<TABLE cellSpacing=0 cellPadding=3 width="70%" border=0>
		<TBODY>
		<TR bgColor="#509C10" height=15>
		<TD colspan="3" align=center height=15>
			<font face="Verdana,Arial,Helvetica,sans-serif" color="white" size="3">
			<b>Client Attributions</b></font></TD>
		</TR>
		<tr><th>Client</th><th>Level</th><th></th></tr>
end_of_html

# read info about the lists
my $cid;
my $fname;
my $level;
$sql = "select ua.client_id,username,level from UniqueAttribution ua, user where ua.client_id=user.user_id order by level"; 
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($cid,$fname,$level) = $sth->fetchrow_array())
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
	print qq { <TR bgColor=$bgcolor><TD align=left><font color="#509C10" face="verdana,arial,helvetica,sans serif" size="2">$fname</td><td>$level</td><td align=right><input type=button value="Delete" onClick="return Remove($cid);"</td></TR> \n };
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
