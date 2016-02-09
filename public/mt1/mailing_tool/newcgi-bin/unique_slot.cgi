#!/usr/bin/perl

# *****************************************************************************************
# unique_slot.cgi
#
# this page displays the list of UniqueSlot and lets the user edit / add
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
my @ENC=("None","ISO","UTF8-Q","UTF8-B","UTF-7","ASCII","UTF-16","UTF-8 HOT","ISO B","UTF-8Q HOT","UTF-7 HOT","ASCII HOT","UTF-16 HOT","ISO B HOT","ISO Q HOT");
my @ENC1=("","ISO","UTF8","UTF8-B","UTF-7","ASCII","UTF-16","UTF-8 HOT","ISO B","UTF-8Q HOT","UTF-7 HOT","ASCII HOT","UTF-16 HOT","ISO B HOT","ISO Q HOT");

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

util::header("Unique Slots");

print << "end_of_html";
</TD>
</TR>
<TR>
<TD vAlign=top align=left bgColor=#999999>
<script language="JavaScript">
function Remove(gid)
{
    if (confirm("Are you sure you want to Delete this Slot?"))
    {
        document.location.href="/cgi-bin/unique_slot_del.cgi?sid="+gid;
        return true;
    }
    return false;
}
</script>
	<TABLE cellSpacing=0 cellPadding=10 bgColor=#999999 border=0 width="100%">
	<TBODY>
	<TR>
	<TD vAlign=top align=left bgColor=#ffffff colSpan=10>
		<TABLE cellSpacing=0 cellPadding=0 width=900 bgColor=#ffffff border=0>
		<TBODY>
		<tr>
		<td><form method=POST action=unique_slot_add.cgi>
		<table cellSpacing=3>
		<tr><td>Time: </td><td><select name=shour>
end_of_html
my $j=1;
while ($j <= 12)
{
	if ($j == 12)
	{
    	print "<option selected value=$j>$j</option>\n";
	}
	else
	{
    	print "<option value=$j>$j</option>\n";
	}
    $j++;
}
print "</select><select name=smin>";
my $j=0;
my $tj;
while ($j <= 59)
{
	if ($j < 10)
	{
		$tj="0".$j;
	}
	if ($j == 1)
	{
    	print "<option selected value=$j>$tj</option>\n";
	}
	else
	{
    	print "<option value=$j>$j</option>\n";
	}
    $j++;
}
print "</select><select name=am_pm>\n";
print "<option value=\"AM\">AM</option>\n";
print "<option value=\"PM\">PM</option>\n";
print<<"end_of_html";
</select>
</td>
		<td>Stop Time: </td><td><select name=stophour>
end_of_html
my $j=0;
while ($j <= 12)
{
	if ($j == 0)
	{
    	print "<option selected value=$j>$j</option>\n";
	}
	else
	{
    	print "<option value=$j>$j</option>\n";
	}
    $j++;
}
print "</select><select name=stopmin>";
my $j=0;
my $tj;
while ($j <= 59)
{
	if ($j < 10)
	{
		$tj="0".$j;
	}
	if ($j == 0)
	{
    	print "<option selected value=$j>$tj</option>\n";
	}
	else
	{
    	print "<option value=$j>$j</option>\n";
	}
    $j++;
}
print "</select><select name=stop_am_pm>\n";
print "<option value=\"AM\">AM</option>\n";
print "<option value=\"PM\">PM</option>\n";
print<<"end_of_html";
</select>
</td>
<td>&nbsp;&nbsp;Client Group:</td>
		<td><select name=cgroupid>
end_of_html
my $cid;
my $cname;
$sql="select client_group_id,group_name from ClientGroup where status='A' order by group_name";
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($cid,$cname)=$sth->fetchrow_array())
{
	print "<option value=$cid>$cname</option>\n";
}
$sth->finish();
print<<"end_of_html";
		</select></td>
		<td>&nbsp;&nbsp;IP Group:</td>
		<td><select name=igroupid>
end_of_html
$sql="select group_id,group_name from IpGroup where status='Active' order by group_name";
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($cid,$cname)=$sth->fetchrow_array())
{
	print "<option value=$cid>$cname</option>\n";
}
$sth->finish();
print<<"end_of_html";
		</select></td></tr>
		<tr><td>&nbsp;&nbsp;Profile:</td>
		<td><select name=pid>
end_of_html
$sql="select profile_id,profile_name from UniqueProfile where status='A' order by profile_name";
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($cid,$cname)=$sth->fetchrow_array())
{
	print "<option value=$cid>$cname</option>\n";
}
$sth->finish();
print<<"end_of_html";
		</select></td>
		<td>&nbsp;&nbsp;Domain:</td>
		<td><select name=dname multiple="multiple" size=5>
