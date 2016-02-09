#!/usr/bin/perl

# *****************************************************************************************
# conversion_category_triggera.cgi
#
# this page display main page for setting up conversion triggers for category 
#
# History
# Jim Sobeck, 08/07/08, Creation
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $sth1;
my $sth2;
my $old_pid;
my $linkcnt;
my $temp_id;
my $company;
my $sql;
my $dbh;
my $aid;
my $t1;
my $t2;
my $altt;
my $t1_str;
my $t2_str;
my $altt_str;
my $cid;
my $aname;
my $errmsg;
my $images = $util->get_images_url;
my $cfrom;
my @from_array;
my $cname;
my $sdate;
my $sdate1;
my $hour;
my $acatid;
my $content_id;
my $shour;
my $status;
my $exclude_days;
my $trigger;
my $trigger2;
my $trigger_creative;
my $trigger_creative2;
my $trigger_creative_str;
my $trigger_creative2_str;

#------ connect to the util database ------------------
my ($dbhq,$dbhu)=$util->get_dbh();

$cid = $query->param('cid');
my $client_id = $query->param('client_id');
my $cnetwork;
my $cday;
#
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>CREATE EMAIL</title>
<script language="JavaScript">
var NS4 = (navigator.appName == "Netscape" && parseInt(navigator.appVersion) < 5);
var NSX = (navigator.appName == "Netscape");
var IE4 = (document.all) ? true : false;
var cstatus;
var waitit;
var waitit1;
var promptcnt;

function chkwait()
{
	if (!waitit)
	{
		waitit1=false;
		if (cstatus)
		{
			document.campform.submit();
		}
	}
	else
	{
		var timerid=setTimeout("chkwait()",100);
	}
}
function ProcessForm()
{
	cstatus = true;
	waitit = true;
	waitit1 = true;
end_of_html
if ($client_id == 0)
{
print<<"end_of_html";
    var selObj = document.getElementById('trigger_creative');
    var selIndex = selObj.selectedIndex;
    var selObj1 = document.getElementById('trigger_creative2');
    var selIndex1 = selObj1.selectedIndex;
	setTimeout("chkwait()",100);
	parent.frames[1].location = "/newcgi-bin/conversion_chk_category_trigger.cgi?cid=$cid&trigger1="+selObj.options[selIndex].value+"&trigger2="+selObj1.options[selIndex1].value;
end_of_html
}
else
{
	print "document.campform.submit();\n";
}
print<<"end_of_html";
}

function displaymsg(msg_str)
{
	if (cstatus)
	{
	if (!confirm(msg_str))
    {
		cstatus = false;
		waitit=false;
    }
	}
	promptcnt--;
	if (promptcnt == 0)
	{
		waitit=false;
	}
}
function finished(cnt)
{
	promptcnt=cnt;
	if (cnt == 0)
	{
		waitit=false;
	}
}
function addTriggerOption(value,text)
{
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    campform.trigger_creative.add(newOpt);
}
function addTriggerOption2(value,text)
{
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    campform.trigger_creative2.add(newOpt);
}

function addOption1(value,text)
{
	var newOpt = document.createElement("OPTION");
	newOpt.text=text;
	newOpt.value=value;
	campform.advertiser_id1.add(newOpt);
}
function addOption2(value,text)
{
	var newOpt = document.createElement("OPTION");
	newOpt.text=text;
	newOpt.value=value;
	campform.advertiser_id2.add(newOpt);
}

