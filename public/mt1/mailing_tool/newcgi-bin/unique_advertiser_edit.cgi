#!/usr/bin/perl

# *****************************************************************************************
# unique_advertiser_edit.cgi
#
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
my $usa_id=$query->param('usa_id');
my $aname;
my $aid;
$sql="select advertiser_info.advertiser_id,advertiser_name from UniqueScheduleAdvertiser,advertiser_info where UniqueScheduleAdvertiser.advertiser_id=advertiser_info.advertiser_id and UniqueScheduleAdvertiser.usa_id=?"; 
$sth=$dbhq->prepare($sql);
$sth->execute($usa_id);
($aid,$aname)=$sth->fetchrow_array();
$sth->finish();

util::header("Unique Schedule Advertisers");

print << "end_of_html";
<script language="JavaScript">
function upd_creative(tval)
{
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
   	parent.frames[1].location="/newcgi-bin/sm2_upd_creative.cgi?all=0&t1=2&aid=$aid&usa_id=$usa_id";
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
</TD>
</TR>
<TR>
<TD vAlign=top align=left bgColor=#999999>
	<TABLE cellSpacing=0 cellPadding=10 bgColor=#999999 border=0 width="100%">
	<TBODY>
	<TR>
	<TD vAlign=top align=left bgColor=#ffffff colSpan=10>
		<TABLE cellSpacing=0 cellPadding=0 width=900 bgColor=#ffffff border=0>
		<TBODY>
		<tr>
		<td><form method=POST name=campform action=unique_advertiser_upd.cgi target=_top onSubmit="return ProcessForm();">
		<input type=hidden name=usa_id value=$usa_id>
		<table cellSpacing=3>
		<tr><td>Advertiser: </td><td>$aname</td>
</tr><tr><td>&nbsp;&nbsp;Creative:</td>
		<td><select name=creative id=creative multiple=multiple size=5>
		</select>&nbsp;&nbsp;<a href="/cgi-bin/adv_preview.cgi?aid=$aid" target=_blank>View All Creatives</a></td><tr>
		<tr>
		<td>&nbsp;&nbsp;Subject:</td>
		<td><select name=csubject id=csubject multiple=multiple size=5>
		</select></td>
		</tr><tr>
		<td>&nbsp;&nbsp;From:</td>
		<td><select name=cfrom id=cfrom multiple=multiple size=5>
		</select></td>
		<td><input type=submit value="Update Advertiser"></td></tr></table>
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
<script language="JavaScript">
upd_creative(2);
</script>
</TD>
</TR>
<TR>
<TD noWrap align=left height=17>
end_of_html

$pms->footer();

# exit function

$pms->clean_up();
exit(0);
