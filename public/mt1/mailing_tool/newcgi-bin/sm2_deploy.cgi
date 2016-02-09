#!/usr/bin/perl
#===============================================================================
# Name   : sm2_deploy.cgi - Screen to deploy a test campaign 
#
#--Change Control---------------------------------------------------------------
# 08/01/07  Jim Sobeck  Creation
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
my $cid;
my $company;
my $copywriter;
my $fname;
my ($email_addr,$copies_to_send,$client_id,$brand_id,$mailing_domain,$mailing_ip,$campaign_name,$adv_id,$creative_id,$subject_id,$from_id,$mailing_template,$include_wiki);

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}
#------  connect to the util database -----------
my ($dbhq,$dbhu)=$util->get_dbh();
my $testid=$query->param('tid');
if ($testid > 0)
{
	$sql="select email_addr,copies_to_send,client_id,brand_id,mailing_domain,mailing_ip,campaign_name,advertiser_id,creative_id,subject_id,from_id,mailing_template,include_wiki from test_campaign where test_id=$testid";
	$sth=$dbhu->prepare($sql);
	$sth->execute();
	($email_addr,$copies_to_send,$client_id,$brand_id,$mailing_domain,$mailing_ip,$campaign_name,$adv_id,$creative_id,$subject_id,$from_id,$mailing_template,$include_wiki)=$sth->fetchrow_array();
	$sth->finish();
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
function update_profile()
{
    var selObj = document.getElementById('clientid');
    var selIndex = selObj.selectedIndex;
    var selLength = campform.profile_id.length;
    while (selLength>0)
    {
        campform.profile_id.remove(selLength-1);
        selLength--;
    }
    campform.profile_id.length=0;
    parent.frames[1].location="/newcgi-bin/sm2_upd_profile.cgi?cid="+selObj.options[selIndex].value;
}
function addProfile(value,text)
{
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    campform.profile_id.add(newOpt);
}
</script>

</head>

<body>
<form method=post name="campform" action="sm2_function.cgi" target=_top>
<input type=hidden name=tid value="$testid">
<div id="container">

	<h1>Deploy a StrongMail Test Campaign</h1>
	<h2><a href="/sm2_build_test.html" target=_top>build a new test</a> | <a href="/newcgi-bin/sm2_list.cgi" target=_top>go home</a></h2>

	<div id="form">
		<table>
		  <tr>
			<td class="label">Recipient Email Address(es):</td>
			<td class="field"><strong>$email_addr</strong></td>
		  </tr>
		  <tr>
			<td class="label">Number of Copies Sent:</td>
			<td class="field"><strong>$copies_to_send</strong></td>
		  </tr>
		  <tr>
			<td class="label"><br />Client:</td>
end_of_html
#
#	Get the information about the client for the campaign
#
my $company;
my $fname;
$sql="select company,first_name from user where user_id=$client_id";
$sth=$dbhq->prepare($sql);
$sth->execute();
($company,$fname)=$sth->fetchrow_array();
$sth->finish();
print<<"end_of_html";
			<td class="field"><br /><strong>$company ($fname)</strong></td>
		  </tr>
		  <tr>
			<td class="label">Brand:</td>
end_of_html
#
#	Get the information about the brand for the campaign
#
my $bname;
$sql="select brand_name from client_brand_info where brand_id=?";
$sth=$dbhq->prepare($sql);
$sth->execute($brand_id);
($bname)=$sth->fetchrow_array();
$sth->finish();
print<<"end_of_html";
			<td class="field"><strong>$bname ($brand_id)</strong></td>
		  </tr>
		  <tr>
			<td class="label">Mailing Domain:</td>
			<td class="field">
end_of_html
#
#	Get the information about the mailing domain(s) for the campaign
#
if ($mailing_domain eq "ALL")
{
	my $domain;
	$sql="select distinct domain from brand_available_domains where brandID=? union select distinct url from brand_url_info where brand_id=? and url_type in ('O','Y') order by 1";
	$sth=$dbhq->prepare($sql);
	$sth->execute($brand_id,$brand_id);
	print "<strong>";
	while (($domain)=$sth->fetchrow_array())
	{
		print "$domain<br>\n";
	}
	$sth->finish();
}
else
{
	print "<strong>$mailing_domain\n";
}
print<<"end_of_html";
		</strong></td>
		  </tr>
		  <tr>
			<td class="label">Mailing IP(s):</td>
			<td class="field">
				<strong>
end_of_html
if ($mailing_ip eq "ALL")
{
	my $ip;
	$sql="select ip from brand_ip where brandId=?"; 
	$sth=$dbhq->prepare($sql);
	$sth->execute($brand_id);
	while (($ip)=$sth->fetchrow_array())
	{
		print "$ip<br>\n";
	}
	$sth->finish();
}
else
{
	print "$mailing_ip\n";
}
print<<"end_of_html";
				</strong>
			</td>
		  </tr>
		  <tr>
			<td class="label"><br />Test Campaign Name:</td>
			<td class="field"><br /><strong>$campaign_name</strong></td>
		  </tr>
		  <tr>
			<td class="label">Advertiser:</td>
end_of_html
my $aname;
$sql="select advertiser_name from advertiser_info where advertiser_id=?";
$sth=$dbhq->prepare($sql);
$sth->execute($adv_id);
($aname)=$sth->fetchrow_array();
$sth->finish();
print<<"end_of_html";
			<td class="field"><strong>$aname</strong></td>
		  </tr>
		  <tr>
			<td class="label">Creative:</td>
end_of_html
$sql = "select creative_id, creative_name,original_flag, trigger_flag, approved_flag,mediactivate_flag,copywriter from creative where creative_id=?"; 
$sth = $dbhq->prepare($sql);
$sth->execute($creative_id);
my $cid;
my $cname;
my $oflag;
my $tflag;
my $aflag;
my $mflag;
my $temp_str;
($cid,$cname,$oflag,$tflag,$aflag,$mflag,$copywriter) = $sth->fetchrow_array();
$sth->finish();
    $temp_str = $cname . " (";
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
print<<"end_of_html";
			<td class="field"><strong>$temp_str</strong></td>
		  </tr>
		  <tr>
			<td class="label">Subject:</td>
end_of_html
$sql = "select advertiser_subject,approved_flag,original_flag,copywriter from advertiser_subject where subject_id=?"; 
$sth = $dbhq->prepare($sql);
$sth->execute($subject_id);
my $sname;
my $oflag;
my $tflag;
my $aflag;
my $mflag;
my $temp_str;
($sname,$oflag,$tflag,$aflag,$mflag,$copywriter) = $sth->fetchrow_array();
$sth->finish();
    $temp_str = $sname . " (";
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
print<<"end_of_html";
			<td class="field"><strong>$temp_str</strong></td>
		  </tr>
		  <tr>
			<td class="label">From Line:</td>
end_of_html
$sql = "select advertiser_from,approved_flag,original_flag,copywriter from advertiser_from where from_id=?"; 
$sth = $dbhq->prepare($sql);
$sth->execute($from_id);
my $fromname;
my $oflag;
my $tflag;
my $aflag;
my $mflag;
my $temp_str;
($fromname,$oflag,$tflag,$aflag,$mflag,$copywriter) = $sth->fetchrow_array();
$sth->finish();
    $temp_str = $fromname . " (";
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
print<<"end_of_html";
			<td class="field"><strong>$temp_str</strong></td>
		  </tr>
		  <tr>
			<td class="label"><br />Mailing Template:</td>
end_of_html
my $template_name;
$sql="select template_name from brand_template where template_id=?";
$sth=$dbhq->prepare($sql);
$sth->execute($mailing_template);
($template_name)=$sth->fetchrow_array();
$sth->finish();
print<<"end_of_html";
			<td class="field"><br /><strong>$template_name</strong></td>
		  </tr>
		  <tr>
			<td class="label">Include Wiki?</td>
	<td class="field"><strong>
end_of_html
if ($include_wiki eq "Y")
{
	print "Yes";
}
else
{
	print "No";
}
print<<"end_of_html";
</strong></td>
		  </tr>
		  <tr>
			<td class="label"><br />Mta Setting:</td>
			<td class="field"><br />
				<select class="field" name="mta_id">
end_of_html
my $mid;
my $mname;
$sql="select mta_id,name from mta order by name"; 
$sth=$dbhq->prepare($sql);
$sth->execute();
while (($mid,$mname)=$sth->fetchrow_array())
{
	print "<option value=$mid>$mname</option>\n";
}
$sth->finish(); 
print<<"end_of_html";
				</select>
			</td>
		  </tr>
		  <tr>
			<td class="label"><br />Client:</td>
			<td class="field"><br />
				<select class="field" name=clientid id=clientid onChange="update_profile();">
end_of_html
$sql="select user_id,company,first_name from user where status='A' order by company";
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($cid,$company,$fname) = $sth->fetchrow_array())
{
	if ($client_id == $cid)
	{
		print "<option selected value=$cid>$company ($fname)</option>\n";
	}
	else
	{
		print "<option value=$cid>$company ($fname)</option>\n";
	}
}
$sth->finish();
print<<"end_of_html";
				</select>
			</td>
		  </tr>
		  <tr>
			<td class="label"><br />Choose a Profile to Deploy to:</td>
			<td class="field"><br />
				<select class="field" name="profile_id">
end_of_html
my $pid;
my $pname;
$sql="select profile_id,profile_name from list_profile where status='A' and third_party_id=10 and profile_type='3RDPARTY' and client_id=$client_id order by profile_name";
$sth=$dbhq->prepare($sql);
$sth->execute();
while (($pid,$pname)=$sth->fetchrow_array())
{
	print "<option value=$pid>$pname</option>\n";
}
$sth->finish(); 
print<<"end_of_html";
				</select>
			</td>
		  </tr>
		</table>

		<div class="submit">
			<input class="submit" type="submit" name="submit" value="preview" />
			<input class="submit" type="submit" name="submit" value="edit it" />
			<input class="submit" type="submit" name="submit" value="deploy it" />
		</div>
	</div>

</div>
</form>
</body>
</html>
end_of_html
