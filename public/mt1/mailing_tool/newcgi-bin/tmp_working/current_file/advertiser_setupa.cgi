#!/usr/bin/perl

# *****************************************************************************************
# advertiser_setupa.cgi
#
# this page display main page for setting up creative info for an advertiser 
#
# History
# Jim Sobeck, 06/28/05, Creation
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
my $trigger;
my $trigger2;

#------ connect to the util database ------------------
$util->db_connect();
$dbh = 0;
while (!$dbh)
{
print LOG "Connecting to db\n";
$dbh = $util->get_dbh;
}
$dbh->{mysql_auto_reconnect}=1;

$aid = $query->param('aid');
my $cnetwork;
my $cday;
$sql = "select advertiser_name from advertiser_info where advertiser_id=$aid";
$sth = $dbh->prepare($sql);
$sth->execute();
($cname) = $sth->fetchrow_array();
$sth->finish;
$sql="select creative1_id,creative2_id,creative3_id,creative4_id,creative5_id,creative6_id,creative7_id,creative8_id,creative9_id,creative10_id,creative11_id,creative12_id,creative13_id,creative14_id,creative15_id,subject1,subject2,subject3,subject4,subject5,subject6,subject7,subject8,subject9,subject10,subject11,subject12,subject13,subject14,subject15,from1,from2,from3,from4,from5,from6,from7,from8,from9,from10,trigger_creative,trigger_creative2 from advertiser_setup where advertiser_id=$aid";
$sth = $dbh->prepare($sql);
$sth->execute();
($creative1_id,$creative2_id,$creative3_id,$creative4_id,$creative5_id,$creative6_id,$creative7_id,$creative8_id,$creative9_id,$creative10_id,$creative11_id,$creative12_id,$creative13_id,$creative14_id,$creative15_id,$subject1,$subject2,$subject3,$subject4,$subject5,$subject6,$subject7,$subject8,$subject9,$subject10,$subject11,$subject12,$subject13,$subject14,$subject15,$from1,$from2,$from3,$from4,$from5,$from6,$from7,$from8,$from9,$from10,$trigger,$trigger2) = $sth->fetchrow_array();
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
function addTriggerOption2(value,text)
{
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    campform.trigger_creative2.add(newOpt);
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
function view_thumbnail()
{
    var newwin = window.open("/newcgi-bin/view_thumbnails.cgi?aid=$aid", "Thumbnails", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,width=800,height=500,left=50,top=50");
    newwin.focus();
}

function update_subject()
{
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
	parent.frames[1].location="/newcgi-bin/upd_adv_creative_list.cgi?aid=$aid";
}
function set_fields(c1,c2,c3,c4,c5,c6,c7,c8,c9,c10,c11,c12,c13,c14,c15,s1,s2,s3,s4,s5,s6,s7,s8,s9,s10,s11,s12,s13,s14,s15,f1,f2,f3,f4,f5,f6,f7,f8,f9,f10,trigger_creative,trigger_creative2,catid1,catid2,advertiser_id1,advertiser_id2)
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

function update_subject1(aid)
{
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
						<td align="left"><b><font face="Arial" size="2">&nbsp;Advertiser Setup</font></b></td>
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
				<form name="campform" method="post" action="/cgi-bin/advertiser_setup_save.cgi" target="_top">
					<input type="hidden" name="aid" value=$aid>
					<input type="hidden" name="nextfunc">
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
           	document.campform.submit();
        }

        </SCRIPT>
 
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
											<b>Advertiser: $cname</b></font></td>
											<td vAlign="top" align="right" bgColor="#509c10" height="15">
											<img height="7" src="/images/blue_tr.gif" width="7" border="0"></td>
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
										<tr>
											<td colSpan="3">&nbsp;</td>
										</tr>
										<tr>
											<td colSpan="3">&nbsp;</td>
										</tr>
										<tr>
											<td colSpan="3">&nbsp;&nbsp;
											<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
											<b>Trigger Email #1</b></font></td>
										</tr>
										</tr>
										<tr>
											<td colSpan="3">&nbsp;</td>
										</tr>
										<tr>
											<td colSpan="3">&nbsp;&nbsp;
											<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
											<b>Category(this will narrow down the offers listed)</b></font></td>
										</tr>
										<tr>
											<td colSpan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<select name="catid1" onChange="update_advertiser1(0);">
											<option value="-1">ALL CATEGORIES</option>
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
											<option value="-1" selected>NONE
											</option>
end_of_html
if ($trigger > 0)
{
	$sql = "select advertiser_id,advertiser_name from advertiser_info where category_id in (select category_id from advertiser_info,creative where creative_id=$trigger and creative.advertiser_id=advertiser_info.advertiser_id) and status='A' order by advertiser_name";
}
else
{
	$sql = "select advertiser_id,advertiser_name from advertiser_info where status='A' order by advertiser_name"; 
}
$sth = $dbh->prepare($sql);
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
											<td colSpan="3">&nbsp;&nbsp;
											<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
											<b>Trigger Email #2</b></font></td>
										</tr>
										</tr>
										<tr>
											<td colSpan="3">&nbsp;</td>
										</tr>
										<tr>
											<td colSpan="3">&nbsp;&nbsp;
											<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2"><b>Category(this will narrow down the offers listed)</b></font></td></tr>
										<tr>
											<td colSpan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<select name="catid2" onChange="update_advertiser2(0);">
											<option value="-1">ALL CATEGORIES</option>
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
											<select name="advertiser_id2" onChange="update_creative2();">
											<option value="-1" selected>NONE
											</option>
end_of_html
if ($trigger2 > 0)
{
	$sql = "select advertiser_id,advertiser_name from advertiser_info where category_id in (select category_id from advertiser_info,creative where creative_id=$trigger2 and creative.advertiser_id=advertiser_info.advertiser_id) and status='A' order by advertiser_name";
}
else
{
	$sql = "select advertiser_id,advertiser_name from advertiser_info where status='A' order by advertiser_name"; 
}
$sth = $dbh->prepare($sql);
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
											<b>&nbsp;</b></font>&nbsp;<select name="trigger_creative2">
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
											<table cellSpacing="0" cellPadding="0" width="100%" border="0" id="table12">
												<tr>
													<td align="middle" width="50%">
													<a href="JavaScript:SaveFunc('save');">
													<img height="22" src="/images/save_rev.gif" width="81" border="0"></a> <a href="/cgi-bin/advertiser_disp2.cgi?puserid=$aid"><img src="/images/cancel.gif" border=0></a>
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
						<tr><td>
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
    update_subject();
</script>
</body>
</html>
end_of_html
exit(0);
