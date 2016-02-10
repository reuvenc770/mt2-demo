#!/usr/bin/perl
#===============================================================================
# Name   : sm2_list.cgi - lists all test_campaigns of campaign_type='TEST' 
#
#--Change Control---------------------------------------------------------------
# 07/23/07  Jim Sobeck  Creation
#===============================================================================

#-----  include Perl Modules ---------
use strict;
use Lib::Database::Perl::Interface::Server;
use CGI;
use util;

#------  get some objects to use later ---------
my $util = util->new;
my $query = CGI->new;
my $sql;
my $sth;
my $dbh;
my $cid;
my $company;
my $fname;
my $cnt;
my $copywriter;
my $injectorID;
my $ctype=$query->param('type');
my $pmesg=$query->param('pmesg');
if ($ctype eq "")
{
	$ctype="T";
}
my ($email_addr,$copies_to_send,$client_id,$brand_id,$mailing_domain,$mailing_ip,$campaign_name,$adv_id,$creative_id,$subject_id,$from_id,$mailing_template,$include_wiki,$mailingHeaderID, $wikiID);
my $proxyGroupID;
my $article_id;
my $header_id;
my $footer_id;
my $trace_header_id;
my $mail_from;
my $use_mail_from;
my $convert_subject;
my $convert_from;
my $convert_usubject;
my ($base64EncodeSubject,$base64EncodeFrom,$subjectEncoding,$fromEncoding);
my $convert_ufrom;
my $content_domain;
my $useRdns;
my $group_id;
my $return_path;
my $mail_from2;
my $include_open;
my $encrypt_link;
my $newMailing;
my $CutMail;
my $include_mailto;
my $fcode;
my $subject;
my $fromline;
my $use_test;
my $continuous_flag;
my $split_emails;
my $batchSize;
my $waitTime;
my $notexternalUser;
my @ENC=("None","ISO","UTF8-Q","UTF8-B","UTF-7","ASCII","UTF-16","UTF-8 HOT","ISO B","UTF-8Q HOT","UTF-7 HOT","ASCII HOT","UTF-16 HOT","ISO B HOT","ISO Q HOT");
my @ENC1=("","ISO","UTF8","UTF8-B","UTF-7","ASCII","UTF-16","UTF-8 HOT","ISO B","UTF-8Q HOT","UTF-7 HOT","ASCII HOT","UTF-16 HOT","ISO B HOT","ISO Q HOT");

#-----  check for login  ------
$notexternalUser=1;
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}

$util->getUserData({'userID' => $user_id});

my $userDataRestrictionWhereClause = '';

if($util->getUserData()->{'isExternalUser'} == 1)
{
	$userDataRestrictionWhereClause = qq|
        userID = $user_id AND
    |;
    
    $notexternalUser=0;
}

