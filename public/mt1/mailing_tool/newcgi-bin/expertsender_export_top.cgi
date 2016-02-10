#!/usr/bin/perl

# *****************************************************************************************
# expertsender_main.cgi
#
# this page is to select advertiser, client, and template so we can create html for expert sender 
#
# History
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $sql;
my $dbh;
my $dbhu;
my $espLabel;
my $aid;
my $aname;
my $template_id=1658;
my $images = $util->get_images_url;
my $oldaid=$query->param('aid');
if ($oldaid eq "")
{
    $oldaid=0;
}
my $esp=$query->param('esp');
my $emailfield;
my $eidfield;
my $espID;
$sql="select espID,espLabel,eidField,emailField from ESP where espName='$esp'";
$sth=$dbhu->prepare($sql);
$sth->execute();
($espID,$espLabel,$eidfield,$emailfield)=$sth->fetchrow_array();
$sth->finish();
my $oldclientid=$query->param('clientid');
if (($oldclientid eq "") and ($espLabel eq "ZetaMail"))
{
	$oldclientid=1692;
}
my $oldtemplateid=$query->param('templateid');
if ($oldtemplateid eq "")
{
	$oldtemplateid=$template_id;
	if ($espLabel eq "ZetaMail")
	{
		$oldtemplateid=2563;
	}
	elsif ($esp eq "AlphaS")
	{
		$oldtemplateid=2749;
	}
	elsif ($esp eq "AlphaD")
	{
		$oldtemplateid=2749;
	}
	elsif ($esp eq "AlphaHYD")
	{
		$oldtemplateid=2322;
	}
}

my ($dbhq,$dbhu)=$util->get_dbh();

my $user_id = util::check_security();
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
	$util->clean_up();
    exit(0);
}


# print html page out
my $redir_domain;
my $send_date;
$sql="select date_add(curdate(),interval 1 day)";
$sth=$dbhq->prepare($sql);
$sth->execute();
($send_date)=$sth->fetchrow_array();
$sth->finish();
util::header($espLabel);
$redir_domain="yourcontentdomain.com";
if (($esp eq "ALP001") or ($esp eq "ALP002"))
{
    $redir_domain="";
}
elsif ($esp eq "PACK1")
{
	$redir_domain="premiumprime.com";
}

print << "end_of_html";
</TD>
</TR>
<TR>
<TD vAlign=top align=left bgColor=#FFFFFF>

	<TABLE cellSpacing=0 cellPadding=10 bgColor=#FFFFFF border=0 width="100%">
    <TBODY>
    <TR>
    <TD vAlign=top align=left bgColor=#ffffff colSpan=10>

        <TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
        <TBODY>
        <TR>
        <TD><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
			You need to select an advertiser, client, and template<BR></FONT></td> 
		</TR>
        <TR>
        <TD><IMG height=5 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>
