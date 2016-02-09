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
my $brand_name;
my $phone;
my $email;
my $id;
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
my $tday;
my $client_id;
my $company;
my $camp_cnt;
my $aol_cnt;
my $third_cnt;
my $cname;
my $camp_id;
my $temp_aid;
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
$util->db_connect();
$dbh = $util->get_dbh;
#
$sql = "select advertiser_name from advertiser_info where advertiser_id=$adv_id";
$sth = $dbh->prepare($sql);
$sth->execute();
($advertiser_name) = $sth->fetchrow_array();
$sth->finish();
#
if ($startdate eq "")
{
	$sql="select date_format(date_sub(curdate(),interval dayofweek(curdate())-1 day),'%m/%d/%Y'),date_sub(curdate(),interval dayofweek(curdate())-1 day)";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	($sdate,$cday) = $sth->fetchrow_array();
	$sth->finish();
	$sql="select date_format(date_add(curdate(),interval 7-dayofweek(curdate()) day),'%m/%d/%Y')";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	($edate) = $sth->fetchrow_array();
	$sth->finish();
	$sql="select date_add(curdate(),interval 8-dayofweek(curdate()) day)";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	($startdate) = $sth->fetchrow_array();
	$sth->finish();
	$sql="select date_sub(curdate(),interval 7+dayofweek(curdate())-1 day)";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	($startdate1) = $sth->fetchrow_array();
	$sth->finish();
}
else
{
	$cday = $startdate;
	$sql="select date_format('$startdate','%m/%d/%Y')";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	($sdate) = $sth->fetchrow_array();
	$sth->finish();
	$sql="select date_format(date_add('$startdate',interval 6 day),'%m/%d/%Y')";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	($edate) = $sth->fetchrow_array();
	$sth->finish();
	$sql="select date_sub('$startdate',interval 7 day)";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	($startdate1) = $sth->fetchrow_array();
	$sth->finish();
	$sql="select date_add('$startdate',interval 7 day)";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	($startdate) = $sth->fetchrow_array();
	$sth->finish();
}
$sql="select date_format(curdate(),'%m/%d/%Y')";
$sth = $dbh->prepare($sql);
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
</head>
<body>
<script language="JavaScript">
parent.middle.set_sdate('$cday');
</script>
<form method=get action="/cgi-bin/weekly_adv_search.cgi">
<input type=hidden name=adv_id value=$adv_id>
<input type=hidden name=sdate value="$cday">
<input type=hidden name=stype value="$stype">
<table border="0" width="100%" id="table3">
	<tr>
		<td><font face="Verdana"><b>Schedule:</b></font><b><font face="Verdana">&nbsp; <a href="/cgi-bin/advertiser_disp2.cgi?pmode=U&puserid=$adv_id" target="_blank">$advertiser_name</a>
		</font></b></td>
		<td>
		<p align="right"><b><font face="Verdana">Today is $cdate</font></b></td>
	</tr>
	<tr>
	<td><font face="Verdana"><b>Client:&nbsp;</b></font><select name=nid>
<option value=0 selected>ALL</option>
end_of_html
$sql="select user_id,company from user where status='A' order by company";
$sth = $dbh->prepare($sql);
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
</select>&nbsp;<input type=submit value="Go"></td></tr>
</table>
</form>
<form method=get action="/cgi-bin/save_schedule.cgi">
<input type=hidden name=adv_id value=$adv_id>
<input type=hidden name=startdate value="$cday">
<input type=hidden name=nid value="$nid"> 
<input type=hidden name=stype value="$stype">
<table border="0" width="100%" id="table7">
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
		<p align="right"><b><font face="Verdana">&nbsp;<a href="/cgi-bin/weekly_adv_search.cgi?adv_id=$adv_id&sdate=$startdate1&nid=$nid&stype=$stype">BACK</a>&nbsp;
		<a href="/cgi-bin/weekly_adv_search.cgi?adv_id=$adv_id&sdate=$startdate&nid=$nid&stype=$stype">FWD</a></font></b></td>
	</tr>