#------  connect to the util database -----------
my ($dbhq,$dbhu)=$util->get_dbh();
my $testid=$query->param('tid');
if ($testid eq "")
{
	$testid=0;
	$use_test="Y";
}
if ($testid > 0)
{
	$sql="select email_addr,copies_to_send,client_id,brand_id,mailing_domain,mailing_ip,campaign_name,advertiser_id,creative_id,subject_id,from_id,mailing_template,include_wiki,campaign_type, mailingHeaderID, wikiTemplateID,freestyle_code,subject,fromline,header_id,footer_id,include_open,use_test,continuous_flag,article_id,encrypt_link,trace_header_id,mail_from,isoConvertSubject,isoConvertFrom,content_domain,return_path,split_emails,batchSize,waitTime,newMailing,utf8ConvertSubject,utf8ConvertFrom,include_mailto,useRdns,group_id,injectorID,use_mail_from,proxyGroupID,base64EncodeSubject,base64EncodeFrom,subjectEncoding,fromEncoding,mail_from2,CutMail from test_campaign where $userDataRestrictionWhereClause test_id=$testid";
	$sth=$dbhu->prepare($sql);
	$sth->execute();
	($email_addr,$copies_to_send,$client_id,$brand_id,$mailing_domain,$mailing_ip,$campaign_name,$adv_id,$creative_id,$subject_id,$from_id,$mailing_template,$include_wiki,$ctype,$mailingHeaderID, $wikiID,$fcode,$subject,$fromline,$header_id,$footer_id,$include_open,$use_test,$continuous_flag,$article_id,$encrypt_link,$trace_header_id,$mail_from,$convert_subject,$convert_from,$content_domain,$return_path,$split_emails,$batchSize,$waitTime,$newMailing,$convert_usubject,$convert_ufrom,$include_mailto,$useRdns,$group_id,$injectorID,$use_mail_from,$proxyGroupID,$base64EncodeSubject,$base64EncodeFrom,$subjectEncoding,$fromEncoding,$mail_from2,$CutMail)=$sth->fetchrow_array();
	$sth->finish();
	if ($ctype eq "HISTORY")
	{
		$ctype="H";
	}
	else
	{
		$ctype="T";
	}
	$mail_from="";
	$sql="select mail_from from test_campaign_mailfrom where test_id=$testid";
	$sth=$dbhu->prepare($sql);
	$sth->execute();
	my $tfrom;
	while (($tfrom)=$sth->fetchrow_array())
	{
		$mail_from=$mail_from.$tfrom."\n";
	}
	$sth->finish();
	chop($mail_from);
}
else
{
	$email_addr="";
	$copies_to_send=1;
	$article_id=0;
	$client_id=1;
	$brand_id=0;
	$mailing_domain="ALL";
	$mailing_ip="ALL";
	$campaign_name="";
	$adv_id=0;
	$creative_id=13222;
	$subject_id=24603;
	$from_id=17336;
	$wikiID=23;
	$mailing_template=1;
	$include_wiki="Y";
	$header_id=0;
	$footer_id=0;
	$trace_header_id=0;
	$mail_from="";
	$use_mail_from="N";
	$proxyGroupID=0;
	$include_open="Y";
	$encrypt_link="Y";
	$newMailing="N";
	$include_mailto="N";
	$continuous_flag="N";
	$convert_subject="N";
	$convert_from="N";
	$convert_usubject="N";
	$convert_ufrom="N";
	$content_domain="";
	$useRdns="N";
	$return_path="";
	$mail_from2="";
	$CutMail="N";
	$split_emails="N";
	$batchSize=0;
	$waitTime=0;
	$group_id=0;
	$base64EncodeSubject="N";
	$base64EncodeFrom="N";
	$subjectEncoding="";
	$fromEncoding="";
}
        print "Content-Type: text/html\n\n";
print<<"end_of_html";
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
end_of_html
if ($ctype eq "H")
{
	print "<title>Build a History Building Campaign</title>\n";
}
else
{
	print "<title>Build a StrongMail Test Campaign</title>\n";
}
print<<"end_of_html";

