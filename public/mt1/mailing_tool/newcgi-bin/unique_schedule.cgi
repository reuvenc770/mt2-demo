#!/usr/bin/perl
#===============================================================================
# Purpose: Displays weekly unique schedule 
# Name   : unique_schedule.cgi 
#
#--Change Control---------------------------------------------------------------
# 11/19/08  Jim Sobeck  Creation
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
my $sth1a;
my $dbh;
my $rowcnt;
my $cname;
my $unq_id;
my $camp_cnt;
my $tables;
my $server_id;
my $cstatus;
my $slot_id;
my $startdate = $query->param('sdate');
my $nid=$query->param('nid');
my $mtaid=$query->param('mtaid');
my $sdate;
my $edate;
my $cdate;
my $startdate1;
my $cday;
my $cday_sav;
my $aid;
my $aname;
my $tday;
my $client_id;
my $company;
my $diffcnt;

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}
#------  connect to the util database -----------
my ($dbhq,$dbhu)=$util->get_dbh();
#
if ($startdate eq "")
{
	$sql="select date_format(date_sub(curdate(),interval dayofweek(curdate())-3 day),'%m/%d/%Y'),date_sub(curdate(),interval dayofweek(curdate())-3 day)";
	$sth = $dbhq->prepare($sql);
	$sth->execute();
	($sdate,$cday) = $sth->fetchrow_array();
	$sth->finish();
	$sql="select date_format(date_add(curdate(),interval 9-dayofweek(curdate()) day),'%m/%d/%Y')";
	$sth = $dbhq->prepare($sql);
	$sth->execute();
	($edate) = $sth->fetchrow_array();
	$sth->finish();
	$sql="select date_add(curdate(),interval 10-dayofweek(curdate()) day)";
	$sth = $dbhq->prepare($sql);
	$sth->execute();
	($startdate) = $sth->fetchrow_array();
	$sth->finish();
	$sql="select date_sub(curdate(),interval 7+dayofweek(curdate())-3 day)";
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
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Unique Schedule</title>
</head>
<body>
<!-- <script language="JavaScript">
parent.middle.set_sdate('$cday');
</script> -->

<center>
<form method="post" action="upload_slot_schedule.cgi" encType=multipart/form-data accept-charset="UTF-8">
Unique File: <input type=file name=upload_file>&nbsp;&nbsp;Test<input type=checkbox checked value="test" name=utype>&nbsp;&nbsp;
<input type=submit value=Load>
</form>
</center>
</form>
<form method=post action="/cgi-bin/unique_schedule.cgi">
<input type=hidden name=sdate value="$cday">
<table border="0" width="100%" id="table3">
	<tr>
		<td></td>
		<td>
		<p align="right"><b><font face="Verdana">Today is $cdate</font></b></td>
	</tr>
	<tr>
	<td><font face="Verdana"><b>Client Group:&nbsp;</b></font><select name=nid>
<option value=0 selected>ALL</option>
end_of_html
$sql="select client_group_id,group_name from ClientGroup where status='A' order by group_name";
$sth = $dbhq->prepare($sql);
$sth->execute();
my $tid;
my $compname;
while (($tid,$compname) = $sth->fetchrow_array())
{
	if ($tid == $nid)
	{
		print "<option value=$tid selected>$compname</option>\n";
	}
	else
	{
		print "<option value=$tid>$compname</option>\n";
	}
}
$sth->finish();

$cday_sav=$cday;
print<<"end_of_html";
</select>
	&nbsp;&nbsp;<font face="Verdana"><b>MTA Setting:&nbsp;</b></font><select name=mtaid>
<option value=0 selected>ALL</option>
end_of_html
$sql="select mta_id,name from mta order by name";
$sth = $dbhq->prepare($sql);
$sth->execute();
my $tid;
my $mname;
while (($tid,$mname) = $sth->fetchrow_array())
{
	if ($tid == $mtaid)
	{
		print "<option value=$tid selected>$mname</option>\n";
	}
	else
	{
		print "<option value=$tid>$mname</option>\n";
	}
}
$sth->finish();
print<<"end_of_html";
</select>&nbsp;<input type=submit value="Go"></td></tr>
</table>
</form>
<form method=post action="/cgi-bin/unique_schedule_save.cgi">
<input type=hidden name=sdate value="$cday">
<input type=hidden name=nid value="$nid">
<input type=hidden name=mtaid value="$mtaid">
<table border="0" width="100%" id="table7">
	<tr>
	<td><font face="Verdana"><b>Advertiser:&nbsp;</b></font><select name=usa_id>
end_of_html
$sql="select usa.usa_id,usa.name from UniqueScheduleAdvertiser usa, advertiser_info ai where usa.advertiser_id=ai.advertiser_id and ai.status!='I' order by name";
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($aid,$aname)=$sth->fetchrow_array())
{
	print "<option value=$aid>$aname</option>\n";
}
$sth->finish();
print<<"end_of_html";
</select></td>
</tr>
	<tr>
		<td>&nbsp;</td>
		<td width="1130">
		<p align="right"><b><font face="Verdana">$sdate-$edate</font></b></td>
	</tr>
