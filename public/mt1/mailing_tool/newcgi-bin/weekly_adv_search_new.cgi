#!/usr/bin/perl
#===============================================================================
# Purpose: Bottom frame of weekly.html page 
# Name   : weekly_adv_search.cgi 
#
#--Change Control---------------------------------------------------------------
# 08/08/05  Jim Sobeck  Creation
# 02/03/06	Jim Sobeck	Added display by network
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
my $adv_id= $query->param('adv_id');
if ($adv_id eq "")
{
	$adv_id=0;
}
my $startdate = $query->param('sdate');
my $nid=$query->param('nid');
if ($nid eq "")
{
	my @raw_cookies;
	my %cookies;
	my $key;
	my $val;
	@raw_cookies = split (/; /,$ENV{'HTTP_COOKIE'});
	foreach (@raw_cookies)
	{
    	($key, $val) = split (/=/,$_);
    	$cookies{$key} = $val;
	}
	$nid = $cookies{'networkopt'};
}
my $stype=$query->param('stype');
if ($stype eq "")
{
	$stype="C";
}
my $advertiser_name;
my $sdate;
my $edate;
my $cdate;
my $startdate1;
my $cday;
my $cday_sav;
my $tday;
my $client_id;
my $company;
my $camp_cnt;
my $aol_cnt;
my $third_cnt;
my $chunk_cnt;
my $daily_cnt;
my $cname;
my $auto_lock;
my $auto_lock_str;
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
#------  connect to the util database -----------
###$util->db_connect();

my ($dbhq,$dbhu)=$util->get_dbh();
###$dbh = $util->get_dbh;
#
$sql = "select advertiser_name from advertiser_info where advertiser_id=?";
$sth = $dbhq->prepare($sql);
$sth->execute($adv_id);
($advertiser_name) = $sth->fetchrow_array();
$sth->finish();
#
if ($startdate eq "")
{
	$sql="select date_format(date_sub(curdate(),interval dayofweek(curdate())-2 day),'%m/%d/%Y'),date_sub(curdate(),interval dayofweek(curdate())-2 day)";
	$sth = $dbhq->prepare($sql);
	$sth->execute();
	($sdate,$cday) = $sth->fetchrow_array();
	$sth->finish();
	$sql="select date_format(date_add(curdate(),interval 8-dayofweek(curdate()) day),'%m/%d/%Y')";
	$sth = $dbhq->prepare($sql);
	$sth->execute();
	($edate) = $sth->fetchrow_array();
	$sth->finish();
	$sql="select date_add(curdate(),interval 9-dayofweek(curdate()) day)";
	$sth = $dbhq->prepare($sql);
	$sth->execute();
	($startdate) = $sth->fetchrow_array();
	$sth->finish();
	$sql="select date_sub(curdate(),interval 7+dayofweek(curdate())-2 day)";
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
<title>Weekly</title>
<link rel="stylesheet" type="text/css" href= "/stylesheet.css" />
</head>
<body>
<div id="container">
<script language="JavaScript">
function set_action(x)
{
	document.saveform.action.value=x;
}
function setadvid(x)
{
	document.saveform.adv_id.value=document.mainform.adv_id.value;
}
function chkadv()
{
	if (document.saveform.adv_id.value == 0)
	{
		alert('You must select an advertiser before selecting save');
		return false;
	}
	return true;
}
</script>
<form method=post name=mainform action="/cgi-bin/weekly_adv_search_new.cgi">
<table class="tbl top" border="1" cellspacing="3" id="table6">
            <tr>
        <td class="advertiser"><strong>Advertiser: </strong></td>
        <td class="dropdown"><label><select name="adv_id" id="adv_id" onChange="setadvid();">
end_of_html
$sql = "select distinct advertiser_info.advertiser_id,advertiser_name from advertiser_info where advertiser_info.status='A'";
if ($stype eq "3")
{
    $sql=$sql." and advertiser_info.allow_strongmail='Y' ";
}
$sql = $sql . " order by advertiser_name";
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($id,$company) = $sth->fetchrow_array())
{
	if ($adv_id == $id)
	{
    	print "<option selected value=$id>$company</option>\n";
	}
	else
	{
    	print "<option value=$id>$company</option>\n";
	}
}
$sth->finish();
print<<"end_of_html";
</select></label></td>
        <td class="blank">
