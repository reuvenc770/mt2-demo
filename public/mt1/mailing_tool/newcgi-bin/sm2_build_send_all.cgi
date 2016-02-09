#!/usr/bin/perl
#===============================================================================
# Name   : sm2_list.cgi - lists all test_campaigns of campaign_type='TEST' 
#
#--Change Control---------------------------------------------------------------
# 07/23/07  Jim Sobeck  Creation
#===============================================================================

#-----  include Perl Modules ---------
use strict;
use CGI;
use util;
use Lib::Database::Perl::Interface::Server;

#------  get some objects to use later ---------
my $util = util->new;
my $query = CGI->new;
my $sql;
my $sth;
my $dbh;
my $cid;
my $proxyGroupID;
my $company;
my $fname;
my $sdate;
my $copywriter;
my $schedule_everyday;
my $ctype=$query->param('type');
if ($ctype eq "")
{
	$ctype="T";
}
my ($email_addr,$copies_to_send,$client_id,$brand_id,$mailing_domain,$mailing_ip,$campaign_name,$adv_id,$creative_id,$subject_id,$from_id,$mailing_template,$include_wiki,$mailingHeaderID, $wikiID);
my $exID;
my $mail_from;
my $split_emails;
my $batchSize;
my $waitTime;
my $header_id;
my $footer_id;
my $trace_header_id;
my $include_open;
my $encrypt_link;
my $newMailing;
my $injectorID;
my $useRdns;
my $group_id;
my $fcode;
my $subject;
my $fromline;
my $isoConvertSubject;
my $isoConvertFrom;
my $utf8ConvertSubject;
my $utf8ConvertFrom;
my $SeedGroupID;
my $server_id;
my $send_time;
my $subjectstr="";
my $fromstr="";
my $usubjectstr="";
my $ufromstr="";
my $content_domain="";

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}

my $userDataRestrictionWhereClause = '';

$util->getUserData({'userID' => $user_id});

my $isExternalUser = $util->getUserData()->{'isExternalUser'};

if($util->getUserData()->{'isExternalUser'} == 1)
{
	$userDataRestrictionWhereClause = qq|
        userID = $user_id AND
    |;
}

#------  connect to the util database -----------
my ($dbhq,$dbhu)=$util->get_dbh();
#$ENV{'DATABASE_USER'}="slavedb.i.routename.com";
#$dbhq = DBI->connect("DBI:mysql:new_mail:slavedb.i.routename.com", "db_user", "sp1r3V");
my $serverInterface     = Lib::Database::Perl::Interface::Server->new();
my $errors;
my $arr;

my $testid=0;
$testid=$query->param('testid');
$sql="select curdate()";
$sth=$dbhq->prepare($sql);
$sth->execute();
($sdate)=$sth->fetchrow_array();
$sth->finish();