</table>
<table border="0" width="100%" id="table8">
	<tr>
		<td>&nbsp;</td>
		<td width="1130">
		<p align="right"><b><font face="Verdana">&nbsp;<a href="/cgi-bin/unique_schedule.cgi?sdate=$startdate1&nid=$nid&mtaid=$mtaid">BACK</a>&nbsp;
		<a href="/cgi-bin/unique_schedule.cgi?sdate=$startdate&nid=$nid&mtaid=$mtaid">FWD</a></font></b></td>
	</tr>
</table>
<table border="1" width="100%" id="table1">
end_of_html
#
# Get Client Groups 
#
my $cday1=$cday;
my $cday2=$cday;

$cday=$cday1;
$tday=$cday1;
$sql="select day('$cday'),date_add('$cday',interval 1 day)";
$sth = $dbhq->prepare($sql);
$sth->execute();
#removed this so the i can get actual dates
#($tday,$cday) = $sth->fetchrow_array();
$sth->finish();
$cday1=$cday;
my $old_tday=$tday;

if ($nid > 0)
{
	$sql="select client_group_id,group_name from ClientGroup where client_group_id=$nid";
}
else
{
	$sql="select client_group_id,group_name from ClientGroup where status='A' order by group_name"; 
}
$sth1=$dbhu->prepare($sql);
$sth1->execute();
my $cgroup_id;
while (($cgroup_id,$company)=$sth1->fetchrow_array())
{
	if ($mtaid > 0)
	{
		$sql="select count(*) from UniqueSlot where client_group_id=? and mta_id=$mtaid and status='A' order by schedule_time";
	}
	else
	{
		$sql="select count(*) from UniqueSlot where client_group_id=? and status='A' order by schedule_time";
	}
	$sth1a=$dbhu->prepare($sql);
	$sth1a->execute($cgroup_id);
	($camp_cnt)=$sth1a->fetchrow_array();
	$sth1a->finish();
	if ($camp_cnt eq "")
	{
		$camp_cnt=0;
	}
	if ($camp_cnt == 0)
	{
		next;
	}
print<<"end_of_html";

	<tr>
	<td><font face="Verdana" style="font-weight: 700" size="1"> $company ($cgroup_id)</font></td>
		<td width="178"><font face="Verdana" style="font-weight: 700" size="1"> 
											<span style="font-weight: 700">
											<font face="Verdana" size="1">$cday - Tuesday</font></span></td>
end_of_html
$sql="select day('$cday'),date_add('$cday',interval 1 day)";
$sth = $dbhq->prepare($sql);
$sth->execute();
($tday,$cday) = $sth->fetchrow_array();
$sth->finish();
print<<"end_of_html";
		<td width="178"><font face="Verdana" style="font-weight: 700" size="1"> 
											<span style="font-weight: 700">
											<font face="Verdana" size="1">$cday - Wednesday</font></span></td>
		<td width="178"><font face="Verdana" style="font-weight: 700" size="1"> 
end_of_html
$sql="select day('$cday'),date_add('$cday',interval 1 day)";
$sth = $dbhq->prepare($sql);
$sth->execute();
($tday,$cday) = $sth->fetchrow_array();
$sth->finish();
print<<"end_of_html";
		<span style="font-weight: 700"><font face="Verdana" size="1">$cday - Thursday</font></span></td>
		<td width="178"><font face="Verdana" style="font-weight: 700" size="1"> 
end_of_html
$sql="select day('$cday'),date_add('$cday',interval 1 day)";
$sth = $dbhq->prepare($sql);
$sth->execute();
($tday,$cday) = $sth->fetchrow_array();
$sth->finish();

print<<"end_of_html";
		<font face="Verdana" size="1">$cday - Friday</font></span></td>
		<td width="178"><font face="Verdana" style="font-weight: 700" size="1"> 
end_of_html
$sql="select day('$cday'),date_add('$cday',interval 1 day)";
$sth = $dbhq->prepare($sql);
$sth->execute();
($tday,$cday) = $sth->fetchrow_array();
$sth->finish();
print<<"end_of_html";
		<span style="font-weight: 700">
		<font face="Verdana" size="1">$cday - Saturday</font></span></td>
		<td width="178"><font face="Verdana" style="font-weight: 700" size="1"> 
end_of_html
$sql="select day('$cday'),date_add('$cday',interval 1 day)";
$sth = $dbhq->prepare($sql);
$sth->execute();
($tday,$cday) = $sth->fetchrow_array();
$sth->finish();
print<<"end_of_html";
		<font face="Verdana" size="1">$cday - Sunday</font></span></td>
		<td width="178">
end_of_html
$sql="select day('$cday'),date_add('$cday',interval 1 day)";
$sth = $dbhq->prepare($sql);
$sth->execute();
($tday,$cday) = $sth->fetchrow_array();
$sth->finish();
print<<"end_of_html";
											<font face="Verdana" style="font-weight: 700" size="1"> 
		<font face="Verdana" size="1">$cday - Monday</font></span></td>
	</tr>
end_of_html
	my $i = 1;
	while ($i <= $camp_cnt)
	{
		my $j = 0;
		while ($j < 7)
		{
			if ($j == 0)
			{
				my $stime;
				my $gname;
				my $gid;
				my $pid;
				my $pname;
				my $utype;
				my $k= $i - 1;
				$sql="select slot_id,schedule_time,group_id,group_name,us.profile_id,profile_name,us.slot_type from UniqueSlot us, IpGroup ip,UniqueProfile up  where client_group_id=? and us.status='A' and us.ip_group_id=ip.group_id and us.profile_id=up.profile_id order by schedule_time limit $k,1";
				$sth1a=$dbhu->prepare($sql);
				$sth1a->execute($cgroup_id);
				($slot_id,$stime,$gid,$gname,$pid,$pname,$utype)=$sth1a->fetchrow_array();
				$sth1a->finish();
				print "<tr><td><b>IP Group:</b> $gname ($gid)|<br><b>Profile:</b><a href=\"/cgi-bin/uniqueprofile_edit.cgi?pid=$pid\" target=_blank>$pname ($pid)</a><br>|<b>Time:</b> <a href=\"/cgi-bin/unique_slot_edit.cgi?sid=$slot_id\" target=_blank>$stime ($slot_id)</a>&nbsp;&nbsp;$utype</td>\n";
				$rowcnt++;
			}
			$sql = "select campaign_name,unique_campaign.unq_id,unique_campaign.server_id,unique_campaign.status from UniqueSchedule,unique_campaign where UniqueSchedule.slot_id=$slot_id and UniqueSchedule.unq_id=unique_campaign.unq_id and unique_campaign.send_date=date_add('$cday2',interval $j day)"; 
			$sth = $dbhq->prepare($sql);
			$sth->execute();
			if (($cname,$unq_id,$server_id,$cstatus) = $sth->fetchrow_array())
			{
				$sth->finish();
				$cname=~s/\(StrongMail\)//;
				$sql = "select datediff(date_add('$cday2',interval $j day),curdate())";
                $sth1a = $dbhq->prepare($sql) ;
                $sth1a->execute();
                ($diffcnt) = $sth1a->fetchrow_array();
                $sth1a->finish();
				if ($server_id > 0)
				{
					$diffcnt=-1;
				}
				if ($diffcnt >= 0)
				{
					if ($server_id == 0)
					{
						print "<td width=171><font face=\"Verdana\" size=1><a href=\"/cgi-bin/unique_main.cgi?uid=$unq_id&sid=$slot_id&diffcnt=$diffcnt\">$cname</a>&nbsp;<font color=\"#FF00000\" face=\"Verdana\" size=\"1\">&nbsp;<font color=\"#FF00000\" face=\"Verdana\" size=\"1\"><b><input type=\"checkbox\" value=\"${slot_id}_${diffcnt}\" name=\"dchkbox\"><a href=\"/cgi-bin/unique_schedule_camp_del.cgi?uid=$unq_id&sdate=$sdate&nid=$nid&mtaid=$mtaid\">D</a></b>"; 
					}
					else
					{
						print "<td width=171><font face=\"Verdana\" size=1>$cname&nbsp;<font color=\"#FF00000\" face=\"Verdana\" size=\"1\"><b>&nbsp;<font color=\"#FF00000\" face=\"Verdana\" size=\"1\">"; 
					}
					if (($cstatus eq "Active") or ($cstatus eq "PRE-PULLING") or ($cstatus eq "INJEcTING"))
    				{
        				print "<a href=\"/cgi-bin/unique_function.cgi?f=pause&uid=$unq_id\">Pause</a>&nbsp;&nbsp;<a href=\"/cgi-bin/unique_function.cgi?f=cancel&uid=$unq_id\">Cancel</a>\n";
    				}
    				elsif ($cstatus eq "PAUSED")
    				{
        				print "<a href=\"/cgi-bin/unique_resume.cgi?uid=$unq_id\" target=_new>Resume</a>&nbsp;&nbsp;<a href=\"/cgi-bin/unique_function.cgi?f=cancel&uid=$unq_id\">Cancel</a>\n";
    				}
				}
				else
				{
					print "<td width=171><font face=\"Verdana\" size=1>$cname&nbsp;<font color=\"#FF00000\" face=\"Verdana\" size=\"1\"><b>&nbsp;<font color=\"#FF00000\" face=\"Verdana\" size=\"1\">"; 
				}
				print "</font>\n";
				print "</td>\n";
			}
			else
			{
				$sth->finish();
				$sql = "select datediff(date_add('$cday2',interval $j day),curdate())";
                $sth1a = $dbhq->prepare($sql) ;
                $sth1a->execute();
                ($diffcnt) = $sth1a->fetchrow_array();
                $sth1a->finish();
				if ($diffcnt >= 0) 
				{
					print "<td width=\"171\"><font size=\"1\"><input type=\"checkbox\" value=\"${slot_id}_${diffcnt}\" name=\"chkbox\">&nbsp;&nbsp;<a href=\"\" target=\"_blank\"><font size=1 face=\"Verdana\" color=\"#FF0000\"><a href=\"/cgi-bin/unique_main.cgi?uid=0&sid=$slot_id&diffcnt=$diffcnt\">Schedule</a></font></td>\n";
				}
				else
				{
					print "<td width=\"171\"><font size=\"1\"></font></td>\n";
				}
			}
			$j++;
		}
		print "</tr>\n";
		$i++;
	}
	$cday=$cday1;
	$tday=$old_tday;;
}
$sth1->finish();
print<<"end_of_html";
	</table>

<p align="center">
						<a href="/cgi-bin/mainmenu.cgi" target="_top">
						<img src="/images/cancel.gif" border="0"></a><img height="1" src="/images/spacer.gif" width="40" border="0">
<input type=image src="/images/save.gif" border=0>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<a href="/cgi-bin/mainmenu.cgi" target="_top">
						<img border="0" src="/images/home_blkline.gif" width="76" height="23"></a></p>
</form>
</body>

</html>
end_of_html
