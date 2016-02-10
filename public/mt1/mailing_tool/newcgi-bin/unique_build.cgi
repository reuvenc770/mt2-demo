#!/usr/bin/perl
#===============================================================================
# Name   : unique_build.cgi - Build/Deploy a Unique Campaign 
#
#--Change Control---------------------------------------------------------------
# 05/19/08  Jim Sobeck  Creation
#===============================================================================

#-----  include Perl Modules ---------
use strict;
use Net::FTP;
use Lib::Database::Perl::Interface::Server;
use CGI;
use util;

#------  get some objects to use later ---------
my $util = util->new;
my $query = CGI->new;
my $sql;
my $sth;
my $dbh;
my $nl_id;
my $nl_name;
my $adv_id;
my $attributedClientID;
my ($cname,$cemail,$old_nl_id,$old_mta_id,$domainid,$ip,$aid,$creative_id,$subject_id,$from_id,$old_template_id,$include_wiki,$include_name,$random_subdomain,$profile_id);
my $cdomainid;
my $jlogProfileID;
my $newMailing;
my $CutMail;
my $old_article_id;
my $old_source_url;
my $old_zip;
my $mail_from;
my $use_mail_from;
my $return_path="";
my $header_id;
my $trace_header_id;
my $groupSuppListID;
my $footer_id;
my $log_campaign;
my $group_id;
my $client_group_id;
my $utype;
my $adknowledgeDeploy;
my $injectorID;
my $randomize;
my $use_master;
my $useRdns;
my $dupes_flag;
my $dup_client_group_id;
my $sdate1;
my $shour;
my $stophour;
my $stopmin;
my $tmin;
my $pasted_domains;
my $cpasted_domains;
my $deployFileName;
my $deployPoolID;
my $deployPoolChunkID;
my @ENC=("None","ISO","UTF8-Q","UTF8-B","UTF-7","ASCII","UTF-16","UTF-8 HOT","ISO B","UTF-8Q HOT","UTF-7 HOT","ASCII HOT","UTF-16 HOT","ISO B HOT","ISO Q HOT");
my @ENC1=("","ISO","UTF8","UTF8-B","UTF-7","ASCII","UTF-16","UTF-8 HOT","ISO B","UTF-8Q HOT","UTF-7 HOT","ASCII HOT","UTF-16 HOT","ISO B HOT","ISO Q HOT");
my $uid=$query->param('uid');
my $cflag=$query->param('cflag');
if ($cflag eq "")
{
	$cflag="N";
}
my $log_camp=$query->param('log_camp');
my $prepull =$query->param('prepull');
if ($prepull eq "")
{
	$prepull="N";
}
my $ConvertSubject=$query->param('ConvertSubject');
if ($ConvertSubject eq "")
{
	$ConvertSubject="None";
}
my $ConvertFrom=$query->param('ConvertFrom');
if ($ConvertFrom eq "")
{
	$ConvertFrom="None";
}
$creative_id=$query->param('creative_id');
if ($creative_id eq "")
{
	$creative_id=0;
}
if ($log_camp == 1)
{
	$log_campaign="On";
}
else
{
	$log_campaign="Off";
}
$sdate1=$query->param('sdate');
my $intime=$query->param('stime');
my $am_pm=$query->param('am_pm');
$adv_id=$query->param('aid');
if ($adv_id eq "")
{
	$adv_id=0;
}
if ($intime ne "")
{
	if (($am_pm eq "PM") and ($intime < 12))
	{
		$shour=$intime + 12;
	}
	elsif (($intime eq "AM") and ($intime == 12))
	{
		$shour=0;
	}
}
$group_id=$query->param('group_id');
if ($group_id eq "")
{
	$group_id=0;
}
$client_group_id=$query->param('client_group_id');
if ($client_group_id eq "")
{
	$client_group_id=0;
}
$dup_client_group_id=$query->param('dup_client_group_id');
if ($dup_client_group_id eq "")
{
	$dup_client_group_id=0;
}
if ($uid eq "")
{
	$uid=0;
}
my $sid=$query->param('sid');
if ($sid eq "")
{
	$sid=0;
}
$utype=$query->param('utype');
if ($utype eq "")
{
	$utype="Normal";
}
$adknowledgeDeploy=$query->param('adknowledgeDeploy');
if ($adknowledgeDeploy eq "")
{
	$adknowledgeDeploy=0;
}
$injectorID=$query->param('injectorID');
if ($injectorID eq "")
{
	$injectorID=0;
}
$randomize=$query->param('randomize');
if ($randomize eq "")
{
	$randomize="N";
}
$jlogProfileID=$query->param('jlogProfileID');
if ($jlogProfileID eq "")
{
	$jlogProfileID=0;
}
$old_mta_id=$query->param('mta_id');
if ($old_mta_id eq "")
{
	$old_mta_id=11;
}
$profile_id=$query->param('profile_id');
if ($profile_id eq "")
{
	$profile_id=0;
}
$cname=$query->param('cname');
my $diffcnt=$query->param('diffcnt');
#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}


my $userDataRestrictionWhereClause = '';

if($util->getUserData()->{'isExternalUser'} == 1)
{
	$userDataRestrictionWhereClause = qq|
        userID = $user_id AND
    |;
}