end_of_html
$sql="select brand_id from client_brand_info where client_id=64 and status='A' and nl_id=?";
$sth = $dbhq->prepare($sql) ;
$sth->execute($nl_id);
($bid)=$sth->fetchrow_array();
$sth->finish();
#$sql="select distinct domain from brand_available_domains where brandID=? and domain != 'arthuradvertising.com' and domain not in (select domain from DomainExclusion) union select distinct url from brand_url_info where brand_id=? and url_type in ('O','Y') and url not in (select domain from DomainExclusion) order by 1";
$sql="select distinct domain from brand_available_domains where brandID=? and domain != 'arthuradvertising.com' union select distinct url from brand_url_info where brand_id=? and url_type in ('O','Y') order by 1";
$sth=$dbhu->prepare($sql);
$sth->execute($bid,$bid);
while (($dname)=$sth->fetchrow_array())
{
	print "<option value=$dname>$dname</option>\n";
}
$sth->finish();
print<<"end_of_html";
		</select></td>
		<td>Paste Domains:</td>
		<td><textarea name=pdname cols=40 rows=5></textarea></td>
</tr><tr>
		<td>&nbsp;&nbsp;Content Domain:</td>
		<td><select name=cdname multiple="multiple" size=5>
end_of_html
$sql="select domain from brand_available_domains where brandID =5197 order by domain";
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($dname)=$sth->fetchrow_array())
{
	print "<option value=$dname>$dname</option>\n";
}
$sth->finish();
print<<"end_of_html";
		</select></td>
		<td>Paste Domains:</td>
		<td><textarea name=cpdname cols=40 rows=5></textarea></td>
</tr><tr>
			<td>&nbsp;&nbsp;Template:</td>
			<td>
				<select name="template_id">
end_of_html
my $old_template_id=1;
$sql="select template_id,template_name from brand_template where status='A' order by template_name";
$sth=$dbhq->prepare($sql);
$sth->execute();
my $tname;
while (($template_id,$tname)=$sth->fetchrow_array())
{
	if ($old_template_id == $template_id)
	{
		print "<option selected value=$template_id>$tname</option>\n";
	}
	else
	{
		print "<option value=$template_id>$tname</option>\n";
	}
}
$sth->finish();
print<<"end_of_html";
				</select></td>
		<td>Type:</td><td><select name=utype><option value="Normal" selected>Normal</option><option value="Time Based">Time Based</option><option value="Hotmail">Hotmail</option><option value="Chunking">Chunking</option><option value="Hotmail Domain">Hotmail Domain</option><option value="NEW DEPLOY">NEW DEPLOY</option><option vallue="TEST">TEST</option></select></td>
		<td colspan=2>Randomize:&nbsp;<input type=radio name=randomize value=Y>Yes&nbsp;<input type=radio name=randomize checked value=N>No</td>
		<td colspan=2>Hour(s) to Add(Time Based only):&nbsp;<select name=chour>
end_of_html
my $k=0;
while ($k <= 23)
{
	print "<option value=$k>$k</option>";
	$k++;
}
print<<"end_of_html";
</select></td>
</tr>
<tr>
		<td>MTA Setting</td><td><select name=mtaid>
end_of_html
$sql="select mta_id,name from mta order by name"; 
$sth=$dbhq->prepare($sql);
$sth->execute();
my $tname;
my $mta_id;
while (($mta_id,$tname)=$sth->fetchrow_array())
{
	if ($mta_id == 11)
	{
		print "<option selected value=$mta_id>$tname</option>\n";
	}
	else
	{
		print "<option value=$mta_id>$tname</option>\n";
	}
}
$sth->finish();
print<<"end_of_html";
				</select></td>
	    <td>Mail From: <input type=text name=mail_from size=20></td>
		<td>Use Master:&nbsp;<input type=radio name=use_master value=Y>Yes&nbsp;<input type=radio name=use_master checked value=N>No</td>
		<td colspan=2>Use Rdns:&nbsp;<input type=radio name=useRdns value=Y>Yes&nbsp;<input type=radio name=useRdns checked value=N>No</td>
	    <td colspan=2>Return Path: <input type=text name=return_path size=50></td>
</tr><tr>
        <td colspan=2>Prepull:&nbsp;<input type=radio name=prepull value=Y>Yes&nbsp;<input type=radio name=prepull checked value=N>No</td>
		<td>Subject Encoding:</td><td><select name=ConvertSubject>