<style type="text/css">
end_of_html
if ($ctype eq "H")
{
print<<"end_of_html";
BODY {
    FONT-SIZE: 0.9em; BACKGROUND: url(http://www.affiliateimages.com/temp/green_bg.jpg) #B9F795 repeat-x center top; COLOR: #4d4d4d; F
ONT-FAMILY: "Trebuchet MS", Tahoma, Arial, sans-serif
}
end_of_html
}
else
{
print<<"end_of_html";
body {
	background: url(http://www.affiliateimages.com/temp/bg.jpg) top center repeat-x #99D1F4;
	font-family: "Trebuchet MS", Tahoma, Arial, sans-serif;
	font-size: .9em;
	color: #4d4d4d;
  }
end_of_html
}
print<<"end_of_html";

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
end_of_html
if ($pmesg ne "")
{
	print "alert('$pmesg');\n";
}
print<<"end_of_html";
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

function add_wikiTemplate()
{
        var newwin = window.open("/cgi-bin/wikiTemplate/template_disp.cgi?mode=A&s=1","AddTemplate", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1");
        newwin.focus();
}

function clearWiki()
{
    var selObj = document.getElementById('wikiTemplateID');
    var selIndex = selObj.selectedIndex;
    var selLength = campform.wikiTemplateID.length;
    while (selLength>0)
    {
        campform.wikiTemplateID.remove(selLength-1);
        selLength--;
    }
    campform.wikiTemplateID.length=0;
}

function addWiki(value,text)
{
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    campform.wikiTemplateID.add(newOpt);
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
function addUSA(value,text)
{
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    campform.cusa.add(newOpt);
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
    var selLength = campform.cusa.length;
    while (selLength>0)
    {
        campform.cusa.remove(selLength-1);
        selLength--;
    }
    campform.cusa.length=0;
    parent.frames[1].location="/newcgi-bin/sm2_upd_creative.cgi?aid="+selObj.options[selIndex].value+"&t1=0&t2=1&t3=1";
}
function adv_preview()
{
    var selObj = document.getElementById('adv_id');
    var selIndex = selObj.selectedIndex;
    url="http://mailingtool.routename.com:83/newcgi-bin/adv_preview.cgi?aid="+selObj.options[selIndex].value;
    window.open(url,'Preview','width=850,height=650,toolbar=no,location=no,scrollbars=yes,resizable=no');
}
</script>
</head>

<body>
<div id="container">
end_of_html
if ($ctype eq "H")
{
print<<"end_of_html";
	<h1>Build a History Building Campaign</h1>
	<h2><a href="sm2_list.cgi?type=H" target=_top>view all history campaigns</a> | <a href="sm2_history_schedule.cgi" target=_top>view the scheduler</a> | <a href="mainmenu.cgi" target=_top>go home</a></h2>
end_of_html
}
else
{
print<<"end_of_html";
	<h1>Build a StrongMail Test Campaign</h1>
	<h2><a href="sm2_list.cgi" target=_top>view/deploy tests</a> | <a href="sm2_list.cgi?type=D" target=_top>view deployed campaigns</a> | <a href="mainmenu.cgi" target=_top>go home</a></h2>
end_of_html
}
print<<"end_of_html";

	<div id="form">
	<form method=post name="campform" id="campform" action="sm2_build_test_save.cgi" ENCTYPE="multipart/form-data" target=_top>
	<input type=hidden name=tid value="$testid">
	<input type=hidden name=type value="$ctype">

		<table>
		  <tr>
			<td class="label">File of Email Address(es):</td><td><input type=file name=emailfile id=emailfile></td></tr>
		  <tr>
			<td class="label">Email Address(es):</td>
end_of_html
if ($ctype eq "H")
{
	print "<td class=\"field\"><textarea name=\"email\" cols=80 rows=5>$email_addr</textarea><br><input type=checkbox value=Y name=updall>Update All History Campaigns</td>\n"
}
else
{
	print "<td class=\"field\"><textarea name=\"email\" cols=80 rows=5>$email_addr</textarea></td>\n";
}
print<<"end_of_html";
		  </tr>
		  <tr>
			<td class="label">Number of Copies to Send:</td>
			<td class="field"><input class="field" id="" type="" name="copies" value="$copies_to_send" maxlength="3" size="1" /></td>
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
            <td class="label">Batch Size(set to zero to disable):</td>
            <td class="field">
            <input class="field" id="" type="" name="batchSize" value="$batchSize" size="10" /></td>
            </tr>
          <tr>
            <td class="label">Wait Time(set to zero to disable):</td>
            <td class="field">
            <input class="field" id="" type="" name="waitTime" value="$waitTime" size="10" /></td>
            </tr>
end_of_html
if ($notexternalUser)
{
print<<"end_of_html";
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
			<td class="label"><br />Injector to use:</td>
<td><br /><select name=injectorID><option value=0>Random</option>
end_of_html
my ($errors, $results);
my $params;
my $serverInterface     = Lib::Database::Perl::Interface::Server->new();
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
			<td class="label">Mailing Domain:</td>
			<td class="field">
				<select class="field" id=domainid name=domainid>
					<option value="ALL">ROTATE ALL</option>
end_of_html
$sql="select domainName from DomainProxyGroup dpg, Domain d where dpg.domainID=d.domainID and dpg.proxyGroupID=$proxyGroupID order by domainName";
$sth = $dbhq->prepare($sql) ;
$sth->execute();
my $domain;
while (($domain) = $sth->fetchrow_array())
{
	if ($domain eq $mailing_domain)
	{
		print "<option selected value=\"$domain\">$domain</option>\n";
	}
	else
	{
		print "<option value=\"$domain\">$domain</option>\n";
	}
}
$sth->finish();
print<<"end_of_html";
				</select>
			</td>
		  </tr>
end_of_html
}
print<<"end_of_html";
		  <tr>
			<td class="label"><br />Return Path(<em class="note">optional</em>):</td>
			<td class="field"><br /><input class="field" id="" type="" name="return_path" value="$return_path" size="50" /></td>
		  </tr>
		  <tr>
			<td class="label"><br />Content Domain(<em class="note">optional</em>):</td>
			<td class="field"><br /><input class="field" id="" type="" name="content_domain" value="$content_domain" size="50" /></td>
		  </tr>
          <tr>
            <td class="label">Use Rdns:</td>
end_of_html
if ($useRdns eq "Y")
{
print<<"end_of_html";
            <td class="field"><input class="radio" type="radio" value="Y" checked name="useRdns" />Yes <input class="radio" type="radio" value="N" name="useRdns" />No</td>
end_of_html
}
else
{
print<<"end_of_html";
            <td class="field"><input class="radio" type="radio" value="Y" name="useRdns" />Yes <input class="radio" type="radio" checked value="N" name="useRdns" />No</td>
end_of_html
}
my $mdomain="";
if ($testid > 0)
{
    my $tdomain;
    $sql="select mailing_domain from SendAllTestDomain where test_id=$testid";
    $sth=$dbhu->prepare($sql);
    $sth->execute();
    while (($tdomain)=$sth->fetchrow_array())
    {
        $mdomain=$mdomain.$tdomain."\n";
    }
    chop($mdomain);
    $sth->finish();
}
print<<"end_of_html";
        </tr>
		  <tr>
			<td class="label">Manual Mailing Domains:</td>
			<td class="field"><textarea name=mdomain rows=10 cols=50>$mdomain</textarea></td>
			</tr>
		  <tr>
			<td class="label">IP Group:</td>
			<td class="field"><select name=ipgroup_id><option value=0>None</option>
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
my $mip="";
if ($testid > 0)
{
    my $tdomain;
    $sql="select mailing_ip from test_campaign_ip where test_id=$testid";
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
		</td></tr>
		  <tr>
			<td class="label">Manual Mailing IPs:</td>
			<td class="field"><textarea name=mip rows=10 cols=50>$mip</textarea></td>
			</tr>
end_of_html
if ($notexternalUser)
{
print<<"end_of_html";
		  <tr>
			<td class="label">Use Test Binding:</td>
end_of_html
if ($use_test eq "Y")
{
	print "<td class=\"field\"><input type=radio value=Y checked name=use_test>Yes&nbsp;&nbsp;<input type=radio value=N name=use_test>No</td>\n";
}
else
{
	print "<td class=\"field\"><input type=radio value=Y name=use_test>Yes&nbsp;&nbsp;<input type=radio checked value=N name=use_test>No</td>\n";
}
print<<"end_of_html";
		  </tr>
		  <tr>
			<td class="label">Continuous Send:</td>
end_of_html
if ($continuous_flag eq "Y")
{
	print "<td class=\"field\"><input type=radio value=Y checked name=continuous_flag>Yes&nbsp;&nbsp;<input type=radio value=N name=continuous_flag>No</td>\n";
}
else
{
	print "<td class=\"field\"><input type=radio value=Y name=continuous_flag>Yes&nbsp;&nbsp;<input type=radio checked value=N name=continuous_flag>No</td>\n";
}
print<<"end_of_html";
		  </tr>
end_of_html
}
else
{
	print "<input type=hidden name=use_test value=Y>\n";
	print "<input type=hidden name=continuous_flag value=N>\n";
}
print<<"end_of_html";
		  <tr>
			<td class="label">Email Header Template:</td>
			<td class="field">
				<select class="field" id=mailingHeaderID name=mailingHeaderID size=5 multiple=multiple>
<option value=0>ROTATE ALL</option>
end_of_html
$sql="select templateID, templateName from mailingHeaderTemplate WHERE $userDataRestrictionWhereClause status='A' order by templateName ";
$sth = $dbhq->prepare($sql) ;
$sth->execute();
my ($templateID,$templateName);
while (($templateID,$templateName) = $sth->fetchrow_array())
{
	my $tcnt=0;
	if ($testid > 0)
	{
		$sql="select count(*) from test_campaign_headers where test_id=? and mailingHeaderID=?";
		my $sthq=$dbhu->prepare($sql);
		$sthq->execute($testid,$templateID);
		($tcnt)=$sthq->fetchrow_array();
		$sthq->finish();
	}
	if ($tcnt > 0)
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
<option value=0>ROTATE ALL</option>
end_of_html
$sql="select wikiID, templateName from wikiTemplate where $userDataRestrictionWhereClause status = 'A'";
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
				<input type="button" value="add new template" onClick="add_wikiTemplate();" target="_blank"/>
			</td>
		  </tr>
		  <tr>
			<td class="label"><br />Test Campaign Name (<em class="note">optional</em>):</td>
			<td class="field"><br /><input class="field" id="" type="" name="cname" value="$campaign_name" size="50" /></td>
		  </tr>
		  <tr>
			<td class="label">Advertiser: (<a href="" class="note"><em><a href="javascript:edit_advertiser();">edit selected</em></a>)</td>
			<td class="field">
				<select class="field" name="adv_id" id="adv_id" onChange="upd_creative();">
end_of_html
#$sql="select advertiser_id,advertiser_name from advertiser_info where status='A' and test_flag='Y' order by advertiser_name";
$sql="select advertiser_id,advertiser_name from advertiser_info where status='A' order by advertiser_name";
$sth=$dbhq->prepare($sql);
$sth->execute();
my $aid;
my $aname;
while (($aid,$aname)=$sth->fetchrow_array())
{
	if ($adv_id == 0)
	{
		$adv_id=$aid;
	}
	if (!$notexternalUser)
	{
		$aname=$aid;
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
            <td class="label">Unique Schedule Advertiser:</td>
            <td>
                <select name="cusa" id="cusa" size=1><option value=0>Select One</option>
end_of_html
    $sql="select usa.usa_id,usa.name from UniqueScheduleAdvertiser usa,advertiser_info ai where usa.advertiser_id=ai.advertiser_id and ai.status!='I' and ai.advertiser_id=$adv_id order by usa.name";
    my $sth1=$dbhu->prepare($sql);
    $sth1->execute();
	my $usaid;
	my $uname;
    while (($usaid,$uname)=$sth1->fetchrow_array())
    {
        print "<option value=$usaid>$uname</option>";
    }
	$sth1->finish();
print<<"end_of_html";
                </select>
            </td>
          </tr>
		  <tr>
			<td class="label">Creative:</td>
			<td class="field">
				<select class="field" name="creative" id="creative" size=5 multiple=multiple>
<option value=0>ROTATE ALL</option>
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
	if ($testid > 0)
	{
		$sql="select count(*) from test_campaign_creatives where test_id=? and creative_id=?";
		my $sth1=$dbhu->prepare($sql);
		$sth1->execute($testid,$cid);
		($cnt)=$sth1->fetchrow_array();
		$sth1->finish();
	}
	if ($cnt > 0)
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
				<input type="button" value="View all creatives" onClick="adv_preview();" />
			</td>
		  </tr>
		  <tr>
			<td class="label">Manual Creative:</td>
			<td class="field"><textarea cols=50 rows=10 name=mcreative>$fcode</textarea></td>
		 </tr>
		  <tr>
			<td class="label">Subject:</td>
			<td class="field">
				<select class="field" name="csubject" id="csubject" size=5 multiple=multiple>
<option value=999999999>ROTATE ALL</option>
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
	if ($testid > 0)
	{
		$sql="select count(*) from test_campaign_subjects where test_id=? and subject_id=?";
		my $sth1=$dbhu->prepare($sql);
		$sth1->execute($testid,$sid);
		($cnt)=$sth1->fetchrow_array();
		$sth1->finish();
	}
	if ($cnt > 0)
	{
		print "<option selected value=$sid>$temp_str</option>\n";
	}
	else
	{
		print "<option value=$sid>$temp_str</option>\n";
	}
}
$sth->finish();
my $subjchecked="";
if ($base64EncodeSubject eq "Y")
{
	$subjchecked="checked";
}	
print<<"end_of_html";
				</select>&nbsp;&nbsp;<input type=checkbox name=base64EncodeSubject value="Y" $subjchecked>Base64 Encode&nbsp;&nbsp;Encoding: <select name=subjectEncoding>
end_of_html
my $i=0;
while ($i <= $#ENC)
{
	if ($ENC1[$i] eq $subjectEncoding)
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
			</select></td>
		  </tr>
		  <tr>
			<td class="label">Manual Subject:</td>
			<td class="field"><input type=text size=50 maxlength=50 name=subject value="$subject"></td>
		  </tr>
		  <tr>
			<td class="label">From Line:</td>
			<td class="field">
				<select class="field" name="cfrom" id="cfrom" size=5 multiple=multiple>
<option value=999999999>ROTATE ALL</option>
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
	if ($testid > 0)
	{
		$sql="select count(*) from test_campaign_froms where test_id=? and from_id=?";
		my $sth1=$dbhu->prepare($sql);
		$sth1->execute($testid,$sid);
		($cnt)=$sth1->fetchrow_array();
		$sth1->finish();
	}
	if ($cnt > 0)
	{
		print "<option selected value=$sid>$temp_str</option>\n";
	}
	else
	{
		print "<option value=$sid>$temp_str</option>\n";
	}
}
$sth->finish();
my $fromchecked="";
if ($base64EncodeFrom eq "Y")
{
    $fromchecked="checked";
}
print<<"end_of_html";
				</select>&nbsp;&nbsp;<input type=checkbox name=base64EncodeFrom value="Y" $fromchecked>Base64 Encode&nbsp;&nbsp;Encoding: <select name=fromEncoding>
end_of_html
my $i=0;
while ($i <= $#ENC)
{
	if ($ENC1[$i] eq $fromEncoding)
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
			</select></td>
		  </tr>
		  <tr>
			<td class="label">Manual From:</td>
			<td class="field"><input type=text size=50 maxlength=50 name=fromline value="$fromline"></td>
		</tr>
		  <tr>
			<td class="label"><br />Mail From:</td>
			<td class="field"><textarea name="mail_from" cols=80 rows=5>$mail_from</textarea></td>
		  </tr>
		  <tr>
			<td class="label"><br />Mail From 2(<em class="note">optional</em>):</td>
			<td class="field"><br /><input class="field" id="" type="" name="mail_from2" value="$mail_from2" size="50" /></td>
		  </tr>
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
			<td class="label"><br />Mailing Template:</td>
			<td class="field"><br />
				<select class="field" name="template_id" id="template_id" multiple=multiple size=10>
<option value=0>ROTATE ALL</option>
end_of_html
$sql="select template_id,template_name from brand_template where $userDataRestrictionWhereClause status='A' order by template_id";
$sth=$dbhu->prepare($sql);
$sth->execute();
my $tid;
my $tname;
while (($tid,$tname)=$sth->fetchrow_array())
{
	if ($testid > 0)
	{
		$sql="select count(*) from test_campaign_templates where test_id=? and mailing_template=?";
		my $sth1=$dbhu->prepare($sql);
		$sth1->execute($testid,$tid);
		($cnt)=$sth1->fetchrow_array();
		$sth1->finish();
	}
	if ($cnt > 0)
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
end_of_html
if ($notexternalUser)
{
print<<"end_of_html";
		  <tr>
			<td class="label"><br />Article:</td>
			<td class="field"><br />
				<select class="field" name="article_id" id="article_id">
end_of_html
$sql="select article_id,article_name from article where status='A' order by article_name";
$sth=$dbhu->prepare($sql);
$sth->execute();
my $tid;
my $tname;
while (($tid,$tname)=$sth->fetchrow_array())
{
	if ($tid == $article_id)
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
			</td>
		  </tr>
end_of_html
}
if ($notexternalUser)
{
print<<"end_of_html";
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
end_of_html
}
print<<"end_of_html";
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
end_of_html
if ($notexternalUser)
{
print<<"end_of_html";
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
print "</tr><tr><td class=label>Include Mail To?</td>";
if ($include_mailto eq "Y")
{
print<<"end_of_html";
			<td class="field"><input class="radio" type="radio" value="Y" checked name="include_mailto" />Yes <input class="radio" type="radio" value="N" name="include_mailto" />No</td>
end_of_html
}
else
{
print<<"end_of_html";
			<td class="field"><input class="radio" type="radio" value="Y" name="include_mailto" />Yes <input class="radio" type="radio" checked value="N" name="include_mailto" />No</td>
end_of_html
}
print "</tr><tr><td class=label>Cut Mail?</td>";
if ($CutMail eq "Y")
{
print<<"end_of_html";
			<td class="field"><input class="radio" type="radio" value="Y" checked name="CutMail" />Yes <input class="radio" type="radio" value="N" name="CutMail" />No</td>
end_of_html
}
else
{
print<<"end_of_html";
			<td class="field"><input class="radio" type="radio" value="Y" name="CutMail" />Yes <input class="radio" type="radio" checked value="N" name="CutMail" />No</td>
end_of_html
}
print<<"end_of_html";
</tr><tr><td>&nbsp;</td></tr>
		<tr><td colspan=2 align=center><a href="JavaScript:popup_validate();">Display Validate URL</a></td></tr>
end_of_html
}
else
{
	print "<input type=hidden name=include_open value=Y>\n";
	print "<input type=hidden name=encrypt_link value=Y>\n";
	print "<input type=hidden name=newMailing value=N>\n";
	print "<input type=hidden name=CutMail value=N>\n";
	print "<input type=hidden name=include_mailto value=N>\n";
}
print<<"end_of_html";
		</table>

		<div class="submit">
end_of_html
if ($notexternalUser)
{
			print "<input class=\"submit\" type=\"submit\" name=\"submit\" value=\"render html\" />\n";
}
print<<"end_of_html";
			<input class="submit" type="submit" name="submit" value="preview it" />
			<input class="submit" type="submit" name="submit" value="save it" />
			<input class="submit" type="submit" name="submit" value="send it" />
			<input class="submit" type="submit" name="submit" value="send it and keep testing" />
end_of_html
if ($testid > 0)
{
print<<"end_of_html";
			<input class="submit" type="submit" name="submit" value="save as new" />
end_of_html
}
print<<"end_of_html";
		</div>
	</div>

</div>
</form>
</body>
</html>
end_of_html