#------  connect to the util database -----------
my ($dbhq,$dbhu)=$util->get_dbh();
$deployFileName="";
$deployPoolID=0;
$deployPoolChunkID=0;
my $serverInterface     = Lib::Database::Perl::Interface::Server->new();
if ($uid > 0)
{
	$sql="select campaign_name,email_addr,nl_id,mta_id,mailing_domain,mailing_ip,advertiser_id,creative_id,subject_id,from_id,mailing_template,include_wiki,include_name,random_subdomain,profile_id,group_id,client_group_id,send_date,hour(send_time),minute(send_time),hour(stop_time),minute(stop_time),header_id,footer_id,dup_client_group_id,dupes_flag,slot_type,use_master,pasted_domains,randomize_records,source_url,article_id,zip,mail_from,trace_header_id,useRdns,newMailing,prepull,return_path,groupSuppListID,jlogProfileID,ConvertSubject,ConvertFrom,deployFileName,deployPoolID,deployPoolChunkID,adknowledgeDeploy,injectorID,use_mail_from,CutMail from unique_campaign where $userDataRestrictionWhereClause unq_id=$uid";
	$sth=$dbhu->prepare($sql);
	$sth->execute();
	($cname,$cemail,$old_nl_id,$old_mta_id,$domainid,$ip,$adv_id,$creative_id,$subject_id,$from_id,$old_template_id,$include_wiki,$include_name,$random_subdomain,$profile_id,$group_id,$client_group_id,$sdate1,$shour,$tmin,$stophour,$stopmin,$header_id,$footer_id,$dup_client_group_id,$dupes_flag,$utype,$use_master,$pasted_domains,$randomize,$old_source_url,$old_article_id,$old_zip,$mail_from,$trace_header_id,$useRdns,$newMailing,$prepull,$return_path,$groupSuppListID,$jlogProfileID,$ConvertSubject,$ConvertFrom,$deployFileName,$deployPoolID,$deployPoolChunkID,$adknowledgeDeploy,$injectorID,$use_mail_from,$CutMail)=$sth->fetchrow_array();
	$sth->finish();
	#
	$sql="select pasted_domains from UniqueContentPasted where unq_id=$uid";
	$sth=$dbhu->prepare($sql);
	$sth->execute();
	($cpasted_domains)=$sth->fetchrow_array();
	$sth->finish();
	#
	$sql="select client_id from UniqueAttributedClient where unq_id=$uid limit 1";
	$sth=$dbhu->prepare($sql);
	$sth->execute();
	($attributedClientID)=$sth->fetchrow_array();
	$sth->finish();

}
else
{
	$cemail="";
	$old_nl_id=0;
	$domainid="";
	$ip="";
	$subject_id=0;
	$from_id=0;
	$old_template_id=1;
	$old_article_id=0;
	$include_wiki="N";
	$include_name="N";
	$ConvertSubject="None";
	$ConvertFrom="None";
	$useRdns="N";
	$newMailing="N";
	$CutMail="N";
	$random_subdomain="N";
	$header_id=0;
	$trace_header_id=0;
	$groupSuppListID=0;
	$footer_id=0;
	$use_master="N";
	$randomize="Y";
	$jlogProfileID=0;
	$shour=0;
	$tmin=1;
	$stophour=0;
	$stopmin=0;
}
if ($use_master eq "")
{
	$use_master="N";
}
if ($sdate1 eq "")
{
	$sql="select curdate()";
	$sth=$dbhu->prepare($sql);
	$sth->execute();
	($sdate1)=$sth->fetchrow_array();
	$sth->finish();
}
my $stime="";
my $stoptime="";
if ($sid > 0)
{
	$sql="select client_group_id,ip_group_id,hour(schedule_time),minute(schedule_time),hour(end_time),minute(end_time),date_add(curdate(),interval $diffcnt day),schedule_time,end_time,profile_id,mailing_domain,template_id,slot_type,log_campaign,randomize_records,mta_id,source_url,zip,mail_from,use_master,useRdns,return_path,prepull,ConvertSubject,ConvertFrom,jlogProfileID from UniqueSlot where $userDataRestrictionWhereClause slot_id=?"; 
	$sth=$dbhu->prepare($sql);
	$sth->execute($sid);
	($client_group_id,$group_id,$shour,$tmin,$stophour,$stopmin,$sdate1,$stime,$stoptime,$profile_id,$domainid,$old_template_id,$utype,$log_campaign,$randomize,$old_mta_id,$old_source_url,$old_zip,$mail_from,$use_master,$useRdns,$return_path,$prepull,$ConvertSubject,$ConvertFrom,$jlogProfileID)=$sth->fetchrow_array();
	$sth->finish();
}
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
<title>Deploy a Uniques Campaign</title>

<style type="text/css">

body {
	background: url(http://www.affiliateimages.com/temp/bg.jpg) top center repeat-x #99D1F4;
	font-family: "Trebuchet MS", Tahoma, Arial, sans-serif;
	font-size: .9em;
	color: #4d4d4d;
  }

h1 {
	text-align: center;
	font-weight: normal;
	font-size: 1.5em;
  }

h2 {
	text-align: center;
	font-weight: normal;
	font-size: 1em;
  }

.centered {
	text-align: center;
  }

#container {
	width: 80%;
	padding-top: 5%;
	margin: 0 auto;
  }

#form {
	margin: 0 auto;
	width: 100%;
	padding: 1em;
	text-align: left;
  }

#form table {
	width: 100%;
	margin: 0 auto;
	margin-top: .5em;
	margin-bottom: .5em;
  }

#form td {
	padding: .25em;
  }

td.label {
	width: 30%;
	text-align: right;
  }

td.field {
	width: 70%;
  }

input.field, select.field, textarea.field {
	padding: .15em;
	border: 1px solid #999;
	color: #000;
	font-family: Tahoma, Arial, sans-serif;
  }

input.field:hover, select.field:hover, textarea.field:hover {
	background: #F9FFE9;
  }

input.field:focus, select.field:focus, textarea.field:focus {
	background: #F9FFE9;
	border: 1px inset;
  }

div.submit {
	text-align: center;
	padding: 1em 0;
  }

input.submit {
	margin-top: .25em;
	font-size: 2em;
	color: #444;
  }

input.radio {
	border: 0;
  }

.note {
	font-size: .8em;
  }

