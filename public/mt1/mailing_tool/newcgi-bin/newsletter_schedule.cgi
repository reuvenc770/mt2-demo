#!/usr/bin/perl
#===============================================================================
# Purpose: Allow scheduling of newsletters 
# Name   : newsletter_schedule.cgi 
#
#--Change Control---------------------------------------------------------------
# 01/05/07  Jim Sobeck  Creation
#===============================================================================

#-----  include Perl Modules ---------
use strict;
use CGI;
use util;

#------  get some objects to use later ---------
my $util = util->new;
my $query = CGI->new;
my $name;
my $sql;
my $sth;
my $sth1;
my $dbh;
my $cdate;
my $tnl_id;
my $nl_name;
my $sdate;
my $edate;
my $cday;
my $cday1;
my $tday;
my $i;
my $cname;
my $diffcnt;
my $startdate1;
my $startdate2;
my $temp_aid;
my $camp_id;
my $camp_brand;
my $auto_lock;
my $nl_id=$query->param('nl_id');
my $startdate=$query->param('sdate');
$startdate2=$startdate;
my $adv_id=$query->param('adv_id');
if ($adv_id eq "")
{
	$adv_id=0;
}
if ($nl_id eq "")
{
	$nl_id=0;
}

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}
#------  connect to the util database -----------
my ($dbhq,$dbhu)=$util->get_dbh();


if ($startdate eq "")
{
	$sql="select date_format(date_sub(curdate(),interval dayofweek(curdate())-1 day),'%m/%d/%Y'),date_sub(curdate(),interval dayofweek(curdate())-1 day)";
	$sth = $dbhq->prepare($sql);
	$sth->execute();
	($sdate,$cday) = $sth->fetchrow_array();
	$sth->finish();
	$sql="select date_format(date_add(curdate(),interval 7-dayofweek(curdate()) day),'%m/%d/%Y')";
	$sth = $dbhq->prepare($sql);
	$sth->execute();
	($edate) = $sth->fetchrow_array();
	$sth->finish();
	$sql="select date_add(curdate(),interval 8-dayofweek(curdate()) day)";
	$sth = $dbhq->prepare($sql);
	$sth->execute();
	($startdate) = $sth->fetchrow_array();
	$sth->finish();
	$sql="select date_sub(curdate(),interval 7+dayofweek(curdate())-1 day)";
	$sth = $dbhq->prepare($sql);
	$sth->execute();
	($startdate1) = $sth->fetchrow_array();
	$sth->finish();
}
else
{
	$cday = $startdate;
	$sql="select date_format('$startdate','%m/%d/%Y')";
	$sth = $dbhq->prepare($sql);
	$sth->execute();
	($sdate) = $sth->fetchrow_array();
	$sth->finish();
	$sql="select date_format(date_add('$startdate',interval 6 day),'%m/%d/%Y')";
	$sth = $dbhq->prepare($sql);
	$sth->execute();
	($edate) = $sth->fetchrow_array();
	$sth->finish();
	$sql="select date_sub('$startdate',interval 7 day)";
	$sth = $dbhq->prepare($sql);
	$sth->execute();
	($startdate1) = $sth->fetchrow_array();
	$sth->finish();
	$sql="select date_add('$startdate',interval 7 day)";
	$sth = $dbhq->prepare($sql);
	$sth->execute();
	($startdate) = $sth->fetchrow_array();
	$sth->finish();
}
$sql="select date_format(curdate(),'%m/%d/%Y')";
$sth = $dbhq->prepare($sql);
$sth->execute();
($cdate) = $sth->fetchrow_array();
$sth->finish();

print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>
<head>
<meta http-equiv="Content-Language" content="en-us">
<title>Weekly Campaign Schedule [Newsletter]</title>
</head>
<script language="JavaScript">
function advchange()
{
	document.selection.adv_id.value=document.main.adv_id.value;
	return true;
}
function chgdate(newdate)
{
	document.selection.sdate.value=newdate;
	document.selection.submit();
}
function setdel(val)
{
	document.main.delflag.value=val;
}
</script>


