#!/usr/bin/perl
#===============================================================================
# Purpose: Middle frame of weekly.html page 
# Name   : weekly_search.cgi 
#
#--Change Control---------------------------------------------------------------
# 07/05/05  Jim Sobeck  Creation
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
my $dbh;
my $phone;
my $email;
my $company;
my $id;
my $aim;
my $website;
my $username;
my $password;
my $tables;
my $client_id = $query->param('client_id');
my $catid= $query->param('catid');
my $stype= $query->param('stype');
my $catid1= $query->param('catid1');
my $catid2= $query->param('catid2');
my $pixel_verified = $query->param('pixel_verified');
my $ad_name = $query->param('adname');
my $cname = $query->param('cname');
my $last_run1 = $query->param('last_run1');
my $last_run2 = $query->param('last_run2');
my $last_run3 = $query->param('last_run3');
my $last_run4 = $query->param('last_run4');
my $rotation_modified1 = $query->param('rotation_modified1');
my $rotation_modified2 = $query->param('rotation_modified2');
my $rotation_modified3 = $query->param('rotation_modified3');
my $rotation_modified4 = $query->param('rotation_modified4');
my $creative_modified1 = $query->param('creative_modified1');
my $creative_modified2 = $query->param('creative_modified2');
my $creative_modified3 = $query->param('creative_modified3');
my $creative_modified4 = $query->param('creative_modified4');
my $approved1 = $query->param('approved1');
my $approved2 = $query->param('approved2');
my $approved3 = $query->param('approved3');
my $approved4 = $query->param('approved4');
my $payout= $query->param('payout');
my $payout1= $query->param('payout1');
my $payout_value= $query->param('payout_value');
my $payout1_value= $query->param('payout1_value');
my $aol_comp = $query->param('aol_comp');
my $aol_comp_value = $query->param('aol_comp_value');
my $aol_comp1 = $query->param('aol_comp1');
my $aol_comp1_value = $query->param('aol_comp1_value');
my $adv_rating = $query->param('adv_rating');
my $adv_rating_value = $query->param('adv_rating_value');
$tables = "advertiser_info";
if (($client_id > 0) || ($last_run1 ne ""))
{
	$tables = $tables . ",campaign";
}

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
if ($client_id > 0)
{
	$sql = "select distinct advertiser_info.advertiser_id,advertiser_name from $tables where advertiser_info.status='A' and campaign.advertiser_id=advertiser_info.advertiser_id and profile_id in (select profile_id from list_profile where client_id=$client_id) and campaign.status != 'W' and campaign.deleted_date is null";
}
else
{
$sql = "select distinct advertiser_info.advertiser_id,advertiser_name from $tables where advertiser_info.status='A'";
}
if ($stype eq "3")
{
	$sql=$sql." and advertiser_info.allow_strongmail='Y' ";
}
if ($last_run1 ne "")
{
	if ($last_run2 > 0)
	{
		$sql = $sql . " and campaign.advertiser_id=advertiser_info.advertiser_id and campaign.sent_datetime $last_run1 date_sub(curdate(),interval $last_run2 day)";
		if ($last_run3 ne "")
		{
			$sql = $sql . " and campaign.sent_datetime $last_run3 date_sub(curdate(),interval $last_run4 day)";
		}
	}
	else
	{
		if ($client_id > 0)
		{
			$sql = "select distinct advertiser_info.advertiser_id,advertiser_name from advertiser_info where advertiser_info.status='A' and advertiser_id not in (select campaign.advertiser_id from campaign where profile_id in (select profile_id from list_profile where client_id=$client_id) and campaign.deleted_date is null and status in ('C','P','W','T'))";
		}
		else
		{
			$sql = "select distinct advertiser_info.advertiser_id,advertiser_name from advertiser_info where advertiser_info.status='A' and advertiser_id not in (select distinct advertiser_id from campaign where status in ('C','P','W','T'))";
		}
	}
}
if ($pixel_verified ne "")
{
    $sql = $sql . " and pixel_verified='$pixel_verified'";
}
if ($ad_name ne "")
{
    $sql = $sql . " and advertiser_name like '%${ad_name}%'";
}
if ($cname ne "")
{
    $sql = $sql . " and advertiser_id in (select advertiser_id from advertiser_contact_info where contact_company like '%${cname}%')";
}
if ($adv_rating ne "")
{
    $sql = $sql . " and advertiser_rating $adv_rating $adv_rating_value ";
}
if ($payout ne "")
{
		$sql = $sql . " and advertiser_info.payout $payout $payout_value ";
		if ($payout1 ne "")
		{
			$sql = $sql . " and advertiser_info.payout $payout1 $payout1_value";
		}
}
if ($adv_rating ne "")
{
	$sql = $sql . " and advertiser_info.advertiser_rating $adv_rating $adv_rating_value ";
}
if ($aol_comp ne "")
{
	if ($aol_comp1 ne "")
	{
		$sql = $sql . " and advertiser_id in (select advertiser_id from campaign,campaign_log where campaign.campaign_id=campaign_log.campaign_id and ((aol_complaints/sent_cnt)*100) $aol_comp $aol_comp_value and ((aol_complaints/sent_cnt)*100) $aol_comp1 $aol_comp1_value and status!='T')";
	}
	else
	{
		$sql = $sql . " and advertiser_id in (select advertiser_id from campaign,campaign_log where campaign.campaign_id=campaign_log.campaign_id and ((aol_complaints/sent_cnt)*100) $aol_comp $aol_comp_value and status!='T')";
	}
}
if ($rotation_modified1 ne "")
{
	if ($rotation_modified2 > 0)
	{
		$sql = $sql . " and advertiser_info.advertiser_id in (select distinct advertiser_id from advertiser_setup where date_modified $rotation_modified1 date_sub(curdate(),interval $rotation_modified2 day))";
		if ($rotation_modified3 ne "")
		{
			$sql = $sql . " and advertiser_info.advertiser_id in (select distinct advertiser_id from advertiser_setup where date_modified $rotation_modified3 date_sub(curdate(),interval $rotation_modified4 day))";
		}
	}
	else
	{
		$sql = $sql . " and (advertiser_info.advertiser_id in (select distinct advertiser_id from advertiser_setup where date_modified is null) or advertiser_info.advertiser_id not in (select distinct advertiser_id from advertiser_setup))";
	}
}
if ($creative_modified1 ne "")
{
	if ($creative_modified2 > 0)
	{
		$sql = $sql . " and advertiser_info.advertiser_id in (select distinct advertiser_id from creative where status='A' and creative_date $creative_modified1 date_sub(curdate(),interval $creative_modified2 day))";
		if ($creative_modified3 ne "")
		{
			$sql = $sql . " and advertiser_info.advertiser_id in (select distinct advertiser_id from creative where status='A' and creative_date $creative_modified3 date_sub(curdate(),interval $creative_modified4 day))";
		}
	}
	else
	{
		$sql = $sql . " and advertiser_info.advertiser_id not in (select advertiser_id from creative)";
	}
}
if ($approved1 ne "")
{
	if ($approved2 > 0)
	{
		$sql = $sql . " and advertiser_info.advertiser_id in (select advertiser_id from creative where status='A' and date_approved $approved1 date_sub(curdate(),interval $approved2 day) union select advertiser_id from advertiser_tracking where date_approved $approved1 date_sub(curdate(),interval $approved2 day))";
		if ($approved3 ne "")
		{
			$sql = $sql . " and advertiser_info.advertiser_id in (select advertiser_id from creative where status='A' and date_approved $approved3 date_sub(curdate(),interval $approved4 day) union select advertiser_id from advertiser_tracking where date_approved $approved3 date_sub(curdate(),interval $approved4 day))";
		}
	}
	else
	{
		$sql = $sql . " and (advertiser_info.advertiser_id in (select advertiser_id from creative where date_approved is null) and advertiser_info.advertiser_id in (select distinct advertiser_id from advertiser_tracking where date_approved is null and client_id=1))";
	}
}
if (($catid ne "58") and ($catid ne ""))
{
		$sql = $sql . " and (advertiser_info.category_id=$catid";
		if (($catid ne "58") and ($catid ne ""))
		{
			$sql = $sql . " or advertiser_info.category_id=$catid1";
		}
		if (($catid ne "58") and ($catid ne ""))
		{
			$sql = $sql . " or advertiser_info.category_id=$catid2";
		}
		$sql = $sql . ")";
}
$sql = $sql . " order by advertiser_name";
open (LOG,">/tmp/weekly.log");
print LOG "<$sql>\n";
close LOG;
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>
<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Weekly</title>
<script language=JavaScript>
function set_sdate(sdate)
{
	document.adform.sdate.value=sdate;
}
</script>
</head>

<body>
<form method=post name=adform action="/cgi-bin/weekly_adv_search.cgi" target="bottom">
<input type=hidden name=sdate value="">
<input type=hidden name=stype value="$stype">
<table border="1" width="100%" id="table6">
            <tr>
        <td width="149">
                                            <b>
<font face="Verdana" size="2">Advertiser: </font></b>
                                            </td>
        <td><select name="adv_id">
end_of_html
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($id,$company) = $sth->fetchrow_array())
{
	print "<option value=$id>$company</option>\n";
}
$sth->finish();
print<<"end_of_html";
</select></td>
		<td width="149">
		<input type="submit" value="Submit" name="B27"></td>
			</tr>
			</table>
</form>
</body>
</html>
end_of_html
