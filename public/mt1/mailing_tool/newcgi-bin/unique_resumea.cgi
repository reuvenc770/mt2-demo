#!/usr/bin/perl
#===============================================================================
# Name   : unique_resume.cgi - Resume a Unique Campaign 
#
#--Change Control---------------------------------------------------------------
#===============================================================================

#-----  include Perl Modules ---------
use strict;
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
my $jlogProfileID;
my ($cname,$cemail,$old_nl_id,$old_mta_id,$domainid,$ip,$aid,$creative_id,$subject_id,$from_id,$old_template_id,$include_wiki,$include_name,$random_subdomain,$profile_id);
my $cdomainid;
my $header_id;
my $footer_id;
my $group_id;
my $client_group_id;
my $dupes_flag;
my $dup_client_group_id;
my $oldtid;
my $sdate1;
my $shour;
my $pid;
my $exclude_domain;
my $pastedomain;
my $convert_subject;
my $convert_from;
my $cstr;
my $cstrf;
my $mail_from;
my @ENC=("None","ISO","UTF8-Q","UTF8-B","UTF-7","ASCII","UTF-16","UTF-8 HOT","ISO B","UTF-8Q HOT","UTF-7 HOT","ASCII HOT","UTF-16 HOT","ISO B HOT","ISO Q HOT");
my @ENC1=("","ISO","UTF8","UTF8-B","UTF-7","ASCII","UTF-16","UTF-8 HOT","ISO B","UTF-8Q HOT","UTF-7 HOT","ASCII HOT","UTF-16 HOT","ISO B HOT","ISO Q HOT");
my $uid=$query->param('uid');
#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}
#------  connect to the util database -----------
my ($dbhq,$dbhu)=$util->get_dbh();
$sql="select campaign_name,nl_id,mailing_domain,mailing_ip,mailing_template,group_id,profile_id,exclude_domain,pasted_domains,advertiser_id,mta_id,ConvertSubject,ConvertFrom,mail_from,jlogProfileID from unique_campaign where unq_id=$uid";
$sth=$dbhu->prepare($sql);
$sth->execute();
($cname,$old_nl_id,$domainid,$ip,$old_template_id,$group_id,$pid,$exclude_domain,$pastedomain,$adv_id,$old_mta_id,$convert_subject,$convert_from,$mail_from,$jlogProfileID)=$sth->fetchrow_array();
$sth->finish();
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
<title>Resume a Uniques Campaign</title>

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
function update_domain(t1)
{
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
    parent.frames[1].location="/newcgi-bin/unique_upd_domain.cgi?nid=$old_nl_id&t1="+t1+"&uid=$uid";
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
    	parent.frames[1].location="/newcgi-bin/sm2_upd_creative.cgi?aid="+selObj.options[selIndex].value+"&t1=1&uid=$uid&all=1&t2=1&t3=1";
	}
	else
	{
    	parent.frames[1].location="/newcgi-bin/sm2_upd_creative.cgi?aid="+selObj.options[selIndex].value+"&t1=0&t2=1&t3=1";
	}
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
</head>

<body>
<div id="container">

	<h1>Resume a Uniques Campaign</h1>
	<h2><a href="unique_list.cgi" target=_top>view/edit/deploy uniques campaigns</a> | <a href="unique_deploy_list.cgi" target=_top>deployed unique campaigns</a> | <a href="/cgi-bin/mainmenu.cgi" target=_top>go home</a></h2>

	<div id="form">
	<form name="campform" method=post action="/cgi-bin/unique_resume_save.cgi" target=_top>
	<input type=hidden name=uid value=$uid>
		<table>
		  <tr>
			<td class="label">Uniques Campaign Name:</td>
			<td>$cname</td>
		  </tr>
		  <tr>
			<td class="label">Mailing Domain:</td>
			<td>
				<select name=domainid id=domainid multiple="multiple" size=5>
				</select>&nbsp;&nbsp;<textarea name=pastedomain id=pastedomain rows=5 columns=50>$pastedomain</textarea>
			</td>
		  </tr>
		  <tr>
			<td class="label">Content Domain:</td>
			<td>
				<select name=cdomainid id=cdomainid multiple="multiple" size=5>
				</select>&nbsp;&nbsp;<textarea name=cpastedomain id=cpastedomain rows=5 columns=50>$pastedomain</textarea>
			</td>
		  </tr>
		  <tr>
			<td class="label">IP Group:</td>
			<td>
				<select name=group_id id=group_id>