</style>
<script language="JavaScript">
function edit_template()
{
    var selObj = document.getElementById('template_id');
    var selIndex = selObj.selectedIndex;
    var newwin = window.open("/cgi-bin/template_disp.cgi?mode=U&nl_id="+selObj.options[selIndex].value,"MTA", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1");
        newwin.focus();
}
function add_template()
{
    var newwin = window.open("/cgi-bin/template_disp.cgi?mode=A","MTA", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1");
    newwin.focus();
}
function edit_advertiser()
{
    var selObj = document.getElementById('adv_id');
    var selIndex = selObj.selectedIndex;
    var newwin = window.open("/cgi-bin/advertiser_disp2.cgi?pmode=U&puserid="+selObj.options[selIndex].value,"MTA", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1");
        newwin.focus();
}
function add_creative()
{
    var selObj = document.getElementById('adv_id');
    var selIndex = selObj.selectedIndex;
    var newwin = window.open("/cgi-bin/add_creative.cgi?puserid="+selObj.options[selIndex].value,"MTA", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1");
        newwin.focus();
}
function edit_profile()
{
    var selObj = document.getElementById('profileid');
    var selIndex = selObj.selectedIndex;
    var newwin = window.open("/cgi-bin/uniqueprofile_edit.cgi?pid="+selObj.options[selIndex].value,"MTA", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1");
        newwin.focus();
}
function add_profile()
{
    var newwin = window.open("/cgi-bin/uniqueprofile_edit.cgi?pid=0","MTA", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1");
        newwin.focus();
}
function add_from()
{
    var selObj = document.getElementById('adv_id');
    var selIndex = selObj.selectedIndex;
    var newwin = window.open("/cgi-bin/from.cgi?aid="+selObj.options[selIndex].value,"MTA", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1");
        newwin.focus();
}
function add_subject()
{
    var selObj = document.getElementById('adv_id');
    var selIndex = selObj.selectedIndex;
    var newwin = window.open("/cgi-bin/subject.cgi?aid="+selObj.options[selIndex].value,"MTA", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1");
        newwin.focus();
}
function edit_creative()
{
    var selObj = document.getElementById('adv_id');
    var selIndex = selObj.selectedIndex;
    var selObj1 = document.getElementById('creative');
    var selIndex1 = selObj1.selectedIndex;
    var newwin = window.open("/cgi-bin/edit_creative.cgi?puserid="+selObj.options[selIndex].value+"&cid="+selObj1.options[selIndex1].value,"MTA", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1");
        newwin.focus();
}
function edit_ipgroup()
{
    var selObj = document.getElementById('group_id');
    var selIndex = selObj.selectedIndex;
    var newwin = window.open("/cgi-bin/ipgroup_edit.cgi?gid="+selObj.options[selIndex].value,"MTA", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1");
    newwin.focus();
}
function edit_clientgroup()
{
    var selObj = document.getElementById('client_group_id');
    var selIndex = selObj.selectedIndex;
    var newwin = window.open("/cgi-bin/clientgroup_edit.cgi?gid="+selObj.options[selIndex].value,"MTA", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1");
    newwin.focus();
}
function view_thumbnails()
{
    var selObj = document.getElementById('adv_id');
    var selIndex = selObj.selectedIndex;
    var newwin = window.open("/cgi-bin/view_thumbnails.cgi?aid="+selObj.options[selIndex].value,"MTA", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1");
        newwin.focus();
}
function adv_preview()
{
    var selObj = document.getElementById('adv_id');
    var selIndex = selObj.selectedIndex;
    url="/newcgi-bin/adv_preview.cgi?aid="+selObj.options[selIndex].value;
    window.open(url,'Preview','width=850,height=650,toolbar=no,location=no,scrollbars=yes,resizable=no');
}
function add_header()
{
        var newwin = window.open("/cgi-bin/Headers/template_disp.cgi?mode=A","AddHeader", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1");
        newwin.focus();
}
function edit_header()
{
   	var selObj = document.getElementById('header_id');
    var selIndex = selObj.selectedIndex;
        var newwin = window.open("/cgi-bin/Headers/template_disp.cgi?mode=U&nl_id="+selObj.options[selIndex].value,"AddHeader", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1");
        newwin.focus();
}
function add_trace_header()
{
        var newwin = window.open("/cgi-bin/TraceHeaders/template_disp.cgi?mode=A","AddHeader", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1");
        newwin.focus();
}
function edit_trace_header()
{
   	var selObj = document.getElementById('trace_header_id');
    var selIndex = selObj.selectedIndex;
        var newwin = window.open("/cgi-bin/TraceHeaders/template_disp.cgi?mode=U&nl_id="+selObj.options[selIndex].value,"AddHeader", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1");
        newwin.focus();
}
function add_footer()
{
        var newwin = window.open("/cgi-bin/Footers/template_disp.cgi?mode=A","AddFooter", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1");
        newwin.focus();
}
function edit_footer()
{
   	var selObj = document.getElementById('footer_id');
    var selIndex = selObj.selectedIndex;
        var newwin = window.open("/cgi-bin/Footers/template_disp.cgi?mode=U&nl_id="+selObj.options[selIndex].value,"AddFooter", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1");
        newwin.focus();
}
function upd_creative(t1)
{
    var selObj = document.getElementById('adv_id');
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
    var selLength = campform.cusa.length;
    while (selLength>0)
    {
        campform.cusa.remove(selLength-1);
        selLength--;
    }
    campform.cusa.length=0;
	if (t1 == 1)
	{
    	parent.frames[1].location="/newcgi-bin/sm2_upd_creative.cgi?aid="+selObj.options[selIndex].value+"&t1=1&uid=$uid&all=0&t2=1&t3=1";
	}
	else
	{
    	parent.frames[1].location="/newcgi-bin/sm2_upd_creative.cgi?aid="+selObj.options[selIndex].value+"&t1=0&t2=1&t3=1";
	}
}
function upd_surl()
{
    var selObj = document.getElementById('client_group_id');
    var selIndex = selObj.selectedIndex;
    var selLength = campform.surl.length;
    while (selLength>0)
    {
        campform.surl.remove(selLength-1);
        selLength--;
    }
    campform.surl.length=0;
   	parent.frames[1].location="/newcgi-bin/sm2_upd_surl.cgi?gid="+selObj.options[selIndex].value;
}
function upd_advertiser()
{
    var selObj = document.getElementById('country');
    var selIndex = selObj.selectedIndex;
    var selLength = campform.adv_id.length;
    while (selLength>0)
    {
        campform.adv_id.remove(selLength-1);
        selLength--;
    }
    campform.adv_id.length=0;
   	parent.frames[1].location="/newcgi-bin/unique_upd_advertiser.cgi?country="+selObj.options[selIndex].value;
}
function addAdv(value,text)
{
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    campform.adv_id.add(newOpt);
}
function addSurl(value,text)
{
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    campform.surl.add(newOpt);
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
function addUSA(value,text)
{
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    campform.cusa.add(newOpt);
}
function disp_mta_list()
{
    var cpage = "/cgi-bin/mta_list.cgi";
    var newwin = window.open(cpage, "MTA", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,width=900,height=500,left=25,top=50")
;
}

function edit_mta()
{
    var url_value;
    url_value = document.campform.mta_id.value;
    if (url_value != "")
    {
        var cpage = "/cgi-bin/mta_setup.cgi?mta_id="+url_value;
        var newwin = window.open(cpage, "MTA", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,width=900,height=500,left=25,top=50");
    }
}
function add_header()
{
    var cpage = "/cgi-bin/mailHeaders/template_disp.cgi?pmode=A";
    var newwin = window.open(cpage, "MTA", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,width=900,height=500,left=25,top=50");
}
function edit_header()
{
    var url_value;
    url_value = document.campform.headerid.value;
    var cpage = "/cgi-bin/mailHeaders/template_disp.cgi?pmode=U&nl_id="+url_value;
    var newwin = window.open(cpage, "MTA", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,width=900,height=500,left=25,top=50");
}
function addDomain(value,text)
{
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    campform.domainid.add(newOpt);
}
function addCDomain(value,text)
{
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    campform.cdomainid.add(newOpt);
}
function addIP(value,text)
{
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    campform.ipid.add(newOpt);
}
function addProfile(value,text)
{
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    campform.profileid.add(newOpt);
}

function update_domain(t1)
{
    var selObj = document.getElementById('nl_id');
    var selIndex = selObj.selectedIndex;
    var selLength = campform.domainid.length;
    while (selLength>0)
    {
        campform.domainid.remove(selLength-1);
        selLength--;
    }
    campform.domainid.length=0;
    var selLength = campform.cdomainid.length;
    while (selLength>0)
    {
        campform.cdomainid.remove(selLength-1);
        selLength--;
    }
    campform.cdomainid.length=0;
    parent.frames[1].location="/newcgi-bin/unique_upd_domain.cgi?nid="+selObj.options[selIndex].value+"&t1="+t1+"&uid=$uid&sid=$sid";
}
function set_domain_fields(domainid)
{
    var i;
    var selObj = document.getElementById('domainid');
    for (i=0; i<selObj.options.length; i++) 
	{ 
		if (selObj.options[i].value == domainid)
 		{ 
			selObj.options[i].selected= true; break;
    	}
	}
}
function set_cdomain_fields(domainid)
{
    var i;
    var selObj = document.getElementById('cdomainid');
    for (i=0; i<selObj.options.length; i++) 
	{ 
		if (selObj.options[i].value == domainid)
 		{ 
			selObj.options[i].selected= true; break;
    	}
	}
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
function chkform()
{
    if (campform.cname.value == '') 
    {
        alert('You must enter a campaign name');
		campform.cname.focus();
        return false;
    }
    if ((campform.adv_id.selectedIndex== -1) || (campform.adv_id.selectedIndex== 0))
    {
        alert('You must select an advertiser');
        return false;
    }
    if (((campform.cusa.selectedIndex== -1) || (campform.cusa.selectedIndex== 0)) && (campform.adv_id > 4))
	{
    if (campform.creative.selectedIndex== -1) 
    {
        alert('You must select a Creative');
        return false;
	}
    if (campform.csubject.selectedIndex== -1) 
    {
        alert('You must select a Subject');
        return false;
	}
    if (campform.cfrom.selectedIndex== -1) 
    {
        alert('You must select a From');
        return false;
	}
	}
    if ((campform.template_id.selectedIndex== -1) || (campform.template_id.selectedIndex== 0))
    {
        alert('You must select a Mailing template');
        return false;
    }
    var selObj = document.getElementById('domainid');
    var selIndex = selObj.selectedIndex;
	if (selObj.options[selIndex].value == 0)
	{
        if (confirm("Mailing Domain set to ROTATE ALL.  Are you sure you want to save?"))
        {
		}
		else
		{
			return false;
		}
	}
    var selObj = document.getElementById('creative');
    var selIndex = selObj.selectedIndex;
	if (selObj.options[selIndex].value == 0)
	{
        if (confirm("Creative set to ROTATE ALL.  Are you sure you want to save?"))
        {
		}
		else
		{
			return false;
		}
	}
    var selObj = document.getElementById('cfrom');
    var selIndex = selObj.selectedIndex;
	if (selObj.options[selIndex].value == 999999999)
	{
        if (confirm("From set to ROTATE ALL.  Are you sure you want to save?"))
        {
		}
		else
		{
			return false;
		}
	}
    var selObj = document.getElementById('csubject');
    var selIndex = selObj.selectedIndex;
	if (selObj.options[selIndex].value == 999999999)
	{
        if (confirm("Subject set to ROTATE ALL.  Are you sure you want to save?"))
        {
		}
		else
		{
			return false;
		}
	}
    return true;
}
</script>
</head>

<body>
<div id="container">

	<h1>Deploy a Uniques Campaign</h1>
	<h2><a href="unique_list.cgi" target=_top>view/edit/deploy uniques campaigns</a> | <a href="unique_deploy_list.cgi" target=_top>deployed unique campaigns</a> | <a href="/cgi-bin/mainmenu.cgi" target=_top>go home</a></h2>

	<div id="form">
	<form name="campform" method=post action="/cgi-bin/unique_save.cgi" target=_top onSubmit="return chkform();">
end_of_html
if ($cflag eq "Y")
{
	print "<input type=hidden name=uid value=0>\n";
}
else
{
	print "<input type=hidden name=uid value=$uid>\n";
}
print<<"end_of_html";
	<input type=hidden name=sid value=$sid>
	<input type=hidden name=diffcnt value=$diffcnt>
		<table>
		  <tr>
			<td class="label">Uniques Campaign Name:</td>
			<td><input id="" type="" name="cname" value="$cname" size="50" /></td>
		  </tr>
		  <tr>
			<td class="label">Email Address(es) to receive tests:</td>
			<td><input id="" type="" name="cemail" value="$cemail" size="50" /></td>
		  </tr>
		  <tr>
			<td class="centered note" colspan="2"><em><strong>Note:</strong> The above email addresses will also receive alerts related to this campaign.</em></td>
		  </tr>
		<tr>									
											<td class="label">
											<font face="verdana,arial,helvetica,sans serif" size="2">
											Scheduled For: </font></td>
end_of_html
if ($sid == 0)
{
print<<"end_of_html";
											<td><input maxLength="10" size="10" name="sdate" value="$sdate1">&nbsp;&nbsp;
											<select name=stime id=stime>
end_of_html
my $i = 1;
my $thour = $shour;
if ($shour > 12)
{
	$thour = $shour - 12;
}
elsif (($shour == 0) or ($shour == 24))
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
											<select name=smin id=smin>
end_of_html
my $i = 0;
while ($i < 60)
{
	if ($i == $tmin)
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
											</select></td></tr>
end_of_html
}
else
{
	print "<td><b>$sdate1 $stime</b></td></tr>\n";
}
print<<"end_of_html";
		<tr>									
											<td class="label">
											<font face="verdana,arial,helvetica,sans serif" size="2">
											Stop Time: </font></td>
end_of_html
if ($sid == 0)
{
print<<"end_of_html";
											<td>
											<select name=stoptime id=stoptime>
end_of_html
my $i = 0;
my $thour = $stophour;
if ($stophour > 12)
{
	$thour = $stophour - 12;
}
elsif ($stophour == 24)
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
											<select name=stopmin id=stopmin>
end_of_html
my $i = 0;
while ($i < 60)
{
	if ($i == $stopmin)
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
											<select name="stop_am_pm">
end_of_html
if ($stophour < 12)
{
	print "<option value=\"AM\" selected>AM</option><option value=\"PM\">PM</option>\n";
}
else
{
	print "<option value=\"AM\">AM</option><option value=\"PM\" selected>PM</option>\n";
}
print<<"end_of_html";
											</select></td></tr>
end_of_html
}
else
{
	print "<td><b>$stoptime</b></td></tr>\n";
}
print<<"end_of_html";
	<tr>
			<td class="label"><br />Log Campaign:</td>
end_of_html
if ($sid == 0)
{
	print "<td><select name=log_camp>";
	if ($log_campaign eq "Off")
	{
		print "<option value=Off selected>Off</option><option value=On>On</option>";
	}
	else
	{
		print "<option value=Off >Off</option><option selected value=On>On</option>";
	}
	print "</select></td></tr>\n";
}
else
{
	print "<td><b>$log_campaign</b></td></tr>\n";
}
print<<"end_of_html";
	<tr>
			<td class="label"><br />Pre-pull Campaign:</td>
end_of_html
if ($sid == 0)
{
	print "<td><select name=prepull>";
	if ($prepull eq "Y")
	{
		print "<option value=Y selected>Yes</option><option value=N>No</option>";
	}
	else
	{
		print "<option value=Y>Yes</option><option value=N selected>No</option>";
	}
	print "</select></td></tr>\n";
}
else
{
	print "<td><b>$prepull</b></td></tr>\n";
}
print<<"end_of_html";
	<tr>
			<td class="label"><br />Injector to use:</td>
<td><br /><select name=injectorID><option value=0>Random</option>
end_of_html
my ($errors, $results);
my $params;
($errors, $results) = $serverInterface->getDedicatedInjectors($params);
my $cnt=$#{$results};
my $i=0;
while ($i <= $cnt)
{
    my $server_id=$results->[$i]->{'serverID'};
    my $sname=$results->[$i]->{'hostname'};
    $sname=~s/.pcposts.com//;
    $sname=~s/.i.routename.com//;
    $sname=~s/.routename.com//;
    if ($server_id == $injectorID)
    {
		print "<option value=$server_id selected>$sname</option>\n";
	}
	else
	{
		print "<option value=$server_id>$sname</option>\n";
	}
	$i++;
}
print<<"end_of_html";
</select></td></tr>
		  <tr>
			<td class="label"><br />Newsletter:</td>
			<td><br />
				<select name=nl_id id=nl_id onChange="update_domain(0);">
end_of_html
$sql="select nl_id,nl_name from newsletter where nl_status='A' order by nl_name";
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($nl_id,$nl_name) = $sth->fetchrow_array())
{
	if ($nl_id == $old_nl_id)
	{
		print "<option selected value=$nl_id>$nl_name</option>\n";
	}
	else
	{
		print "<option value=$nl_id>$nl_name</option>\n";
	}
}
$sth->finish();
print<<"end_of_html";
				</select>&nbsp;&nbsp;<input type="button" value="Refresh" onClick="update_domain(0);"/>
			</td>
		  </tr>
		  <tr>
			<td class="label">Mailing Domain:</td>
			<td>
end_of_html
my $rstr="";
if ($useRdns eq "Y")
{
	$rstr="checked";
}
if ($sid > 0)
{
	print "<input type=hidden name=domainid value=\"$domainid\">\n";
	$sql="select mailing_domain from UniqueSlotDomain where slot_id=?";
	my $sth1=$dbhu->prepare($sql);
	$sth1->execute($sid);
	my $dname;
	$dname="";
	my $tname;
	while (($tname)=$sth1->fetchrow_array())
	{
		$dname=$dname.$tname.",";
	}
	$sth1->finish();
	chop($dname);
	print "<b>$dname</b></td>\n";
}
else
{
	print "<select name=domainid id=domainid size=5 multiple=\"multiple\"></select>&nbsp;&nbsp;<textarea name=pastedomainid id=pastedomainid rows=5 columns=50>$pasted_domains</textarea>&nbsp;&nbsp;<input type=checkbox name=useRdns $rstr value=Y>Use Rdns</td>\n";
}
print<<"end_of_html";
		  </tr>
		  <tr>
			<td class="label">Content Domain:</td>
			<td>
end_of_html
if ($sid > 0)
{
	print "<input type=hidden name=cdomainid value=\"$cdomainid\">\n";
	$sql="select domain_name from UniqueSlotContentDomain where slot_id=?";
	my $sth1=$dbhu->prepare($sql);
	$sth1->execute($sid);
	my $dname;
	$dname="";
	my $tname;
	while (($tname)=$sth1->fetchrow_array())
	{
		$dname=$dname.$tname.",";
	}
	$sth1->finish();
	chop($dname);
	print "<b>$dname</b></td>\n";
}
else
{
	print "<select name=cdomainid id=cdomainid size=5 multiple=\"multiple\"></select>&nbsp;&nbsp;<textarea name=cpastedomainid id=cpastedomainid rows=5 columns=50>$cpasted_domains</textarea></td>\n";
}
print<<"end_of_html";
		  </tr>
<!--		  <tr>
			<td class="label">Mailing IP(s):</td>
			<td>
				<select name=ipid id=ipid>
				</select>
			</td>
		  </tr> -->
		  <tr>
			<td class="label">IP Group:</td>
end_of_html
if ($sid > 0)
{
	my $gname;
	$sql="select group_name from IpGroup where $userDataRestrictionWhereClause group_id=$group_id"; 
	my $sth1=$dbhu->prepare($sql);
	$sth1->execute();
	($gname)=$sth1->fetchrow_array();
	$sth1->finish();
	print "<td><b>$gname</b>";
}
else
{
print<<"end_of_html";
			<td>
				<select name=group_id id=group_id>
end_of_html
$sql="select group_id,group_name from IpGroup where $userDataRestrictionWhereClause status='Active' order by group_name";
my $sth1=$dbhu->prepare($sql);
$sth1->execute();
my $gid;
my $gname;
while (($gid,$gname)=$sth1->fetchrow_array())
{
	if ($gid == $group_id)
	{
		print "<option selected value=$gid>$gname</option>\n";
	}
	else
	{
		print "<option value=$gid>$gname</option>\n";
	}
}
$sth1->finish();
print<<"end_of_html";
				</select>&nbsp;&nbsp; <input type="button" value="edit" onClick="edit_ipgroup();"/>
			</td>
end_of_html
}
print<<"end_of_html";
		  </tr>
		  <tr>
			<td class="label">Client Group:</td>
end_of_html
if ($sid > 0)
{
	my $gname;
	$sql="select group_name from ClientGroup where client_group_id=$client_group_id"; 
	my $sth1=$dbhu->prepare($sql);
	$sth1->execute();
	($gname)=$sth1->fetchrow_array();
	$sth1->finish();
	print "<td><b>$gname</b></td>";
}
else
{
print<<"end_of_html";
			<td>
				<select name=client_group_id id=client_group_id onChange="upd_surl();">
end_of_html
$sql="select client_group_id,group_name from ClientGroup where status='A' order by group_name";
my $sth1=$dbhu->prepare($sql);
$sth1->execute();
my $gid;
my $gname;
while (($gid,$gname)=$sth1->fetchrow_array())
{
	if ($gid == $client_group_id)
	{
		print "<option selected value=$gid>$gname</option>\n";
	}
	else
	{
		print "<option value=$gid>$gname</option>\n";
	}
}
$sth1->finish();
print<<"end_of_html";
				</select>&nbsp;&nbsp; <input type="button" value="edit" onClick="edit_clientgroup();"/>
			</td>
end_of_html
}
print<<"end_of_html";
		  </tr>
		  <tr>
			<td class="label">Attributed Client:</td>
			<td>
				<select name=attributed_client id=attributed_client>
				<option value="" selected>N/A</option>
end_of_html
$sql="select user_id,username from user where status='A' order by username"; 
my $sth1=$dbhu->prepare($sql);
$sth1->execute();
my $clientID;
my $uname;
while (($clientID,$uname)=$sth1->fetchrow_array())
{
	if ($clientID == $attributedClientID)
	{
		print "<option selected value=$clientID>$uname</option>\n";
	}
	else
	{
		print "<option value=$clientID>$uname</option>\n";
	}
}
$sth1->finish();
print<<"end_of_html";
				</select>&nbsp;&nbsp; 
			</td>
		  </tr><tr>
end_of_html
if ($sid == 0)
{
print<<"end_of_html";
			<td class="label"><br />Profile:</td>
			<td><br />
				<select name="profileid" id="profileid">
end_of_html
	$sql="select profile_id,profile_name from UniqueProfile where status='A' order by profile_name";
	$sth=$dbhu->prepare($sql);
	$sth->execute();
	my $pid;
	my $pname;
	while (($pid,$pname)=$sth->fetchrow_array())
	{
		if ($pid == $profile_id)
		{
			print "<option value=$pid selected>$pname</option>\n";
		}
		else
		{
			print "<option value=$pid>$pname</option>\n";
		}
	}
	$sth->finish();
print<<"end_of_html";
				</select>
				<input type="button" value="edit" onClick="edit_profile();"/>
				<input type="button" value="add new profile" onClick="add_profile();"/>
			</td>
		  </tr>
end_of_html
}
else
{
	my $pname;
	$sql="select profile_name from UniqueProfile where profile_id=$profile_id"; 
	$sth=$dbhu->prepare($sql);
	$sth->execute();
	($pname)=$sth->fetchrow_array();
	$sth->finish();
	print "<td class=label>Profile:</td><td><b>$pname</b></td></tr>\n";
}
print<<"end_of_html";
<tr>
			<td class="label">Filename containing EIDs:</td>
			<td><select name=deployFileName>
			<option value="">None</option>
end_of_html
my $host = "ftp.aspiremail.com";
my $ftp = Net::FTP->new("$host", Timeout => 20, Debug => 0, Passive => 0) or print "Cannot connect to $host: $@\n";
if ($ftp)
{
    $ftp->login('deploysByEID','24vision') or print "Cannot login ", $ftp->message;
	foreach my $file($ftp->ls)
	{
		if ($file eq $deployFileName)
		{
			print "<option selected value=\"$file\">$file</option>";
		}
		else
		{
			print "<option value=\"$file\">$file</option>";
		}
	}
    $ftp->quit;
}
print<<"end_of_html";
			</select></td></tr>
<tr>
			<td class="label">Deploy Pool:</td>
			<td><select name=deployPoolID>
			<option value="0">None</option>
end_of_html
$sql="select deployPoolID,deployPoolName from DeployPool where status='Active' order by deployPoolName";
$sth=$dbhu->prepare($sql);
$sth->execute();
my $pid;
my $pname;
while (($pid,$pname)=$sth->fetchrow_array())
{
	if ($pid == $deployPoolID)
	{
		print "<option selected value=\"$pid\">$pname</option>";
	}
	else
	{
		print "<option value=\"$pid\">$pname</option>";
	}
}
print<<"end_of_html";
			</select>&nbsp;&nbsp;Chunk Id: <input type=text name=deployPoolChunkID value="$deployPoolChunkID" size=3 maxlength=3></td></tr>
<tr>
			<td class="label">AdKnowledge Deploy Listid(set to zero to turn off):</td>
			<td>
end_of_html
	print qq^<input type=text maxlength=5 size=5 value="$adknowledgeDeploy"  name=adknowledgeDeploy>\n^;
print<<"end_of_html";
			</td>
		  </tr>
<tr>
			<td class="label">Deploy Type:</td>
end_of_html
if ($sid > 0)
{
	print "<td><b>$utype</b>";
}
else
{
print<<"end_of_html";
			<td>
				<select name=utype id=utype>
end_of_html

setDeployType($utype);

print<<"end_of_html";
				</select>
			</td>
end_of_html
}
print<<"end_of_html";
		  </tr>
<tr>
			<td class="label">Apply Jlog Profile:</td>
			<td><select name=jlogProfileID><option value=0>None</option>
end_of_html
$sql=" select profileID,profileName from EmailEventHandlerProfile order by profileName";
my $sthp=$dbhu->prepare($sql);
$sthp->execute();
my $profileID;
my $profileName;
while (($profileID,$profileName)=$sthp->fetchrow_array())
{
	if ($profileID == $jlogProfileID)
	{
		print "<option selected value=$profileID>$profileName</option>";
	}
	else
	{
		print "<option value=$profileID>$profileName</option>";
	}
}
$sthp->finish();

print<<"end_of_html";
			</select></td>
		  </tr>
<tr>
			<td class="label">Randomize:</td>
end_of_html
if ($sid > 0)
{
	print "<td><b>$randomize</b>";
}
else
{
print<<"end_of_html";
			<td>
end_of_html
if ($randomize eq "Y")
{
	print "<input type=radio checked value=Y name=randomize>Yes&nbsp;<input type=radio value=N name=randomize>No\n";
}
else
{
	print "<input type=radio value=Y name=randomize>Yes&nbsp;<input checked type=radio value=N name=randomize>No\n";
}
print<<"end_of_html";
			</td>
end_of_html
}
print<<"end_of_html";
		  </tr>
		  <tr>
			<td class="label">Duplicate Client Group:</td>
			<td>
				<select name=dup_client_group_id id=dup_client_group_id>
<option selected value=0>N/A</option>
end_of_html
$sql="select client_group_id,group_name from ClientGroup where status='A' order by group_name";
my $sth1=$dbhu->prepare($sql);
$sth1->execute();
my $gid;
my $gname;
while (($gid,$gname)=$sth1->fetchrow_array())
{
	if ($gid == $dup_client_group_id)
	{
		print "<option selected value=$gid>$gname</option>\n";
	}
	else
	{
		print "<option value=$gid>$gname</option>\n";
	}
}
$sth1->finish();
print<<"end_of_html";
				</select> <i>Note: This only applies to client 276(spiremain)</i>
			</td>
		  </tr>
		  <tr>
			<td class="label">Mailing Type:</td>
			<td>
end_of_html
	if ($dupes_flag eq '')
	{
		$dupes_flag="N/A";
	}
 	my $sthc = $dbhu->column_info(undef, undef, 'unique_campaign', '%');
    while ( my $col_info = $sthc->fetchrow_hashref)
    {
        if (($col_info->{'TYPE_NAME'} eq 'ENUM') and ($col_info->{'COLUMN_NAME'} eq "dupes_flag"))
		{    
			my $str=join(',',@{$col_info->{'mysql_values'}});
			my @a=split(',',$str);
			my $i=0;
			while ($i <= $#a)
			{
				if ($dupes_flag eq $a[$i])
				{
					print "<input checked type=radio value=\"$a[$i]\" name=dupes_flag>$a[$i]&nbsp;&nbsp;";
				}
				else
				{
					print "<input type=radio value=\"$a[$i]\" name=dupes_flag>$a[$i]&nbsp;&nbsp;";
				}
				$i++;
			}
			print "</td></tr>";
		}
	}  
print<<"end_of_html";
		  <tr>
			<td class="label">MTA Settings Profile:</td>
			<td>
				<select name=mta_id id=mta_id>
end_of_html
$sql="select mta_id,name from mta order by name";
$sth=$dbhq->prepare($sql);
$sth->execute();
my $mta_id;
my $mname;
while (($mta_id,$mname)=$sth->fetchrow_array())
{
	if ($old_mta_id == $mta_id)
	{
		print "<option selected value=$mta_id>$mname</option>\n";
	}
	else
	{
		print "<option value=$mta_id>$mname</option>\n";
	}
}
$sth->finish();
print<<"end_of_html";
				</select>
				<input type="button" value="edit" onClick="edit_mta();"/>
				<input type="button" value="add new MTA settings profile" onClick="disp_mta_list();""/>
			</td>
		  </tr>
<tr>
			<td class="label">Source URL:</td>
end_of_html
if ($sid > 0)
{
	print "<td><b>$old_source_url</b></td>";
}
else
{
	my $ccnt;
	my $tcid;
	my $turl;
	my $tcnt;
	print "<td><select name=surl><option selected value=ALL>ALL</option>";
	$sql="select count(*) from ClientGroupClients where client_group_id=?";
	$sth=$dbhq->prepare($sql);
	$sth->execute($client_group_id);
	($ccnt)=$sth->fetchrow_array();
	$sth->finish();
	if ($ccnt == 1)
	{
		$sql="select client_id from ClientGroupClients where client_group_id=?";
		$sth=$dbhq->prepare($sql);
		$sth->execute($client_group_id);
		($tcid)=$sth->fetchrow_array();
		$sth->finish();
		$sql="select url,count(*) from SourceUrlSummary sus, source_url su where sus.url_id=su.url_id and sus.client_id=? and sus.effectiveDate >= date_sub(curdate(),interval 30 day) and su.url != '' and su.url != '.' group by 1 order by 2 desc limit 5";
		$sth=$dbhq->prepare($sql);
		$sth->execute($tcid);
		while (($turl,$tcnt)=$sth->fetchrow_array())
		{
			if ($turl eq $old_source_url)
			{
				print "<option value=\"$turl\" selected>$turl - $tcnt</option>";
			}
			else
			{
				print "<option value=\"$turl\">$turl - $tcnt</option>";
			}
		}
		$sth->finish();
	}
	print "</select>&nbsp;&nbsp;";
	print "Input URL: <input type=text name=input_url size=50 maxlenght=255></td>\n";
}
print<<"end_of_html";
		  </tr>
			<tr>
			<td class="label">Zip:</td>
end_of_html
if ($old_zip eq "ALL")
{
	$old_zip="";
}
if ($sid > 0)
{
	print "<td><b>$old_zip</b></td>";
}
else
{
	print "<td><input type=text name=zip size=10 maxlength=10 value=\"$old_zip\"></td>\n";
}
print<<"end_of_html";
</tr>
			<tr>
			<td class="label">Mail From:</td>
	<td><input type=text name=mail_from size=50 maxlength=50 value="$mail_from"></td></tr>
		  <tr>
			<td class="label"><br />Use Mail From as From:</td>
end_of_html
if ($use_mail_from eq "Y")
{
print<<"end_of_html";
			<td class="field"><input class="radio" type="radio" value="Y" checked name="use_mail_from" />Yes <input class="radio" type="radio" value="N" name="use_mail_from" />No</td>
end_of_html
}
else
{
print<<"end_of_html";
			<td class="field"><input class="radio" type="radio" value="Y" name="use_mail_from" />Yes <input class="radio" type="radio" value="N" checked name="use_mail_from" />No</td>
end_of_html
}
print<<"end_of_html";
</tr>
			<tr>
			<td class="label">Return Path:</td>
	<td><input type=text name=return_path size=50 maxlength=80 value="$return_path"></td></tr>
		  <tr>
            <td class="label"><br />Country:</td>
            <td class="field">
                <br /><select class="field" name="country" id="country" onChange="upd_advertiser();">
<option value="" checked>ALL</option>
end_of_html
$sql="select countryID,countryName from Country order by countryName";
$sth=$dbhq->prepare($sql);
$sth->execute();
my $abbr;
my $cname;
while (($abbr,$cname)=$sth->fetchrow_array())
{
    print "<option value=$abbr>$cname</option>\n";
}
$sth->finish();
print<<"end_of_html";
				</select>&nbsp;&nbsp;<input type="button" value="Refresh" onClick="upd_advertiser();"/>
			</td>
		  </tr>
		  <tr>
            <td class="label"><br />Advertiser: (<a href="" class="note"><em><a href="javascript:edit_advertiser();">edit selected</em></a>)</td>
            <td class="field">
                <br /><select class="field" name="adv_id" id="adv_id" onChange="upd_creative(0);">
<option value=0 checked>--SELECT ONE--</option>
<option value="RAND_RAND">Random Advertiser Random CFS</option>
<option value="RAND_MAX">Random Advertiser Max CFS</option>
<option value="RAND_MAX_15">Random Advertiser not mailled last 15 days</option>
<option value="RAND_MAX_30">Random Advertiser not mailed last 30 days</option>
end_of_html
$sql="select advertiser_id,advertiser_name from advertiser_info where status='A' and test_flag='N' order by advertiser_name";
$sth=$dbhq->prepare($sql);
$sth->execute();
my $aid;
my $aname;
while (($aid,$aname)=$sth->fetchrow_array())
{
    if ($adv_id == $aid)
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
				</select>&nbsp;&nbsp;<input type="button" value="Refresh" onClick="upd_creative(0);"/>
			</td>
		  </tr>
		  <tr>
			<td class="label">Unique Schedule Advertiser:</td>
			<td>
				<select name="cusa" id="cusa" size=1>
				</select>
			</td>
		  </tr>
		  <tr>
			<td class="label">Creative:</td>
			<td>
				<select name="creative" id="creative" size=5 multiple=multiple>
				</select>
				<input type="button" value="view all thumbnails" onClick="view_thumbnails();"/>
                <input type="button" value="View all creatives" onClick="adv_preview();" />
			</td>
		  </tr>
		  <tr>
			<td class="label">From Line:</td>
			<td>
				<select name="cfrom" id="cfrom" size=5 multiple=multiple>
				</select>&nbsp;&nbsp;Encoding: <select name=ConvertFrom>
end_of_html
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
print<<"end_of_html";
</select>&nbsp;&nbsp;
			</td>
		  </tr>
		  <tr>
			<td class="label">Subject:</td>
			<td>
				<select name="csubject" id="csubject" multiple=multiple size=5>
				</select>&nbsp;&nbsp;Encoding: <select name=ConvertSubject>
end_of_html
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
print<<"end_of_html";
</select>&nbsp;&nbsp;
			</td>
		  </tr>
		  <tr>
			<td class="label">Include {{NAME}} in subject?</td>
end_of_html
if ($include_name eq "Y")
{
			print "<td><input class=\"radio\" checked type=\"radio\" value=\"Y\" name=\"include_name\" />Yes <input class=\"radio\" type=\"radio\" value=\"N\" name=\"include_name\" />No</td>\n";
}
else
{
			print "<td><input class=\"radio\" type=\"radio\" value=\"Y\" name=\"include_name\" />Yes <input class=\"radio\" checked type=\"radio\" value=\"N\" name=\"include_name\" />No</td>\n";
}
print<<"end_of_html";
		  </tr>
		  <tr>
			<td class="label">Use Master:</td>
end_of_html
if ($use_master eq "Y")
{
	print "<td><input type=radio name=use_master checked value=Y>Yes&nbsp;&nbsp;<input type=radio name=use_master selected value=N>No</td></tr>";
}
else
{
	print "<td><input type=radio name=use_master value=Y>Yes&nbsp;&nbsp;<input type=radio checked name=use_master selected value=N>No</td></tr>";
}
print<<"end_of_html";
		  <tr>
			<td class="label">Mailing Template:</td>
end_of_html
if ($sid == 0)
{
print<<"end_of_html";
			<td>
				<select name="template_id" id="template_id">
<option value=0 selected>--SELECT ONE--</option>
end_of_html
if ($old_template_id == 999999)
{
	print "<option value=999999 selected>Use MTA Settings</option>\n";
}
else
{
	print "<option value=999999>Use MTA Settings</option>\n";
}
$sql="select template_id,template_name from brand_template where $userDataRestrictionWhereClause status='A' order by template_name";
$sth=$dbhq->prepare($sql);
$sth->execute();
my $template_id;
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
				</select>
				<input type="button" value="edit" onClick="edit_template();"/>
				<input type="button" value="add new template" onClick="add_template();"/>
			</td>
end_of_html
}
else
{
	my $tname;
	$sql="select template_name from brand_template where $userDataRestrictionWhereClause template_id=?"; 
	$sth=$dbhu->prepare($sql);
	$sth->execute($old_template_id);
	($tname)=$sth->fetchrow_array();
	$sth->finish();
	print "<td><b>$tname</b></td>\n";
}
print<<"end_of_html";
		  </tr>
		  <tr>
			<td class="label">Article:</td>
			<td>
				<select name="article_id">
end_of_html
$sql="select article_id,article_name from article where status='A' order by article_name";
$sth=$dbhq->prepare($sql);
$sth->execute();
my $template_id;
my $tname;
while (($template_id,$tname)=$sth->fetchrow_array())
{
	if ($old_article_id == $template_id)
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
				</select>
			</td>
		  </tr>
		  <tr>
			<td class="label">Header:</td>
			<td class="field">
				<select class="field" name="header_id" id="header_id">
<option value=0 checked>--SELECT ONE--</option>
end_of_html
$sql="select header_id,header_name from Headers where status='A' order by header_name";
$sth=$dbhu->prepare($sql);
$sth->execute();
my $tid;
my $tname;
while (($tid,$tname)=$sth->fetchrow_array())
{
	if ($tid == $header_id)
	{
		print "<option selected value=$tid>$tname</option>\n";
	}
	else
	{
		print "<option value=$tid>$tname</option>\n";
	}
}
$sth->finish();

print<<"end_of_html";
				</select>
				<input type="button" value="edit" onClick="edit_header();" />
				<input type="button" value="add new header" onClick="add_header();" target="_blank"/>
			</td>
		  </tr>
		  <tr>
			<td class="label">Footer:</td>
			<td class="field">
				<select class="field" name="footer_id" id="footer_id">
<option value=0 checked>--SELECT ONE--</option>
end_of_html
$sql="select footer_id,footer_name from Footers where status='A' order by footer_name";
$sth=$dbhu->prepare($sql);
$sth->execute();
my $tid;
my $tname;
while (($tid,$tname)=$sth->fetchrow_array())
{
	if ($tid == $footer_id)
	{
		print "<option selected value=$tid>$tname</option>\n";
	}
	else
	{
		print "<option value=$tid>$tname</option>\n";
	}
}
$sth->finish();

print<<"end_of_html";
				</select>
				<input type="button" value="edit" onClick="edit_footer();" />
				<input type="button" value="add new footer" onClick="add_footer();" target="_blank"/>
			</td>
		  </tr>
		  <tr>
			<td class="label">Trace Header:</td>
			<td class="field">
				<select class="field" name="trace_header_id" id="trace_header_id">
<option value=0 checked>--SELECT ONE--</option>
end_of_html
$sql="select header_id,header_name from TraceHeaders where  $userDataRestrictionWhereClause status='A' order by header_name";
$sth=$dbhu->prepare($sql);
$sth->execute();
my $tid;
my $tname;
while (($tid,$tname)=$sth->fetchrow_array())
{
	if ($tid == $trace_header_id)
	{
		print "<option selected value=$tid>$tname</option>\n";
	}
	else
	{
		print "<option value=$tid>$tname</option>\n";
	}
}
$sth->finish();

print<<"end_of_html";
				</select>
				<input type="button" value="edit" onClick="edit_trace_header();" />
				<input type="button" value="add new header" onClick="add_trace_header();" target="_blank"/>
			</td>
		  </tr>
		  <tr>
			<td class="label">Include Wiki?</td>
end_of_html
if ($include_wiki eq "Y")
{
			print "<td><input class=\"radio\" type=\"radio\" checked value=\"Y\" name=\"wiki\" />Yes <input class=\"radio\" type=\"radio\" value=\"N\" name=\"wiki\" />No</td>\n";
}
else
{
			print "<td><input class=\"radio\" type=\"radio\" value=\"Y\" name=\"wiki\" />Yes <input class=\"radio\" type=\"radio\" checked value=\"N\" name=\"wiki\" />No</td>\n";
}
print<<"end_of_html";
		  </tr>
		  <tr>
			<td class="label">Insert random subdomain?</td>
end_of_html
if ($random_subdomain eq "Y")
{
			print "<td><input class=\"radio\" type=\"radio\" checked value=\"Y\" name=\"subdomain\" />Yes <input class=\"radio\" type=\"radio\" value=\"N\" name=\"subdomain\" />No</td>\n";
}
else
{
			print "<td><input class=\"radio\" type=\"radio\" value=\"Y\" name=\"subdomain\" />Yes <input class=\"radio\" type=\"radio\" checked value=\"N\" name=\"subdomain\" />No</td>\n";
}
print<<"end_of_html";
		  </tr>
		  <tr>
			<td class="label">New Mailing?</td>
end_of_html
if ($newMailing eq "Y")
{
			print "<td><input class=\"radio\" type=\"radio\" checked value=\"Y\" name=\"newMailing\" />Yes <input class=\"radio\" type=\"radio\" value=\"N\" name=\"newMailing\" />No</td>\n";
}
else
{
			print "<td><input class=\"radio\" type=\"radio\" value=\"Y\" name=\"newMailing\" />Yes <input class=\"radio\" type=\"radio\" checked value=\"N\" name=\"newMailing\" />No</td>\n";
}
print<<"end_of_html";
		  </tr>
		  <tr>
			<td class="label">CutMail?</td>
end_of_html
if ($CutMail eq "Y")
{
			print "<td><input class=\"radio\" type=\"radio\" checked value=\"Y\" name=\"CutMail\" />Yes <input class=\"radio\" type=\"radio\" value=\"N\" name=\"CutMail\" />No</td>\n";
}
else
{
			print "<td><input class=\"radio\" type=\"radio\" value=\"Y\" name=\"CutMail\" />Yes <input class=\"radio\" type=\"radio\" checked value=\"N\" name=\"CutMail\" />No</td>\n";
}
print<<"end_of_html";
		  </tr>
		  <tr>
			<td class="label">Group Suppression List:</td>
			<td class="field">
				<select class="field" name="groupSuppListID" id="groupSuppListID">
<option value=0 checked>None</option>
end_of_html
$sql="select list_id,list_name from vendor_supp_list_info where status='A' and suppressionType='Group' order by list_name";
$sth=$dbhu->prepare($sql);
$sth->execute();
my $slid;
my $sname;
while (($slid,$sname)=$sth->fetchrow_array())
{
	if ($slid == $groupSuppListID)
	{
		print "<option selected value=$slid>$sname</option>\n";
	}
	else
	{
		print "<option value=$slid>$sname</option>\n";
	}
}
$sth->finish();

print<<"end_of_html";
				</select>
			</td>
		  </tr>
		</table>

		<div class="submit">
			<input class="submit" type="submit" name="submit" value="deploy it" />
			<input class="submit" type="submit" name="submit" value="Deploy it and continue" />
		</div>
	</div>

</div>
</form>
<script language="JavaScript">
    update_domain(1);
end_of_html
#if ($uid > 0)
#{
#	print "upd_creative(1);\n";
#}
print<<"end_of_html";
</script>
</body>
</html>
end_of_html

sub setDeployType {
	
	my ($utype) = @_;
	
	my $deployTypes = [
		'Normal',
		'Time Based',
		'Hotmail',
		'Hotmail Domain'
	];
	
	my $html;
	
	foreach my $deployType (@{$deployTypes}){
		
		my $selected = '';
		
		if($deployType eq $utype){
			$selected = 'selected';
		}
		
		$html .= qq|<option value='$deployType' $selected>$deployType</option> \n|;
		
	}
	
	print $html;
	
}