function update_advertiser(tid)
{
	parent.frames[1].location="/newcgi-bin/upd_advertiser_list1.cgi?cid="+selObj.options[selIndex].value+"&tid="+tid+"&aid=$aid";
}
function update_advertiser1(tid)
{
	var selObj = document.getElementById('catid1');
	var selIndex = selObj.selectedIndex;
	var selLength = campform.advertiser_id1.length;
	while (selLength>0)
	{
		campform.advertiser_id1.remove(selLength-1);
		selLength--;
	}
	campform.advertiser_id1.length=0;
	parent.frames[1].location="/newcgi-bin/upd_advertiser_list2.cgi?cid="+selObj.options[selIndex].value;
}
function update_advertiser2(tid)
{
	var selObj = document.getElementById('catid2');
	var selIndex = selObj.selectedIndex;
	var selLength = campform.advertiser_id2.length;
	while (selLength>0)
	{
		campform.advertiser_id2.remove(selLength-1);
		selLength--;
	}
	campform.advertiser_id2.length=0;
	parent.frames[1].location="/newcgi-bin/upd_advertiser_list3.cgi?cid="+selObj.options[selIndex].value;
}
function update_subject()
{
	parent.frames[1].location="/newcgi-bin/conversion_upd_cat_creative_list.cgi?cid=$cid&client_id=$client_id";
}
function set_fields(trigger_creative,trigger_creative2,catid1,catid2,advertiser_id1,advertiser_id2)
{
  	var i;
  	var selObj = document.getElementById('catid1');
  	for (i=0; i<selObj.options.length; i++) {
    	if (selObj.options[i].value == catid1) { selObj.selectedIndex = i; break; }
	}
  	var selObj = document.getElementById('catid2');
  	for (i=0; i<selObj.options.length; i++) {
    	if (selObj.options[i].value == catid2) { selObj.selectedIndex = i; break; }
	}
  	var selObj = document.getElementById('advertiser_id1');
  	for (i=0; i<selObj.options.length; i++) {
    	if (selObj.options[i].value == advertiser_id1) { selObj.selectedIndex = i; break; }
	}
  	var selObj = document.getElementById('advertiser_id2');
  	for (i=0; i<selObj.options.length; i++) {
    	if (selObj.options[i].value == advertiser_id2) { selObj.selectedIndex = i; break; }
	}
  	var selObj = document.getElementById('trigger_creative');
  	for (i=0; i<selObj.options.length; i++) {
    	if (selObj.options[i].value == trigger_creative) { selObj.selectedIndex = i; break; }
	}
  	var selObj = document.getElementById('trigger_creative2');
  	for (i=0; i<selObj.options.length; i++) {
    	if (selObj.options[i].value == trigger_creative2) { selObj.selectedIndex = i; break; }
	}
}

function update_creative()
{
	var selObj = document.getElementById('advertiser_id1');
	var selIndex = selObj.selectedIndex;
	var selLength = campform.trigger_creative.length;
	while (selLength>0)
	{
		campform.trigger_creative.remove(selLength-1);
		selLength--;
	}
	parent.frames[1].location="/newcgi-bin/upd_creative_list1.cgi?aid="+selObj.options[selIndex].value;
}
function update_creative2()
{
	var selObj = document.getElementById('advertiser_id2');
	var selIndex = selObj.selectedIndex;
	var selLength = campform.trigger_creative2.length;
	while (selLength>0)
	{
		campform.trigger_creative2.remove(selLength-1);
		selLength--;
	}
	parent.frames[1].location="/newcgi-bin/upd_creative_list2.cgi?aid="+selObj.options[selIndex].value;
}
</script>
</head>

<body>

<table cellSpacing="0" cellPadding="0" align="left" bgColor="#ffffff" border="0" id="table1">
	<tr vAlign="top">
		<td noWrap align="left">
		<table cellSpacing="0" cellPadding="0" width="719" border="0" id="table2">
			<tr>
				<td width="248" rowSpan="2">&nbsp;</td>
				<td width="328" >&nbsp;</td>
			</tr>
			<tr>
				<td width="468">
				<table cellSpacing="0" cellPadding="0" width="100%" border="0" id="table3">
					<tr>
						<td align="left"><b><font face="Arial" size="2">&nbsp;Trigger Setup</font></b></td>
					</tr>
					<tr>
						<td align="right"><b>
						<a style="TEXT-DECORATION: none" href="/cgi-bin/logout.cgi">
						<font face="Arial" color="#509c10" size="2">Logout</font></a>&nbsp;&nbsp;&nbsp;
						<a style="TEXT-DECORATION: none" href="/cgi-bin/wss_support_form.cgi">
						<font face="Arial" color="#509c10" size="2">Customer 
						Assistance</font></a></b> 
						
						</td>
					</tr>
				</table>
				</td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td vAlign="top" align="left" bgColor="#999999">
		<table cellSpacing="0" cellPadding="10" width="100%" bgColor="#999999" border="0" id="table4">
			<tr>
				<td vAlign="top" align="left" bgColor="#ffffff" colSpan="10">
				<form name="campform" method="post" action="/cgi-bin/conversion_category_trigger_save.cgi" target=_top>
					<input type="hidden" name="cid" value=$cid>
					<input type="hidden" name="client_id" value=$client_id>
					<input type="hidden" name="nextfunc">
					<table cellSpacing="0" cellPadding="0" width="660" bgColor="#ffffff" border="0" id="table7">
						<tr>
							<td vAlign="top">
							<table cellSpacing="0" cellPadding="0" width="660" bgColor="#ffffff" border="0" id="table8">
								<tr>
									<td vAlign="top" align="middle" width="195">
									<img height="7" src="/images/spacer.gif" width="190">
 
									<table cellSpacing="0" cellPadding="0" width="190" border="0" id="table9">
										<tr bgColor="#ffffff">
											<td vAlign="top" align="left" width="9" height="7"></td>
											<td vAlign="top" align="right" width="100%"></td>
										</tr>
										<tr bgColor="#ffffff">
											<td vAlign="bottom" colSpan="2" height="7">
											&nbsp;&nbsp;
										</tr>
										<tr bgColor="#ffffff">
											<td>&nbsp;&nbsp;&nbsp;&nbsp; </td>
											<td vAlign="bottom" height="12">
