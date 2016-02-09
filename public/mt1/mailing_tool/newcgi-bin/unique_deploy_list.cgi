#!/usr/bin/perl
#===============================================================================
# Name   : unique_deploy_list.cgi - Displays deployed campaigns 
#
#--Change Control---------------------------------------------------------------
# 07/22/08  Jim Sobeck  Creation
#===============================================================================

#-----  include Perl Modules ---------
use strict;
use CGI;
use util;
use Lib::Database::Perl::Interface::Server;

#------  get some objects to use later ---------
my $util = util->new;
my $filter;
my $query = CGI->new;
my $sord=$query->param('sord');
if ($sord eq "")
{
	$sord="ip.group_name";
}
my $indate=$query->param('indate');
my $ipgroup=$query->param('ipgroup');
if ($ipgroup eq "")
{
	$ipgroup=0;
}
my $cgroup=$query->param('cgroup');
if ($cgroup eq "")
{
	$cgroup=0;
}
my $instatus=$query->param('instatus');
if ($instatus eq "")
{
	$instatus="ALL";
}
my $cserver=$query->param('cserver');
my $lastupdated=$query->param('lastupdated');
if ($lastupdated eq "")
{
	$lastupdated=0;
}
my $in_template=$query->param('template_id');
if ($in_template eq "")
{
	$in_template=0;
}
my $incname=$query->param('incname');
my $inuid=$query->param('inuid');
my $inaid=$query->param('inaid');
if ($inaid eq "")
{
	$inaid=0;
}
#if ($inaid > 0)
#{
#	$instatus="Active";
#}
my $cdomain=$query->param('cdomain');
my $ccdomain=$query->param('ccdomain');

if ($cserver eq "")
{
	$cserver=0;
}
my $cmta=$query->param('cmta');
if ($cmta eq "")
{
	$cmta=0;
}
my $gsm=$query->param('gsm');
if ($gsm eq "")
{
	$gsm=0;
}
my $sql;
my $sth;
my $dbh;
my ($uid,$cname,$sdate,$cstatus);
my $cancel_reason;
my $actual_status;
my $tcstatus;
my $group_id;
my $gname;
my $IPGRPMTA;

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
	$userDataRestrictionWhereClause = qq|userID = $user_id AND |;
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
if ($indate eq "")
{
	$sql="select curdate()";
	$sth=$dbhq->prepare($sql);
	$sth->execute();
	($indate)=$sth->fetchrow_array();
	$sth->finish();
}
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
<title>Deployed Unique Campaigns</title>

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
<script language="JavaScript">
function selectall()
{
    refno=/caction/;
    for (var x=0; x < document.adform.length; x++)
    {
        if ((document.adform.elements[x].type=="checkbox") && (refno.test(document.adform.elements[x].name)))
        {
            document.adform.elements[x].checked = true;
        }
    }
}
function unselectall()
{
    refno=/caction/;
    for (var x=0; x < document.adform.length; x++)
    {
        if ((document.adform.elements[x].type=="checkbox") && (refno.test(document.adform.elements[x].name)))
        {
            document.adform.elements[x].checked = false;
        }
    }
}
function redeploy_camp(uid)
{
document.location="/cgi-bin/unique_function.cgi?f=redeploy&uid="+uid;
}
function restart_camp(uid)
{
document.location="/cgi-bin/unique_function.cgi?f=restart&uid="+uid;
}
function redeliver_camp(uid)
{
document.location="/cgi-bin/unique_function.cgi?f=redeliver&uid="+uid;
}
function resume_camp(uid)
{
document.location="/cgi-bin/unique_resume.cgi?uid="+uid;
}
function pause_camp(uid)
{
document.location="/cgi-bin/unique_function.cgi?f=pause&uid="+uid;
}
function chgtime_camp(uid)
{
document.location="/cgi-bin/unique_chgtime.cgi?uid="+uid;
}
function pauseall_camp()
{
document.location="/cgi-bin/unique_function.cgi?f=pauseall";
}
function cancel_camp(uid)
{
document.location="/cgi-bin/unique_function.cgi?f=cancel&uid="+uid;
}
</script>
</head>

