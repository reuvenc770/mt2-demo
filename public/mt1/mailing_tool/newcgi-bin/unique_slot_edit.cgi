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
my $utype;
my $chour;
my $randomize;
my $use_master;
my $useRdns;
my $link_id;
my $refurl;
my $chk;
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
my $mta_id;
my $sid=$query->param('sid');
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

my $shour;
my $stophour;
my $smin;
my $stopmin;
my $gid;
my $iid;
my $pid;
my $gcnt;
my $dname;
my $old_template_id;
my $log_camp;
my $source_url;
my $mail_from;
my $return_path;
my $prepull;
my $ConvertSubject;
my $ConvertFrom;
my $jlogProfileID;
my $zip;
$sql="select hour(schedule_time),hour(end_time),minute(schedule_time),minute(end_time),client_group_id,ip_group_id,profile_id,mailing_domain,template_id,slot_type,hour_offset,log_campaign,randomize_records,mta_id,source_url,zip,mail_from,use_master,useRdns,return_path,prepull,ConvertSubject,ConvertFrom,jlogProfileID from UniqueSlot where slot_id=?";
$sth=$dbhu->prepare($sql);
$sth->execute($sid);
($shour,$stophour,$smin,$stopmin,$gid,$iid,$pid,$dname,$old_template_id,$utype,$chour,$log_camp,$randomize,$mta_id,$source_url,$zip,$mail_from,$use_master,$useRdns,$return_path,$prepull,$ConvertSubject,$ConvertFrom,$jlogProfileID)=$sth->fetchrow_array();
$sth->finish();
if ($zip eq "ALL")
{
	$zip="";
}

$sql="select count(*) from ClientGroupClients where client_group_id=?";
$sth=$dbhu->prepare($sql);
$sth->execute($gid);
($gcnt)=$sth->fetchrow_array();
$sth->finish();

$sql="select mailing_domain from UniqueSlotDomain where slot_id=?";
$sth=$dbhu->prepare($sql);
$sth->execute($sid);
my $arr=$sth->fetchall_arrayref();
$sth->finish();
$sql="select domain_name from UniqueSlotContentDomain where slot_id=?";
$sth=$dbhu->prepare($sql);
$sth->execute($sid);
my $carr=$sth->fetchall_arrayref();
$sth->finish();

# print out the html page

util::header("Update Unique Slot");

print << "end_of_html";
</TD>
</TR>
<TR>
<TD vAlign=top align=left bgColor=#999999>
	<TABLE cellSpacing=0 cellPadding=10 bgColor=#999999 border=0 width="100%">
	<TBODY>
	<TR>
	<TD vAlign=top align=left bgColor=#ffffff colSpan=10>
		<TABLE cellSpacing=0 cellPadding=0 width=100% bgColor=#ffffff border=0>
		<TBODY>
		<tr>
		<td><form method=POST action=unique_slot_upd.cgi>
		<input type=hidden name=sid value=$sid>
		<table cellSpacing=3>
		<tr><td>Time: </td><td><select name=shour>
end_of_html
my $j=1;
my $thour;
if ($shour == 24)
{
	$thour=12;
}
elsif ($shour > 12)
{
	$thour=$shour-12;
}
else
{
	if ($shour == 0)
	{
		$thour=12;
	}
	else
	{
		$thour=$shour;
	}
}
while ($j <= 12)
{
	if ($j == $thour)
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
	$tj=$j;
	if ($j < 10)
	{
		$tj="0".$j;
	}
	
	if ($j == $smin)
	{
    	print "<option selected value=$j>$tj</option>\n";
	}
	else
	{
    	print "<option value=$j>$tj</option>\n";
	}
    $j++;
}
print "</select><select name=am_pm>\n";
if ($shour >= 12)
{
	print "<option value=\"AM\">AM</option>\n";
	print "<option selected value=\"PM\">PM</option>\n";
}
elsif ($shour == 0)
{
	print "<option selected value=\"AM\">AM</option>\n";
	print "<option value=\"PM\">PM</option>\n";
}
else
{
	print "<option selected value=\"AM\">AM</option>\n";
	print "<option value=\"PM\">PM</option>\n";
}
print<<"end_of_html";
</select>
</td>
		<td>Stop Time: </td><td><select name=stophour>