&nbsp;</td>
										</tr>
										<tr bgColor="#ffffff">
											<td vAlign="bottom" align="left" height="7"></td>
											<td vAlign="bottom" align="right">
</td>
										</tr> 
									</table>
									<img height="7" src="/images/spacer.gif" width="190"> 
									</td>
									<td vAlign="top" align="middle" width="465">
									<img height="7" src="/images/spacer.gif" width="455"> <!-- Begin main body area -->
									<table cellSpacing="0" cellPadding="0" width="455" bgColor="#e3fad1" border="0" id="table11">
										<tr bgColor="#509c10">
											<td vAlign="top" align="left" height="15">
											<img height="7" src="/images/blue_tl.gif" width="7" border="0"></td>
											<td align="middle" height="15">
											<font face="verdana,arial,helvetica,sans serif" color="#ffffff" size="2">
											<b>Conversion Trigger By Category</b></font></td>
											<td vAlign="top" align="right" bgColor="#509c10" height="15">
											<img height="7" src="/images/blue_tr.gif" width="7" border="0"></td>
										</tr>
										<tr>
											<td colSpan="3">&nbsp;&nbsp;
											<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
											<b>Trigger Email #1</b></font></td>
										</tr>
										</tr>
										<tr>
											<td colSpan="3">&nbsp;&nbsp;
											<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
											<b>Category</b></font></td>
										</tr>
										<tr>
											<td colSpan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<select name="catid1" onChange="update_advertiser1(0);">
											<option value="-1">ALL CATEGORIES</option>
end_of_html
$sql = "select category_id,category_name from category_info order by category_name";
$sth = $dbhq->prepare($sql);
$sth->execute();
my $catid;
my $cname;
while (($catid,$cname) = $sth->fetchrow_array())
{
    print "<option value=$catid>$cname</option>\n";
}
$sth->finish;
print "</select>";
print<<"end_of_html";
</td>
										</tr>
										
										<tr>
											<td colSpan="3">&nbsp;&nbsp;
											<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
											<b>&nbsp; Advertiser</b></font></td>
										</tr>
										<tr>
											<td colSpan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<select name="advertiser_id1" onChange="update_creative();">
											<option value="-1" selected>NONE
											</option>
end_of_html
if ($trigger > 0)
{
	$sql = "select advertiser_id,advertiser_name from advertiser_info where category_id in (select category_id from advertiser_info,creative where creative_id=$trigger and creative.advertiser_id=advertiser_info.advertiser_id) and status='A' and test_flag='N' order by advertiser_name";
}
else
{
	$sql = "select advertiser_id,advertiser_name from advertiser_info where status='A' and test_flag='N' order by advertiser_name"; 
}
$sth = $dbhq->prepare($sql);
$sth->execute();
my $taid;
my $tname;
while (($taid,$tname) = $sth->fetchrow_array())
{
    print "<option value=$taid>$tname</option>\n";
}
$sth->finish;
print<<"end_of_html";
											</select> </td>
										</tr>
										
										</tr>
										<tr>
											<td colSpan="3">
											<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
											<b>&nbsp;&nbsp;&nbsp;&nbsp; 
											Creative</b></font></td>
										</tr>
										<tr>
											<td colSpan="3">&nbsp;&nbsp;&nbsp;&nbsp;<font color="#509c10"> </font>
											<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
											<b>&nbsp;</b></font>&nbsp;<select name="trigger_creative">