<!--        <input type="submit" value="Submit" name="B27"> --></td>
            </tr>
            </table>
<input type=hidden name=sdate value="$cday">
<input type=hidden name=stype value="$stype">
        <div class="date margin_top flL">
	Client:&nbsp;<select name=nid>
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

$cday_sav=$cday;
print<<"end_of_html";
</select>&nbsp;<input type=submit value="Go">
</div>
<div class="paging margin_top flR">
<a href="/cgi-bin/weekly_adv_search_new.cgi?adv_id=$adv_id&sdate=$startdate1&nid=$nid&stype=$stype">BACK</a>&nbsp;<span class="date">$sdate-$edate</span>&nbsp;&nbsp;<a href="/cgi-bin/weekly_adv_search_new.cgi?adv_id=$adv_id&sdate=$startdate&nid=$nid&stype=$stype">FWD</a>
</div>
</form>
<div class="clear"></div>
<form method=post name=saveform action="/cgi-bin/save_schedule_new.cgi" onSubmit="return chkadv();">
<input type=hidden name=adv_id value=$adv_id>
<input type=hidden name=startdate value="$cday">
<input type=hidden name=nid value="$nid"> 
<input type=hidden name=stype value="$stype">
<input type=hidden name=action value="">
<table class="tbl schedule" border="1" cellspacing="3">
end_of_html
#
# Get networks
#
my $cday1=$cday;
if ($nid > 0)
{
$sql = "select client_id,first_name,client_type,campaign_cnt,3rdparty_cnt,aol_cnt,chunk_cnt,daily_cnt from network_schedule, user where client_id=user_id and client_id=$nid order by client_id";
}
else
{
$sql = "select client_id,first_name,client_type,campaign_cnt,3rdparty_cnt,aol_cnt,chunk_cnt,daily_cnt from network_schedule, user where client_id=user_id and client_id != 287 order by client_id";
}
$sth1 = $dbhq->prepare($sql);
$sth1->execute();
my $client_type;
while (($client_id,$company,$client_type,$camp_cnt,$third_cnt,$aol_cnt,$chunk_cnt,$daily_cnt) = $sth1->fetchrow_array())
{
$cday=$cday1;
$tday=$cday1;
$sql="select day('$cday'),date_add('$cday',interval 1 day)";
$sth = $dbhq->prepare($sql);
$sth->execute();
($tday,$cday) = $sth->fetchrow_array();
$sth->finish();
print<<"end_of_html";
	<tr>
		<th><font face="Verdana" style="font-weight: 700" size="1"> <a href="/cgi-bin/upd_client_schedule.cgi?client_id=$client_id" target="_blank">$company</a><br>($client_type)</font>
end_of_html
if ($stype ne "3")
{
print<<"end_of_html";
		<br><font face="Verdana" size="1" style="font-weight: 700">Brand - Profile</font></th>
end_of_html
}
else
{
print<<"end_of_html";
		<br><font face="Verdana" size="1" style="font-weight: 700">Brand - Profile</font></th>
end_of_html
}
print<<"end_of_html";
		<th>$tday - Monday</th>
end_of_html
$sql="select day('$cday'),date_add('$cday',interval 1 day)";
$sth = $dbhq->prepare($sql);
$sth->execute();
($tday,$cday) = $sth->fetchrow_array();
$sth->finish();
print<<"end_of_html";
		<th>$tday - Tuesday</th>
end_of_html
$sql="select day('$cday'),date_add('$cday',interval 1 day)";
$sth = $dbhq->prepare($sql);
$sth->execute();
($tday,$cday) = $sth->fetchrow_array();
$sth->finish();

print<<"end_of_html";
		<th>$tday - Wednesday</th>
end_of_html
$sql="select day('$cday'),date_add('$cday',interval 1 day)";
$sth = $dbhq->prepare($sql);
$sth->execute();
($tday,$cday) = $sth->fetchrow_array();
$sth->finish();
print<<"end_of_html";
		<th>$tday - Thursday</th>
end_of_html
$sql="select day('$cday'),date_add('$cday',interval 1 day)";
$sth = $dbhq->prepare($sql);
$sth->execute();
($tday,$cday) = $sth->fetchrow_array();
$sth->finish();
print<<"end_of_html";
		<th>$tday - Friday</th>
end_of_html
$sql="select day('$cday'),date_add('$cday',interval 1 day)";
$sth = $dbhq->prepare($sql);
$sth->execute();
($tday,$cday) = $sth->fetchrow_array();
$sth->finish();
print<<"end_of_html";
		<th>$tday - Saturday</th>
end_of_html
$sql="select day('$cday'),date_add('$cday',interval 1 day)";
$sth = $dbhq->prepare($sql);
$sth->execute();
($tday,$cday) = $sth->fetchrow_array();
$sth->finish();
print<<"end_of_html";
		<th>$tday - Sunday</th>
	</tr>
end_of_html
	my $i = 1;
	if ($stype eq "3")
	{
		$camp_cnt=$third_cnt;
	}
	elsif ($stype eq "A")
	{
		$camp_cnt=$aol_cnt;
	}
	elsif ($stype eq "W")  # W - Whitelisting/Chunking
	{
		$camp_cnt=$chunk_cnt;
	}
	elsif ($stype eq "D")  # W - Whitelisting/Chunking
	{
		$camp_cnt=$daily_cnt;
	}
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
				if ($stype ne "3")
				{
					$sql="select profile_name,schedule_info.status,brand_name,schedule_info.performance,schedule_info.schedule_time from list_profile,schedule_info,client_brand_info where list_profile.profile_id=schedule_info.profile_id and slot_type=? and slot_id=? and schedule_info.client_id=? and schedule_info.brand_id=client_brand_info.brand_id";
					$sth = $dbhq->prepare($sql);
					$sth->execute($stype, $i, $client_id);
					($profile_name,$tstatus,$brand_name,$performance,$ttime) = $sth->fetchrow_array();
					$sth->finish();
					$profile_name = $profile_name . " - " . $brand_name;
				}
				else
				{
					$sql="select mailer_name,brand_name,schedule_info.status,profile_name,schedule_info.performance,schedule_info.schedule_time from third_party_defaults,schedule_info,client_brand_info,list_profile where third_party_defaults.third_party_id=schedule_info.third_party_id and slot_type='3' and slot_id=? and schedule_info.client_id=? and schedule_info.brand_id=client_brand_info.brand_id and schedule_info.profile_id=list_profile.profile_id";
					$sth = $dbhq->prepare($sql);
					$sth->execute($i, $client_id);
					my $mailer_name;
					($mailer_name,$brand_name,$tstatus,$profile_name,$performance,$ttime) = $sth->fetchrow_array();
					$sth->finish();
					if ($mailer_name eq "StrongMail")
					{
						$profile_name = $brand_name . " - " . $profile_name; 
					}
					else
					{
						$profile_name = $mailer_name . " - " . $brand_name . " - " . $profile_name; 
					}
				}
				if ($tstatus eq "D")
				{
					$profile_name="Deleted";
					$j++;
					next;
				}
				$gotprofile=1;
				print "<tr class=\"priority$performance\">";
				if ($ttime eq "24:00:00")
				{
					$ttime="12:00:00";
				}
				print "<td><font size=1>$profile_name<br>$ttime</font></td>";
				$rowcnt++;
			}
			if ($stype ne "3")
			{
				$sql = "select campaign_name,advertiser_id,campaign.campaign_id,client_brand_info.brand_name,auto_lock from campaign,camp_schedule_info,client_brand_info where camp_schedule_info.client_id=$client_id and camp_schedule_info.slot_id=$i and slot_type='$stype' and camp_schedule_info.schedule_date=date_add('$cday1',interval $j day) and campaign.campaign_id=camp_schedule_info.campaign_id and campaign.brand_id=client_brand_info.brand_id and camp_schedule_info.status='A'"; 
			}
			else
			{
				$sql = "select campaign_name,advertiser_id,campaign.campaign_id,brand_name,auto_lock from campaign,camp_schedule_info,client_brand_info where camp_schedule_info.client_id=$client_id and camp_schedule_info.slot_id=$i and slot_type='3' and camp_schedule_info.schedule_date=date_add('$cday1',interval $j day) and campaign.campaign_id=camp_schedule_info.campaign_id and deleted_date is null and campaign.brand_id=client_brand_info.brand_id and camp_schedule_info.status='A'";
			}
			$sth = $dbhq->prepare($sql);
			$sth->execute();
			if (($cname,$temp_aid,$camp_id,$camp_brand,$auto_lock) = $sth->fetchrow_array())
			{
				$sth->finish();
				$sql="select test_flag from advertiser_info where advertiser_id=?";
				$sth = $dbhq->prepare($sql);
				$sth->execute($temp_aid);
				($test_flag)=$sth->fetchrow_array();
				$sth->finish();
				$cname=~s/\(StrongMail\)//;
				if ($auto_lock eq "N")
				{
					$auto_lock="Y";
					$auto_lock_str="C";
				}
				else
				{
					$auto_lock="N";
					$auto_lock_str="U";
				}
				$sql = "select datediff(date_add('$cday1',interval $j day),curdate())";
                $sth1a = $dbhq->prepare($sql) ;
                $sth1a->execute();
                ($diffcnt) = $sth1a->fetchrow_array();
                $sth1a->finish();
				if ($diffcnt >= 0)
				{
					if ($stype ne "3")
					{
						if ($test_flag eq "Y")
						{
							print "<td width=171><input type=\"checkbox\" value=\"${client_id}_${i}_${j}\" name=\"chkbox\">&nbsp;&nbsp<font face=\"Verdana\" size=1><a href=\"/cgi-bin/advertiser_disp2.cgi?pmode=U&puserid=$temp_aid\" target=\"_blank\"><b>$cname</b></a>&nbsp;<font color=\"#FF00000\" face=\"Verdana\" size=\"1\"><font color=\"#FF00000\" face=\"Verdana\" size=\"1\"><b><a href=\"/cgi-bin/camp_del.cgi?campaign_id=$camp_id\" target=_blank>D</a></b>"; 
						}
						else
						{
							print "<td width=171><input type=\"checkbox\" value=\"${client_id}_${i}_${j}\" name=\"chkbox\">&nbsp;&nbsp<font face=\"Verdana\" size=1><a href=\"/cgi-bin/advertiser_disp2.cgi?pmode=U&puserid=$temp_aid\" target=\"_blank\">$cname</a>&nbsp;<font color=\"#FF00000\" face=\"Verdana\" size=\"1\"><font color=\"#FF00000\" face=\"Verdana\" size=\"1\"><b><a href=\"/cgi-bin/camp_del.cgi?campaign_id=$camp_id\" target=_blank>D</a></b>"; 
						}
					}
					else
					{
						my $temp_cnt;
						if ($test_flag eq "Y")
						{
							print "<td width=171><input type=\"checkbox\" value=\"${client_id}_${i}_${j}\" name=\"chkbox\">&nbsp;&nbsp<font face=\"Verdana\" size=1><a href=\"/cgi-bin/advertiser_disp2.cgi?pmode=U&puserid=$temp_aid\" target=\"_blank\"><b>$cname</b></a>&nbsp;<font color=\"#FF00000\" face=\"Verdana\" size=\"1\">&nbsp;<font color=\"#FF00000\" face=\"Verdana\" size=\"1\"><b><a href=\"/cgi-bin/camp_del.cgi?campaign_id=$camp_id\" target=_blank>D</a></b>"; 
						}
						else
						{
							print "<td width=171><input type=\"checkbox\" value=\"${client_id}_${i}_${j}\" name=\"chkbox\">&nbsp;&nbsp<font face=\"Verdana\" size=1><a href=\"/cgi-bin/advertiser_disp2.cgi?pmode=U&puserid=$temp_aid\" target=\"_blank\">$cname</a>&nbsp;<font color=\"#FF00000\" face=\"Verdana\" size=\"1\">&nbsp;<font color=\"#FF00000\" face=\"Verdana\" size=\"1\"><b><a href=\"/cgi-bin/camp_del.cgi?campaign_id=$camp_id\" target=_blank>D</a></b>"; 
						}
					}
				}
				else
				{
					if ($stype ne "3")
					{
						if ($test_flag eq "Y")
						{
							print "<td width=171><font face=\"Verdana\" size=1><a href=\"/cgi-bin/advertiser_disp2.cgi?pmode=U&puserid=$temp_aid\" target=\"_blank\"><b>$cname</b></a>&nbsp;<font color=\"#FF00000\" face=\"Verdana\" size=\"1\"><b><a href=\"/cgi-bin/camp_history.cgi?campaign_id=$camp_id\" target=_blank>H</a></b>";
						}
						else
						{
							print "<td width=171><font face=\"Verdana\" size=1><a href=\"/cgi-bin/advertiser_disp2.cgi?pmode=U&puserid=$temp_aid\" target=\"_blank\">$cname</a>&nbsp;<font color=\"#FF00000\" face=\"Verdana\" size=\"1\"><b><a href=\"/cgi-bin/camp_history.cgi?campaign_id=$camp_id\" target=_blank>H</a></b>";
						}
					}
					else
					{
						my $temp_cnt;
						if ($test_flag eq "Y")
						{
							print "<td width=171><font face=\"Verdana\" size=1><a href=\"/cgi-bin/advertiser_disp2.cgi?pmode=U&puserid=$temp_aid\" target=\"_blank\"><b>$cname</b></a>&nbsp;<font color=\"#FF00000\" face=\"Verdana\" size=\"1\"><b>&nbsp;<font color=\"#FF00000\" face=\"Verdana\" size=\"1\"><b><a href=\"/cgi-bin/camp_history.cgi?campaign_id=$camp_id\" target=_blank>H</a></b>"; 
						}
						else
						{
							print "<td width=171><font face=\"Verdana\" size=1><a href=\"/cgi-bin/advertiser_disp2.cgi?pmode=U&puserid=$temp_aid\" target=\"_blank\">$cname</a>&nbsp;<font color=\"#FF00000\" face=\"Verdana\" size=\"1\"><b>&nbsp;<font color=\"#FF00000\" face=\"Verdana\" size=\"1\"><b><a href=\"/cgi-bin/camp_history.cgi?campaign_id=$camp_id\" target=_blank>H</a></b>"; 
						}
					}
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
				$sql = "select datediff(date_add('$cday1',interval $j day),curdate())";
                $sth1a = $dbhq->prepare($sql) ;
                $sth1a->execute();
                ($diffcnt) = $sth1a->fetchrow_array();
                $sth1a->finish();
				if (($diffcnt >= 0) && ($tstatus eq "A"))
				{
					print "<td width=\"171\"><font size=\"1\"><input type=\"checkbox\" value=\"${client_id}_${i}_${j}\" name=\"chkbox\"></font>&nbsp;&nbsp;<a href=\"schedule_advertiser.cgi?stype=$stype&xid=${client_id}_${i}_${j}&startdate=$cday_sav\" target=\"_blank\"><font size=1 face=\"Verdana\" color=\"#FF0000\">Schedule</font></td>\n";
				}
				else
				{
					print "<td width=\"171\"><font size=\"1\"></font></td>\n";
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
						<input type="image" src="/images/save.gif" border="0" name="I1" onClick="javascript:set_action('Save');">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<a href="/cgi-bin/mainmenu.cgi" target="_top">
						<img border="0" src="/images/home_blkline.gif" width="76" height="23"></a></p>
</form>
</div>
</body>

</html>
end_of_html
