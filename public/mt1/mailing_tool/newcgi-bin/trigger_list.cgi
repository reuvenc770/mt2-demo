#!/usr/bin/perl

# *****************************************************************************************
# trigger_client.cgi
#
# this page allows the setup of trigger settings per client 
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
my $odname;
my $cdname;
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

# print out the html page

util::header("Client Trigger Setup");

print << "end_of_html";
</TD>
</TR>
<TR>
<TD vAlign=top align=left bgColor=#999999>
<script language="JavaScript">
function Remove(gid,ttype)
{
    document.location.href="/cgi-bin/trigger_client_del.cgi?sid="+gid+"&ttype="+ttype;
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
		<td><form method=POST action=trigger_client_add.cgi>
		<table cellSpacing=3>
		<tr><td>Client: </td><td><select name=cid>
end_of_html
my $cid;
my $cname;
$sql="select user_id,first_name from user where status='A' and dd_id=0 order by first_name";
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($cid,$cname)=$sth->fetchrow_array())
{
	print "<option value=$cid>$cname</option>\n";
}
$sth->finish();
print<<"end_of_html";
		</select></td>
		<td>&nbsp;&nbsp;Trigger Setting:</td>
		<td><select name=dd_id>
end_of_html
$sql="select dd_id,name from DailyDealSetting where settingType='Trigger' and dd_id != 0 order by name";
$sth=$dbhu->prepare($sql);
$sth->execute();
my $did;
while (($did,$dname)=$sth->fetchrow_array())
{
	print "<option value=$did>$dname</option>\n";
}
$sth->finish();
print<<"end_of_html";
		</td>
		<td>Trigger Type: <select name=ttype><option value=click>Click</option><option value=conversion>Conversion</option><option value=open>Open</option></select></td>
		<td><input type=submit value="Add"></td></tr></table>
		</form>
		</td>
		</tr>
        <tr><td><form method=POST action="upload_trigger_csv.cgi" encType=multipart/form-data>Trigger CSV File:  <INPUT type=file name="upload_file" size="65">&nbsp;&nbsp;<input type=submit value="Upload"></form></td></tr>
        <tr>
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
		<TD colspan="4" align=center height=15>
			<font face="Verdana,Arial,Helvetica,sans-serif" color="white" size="3">
			<b>Client Trigger Settings</b></font></TD>
		</TR>
		<tr><th>Client</th><th>Trigger Type</th><th>Current Trigger Setting</th><th>Available Setting</th><th></th></tr>
end_of_html

# read info about the lists
my $cid;
my $fname;
my $ddid;
my $oddid;
my $cddid;
$sql = "select u.user_id,first_name,dds.name,dds.dd_id,dds1.name,dds1.dd_id,dds2.name,dds2.dd_id from DailyDealSetting dds, user u,DailyDealSetting dds1,DailyDealSetting dds2  where u.dd_id=dds.dd_id and u.status='A' and u.dd_id_open=dds1.dd_id and u.dd_id_conversion=dds2.dd_id order by first_name"; 
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($cid,$fname,$dname,$ddid,$odname,$oddid,$cdname,$cddid) = $sth->fetchrow_array())
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
	print qq { <form method=post action="trigger_upd_client.cgi"><input type=hidden name=cid value=$cid><input type=hidden name=ttype value=click><TR bgColor=$bgcolor><TD align=left><font color="#509C10" face="verdana,arial,helvetica,sans serif" size="2">$fname</td><td>Click</td><td>$dname</td><td><select name=newid>};
	$sql="select dd_id,name from DailyDealSetting where settingType='Trigger' order by name";
	my $sth1=$dbhu->prepare($sql);
	$sth1->execute();
	my $tid;
	my $tname;
	while (($tid,$tname)=$sth1->fetchrow_array())
	{
		if ($ddid == $tid)
		{
			print "<option selected value=$tid>$tname</option>";
		}
		else
		{
			print "<option value=$tid>$tname</option>";
		}
	}
	$sth1->finish();
	print qq {</select></td><td align=right><input type=submit value=Change><input type=button value="Delete" onClick="return Remove($cid,'click');"</td></TR></form> \n };
	print qq { <form method=post action="trigger_upd_client.cgi"><input type=hidden name=cid value=$cid><input type=hidden name=ttype value=open><TR bgColor=$bgcolor><TD align=left><font color="#509C10" face="verdana,arial,helvetica,sans serif" size="2">$fname</td><td>Open</td><td>$odname</td><td><select name=newid>};
	$sql="select dd_id,name from DailyDealSetting where settingType='Trigger' order by name";
	my $sth1=$dbhu->prepare($sql);
	$sth1->execute();
	my $tid;
	my $tname;
	while (($tid,$tname)=$sth1->fetchrow_array())
	{
		if ($oddid == $tid)
		{
			print "<option selected value=$tid>$tname</option>";
		}
		else
		{
			print "<option value=$tid>$tname</option>";
		}
	}
	$sth1->finish();
	print qq {</select></td><td align=right><input type=submit value=Change><input type=button value="Delete" onClick="return Remove($cid,'open');"</td></TR></form> \n };
	print qq { <form method=post action="trigger_upd_client.cgi"><input type=hidden name=cid value=$cid><input type=hidden name=ttype value=conversion><TR bgColor=$bgcolor><TD align=left><font color="#509C10" face="verdana,arial,helvetica,sans serif" size="2">$fname</td><td>Conversion</td><td>$cdname</td><td><select name=newid>};
	$sql="select dd_id,name from DailyDealSetting where settingType='Trigger' order by name";
	my $sth1=$dbhu->prepare($sql);
	$sth1->execute();
	my $tid;
	my $tname;
	while (($tid,$tname)=$sth1->fetchrow_array())
	{
		if ($cddid == $tid)
		{
			print "<option selected value=$tid>$tname</option>";
		}
		else
		{
			print "<option value=$tid>$tname</option>";
		}
	}
	$sth1->finish();
	print qq {</select></td><td align=right><input type=submit value=Change><input type=button value="Delete" onClick="return Remove($cid,'conversion');"</td></TR></form> \n };
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
