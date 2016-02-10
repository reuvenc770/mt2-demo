#!/usr/bin/perl

# *****************************************************************************************
# unique_advertiser.cgi
#
# this page displays the list of UniqueScheduleAdvertiser and lets the user edit / add
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
my $bid;
my $dname;
my $nl_id=14;
my $cstatus=$query->param('cstatus');
my $inaid=$query->param('aid');
if ($cstatus eq "")
{
	$cstatus="A";
}
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

util::header("Unique Schedule Advertisers");

print << "end_of_html";
<script language="JavaScript">
function exportfile()
{
    var newwin = window.open("/cgi-bin/usa_export.cgi", "Export", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,width=900,height=500,left=25,top=50");
    newwin.focus();
}
function upd_creative(t1)
{
    var selObj = document.getElementById('aid');
    var selIndex = selObj.selectedIndex;
    var selLength = campform.creative.length;
    while (selLength>0)
    {
        campform.creative.remove(selLength-1);
        selLength--;
    }
    campform.creative.length=0;
    var selLength = campform.csubject.length;
    while (selLength>0)
    {
        campform.csubject.remove(selLength-1);
        selLength--;
    }
    campform.csubject.length=0;
    var selLength = campform.cfrom.length;
    while (selLength>0)
    {
        campform.cfrom.remove(selLength-1);
        selLength--;
    }
    campform.cfrom.length=0;
   	parent.frames[1].location="/newcgi-bin/sm2_upd_creative.cgi?all=0&t2=1&aid="+selObj.options[selIndex].value;
}
function addCreative(value,text)
{
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    campform.creative.add(newOpt);
}
function addSubject(value,text)
{
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    campform.csubject.add(newOpt);
}
function addFrom(value,text)
{
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    campform.cfrom.add(newOpt);
}
function set_creative_fields(crid,csubject,cfrom)
{
    var i;
    var selObj = document.getElementById('creative');
    for (i=0; i<selObj.options.length; i++) 
	{ 
		if (selObj.options[i].value == crid)
 		{ 
			selObj.selectedIndex = i; break;
    	}
	}
    var selObj = document.getElementById('csubject');
    for (i=0; i<selObj.options.length; i++) 
	{ 
		if (selObj.options[i].value == csubject)
 		{ 
			selObj.selectedIndex = i; break;
    	}
	}
    var selObj = document.getElementById('cfrom');
    for (i=0; i<selObj.options.length; i++) 
	{ 
		if (selObj.options[i].value == cfrom)
 		{ 
			selObj.selectedIndex = i; break;
    	}
	}
}
function set_creative_field(crid)
{
    var i;
    var selObj = document.getElementById('creative');
    for (i=0; i<selObj.options.length; i++) 
	{ 
		if (selObj.options[i].value == crid)
 		{ 
    		selObj.options[i].selected = true;
    	}
	}
}
function set_subject_field(csubject)
{
    var i;
    var selObj = document.getElementById('csubject');
    for (i=0; i<selObj.options.length; i++) 
	{ 
		if (selObj.options[i].value == csubject)
 		{ 
    		selObj.options[i].selected = true;
    	}
	}
}
function set_from_field(cfrom)
{
    var i;
    var selObj = document.getElementById('cfrom');
    for (i=0; i<selObj.options.length; i++) 
	{ 
		if (selObj.options[i].value == cfrom)
 		{ 
    		selObj.options[i].selected = true;
    	}
	}
}
</script>
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
function ProcessForm()
{
    var selObj = document.getElementById('creative');
    var selIndex = selObj.selectedIndex;
    if (selIndex < 0)
    {
        alert('You must select one or more creatives');
        return false;
    }
    var selObj = document.getElementById('csubject');
    var selIndex = selObj.selectedIndex;
    if (selIndex < 0)
    {
        alert('You must select one or more subjects');
        return false;
    }
    var selObj = document.getElementById('cfrom');
    var selIndex = selObj.selectedIndex;
    if (selIndex < 0)
    {
        alert('You must select one or more froms');
        return false;
    }
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
		<td><form method=POST name=campform action=unique_advertiser_add.cgi target=_top onSubmit="return ProcessForm();">
		<table cellSpacing=3>
		<tr><td>Name: </td><td colspan=6><input type=text name=uname size=50 maxlength=100></td></tr>
		<tr><td>Advertiser: </td><td colspan=6><select name=aid id=aid onChange="upd_creative(0);">
<option value=0 checked>--SELECT ONE--</option>
end_of_html
$sql="select advertiser_id,advertiser_name from advertiser_info where status='A' and test_flag='N' order by advertiser_name";
$sth=$dbhq->prepare($sql);
$sth->execute();
my $aid;
my $aname;
while (($aid,$aname)=$sth->fetchrow_array())
{
    print "<option value=$aid>$aname</option>\n";
}
$sth->finish();
print<<"end_of_html";
                </select>&nbsp;&nbsp;<input type="button" value="Refresh" onClick="upd_creative(0);"/>
</td></tr><tr><td>&nbsp;&nbsp;Creative:</td>
		<td><select name=creative id=creative multiple=multiple size=5>
		</select></td>
		<td>&nbsp;&nbsp;Subject:</td>
		<td><select name=csubject id=csubject multiple=multiple size=5>
		</select></td>
		<td>&nbsp;&nbsp;From:</td>
		<td><select name=cfrom id=cfrom multiple=multiple size=5>
		</select></td>
		<td><input type=submit value="Add Advertiser"></td></tr></table>
		</form>
<input type="button" value="Export USA" onClick="javascript:exportfile();" />&nbsp;&nbsp;&nbsp;<a href="usa_combination_add.cgi">Add USA Combination</a>
		</td>
		</tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>
<form method=POST action="upload_usa.cgi" encType=multipart/form-data accept-charset="UTF-8"><strong>USA CSV File:  </strong><INPUT type=file name="upload_file" size="65">&nbsp;&nbsp;<input type=submit value="Upload"></form></td></tr>
		<tr>
<td align="center" valign="top">
                <a href="mainmenu.cgi" target=_top>
                <img src="$images/home_blkline.gif" border=0></a></TD>
		</tr>
		<TR>
		<TD><IMG height=15 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>
		<form method=post action=unique_advertiser.cgi>
		<center>
		Advertiser: <select name=aid>
<option value=0>ALL</option>
end_of_html
$sql="select advertiser_id,advertiser_name from advertiser_info where status='A' and test_flag='N' order by advertiser_name";
$sth=$dbhq->prepare($sql);
$sth->execute();
my $aid;
my $aname;
while (($aid,$aname)=$sth->fetchrow_array())
{
	if ($aid == $inaid)
	{
    	print "<option value=$aid selected>$aname</option>\n";
	}
	else
	{
    	print "<option value=$aid>$aname</option>\n";
	}
}
$sth->finish();
print<<"end_of_html";
                </select>&nbsp;&nbsp;Records to show: <select name=cstatus>
end_of_html
my @STAT=("ALL","Active","Inactive");
my @CSTAT=("B","A","I");
my $i=0;
while ($i <= $#CSTAT)
{
	if ($CSTAT[$i] eq $cstatus)
	{
		print "<option value=$CSTAT[$i] selected>$STAT[$i]</option>\n";
	}
	else
	{
		print "<option value=$CSTAT[$i]>$STAT[$i]</option>\n";
	}
	$i++;
}
print<<"end_of_html";
		</select>
		<input type=submit name=submit value=Refresh>
		</form> 
		<TABLE cellSpacing=0 cellPadding=3 width="100%" border=1>
		<TBODY>
		<TR bgColor="#509C10" height=15>
		<TD colspan="6" align=center height=15>
			<font face="Verdana,Arial,Helvetica,sans-serif" color="white" size="3">
			<b>Unique Schedule Advertisers</b></font></TD>
		</TR>
		<tr><th>Name</th><th>Advertiser</th><th>Creative</th><th>Subject</th><th>From</th><th></th></tr>
		<TR>
		<TD colspan=3><IMG height=7 src="$images/spacer.gif"></TD>
		</TR>
end_of_html
if ($inaid ne "")
{
if ($cstatus eq "B")
{
	$sql="select usa_id,name,advertiser_id,creative_id,subject_id,from_id,usaType from UniqueScheduleAdvertiser usa where 1=1 ";
}
elsif ($cstatus eq "A")
{
	$sql="select usa.usa_id,usa.name,usa.advertiser_id,usa.creative_id,usa.subject_id,usa.from_id,usa.usaType from UniqueScheduleAdvertiser usa, advertiser_info ai where usa.advertiser_id=ai.advertiser_id and ai.status!='I' ";
}
elsif ($cstatus eq "I")
{
	$sql="select usa.usa_id,usa.name,usa.advertiser_id,usa.creative_id,usa.subject_id,usa.from_id,usa.usaType from UniqueScheduleAdvertiser usa, advertiser_info ai where usa.advertiser_id=ai.advertiser_id and ai.status='I' ";
}
if ($inaid > 0)
{
	$sql=$sql." and usa.advertiser_id=$inaid ";
}
$sql=$sql." order by name";

$sth=$dbhu->prepare($sql);
$sth->execute();
my $aid;
my $sid;
my $cid;
my $fid;
my $aname;
my $cname;
my $sname;
my $fname;
my $name;
my $usa_id;
my $usaType;
while (($usa_id,$name,$aid,$cid,$sid,$fid,$usaType)=$sth->fetchrow_array())
{
	$sql="select advertiser_name from advertiser_info where advertiser_id=?";
	my $sth1=$dbhq->prepare($sql);
	$sth1->execute($aid);
	($aname)=$sth1->fetchrow_array();
	$sth1->finish();

	if ($cid == 0)
	{
		$cname="ROTATE ALL";
	}
	else
	{
	$sql="select creative_name from creative where creative_id=?";
	my $sth1=$dbhq->prepare($sql);
	$sth1->execute($cid);
	($cname)=$sth1->fetchrow_array();
	$sth1->finish();
	}
	if ($sid == 999999999)
	{
		$sname="ROTATE ALL";
	}
	else
	{
	$sql="select advertiser_subject from advertiser_subject where subject_id=?";
	my $sth1=$dbhq->prepare($sql);
	$sth1->execute($sid);
	($sname)=$sth1->fetchrow_array();
	$sth1->finish();
	}
	if ($fid == 999999999)
	{
		$fname="ROTATE ALL";
	}
	else
	{
	$sql="select advertiser_from from advertiser_from where from_id=?";
	my $sth1=$dbhq->prepare($sql);
	$sth1->execute($fid);
	($fname)=$sth1->fetchrow_array();
	$sth1->finish();
	}
	if ($usaType eq "Combination")
	{
		print "<tr><td><a href=usa_combination.cgi?usaid=$usa_id target=_top>$name</a></td>";
	}
	else
	{
		print "<tr><td><a href=unique_advertiser_main1.cgi?usa_id=$usa_id target=_top>$name</a></td>";
	}
	print "<td>$aname</td><td>$cname</td><td>$sname</td><td>$fname</td><td><a href=\"unique_advertiser_delete.cgi?usa_id=$usa_id\" target=_top>Delete</td></tr>\n";
}
$sth->finish();
}
print<<"end_of_html";
		<TR>
		<TD colspan=3>

			<table cellpadding="0" cellspacing="0" border="0" width="100%">
			<tr>
			<td width=50% align="center">
				<a href="mainmenu.cgi" target=_top>
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