end_of_html
my $j=0;
my $thour;
if ($stophour == 24)
{
	$thour=12;
}
elsif ($stophour > 12)
{
	$thour=$stophour-12;
}
else
{
	$thour=$stophour;
}
while ($j <= 12)
{
	if ($j == $thour)
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
	$tj=$j;
	if ($j < 10)
	{
		$tj="0".$j;
	}
	if ($j == $stopmin)
	{
    	print "<option selected value=$j>$tj</option>\n";
	}
	else
	{
    	print "<option value=$j>$tj</option>\n";
	}
    $j++;
}
print "</select><select name=stop_am_pm>\n";
if ($stophour >= 12)
{
	print "<option value=\"AM\">AM</option>\n";
	print "<option selected value=\"PM\">PM</option>\n";
}
elsif ($stophour == 0)
{
	print "<option selected value=\"AM\">AM</option>\n";
	print "<option value=\"PM\">PM</option>\n";
}
else
{
	print "<option selected value=\"AM\">AM</option>\n";
	print "<option value=\"PM\">PM</option>\n";
}
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
	if ($cid == $gid)
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
		<td>&nbsp;&nbsp;IP Group:</td>
		<td><select name=igroupid>
end_of_html
$sql="select group_id,group_name from IpGroup where status='Active' order by group_name";
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($cid,$cname)=$sth->fetchrow_array())
{
	if ($cid == $iid)
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
		</tr></tr>
		<td>&nbsp;&nbsp;Profile:</td>
		<td><select name=pid>
end_of_html
$sql="select profile_id,profile_name from UniqueProfile where status='A' order by profile_name";
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($cid,$cname)=$sth->fetchrow_array())
{
	if ($cid == $pid)
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
		<td>&nbsp;&nbsp;Domain:</td>
		<td><select name=dname size=5 multiple="multiple">
end_of_html
my $nl_id=14;
my $tdname;
my $bid;
$sql="select brand_id from client_brand_info where client_id=64 and status='A' and nl_id=?";
$sth = $dbhq->prepare($sql) ;
$sth->execute($nl_id);
($bid)=$sth->fetchrow_array();
$sth->finish();
$sql="select distinct domain from brand_available_domains where brandID=? and domain != 'arthuradvertising.com' union select distinct url from brand_url_info where brand_id=? and url_type in ('O','Y') order by 1";
$sth=$dbhu->prepare($sql);
$sth->execute($bid,$bid);
while (($tdname)=$sth->fetchrow_array())
{
	$chk=check_domain($arr,$tdname);
	if ($chk)
	{
		print "<option selected value=$tdname>$tdname</option>\n";
	}
	else
	{
		print "<option value=$tdname>$tdname</option>\n";
	}
}
$sth->finish();
print<<"end_of_html";
		</select></td>
		<td>Paste Domains:</td>
		<td><textarea name=pdname rows=5 cols=40></textarea></td>
		</tr><tr>
		<td>&nbsp;&nbsp;Content Domain:</td>
		<td><select name=cdname size=5 multiple="multiple">
end_of_html
my $tdname;
$sql="select distinct domain from brand_available_domains where brandID=5197 order by domain"; 
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($tdname)=$sth->fetchrow_array())
{
	$chk=check_domain($carr,$tdname);
	if ($chk)
	{
		print "<option selected value=$tdname>$tdname</option>\n";
	}
	else
	{
		print "<option value=$tdname>$tdname</option>\n";
	}
}
$sth->finish();
print<<"end_of_html";
		</select></td>
		<td>Paste Domains:</td>
		<td><textarea name=cpdname rows=5 cols=40></textarea></td>
</tr></tr>
			<td>&nbsp;&nbsp;Template:</td>
			<td>
				<select name="template_id">