end_of_html
$sql="select group_id,group_name from IpGroup where status='Active' order by group_name";
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
				</select>
			</td>
		  </tr>
		  <tr>
			<td class="label">Mailing Template:</td>
			<td>
				<select name="template_id">
<option value=0 checked>--SELECT ONE--</option>
end_of_html
$sql="select template_id,template_name from brand_template where status='A' order by template_name";
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
<!--				<input type="button" value="edit" onClick="edit_template();"/>
				<input type="button" value="add new template" onClick="add_template();"/> -->
			</td>
		  </tr>
			<tr><td class="label">Domains To Exclude:</td>
			<td><input type=text name=exclude_domains value='$exclude_domain' size=80></td></tr>
		  <tr>
			<td class="label"><br />ISPs to Exclude:</td>
			<td class="field"><br />
end_of_html
$sql="select class_id,class_name from email_class where status='Active' and class_id in (select class_id from UniqueProfileIsp where profile_id=$pid) order by class_name";
$sth=$dbhq->prepare($sql);
$sth->execute();
my $cid;
my $cname;
my $classid;
while (($cid,$cname)=$sth->fetchrow_array())
{
	$sql="select class_id from UniqueExcludeClass where unq_id=? and class_id=?";
	my $sth1=$dbhu->prepare($sql);
	$sth1->execute($uid,$cid);
	($classid)=$sth1->fetchrow_array();
	$sth1->finish();
	if ($cid == $classid)
	{
		print "<input class=radio type=checkbox checked name=isps value=$cid />$cname/\n";
	}		
	else
	{
		print "<input class=radio type=checkbox name=isps value=$cid />$cname/\n";
	}		
}
$sth->finish();
print<<"end_of_html";
			</td>
		  </tr>
		  </tr>
			<tr><td class="label">Mail From:</td>
			<td><input type=text name=mail_from value='$mail_from' size=80></td></tr>
		  <tr>
            <td class="label"><br />Advertiser:</td>
            <td class="field">
                <br /><select class="field" name="adv_id" id="adv_id" onChange="upd_creative(1);">
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
				</select>&nbsp;&nbsp;<input type="button" value="Refresh" onClick="upd_creative(1);"/>
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
			</td>
		  </tr>
		  <tr>
			<td class="label">From Line:</td>
			<td>
				<select name="cfrom" id="cfrom" size=5 multiple=multiple>
				</select>&nbsp;&nbsp;&nbsp;&nbsp;Encoding: <select name=ConvertFrom>
end_of_html
my $i=0;
while ($i <= $#ENC)
{
	if ($ENC1[$i] eq $convert_from)
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
			<td class="label">Subject:</td>
			<td>
				<select name="csubject" id="csubject" multiple=multiple size=5>
				</select>&nbsp;&nbsp;Encoding: <select name=ConvertSubject>
end_of_html
my $i=0;
while ($i <= $#ENC)
{
	if ($ENC1[$i] eq $convert_subject)
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
			<td class="label">MTA Settings Profile:</td>
			<td>
				<select name=mta_id>
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
			</td>
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
		</table>

		<div class="submit">
			<input class="submit" type="submit" name="submit" value="resume" />
		</div>
	</div>

</div>
</form>
<script language="JavaScript">
    update_domain(1);
</script>
</body>
</html>
end_of_html