end_of_html
my $i=0;
while ($i <= $#ENC)
{
	print qq^<option value="$ENC1[$i]">$ENC[$i]</option>^;
	$i++;
}
print<<"end_of_html";
	</select></td>
		<td>From Encoding:</td><td><select name=ConvertFrom>
end_of_html
my $i=0;
while ($i <= $#ENC)
{
	print qq^<option value="$ENC1[$i]">$ENC[$i]</option>^;
	$i++;
}
print<<"end_of_html";
	</select></td></tr>
	<tr>
		<td>Apply Jlog Profile:</td><td><select name=jlogProfileID><option value=0>None</option>
end_of_html
$sql="select profileID,profileName from EmailEventHandlerProfile order by profileName";
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($cid,$cname)=$sth->fetchrow_array())
{
	print "<option value=$cid>$cname</option>";
}
$sth->finish();
print<<"end_of_html";
		</select></td>
		<td><input type=submit value="Add Slot"></td></tr></table>
		</form>
		</td>
		</tr>
        <tr><td><form method=POST action="upload_unqslot.cgi" encType=multipart/form-data>Unique Slot CSV File:  <INPUT type=file name="upload_file" size="65">&nbsp;&nbsp;<input type=submit value="Upload"></form></td></tr>
		<tr>
<td align="center" valign="top">
                <a href="mainmenu.cgi">
                <img src="$images/home_blkline.gif" border=0></a></TD>
		</tr>
		<TR>
		<TD><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
			Select a Slot to edit or add a new Slot</FONT></TD>
		</TR>
		<TR>
		<TD><IMG height=15 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>
		<TABLE cellSpacing=0 cellPadding=3 width="100%" border=0>
		<TBODY>
		<TR bgColor="#509C10" height=15>
		<TD colspan="12" align=center height=15>
			<font face="Verdana,Arial,Helvetica,sans-serif" color="white" size="3">
			<b>Unique Slots</b></font></TD>
		</TR>
		<tr><th>Time</th><th>Stop Time</th><th>Client Group</th><th>IP Group</th><th>Profile</th><th>Domain</th><th>Content Domain</th><th>MTA Setting</th><th>Template</th><th>Type</th><th>Randomize</th><th>Logging</th></tr>
end_of_html

# read info about the lists

my $sid;
my $gname;
my $ipname;
my $pname;
my $stime;
my $etime;
my $slot_type;
my $log_camp;
my $randomize;
my $mname;
$sql = "select slot_id,cg.group_name,ip.group_name,schedule_time,end_time,up.profile_name,mailing_domain,template_id,slot_type,log_campaign,randomize_records,mta.name from UniqueSlot,ClientGroup cg,IpGroup ip,UniqueProfile up,mta  where UniqueSlot.client_group_id=cg.client_group_id and UniqueSlot.ip_group_id=ip.group_id and UniqueSlot.status='A' and UniqueSlot.profile_id=up.profile_id and UniqueSlot.mta_id=mta.mta_id order by cg.group_name,slot_id"; 
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($sid,$gname,$ipname,$stime,$etime,$pname,$dname,$template_id,$slot_type,$log_camp,$randomize,$mname) = $sth->fetchrow_array())
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
	my $template_name;

	$sql="select template_name from brand_template where template_id=?";
	my $sth1=$dbhq->prepare($sql);
	$sth1->execute($template_id);
	($template_name)=$sth1->fetchrow_array();
	$sth1->finish();

	my $dname_str;
	$dname_str="";
	$sql="select mailing_domain from UniqueSlotDomain where slot_id=?";
	$sth1=$dbhq->prepare($sql);
	$sth1->execute($sid);
	while (($dname)=$sth1->fetchrow_array())
	{
		$dname_str=$dname_str.$dname." ";
	}
	$sth1->finish();
	chop($dname_str);

	my $cdname_str;
	$cdname_str="";
	$sql="select domain_name from UniqueSlotContentDomain where slot_id=?";
	$sth1=$dbhq->prepare($sql);
	$sth1->execute($sid);
	while (($dname)=$sth1->fetchrow_array())
	{
		$cdname_str=$cdname_str.$dname." ";
	}
	$sth1->finish();
	chop($cdname_str);
	if ($etime eq "00:00:00")
	{
		$etime="";
	}

	print qq { <TR bgColor=$bgcolor><TD align=left><font color="#509C10" face="verdana,arial,helvetica,sans serif" size="2"><a href="unique_slot_edit.cgi?sid=$sid">$stime</a></td><td>$etime</td><td>$gname</td><td>$ipname</td><td>$pname</td><td>$dname_str</td><td>$cdname_str</td><td>$mname</td><td>$template_name</td><td>$slot_type</td><td>$randomize</td><td>$log_camp</td><td align=right><input type=button value="Delete" onClick="return Remove($sid);"</td></TR> \n };
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