$schedule_everyday="N";
$email_addr="";
$batchSize=0;
$waitTime=0;
$exID=0;
$split_emails="N";
$copies_to_send=1;
$client_id=0;
$brand_id=0;
$mailing_domain="ALL";
$send_time=0;
$mailing_ip="ALL";
$campaign_name="";
$mail_from="";
$adv_id=305;
$creative_id=13222;
$subject_id=24603;
$from_id=17336;
$mailing_template=1;
$include_wiki="Y";
$header_id=0;
$footer_id=0;
$trace_header_id=0;
$include_open="Y";
$encrypt_link="Y";
$newMailing="N";
$proxyGroupID=0;
$useRdns="N";
$isoConvertSubject="N";
$isoConvertFrom="N";
$utf8ConvertSubject="N";
$utf8ConvertFrom="N";
$group_id=0;
if (($testid ne "") and ($testid > 0))
{
	$sql="select send_date,email_addr,copies_to_send,mailing_domain,campaign_name,advertiser_id,creative_id,subject_id,from_id,mailing_template,include_wiki,mailingHeaderID,wikiTemplateID,header_id,footer_id,include_open,server_id,hour(send_time),encrypt_link,trace_header_id,split_emails,mail_from,isoConvertSubject,isoConvertFrom,SeedGroupID,batchSize,waitTime,IpExclusionID,schedule_everyday,useRdns,group_id,content_domain,utf8ConvertSubject,utf8ConvertFrom,newMailing,injectorID,proxyGroupID from test_campaign where $userDataRestrictionWhereClause test_id=$testid";
	$sth=$dbhu->prepare($sql);
	$sth->execute();
	($sdate,$email_addr,$copies_to_send,$mailing_domain,$campaign_name,$adv_id,$creative_id,$subject_id,$from_id,$mailing_template,$include_wiki,$mailingHeaderID,$wikiID,$header_id,$footer_id,$include_open,$server_id,$send_time,$encrypt_link,$trace_header_id,$split_emails,$mail_from,$isoConvertSubject,$isoConvertFrom,$SeedGroupID,$batchSize,$waitTime,$exID,$schedule_everyday,$useRdns,$group_id,$content_domain,$utf8ConvertSubject,$utf8ConvertFrom,$newMailing,$injectorID,$proxyGroupID)=$sth->fetchrow_array();
	$sth->finish();
}
if ($isoConvertSubject eq "Y")
{
	$subjectstr="checked";
}
if ($isoConvertFrom eq "Y")
{
	$fromstr="checked";
}
if ($utf8ConvertSubject eq "Y")
{
	$usubjectstr="checked";
}
if ($utf8ConvertFrom eq "Y")
{
	$ufromstr="checked";
}
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
<title>Build a StrongMail Test Campaign</title>
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

#container {
	width: 70%;
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
	width: 40%;
	text-align: right;
  }