</table>
<table border="1" width="100%" id="table1">
end_of_html
#
# Get networks
#
my $cday1=$cday;
if ($nid > 0)
{
$sql = "select client_id,company,campaign_cnt,3rdparty_cnt,aol_cnt from network_schedule, user where client_id=user_id and client_id=$nid order by client_id";
}
else
{
$sql = "select client_id,company,campaign_cnt,3rdparty_cnt,aol_cnt from network_schedule, user where client_id=user_id order by client_id";
}
$sth1 = $dbh->prepare($sql);
$sth1->execute();
while (($client_id,$company,$camp_cnt,$third_cnt,$aol_cnt) = $sth1->fetchrow_array())
{
$cday=$cday1;
$tday=$cday1;
$sql="select day('$cday'),date_add('$cday',interval 1 day)";
$sth = $dbh->prepare($sql);
$sth->execute();
($tday,$cday) = $sth->fetchrow_array();
$sth->finish();
print<<"end_of_html";
	<tr>
		<td width="100"><font face="Verdana" style="font-weight: 700" size="1"> <a href="/cgi-bin/upd_client_schedule.cgi?client_id=$client_id" target="_blank">$company</a></font></td>
		<td width="17"> &nbsp;</td>
		<td width="178"> &nbsp;</td>
		<td width="17">
											&nbsp;</td>
		<td>&nbsp;</td>
		<td width="17">
											&nbsp;</td>
		<td width="171">&nbsp;</td>
		<td width="17">
											&nbsp;</td>
		<td width="167">&nbsp;</td>
		<td width="17">
											&nbsp;</td>
		<td width="165">&nbsp;</td>
		<td width="17">
											&nbsp;</td>
		<td width="165">&nbsp;</td>
		<td width="17">
											&nbsp;</td>
		<td width="165">&nbsp;</td>
	</tr>
	<tr>
end_of_html
if ($stype ne "3")
{
print<<"end_of_html";
		<td width="100"><font face="Verdana" size="1" style="font-weight: 700">Profile - Brand</font></td>
end_of_html
}
else
{
print<<"end_of_html";
		<td width="100"><font face="Verdana" size="1" style="font-weight: 700">Mailer - Brand - Profile</font></td>
end_of_html
}
print<<"end_of_html";
		<td width="17"><font face="Verdana" style="font-weight: 700" size="1"> 
											$tday</font></td>
		<td width="178">
											<span style="font-weight: 700">
											<font face="Verdana" size="1">Sunday</font></span></td>
end_of_html
$sql="select day('$cday'),date_add('$cday',interval 1 day)";
$sth = $dbh->prepare($sql);
$sth->execute();
($tday,$cday) = $sth->fetchrow_array();
$sth->finish();
print<<"end_of_html";
		<td width="17"><font face="Verdana" style="font-weight: 700" size="1"> 
											$tday</font></td>
		<td width="178">
											<span style="font-weight: 700">
											<font face="Verdana" size="1">Monday</font></span></td>
		<td width="17"><font face="Verdana" style="font-weight: 700" size="1"> 
end_of_html
$sql="select day('$cday'),date_add('$cday',interval 1 day)";
$sth = $dbh->prepare($sql);
$sth->execute();
($tday,$cday) = $sth->fetchrow_array();
$sth->finish();
print<<"end_of_html";
											$tday</font></td>
		<td><span style="font-weight: 700"><font face="Verdana" size="1">Tuesday</font></span></td>
		<td width="17"><font face="Verdana" style="font-weight: 700" size="1"> 
end_of_html
$sql="select day('$cday'),date_add('$cday',interval 1 day)";
$sth = $dbh->prepare($sql);
$sth->execute();
($tday,$cday) = $sth->fetchrow_array();
$sth->finish();

print<<"end_of_html";
											$tday</font></td>
		<td width="171"><span style="font-weight: 700">
		<font face="Verdana" size="1">Wednesday</font></span></td>
		<td width="17"><font face="Verdana" style="font-weight: 700" size="1"> 
end_of_html
$sql="select day('$cday'),date_add('$cday',interval 1 day)";
$sth = $dbh->prepare($sql);
$sth->execute();
($tday,$cday) = $sth->fetchrow_array();
$sth->finish();
print<<"end_of_html";
											$tday</font></td>
		<td width="167"><span style="font-weight: 700">
		<font face="Verdana" size="1">Thursday</font></span></td>
		<td width="17"><font face="Verdana" style="font-weight: 700" size="1"> 
end_of_html
$sql="select day('$cday'),date_add('$cday',interval 1 day)";
$sth = $dbh->prepare($sql);
$sth->execute();
($tday,$cday) = $sth->fetchrow_array();
$sth->finish();
print<<"end_of_html";
											$tday</font></td>
		<td width="165"><span style="font-weight: 700">
		<font face="Verdana" size="1">Friday</font></span></td>
		<td width="17">
end_of_html
$sql="select day('$cday'),date_add('$cday',interval 1 day)";
$sth = $dbh->prepare($sql);
$sth->execute();
($tday,$cday) = $sth->fetchrow_array();
$sth->finish();
print<<"end_of_html";
											<font face="Verdana" style="font-weight: 700" size="1"> 
											$tday</font></td>
		<td width="165"><span style="font-weight: 700">
		<font face="Verdana" size="1">Saturday</font></span></td>
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
	while ($i <= $camp_cnt)
	{
		print "<tr>";
		my $j = 0;
		while ($j < 7)
		{
			if ($j == 0)
			{
				my $tstatus;
				if ($stype ne "3")
				{
					$sql="select profile_name,schedule_info.status,brand_name from list_profile,schedule_info,client_brand_info where list_profile.profile_id=schedule_info.profile_id and slot_type='$stype' and slot_id=$i and schedule_info.client_id=$client_id and schedule_info.brand_id=client_brand_info.brand_id";
					$sth = $dbh->prepare($sql);
					$sth->execute();
					($profile_name,$tstatus,$brand_name) = $sth->fetchrow_array();
					$sth->finish();
					$profile_name = $profile_name . " - " . $brand_name;
				}
				else
				{
					$sql="select mailer_name,brand_name,schedule_info.status,profile_name from third_party_defaults,schedule_info,client_brand_info,list_profile where third_party_defaults.third_party_id=schedule_info.third_party_id and slot_type='3' and slot_id=$i and schedule_info.client_id=$client_id and schedule_info.brand_id=client_brand_info.brand_id and schedule_info.profile_id=list_profile.profile_id";
					$sth = $dbh->prepare($sql);
					$sth->execute();
					my $mailer_name;
					($mailer_name,$brand_name,$tstatus,$profile_name) = $sth->fetchrow_array();
					$sth->finish();
					$profile_name = $mailer_name . " - " . $brand_name . " - " . $profile_name; 
				}
				if ($tstatus eq "D")
				{
					$profile_name="Deleted";
				}
				print "<td><font size=1>$profile_name</font></td>";
				print "<td width=17 align=middle><font size=1></font></td>\n";
			}
			else
			{
				print "<td width=17 align=middle><font size=1></font></td>\n";
			}
			if ($stype ne "3")
			{
				$sql = "select campaign_name,advertiser_id,campaign.campaign_id from campaign,camp_schedule_info where client_id=$client_id and camp_schedule_info.slot_id=$i and slot_type='$stype' and camp_schedule_info.schedule_date=date_add('$cday1',interval $j day) and campaign.campaign_id=camp_schedule_info.campaign_id";
			}
			else
			{
				$sql = "select campaign_name,advertiser_id,campaign.campaign_id from campaign,camp_schedule_info where client_id=$client_id and camp_schedule_info.slot_id=$i and slot_type='3' and camp_schedule_info.schedule_date=date_add('$cday1',interval $j day) and campaign.campaign_id=camp_schedule_info.campaign_id and deleted_date is null";
			}
			$sth = $dbh->prepare($sql);
			$sth->execute();
			if (($cname,$temp_aid,$camp_id) = $sth->fetchrow_array())
			{
				$sth->finish();
				$sql = "select datediff(date_add('$cday1',interval $j day),curdate())";
                $sth1a = $dbh->prepare($sql) ;
                $sth1a->execute();
                ($diffcnt) = $sth1a->fetchrow_array();
                $sth1a->finish();
				if ($diffcnt >= 0)
				{
					if ($stype ne "3")
					{
						print "<td><font face=\"Verdana\" size=1><a href=\"/cgi-bin/advertiser_disp2.cgi?pmode=U&puserid=$temp_aid\" target=\"_blank\">$cname</a>&nbsp;<font color=\"#FF00000\" face=\"Verdana\" size=\"1\"><b><a href=\"/cgi-bin/show_campaign.cgi?campaign_id=$camp_id&aid=$temp_aid&mode=U\" target=_blank>E</a></b>&nbsp;<font color=\"#FF00000\" face=\"Verdana\" size=\"1\"><b><a href=\"/cgi-bin/camp_del.cgi?campaign_id=$camp_id\" target=_blank>D</a></b>"; 
					}
					else
					{
						my $temp_cnt;
                		$sql="select id from 3rdparty_campaign where campaign_id=$camp_id";
                		$sth1a = $dbh->prepare($sql) ;
                		$sth1a->execute();
                		($temp_cnt) = $sth1a->fetchrow_array();
						$sth1a->finish();

						print "<td><font face=\"Verdana\" size=1><a href=\"/cgi-bin/advertiser_disp2.cgi?pmode=U&puserid=$temp_aid\" target=\"_blank\">$cname</a>&nbsp;<font color=\"#FF00000\" face=\"Verdana\" size=\"1\"><b><a href=\"/cgi-bin/3rdparty_show_camp.cgi?campaign_id=$temp_cnt\" target=_blank>E</a></b>&nbsp;<font color=\"#FF00000\" face=\"Verdana\" size=\"1\"><b><a href=\"/cgi-bin/camp_del.cgi?campaign_id=$camp_id\" target=_blank>D</a></b>"; 
					}
				}
				else
				{
					if ($stype ne "3")
					{
						print "<td><font face=\"Verdana\" size=1><a href=\"/cgi-bin/advertiser_disp2.cgi?pmode=U&puserid=$temp_aid\" target=\"_blank\">$cname</a>&nbsp;<font color=\"#FF00000\" face=\"Verdana\" size=\"1\"><b><a href=\"/cgi-bin/camp_history.cgi?campaign_id=$camp_id\" target=_blank>H</a></b>";
					}
					else
					{
						my $temp_cnt;
                		$sql="select id from 3rdparty_campaign where campaign_id=$camp_id";
                		$sth1a = $dbh->prepare($sql) ;
                		$sth1a->execute();
                		($temp_cnt) = $sth1a->fetchrow_array();
						$sth1a->finish();
						print "<td><font face=\"Verdana\" size=1><a href=\"/cgi-bin/advertiser_disp2.cgi?pmode=U&puserid=$temp_aid\" target=\"_blank\">$cname</a>&nbsp;<font color=\"#FF00000\" face=\"Verdana\" size=\"1\"><b><a href=\"/cgi-bin/3rdparty_show_camp.cgi?campaign_id=$temp_cnt\" target=_blank>E</a></b>&nbsp;<font color=\"#FF00000\" face=\"Verdana\" size=\"1\"><b><a href=\"/cgi-bin/camp_del.cgi?campaign_id=$camp_id\" target=_blank>D</a></b>"; 
					}
				}
				print "</font>\n";
				#
				# Check for advertiser rotations
				#
				my $temp_cnt;
#				$sql = "select count(*) from advertiser_setup where advertiser_id=$temp_aid";
#    			$sth = $dbh->prepare($sql) ;
#    			$sth->execute();
#				($temp_cnt) = $sth->fetchrow_array();
#				$sth->finish();
#				if ($temp_cnt == 4)
#				{
#                	print "<font color=\"#FF0000\" face=\"Verdana\" size=\"1\"><b>&nbsp;<a href=\"/cgi-bin/advertiser_setup_new.cgi?aid=$temp_aid\" target=_blank>R</a></b></font>";
#				}
#				else
#				{
#                	print "<font color=\"red\" face=\"Verdana\" size=\"1\"><b>&nbsp;<a href=\"/cgi-bin/advertiser_setup_new.cgi?aid=$temp_aid\" target=_blank>R!</a></b></font>";
#				}
#			    $sql = "select list_name,last_updated,filedate,vendor_supp_list_id,datediff(curdate(),last_updated) from advertiser_info,vendor_supp_list_info where advertiser_info.advertiser_id=$temp_aid and advertiser_info.vendor_supp_list_id=vendor_supp_list_info.list_id";
#    			$sth = $dbh->prepare($sql) ;
#    			$sth->execute();
#    			if (($supp_name,$last_updated,$filedate,$sid,$day_cnt) = $sth->fetchrow_array())
#    			{
#    		    	if ($supp_name ne "NONE")
#            		{
#                		if ($filedate eq "")
#                		{
#                    		if ($day_cnt <= 7)
#                    		{
#                    			print "<font color=\"#FF0000\" face=\"Verdana\" size=\"1\"><b>&nbsp;<a href=\"/cgi-bin/supplist_addnames.cgi?tid=$sid\" target=_blank>S</a></b></font>";
#                    		}
#                    		else
#                    		{
#                    			print "&nbsp;<a href=\"/cgi-bin/supplist_addnames.cgi?tid=$sid\" target=_blank><font color=\"red\" face=\"Verdana\" size=\"1\"><b>S!</a></b></font>";
#                    		}
#                		}
#                		else
#                		{
#                    		$sql = "select datediff(curdate(),'$filedate')";
#                    		$sth1a = $dbh->prepare($sql) ;
#                    		$sth1a->execute();
#                    		($day_cnt) = $sth1a->fetchrow_array();
#                    		$sth1a->finish();
#                    		if ($day_cnt <= 7)
#                    		{
#                    			print "&nbsp;<font color=\"#FF0000\" face=\"Verdana\" size=\"1\"><b><a href=\"/cgi-bin/supplist_addnames.cgi?tid=$sid\" target=_blank>S</a></b></font>";
#                    		}
#                    		else
#                    		{
#                    			print "&nbsp;<a href=\"/cgi-bin/supplist_addnames.cgi?tid=$sid\" target=_blank><font color=\"red\" face=\"Verdana\" size=\"1\"><b>S!</a></b></font>";
#                    		}
#                    	}
#                    }
#                    print "</td>";
#    			}
			}
			else
			{
				$sth->finish();
				$sql = "select datediff(date_add('$cday1',interval $j day),curdate())";
                $sth1a = $dbh->prepare($sql) ;
                $sth1a->execute();
                ($diffcnt) = $sth1a->fetchrow_array();
                $sth1a->finish();
				if ($diffcnt >= 0)
				{
					print "<td width=\"171\"><font size=\"1\"><input type=\"checkbox\" value=\"${client_id}_${i}_${j}\" name=\"chkbox\"></font></td>\n";
				}
				else
				{
					print "<td width=\"171\"><font size=\"1\"></font></td>\n";
				}
			}
			$j++;
		}
		print "</tr>";
		$i++;
	}
}
$sth1->finish();
print<<"end_of_html";
	</table>

<p align="center">
						<a href="/cgi-bin/mainmenu.cgi" target="_top">
						<img src="/images/cancel.gif" border="0"></a><img height="1" src="/images/spacer.gif" width="40" border="0">
end_of_html
if ($adv_id > 0)
{
print<<"end_of_html";
						<input type="image" src="/images/save.gif" border="0" name="I1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
end_of_html
}
print<<"end_of_html";
						<a href="/cgi-bin/mainmenu.cgi" target="_top">
						<img border="0" src="/images/home_blkline.gif" width="76" height="23"></a></p>
</form>

</body>

</html>
end_of_html