end_of_html
$sql="select template_id,template_name from brand_template where status='A' order by template_name";
$sth=$dbhq->prepare($sql);
$sth->execute();
my $tname;
my $template_id;
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
<td colspan=2>Type: &nbsp;&nbsp;<select name=utype>
end_of_html
if ($utype eq "Normal")
{
	$chour=0;
	print "<option value=Normal selected>Normal</option><option value=\"Time Based\">Time Based</option></option><option value=Hotmail>Hotmail</option><option value=Chunking>Chunking</option><option value=\"Hotmail Domain\">Hotmail Domain</option><option value=\"NEW DEPLOY\">NEW DEPLOY</option><option value=TEST>TEST</option>\n";
}
elsif ($utype eq "Time Based")
{
	print "<option value=Normal>Normal</option><option value=\"Time Based\" selected>Time Based</option><option value=Hotmail>Hotmail</option><option value=Chunking>Chunking</option><option value=\"Hotmail Domain\">Hotmail Domain</option><option value=\"NEW DEPLOY\">NEW DEPLOY</option><option value=TEST>TEST</option>\n";
}
elsif ($utype eq "Hotmail")
{
	print "<option value=Normal>Normal</option><option value=\"Time Based\" >Time Based</option><option selected value=Hotmail>Hotmail</option><option value=Chunking>Chunking</option><option value=\"Hotmail Domain\">Hotmail Domain</option><option value=\"NEW DEPLOY\">NEW DEPLOY</option><option value=TEST>TEST</option>\n";
}
elsif ($utype eq "Chunking")
{
	print "<option value=Normal>Normal</option><option value=\"Time Based\">Time Based</option><option value=Hotmail>Hotmail</option><option selected value=Chunking>Chunking</option><option value=\"Hotmail Domain\">Hotmail Domain</option><option value=\"NEW DEPLOY\">NEW DEPLOY</option><option value=TEST>TEST</option>\n";
}
elsif ($utype eq "Hotmail Domain")
{
	print "<option value=Normal>Normal</option><option value=\"Time Based\">Time Based</option><option value=Hotmail>Hotmail</option><option value=Chunking>Chunking</option><option selected value=\"Hotmail Domain\">Hotmail Domain</option><option value=\"NEW DEPLOY\">NEW DEPLOY</option><option value=TEST>TEST</option>\n";
}
elsif ($utype eq "NEW DEPLOY")
{
	print "<option value=Normal>Normal</option><option value=\"Time Based\">Time Based</option><option value=Hotmail>Hotmail</option><option value=Chunking>Chunking</option><option value=\"Hotmail Domain\">Hotmail Domain</option><option selected value=\"NEW DEPLOY\">NEW DEPLOY</option><option value=TEST>TEST</option>\n";
}
elsif ($utype eq "TEST")
{
	print "<option value=Normal>Normal</option><option value=\"Time Based\">Time Based</option><option value=Hotmail>Hotmail</option><option value=Chunking>Chunking</option><option value=\"Hotmail Domain\">Hotmail Domain</option><option value=\"NEW DEPLOY\">NEW DEPLOY</option><option selected value=TEST>TEST</option>\n";
}
print<<"end_of_html";
</select></td></tr><tr><td>Randomize</td>
end_of_html
if ($randomize eq "Y")
{
	print "<td><input type=radio checked value=Y name=randomize>Yes&nbsp;<input type=radio value=N name=randomize>No</td>\n";
}
else
{
	print "<td><input type=radio value=Y name=randomize>Yes&nbsp;<input type=radio checked value=N name=randomize>No</td>\n";
}
print<<"end_of_html";
		<td colspan=2>Hour(s) to Add(Time Based only):&nbsp;<select name=chour>
end_of_html
my $k=0;
while ($k <= 23)
{
	if ($chour == $k)
	{
		print "<option selected value=$k>$k</option>";
	}
	else
	{
		print "<option value=$k>$k</option>";
	}
	$k++;
}
print<<"end_of_html";
</select></td>
end_of_html
if ($log_camp eq "Off")
{
	print "<td>Logging: </td><td><select name=log_camp><option value=Off selected>Off</option><option value=On>On</option></select>";
}
else
{
	print "<td>Logging: </td><td><select name=log_camp><option value=Off>Off</option><option selected value=On>On</option></select>";
}
print<<"end_of_html";
</td>
</tr>
<tr>
<td>MTA Setting</td><td><select name=mtaid>
end_of_html
$sql="select mta_id,name from mta order by name";
$sth=$dbhu->prepare($sql);
$sth->execute();
my $mid;
my $mname;
while (($mid,$mname)=$sth->fetchrow_array())
{
	if ($mid == $mta_id)
	{
		print "<option selected value=$mid>$mname</option>";
	}
	else
	{
		print "<option value=$mid>$mname</option>";
	}
}
$sth->finish();
print<<"end_of_html";
</select></td>
<td>Source URL</td><td><select name=surl>
<option selected value=ALL>ALL</option>
end_of_html
if ($gcnt == 1)
{
	my $tclient_id;
	my $turl;
	my $tcount;
	$sql="select client_id from  ClientGroupClients where client_group_id=?";
	$sth=$dbhu->prepare($sql);
	$sth->execute($gid);
	($tclient_id)=$sth->fetchrow_array();
	$sth->finish();

	$sql="select url,count(*) from SourceUrlSummary sus, source_url su where sus.url_id=su.url_id and sus.client_id=? and sus.effectiveDate >= date_sub(curdate(),interval 30 day) and su.url != '' and su.url != '.' group by 1 order by 2 desc limit 5";
	$sth=$dbhu->prepare($sql);
	$sth->execute($tclient_id);
	while (($turl,$tcount)=$sth->fetchrow_array())
	{
		if ($turl eq $source_url)
		{
			print "<option value=\"$turl\" selected>$turl - $tcount</option>\n";
		}
		else
		{
			print "<option value=\"$turl\">$turl - $tcount</option>\n";
		}
	}
	$sth->finish();
}
if ($source_url eq "ALL")
{
	$source_url="";
}
print<<"end_of_html";
	</select></td>