td.field {
	width: 60%;
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

.submit {
	text-align: center;
	margin-bottom: .3em;
  }

input.submit {
	margin-top: 1em;
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
function popup_validate()
{
    url="http://validator.w3.org/#validate_by_input";
    window.open(url,'Validate','width=850,height=650,toolbar=no,location=no,scrollbars=yes,resizable=no');
}

function add_wikiTempate()
{
        var newwin = window.open("/cgi-bin/wikiTemplate/template_disp.cgi?mode=A","AddTemplate", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1");
        newwin.focus();
}

function edit_wikiTempate()
{
   	var selObj = document.getElementById('wikiTemplateID');
    var selIndex = selObj.selectedIndex;
    var newwin = window.open("/cgi-bin/wikiTemplate/template_disp.cgi?pmode=U&nl_id="+selObj.options[selIndex].value,"EditTemplate", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1");
    newwin.focus();
}

function add_mailingHeader()
{
        var newwin = window.open("/cgi-bin/mailHeaders/template_disp.cgi?mode=A","AddTemplate", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1");
        newwin.focus();
}

function edit_mailingHeader()
{
   	var selObj = document.getElementById('mailingHeaderID');
    var selIndex = selObj.selectedIndex;
    var newwin = window.open("/cgi-bin/mailHeaders/template_disp.cgi?pmode=U&nl_id="+selObj.options[selIndex].value,"EditTemplate", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1");
    newwin.focus();
}

function add_nl()
{
        var newwin = window.open("/cgi-bin/template_disp.cgi?mode=A","AddNewsletter", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1");
        newwin.focus();
}
function edit_nl()
{
   	var selObj = document.getElementById('template_id');
    var selIndex = selObj.selectedIndex;
        var newwin = window.open("/cgi-bin/template_disp.cgi?mode=U&nl_id="+selObj.options[selIndex].value,"AddNewsletter", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1");
        newwin.focus();
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
function add_footer()
{
        var newwin = window.open("/cgi-bin/Footers/template_disp.cgi?mode=A","AddFooter", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1");
        newwin.focus();
}
function edit_footer()
{
   	var selObj = document.getElementById('header_id');
    var selIndex = selObj.selectedIndex;
        var newwin = window.open("/cgi-bin/Footers/template_disp.cgi?mode=U&nl_id="+selObj.options[selIndex].value,"AddFooter", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1");
        newwin.focus();
}
function view_thumbnails()
{
   	var selObj = document.getElementById('adv_id');
    var selIndex = selObj.selectedIndex;
       var newwin = window.open("/cgi-bin/view_thumbnails.cgi?aid="+selObj.options[selIndex].value,"AddNewsletter", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1");
        newwin.focus();
}
function edit_advertiser()
{
   	var selObj = document.getElementById('adv_id');
    var selIndex = selObj.selectedIndex;
       var newwin = window.open("/cgi-bin/advertiser_disp2.cgi?pmdoe=U&puserid="+selObj.options[selIndex].value,"AddNewsletter", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1");
        newwin.focus();
}

function update_brand()
{
    var selObj = document.getElementById('clientid');
    var selIndex = selObj.selectedIndex;
    var selLength = campform.brandid.length;
    while (selLength>0)
    {
        campform.brandid.remove(selLength-1);
        selLength--;
    }
    campform.brandid.length=0;
    parent.frames[1].location="/newcgi-bin/sm2_upd_brand.cgi?cid="+selObj.options[selIndex].value;
}

function addBrand(value,text)
{
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    campform.brandid.add(newOpt);
}

function addDomain(value,text)
{
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    campform.domainid.add(newOpt);
}
function addIP(value,text)
{
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    campform.ipid.add(newOpt);
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
function update_domain()
{
    var selObj = document.getElementById('proxyGroupID');
    var selIndex = selObj.selectedIndex;
    var selLength = campform.domainid.length;
    while (selLength>0)
    {
        campform.domainid.remove(selLength-1);
        selLength--;
    }
    campform.domainid.length=0;
    parent.frames[1].location="/newcgi-bin/sm2_upd_domain.cgi?bid="+selObj.options[selIndex].value;
}
function upd_creative()
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
    parent.frames[1].location="/newcgi-bin/sm2_upd_creative.cgi?all=1&aid="+selObj.options[selIndex].value;
}
</script>
</head>

<body>
<div id="container">
	<h1>Send All Test</h1>
	<h2><a href="sm2_send_all_list.cgi" target=_top>View Send All Tests</a> | <a href="sm2_send_all_list.cgi?everyday=1" target=_top>View Send Everyday</a> | <a href="sm2_list.cgi" target=_top>view/deploy tests</a> | <a href="sm2_list.cgi?type=D" target=_top>view deployed campaigns</a> | <a href="mainmenu.cgi" target=_top>go home</a></h2>
	<div id="form">
	<form method=post name="campform" id="campform" action="sm2_send_all_save.cgi" target=_top>
	<input type=hidden name=tid value="$testid">
	<input type=hidden name=type value="$ctype">
	<input type=hidden name=copies value="1">

		<table>
		  <tr>
			<td class="label">Email Address(es)<i> (If ALL chosen for MTA then SM2 Seeds will be used</i>):</td>
end_of_html
	print "<td class=\"field\"><input class=\"field\" id=\"\" type=\"\" name=\"email\" value=\"$email_addr\" size=\"50\" /></td>\n";
print<<"end_of_html";
		  </tr>
		  <tr>
			<td class="label">Split Emails Across Email Addresses:</td>
			<td class="field">
				<select class="field" id=split_emails name=split_emails>
end_of_html
if ($split_emails eq "N")
{
	print "<option selected value=N>No</option><option value=Y>Yes</option>\n";
}
else
{
	print "<option value=N>No</option><option selected value=Y>Yes</option>\n";
}
print<<"end_of_html";
				</select>
			</td></tr>
		  <tr>
			<td class="label">MTA:</td>
			<td class="field">
				<select class="field" id=server_id name=server_id>
<option value=0>ALL</option>
end_of_html
my $errors;
my $results;
my $params;
$params->{active}=1;
($errors, $results) = $serverInterface->getMtaServers($params);
for my $server (@$results)
{
	if ($server->{'serverName'} eq "mta02.routename.com")
	{
		next;
	}
	if ($server->{'serverName'} eq "mta34.routename.com")
	{
		next;
	}
	if ($server->{'serverID'} == $server_id)
	{
		print "<option selected value=\"$server->{'serverID'}\">$server->{'serverName'}</option>\n";
	}
	else
	{
		print "<option value=\"$server->{'serverID'}\">$server->{'serverName'}</option>\n";
	}
}
print<<"end_of_html";
				</select>
			</td>
		  </tr>
		  <tr>
			<td class="label">SM2 SeedGroup:</td>
			<td class="field">
				<select class="field" id=seedgroup name=seedgroup>
end_of_html
my $sgroupid;
my $sgroupname;
$sql="select SeedGroupID,SeedGroupName from SM2SeedsGroup order by SeedGroupID";
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($sgroupid,$sgroupname)=$sth->fetchrow_array())
{
	if ($sgroupid == $SeedGroupID)
	{
		print "<option selected value=\"$sgroupid\">$sgroupname</option>\n";
	}
	else
	{
		print "<option value=\"$sgroupid\">$sgroupname</option>\n";
	}
}
print<<"end_of_html";
				</select>
			</td>
		  </tr>
		<tr><td class="label">Scheduled For: </td><td class="field"><input type=text name=sdate maxlength=10 size=10 value="$sdate">
end_of_html
if ($schedule_everyday eq "Y")
{
	print "&nbsp;&nbsp;<input type=checkbox name=schedule_everyday checked value=Y>Schedule Everyday";
}
else
{
	print "&nbsp;&nbsp;<input type=checkbox name=schedule_everyday value=Y>Schedule Everyday";
}
print<<"end_of_html";
</td></tr>
		<tr><td class="label">Time: </td><td class="field"><select name=shour>
end_of_html
my $j=1;
my $thour=$send_time;
if ($send_time > 12)
{
	$thour=$send_time-12;
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
print "</select><select name=am_pm>\n";
if ($send_time <= 12)
{
	print "<option selected value=\"AM\">AM</option>\n";
	print "<option value=\"PM\">PM</option>\n";
}
else
{
	print "<option value=\"AM\">AM</option>\n";
	print "<option selected value=\"PM\">PM</option>\n";
}
print<<"end_of_html";
</select></td></tr>
		  <tr>
			<td class="label">Batch Size(set to zero to disable):</td>
			<td class="field">
			<input class="field" id="" type="" name="batchSize" value="$batchSize" size="10" /></td>
			</tr>
		  <tr>
			<td class="label">Wait Time(set to zero to disable):</td>
			<td class="field">
			<input class="field" id="" type="" name="waitTime" value="$waitTime" size="10" /></td>
			</tr>
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
			<td class="label"><br />Proxy Group:</td>
			<td class="field"><br />
				<select class="field" name=proxyGroupID id=proxyGroupID onChange="update_domain();">
end_of_html
my $proxyName;
$sql="select proxyGroupID,proxyGroupName from ProxyGroup order by proxyGroupName"; 
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($cid,$proxyName) = $sth->fetchrow_array())
{
	if ($proxyGroupID == $cid)
	{
		print "<option selected value=$cid>$proxyName</option>\n";
	}
	else
	{
		print "<option value=$cid>$proxyName</option>\n";
	}
}
$sth->finish();
print<<"end_of_html";
				</select>
			</td>
		  </tr>
		  <tr>
			<td class="label">Mailing Domain:</td>
			<td class="field">
				<select class="field" id=domainid name=domainid>
<option value="PAIR UP ALL">PAIR UP ALL</option>
end_of_html
my $mdomain="";
my $mip="";
if ($testid > 0)
{
	my $tdomain;
	$sql="select domainName from DomainProxyGroup dpg, Domain d where dpg.domainID=d.domainID and dpg.proxyGroupID=$proxyGroupID order by domainName";
	$sth=$dbhu->prepare($sql);
	$sth->execute();
	while (($tdomain)=$sth->fetchrow_array())
	{
		if ($tdomain eq $mailing_domain)
		{
			print "<option value=$tdomain selected>$tdomain</option>";
		}
		else
		{
			print "<option value=$tdomain>$tdomain</option>";
		}	
	}
	$sth->finish();
	$sql="select mailing_domain from SendAllTestDomain where $userDataRestrictionWhereClause test_id=$testid";
	$sth=$dbhu->prepare($sql);
	$sth->execute();
	while (($tdomain)=$sth->fetchrow_array())
	{
		$mdomain=$mdomain.$tdomain."\n";
	}
	chop($mdomain);
	$sth->finish();
	my $tdomain;
	$sql="select ip_addr from SendAllTestIp where $userDataRestrictionWhereClause test_id=$testid";
	$sth=$dbhu->prepare($sql);
	$sth->execute();
	while (($tdomain)=$sth->fetchrow_array())
	{
		$mip=$mip.$tdomain."\n";
	}
	chop($mip);
	$sth->finish();
}
print<<"end_of_html";
				</select>
			</td>
		  </tr>
          <tr>
            <td class="label"><br />Content Domain(<em class="note">optional</em>):</td>
            <td class="field"><br /><input class="field" id="" type="" name="content_domain" value="$content_domain" size="50" /></td>
          </tr>
		  <tr>
			<td class="label">Send All Permutations:</td>
			<td class="field"><select name=permutation_flag><option value=Enabled>Enabled</option><option value=Disabled selected>Disabled</option></select></td></tr>
		  <tr>
			<td class="label">Use Rdns:</td>
end_of_html
if ($useRdns eq "Y")
{
print<<"end_of_html";
			<td class="field"><input class="radio" type="radio" value="Y" checked name="useRdns" />Yes <input class="radio" type="radio" value="N" name="useRnds" />No</td>
end_of_html
}
else
{
print<<"end_of_html";
			<td class="field"><input class="radio" type="radio" value="Y" name="useRdns" />Yes <input class="radio" type="radio" checked value="N" name="useRdns" />No</td>
end_of_html
}
print<<"end_of_html";
	</tr>
		  <tr>
			<td class="label">Manual Mailing Domains:</td>
			<td class="field"><textarea name=mdomain rows=10 cols=50>$mdomain</textarea></td>
			</tr>
		  <tr>
			<td class="label">IP Group:</td>
			<td class="field"><select name=ipgroup_id>
end_of_html
my $gid;
my $gname;
$sql="select group_id,group_name from IpGroup where $userDataRestrictionWhereClause status='Active' group by group_name";
my $sthi=$dbhu->prepare($sql);
$sthi->execute();
while (($gid,$gname)=$sthi->fetchrow_array())
{
	if ($gid == $group_id)
	{
		print "<option value=$gid selected>$gname</option>";
	}
	else
	{
		print "<option value=$gid>$gname</option>";
	}
}
$sthi->finish();
print<<"end_of_html";
		</td></tr>
		  <tr>
			<td class="label">Manual Mailing IPs:</td>
			<td class="field"><textarea name=mip rows=10 cols=50>$mip</textarea></td>
			</tr>
		  <tr>
			<td class="label">IP Exclusion:</td>
			<td class="field">
				<select class="field" id=ipexclusionid name=ipexclusionid>
<option value=0>None</option>
end_of_html
$sql="select IpExclusionID, IpExclusion_name from IpExclusion where IpExclusionID > 1 order by IpExclusion_name";
$sth = $dbhu->prepare($sql) ;
$sth->execute();
my ($ID,$exname);
while (($ID,$exname) = $sth->fetchrow_array())
{
	if ($ID eq $exID)
	{
		print "<option selected value=\"$ID\">$exname</option>\n";
	}
	else
	{
		print "<option value=\"$ID\">$exname</option>\n";
		print "<option value=\"$ID\">$exname</option>\n";
	}
}
$sth->finish();
print<<"end_of_html";
				</select>
			</td>
		  </tr>
		  <tr>
			<td class="label">Email Header Template:</td>
			<td class="field">
				<select class="field" id=mailingHeaderID name=mailingHeaderID>
end_of_html
$sql="select templateID, templateName from mailingHeaderTemplate where $userDataRestrictionWhereClause 1";
$sth = $dbhq->prepare($sql) ;
$sth->execute();
my ($templateID,$templateName);
while (($templateID,$templateName) = $sth->fetchrow_array())
{
	if ($templateID eq $mailingHeaderID)
	{
		print "<option selected value=\"$templateID\">$templateName</option>\n";
	}
	else
	{
		print "<option value=\"$templateID\">$templateName</option>\n";
	}
}
$sth->finish();
print<<"end_of_html";
				</select>
				<input type="button" value="edit" onClick="edit_mailingHeader();" />
				<input type="button" value="add new template" onClick="add_mailingHeader();" target="_blank"/>
			</td>
		  </tr>
		  	<tr>
			<td class="label">Wiki Header Template:</td>
			<td class="field">
				<select class="field" id=wikiTemplateID name=wikiTemplateID>
end_of_html
$sql="select wikiID, templateName from wikiTemplate where $userDataRestrictionWhereClause 1";
$sth = $dbhq->prepare($sql) ;
$sth->execute();

while (my ($wikiTemplateID,$templateName) = $sth->fetchrow_array())
{
	if ($wikiTemplateID eq $wikiID)
	{
		print "<option selected value=\"$wikiTemplateID\">$templateName</option>\n";
	}
	else
	{
		print "<option value=\"$wikiTemplateID\">$templateName</option>\n";
	}
}
$sth->finish();
print<<"end_of_html";
				</select>
				<input type="button" value="edit" onClick="edit_wikiTempate();" />
				<input type="button" value="add new template" onClick="add_wikiTempate();" target="_blank"/>
			</td>
		  </tr>
		  <tr>
			<td class="label"><br />Test Campaign Name (<em class="note">optional</em>):</td>
			<td class="field"><br /><input class="field" id="" type="" name="cname" value="$campaign_name" size="50" /></td>
		  </tr>
end_of_html
if ($ctype eq "F")
{
print<<"end_of_html";
		  <tr>
			<td class="label">Creative:</td>
			<td class="field"><textarea cols=50 rows=10 name=creative>$fcode</textarea></td>
		</tr>
		  <tr>
			<td class="label">Subject:</td>
			<td class="field"><input type=text size=50 maxlength=50 name=subject value="$subject"></td>
		</tr>
		  <tr>
			<td class="label">From:</td>
			<td class="field"><input type=text size=50 maxlength=50 name=fromline value="$fromline"></td>
		</tr>
end_of_html
}
else
{
print<<"end_of_html";
		  <tr>
			<td class="label">Advertiser: (<a href="" class="note"><em><a href="javascript:edit_advertiser();">edit selected</em></a>)</td>
			<td class="field">
				<select class="field" name="adv_id" id="adv_id" onChange="upd_creative();">
end_of_html
$sql="select advertiser_id,advertiser_name from advertiser_info where status='A' order by advertiser_name";
$sth=$dbhq->prepare($sql);
$sth->execute();
my $aid;
my $aname;
while (($aid,$aname)=$sth->fetchrow_array())
{
	if($isExternalUser)
	{
		$aname = $aid;
	}
	
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
				</select>
			</td>
		  </tr>
		  <tr>
			<td class="label">Creative:</td>
			<td class="field">
				<select class="field" name="creative" id="creative">
end_of_html
$sql = "select creative_id, creative_name,original_flag, trigger_flag, approved_flag,mediactivate_flag,copywriter from creative where status='A' and advertiser_id=$adv_id order by creative_name";
$sth = $dbhq->prepare($sql);
$sth->execute();
my $cid;
my $cname;
my $oflag;
my $tflag;
my $aflag;
my $mflag;
my $temp_str;
while (($cid,$cname,$oflag,$tflag,$aflag,$mflag,$copywriter) = $sth->fetchrow_array())
{
	$temp_str = $cid . " - ". $cname . " (";
	if ($tflag eq "Y")
	{
		$temp_str = $temp_str . "TRIGGER - ";
	}
	if ($oflag eq "Y")
	{
		$temp_str = $temp_str . "O ";
	}
	else
	{
		$temp_str = $temp_str . "A ";
	}
	if ($copywriter eq "Y")
	{
		$temp_str = $temp_str . "C ";
	}
	if ($mflag eq "Y")
	{
		$temp_str = $temp_str . " - M ";
	}
	if ($aflag eq "Y")
	{
		$temp_str = $temp_str . ")";
	}
	else
	{
		$temp_str = $temp_str . "- NA!)";
	}
	if ($creative_id == $cid)
	{
		print "<option selected value=$cid>$temp_str</option>\n";
	}
	else
	{
		print "<option value=$cid>$temp_str</option>\n";
	}
}
$sth->finish();

print<<"end_of_html";
				</select>
				<input type="button" value="view all thumbnails" onClick="view_thumbnails();" />
			</td>
		  </tr>
		  <tr>
			<td class="label">Subject:</td>
			<td class="field">
				<select class="field" name="csubject" id="csubject">
end_of_html
$sql = "select subject_id,advertiser_subject,approved_flag,original_flag,copywriter from advertiser_subject where advertiser_id=$adv_id and status='A' order by advertiser_subject";
$sth = $dbhq->prepare($sql);
$sth->execute();
my $csubject;
my $sid;
my $aflag;
my $oflag;
while (($sid,$csubject,$aflag,$oflag,$copywriter) = $sth->fetchrow_array())
{
    $temp_str = $sid . " - " . $csubject. " (";
    if ($oflag eq "Y")
    {
        $temp_str = $temp_str . "O ";
    }
    else
    {
        $temp_str = $temp_str . "A ";
    }
    if ($copywriter eq "Y")
    {
        $temp_str = $temp_str . "C ";
    }
    if ($aflag eq "Y")
    {
        $temp_str = $temp_str . ")";
    }
    else
    {
        $temp_str = $temp_str . "- NA!)";
    }
	if ($sid == $subject_id)
	{
		print "<option selected value=$sid>$temp_str</option>\n";
	}
	else
	{
		print "<option value=$sid>$temp_str</option>\n";
	}
}
$sth->finish();
print<<"end_of_html";
				</select>&nbsp;&nbsp;<input type=checkbox name=convert_subject $subjectstr value=Y>Convert Subject to ISO&nbsp;&nbsp;<input type=checkbox name=convert_subjectu $usubjectstr value=Y>Convert Subject to UTF8 
			</td>
		  </tr>
		  <tr>
			<td class="label">From Line:</td>
			<td class="field">
				<select class="field" name="cfrom" id="cfrom">
end_of_html
$sql = "select from_id,advertiser_from,approved_flag,original_flag,copywriter from advertiser_from where advertiser_id=$adv_id and status='A' order by advertiser_from";
$sth = $dbhq->prepare($sql);
$sth->execute();
my $cfrom;
my $sid;
my $aflag;
my $oflag;
while (($sid,$cfrom,$aflag,$oflag,$copywriter) = $sth->fetchrow_array())
{
    $temp_str = $sid . " - " . $cfrom. " (";
    if ($oflag eq "Y")
    {
        $temp_str = $temp_str . "O ";
    }
    else
    {
        $temp_str = $temp_str . "A ";
    }
    if ($copywriter eq "Y")
    {
        $temp_str = $temp_str . "C ";
    }
    if ($aflag eq "Y")
    {
        $temp_str = $temp_str . ")";
    }
    else
    {
        $temp_str = $temp_str . "- NA!)";
    }
	if ($sid == $from_id)
	{
		print "<option selected value=$sid>$temp_str</option>\n";
	}
	else
	{
		print "<option value=$sid>$temp_str</option>\n";
	}
}
$sth->finish();
print<<"end_of_html";
				</select>&nbsp;&nbsp;<input type=checkbox name=convert_from $fromstr value=Y>Convert From to ISO&nbsp;&nbsp;<input type=checkbox name=convert_fromu $ufromstr value=Y>Convert From to UTF8 
			</td>
		  </tr>
end_of_html
}
print<<"end_of_html";
		  <tr>
			<td class="label"><br />Mail From:</td>
			<td class="field"><br /><input class="field" id="mail_from" type="" name="mail_from" value="$mail_from" size="50" /></td>
		  </tr>
		  <tr>
			<td class="label"><br />Mailing Template:</td>
			<td class="field"><br />
				<select class="field" name="template_id" id="template_id">
end_of_html
$sql="select template_id,template_name from brand_template where $userDataRestrictionWhereClause status='A' order by template_id";
$sth=$dbhu->prepare($sql);
$sth->execute();
my $tid;
my $tname;
while (($tid,$tname)=$sth->fetchrow_array())
{
	if ($tid == $mailing_template)
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
				<input type="button" value="edit" onClick="edit_nl();" />
				<input type="button" value="add new template" onClick="add_nl();" target="_blank"/>
			</td>
		  </tr>
		  <tr>
			<td class="label">Header:</td>
			<td class="field">
				<select class="field" name="header_id" id="header_id">
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
				<select class="field" name="footer_id" id="footer_id"><option value=0>None</option>
end_of_html
$sql="select footer_id,footer_name from Footers where $userDataRestrictionWhereClause status='A' order by footer_name";
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
$sql="select header_id,header_name from TraceHeaders where $userDataRestrictionWhereClause status='A' order by header_name";
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
print<<"end_of_html";
			<td class="field"><input class="radio" type="radio" value="Y" checked name="wiki" />Yes <input class="radio" type="radio" value="N" name="wiki" />No</td>
end_of_html
}
else
{
print<<"end_of_html";
			<td class="field"><input class="radio" type="radio" value="Y" name="wiki" />Yes <input class="radio" type="radio" checked value="N" name="wiki" />No</td>
end_of_html
}
print<<"end_of_html";
		  </tr>
			<tr>
			<td class="label">Include Open Link?</td>
end_of_html
if ($include_open eq "Y")
{
print<<"end_of_html";
			<td class="field"><input class="radio" type="radio" value="Y" checked name="include_open" />Yes <input class="radio" type="radio" value="N" name="include_open" />No</td>
end_of_html
}
else
{
print<<"end_of_html";
			<td class="field"><input class="radio" type="radio" value="Y" name="include_open" />Yes <input class="radio" type="radio" checked value="N" name="include_open" />No</td>
end_of_html
}
print<<"end_of_html";
		  </tr>
			<tr>
			<td class="label">Encrypt Links?</td>
end_of_html
if ($encrypt_link eq "Y")
{
print<<"end_of_html";
			<td class="field"><input class="radio" type="radio" value="Y" checked name="encrypt_link" />Yes <input class="radio" type="radio" value="N" name="encrypt_link" />No</td>
end_of_html
}
else
{
print<<"end_of_html";
			<td class="field"><input class="radio" type="radio" value="Y" name="encrypt_link" />Yes <input class="radio" type="radio" checked value="N" name="encrypt_link" />No</td>
end_of_html
}
print<<"end_of_html";
		  </tr>
end_of_html
print "</tr><tr><td class=label>New Mailing?</td>";
if ($newMailing eq "Y")
{
print<<"end_of_html";
			<td class="field"><input class="radio" type="radio" value="Y" checked name="newMailing" />Yes <input class="radio" type="radio" value="N" name="newMailing" />No</td>
end_of_html
}
else
{
print<<"end_of_html";
			<td class="field"><input class="radio" type="radio" value="Y" name="newMailing" />Yes <input class="radio" type="radio" checked value="N" name="newMailing" />No</td>
end_of_html
}
print<<"end_of_html";
		  </tr>
		<tr><td>&nbsp;</td></tr>
		</table>

		<div class="submit">
			<input class="submit" type="submit" name="submit" value="send it" />&nbsp;&nbsp;<input class="submit" type="submit" name="submit" value="save it and continue" />
		</div>
	</div>

</div>
</form>
</body>
</html>
end_of_html