<script language="JavaScript">
function preview()
{
    var selObj = document.getElementById('aid');
    var selIndex = selObj.selectedIndex;
    var newwin = window.open("/cgi-bin/adv_preview.cgi?aid="+selObj.options[selIndex].value, "Preview", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,width=900,height=500,left=25,top=50");
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
   	parent.frames[1].location="/newcgi-bin/sm2_upd_creative.cgi?aid="+selObj.options[selIndex].value+"sord="+campform.sord.value."&t1=1&t2=1&all=1";
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
</script>
		<FORM action="expertsender_export.cgi" method=post name=campform>
		<input type=hidden name=esp value=$esp>
		<TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
		<TBODY>
		<tr><td>Sort by:</td><td><input type=radio name=sord id=sord value="Alphabetical" checked>Asset Alphabetical(A-Z)&nbsp;&nbsp;<input type=radio name=sord id=sord value="ID">Asset ID descending</td></tr>
		<TR>
		<TD><b>Advertiser</b></td><td><select name="aid" onChange="upd_creative(0);">
end_of_html
$sql = "select advertiser_id,advertiser_name from advertiser_info where status ='A' and test_flag='N' order by advertiser_name"; 
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($aid,$aname) = $sth->fetchrow_array())
{
	if ($aid == $oldaid)
	{
		print "<option selected value=$aid>$aname</option>\n";
	}
	else
	{
		print "<option value=$aid>$aname</option>\n";
	}
}
$sth->finish();
print<<"end_of_html";
</select>&nbsp;&nbsp;<a href="javascript:preview();">Preview Creatives</a></td></tr>
		  <tr>
			<td class="label"><b>Creative:</b></td>
			<td>
				<select name="creative" id="creative" size=5>
				</select>
			</td>
		  </tr>
		  <tr>
			<td class="label"><b>From Line:</b></td>
			<td>
				<select name="cfrom" id="cfrom" size=5>
				</select>&nbsp;&nbsp;
			</td>
		  </tr>
		  <tr>
			<td class="label"><b>Subject:</b></td>
			<td>
				<select name="csubject" id="csubject" size=5>
				</select>&nbsp;&nbsp;
			</td>
		  </tr>
		<TR>
		<TD><b>Client</b></td><td><select name="clientid">
end_of_html
$sql = "select user_id,first_name from user where status ='A' order by first_name"; 
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($aid,$aname) = $sth->fetchrow_array())
{
	if ($aid == $oldclientid)
	{
		print "<option selected value=$aid>$aname</option>\n";
	}
	else
	{
		print "<option value=$aid>$aname</option>\n";
	}
}
$sth->finish();
print<<"end_of_html";
</select></td></tr>
		<TR>
		<TD><b>Template</b></td><td><select name="templateid">
end_of_html
$sql = "select template_id,template_name from brand_template where status='A' order by template_name"; 
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($aid,$aname) = $sth->fetchrow_array())
{
	if ($aid == $oldtemplateid)
	{
		print "<option selected value=$aid>$aname</option>\n";
	}
	else
	{
		print "<option value=$aid>$aname</option>\n";
	}
}
$sth->finish();
print<<"end_of_html";
</select></td></tr>
		<TR>
		<TD><b>Content Domain:</b></td><td><input type=text size=50 name=redir_domain value="$redir_domain"></td></tr>
		<TD><b>Send Date:</b></td><td><input type=text size=11 maxlength=10 name=send_date value="$send_date"></td></tr>
		<tr><td><b>Footer:</b></td><td><select name=footer_id><option value=0 selected>None</option>
end_of_html
$sql = "select footer_id,footer_name from Footers where status='A' order by footer_name";
$sth = $dbhq->prepare($sql);
$sth->execute();
my $fid;
my $fname;
while (($fid,$fname) = $sth->fetchrow_array())
{
	print "<option value=$fid>$fname</option>";
}
$sth->finish();
print<<"end_of_html";
			</select></td><TR>
end_of_html
if (($esp eq "ALP001") or ($esp eq "ALP002"))
{
	print "<tr><td><b>SubAffiliate ID:</b></td><td><select name=subAffiliateID>";
	$sql = "select subAffiliateID,name from AlphaSSubAffiliate where espID=51 order by name"; 
	$sth = $dbhq->prepare($sql);
	$sth->execute();
	my $sid;
	my $sname;
	while (($sid,$sname) = $sth->fetchrow_array())
	{
		print "<option value=$sid>$sname</option>";
	}
	$sth->finish();
	print "</select></td><TR>";
}
if ($esp eq "PACK1")
{
	print "<tr><td><b>Affiliate ID:</b></td><td><input type=text name=AffiliateID value=489 size=5></td></tr>";
	print "<tr><td><b>Cake Domain:</b></td><td><input type=text name=cakeDomain value=pixtrk.com size=20></td></tr>";
}
print<<"end_of_html";
			<TD><INPUT type=submit value="Create $espLabel HTML"> </td><td>
				<a href="mainmenu.cgi" target=_top><IMG hspace=7 src="$images/home_blkline.gif" border=0 width="90" height="22"></a>
			</TD>
			</TR>
			</TBODY>
			</TABLE>
		</FORM>

	</TD></TR>
	</TBODY>
	</TABLE>

</TD>
</TR>
<TR>
<TD noWrap align=left height=17>
end_of_html
if ($oldaid > 0)
{
	print "<script language=JavaScript>\n";
    print "upd_creative(1);\n";
	print "</script>\n";
}

$util->footer();
exit(0);
