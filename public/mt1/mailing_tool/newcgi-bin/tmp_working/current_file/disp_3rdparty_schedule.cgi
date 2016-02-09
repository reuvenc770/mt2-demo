#!/usr/bin/perl
#===============================================================================
# Purpose: Bottom frame of 3rdparty_view_schedule.cgi page 
# Name   : disp_3rdparty_schedule.cgi 
#
#--Change Control---------------------------------------------------------------
# 02/09/06  Jim Sobeck  Creation
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
my $client_id;
my $sth1a;
my $dbh;
my $profile_name;
my $phone;
my $email;
my $id;
my $aim;
my $website;
my $username;
my $password;
my $tables;
my $clientid= $query->param('clientid');
my $startdate = $query->param('sdate');
my $advertiser_name;
my $sdate;
my $edate;
my $cdate;
my $startdate1;
my $cday;
my $tday;
my $company;
my $camp_cnt;
my @slot_id;
my @brand_id;
my @profile_id;
my $cname;
my $days_in_month;
my $tday1;
my $camp_id;
my $temp_cid;
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
if ($startdate eq "")
{
my $month_str;
my $year_str;

$sql = "select month(curdate()),year(curdate())";
$sth = $dbh->prepare($sql);
$sth->execute();
($month_str,$year_str) = $sth->fetchrow_array();
$sth->finish();
	$sdate=$month_str . "/01/" . $year_str;
	$cday=$year_str . "-" . $month_str . "-01";
	$sql="select date_format(date_sub(date_add('$cday',interval 1 month),interval 1 day),'%m/%d/%Y')";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	($edate) = $sth->fetchrow_array();
	$sth->finish();
	$sql="select date_add('$cday',interval 1 month)";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	($startdate) = $sth->fetchrow_array();
	$sth->finish();
	$sql="select date_sub('$cday',interval 1 month)";
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
	$sql="select date_format(date_sub(date_add('$startdate',interval 1 month),interval 1 day),'%m/%d/%Y')";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	($edate) = $sth->fetchrow_array();
	$sth->finish();
	$sql="select date_sub('$startdate',interval 1 month)";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	($startdate1) = $sth->fetchrow_array();
	$sth->finish();
	$sql="select date_add('$startdate',interval 1 month)";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	($startdate) = $sth->fetchrow_array();
	$sth->finish();
}
$sql="select datediff('$startdate','$cday')";
$sth = $dbh->prepare($sql);
$sth->execute();
($days_in_month) = $sth->fetchrow_array();
$sth->finish();
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
<form method=post action="/cgi-bin/save_3rdparty_schedule.cgi">
<input type=hidden name=clientid value=$clientid>
<input type=hidden name=startdate value="$cday">
<table border="0" width="100%" id="table3">
	<tr>
		<td></td>
		<td>
		<p align="right"><b><font face="Verdana">Today is $cdate</font></b></td>
	</tr>
</table>
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
		<p align="right"><b><font face="Verdana">&nbsp;<a href="/cgi-bin/disp_3rdparty_schedule.cgi?clientid=$clientid&sdate=$startdate1">BACK</a>&nbsp;
		<a href="/cgi-bin/disp_3rdparty_schedule.cgi?clientid=$clientid&sdate=$startdate">FWD</a></font></b></td>
	</tr>
