#!/usr/bin/perl
#===============================================================================
# Name   : dailydeal_list.cgi - Displays DailyDeals/Trigger campaigns 
#
#--Change Control---------------------------------------------------------------
# 12/20/11  Jim Sobeck  Creation
#===============================================================================

#-----  include Perl Modules ---------
use strict;
use CGI;
use util;
use Lib::Database::Perl::Interface::Server;

#------  get some objects to use later ---------
my $util = util->new;
my $filter;
my $mta;
my $ip_group_id;
my $query = CGI->new;
my $ctype=$query->param('ctype');
if ($ctype eq "")
{
	$ctype="Daily Deal";
}
my $sord=$query->param('sord');
if ($sord eq "")
{
	$sord="u.username";
}
my $ipgroup=$query->param('ipgroup');
if ($ipgroup eq "")
{
	$ipgroup=0;
}
my $in_template=$query->param('template_id');
if ($in_template eq "")
{
	$in_template=0;
}
my $incname=$query->param('incname');
my $inaid=$query->param('inaid');
if ($inaid eq "")
{
	$inaid=0;
}
my $cmta=$query->param('cmta');
if ($cmta eq "")
{
	$cmta=0;
}
my $sql;
my $sth;
my $dbh;
my $IPGRPMTA;

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}
#------  connect to the util database -----------
my ($dbhq,$dbhu)=$util->get_dbh();
#$dbhq = DBI->connect("DBI:mysql:new_mail:slavedb.i.routename.com", "db_readuser", "Tr33Wat3r");
#$ENV{'DATABASE_HOST'}="slavedb.i.routename.com";
#$ENV{'DATABASE_USER'}="db_readuser";
#$ENV{'DATABASE_PASSWORD'}="Tr33Wat3r";
$ENV{'DATABASE_HOST'}="masterdb.i.routename.com";
$ENV{'DATABASE_USER'}="db_user";
$ENV{'DATABASE_PASSWORD'}="sp1r3V";
$ENV{'DATABASE'}="new_mail";
my $serverInterface     = Lib::Database::Perl::Interface::Server->new();
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
<title>$ctype Campaigns</title>

<style type="text/css">