<option value="0" selected>NONE</option>
											</select> </td>
										</tr>
										<tr><td colspan=3>&nbsp;</td></td>
										<tr>
											<td colSpan="3">&nbsp;&nbsp;
											<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
											<b>Trigger Email #2</b></font></td>
										</tr>
										</tr>
										<tr>
											<td colSpan="3">&nbsp;&nbsp;
											<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2"><b>Category</b></font></td></tr>
										<tr>
											<td colSpan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<select name="catid2" onChange="update_advertiser2(0);">
											<option value="-1">ALL CATEGORIES</option>
end_of_html
$sql = "select category_id,category_name from category_info order by category_name";
$sth = $dbhq->prepare($sql);
$sth->execute();
my $catid;
my $cname;
while (($catid,$cname) = $sth->fetchrow_array())
{
    print "<option value=$catid>$cname</option>\n";
}
$sth->finish;
print "</select>";
print<<"end_of_html";
</td>
										</tr>
										
										<tr>
											<td colSpan="3">&nbsp;&nbsp;
											<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
											<b>&nbsp; Advertiser</b></font></td>
										</tr>
										<tr>
											<td colSpan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<select name="advertiser_id2" onChange="update_creative2();">
											<option value="-1" selected>NONE
											</option>
end_of_html
if ($trigger2 > 0)
{
	$sql = "select advertiser_id,advertiser_name from advertiser_info where category_id in (select category_id from advertiser_info,creative where creative_id=$trigger2 and creative.advertiser_id=advertiser_info.advertiser_id) and status='A' and test_flag='N' order by advertiser_name";
}
else
{
	$sql = "select advertiser_id,advertiser_name from advertiser_info where status='A' and test_flag='N' order by advertiser_name"; 
}
$sth = $dbhq->prepare($sql);
$sth->execute();
my $taid;
my $tname;
while (($taid,$tname) = $sth->fetchrow_array())
{
    print "<option value=$taid>$tname</option>\n";
}
$sth->finish;
print<<"end_of_html";
											</select> </td>
										</tr>
										</tr>
										<tr>
											<td colSpan="3">
											<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
											<b>&nbsp;&nbsp;&nbsp;&nbsp; 
											Creative</b></font></td>
										</tr>
										<tr>
											<td colSpan="3">&nbsp;&nbsp;&nbsp;&nbsp;<font color="#509c10"> </font>
											<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
											<b>&nbsp;</b></font>&nbsp;<select name="trigger_creative2">
<option value="0" selected>NONE</option>
											</select> </td>
										</tr>
										<tr>
											<td colSpan="3">&nbsp;</td>
										</tr>
										<tr>
											<td colSpan="3">&nbsp;</td>
										</tr>
										<tr>
											<td colSpan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </td>
										</tr>
	
										
										<tr>
											<td>&nbsp;</td>
											<td>
											<table cellSpacing="0" cellPadding="0" width="100%" border="0" id="table12">
												<tr>
													<td align="middle" width="50%">
													<img height="22" src="/images/save_rev.gif" width="81" border="0" onClick="ProcessForm()"> <a href="/cgi-bin/conversion_category_trigger_list.cgi?userid=$client_id" target=_top><img src="/images/cancel.gif" border=0></a>
													</td>
												</tr>
											</table>
											</td>
											<td>&nbsp;</td>
										</tr>
										<tr>
											<td vAlign="bottom" align="left" colSpan="2">
											<img height="7" src="/images/lt_purp_bl.gif" width="7" border="0"></td>
											<td vAlign="bottom" align="right">
											<img height="7" src="/images/lt_purp_br.gif" width="7" border="0"></td>
										</tr>
									</table>
									<!-- End main body area --></td>
								</tr>
							</table>
							</td>
						</tr>
						<tr>
							<td>
							<table cellSpacing="0" cellPadding="7" width="100%" border="0" id="table13">
							</table>
							</td>
						</tr>
						<tr>
							<td>
							<img height="7" src="/images/spacer.gif"></td>
						</tr>
					</table>
				</form>
				</td>
			</tr>
		</table>
		</td>
	</tr>
</table>
<script Language="JavaScript">
    update_subject();
</script>
</body>
</html>
end_of_html
exit(0);