</table>
end_of_html
#
# Get networks
#
my $cday1=$cday;
my $day_str;
$sql = "select distinct schedule_info.client_id,company from schedule_info, user where client_id=user_id and third_party_id=$clientid order by company";
$sth1 = $dbh->prepare($sql);
$sth1->execute();
while (($client_id,$company) = $sth1->fetchrow_array())
{
	$sql="select count(*) from schedule_info where client_id=$client_id and third_party_id=$clientid and slot_type='3' and status='A'";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	($camp_cnt) = $sth->fetchrow_array();
	$sth->finish();
#
#	Get slots, profiles, and brands and store off
#
	my $t=1;
	$sql = "select slot_id,profile_id,brand_id from schedule_info where client_id=$client_id and third_party_id=$clientid and slot_type='3' and status='A'";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	while (($slot_id[$t],$profile_id[$t],$brand_id[$t]) = $sth->fetchrow_array())
	{
		$t++;
	}
	$sth->finish;

#
$cday=$cday1;
$tday=$cday1;
$sql="select day('$cday'),dayname('$cday'),date_add('$cday',interval 1 day)";
$sth = $dbh->prepare($sql);
$sth->execute();
($tday,$day_str,$cday) = $sth->fetchrow_array();
$sth->finish();
print<<"end_of_html";
Network: <a href="/cgi-bin/upd_client_schedule.cgi?client_id=$client_id" target="_blank">$company<a/><br>
<table border="1" width="100%" id="table1">
end_of_html
	$tday1 = 0;
	while ($tday1 < $days_in_month)
	{
print<<"end_of_html";
	<tr>
		<td width="100"><font face="Verdana" size="1" style="font-weight: 700">Brand</font></td>
		<td width="17"><font face="Verdana" style="font-weight: 700" size="1"> 
											$tday</font></td>
		<td width="178">
											<span style="font-weight: 700">
											<font face="Verdana" size="1">$day_str</font></span></td>
end_of_html
$sql="select day('$cday'),dayname('$cday'),date_add('$cday',interval 1 day)";
$sth = $dbh->prepare($sql);
$sth->execute();
($tday,$day_str,$cday) = $sth->fetchrow_array();
$sth->finish();
print<<"end_of_html";
		<td width="17"><font face="Verdana" style="font-weight: 700" size="1"> 
											$tday</font></td>
		<td width="178">
											<span style="font-weight: 700">
											<font face="Verdana" size="1">$day_str</font></span></td>
		<td width="17"><font face="Verdana" style="font-weight: 700" size="1"> 
end_of_html
$sql="select day('$cday'),dayname('$cday'),date_add('$cday',interval 1 day)";
$sth = $dbh->prepare($sql);
$sth->execute();
($tday,$day_str,$cday) = $sth->fetchrow_array();
$sth->finish();
print<<"end_of_html";
											$tday</font></td>
		<td><span style="font-weight: 700"><font face="Verdana" size="1">$day_str</font></span></td>
		<td width="17"><font face="Verdana" style="font-weight: 700" size="1"> 
end_of_html
$sql="select day('$cday'),dayname('$cday'),date_add('$cday',interval 1 day)";
$sth = $dbh->prepare($sql);
$sth->execute();
($tday,$day_str,$cday) = $sth->fetchrow_array();
$sth->finish();

print<<"end_of_html";
											$tday</font></td>
		<td width="171"><span style="font-weight: 700">
		<font face="Verdana" size="1">$day_str</font></span></td>
		<td width="17"><font face="Verdana" style="font-weight: 700" size="1"> 
end_of_html
$sql="select day('$cday'),dayname('$cday'),date_add('$cday',interval 1 day)";
$sth = $dbh->prepare($sql);
$sth->execute();
($tday,$day_str,$cday) = $sth->fetchrow_array();
$sth->finish();
print<<"end_of_html";
											$tday</font></td>
		<td width="167"><span style="font-weight: 700">
		<font face="Verdana" size="1">$day_str</font></span></td>
		<td width="17"><font face="Verdana" style="font-weight: 700" size="1"> 
end_of_html
$sql="select day('$cday'),dayname('$cday'),date_add('$cday',interval 1 day)";
$sth = $dbh->prepare($sql);
$sth->execute();
($tday,$day_str,$cday) = $sth->fetchrow_array();
$sth->finish();
print<<"end_of_html";
											$tday</font></td>
		<td width="165"><span style="font-weight: 700">
		<font face="Verdana" size="1">$day_str</font></span></td>
		<td width="17">
end_of_html
$sql="select day('$cday'),dayname('$cday'),date_add('$cday',interval 1 day)";
$sth = $dbh->prepare($sql);
$sth->execute();
($tday,$day_str,$cday) = $sth->fetchrow_array();
$sth->finish();
print<<"end_of_html";
											<font face="Verdana" style="font-weight: 700" size="1"> 
											$tday</font></td>
		<td width="165"><span style="font-weight: 700">
		<font face="Verdana" size="1">$day_str</font></span></td>
	</tr>
end_of_html
$sql="select day('$cday'),dayname('$cday'),date_add('$cday',interval 1 day)";
$sth = $dbh->prepare($sql);
$sth->execute();
($tday,$day_str,$cday) = $sth->fetchrow_array();
$sth->finish();
	my $i = 1;
	while ($i <= $camp_cnt)
	{
		print "<tr>";
		my $j = 0;
		my $k = $days_in_month - $tday1;
		if ($k >= 7)
		{
			$k = 7;
		}
		while ($j < 7)
		{
			if ($j == 0)
			{
				$sql="select brand_name from client_brand_info where brand_id=$brand_id[$i]"; 
				$sth = $dbh->prepare($sql);
				$sth->execute();
				($profile_name) = $sth->fetchrow_array();
				$sth->finish();
				print "<td><font size=1>$profile_name</font></td>";
				print "<td width=17 align=middle><font size=1></font></td>\n";
			}
			else
			{
				print "<td width=17 align=middle><font size=1></font></td>\n";
			}
			$sql = "select campaign_name,advertiser_id,campaign.campaign_id from campaign,camp_schedule_info where client_id=$client_id and camp_schedule_info.slot_id=$slot_id[$i] and slot_type='3' and camp_schedule_info.schedule_date=date_add('$cday1',interval $tday1+$j day) and campaign.campaign_id=camp_schedule_info.campaign_id";
			$sth = $dbh->prepare($sql);
			$sth->execute();
			if (($cname,$temp_aid,$camp_id) = $sth->fetchrow_array())
			{
				$sth->finish();
				$sql="select id from 3rdparty_campaign where campaign_id=$camp_id";
                $sth1a = $dbh->prepare($sql) ;
                $sth1a->execute();
                ($temp_cid) = $sth1a->fetchrow_array();
                $sth1a->finish();
				$sql = "select datediff(date_add('$cday1',interval $tday1+$j day),curdate())";
                $sth1a = $dbh->prepare($sql) ;
                $sth1a->execute();
                ($diffcnt) = $sth1a->fetchrow_array();
                $sth1a->finish();
				if ($diffcnt >= 0)
				{
					print "<td width=171><font size=\"1\"><input type=\"checkbox\" value=\"$camp_id\" name=\"chkbox\"></font>\n";
					print "<font face=\"Verdana\" size=1><a href=\"/cgi-bin/advertiser_disp2.cgi?pmode=U&puserid=$temp_aid\" target=\"_blank\">$cname</a>&nbsp;<font color=\"#FF00000\" face=\"Verdana\" size=\"1\"><b><a href=\"/cgi-bin/3rdparty_show_camp.cgi?campaign_id=$temp_cid\" target=_blank>E</a></b>&nbsp;<font color=\"#FF00000\" face=\"Verdana\" size=\"1\"><b><a href=\"/cgi-bin/camp_del.cgi?campaign_id=$camp_id\" target=_blank>D</a></b>"; 
				}
				else
				{
					print "<td width=171>\n";
					print "<font face=\"Verdana\" size=1><a href=\"/cgi-bin/advertiser_disp2.cgi?pmode=U&puserid=$temp_aid\" target=\"_blank\">$cname</a>&nbsp;<font color=\"#FF00000\" face=\"Verdana\" size=\"1\"><b><a href=\"/cgi-bin/3rdparty_show_camp.cgi?campaign_id=$temp_cid\" target=_blank>E</a></b>&nbsp;<font color=\"#FF00000\" face=\"Verdana\" size=\"1\"><b><a href=\"/cgi-bin/camp_history.cgi?campaign_id=$camp_id\" target=_blank>H</a></b>"; 
				}
				print "</font>\n";
			    $sql = "select list_name,last_updated,filedate,vendor_supp_list_id,datediff(curdate(),last_updated) from advertiser_info,vendor_supp_list_info where advertiser_info.advertiser_id=$temp_aid and advertiser_info.vendor_supp_list_id=vendor_supp_list_info.list_id";
    			$sth = $dbh->prepare($sql) ;
    			$sth->execute();
    			if (($supp_name,$last_updated,$filedate,$sid,$day_cnt) = $sth->fetchrow_array())
    			{
    		    	if ($supp_name ne "NONE")
            		{
                		if ($filedate eq "")
                		{
                    		if ($day_cnt <= 7)
                    		{
                    			print "<font color=\"#FF0000\" face=\"Verdana\" size=\"1\"><b>&nbsp;<a href=\"/cgi-bin/supplist_addnames.cgi?tid=$sid\" target=_blank>S</a></b></font>";
                    		}
                    		else
                    		{
                    			print "&nbsp;<a href=\"/cgi-bin/supplist_addnames.cgi?tid=$sid\" target=_blank><font color=\"red\" face=\"Verdana\" size=\"1\"><b>S!</a></b></font>";
                    		}
                		}
                		else
                		{
                    		$sql = "select datediff(curdate(),'$filedate')";
                    		$sth1a = $dbh->prepare($sql) ;
                    		$sth1a->execute();
                    		($day_cnt) = $sth1a->fetchrow_array();
                    		$sth1a->finish();
                    		if ($day_cnt <= 7)
                    		{
                    			print "&nbsp;<font color=\"#FF0000\" face=\"Verdana\" size=\"1\"><b><a href=\"/cgi-bin/supplist_addnames.cgi?tid=$sid\" target=_blank>S</a></b></font>";
                    		}
                    		else
                    		{
                    			print "&nbsp;<a href=\"/cgi-bin/supplist_addnames.cgi?tid=$sid\" target=_blank><font color=\"red\" face=\"Verdana\" size=\"1\"><b>S!</a></b></font>";
                    		}
                    	}
                    }
                    print "</td>";
    			}
			}
			else
			{
				$sth->finish();
				print "<td width=171></td>";
			}
			$j++;
		}
		print "</tr>";
		$i++;
	}
	$tday1=$tday1+7;
	}
	print "</table>\n";
}
$sth1->finish();
print<<"end_of_html";

<p align="center">
						<a href="/cgi-bin/mainmenu.cgi" target="_top">
						<img src="/images/cancel.gif" border="0"></a><img height="1" src="/images/spacer.gif" width="40" border="0">
						<input type="image" src="/images/remove.gif" border="0" name="I1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<a href="/cgi-bin/mainmenu.cgi" target="_top">
						<img border="0" src="/images/home_blkline.gif" width="76" height="23"></a></p>
</form>

</body>

</html>
end_of_html
