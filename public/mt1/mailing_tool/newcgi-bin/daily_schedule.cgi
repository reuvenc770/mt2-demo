#!/usr/bin/perl
#===============================================================================
# Purpose: Bottom frame of daily_schedule.html page 
# Name   : daily_schedule.cgi 
#
#--Change Control---------------------------------------------------------------
# 02/27/09  Jim Sobeck  Creation
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
my $profile_name;
my $performance;
my $brand_name;
my $sourceURL;
my $dd_name;
my $camp_brand;
my $camp_brand_id;
my $phone;
my $email;
my $id;
my $tstatus;
my $aim;
my $website;
my $username;
my $password;
my $tables;
my $usa_id= $query->param('usa_id');
if ($usa_id eq "")
{
	$usa_id=0;
}
my $stype="D";
my $nid=$query->param('nid');
my $ctype=$query->param('ctype');
if ($nid eq "")
{
	$nid=0;
	$ctype="ALL";
}
$ctype=~s/_/ /g;
#if ($nid eq "")
#{
#	my @raw_cookies;
#	my %cookies;
#	my $key;
#	my $val;
#	@raw_cookies = split (/; /,$ENV{'HTTP_COOKIE'});
#	foreach (@raw_cookies)
#	{
#    	($key, $val) = split (/=/,$_);
#    	$cookies{$key} = $val;
#	}
#	$nid = $cookies{'networkopt'};
#}
my $sdate;
my $edate;
my $cdate;
my $startdate1;
my $tday;
my $client_id;
my $company;
my $camp_cnt;
my $aol_cnt;
my $daily_cnt;
my $cname;
my $camp_id;
my $temp_aid;
my $test_flag;
my $diffcnt;
my ($supp_name,$last_updated,$filedate,$sid,$day_cnt);


