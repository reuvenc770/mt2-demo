#!/usr/bin/perl

# *****************************************************************************************
# show_campaigna.cgi
#
# this page display main page for editing a campaign 
#
# History
# Jim Sobeck, 01/24/05, Creation
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
my $old_bid;
my $linkcnt;
my $temp_id;
my $company;
my $sql;
my $dbh;
my $aid;
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
my $creative1_id;
my $creative2_id;
my $creative3_id;
my $creative4_id;
my $creative5_id;
my $creative6_id;
my $creative7_id;
my $creative8_id;
my $creative9_id;
my $creative10_id;
my $creative11_id;
my $creative12_id;
my $creative13_id;
my $creative14_id;
my $creative15_id;
my $subject1;
my $subject2;
my $subject3;
my $subject4;
my $subject5;
my $subject6;
my $subject7;
my $subject8;
my $subject9;
my $subject10;
my $subject11;
my $subject12;
my $subject13;
my $subject14;
my $subject15;
my $from1;
my $from2;
my $from3;
my $from4;
my $from5;
my $from6;
my $from7;
my $from8;
my $from9;
my $from10;
my $shour;
my $status;
my $exclude_days;

#------ connect to the util database ------------------
$util->db_connect();
$dbh = 0;
while (!$dbh)
{
print LOG "Connecting to db\n";
$dbh = $util->get_dbh;
}
$dbh->{mysql_auto_reconnect}=1;

my $aid = $query->param('aid');
if ($aid eq "")
{
	$aid=0;
}
my $cid = $query->param('cid');
my $mode = $query->param('mode');
my $daily_flag = $query->param('daily_flag');
my $cnetwork;
my $cday;
	$sql = "select date_format(curdate(),'%m/%d/%Y')";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	($sdate1) = $sth->fetchrow_array();
	$sth->finish();
if ($mode eq "A")
{
	$cname="";
	$cid = 0;
	$sql = "select date_format(curdate(),'%m/%d/%Y')";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	($sdate) = $sth->fetchrow_array();
	$sth->finish();
	$status="D";
	$content_id = 1;
	$old_pid = 0;
	$old_bid = 0;
}
else
{
	$sql="select campaign_name,creative1_id,creative2_id,creative3_id,creative4_id,creative5_id,creative6_id,creative7_id,creative8_id,creative9_id,creative10_id,creative11_id,creative12_id,creative13_id,creative14_id,creative15_id,subject1,subject2,subject3,subject4,subject5,subject6,subject7,subject8,subject9,subject10,subject11,subject12,subject13,subject14,subject15,from1,from2,from3,from4,from5,from6,from7,from8,from9,from10,date_format(scheduled_datetime,'%m/%d/%Y'),hour(scheduled_datetime),server_id,status,profile_id,brand_id from campaign where campaign_id=$cid";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	($cname,$creative1_id,$creative2_id,$creative3_id,$creative4_id,$creative5_id,$creative6_id,$creative7_id,$creative8_id,$creative9_id,$creative10_id,$creative11_id,$creative12_id,$creative13_id,$creative14_id,$creative15_id,$subject1,$subject2,$subject3,$subject4,$subject5,$subject6,$subject7,$subject8,$subject9,$subject10,$subject11,$subject12,$subject13,$subject14,$subject15,$from1,$from2,$from3,$from4,$from5,$from6,$from7,$from8,$from9,$from10,$sdate,$shour,$content_id,$status,$old_pid,$old_bid) = $sth->fetchrow_array();
	$sth->finish;
	if ($sdate eq "00/00/0000")
	{
		$sdate="";
	}
	if ($status eq "W")
	{
		$daily_flag = "Y";
	}
}
if ($daily_flag eq "Y")
{
	$sql = "select client_id,cday from daily_deals where campaign_id=$cid"; 
	$sth = $dbh->prepare($sql);
	$sth->execute();
	($cnetwork,$cday) = $sth->fetchrow_array();
	$sth->finish();
}
$sql = "select category_id,exclude_days from advertiser_info where advertiser_id=$aid"; 
$sth = $dbh->prepare($sql);
$sth->execute();
($acatid,$exclude_days) = $sth->fetchrow_array();
$sth->finish;
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

function check_date()
{
	ex_str = "$exclude_days";
	sdate = document.campform.tid0.value;
	if (sdate != '')
	{
		var date_arr = sdate.split("/");
		month = date_arr[0] - 1;
        day = date_arr[1];
		year = date_arr[2];
		mydate = new Date(year,month,day);
		myday = mydate.getDay();
		if (myday == 0)
		{
			myday = 6;
		}
		else
		{
			myday = myday - 1;
		}
		b = ex_str.charAt(myday);
		if (b == 'Y')
		{
			alert('Campaign cannot be scheduled on this day of week for the advertiser');
			return false;
		}
	}
	return true;
}
function addOption(value,text)
{
	if (NSX)
	{
		addOptionNS(value,text);
	}
	else if (IE4)
	{
		addOptionIE(value,text);
	}
}
function addCreativeOption(value,text)
{
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    campform.creative1.add(newOpt);
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    campform.creative2.add(newOpt);
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    campform.creative3.add(newOpt);
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    campform.creative4.add(newOpt);
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    campform.creative5.add(newOpt);
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    campform.creative6.add(newOpt);
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    campform.creative7.add(newOpt);
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    campform.creative8.add(newOpt);
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    campform.creative9.add(newOpt);
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    campform.creative10.add(newOpt);
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    campform.creative11.add(newOpt);
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    campform.creative12.add(newOpt);
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    campform.creative13.add(newOpt);
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    campform.creative14.add(newOpt);
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    campform.creative15.add(newOpt);
}
function addTriggerOption(value,text)
{
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    campform.trigger_creative.add(newOpt);
}
function addSubjectOption(value,text)
{
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    campform.subject1.add(newOpt);
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    campform.subject2.add(newOpt);
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    campform.subject3.add(newOpt);
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    campform.subject4.add(newOpt);
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    campform.subject5.add(newOpt);
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    campform.subject6.add(newOpt);
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    campform.subject7.add(newOpt);
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    campform.subject8.add(newOpt);
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    campform.subject9.add(newOpt);
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    campform.subject10.add(newOpt);
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    campform.subject11.add(newOpt);
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    campform.subject12.add(newOpt);
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    campform.subject13.add(newOpt);
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    campform.subject14.add(newOpt);
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    campform.subject15.add(newOpt);
}
function addFromOption(value,text)
{
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    campform.from1.add(newOpt);
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    campform.from2.add(newOpt);
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    campform.from3.add(newOpt);
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    campform.from4.add(newOpt);
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    campform.from5.add(newOpt);
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    campform.from6.add(newOpt);
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    campform.from7.add(newOpt);
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    campform.from8.add(newOpt);
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    campform.from9.add(newOpt);
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    campform.from10.add(newOpt);
}