<body>
<div id="container">

	<h1>List of Unique Deployed Campaigns</h1>
	<h2> <a href="/newcgi-bin/unique_deploy_list.cgi">deployed unique campaigns</a> | <a href="/newcgi-bin/unique_deploy_list.cgi?gsm=2">Hotmail deploys</a> | <a href="/newcgi-bin/unique_deploy_list.cgi?gsm=4">Time Based</a> | <a href="/newcgi-bin/unique_deploy_list.cgi?gsm=5">Hotmail Domain Deploys</a> | <a href="/newcgi-bin/dailydeal_list.cgi">Daily Deals</a> | <a href="/newcgi-bin/trigger_display_list.cgi">Triggers</a> | <a href="/newcgi-bin/mainmenu.cgi" target=_top>go home</a></h2>
<center>
<form method=post action=unique_deploy_list.cgi>
<input type=hidden name=sord value="$sord">
<input type=hidden name=gsm value="$gsm">
<font size=+1><b>Date Deployed: </b><input type=text name=indate size=10 maxlength=10 value='$indate'>&nbsp;&nbsp;<b>IP Group:</b>
<select name=ipgroup><option value=0 selected>ALL</option>
end_of_html
$sql="select group_id,group_name from IpGroup where $userDataRestrictionWhereClause status='Active' order by group_name";
#$sth=$dbhq1->prepare($sql);
$sth=$dbhq->prepare($sql);
$sth->execute();
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
&nbsp;&nbsp;<b>Client Group:</b>
<select name=cgroup><option value=0 selected>ALL</option>
end_of_html
$sql="select client_group_id,group_name from ClientGroup where $userDataRestrictionWhereClause status='A' order by group_name";
$sth=$dbhq->prepare($sql);
$sth->execute();
while (($group_id,$gname)=$sth->fetchrow_array())
{
	if ($group_id == $cgroup)
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
&nbsp;&nbsp;<b>Status:</b>
<select name=instatus><option value="ALL">ALL</option>
end_of_html
my @D=("Active","Completed","CANCELLED","INJECTING","PAUSED","PRE-PULLING","SLEEPING");
my $i=0;
while ($i <= $#D)
{
	if ($D[$i] eq $instatus)
	{
		print "<option selected value=$D[$i]>$D[$i]</option>\n";
	}
	else
	{
		print "<option value=$D[$i]>$D[$i]</option>\n";
	}
	$i++;
}
print<<"end_of_html";
</select>
<br>
<b>MTA:</b>
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
	$gname=~s/.i.routename.com//;
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
&nbsp;&nbsp;<b>Server:</b>
<select name=cserver><option value=0 selected>ALL</option>
end_of_html
my $params={};
($errors, $results) = $serverInterface->getMtaDataServers($params);
$cnt=$#{$results};
$i=0;
while ($i <= $cnt)
{
	print "<$results->[$i]->{'serverID'}> $results->[$i]->{'hostname'}\n";
	$group_id=$results->[$i]->{'serverID'};
	$gname=$results->[$i]->{'hostname'};
	$gname=~s/.pcposts.com//;
	$gname=~s/.i.routename.com//;
	$gname=~s/.routename.com//;
	if ($group_id == $cserver)
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
</select>&nbsp;&nbsp;Campaign Name:&nbsp;<input type=text name=incname value='$incname' size=30>
&nbsp;&nbsp;Not Updated In: <select name=lastupdated>
end_of_html
my @L=("0","30","60");
my @L1=("NA","30 Minutes","60 Minutes");
my $i=0;
while ($i <= $#L)
{
	if ($L[$i] == $lastupdated)
	{
		print "<option value=$L[$i] selected>$L1[$i]</option>\n";
	}
	else
	{
		print "<option value=$L[$i]>$L1[$i]</option>\n";
	}
	$i++;
}
print<<"end_of_html";
</select>
<br>Template: <select name=template_id><option value=0>ALL</option>
end_of_html
my $tid;
my $tname;
$sql="select template_id,template_name from brand_template where $userDataRestrictionWhereClause status='A' order by template_name";
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
</select>&nbsp;&nbsp;UID:&nbsp;<input type=text name=inuid value='$inuid' size=15>&nbsp;&nbsp;Advertiser: <select name=inaid><option value=0>--Select One--</option>
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
</select><br>&nbsp;&nbsp;Domain:&nbsp;<input type=text name=cdomain value='$cdomain' size=30>&nbsp;&nbsp;Content Domain:&nbsp;<input type=text name=ccdomain value='$ccdomain' size=30>&nbsp;&nbsp;<input type=submit name=submit value="Filter"></font>
</form>
</center>
	<div id="form">
<form method=post action=unique_multiple_action.cgi name=adform>
<input type=hidden name=sord value="$sord">
<input type=hidden name=gsm value="$gsm">
		<center><font size=+1><a href="javascript:selectall();">Select All</a>&nbsp;&nbsp;&nbsp;<a href="javascript:unselectall();">Unselect All</a><br></font>
		<table style="border: 1px solid #999; " border=1>
		  <tr>
			<td></td>
		  	<td ><strong>Date Deployed</strong></td>
			<td ><strong>ID</strong></td>
			<td ><strong><a href="unique_deploy_list.cgi?sord=campaign_name&gsm=$gsm">Campaign Name</a></strong></td>
			<td ><strong>MTA</strong></td>
			<td ><strong>Server</strong></td>
			<td ><strong>Process No</strong></td>
			<td ><strong><a href="unique_deploy_list.cgi?sord=ip.group_name&gsm=$gsm">IP Group</a></strong></td>
			<td ><strong><a href="unique_deploy_list.cgi?sord=cg.group_name&gsm=$gsm">Client Group</a></strong></td>
			<td ><strong><a href="unique_deploy_list.cgi?sord=up.profile_name&gsm=$gsm">Profile</a></strong></td>
			<td ><strong><a href="unique_deploy_list.cgi?sord=m.name&gsm=$gsm">MTA Setting</a></strong></td>
			<td><strong><a href="unique_deploy_list.cgi?sord=uc.status&gsm=$gsm">Status</a></strong></td>
			<td ><strong>Substatus</strong></td>
			<td ><strong>Domain</strong></td>
			<td ><strong>Content Domain</strong></td>
			<td ><strong>Template</strong></td>
			<td ><strong>Subject(s)</strong></td>
			<td ><strong>From(s)</strong></td>
			<td ><strong>Creative Code(s)</strong></td>
			<td ><strong>Article</strong></td>
			<td ><strong>Available<br>To Deploy</strong></td>
			<td ><strong>Scheduled</strong></td>
			<td ><strong>Seedlist</strong></td>
			<td ><strong>Start Time</strong></td>
			<td ><strong>Finish Time</strong></td>
			<td ><strong>Time Taken</strong></td>
			<td ><strong>Est. Send Cnt</strong></td>
			<td ><strong>Delivered</strong></td>
			<td ><strong>Actions</strong></td>
		  </tr>
end_of_html
$sql="select unq_id,campaign_name,send_date,uc.status,ip.group_name,server_id,cg.group_name,send_time,ip.group_id, up.profile_name,uc.pid,uc.start_time,uc.end_time,timediff(uc.end_time,uc.start_time),mailing_domain,send_cnt, inj_cnt,sleep_reason,template_name,uc.mta_id,uc.article_id,cancel_reason,up.start_record,up.end_record, deployStatusName,statusUpdateTime,statusInformation,useRdns,m.name from unique_campaign uc left outer join DeployStatus on DeployStatus.deployStatusID=uc.substatusID, IpGroup ip, ClientGroup cg, UniqueProfile up, brand_template bt,mta m where uc.mta_id=m.mta_id and send_date = '$indate' and campaign_type='DEPLOYED' and uc. group_id=ip.group_id and uc.client_group_id=cg.client_group_id and uc.profile_id=up.profile_id and uc.mailing_template=bt.template_id "; 
$filter=0;
if ($ipgroup > 0)
{
	$filter=1;
	$sql=$sql." and uc.group_id=$ipgroup ";
}
if ($in_template > 0)
{
	$filter=1;
	$sql=$sql." and uc.mailing_template=$in_template";
}
if (($inuid > 0) and ($inuid ne ""))
{
	$filter=1;
	$sql=$sql." and uc.unq_id=$inuid";
}
if (($inaid > 0) and ($inaid ne ""))
{
	$filter=1;
	$sql=$sql." and uc.advertiser_id=$inaid";
}
if ($cgroup > 0)
{
	$filter=1;
	$sql=$sql." and uc.client_group_id=$cgroup ";
}
if ($instatus eq "Active")
{
	$filter=1;
	$sql=$sql." and uc.status in ('START','PENDING') ";
}
elsif ($instatus eq "Completed")
{
	$filter=1;
	$sql=$sql." and uc.status='PULLED' ";
}
elsif ($instatus ne "ALL")
{
	$filter=1;
	$sql=$sql." and uc.status='$instatus' ";
}

$sql .= ($util->getUserData()->{'isExternalUser'}) ? (' AND userID=' . $user_id) : ('');

if ($cserver > 0)
{
	$filter=1;
	$sql=$sql." and uc.server_id=$cserver";
}
if ($lastupdated > 0)
{
	$filter=1;
	$sql=$sql." and statusUpdateTime < date_sub(now(),interval $lastupdated minute) and uc.status not in ('PULLED','COMPLETED')";
}
if ($incname ne "")
{
	$filter=1;
	$sql=$sql." and uc.campaign_name like '%".$incname."%'";
}
if ($cmta > 0)
{
	$filter=1;
	my $params;
	$params->{'mtaServerID'}=$cmta;
    $params->{'withFailedData'}=0;
    my ($errors, $results) = $serverInterface->getMailingIpsAssigned($params);
	my $cnt=$#{$results};
	my $i=0;
	my $mtaip="";
	while ($i <= $cnt)
	{
		$mtaip.="'".$results->[$i]->{'ip'}."',";
		$i++;
	}
	chop($mtaip);

	$sql=$sql." and uc.group_id in (select distinct igi.group_id from IpGroupIps igi, IpGroup ig where igi.ip_addr in ($mtaip) and igi.group_id=ig.group_id and ig.status='Active' "; 
	if ($util->getUserData()->{'isExternalUser'})
	{
		$sql.=" and ig.".${userDataRestrictionWhereClause};
	}
	$sql.=" )";
}
if ($cdomain ne "")
{
	$sql=$sql." and uc.unq_id in (select unq_id from UniqueDomain where mailing_domain='$cdomain') ";
	$filter=1;
}
if ($ccdomain ne "")
{
	$sql=$sql." and uc.unq_id in (select unq_id from UniqueContentDomain where domain_name='$ccdomain') ";
	$filter=1;
}
if ($gsm == 1)
{
	$filter=1;
	$sql=$sql." and (ip.goodmail_enabled='Y' or ip.group_name like 'discover%' or ip.group_name like 'credithelpadvisor%') and uc.slot_type != 'Hotmail' and uc.slot_type != 'Chunking' ";
}
elsif ($gsm == 2)
{
	$filter=1;
	$sql=$sql." and uc.slot_type = 'Hotmail'";
}
elsif ($gsm == 3)
{
	$filter=1;
	$sql=$sql." and uc.slot_type = 'Chunking'";
}
elsif ($gsm == 4)
{
	$filter=1;
	$sql=$sql." and uc.slot_type = 'Time Based'";
}
elsif ($gsm == 5)
{
	$filter=1;
	$sql=$sql." and uc.slot_type = 'Hotmail Domain'";
}
elsif ($gsm == 6)
{
	$filter=1;
	$sql=$sql." and uc.slot_type like 'NEW DEPLOY%'";
}
elsif ($gsm == 7)
{
	$filter=1;
	$sql=$sql." and uc.slot_type ='TEST'";
}
else
{
	$sql=$sql." and (ip.goodmail_enabled='N' and ip.group_name not like 'discover%' and ip.group_name not like 'credithelpadvisor%') and uc.slot_type = 'Normal'";
}
if ($filter)
{
$sql=$sql." order by $sord,send_time";
$sth=$dbhu->prepare($sql);
$sth->execute();
my $ipgroup;
my $server_id;
my $stime;
my $clientgroup;
my $ip_group_id;
my $mta;
my $profile_name;
my $pid;
my $start_time;
my $end_time;
my $time_taken;
my $deploy;
my $cnt;
my $mdomain;
my $cmdomain;
my $send_cnt;
my $inj_cnt;
my $delivered_cnt;
my $sleep_reason;
my $template_name;
my $mta_id;
my $article_id;
my $start_record;
my $end_record;
my $deployStatusName;
my $statusUpdateTime;
my $statusInformation;
my $useRdns;
my $mta_name;
while (($uid,$cname,$sdate,$cstatus,$ipgroup,$server_id,$clientgroup,$stime,$ip_group_id,$profile_name,$pid,$start_time,$end_time,$time_taken,$mdomain,$send_cnt,$inj_cnt,$sleep_reason,$template_name,$mta_id,$article_id,$cancel_reason,$start_record,$end_record,$deployStatusName,$statusUpdateTime,$statusInformation,$useRdns,$mta_name)=$sth->fetchrow_array())
{
	if ($pid == 0)
	{
		$pid="";
	}
	if (($start_record != 0) or ($end_record != 0))
	{
		my $tsend_cnt=$end_record-$start_record+1;
		if ($tsend_cnt < $send_cnt) 
		{
			$send_cnt=$tsend_cnt;
		}
	}
	if ($start_time eq "0000-00-00 00:00:00")
	{
		$start_time="";
	}
	if ($end_time eq "0000-00-00 00:00:00")
	{
		$end_time="";
		$time_taken="";
	}

	$sql="select distinct dbID from campaign where $userDataRestrictionWhereClause id = $uid"; 
	my $sth1=$dbhu->prepare($sql);
	$sth1->execute();
	my ($processID)=$sth1->fetchrow_array();
	$sth1->finish();
	
	my($dbID, $pid) = split(/\./, $processID); 

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
    	$mta=~s/.i.routename.com//;
    	$mta=~s/.routename.com//;
		$IPGRPMTA->{$ip_group_id}=$mta;
	}
#
#	Done this way for performance for now
	
#    $sql="select hostname from Server s, ServerIp sic, IpGroupIps igi where s.serverID=sic.serverID and sic.ip=igi.ip_addr and igi.group_id=? and  sic.ipRoleID=2 and ipStatusID=14 limit 1";
#    my $sth1=$dbhq->prepare($sql);
#    $sth1->execute($ip_group_id);
#    ($mta)=$sth1->fetchrow_array();
#    $sth1->finish();


	$sql="select count(*) from unique_campaign where $userDataRestrictionWhereClause group_id=? and send_date=curdate() and status not in ('PULLED','CANCELLED')";
	my $sth1=$dbhq->prepare($sql);
	$sth1->execute($ip_group_id);
	($cnt)=$sth1->fetchrow_array();
	$sth1->finish();
	if ($cnt > 0)
	{
		$deploy="No";
	}
	else
	{
		$deploy="Yes";
	}
	$actual_status=$cstatus;

	if ($cstatus eq "PULLED")
	{
		$cstatus="Completed";
	}
	if (($cstatus eq "PENDING") or ($cstatus eq "START"))
	{
		$cstatus="Active";
	}
	$delivered_cnt="";
	if ($cstatus ne "Active")
	{
    #$sql="SELECT sum(statisticValue) FROM EmailEventSummary e JOIN EmailEvent ee on e.emailEventID = ee.emailEventID JOIN campaign c on e.campaignID=c.campaign_id where c.id=? and emailEventCategory = 'DE'";
	#$sth1=$dbhq->prepare($sql);
	#$sth1->execute($uid);
	#($delivered_cnt)=$sth1->fetchrow_array();
	#$sth1->finish();
	#if ($delivered_cnt eq "")
	#{
		$delivered_cnt=0;
	#}
	}
	
	$tcstatus=$cstatus;
	if ($cstatus eq "CANCELLED")
	{
		if ($cancel_reason ne "")
		{
			$tcstatus = $cstatus . " - ".$cancel_reason;
		}
		else
		{
			$tcstatus = $cstatus ;
		}
	}
	if ($cstatus eq "SLEEPING")
	{
		$tcstatus = $cstatus . " - ". $sleep_reason;
	}
	my $tdom;
	$mdomain="";
	if ($useRdns eq "Y")
	{
		$mdomain="Use Rdns";
	}
	else
	{
		$sql="select mailing_domain from UniqueDomain where unq_id=? order by mailing_domain";
		my $sth2a=$dbhu->prepare($sql);
		$sth2a->execute($uid);
		while (($tdom)=$sth2a->fetchrow_array())
		{
			$mdomain=$mdomain.$tdom." ";
		}
		$sth2a->finish();
		chop($mdomain);
	}
	$cmdomain="";
	$sql="select domain_name from UniqueContentDomain where unq_id=? order by domain_name";
	my $sth2a=$dbhu->prepare($sql);
	$sth2a->execute($uid);
	while (($tdom)=$sth2a->fetchrow_array())
	{
		$cmdomain=$cmdomain.$tdom." ";
	}
	$sth2a->finish();
	chop($cmdomain);

	my $msubject="";
	$sql="select advertiser_subject from UniqueSubject us,advertiser_subject where us.unq_id=? and us.subject_id=advertiser_subject.subject_id order by advertiser_subject";
	my $sth2a=$dbhu->prepare($sql);
	$sth2a->execute($uid);
	while (($tdom)=$sth2a->fetchrow_array())
	{
		$msubject=$msubject.$tdom." ";
	}
	$sth2a->finish();
	chop($msubject);
	my $mfrom="";
	$sql="select advertiser_from from UniqueFrom us,advertiser_from where us.unq_id=? and us.from_id=advertiser_from.from_id order by advertiser_from";
	$sth2a=$dbhu->prepare($sql);
	$sth2a->execute($uid);
	while (($tdom)=$sth2a->fetchrow_array())
	{
		$mfrom=$mfrom.$tdom." ";
	}
	$sth2a->finish();
	chop($mfrom);
	my $mcreative="";
	$sql="select creative_id from UniqueCreative where unq_id=? order by creative_id";
	$sth2a=$dbhu->prepare($sql);
	$sth2a->execute($uid);
	while (($tdom)=$sth2a->fetchrow_array())
	{
		$mcreative=$mcreative.$tdom." ";
	}
	$sth2a->finish();
	chop($mcreative);

	my $seedlist;
	$sql="select seedlist from mta_detail where mta_id=? and class_id=3";
	$sth2a=$dbhq->prepare($sql);
	$sth2a->execute($mta_id);
	($seedlist)=$sth2a->fetchrow_array();
	$sth2a->finish();
	my $article_str="";
	if ($article_id > 0)
	{
		$sql="select article_name from article where article_id=? and status='A'";
		$sth2a=$dbhq->prepare($sql);
		$sth2a->execute($article_id);
		($article_str)=$sth2a->fetchrow_array();
		$sth2a->finish();
	}

	print "<tr><td><input type=checkbox name=caction value=$uid></td><td>$sdate</td><td>$uid</td><td>$cname</td><td>$mta</td><td>$dbID</td><td>$pid</td><td>$ipgroup</td><td>$clientgroup</td><td>$profile_name</td><td>$mta_name</td><td>$tcstatus</td><td>$statusUpdateTime $deployStatusName $statusInformation</td><td>$mdomain</td><td>$cmdomain</td><td>$template_name</td><td>$msubject</td><td>$mfrom</td><td>$mcreative</td><td>$article_str</td><td>$deploy</td><td>$stime</td><td>$seedlist</td><td>$start_time</td><td>$end_time</td><td>$time_taken</td><td>$send_cnt</td><td>$delivered_cnt</td>";
	if ($cstatus eq "CANCELLED")
	{
		print "<td><input type=\"button\" value=\"restart\" onClick=\"restart_camp($uid);\"/>&nbsp;&nbsp;<input type=\"button\" value=\"redeploy\" onClick=\"redeploy_camp($uid);\"/>&nbsp;&nbsp;<input type=\"button\" value=\"redeliver\" onClick=\"redeliver_camp($uid);\"/></td></tr>\n";
	}
	elsif ($cstatus eq "Completed") 
	{
		print "<td><input type=\"button\" value=\"redeploy\" onClick=\"redeploy_camp($uid);\"/>&nbsp;&nbsp;<input type=\"button\" value=\"redeliver\" onClick=\"redeliver_camp($uid);\"/></td></tr>\n";
	}
	elsif (($actual_status eq "START") or ($actual_status eq "PRE-PULLING")) 
	{
		if ($gsm == 2)
		{
			print "<td><input type=\"button\" value=\"pause\" onClick=\"pause_camp($uid);\"/>&nbsp;&nbsp;<input type=\"button\" value=\"pauseall\" onClick=\"pauseall_camp();\"/>&nbsp;&nbsp;<input type=\"button\" value=\"cancel\" onClick=\"cancel_camp($uid);\"/>&nbsp;&nbsp;<input type=\"button\" value=\"Change Time\" onClick=\"chgtime_camp($uid);\"/></td></tr>\n";
		}
		else
		{
			print "<td><input type=\"button\" value=\"pause\" onClick=\"pause_camp($uid);\"/>&nbsp;&nbsp;<input type=\"button\" value=\"cancel\" onClick=\"cancel_camp($uid);\"/>&nbsp;&nbsp;<input type=\"button\" value=\"Change Time\" onClick=\"chgtime_camp($uid);\"/></td></tr>\n";
		}
	}
	elsif (($cstatus eq "Active") or ($cstatus eq "PRE-PULLING") or ($cstatus eq "INJECTING"))
	{
		if ($gsm == 2)
		{
			print "<td><input type=\"button\" value=\"pause\" onClick=\"pause_camp($uid);\"/>&nbsp;&nbsp;<input type=\"button\" value=\"pauseall\" onClick=\"pauseall_camp();\"/>&nbsp;&nbsp;<input type=\"button\" value=\"cancel\" onClick=\"cancel_camp($uid);\"/></td></tr>\n";
		}
		else
		{
			print "<td><input type=\"button\" value=\"pause\" onClick=\"pause_camp($uid);\"/>&nbsp;&nbsp;<input type=\"button\" value=\"cancel\" onClick=\"cancel_camp($uid);\"/></td></tr>\n";
		}
	}
	elsif ($cstatus eq "SLEEPING") 
	{
		print "<td><input type=\"button\" value=\"cancel\" onClick=\"cancel_camp($uid);\"/></td></tr>\n";
	}
	elsif ($cstatus eq "PAUSED")
	{
		print "<td><input type=\"button\" value=\"resume\" onClick=\"resume_camp($uid);\"/>&nbsp;&nbsp;<input type=\"button\" value=\"cancel\" onClick=\"cancel_camp($uid);\"/></td></tr>\n";
	}
}
$sth->finish();
}
print<<"end_of_html";
		</table>
<br>
<center><input type=submit name=submit value="Resume">&nbsp;<input type=submit name=submit value="Pause">&nbsp;<input type=submit name=submit value="Cancel">&nbsp;<input type=submit name=submit value="Redeploy">&nbsp;<input type=submit name=submit value="Restart">&nbsp;<input type=submit name=submit value="Replace USA">&nbsp;&nbsp;<input type=submit name=submit value="Replace Domains">
</form>
	</div>
</body>
</html>
end_of_html