<body>
<form method="post" name="selection" action="newsletter_schedule.cgi" onSubmit="return advchange();">
<input type=hidden name=adv_id value="$adv_id">
<input type=hidden name=sdate value="">
<table width="100%" cellspacing="0" cellpadding="0" border="0">
  <tr>
    <td width="50%" align="left" valign="bottom">
<font class="txtB">sort by newsletter: </font><br>
			<select class="txt" name='nl_id'>
				<option selected value=0>[show all]</option>
end_of_html
$sql="select nl_id,nl_name from newsletter where nl_status='A' order by nl_name";
$sth=$dbhq->prepare($sql);
$sth->execute();
while (($tnl_id,$nl_name) = $sth->fetchrow_array())
{
	if ($nl_id == $tnl_id)
	{
		print "<option selected value=$tnl_id>$nl_name</option>\n";
	}
	else
	{
		print "<option value=$tnl_id>$nl_name</option>\n";
	}
}
$sth->finish();
print<<"end_of_html";
</select> <input type=button value="Submit" onClick="JavaScript:chgdate('$startdate2');"></font></td>
    <td width="50%" align="right" valign="top"><b><font class="title">Today is $cdate<br>
	<br></b>Week of $sdate - $edate<b><br>
	<a href="JavaScript:chgdate('$startdate1');">back</a> | <a href="JavaScript:chgdate('$startdate');"">next</a></b></font></td>
  </tr>
</table>
</form>
<form method=get name="main" action="/cgi-bin/save_newsletter_schedule.cgi">
<input type=hidden name=nl_id value=$nl_id>
<input type=hidden name=startdate value="$cday">
<input type=hidden name=delflag value="">