function addOptionNS(value,text)
{
	var newOpt  = new Option(value, text);
	var selLength = theForm.advertiser_id.length;
	campform.advertiser_id.options[selLength] = newOpt;
	if (NS4) history.go(0);
}

function addOptionIE(value,text)
{
	var newOpt = document.createElement("OPTION");
	newOpt.text=text;
	newOpt.value=value;
	campform.advertiser_id.add(newOpt);
}
function addOption1(value,text)
{
	var newOpt = document.createElement("OPTION");
	newOpt.text=text;
	newOpt.value=value;
	campform.advertiser_id1.add(newOpt);
}
function addBrand(value,text)
{
	var newOpt = document.createElement("OPTION");
	newOpt.text=text;
	newOpt.value=value;
	campform.brand_id.add(newOpt);
}

function update_advertiser(tid)
{
	var selObj = document.getElementById('catid');
	var selIndex = selObj.selectedIndex;
	var selLength = campform.advertiser_id.length;
	while (selLength>0)
	{
		campform.advertiser_id.remove(selLength-1);
		selLength--;
	}
	campform.advertiser_id.length=0;
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
function update_brand()
{
	var selObj = document.getElementById('profile_id');
	var selIndex = selObj.selectedIndex;
	var selLength = campform.brand_id.length;
	while (selLength>0)
	{
		campform.brand_id.remove(selLength-1);
		selLength--;
	}
	campform.brand_id.length=0;
	parent.frames[1].location="/newcgi-bin/upd_brand.cgi?pid="+selObj.options[selIndex].value;
}
function view_thumbnail()
{
	var selObj = document.getElementById('advertiser_id');
	var selIndex = selObj.selectedIndex;
    var newwin = window.open("/newcgi-bin/view_thumbnails.cgi?aid=" + selObj.options[selIndex].value, "Thumbnails", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,width=800,height=500,left=50,top=50");
    newwin.focus();
}

function update_subject()
{
	var selObj = document.getElementById('advertiser_id');
	var selIndex = selObj.selectedIndex;
	var selLength = campform.creative1.length;
	while (selLength>0)
	{
		campform.creative1.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.creative2.length;
	while (selLength>0)
	{
		campform.creative2.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.creative3.length;
	while (selLength>0)
	{
		campform.creative3.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.creative4.length;
	while (selLength>0)
	{
		campform.creative4.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.creative5.length;
	while (selLength>0)
	{
		campform.creative5.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.creative6.length;
	while (selLength>0)
	{
		campform.creative6.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.creative7.length;
	while (selLength>0)
	{
		campform.creative7.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.creative8.length;
	while (selLength>0)
	{
		campform.creative8.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.creative9.length;
	while (selLength>0)
	{
		campform.creative9.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.creative10.length;
	while (selLength>0)
	{
		campform.creative10.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.creative11.length;
	while (selLength>0)
	{
		campform.creative11.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.creative12.length;
	while (selLength>0)
	{
		campform.creative12.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.creative13.length;
	while (selLength>0)
	{
		campform.creative13.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.creative14.length;
	while (selLength>0)
	{
		campform.creative14.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.creative15.length;
	while (selLength>0)
	{
		campform.creative15.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.subject1.length;
	while (selLength>0)
	{
		campform.subject1.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.subject2.length;
	while (selLength>0)
	{
		campform.subject2.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.subject3.length;
	while (selLength>0)
	{
		campform.subject3.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.subject4.length;
	while (selLength>0)
	{
		campform.subject4.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.subject5.length;
	while (selLength>0)
	{
		campform.subject5.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.subject6.length;
	while (selLength>0)
	{
		campform.subject6.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.subject7.length;
	while (selLength>0)
	{
		campform.subject7.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.subject8.length;
	while (selLength>0)
	{
		campform.subject8.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.subject9.length;
	while (selLength>0)
	{
		campform.subject9.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.subject10.length;
	while (selLength>0)
	{
		campform.subject10.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.subject11.length;
	while (selLength>0)
	{
		campform.subject11.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.subject12.length;
	while (selLength>0)
	{
		campform.subject12.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.subject13.length;
	while (selLength>0)
	{
		campform.subject13.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.subject14.length;
	while (selLength>0)
	{
		campform.subject14.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.subject15.length;
	while (selLength>0)
	{
		campform.subject15.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.from1.length;
	while (selLength>0)
	{
		campform.from1.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.from2.length;
	while (selLength>0)
	{
		campform.from2.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.from3.length;
	while (selLength>0)
	{
		campform.from3.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.from4.length;
	while (selLength>0)
	{
		campform.from4.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.from5.length;
	while (selLength>0)
	{
		campform.from5.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.from6.length;
	while (selLength>0)
	{
		campform.from6.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.from7.length;
	while (selLength>0)
	{
		campform.from7.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.from8.length;
	while (selLength>0)
	{
		campform.from8.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.from9.length;
	while (selLength>0)
	{
		campform.from9.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.from10.length;
	while (selLength>0)
	{
		campform.from10.remove(selLength-1);
		selLength--;
	}
	parent.frames[1].location="/newcgi-bin/upd_creative_list.cgi?aid="+selObj.options[selIndex].value;
}
function set_fields(c1,c2,c3,c4,c5,c6,c7,c8,c9,c10,c11,c12,c13,c14,c15,s1,s2,s3,s4,s5,s6,s7,s8,s9,s10,s11,s12,s13,s14,s15,f1,f2,f3,f4,f5,f6,f7,f8,f9,f10,trigger_creative)
{
  	var i;
  	var selObj = document.getElementById('creative1');
  	for (i=0; i<selObj.options.length; i++) { if (selObj.options[i].value == c1) { selObj.selectedIndex = i; break;
    	}
	}
  	var selObj = document.getElementById('creative2');
  	for (i=0; i<selObj.options.length; i++) {
    	if (selObj.options[i].value == c2) { selObj.selectedIndex = i; break; }
	}
  	var selObj = document.getElementById('creative3');
  	for (i=0; i<selObj.options.length; i++) {
    	if (selObj.options[i].value == c3) { selObj.selectedIndex = i; break; }
	}
  	var selObj = document.getElementById('creative4');
  	for (i=0; i<selObj.options.length; i++) {
    	if (selObj.options[i].value == c4) { selObj.selectedIndex = i; break; }
	}
  	var selObj = document.getElementById('creative5');
  	for (i=0; i<selObj.options.length; i++) {
    	if (selObj.options[i].value == c5) { selObj.selectedIndex = i; break; }
	}
  	var selObj = document.getElementById('creative6');
  	for (i=0; i<selObj.options.length; i++) {
    	if (selObj.options[i].value == c6) { selObj.selectedIndex = i; break; }
	}
  	var selObj = document.getElementById('creative7');
  	for (i=0; i<selObj.options.length; i++) {
    	if (selObj.options[i].value == c7) { selObj.selectedIndex = i; break; }
	}
  	var selObj = document.getElementById('creative8');
  	for (i=0; i<selObj.options.length; i++) {
    	if (selObj.options[i].value == c8) { selObj.selectedIndex = i; break; }
	}
  	var selObj = document.getElementById('creative9');
  	for (i=0; i<selObj.options.length; i++) {
    	if (selObj.options[i].value == c9) { selObj.selectedIndex = i; break; }
	}
  	var selObj = document.getElementById('creative10');
  	for (i=0; i<selObj.options.length; i++) {
    	if (selObj.options[i].value == c10) { selObj.selectedIndex = i; break; }
	}
  	var selObj = document.getElementById('creative11');
  	for (i=0; i<selObj.options.length; i++) {
    	if (selObj.options[i].value == c11) { selObj.selectedIndex = i; break; }
	}
  	var selObj = document.getElementById('creative12');
  	for (i=0; i<selObj.options.length; i++) {
    	if (selObj.options[i].value == c12) { selObj.selectedIndex = i; break; }
	}
  	var selObj = document.getElementById('creative13');
  	for (i=0; i<selObj.options.length; i++) {
    	if (selObj.options[i].value == c13) { selObj.selectedIndex = i; break; }
	}
  	var selObj = document.getElementById('creative14');
  	for (i=0; i<selObj.options.length; i++) {
    	if (selObj.options[i].value == c14) { selObj.selectedIndex = i; break; }
	}
  	var selObj = document.getElementById('creative15');
  	for (i=0; i<selObj.options.length; i++) {
    	if (selObj.options[i].value == c15) { selObj.selectedIndex = i; break; }
	}
  	var selObj = document.getElementById('subject1');
  	for (i=0; i<selObj.options.length; i++) {
    	if (selObj.options[i].value == s1) { selObj.selectedIndex = i; break; }
	}
  	var selObj = document.getElementById('subject2');
  	for (i=0; i<selObj.options.length; i++) {
    	if (selObj.options[i].value == s2) { selObj.selectedIndex = i; break; }
	}
  	var selObj = document.getElementById('subject3');
  	for (i=0; i<selObj.options.length; i++) {
    	if (selObj.options[i].value == s3) { selObj.selectedIndex = i; break; }
	}
  	var selObj = document.getElementById('subject4');
  	for (i=0; i<selObj.options.length; i++) {
    	if (selObj.options[i].value == s4) { selObj.selectedIndex = i; break; }
	}
  	var selObj = document.getElementById('subject5');
  	for (i=0; i<selObj.options.length; i++) {
    	if (selObj.options[i].value == s5) { selObj.selectedIndex = i; break; }
	}
  	var selObj = document.getElementById('subject6');
  	for (i=0; i<selObj.options.length; i++) {
    	if (selObj.options[i].value == s6) { selObj.selectedIndex = i; break; }
	}
  	var selObj = document.getElementById('subject7');
  	for (i=0; i<selObj.options.length; i++) {
    	if (selObj.options[i].value == s7) { selObj.selectedIndex = i; break; }
	}
  	var selObj = document.getElementById('subject8');
  	for (i=0; i<selObj.options.length; i++) {
    	if (selObj.options[i].value == s8) { selObj.selectedIndex = i; break; }
	}
  	var selObj = document.getElementById('subject9');
  	for (i=0; i<selObj.options.length; i++) {
    	if (selObj.options[i].value == s9) { selObj.selectedIndex = i; break; }
	}
  	var selObj = document.getElementById('subject10');
  	for (i=0; i<selObj.options.length; i++) {
    	if (selObj.options[i].value == s10) { selObj.selectedIndex = i; break; }
	}
  	var selObj = document.getElementById('subject11');
  	for (i=0; i<selObj.options.length; i++) {
    	if (selObj.options[i].value == s11) { selObj.selectedIndex = i; break; }
	}
  	var selObj = document.getElementById('subject12');
  	for (i=0; i<selObj.options.length; i++) {
    	if (selObj.options[i].value == s12) { selObj.selectedIndex = i; break; }
	}
  	var selObj = document.getElementById('subject13');
  	for (i=0; i<selObj.options.length; i++) {
    	if (selObj.options[i].value == s13) { selObj.selectedIndex = i; break; }
	}
  	var selObj = document.getElementById('subject14');
  	for (i=0; i<selObj.options.length; i++) {
    	if (selObj.options[i].value == s14) { selObj.selectedIndex = i; break; }
	}
  	var selObj = document.getElementById('subject15');
  	for (i=0; i<selObj.options.length; i++) {
    	if (selObj.options[i].value == s15) { selObj.selectedIndex = i; break; }
	}
  	var selObj = document.getElementById('from1');
  	for (i=0; i<selObj.options.length; i++) {
    	if (selObj.options[i].value == f1) { selObj.selectedIndex = i; break; }
	}
  	var selObj = document.getElementById('from2');
  	for (i=0; i<selObj.options.length; i++) {
    	if (selObj.options[i].value == f2) { selObj.selectedIndex = i; break; }
	}
  	var selObj = document.getElementById('from3');
  	for (i=0; i<selObj.options.length; i++) {
    	if (selObj.options[i].value == f3) { selObj.selectedIndex = i; break; }
	}
  	var selObj = document.getElementById('from4');
  	for (i=0; i<selObj.options.length; i++) {
    	if (selObj.options[i].value == f4) { selObj.selectedIndex = i; break; }
	}
  	var selObj = document.getElementById('from5');
  	for (i=0; i<selObj.options.length; i++) {
    	if (selObj.options[i].value == f5) { selObj.selectedIndex = i; break; }
	}
  	var selObj = document.getElementById('from6');
  	for (i=0; i<selObj.options.length; i++) {
    	if (selObj.options[i].value == f6) { selObj.selectedIndex = i; break; }
	}
  	var selObj = document.getElementById('from7');
  	for (i=0; i<selObj.options.length; i++) {
    	if (selObj.options[i].value == f7) { selObj.selectedIndex = i; break; }
	}
  	var selObj = document.getElementById('from8');
  	for (i=0; i<selObj.options.length; i++) {
    	if (selObj.options[i].value == f8) { selObj.selectedIndex = i; break; }
	}
  	var selObj = document.getElementById('from9');
  	for (i=0; i<selObj.options.length; i++) {
    	if (selObj.options[i].value == f9) { selObj.selectedIndex = i; break; }
	}
  	var selObj = document.getElementById('from10');
  	for (i=0; i<selObj.options.length; i++) {
    	if (selObj.options[i].value == f10) { selObj.selectedIndex = i; break; }
	}
  	var selObj = document.getElementById('trigger_creative');
  	for (i=0; i<selObj.options.length; i++) {
    	if (selObj.options[i].value == trigger_creative) { selObj.selectedIndex = i; break; }
	}
}

function update_subject1(aid)
{
  	var selObj = document.getElementById('advertiser_id');
  	var i;
  	for (i=0; i<selObj.options.length; i++) {
    	if (selObj.options[i].value == aid) 
		{
      		selObj.selectedIndex = i;
    	}
  	}

	var selLength = campform.creative1.length;
	while (selLength>0)
	{
		campform.creative1.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.creative2.length;
	while (selLength>0)
	{
		campform.creative2.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.creative3.length;
	while (selLength>0)
	{
		campform.creative3.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.creative4.length;
	while (selLength>0)
	{
		campform.creative4.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.creative5.length;
	while (selLength>0)
	{
		campform.creative5.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.creative6.length;
	while (selLength>0)
	{
		campform.creative6.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.creative7.length;
	while (selLength>0)
	{
		campform.creative7.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.creative8.length;
	while (selLength>0)
	{
		campform.creative8.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.creative9.length;
	while (selLength>0)
	{
		campform.creative9.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.creative10.length;
	while (selLength>0)
	{
		campform.creative10.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.creative11.length;
	while (selLength>0)
	{
		campform.creative11.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.creative12.length;
	while (selLength>0)
	{
		campform.creative12.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.creative13.length;
	while (selLength>0)
	{
		campform.creative13.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.creative14.length;
	while (selLength>0)
	{
		campform.creative14.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.creative15.length;
	while (selLength>0)
	{
		campform.creative15.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.subject1.length;
	while (selLength>0)
	{
		campform.subject1.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.subject2.length;
	while (selLength>0)
	{
		campform.subject2.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.subject3.length;
	while (selLength>0)
	{
		campform.subject3.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.subject4.length;
	while (selLength>0)
	{
		campform.subject4.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.subject5.length;
	while (selLength>0)
	{
		campform.subject5.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.subject6.length;
	while (selLength>0)
	{
		campform.subject6.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.subject7.length;
	while (selLength>0)
	{
		campform.subject7.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.subject8.length;
	while (selLength>0)
	{
		campform.subject8.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.subject9.length;
	while (selLength>0)
	{
		campform.subject9.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.subject10.length;
	while (selLength>0)
	{
		campform.subject10.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.subject11.length;
	while (selLength>0)
	{
		campform.subject11.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.subject12.length;
	while (selLength>0)
	{
		campform.subject12.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.subject13.length;
	while (selLength>0)
	{
		campform.subject13.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.subject14.length;
	while (selLength>0)
	{
		campform.subject14.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.subject15.length;
	while (selLength>0)
	{
		campform.subject15.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.from1.length;
	while (selLength>0)
	{
		campform.from1.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.from2.length;
	while (selLength>0)
	{
		campform.from2.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.from3.length;
	while (selLength>0)
	{
		campform.from3.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.from4.length;
	while (selLength>0)
	{
		campform.from4.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.from5.length;
	while (selLength>0)
	{
		campform.from5.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.from6.length;
	while (selLength>0)
	{
		campform.from6.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.from7.length;
	while (selLength>0)
	{
		campform.from7.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.from8.length;
	while (selLength>0)
	{
		campform.from8.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.from9.length;
	while (selLength>0)
	{
		campform.from9.remove(selLength-1);
		selLength--;
	}
	var selLength = campform.from10.length;
	while (selLength>0)
	{
		campform.from10.remove(selLength-1);
		selLength--;
	}
	var selObj = document.getElementById('advertiser_id1');
	var selLength = campform.trigger_creative.length;
	while (selLength>0)
	{
		campform.trigger_creative.remove(selLength-1);
		selLength--;
	}
	parent.frames[1].location="/newcgi-bin/upd_creative_list.cgi?aid="+aid+"&tid=1&cid="+campform.cid.value;
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
</script>
</head>

<body>

<table cellSpacing="0" cellPadding="0" align="left" bgColor="#ffffff" border="0" id="table1">
	<tr vAlign="top">
		<td noWrap align="left">
		<table cellSpacing="0" cellPadding="0" width="719" border="0" id="table2">
			<tr>
				<td width="248" bgColor="#ffffff" rowSpan="2">&nbsp;</td>
				<td width="328" bgColor="#ffffff">&nbsp;</td>
			</tr>
			<tr>
				<td width="468">
				<table cellSpacing="0" cellPadding="0" width="100%" border="0" id="table3">
					<tr>
						<td align="left"><b><font face="Arial" size="2">&nbsp;CREATE 
						EMAIL</font></b></td>
					</tr>
					<tr>
						<td align="right"><b>
						<a style="TEXT-DECORATION: none" href="http://66.54.87.41:81/cgi-bin/logout.cgi">
						<font face="Arial" color="#509c10" size="2">Logout</font></a>&nbsp;&nbsp;&nbsp;
						<a style="TEXT-DECORATION: none" href="http://66.54.87.41:81/cgi-bin/wss_support_form.cgi">
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
				<form name="campform" method="post" action="/cgi-bin/camp_step5_save.cgi" target="_top">
					<input type="hidden" name="aid" value=$aid>
					<input type="hidden" name="cid" value=$cid>
					<input type="hidden" name="nextfunc">
					<input type="hidden" name="status" value=$status>
					<input type="hidden" name="daily_flag" value=$daily_flag>
					<table cellSpacing="0" cellPadding="0" width="660" bgColor="#ffffff" border="0" id="table7">
						<tr>
							<td vAlign="top">
							<table cellSpacing="0" cellPadding="0" width="660" bgColor="#ffffff" border="0" id="table8">
								<tr>
									<td vAlign="top" align="middle" width="195">
									<img height="7" src="/images/spacer.gif" width="190">
<SCRIPT language=Javascript>

        function SaveFunc(btn)

        {

            if (SaveFunc.arguments.length == 2)

            {

                document.campform.article.value = SaveFunc.arguments[1];

            }

            document.campform.nextfunc.value = btn;
end_of_html
if ($daily_flag ne "Y")
{
print<<"end_of_html";
			if (check_date())
			{
            	document.campform.submit();
			}
end_of_html
}
else
{
	print "document.campform.submit();\n";
}
print<<"end_of_html";

        }

        </SCRIPT>
 
									<table cellSpacing="0" cellPadding="0" width="190" border="0" id="table9">
										<tr bgColor="#d6c6ff">
											<td vAlign="top" align="left" width="9" height="7">
											<img height="7" src="/images/yellow_tl.gif" width="8" border="0"></td>
											<td vAlign="top" align="right" width="100%">
											<img height="7" src="/images/yellow_tr.gif" width="8" border="0"></td>
										</tr>
										<tr bgColor="#d6c6ff">
											<td vAlign="bottom" colSpan="2" height="7">
											&nbsp;&nbsp;
											<font face="Verdana,Arial,Helvetica,sans-serif" color="#509c10" size="2">
											<b>Introduction </b></font></td>
										</tr>
										<tr bgColor="#d6c6ff">
											<td>&nbsp;&nbsp;&nbsp;&nbsp; </td>
											<td vAlign="bottom" height="12">
											<font face="Verdana,Arial,Helvetica,sans-serif" color="#509c10" size="1">
											Catch your reader's attention with a 
											compelling message <br>
&nbsp;</font></td>
										</tr>
										<tr bgColor="#d6c6ff">
											<td vAlign="bottom" align="left" height="7">
											<img height="7" src="/images/yellow_bl.gif" width="8" border="0"></td>
											<td vAlign="bottom" align="right">
											<img height="7" src="/images/yellow_br.gif" width="8" border="0"></td>
										</tr> 
									</table>
									<img height="7" src="/images/spacer.gif" width="190"> 
<!--									<table cellSpacing="0" cellPadding="0" width="190" border="0" id="table10">
										<tr bgColor="#e3fad1">
											<td vAlign="top" align="left" width="9" height="7">
											<img height="7" src="/images/lt_purp_tl.gif" width="8" border="0"></td>
											<td vAlign="top" align="right" width="100%">
											<img height="7" src="/images/lt_purp_tr.gif" width="8" border="0"></td>
										</tr>
										<tr bgColor="#e3fad1">
											<td vAlign="bottom" align="left" height="7">
											<img height="7" src="/images/lt_purp_bl.gif" width="8" border="0"></td>
											<td vAlign="bottom" align="right">
											<img height="7" src="/images/lt_purp_br.gif" width="8" border="0"></td>
										</tr>
									</table> -->
									</td>
									<td vAlign="top" align="middle" width="465">
									<img height="7" src="/images/spacer.gif" width="455"> <!-- Begin main body area -->
									<table cellSpacing="0" cellPadding="0" width="455" bgColor="#e3fad1" border="0" id="table11">
										<tr bgColor="#509c10">
											<td vAlign="top" align="left" height="15">
											<img height="7" src="/images/blue_tl.gif" width="7" border="0"></td>
											<td align="middle" height="15">
											<font face="verdana,arial,helvetica,sans serif" color="#ffffff" size="2">
											<b>Introduction</b></font></td>
											<td vAlign="top" align="right" bgColor="#509c10" height="15">
											<img height="7" src="/images/blue_tr.gif" width="7" border="0"></td>
										</tr>
										<tr>
											<td colSpan="3">&nbsp;&nbsp;
											<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
											<b>Campaign Name</b></font></td>
										</tr>
										<tr>
											<td colSpan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<input maxLength="30" size="46" name="campaign_name" value="$cname"> 
											</td>
										</tr>
										<tr>
											<td colSpan="3">&nbsp;</td>
										</tr>										<tr>
											<td colSpan="3">&nbsp;&nbsp;
											<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
											<b>Scheduled For (Today's Date: $sdate1)</b></font></td>
										</tr>
end_of_html
if ($daily_flag eq "Y")
{
	print "<tr><td colspan=3>&nbsp;&nbsp;Day&nbsp;&nbsp;<select name=cday>\n";
	my $i;
	$i = 1;
	while ($i <=5)
	{
		if ($cday == $i)
		{
			print "<option selected value=$i>$i</option>";
		}
		else
		{
			print "<option value=$i>$i</option>";
		}
		$i++;
	}
	print "</select>";
}
else
{
print<<"end_of_html";
										<tr>
											<td colSpan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<input maxLength="10" size="10" name="tid0" value="$sdate">&nbsp;&nbsp;
end_of_html
}
print<<"end_of_html";
											<select name=tid1>
end_of_html
my $i = 1;
my $thour = $shour;
if ($shour > 12)
{
	$thour = $shour - 12;
}
elsif ($shour == 0)
{
	$thour = 12;
}
while ($i < 13)
{
	if ($i == $thour)
	{
		print "<option value=$i selected>$i</option>";
	}
	else
	{
		print "<option value=$i>$i</option>";
	}
	$i++;
}
print<<"end_of_html";
</select>&nbsp;
											<select name="am_pm">
end_of_html
if ($shour < 12)
{
	print "<option value=\"AM\" selected>AM</option><option value=\"PM\">PM</option>\n";
}
else
{
	print "<option value=\"AM\">AM</option><option value=\"PM\" selected>PM</option>\n";
}
print<<"end_of_html";
											</select></td>
										</tr>
											<tr>
											<td colSpan="3">&nbsp;</td><br>
									
									
										<tr>
											<td colSpan="3">&nbsp;&nbsp;
											<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
											<b>Category (this will narrow down 
											the offers listed)</b></font></td>
										</tr>
										<tr>
											<td colSpan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<select name="catid" onChange="update_advertiser(0);">
											<option value="-1">ALL CATEGORIES
											</option>
end_of_html
my $category_name;
$sql = "select category_id,category_name from category_info order by category_name";
$sth = $dbh->prepare($sql);
$sth->execute();
my $catid;
my $cname;
while (($catid,$cname) = $sth->fetchrow_array())
{
	if ($catid == $acatid)
	{
		print "<option selected value=$catid>$cname</option>\n";
	}
	else
	{
		print "<option value=$catid>$cname</option>\n";
	}
}
$sth->finish;
print<<"end_of_html";
											</select></td>
										</tr>
										
										<tr>
											<td colSpan="3">&nbsp;</td>
										</tr>
										<tr>
											<td colSpan="3">&nbsp;&nbsp;
											<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
											<b><a href="advertiser_list.cgi" target="_blank">Advertiser</a></b></font>
											</td>
										</tr>
										<tr>
											<td colSpan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<select name="advertiser_id" onChange="update_subject();">
											</select> </td>
										</tr>
										<tr>
											<td colSpan="3">&nbsp;</td>
										</tr>
end_of_html
if ($daily_flag eq "Y")
{
	my $tuid;
	my $tcomp;
	print "<tr><td colspan=3>Network&nbsp;&nbsp;<select name=cnetwork>\n";
	$sql = "select user_id,company from user where status != 'D' order by company";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	while (($tuid,$tcomp) = $sth->fetchrow_array())
	{
		if ($tuid == $cnetwork)
		{
			print "<option selected value=$tuid>$tcomp</option>\n";
		}
		else
		{
			print "<option value=$tuid>$tcomp</option>\n";
		}
	}
	$sth->finish();
	print "</td></tr>\n";
}
else
{
print<<"end_of_html";	
<tr><td colSpan="3">&nbsp;&nbsp;<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2"><b>List Profile</b></font></td></tr>
<tr><td colSpan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<select name=profile_id onChange="update_brand();">
end_of_html
my $pid;
my $pname;
my $company;
$sql = "select profile_id,profile_name,company from list_profile,user where list_profile.status='A' and list_profile.client_id=user.user_id order by company,profile_name";
$sth = $dbh->prepare($sql);
$sth->execute();
while (($pid,$pname,$company) = $sth->fetchrow_array())
{
	if ($pid == $old_pid)
	{
		print "<option selected value=$pid>$company - $pname</option>\n";
	}
	else
	{
		print "<option value=$pid>$company - $pname</option>\n";
	}
}
$sth->finish();
print<<"end_of_html";
</select></td></tr>
<tr><td colSpan="3">&nbsp;&nbsp;<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2"><b>Brand</b></font></td></tr>
<tr><td colSpan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<select name=brand_id>
end_of_html
my $bid;
my $bname;
if ($old_pid > 0)
{
	$sql = "select brand_id,brand_name,company from client_brand_info,user where client_id=user_id and client_brand_info.status='A' and user_id in (select client_id from list_profile where profile_id=$old_pid) order by brand_name"; 
}
else
{
	$sql = "select brand_id,brand_name,company from client_brand_info,user where client_id=user_id and client_brand_info.status='A' order by brand_name"; 
}
$sth = $dbh->prepare($sql);
$sth->execute();
my $clname;
if ($old_bid == 0)
{
	print "<option selected value=0>N/A</option>\n";
}
else
{
	print "<option value=0>N/A</option>\n";
}
while (($bid,$bname,$clname) = $sth->fetchrow_array())
{
	if ($bid == $old_bid)
	{
		print "<option selected value=$bid>$clname - $bname</option>\n";
	}
	else
	{
		print "<option value=$bid>$clname - $bname</option>\n";
	}
}
$sth->finish();
print<<"end_of_html";
</select></td></tr>
end_of_html
}
print<<"end_of_html";	
										<tr>
											<td colSpan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											</td>
										</tr>
									
										
										<tr>
											<td colSpan="3">&nbsp;&nbsp;
											<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
											<b><a href="creative_list.cgi" target="_blank">Creative Name</a>(<a href="javascript:view_thumbnail();">View Thumbnails</a>) **See notes at 
											bottom</b></font></td>
										</tr>
										<tr>
											<td colSpan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<select name="creative1">
<option value="0">
SELECT ONE</option>
</select> 
											</td>
										</tr>
										<tr>
											<td colSpan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<select name="creative2">
<option value="0">
SELECT ONE</option>
</select> 
											</td>
										</tr>
										<tr>
											<td colSpan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<select name="creative3">
<option value="0">
SELECT ONE</option>
</select> 
											</td>
										</tr>
										<tr><td colSpan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<select name="creative4">
<option value="0">
SELECT ONE</option>
</select> 
											</td></tr>
										<tr><td colSpan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<select name="creative5">
<option value="0">
SELECT ONE</option>
</select> 
											</td></tr>
										<tr><td colSpan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<select name="creative6">
<option value="0">
SELECT ONE</option>
</select> 
											</td></tr>
										<tr><td colSpan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<select name="creative7">
<option value="0">
SELECT ONE</option>
</select> 
											</td></tr>
										<tr><td colSpan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<select name="creative8">
<option value="0">
SELECT ONE</option>
</select> 
											</td></tr>
										<tr><td colSpan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<select name="creative9">
<option value="0">
SELECT ONE</option>
</select> 
											</td></tr>
										<tr><td colSpan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<select name="creative10">
<option value="0">
SELECT ONE</option>
</select> 
											</td></tr>
										<tr><td colSpan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<select name="creative11">
<option value="0">
SELECT ONE</option>
</select> 
											</td></tr>
										<tr><td colSpan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<select name="creative12">
<option value="0">
SELECT ONE</option>
</select> 
											</td></tr>
										<tr><td colSpan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<select name="creative13">
<option value="0">
SELECT ONE</option>
</select> 
											</td></tr>
										<tr><td colSpan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<select name="creative14">
<option value="0">
SELECT ONE</option>
</select> 
											</td></tr>
										<tr><td colSpan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<select name="creative15">
<option value="0">
SELECT ONE</option>
</select> 
											</td></tr>
										<tr><td colSpan="3">&nbsp;</td></tr>
										
        

										<tr>
											<td colSpan="3">&nbsp;&nbsp;
											<b>
											<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
											Subject(s)</font>
											<font face="verdana,arial,helvetica,sans serif" color="#509C10" size="2">
											**(can only have mult subject or from)</font></b></td>
										</tr>
										<tr>
											<td colSpan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<select name="subject1">
<option value="Select One">SELECT ONE</option></select> 
 </td>
										</tr>
										<tr>
											<td colSpan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<select name="subject2">
<option value="Select One">SELECT ONE</option></select> 
</td>
										</tr>
										<tr>
											<td colSpan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<select name="subject3">
<option value="Select One">SELECT ONE</option></select> 
</td>
										</tr>
										<tr>
											<td colSpan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<select name="subject4">
<option value="Select One">SELECT ONE</option></select> 
</td>
										</tr>
										<tr>
											<td colSpan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<select name="subject5">
<option value="Select One">SELECT ONE</option></select> 
</td>
										</tr>
										<tr>
											<td colSpan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<select name="subject6">
<option value="Select One">SELECT ONE</option></select> 
</td>
										</tr>
										<tr>
											<td colSpan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<select name="subject7">
<option value="Select One">SELECT ONE</option></select> 
</td>
										</tr>
										<tr>
											<td colSpan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<select name="subject8">
<option value="Select One">SELECT ONE</option></select> 
</td>
										</tr>
										<tr>
											<td colSpan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<select name="subject9">
<option value="Select One">SELECT ONE</option></select> 
</td>
										</tr>
										<tr>
											<td colSpan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<select name="subject10">
<option value="Select One">SELECT ONE</option></select> 
</td>
										</tr>
										<tr>
											<td colSpan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<select name="subject11">
<option value="Select One">SELECT ONE</option></select> 
</td>
										</tr>
										<tr>
											<td colSpan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<select name="subject12">
<option value="Select One">SELECT ONE</option></select> 
</td>
										</tr>
										<tr>
											<td colSpan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<select name="subject13">
<option value="Select One">SELECT ONE</option></select> 
</td>
										</tr>
										<tr>
											<td colSpan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<select name="subject14">
<option value="Select One">SELECT ONE</option></select> 
</td>
										</tr>
										<tr>
											<td colSpan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<select name="subject15">
<option value="Select One">SELECT ONE</option></select> 
</td>
										</tr>
										<tr>
											<td colSpan="3">&nbsp;</td>
										</tr>
										<tr>
											<td colSpan="3">&nbsp;&nbsp;
											<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
											<b>From Address</b></font>
											<font face="verdana,arial,helvetica,sans serif" color="#509C10" size="2">
											<b>
											**</b></font></td>
										</tr>
										<tr>
											<td colSpan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<select name="from1">
<option value="Select One">SELECT ONE</option></select> 
											</td>
										</tr>
										</tr>
										</tr>
										<tr>
											<td colSpan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<select name="from2">
<option value="Select One">SELECT ONE</option></select> 
											</td>
										</tr>
										<tr>
											<td colSpan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<select name="from3">
<option value="Select One">SELECT ONE</option></select> 
											</td>
										</tr>
										<tr>
											<td colSpan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<select name="from4">
<option value="Select One">SELECT ONE</option></select> 
											</td>
										</tr>
										<tr>
											<td colSpan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<select name="from5">
<option value="Select One">SELECT ONE</option></select> 
											</td>
										</tr>
										<tr>
											<td colSpan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<select name="from6">
<option value="Select One">SELECT ONE</option></select> 
											</td>
										</tr>
										<tr>
											<td colSpan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<select name="from7">
<option value="Select One">SELECT ONE</option></select> 
											</td>
										</tr>
										<tr>
											<td colSpan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<select name="from8">
<option value="Select One">SELECT ONE</option></select> 
											</td>
										</tr>
										<tr>
											<td colSpan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<select name="from9">
<option value="Select One">SELECT ONE</option></select> 
											</td>
										</tr>
										<tr>
											<td colSpan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<select name="from10">
<option value="Select One">SELECT ONE</option></select> 
											</td>
										</tr>
										<tr><td colSpan="3">&nbsp;</td></tr>
                <TR>
                <TD colSpan=3>&nbsp;&nbsp; <FONT face="verdana,arial,helvetica,s
ans serif" color=#509C10 size=2><B>Bandwidth Provider - Only effects non-yahoo mailings</B></FONT></TD>
                </TR>
                <TR>
                <TD colSpan=3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <select name=content_id>
end_of_html
$sql = "select content_id,content_str from content_info order by content_str";
$sth = $dbh->prepare($sql);
$sth->execute();
while (($catid,$category_name) = $sth->fetchrow_array())
{
    if ($catid == $content_id)
    {
        print "<option selected value=$catid>$category_name</option>\n";
    }
    else
    {
        print "<option value=$catid>$category_name</option>\n";
    }
}
$sth->finish();
print <<"end_of_html";
                </select></td>
                </tr>
										<tr>
											<td colSpan="3">&nbsp;</td>
										</tr>
										<tr>
											<td colSpan="3">&nbsp;</td>
										</tr>
										<tr>
											<td colSpan="3">&nbsp;&nbsp;
											<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
											<b>Campaign to Email On Click</b></font></td>
										</tr>
										</tr>
										<tr>
											<td colSpan="3">&nbsp;</td>
										</tr>
										<tr>
											<td colSpan="3">&nbsp;&nbsp;
											<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
											<b>Category(this will narrow down 
											the offers listed)</b></font></td>
										</tr>
										<tr>
											<td colSpan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<select name="catid1" onChange="update_advertiser1(0);">
											<option value="-1">ALL CATEGORIES
											</option>
end_of_html
$sql = "select category_id,category_name from category_info order by category_name";
$sth = $dbh->prepare($sql);
$sth->execute();
my $catid;
my $cname;
while (($catid,$cname) = $sth->fetchrow_array())
{
    print "<option value=$catid>$cname</option>\n";
}
$sth->finish;
print<<"end_of_html";
											</select></td>
										</tr>
										<tr>
											<td colSpan="3">&nbsp;</td>
										</tr>
										
										<tr>
											<td colSpan="3">&nbsp;&nbsp;
											<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
											<b>&nbsp; Advertiser</b></font></td>
										</tr>
										<tr>
											<td colSpan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<select name="advertiser_id1" onChange="update_creative();">
											<option value="" selected>NONE
											</option>
											</select> </td>
										</tr>
										<tr>
											<td colSpan="3">&nbsp;</td>
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
										<tr>
											<td colSpan="3">&nbsp;</td>
										</tr>
										<tr>
											<td colSpan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </td>
										</tr>
	
										
										<tr>
											<td>&nbsp;</td>
											<td>
end_of_html
if (($status eq "D") || ($status eq "S") || ($status eq "W"))
{
print<<"end_of_html";
											<table cellSpacing="0" cellPadding="0" width="100%" border="0" id="table12">
												<tr>
													<td align="middle" width="50%">
													<a href="JavaScript:SaveFunc('save');">
													<img height="22" src="/images/save_rev.gif" width="81" border="0"></a> 
													</td>
												</tr>
											</table>
end_of_html
}
print<<"end_of_html";
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
								<tr>
									<td align="right">
end_of_html
if (($status eq "D") || ($status eq "S") || ($status eq "W"))
{
	print "<a href=\"JavaScript:SaveFunc('exit');\">\n";
}
else
{
	print "<a href=\"/cgi-bin/mainmenu.cgi\" target=_top>\n";
}
print<<"end_of_html";
									<img height="22" src="/images/exit_wizard.gif" width="90" border="0"></a>
									<img height="1" src="/images/spacer.gif" width="130" border="0"> 
									</td>
								</tr>
							</table>
							</td>
						</tr>
						<tr>
							<td>
							<img height="7" src="/images/spacer.gif"></td>
						</tr>
						<tr>
							<td vAlign="center" align="left">
							<font face="verdana,arial,helvetica,sans serif" color="#000000" size="2">
							Your Email Privacy Policy</font></td>
						</tr>
						<tr>
							<td>
							<img height="7" src="/images/spacer.gif"><b><font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">**</font><font face="verdana,arial,helvetica,sans serif" size="2">It 
							will be possible to select the same data on multiple 
							drop downs so that it will appear more than the 
							others.&nbsp; This will change the calculation for 
							the stats.</font></b></td>
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
    update_advertiser(1);
end_of_html
if ($status eq "S")
{
	if ($old_bid == 0)
	{
		print "alert('No Brand Selected.');\n";
	}
    $sql = "select distinct user.user_id,company from list_profile,campaign,user where list_profile.profile_id=campaign.profile_id and campaign_id=$cid and list_profile.client_id=user.user_id";
	$sth1 = $dbh->prepare($sql);
	$sth1->execute();
	while (($temp_id,$company) = $sth1->fetchrow_array())
	{
		$sql = "select count(*) from advertiser_tracking,campaign where client_id =$temp_id and campaign.advertiser_id=advertiser_tracking.advertiser_id and campaign_id=$cid";
		if ($daily_flag eq "Y")
		{
			$sql = $sql . " and daily_deal='Y'";
		}
		else
		{
			$sql = $sql . " and daily_deal='N'";
		}
		$sth2 = $dbh->prepare($sql);
		$sth2->execute();
		($linkcnt) = $sth2->fetchrow_array();
		$sth2->finish();
		if ($linkcnt <= 0)
		{
			print "alert('No URL defined for $company for this advertiser');\n";
		}
		#
		# Check to see if this deal scheduled for same client in last 3 days
		#
		$sql = "select count(*) from campaign where advertiser_id=$aid and campaign_id != $cid and scheduled_datetime >= date_sub(curdate(),interval 3 day) and campaign_id in (select distinct campaign_id from campaign,list_profile where list_profile.profile_id=campaign.profile_id and client_id=$temp_id)";
		$sth2 = $dbh->prepare($sql);
		$sth2->execute();
		($linkcnt) = $sth2->fetchrow_array();
		$sth2->finish();
		if ($linkcnt > 0)
		{
			print "alert('This deal has been scheduled for $company in the last 3 days!');\n";
		}
	}
	$sth1->finish();
	#
	# Check to see if no suppression list defined or old
	#
	my $supp_id;
	my $daycnt;
	$sql = "select vendor_supp_list_id from advertiser_info where advertiser_id=(select advertiser_id from campaign where campaign_id=$cid)";
	$sth1 = $dbh->prepare($sql);
	$sth1->execute();
	($supp_id) = $sth1->fetchrow_array();
	$sth1->finish();
	if ($supp_id == 1)
	{
			print "alert('No Suppression List defined for this advertiser');\n";
	}
	else
	{
		$sql="select datediff(curdate(),last_updated) from vendor_supp_list_info where list_id=$supp_id";
		$sth1 = $dbh->prepare($sql);
		$sth1->execute();
		($daycnt) = $sth1->fetchrow_array();
		$sth1->finish();
		if ($daycnt > 7)
		{
			print "alert('Suppression List has not been updated in last 7 days for this advertiser');\n";
		}
	}
}
print<<"end_of_html";
</script>
</body>
</html>
end_of_html
exit(0);