body {
	background: url(http://www.affiliateimages.com/temp/bg.jpg) top center repeat-x #99D1F4;
	font-family: "Trebuchet MS", Tahoma, Arial, sans-serif;
	font-size: .7em;
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
	width: 100%;
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
</head>

<body>
<div id="container">

	<h1>List of $ctype Campaigns</h1>
	<h2><a href="/newcgi-bin/unique_deploy_list.cgi">deployed unique campaigns</a> | <a href="/newcgi-bin/unique_deploy_list.cgi?gsm=2">Hotmail deploys</a> | <a href="/newcgi-bin/unique_deploy_list.cgi?gsm=4">Time Based</a> | <a href="/newcgi-bin/unique_deploy_list.cgi?gsm=5">Hotmail Domain Deploys</a> | <a href="/newcgi-bin/dailydeal_list.cgi">Daily Deals</a> | <a href="/newcgi-bin/trigger_display_list.cgi">Triggers</a> | <a href="/newcgi-bin/mainmenu.cgi" target=_top>go home</a></h2>
<center>
<form method=post action=dailydeal_list.cgi>
<input type=hidden name=sord value="$sord">
<font size=+1><b>IP Group:</b>
<select name=ipgroup><option value=0 selected>ALL</option>
end_of_html
$sql="select group_id,group_name from IpGroup where status='Active' order by group_name";
#$sth=$dbhq1->prepare($sql);
$sth=$dbhq->prepare($sql);
$sth->execute();
my $group_id;
my $gname;
while (($group_id,$gname)=$sth->fetchrow_array())
{
	if ($group_id == $ipgroup)
	{
		print "<option selected value=$group_id>$gname</option>\n";
	}
	else
	{
		print "<option value=$group_id>$gname</option>\n";
	}
}
$sth->finish();
print<<"end_of_html";
</select>
&nbsp;&nbsp;<b>MTA:</b>
<select name=cmta id=cmta><option value=0 selected>ALL</option>
end_of_html
my ($errors, $results);
my $params;
$params->{active}=1;
($errors, $results) = $serverInterface->getMtaServers($params);
my $cnt=$#{$results};
my $i=0;
while ($i <= $cnt)
{
	$group_id=$results->[$i]->{'serverID'};
	$gname=$results->[$i]->{'hostname'};
	$gname=~s/.pcposts.com//;
	$gname=~s/.routename.com//;
	if ($group_id == $cmta)
	{
		print "<option selected value=$group_id>$gname</option>\n";
	}
	else
	{
		print "<option value=$group_id>$gname</option>\n";
	}
   	$i++;
}
print<<"end_of_html";
</select>
Campaign Name:&nbsp;<input type=text name=incname value='$incname' size=30>
&nbsp;&nbsp;<br>Template: <select name=template_id><option value=0>ALL</option>
end_of_html
my $tid;
my $tname;
$sql="select template_id,template_name from brand_template where status='A' order by template_name";
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($tid,$tname)=$sth->fetchrow_array())
{
	if ($tid == $in_template)
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
</select>&nbsp;&nbsp;Advertiser: <select name=inaid><option value=0>--Select One--</option>
end_of_html
$sql="select advertiser_id,advertiser_name from advertiser_info where status='A' and test_flag='N' order by advertiser_name";
$sth=$dbhu->prepare($sql);
$sth->execute();
my $aid;
my $aname;
while (($aid,$aname)=$sth->fetchrow_array())
{
	if ($aid == $inaid)
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
</select><br><input type=submit name=submit value="Filter"></font>
</form>
</center>
	<div id="form">
		<center>
		<table style="border: 1px solid #999; " border=1>
		  <tr>
			<td ><strong>Campaign Name</strong></td>
			<td ><strong>IP Group</strong></td>
			<td ><strong>Domain</strong></td>
			<td ><strong>Template</strong></td>
			<td ><strong>MTA</strong></td>
			<td ><strong>Setting Name</strong></td>
			<td ><strong>Client</strong></td>
			<td ><strong>ISP</strong></td>
			<td ><strong>Day</strong></td>
		  </tr>
end_of_html
$sql="select u.username,dd.cday,c.campaign_id,c.campaign_name,si.mta_id,ddsd.class_id,ddsd.domain,bt.template_name,ip.group_name,ip.group_id,dds.name from schedule_info si,camp_schedule_info csi,campaign c,user u,daily_deals dd,DailyDealSettingDetail ddsd,brand_template bt,IpGroup ip,DailyDealSetting dds  where si.slot_type='D' and si.status='A' and csi.status='A' and si.client_id=csi.client_id and si.slot_id=csi.slot_id and si.slot_type=csi.slot_type and csi.campaign_id=c.campaign_id and si.client_id=u.user_id and csi.campaign_id=dd.campaign_id and csi.client_id=dd.client_id and si.mta_id=ddsd.dd_id and ddsd.template_id=bt.template_id and ddsd.group_id=ip.group_id and ddsd.group_id > 0 and ddsd.dd_id=dds.dd_id";
$filter=0;
if ($ipgroup > 0)
{
	$filter=1;
	$sql=$sql." and ddsd.group_id=$ipgroup ";
}
if ($in_template > 0)
{
	$filter=1;
	$sql=$sql." and ddsd.template_id=$in_template";
}
if (($inaid > 0) and ($inaid ne ""))
{
	$filter=1;
	$sql=$sql." and c.advertiser_id=$inaid";
}
if ($incname ne "")
{
	$filter=1;
	$sql=$sql." and c.campaign_name like '%".$incname."%'";
}
if ($cmta > 0)
{
	$filter=1;
	my $params;
	$params->{'mtaServerID'}=$cmta;
    $params->{'withFailedData'}=0;
    my ($errors, $results) = $serverInterface->getMailingIpsAssigned($params);
	my $cnt=$#{$results};
	my $mtaip=$results->[0]->{'ip'};

	$sql=$sql." and ddsd.group_id in (select distinct igi.group_id from IpGroupIps igi, IpGroup ig where igi.ip_addr='$mtaip' and igi.group_id=ig.group_id and ig.status='Active') ";
}
if ($filter)
{
	my $C;
	my $sql1="select class_id,class_name from email_class where status='Active'";
	my $sth1=$dbhu->prepare($sql1);
	$sth1->execute();
	my $class_id;
	my $class_name;
	while (($class_id,$class_name)=$sth1->fetchrow_array())
	{
		$C->{$class_id}=$class_name;
	}
	$sth1->finish();
$sql=$sql." order by $sord,dd.cday";
$sth=$dbhu->prepare($sql);
$sth->execute();
my ($username,$cday,$campaign_id,$cname,$dd_id,$class_id,$domain,$template_name,$group_name);
my $dds_name;
while (($username,$cday,$campaign_id,$cname,$dd_id,$class_id,$domain,$template_name,$group_name,$ip_group_id,$dds_name)=$sth->fetchrow_array())
{
	if ($C->{$class_id})
	{
		$class_name=$C->{$class_id};
	}
	else
	{
		next;
	}
	if ($IPGRPMTA->{$ip_group_id})
	{
		$mta=$IPGRPMTA->{$ip_group_id};
	}
	else
	{
		my $tip;
    	$sql="select hostname from Server s, ServerIp sic, IpGroupIps igi,IpRole ir_n,IpStatus ist_n  where s.serverID=sic.serverID and sic.ip=igi.ip_addr and igi.group_id=? and ir_n.ipRoleID=sic.ipRoleID and ist_n.ipStatusID=sic.ipStatusID and (ir_n.ipRoleLabel='mailing' or ir_n.ipRoleLabel is NULL) and (ist_n.ipStatusLabel='active' or ist_n.ipStatusLabel IS NULL) limit 1";
    	my $sth1=$dbhu->prepare($sql);
    	$sth1->execute($ip_group_id);
    	($mta)=$sth1->fetchrow_array();
    	$sth1->finish();
    	$mta=~s/.pcposts.com//;
    	$mta=~s/.routename.com//;
		$IPGRPMTA->{$ip_group_id}=$mta;
	}
	print "<tr><td>$cname</td><td>$group_name</td><td>$domain</td><td>$template_name</td><td>$mta</td><td>$dds_name</td><td>$username</td><td>$class_name</td><td>$cday</td></tr>";
}
$sth->finish();
}
print<<"end_of_html";
		</table>
	</div>
</body>
</html>
end_of_html