<td>Input URL: </td><td><input type=text size=50 maxlength=80 name=input_url value="$source_url"></t> 
<td>Zip</td><td><input type=text name=zip size=10 maxlength=10 value="$zip"></td></tr>
<tr><td>Mail From</td><td><input type=text name=mail_from size=20 maxlength=50 value="$mail_from"></td>
</select></td><td>Use Master</td>
end_of_html
if ($use_master eq "Y")
{
	print "<td><input type=radio checked value=Y name=use_master>Yes&nbsp;<input type=radio value=N name=use_master>No</td>\n";
}
else
{
	print "<td><input type=radio value=Y name=use_master>Yes&nbsp;<input type=radio checked value=N name=use_master>No</td>\n";
}
print "<td>Use Rdns</td>\n";
if ($useRdns eq "Y")
{
	print "<td><input type=radio checked value=Y name=useRdns>Yes&nbsp;<input type=radio value=N name=useRdns>No</td>\n";
}
else
{
	print "<td><input type=radio value=Y name=useRdns>Yes&nbsp;<input type=radio checked value=N name=useRdns>No</td>\n";
}
print<<"end_of_html";
<tr><td>Return Path</td><td><input type=text name=return_path size=50 maxlength=80 value="$return_path"></td>
end_of_html
print "<td>Prepull</td>\n";
if ($prepull eq "Y")
{
	print "<td><input type=radio checked value=Y name=prepull>Yes&nbsp;<input type=radio value=N name=prepull>No</td>\n";
}
else
{
	print "<td><input type=radio value=Y name=prepull>Yes&nbsp;<input type=radio checked value=N name=prepull>No</td>\n";
}
print "<td>Subject Encoding</td><td><select name=ConvertSubject>\n";
my $i=0;
while ($i <= $#ENC)
{
	if ($ENC1[$i] eq $ConvertSubject)
	{
		print qq^<option value="$ENC1[$i]" selected>$ENC[$i]</option>^;
	}
	else
	{
		print qq^<option value="$ENC1[$i]">$ENC[$i]</option>^;
	}
	$i++;
}
print "</select></td>";
print "<td>From Encoding</td><td><select name=ConvertFrom>\n";
my $i=0;
while ($i <= $#ENC)
{
	if ($ENC1[$i] eq $ConvertFrom)
	{
		print qq^<option value="$ENC1[$i]" selected>$ENC[$i]</option>^;
	}
	else
	{
		print qq^<option value="$ENC1[$i]">$ENC[$i]</option>^;
	}
	$i++;
}
print "</select></td></tr><tr>";
print "<td>Apply Jlog Profile:</td>\n";
print "<td><select name=jlogProfileID><option value=0>None</option>\n";
$sql="select profileID,profileName from EmailEventHandlerProfile order by profileName";
my $sthp=$dbhu->prepare($sql);
$sthp->execute();
my $profileID;
my $profileName;
while (($profileID,$profileName)=$sthp->fetchrow_array())
{
	if ($jlogProfileID == $profileID)
	{
		print "<option value=$profileID selected>$profileName</option>";
	}
	else
	{
		print "<option value=$profileID>$profileName</option>";
	}
}
$sthp->finish();
print<<"end_of_html";
		</select></td>
		<td><input type=submit value="Update Slot"></td></tr></table>
		</form>
		</td>
		</tr>
		<tr>
<td align="center" valign="top">
                <a href="mainmenu.cgi">
                <img src="$images/home_blkline.gif" border=0></a></TD>
		</tr>
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

sub check_domain
{
	my ($arr,$tname)=@_;
	my $i;
    for $i ( 0 .. $#{$arr} )
    {
		if ($tname eq $arr->[$i][0])
		{
			return 1;
		}
	}
	return 0;
}