#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}
my ($dbhq,$dbhu)=$util->get_dbh();
#
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>
<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Daily Schedule</title>
<link rel="stylesheet" type="text/css" href= "/stylesheet.css" />
<script language="Javascript">
function exportfile()
{
    var selObj = document.getElementById('ctype');
    var selIndex = selObj.selectedIndex;
    var selObj1 = document.getElementById('nid');
    var selIndex1 = selObj1.selectedIndex;
    var newwin = window.open("/cgi-bin/daily_deal_export.cgi?group_id="+selObj.options[selIndex].value+"&client_id="+selObj1.options[selIndex1].value, "Export", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,width=900,height=500,left=25,top=50");
    newwin.focus();
}
</script>
</head>
<body>
<div id="container">
<script language="JavaScript">
function setadvid(x)
{
	document.saveform.usa_id.value=document.mainform.usa_id.value;
}
function chkadv()
{
	if (document.saveform.usa_id.value == 0)
	{
		alert('You must select an Unique Schedule Advertiser before selecting save');
		return false;
	}
	return true;
}
</script>
<table class="tbl top" border="1" cellspacing="3" id="table7">
        <tr><td class=advertiser><form method=POST action="upload_ddschedule.cgi" encType=multipart/form-data><strong>Daily Deal Schedule CSV File:  </strong><INPUT type=file name="upload_file" size="65">&nbsp;&nbsp;<input type=submit value="Upload"></form></td></tr>
</table>
<form method=post name=mainform action="/cgi-bin/daily_schedule.cgi">
<table class="tbl top" border="1" cellspacing="3" id="table6">
            <tr>
        <td class=advertiser><strong>Unique Schedule Advertiser: </strong></td>
        <td class=dropdown><label><select name="usa_id" id="usa_id" onChange="setadvid();">
end_of_html
$sql="select usa_id,name from UniqueScheduleAdvertiser usa,advertiser_info ai where usa.advertiser_id=ai.advertiser_id and ai.status!='I' order by name";
$sth=$dbhu->prepare($sql);
$sth->execute();
my $aid;
my $aname;
while (($aid,$aname)=$sth->fetchrow_array())
{
	if ($aid == $usa_id)
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
</select></label></td>
</tr>
            </table>
        <div class="date margin_top flL">
	Client Group:&nbsp;<select name=ctype id=ctype>
<option value="ALL" selected>ALL</option>
end_of_html
 	my $sthc = $dbhu->column_info(undef, undef, 'user', '%');
    while ( my $col_info = $sthc->fetchrow_hashref)
    {
    	if (($col_info->{'TYPE_NAME'} eq 'ENUM') and ($col_info->{'COLUMN_NAME'} eq "client_type"))
		{    
			my $str=join(',',@{$col_info->{'mysql_values'}});
			my @a=split(',',$str);
			my $i=0;
			while ($i <= $#a)
			{
				my $val=$a[$i];
				$val=~s/ /_/g;
				if ($ctype eq $a[$i])
				{
					print "<option selected value=\"$val\">$a[$i]</option>";
				}
				else
				{
					print "<option value=\"$val\">$a[$i]</option>";
				}
				$i++;
			}
		}
	}  
print<<"end_of_html";
</select>&nbsp;&nbsp;Client:&nbsp;<select name=nid id=nid>
<option value=0 selected>ALL</option>
end_of_html
$sql="select user_id,first_name from user where status='A' order by first_name";
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

print<<"end_of_html";
</select>&nbsp;<input type=submit value="Go">
<input type="button" value="Export Schedule" onClick="javascript:exportfile();" />
</div>
</form>
<div class="clear"></div>
<form method=post name=saveform action="/cgi-bin/save_daily_schedule.cgi" onSubmit="return chkadv();">
<input type=hidden name=usa_id value=$usa_id>
<input type=hidden name=nid value="$nid"> 
<input type=hidden name=ctype value="$ctype"> 
<table class="tbl schedule" border="1" cellspacing="3">
end_of_html
#
# Get networks
#
if ($ctype eq "ALL")
{
	if ($nid > 0)
	{
		$sql = "select client_id,first_name,client_type,daily_cnt from network_schedule, user where client_id=user_id and client_id=$nid and user.status='A' order by client_id";
	}
	else
	{
		$sql = "select client_id,first_name,client_type,daily_cnt from network_schedule, user where client_id=user_id and daily_cnt > 0 and user.status='A' order by client_id";
	}
}
else
{
	if ($nid > 0)
	{
		$sql = "select client_id,first_name,client_type,daily_cnt from network_schedule, user where client_id=user_id and client_id=$nid and user.status='A' order by client_id";
	}
	else
	{
		$sql = "select client_id,first_name,client_type,daily_cnt from network_schedule, user where client_id=user_id and client_type='$ctype' and user.status='A' order by client_id";
	}
}
$sth1 = $dbhq->prepare($sql);
$sth1->execute();
my $client_type;
while (($client_id,$company,$client_type,$daily_cnt) = $sth1->fetchrow_array())
{
print<<"end_of_html";
	<tr>
		<th><font face="Verdana" style="font-weight: 700" size="1"> <a href="/cgi-bin/upd_client_schedule.cgi?client_id=$client_id" target="_blank">$company</a></font>
		<br><font face="Verdana" size="1" style="font-weight: 700">Brand - Setting</font></th>
		<th>Day 1</th>
		<th>Day 2</th>
		<th>Day 3</th>
		<th>Day 4</th>
		<th>Day 5</th>
		<th>Day 6</th>
		<th>Day 7</th>
	</tr>
end_of_html
	my $i = 1;
	$camp_cnt=$daily_cnt;
	my $rowcnt;
	while ($i <= $camp_cnt)
	{
		my $gotprofile=0;
		my $j = 0;
		while ($j < 7)
		{
			if ($j == 0)
			{
				my $ttime;
				$sql="select cb.brand_name,schedule_info.status,dd.name,schedule_info.performance,schedule_info.source_url from schedule_info,client_brand_info cb,DailyDealSetting dd where slot_type=? and slot_id=? and schedule_info.client_id=? and schedule_info.brand_id=cb.brand_id and schedule_info.mta_id=dd.dd_id and dd.settingType='Daily'";
				$sth = $dbhq->prepare($sql);
				$sth->execute($stype, $i, $client_id);
				($brand_name,$tstatus,$dd_name,$performance,$sourceURL) = $sth->fetchrow_array();
				$sth->finish();
				if (($tstatus eq "D") or ($tstatus eq ""))
				{
					$brand_name="Deleted";
					$j++;
					$j=7;
					next;
				}
				#my $slotcnt;
				#$sql="select count(*) from campaign,camp_schedule_info,client_brand_info,daily_deals where camp_schedule_info.client_id=? and camp_schedule_info.slot_id=? and slot_type=? and campaign.campaign_id=camp_schedule_info.campaign_id and campaign.brand_id=client_brand_info.brand_id and daily_deals.campaign_id=campaign.campaign_id and daily_deals.client_id=? and camp_schedule_info.status='A'";
				#$sth = $dbhq->prepare($sql);
				#$sth->execute($client_id,$i,$stype,$client_id);
				#($slotcnt) = $sth->fetchrow_array();
				#$sth->finish();
				#if ($slotcnt == 0)
				#{
				#	$j=7;
				#	next;
				#}
				print "<tr class=\"priority$performance\">";
				print "<td><font size=1>$brand_name<br>$dd_name<br><b>URL:</b>$sourceURL</font></td>";
				$rowcnt++;
			}
#			if ($cday != ($j+1))
#			{
#				print "<td></td>\n";
#				$j++;
#				next;
#			}
			my $cday = $j + 1;
			$sql = "select campaign_name,advertiser_id,campaign.campaign_id,client_brand_info.brand_name from campaign,camp_schedule_info,client_brand_info,daily_deals where camp_schedule_info.client_id=$client_id and camp_schedule_info.slot_id=$i and slot_type='$stype' and campaign.campaign_id=camp_schedule_info.campaign_id and campaign.brand_id=client_brand_info.brand_id and daily_deals.campaign_id=campaign.campaign_id and daily_deals.client_id=$client_id and daily_deals.cday=$cday and camp_schedule_info.status='A'"; 
			$sth = $dbhq->prepare($sql);
			$sth->execute();
			if (($cname,$temp_aid,$camp_id,$camp_brand) = $sth->fetchrow_array())
			{
				$sth->finish();
				$sql="select test_flag from advertiser_info where advertiser_id=?";
				$sth = $dbhq->prepare($sql);
				$sth->execute($temp_aid);
				($test_flag)=$sth->fetchrow_array();
				$sth->finish();
				$cname=~s/\(StrongMail\)//;
				if ($test_flag eq "Y")
				{
					print "<td><input type=\"checkbox\" value=\"${client_id}_${i}_${cday}\" name=\"chkbox\">&nbsp;&nbsp<font face=\"Verdana\" size=1><a href=\"/cgi-bin/advertiser_disp2.cgi?pmode=U&puserid=$temp_aid\" target=\"_blank\"><b>$cname</b></a>&nbsp;<font color=\"#FF00000\" face=\"Verdana\" size=\"1\"><font color=\"#FF00000\" face=\"Verdana\" size=\"1\"><b><a href=\"/cgi-bin/camp_del.cgi?campaign_id=$camp_id\" target=_blank>D</a></b>"; 
				}
				else
				{
					print "<td><input type=\"checkbox\" value=\"${client_id}_${i}_${cday}\" name=\"chkbox\">&nbsp;&nbsp<font face=\"Verdana\" size=1><a href=\"/cgi-bin/advertiser_disp2.cgi?pmode=U&puserid=$temp_aid\" target=\"_blank\">$cname</a>&nbsp;<font color=\"#FF00000\" face=\"Verdana\" size=\"1\"><font color=\"#FF00000\" face=\"Verdana\" size=\"1\"><b><a href=\"/cgi-bin/camp_del.cgi?campaign_id=$camp_id\" target=_blank>D</a></b>"; 
				}
				print "</font>\n";
				if ($camp_brand ne $brand_name)
				{
					print "&nbsp;<font color=red><b>$camp_brand</b></font>";
				}
				print "</td>\n";
				#
				# Check for advertiser rotations
				#
				my $temp_cnt;
			}
			else
			{
				$sth->finish();
				if ($tstatus eq "A")
				{
#					print "<td><font size=\"1\"><input type=\"checkbox\" value=\"${client_id}_${i}_${j}\" name=\"chkbox\"></font>&nbsp;&nbsp;<a href=\"schedule_advertiser.cgi?stype=$stype&xid=${client_id}_${i}_${j}\" target=\"_blank\"><font size=1 face=\"Verdana\" color=\"#FF0000\">Schedule</font></a></td>\n";
					print "<td><font size=\"1\"><input type=\"checkbox\" value=\"${client_id}_${i}_${cday}\" name=\"chkbox\"></font>&nbsp;&nbsp;<font size=1 face=\"Verdana\" color=\"#FF0000\">Schedule</font></td>\n";
				}
				else
				{
					print "<td><font size=\"1\"></font></td>\n";
				}
			}
			$j++;
		}
		if ($gotprofile == 1)
		{
			print "</tr>";
		}
		$i++;
	}
}
$sth1->finish();
print<<"end_of_html";
	</table>

<p align="center">
						<a href="/cgi-bin/mainmenu.cgi" target="_top">
						<img src="/images/cancel.gif" border="0"></a><img height="1" src="/images/spacer.gif" width="40" border="0">
						<input type="image" src="/images/save.gif" border="0" name="I1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="image" src="/images/remove.gif" border=0 name="Delete">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<a href="/cgi-bin/mainmenu.cgi" target="_top">
						<img border="0" src="/images/home_blkline.gif" width="76" height="23"></a></p>
</form>
</div>
</body>

</html>
end_of_html