<p align="center" class="txt">- click campaign name to modify advertiser<br>
- click newsletter name to modify newsletter<br>
- Select one or more slots and click save - this will either create a new campaign if empty or replace the advertiser if slot is already filled in<br>
<b>
</p>
<p align="center"><a href="/cgi-bin/schedule_copy.cgi?stype=N">Copy Schedule</a></p>
<font face="Verdana" size="2">Advertiser: </font></b>
<select name="adv_id" onChange="advchange();">
end_of_html
$sql="select distinct advertiser_info.advertiser_id,advertiser_name from advertiser_info where advertiser_info.status='A' order by advertiser_name";
$sth=$dbhq->prepare($sql);
$sth->execute();
my $taid;
my $aname;
while (($taid,$aname) = $sth->fetchrow_array())
{
	if ($adv_id == $taid)
	{
		print "<option selected value=$taid>$aname</option>\n";
	}
	else
	{
		print "<option value=$taid>$aname</option>\n";
	}
}
$sth->finish();
print<<"end_of_html";
</select>
<br><br>
end_of_html
if ($nl_id > 0)
{
	$sql="select nl_id,nl_name from newsletter where nl_id=$nl_id";
}
else
{
	$sql="select nl_id,nl_name from newsletter where nl_status='A' order by nl_name";
}
$sth1=$dbhu->prepare($sql);
$sth1->execute();
my $tnl_id;
$cday1=$cday;
while (($tnl_id,$nl_name) = $sth1->fetchrow_array())
{
print<<"end_of_html";
<div align="center" class="title"><a href="/cgi-bin/newsletter_disp.cgi?nl_id=$tnl_id">$nl_name</a></div>
</b>
<table align="center" width="100%" cellpadding="5" style="border-collapse: collapse; border-style: groove; border-width: 2px; border-color: #a6a6a6; " class="txt">
	<tr>
		<td height="35" class="txt" align="center" bgcolor="#CDCDCD" style="border-style: solid; border-width: 2px; border-color: #FFFFFF;" width="104">
		<b><a href="/cgi-bin/listprofile_list.cgi?tflag=L">profiles</a></b>
<br><b><a href="/cgi-bin/upd_newsletter_schedule.cgi">slots</a></b>
</td>
end_of_html
$cday=$cday1;
$tday=$cday1;
$sql="select date_format('$cday','%m/%d'),date_add('$cday',interval 1 day)";
$sth = $dbhq->prepare($sql);
$sth->execute();
($tday,$cday) = $sth->fetchrow_array();
$sth->finish();
print<<"end_of_html";
		<td class="txtB" align="center" bgcolor="#CDCDCD" style="border-style: solid; border-width: 2px; border-color: #FFFFFF;">Sunday - $tday</td>
end_of_html
$sql="select date_format('$cday','%m/%d'),date_add('$cday',interval 1 day)";
$sth = $dbhq->prepare($sql);
$sth->execute();
($tday,$cday) = $sth->fetchrow_array();
$sth->finish();
print<<"end_of_html";
		<td class="txtB" align="center" bgcolor="#CDCDCD" style="border-style: solid; border-width: 2px; border-color: #FFFFFF;">Monday - $tday</td>
end_of_html
$sql="select date_format('$cday','%m/%d'),date_add('$cday',interval 1 day)";
$sth = $dbhq->prepare($sql);
$sth->execute();
($tday,$cday) = $sth->fetchrow_array();
$sth->finish();
print<<"end_of_html";
		<td class="txtB" align="center" bgcolor="#CDCDCD" style="border-style: solid; border-width: 2px; border-color: #FFFFFF;">Tuesday - $tday</td>
end_of_html
$sql="select date_format('$cday','%m/%d'),date_add('$cday',interval 1 day)";
$sth = $dbhq->prepare($sql);
$sth->execute();
($tday,$cday) = $sth->fetchrow_array();
$sth->finish();
print<<"end_of_html";
		<td class="txtB" align="center" bgcolor="#CDCDCD" style="border-style: solid; border-width: 2px; border-color: #FFFFFF;">Wednesday - $tday</td>
end_of_html
$sql="select date_format('$cday','%m/%d'),date_add('$cday',interval 1 day)";
$sth = $dbhq->prepare($sql);
$sth->execute();
($tday,$cday) = $sth->fetchrow_array();
$sth->finish();
print<<"end_of_html";
		<td class="txtB" align="center" bgcolor="#CDCDCD" style="border-style: solid; border-width: 2px; border-color: #FFFFFF;">Thursday - $tday</td>
end_of_html
$sql="select date_format('$cday','%m/%d'),date_add('$cday',interval 1 day)";
$sth = $dbhq->prepare($sql);
$sth->execute();
($tday,$cday) = $sth->fetchrow_array();
$sth->finish();
print<<"end_of_html";
		<td class="txtB" align="center" bgcolor="#CDCDCD" style="border-style: solid; border-width: 2px; border-color: #FFFFFF;">Friday - $tday</td>
end_of_html
$sql="select date_format('$cday','%m/%d'),date_add('$cday',interval 1 day)";
$sth = $dbhq->prepare($sql);
$sth->execute();
($tday,$cday) = $sth->fetchrow_array();
$sth->finish();
print<<"end_of_html";
		<td class="txtB" align="center" bgcolor="#CDCDCD" style="border-style: solid; border-width: 2px; border-color: #FFFFFF;">Saturday - $tday</td>
	</tr>
end_of_html
$sql="select slot_id,date_format(schedule_time,'%l:%i%p'),profile_name,nl_slot_info.profile_id,nl_slot_info.status from nl_slot_info,list_profile where nl_slot_info.nl_id=$tnl_id and nl_slot_info.profile_id=list_profile.profile_id order by slot_id";
my $sth1a=$dbhq->prepare($sql);
$sth1a->execute();
my $slot_id;
my $schedule_time;
my $profile_name;
my $profile_id;
my $status;
while (($slot_id,$schedule_time,$profile_name,$profile_id,$status) = $sth1a->fetchrow_array())
{
	if ($status eq "D")
	{
		$profile_name="Deleted";
	}
	print "<tr><td class=\"txt\" bgcolor=\"#CDCDCD\" style=\"border-style: solid; border-width: 2px; border-color: #FFFFFF;\" width=\"104\"><b>\n";
	print "$slot_id\. </b>$profile_name<br> \@ $schedule_time </td>\n";
	my $j = 0;
	while ($j < 7)
	{
		$sql = "select campaign_name,advertiser_id,campaign.campaign_id,client_brand_info.brand_name,auto_lock from campaign,camp_schedule_info,client_brand_info where camp_schedule_info.nl_id=$tnl_id and camp_schedule_info.slot_id=$slot_id and slot_type='N' and camp_schedule_info.schedule_date=date_add('$cday1',interval $j day) and campaign.campaign_id=camp_schedule_info.campaign_id and campaign.brand_id=client_brand_info.brand_id and camp_schedule_info.status='A' limit 1"; 
		my $sth1c = $dbhq->prepare($sql);
		$sth1c->execute();
		if (($cname,$temp_aid,$camp_id,$camp_brand,$auto_lock) = $sth1c->fetchrow_array())
		{
			$sth1c->finish();
			$sql = "select datediff(date_add('$cday1',interval $j day),curdate())";
            my $sth1b = $dbhq->prepare($sql) ;
            $sth1b->execute();
            ($diffcnt) = $sth1b->fetchrow_array();
            $sth1b->finish();
			if ($diffcnt >= 0)
			{
				print "<td align=\"center\" style=\"border-style: dotted; border-width: 1px; \"><a href=\"/cgi-bin/advertiser_disp2.cgi?pmode=U&puserid=$temp_aid\" target=\"_blank\">$cname</a>&nbsp;<font color=\"#FF00000\" face=\"Verdana\" size=\"1\"><b><a href=\"/cgi-bin/newsletter_edit_camp.cgi?campaign_id=$camp_id&adv_id=$temp_aid&nl_id=$nl_id&startdate=$sdate\">E</a></b>&nbsp;<font color=\"#FF00000\" face=\"Verdana\" size=\"1\"><b><a href=\"/cgi-bin/camp_del.cgi?campaign_id=$camp_id&nflag=Y\">D</a></b>&nbsp;&nbsp;<br><input type=\"checkbox\" value=\"${tnl_id}_${slot_id}_${j}\" name=\"chkbox\">"; 
			}
			else
			{
				print "<td align=\"center\" style=\"border-style: dotted; border-width: 1px; \"><font face=\"Verdana\" size=1><a href=\"/cgi-bin/advertiser_disp2.cgi?pmode=U&puserid=$temp_aid\" target=\"_blank\">$cname</a>&nbsp;<font color=\"#FF00000\" face=\"Verdana\" size=\"1\">";
			}
			print "</td>\n";
		}
        else
        {
        	$sth1c->finish();
            $sql = "select datediff(date_add('$cday1',interval $j day),curdate())";
            my $sth1b = $dbhq->prepare($sql) ;
            $sth1b->execute();
            ($diffcnt) = $sth1b->fetchrow_array();
            $sth1b->finish();
            if ($diffcnt >= 0)
            {
            	print "<td align=center><a href=\"/cgi-bin/schedule_advertiser.cgi?stype=N&xid=${tnl_id}_${slot_id}_${j}&startdate=$cday1\"><font face=Verdana size=1>Schedule</a></font><input type=\"checkbox\" value=\"${tnl_id}_${slot_id}_${j}\" name=\"chkbox\"></td>\n";
            }
            else
            {
            	print "<td><font size=\"1\"></font></td>\n";
            }
        }
		$j++;
	}
	print "</tr>";
}
$sth1a->finish();
print<<"end_of_html";
	</table>

<br><br>
end_of_html
}
$sth1->finish();
print<<"end_of_html";
<b>
<p align="center" class="txt">
<input type="image" src="/images/save.gif" onClick="setdel('N');"> <input type="image" src="/images/remove.gif" onClick="setdel('Y');">
<a href="/newcgi-bin/mainmenu.cgi"><img src="/images/cancel.gif" border="0"></a>
</p>
</form>
</body>

</html>
end_of_html
