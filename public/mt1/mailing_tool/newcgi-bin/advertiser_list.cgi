#!/usr/bin/perl
#===============================================================================
# Purpose: Displays a List of Advertisers 
# File   : advertiser_list.cgi
#
#--Change Control---------------------------------------------------------------
#===============================================================================

#-----------------------
# include Perl Modules
#-----------------------
use strict;
use CGI;
use util;

#--------------------------------
# get some objects to use later
#--------------------------------
my $util = util->new;
my $query = CGI->new;
my $username = $query->param('username');
my $sord= $query->param('sord');
my $oord= $query->param('oord');
my $in_cstatus = $query->param('cstatus');
my $orderby= $query->param('orderby');
my $in_managerid= $query->param('inmanager_id');
my $manager_group = $query->param('manager_group');
my $in_cstatus2 = $query->param('cstatus2');
my $in_cstatus3 = $query->param('cstatus3');
my $in_cstatus4 = $query->param('cstatus4');
my $in_cstatus5 = $query->param('cstatus5');
my $in_cstatus6 = $query->param('cstatus6');
my $oflag= $query->param('oflag');
my $csource=$query->param('csource');
my $csource1=$query->param('csource1');
my $csource2=$query->param('csource2');
my $csource3=$query->param('csource3');
my $country1=$query->param('country1');
my $country2=$query->param('country2');
my $country3=$query->param('country3');
my $country4=$query->param('country4');
my $sort_str;
my $temp_sord;
my $aid;
my $aname;
my $asid;
my $request_flag;
my $notes;
my $priority;
my ($supp_name,$last_updated,$filedate,$sid);
my $md5_suppression;
my $md5_last_updated;
my $cake_creativeID;
my $cake_offerID;
my $f1;
my $mdate;
my $sdate;
my $f2;
my $sth1a;
my $day_cnt;
my $mediactivate_str;
my $pixel_requested;
my $aid_list="";
my $tables;
my $pixel_placed;
my $pixel_verified;
my $pixel_verified_logic;
my $direct_track;
my $direct_track_logic;
my $ad_name;
my $old_adv_rating;
my $trigger;
my $trigger2;
if ($sord eq "")
{
	if ($orderby eq "Alpha")
	{
		$sord="advertiser_name";
	}
	else
	{
		$sord="priority";
	}
}
$temp_sord = $sord;
if ($sord eq $oord)
{
	$temp_sord = "";
	if ($sord eq "advertiser_name")
	{
		$sord = $sord . " desc";
	}
	else
	{
		($f1,$f2) = split ',',$sord;
		$sord = $f1 . " desc," . $f2;
	}
}
my $mesg = $query->param('mesg');
my ($sth, $sth1,$reccnt, $sql, $dbh ) ;
my $no_thumb_cnt;
my $images = $util->get_images_url;
my $alt_light_table_bg = $util->get_alt_light_table_bg;

# ------- connect to the util database ---------
my ($dbhq,$dbhu)=$util->get_dbh();

# ------- check for login - if not logged in then Exit --------------
my $user_id = util::check_security();

if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}
my $refresh_flag=0;
my $statstr="";
if (($in_cstatus eq 'R') or ($in_cstatus2 eq 'R') or ($in_cstatus3 eq 'R') or ($in_cstatus4 eq 'R') or ($in_cstatus5 eq 'R') or ($in_cstatus6 eq 'R'))
{
	$statstr="cstatus=".$in_cstatus."&cstatus2=".$in_cstatus2."&cstatus3=".$in_cstatus3."&cstatus4=".$in_cstatus4."&cstatus5=".$in_cstatus5."&cstatus6=".$in_cstatus6;
	$refresh_flag=1;
}
&disp_header($refresh_flag,$statstr);
&disp_body();
&disp_footer();
#------------------------
# End Main Logic
#------------------------



#===============================================================================
# Sub: disp_header - Header for PMS System (close bogus tbls to disp correctly)
#===============================================================================
sub disp_header
{
	my ($refresh_flag,$statstr)=@_;
	my ($heading_text, $username, $curdate) ;

	my $time=60*10;
	$curdate = $util->date(0,2) ;
	$heading_text = "User: $username &nbsp;&nbsp;&nbsp;Date: $curdate" ;

	my $url="http://mailingtool.routename.com:83/cgi-bin/advertiser_list.cgi?$statstr&oord=$oord&sord=$sord&username=$username&search=Y";
    print "Content-Type: text/html\n\n";
print << "end_of_html";
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
end_of_html
if ($refresh_flag == 1)
{
	print "<meta http-equiv=\"refresh\" content=\"$time;URL=$url\">\n";
}
print<<"end_of_html";
<title>Mailing System EMail System</title>
</head>
<body link="#000000" vlink="#000000" alink="#000000">
<TABLE width=100% cellSpacing=0 cellPadding=0 align=left bgColor=#ffffff border=0>
<TBODY>
<TR vAlign=top>
<TD noWrap align=left>
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
    <TD width=248 bgColor=#FFFFFF rowSpan=2>
        <img border="0" src="/mail-images/header.gif"></TD>
    <TD width=328 bgColor=#FFFFFF>&nbsp;</TD>
    </tr>
    <tr>
    <td width="468">
        <table cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
        <td align="left"><b><font face="Arial" size="2">&nbsp;$heading_text</FONT></b></td>
        </tr>
        <tr>
        <td align="right">
            <b><a style="TEXT-DECORATION: none" href="logout.cgi" target="_top">
            <font face=Arial size=2 color="#509C10">Logout</font></a>&nbsp;&nbsp;&nbsp;
        </td>
        </tr>
        </table>
    </td>
    </tr>
    </table>
end_of_html
	#-----------------------------------------------------------
	#  BEGIN HEADER FIX 
	#-----------------------------------------------------------
	print << "end_of_html";
	</TD>
	</TR>
	<tr>
	<td>
	<TABLE width=100% cellSpacing=0 cellPadding=0 bgColor=#FFFFFF border=0>
	<TBODY>
	<TR>
	<TD vAlign=top align=left bgColor=#FFFFFF colSpan=12>

	<TABLE cellSpacing=0 cellPadding=0 width=100% bgColor=#ffffff border=0>
	<TBODY>
	<TR>
	<TD>&nbsp;</TD></TR>
	</TBODY>
	</TABLE>
end_of_html
	#-----------------------------------------------------------
	#  END HEADER FIX 
	#-----------------------------------------------------------

} # end sub disp_header



#===============================================================================
# Sub: disp_body
#===============================================================================
sub disp_body
{
	my ($puserid, $username, $name, $email_addr,$internal_email_addr,$physical_addr,$cstatus,$cname,$company_name,$payout,$cdate,$request_date,$url_count,$contact_name); 
	my $test_flag;
	my $manager_name;
	my ($bgcolor);
	my ($pending_date, $active_date, $testing_date, $inactive_date_set);


	#===========================================================================
	# Loop - Get ALL Lists that belong to the User
	#===========================================================================
#	$sql = "update advertiser_info set url_count=(select count(*) from advertiser_tracking where advertiser_tracking.advertiser_id=advertiser_info.advertiser_id)";
#	my $rows = $dbhu->do($sql);
#
#	Add logic for displaying advertiser by selection criteria
#
$sql="";
my $search = $query->param('search');
if ($search eq "Y")
{
my $client_id = $query->param('client_id');
my $not_client_id = $query->param('not_client_id');
my $catid= $query->param('catid');
if ($catid eq "")
{
	$catid=0;
}
my $catid1= $query->param('catid1');
if ($catid1 eq "")
{
	$catid1=0;
}
my $catid2= $query->param('catid2');
if ($catid2 eq "")
{
	$catid2=0;
}
my $pixel_verified = $query->param('pixel_verified');
my $pixel_verified_logic = $query->param('pixel_verified_logic');
my $direct_track= $query->param('direct_track');
my $direct_track_logic = $query->param('direct_track_logic');
my $ad_name = $query->param('adname');
my $cre_name = $query->param('crename');
my $cname = $query->param('cname');
my $in_sid = $query->param('sid');
my $cake_creative_id = $query->param('cake_creative_id');
my $cake_offer_id = $query->param('cake_offer_id');
my $contact_name = $query->param('contact_name');
my $last_run1 = $query->param('last_run1');
my $last_run2 = $query->param('last_run2');
my $last_run3 = $query->param('last_run3');
my $last_run4 = $query->param('last_run4');
my $adurl_1 = $query->param('adurl_1');
my $adurl_2 = $query->param('adurl_2');
my $adurl_3 = $query->param('adurl_3');
my $adurl_4 = $query->param('adurl_4');
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
my $adv_rating= $query->param('adv_rating');
my $adv_rating_value= $query->param('adv_rating_value');
my $thirdURL=$query->param('third_url_flag');
my $supp_updated=$query->param('supp_updated');
my $supp_updated_value=$query->param('supp_updated_value');
my $creative_num=$query->param('creative_num');
my $creative_num_value=$query->param('creative_num_value');

my $from    = $query->param('from');
my $subject = $query->param('subject');
my $afrom    = $query->param('afrom');
my $asubject = $query->param('asubject');

my ($from_names, $subject_names);

$tables = "advertiser_info";
if (($client_id > 0) || ($last_run1 ne "") || ($not_client_id > 0))
{
	$tables = $tables . ",campaign";
}
if ($cre_name ne "")
{
	$tables = $tables . ",creative";
}

if ($client_id > 0)
{
	$sql = "select distinct advertiser_info.advertiser_id,advertiser_name from $tables where advertiser_info.status in ('A','S','I','R','C','B','W') and campaign.advertiser_id=advertiser_info.advertiser_id and profile_id in (select profile_id from list_profile where client_id=$client_id) and campaign.status != 'W' and campaign.deleted_date is null";
}
else
{
$sql = "select distinct advertiser_info.advertiser_id,advertiser_name from $tables where advertiser_info.status in ('A','S','I','R','C','B','W')";
}
if ($not_client_id > 0)
{
	$sql=$sql . " and campaign.advertiser_id=advertiser_info.advertiser_id and profile_id not in (select profile_id from list_profile where client_id=$not_client_id) and campaign.status != 'W' and campaign.deleted_date is null";
}

if($from ne ''){
	$from_names = getFromInfo($from);
	$sql = $sql . " and advertiser_info.advertiser_name in ($from_names) ";
} #end if


if($subject ne ''){
	
	$subject_names = getSubjectInfo($subject);
	$sql = $sql . " and advertiser_info.advertiser_name in ($subject_names) ";

} #end if
if ($afrom ne '')
{
	$sql = $sql . " and advertiser_info.advertiser_id in (select distinct advertiser_id from advertiser_from where advertiser_from like '%$afrom%' and status='A') ";
}
if ($asubject ne '')
{
	$sql = $sql . " and advertiser_info.advertiser_id in (select distinct advertiser_id from advertiser_subject where advertiser_subject like '%$asubject%' and status='A') ";
}


if ($last_run1 ne "")
{
	if ($last_run2 > 0)
	{
		if ($last_run1 eq ">")
		{
			$sql = $sql . " and campaign.advertiser_id=advertiser_info.advertiser_id and campaign.sent_datetime $last_run1 date_sub(curdate(),interval $last_run2 day)";
		}
		else
		{
			$sql = $sql . " and campaign.advertiser_id=advertiser_info.advertiser_id and campaign.advertiser_id in (select advertiser_id from campaign group by advertiser_id having max(sent_datetime) $last_run1 date_sub(curdate(),interval $last_run2 day))";
		}
		if ($last_run3 ne "")
		{
			if ($last_run4 eq ">")
			{
				$sql = $sql . " and campaign.sent_datetime $last_run3 date_sub(curdate(),interval $last_run4 day)";
			}
			else
			{
				$sql = $sql . " and campaign.advertiser_id in (select advertiser_id from campaign group by advertiser_id having max(sent_datetime) $last_run3 date_sub(curdate(),interval $last_run4 day))";
			}
		}
		$sql = $sql . " and campaign_type='STRONGMAIL'";
	}
	else
	{
		if ($client_id > 0)
		{
			$sql = "select distinct advertiser_info.advertiser_id,advertiser_name from advertiser_info where advertiser_info.status in ('A','S','I') and advertiser_id not in (select campaign.advertiser_id from campaign where profile_id in (select profile_id from list_profile where client_id=$client_id) and campaign.deleted_date is null and status in ('C','P','W','T'))";
		}
		else
		{
			$sql = "select distinct advertiser_info.advertiser_id,advertiser_name from advertiser_info where advertiser_info.status in ('A','S','I') and advertiser_id not in (select distinct advertiser_id from campaign where status in ('C','P','W','T'))";
		}
	}
}
if ($adurl_1 ne "")
{
	if ($adurl_2 > 0)
	{
		$sql = $sql . " and advertiser_url_updated $adurl_1 date_sub(curdate(),interval $adurl_2 day)";
		if ($adurl_3 ne "")
		{
			if ($adurl_4 > 0)
			{
				$sql = $sql . " and advertiser_url_updated $adurl_3 date_sub(curdate(),interval $adurl_4 day)";
			}
			else
			{
				$sql = $sql . " and advertiser_url_updated is null"; 
			}
		}
	}
	else
	{
		$sql = $sql . " and advertiser_url_updated is null"; 
	}
}
if ($pixel_verified ne "")
{
	if($pixel_verified_logic ne "") 
	{
		$sql = $sql . " and (pixel_verified='$pixel_verified'";
		$sql = $sql . " OR pixel_verified='$pixel_verified_logic')";
	}
	else
	{
		$sql = $sql . " and pixel_verified='$pixel_verified'";
	}
}
if ($direct_track ne "")
{
	$sql = $sql . " and direct_track='$direct_track'";
	if($direct_track_logic ne "") {
		$sql = $sql . " OR direct_track='$direct_track_logic'";
	}
}
if ($ad_name ne "")
{
	$sql = $sql . " and advertiser_name like '%${ad_name}%'";
}
if ($cre_name ne "")
{
	$sql = $sql . " and creative.advertiser_id=advertiser_info.advertiser_id and creative.creative_name like '%${cre_name}%'";
}
if ($cname ne "")
{
	$sql = $sql . " and company_id in (select company_id from company_info where company_name like '%${cname}%')";
}
if ($in_managerid ne "")
{
	$sql = $sql . " and advertiser_info.manager_id=".$in_managerid;
}
if ($manager_group ne "")
{
	$sql = $sql . " and advertiser_info.manager_id in (select manager_id from CampaignManager where MemberGroup='".$manager_group."')";
}
if ($csource ne "")
{
	$sql = $sql . " and ((advertiser_info.source".$csource."='Y')";
	if ($csource1 ne "")
	{
		$sql = $sql . " or (advertiser_info.source".$csource1."='Y')";
	}
	if ($csource2 ne "")
	{
		$sql = $sql . " or (advertiser_info.source".$csource2."='Y')";
	}
	if ($csource3 ne "")
	{
		$sql = $sql . " or (advertiser_info.source".$csource3."='Y')";
	}
	$sql=$sql . ")";
}
if ($country1 ne "")
{
	$sql = $sql . " and (advertiser_info.countryID = $country1";
	if ($country2 ne "")
	{
		$sql = $sql . " or advertiser_info.countryID = $country2";
	}
	if ($country3 ne "")
	{
		$sql = $sql . " or advertiser_info.countryID = $country3";
	}
	if ($country4 ne "")
	{
		$sql = $sql . " or advertiser_info.countryID = $country4";
	}
	$sql=$sql .")";
}
if ($in_cstatus ne "")
{
	if ($in_cstatus eq "T")
	{
		$sql = $sql . " and ((advertiser_info.status='A' and test_flag='Y')";
	}
	elsif ($in_cstatus eq "P")
	{
		$sql = $sql . " and ((advertiser_info.status='I' and test_flag='P')";
	}
	elsif ($in_cstatus eq "U")
	{
		$sql = $sql . " and ((advertiser_info.status='A' and test_flag='U')";
	}
	else
	{
		$sql = $sql . " and ((advertiser_info.status='$in_cstatus')";
	}
	if ($in_cstatus2 eq "T")
	{
		$sql = $sql . " or (advertiser_info.status='A' and test_flag='Y')";
	}
	elsif ($in_cstatus2 eq "P")
	{
		$sql = $sql . " or (advertiser_info.status='I' and test_flag='P')";
	}
	elsif ($in_cstatus2 eq "U")
	{
		$sql = $sql . " or (advertiser_info.status='A' and test_flag='U')";
	}
	elsif ($in_cstatus2 ne "")
	{
		$sql = $sql . " or (advertiser_info.status='$in_cstatus2')";
	}
	if ($in_cstatus3 eq "T")
	{
		$sql = $sql . " or (advertiser_info.status='A' and test_flag='Y')";
	}
	elsif ($in_cstatus3 eq "P")
	{
		$sql = $sql . " or (advertiser_info.status='I' and test_flag='P')";
	}
	elsif ($in_cstatus3 eq "U")
	{
		$sql = $sql . " or (advertiser_info.status='A' and test_flag='U')";
	}
	elsif ($in_cstatus3 ne "")
	{
		$sql = $sql . " or (advertiser_info.status='$in_cstatus3')";
	}
	if ($in_cstatus4 eq "T")
	{
		$sql = $sql . " or (advertiser_info.status='A' and test_flag='Y')";
	}
	elsif ($in_cstatus4 eq "P")
	{
		$sql = $sql . " or (advertiser_info.status='I' and test_flag='P')";
	}
	elsif ($in_cstatus4 eq "U")
	{
		$sql = $sql . " or (advertiser_info.status='A' and test_flag='U')";
	}
	elsif ($in_cstatus4 ne "")
	{
		$sql = $sql . " or (advertiser_info.status='$in_cstatus4')";
	}
	if ($in_cstatus5 eq "T")
	{
		$sql = $sql . " or (advertiser_info.status='A' and test_flag='Y')";
	}
	elsif ($in_cstatus5 eq "P")
	{
		$sql = $sql . " or (advertiser_info.status='I' and test_flag='P')";
	}
	elsif ($in_cstatus5 eq "U")
	{
		$sql = $sql . " or (advertiser_info.status='A' and test_flag='U')";
	}
	elsif ($in_cstatus5 ne "")
	{
		$sql = $sql . " or (advertiser_info.status='$in_cstatus5')";
	}
	if ($in_cstatus6 eq "T")
	{
		$sql = $sql . " or (advertiser_info.status='A' and test_flag='Y')";
	}
	elsif ($in_cstatus6 eq "P")
	{
		$sql = $sql . " or (advertiser_info.status='I' and test_flag='P')";
	}
	elsif ($in_cstatus6 eq "U")
	{
		$sql = $sql . " or (advertiser_info.status='A' and test_flag='U')";
	}
	elsif ($in_cstatus6 ne "")
	{
		$sql = $sql . " or (advertiser_info.status='$in_cstatus6')";
	}
	$sql=$sql . ")";
}
if ($in_sid ne "")
{
	$sql = $sql . " and advertiser_info.sid in ($in_sid)";
}
if ($cake_creative_id ne "")
{
	$sql = $sql . " and advertiser_info.cake_creativeID in ($cake_creative_id)";
}
if ($cake_offer_id ne "")
{
	$sql = $sql . " and advertiser_info.cake_creativeID in (select creativeID from CakeCreativeOfferJoin where offerID=$cake_offer_id)";
}
if ($contact_name ne "")
{
	$sql = $sql . " and contact_id in (select contact_id from company_info_contact where contact_name like '%${contact_name}%')";
}
if ($adv_rating ne "")
{
	$sql = $sql . " and advertiser_rating $adv_rating $adv_rating_value ";
}
if ($supp_updated ne "")
{
	if ($supp_updated_value eq 0)
	{
		$sql = $sql . " and vendor_supp_list_id in (select list_id from vendor_supp_list_info where last_updated is null) ";
	}
	else
	{
		$sql = $sql . " and vendor_supp_list_id in (select list_id from vendor_supp_list_info where datediff(curdate(),last_updated) $supp_updated $supp_updated_value) ";
	}
}
if ($payout ne "")
{
		$sql = $sql . " and advertiser_info.payout $payout $payout_value ";
		if ($payout1 ne "")
		{
			$sql = $sql . " and advertiser_info.payout $payout1 $payout1_value";
		}
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
if ($oflag ne "")
{
	$sql = $sql . " and advertiser_info.advertiser_id in (select distinct advertiser_id from advertiser_subject where status='A' and original_flag='$oflag')";
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

if ($thirdURL ne "") {
	$sql = $sql . " AND (advertiser_info.advertiser_id in (SELECT DISTINCT(advertiser_id) FROM advertiser_tracking WHERE daily_deal not in ('N','Y','T')))";
}
if ($catid ne "0")
{
		$sql = $sql . " and (advertiser_info.category_id=$catid";
		if ($catid1 ne "0")
		{
			$sql = $sql . " or advertiser_info.category_id=$catid1";
		}
		if ($catid2 ne "0")
		{
			$sql = $sql . " or advertiser_info.category_id=$catid2";
		}
		$sql = $sql . ")";
}
if ($creative_num ne "")
{
	$sql = $sql . " and advertiser_id in (select advertiser_id from creative where status='A' group by advertiser_id having count(*)".$creative_num.$creative_num_value.")";
}
my $query_name= $query->param('query_name');
my $squery= $query->param('squery');
if (($query_name ne "") && ($squery == 0))
{
	my $tquery_name= $dbhq->quote($query_name);
	my $sql1="delete from saved_query where query_name=$tquery_name";
	my $rows= $dbhu->do($sql1) ;
	my $tsql= $dbhq->quote($sql);
	$sql1="insert into saved_query(query_name,query_str) values($tquery_name,$tsql)";
	$rows= $dbhu->do($sql1) ;
}
if ($squery > 0)
{
	my $sql1="select query_str from saved_query where query_id=$squery";
	my $sth1a=$dbhu->prepare($sql1);
	$sth1a->execute();
	($sql)=$sth1a->fetchrow_array();
	$sth1a->finish();
}
	$sth = $dbhq->prepare($sql) ;
open(LOG,">/tmp/q.log");
print LOG "<$sql>\n";
close(LOG);
	$request_flag=0;
	$_=$sql;
	if ((/((advertiser_info.status='R'))/) or (/((advertiser_info.status='C'))/) or (/((advertiser_info.status='B'))/) or (/((advertiser_info.status='W'))/) or (/((advertiser_info.status='I'))/))
	{
		$request_flag=1;
	}
	$sth->execute();
	$aid_list="0,";
	while (($aid,$aname) = $sth->fetchrow_array())
	{
		$aid_list = $aid_list . $aid . ",";
	}
	$sth->finish();
	$_ = $aid_list;
	chop;
	$aid_list=$_;
}

if ($request_flag == 0)
{
	print << "end_of_html" ;

	<TABLE cellSpacing=1 cellPadding=0 width="100%" border=0>
	<TBODY>
	<TR>
<td colspan=2 align=center><a href="mainmenu.cgi" target="_top"><img src="/mail-images/home_blkline.gif" border=0></a></td>
<td colspan=1 align=center><a href="adv_copy.cgi" target="_top"><img src="/mail-images/copy.gif" border=0></a></td>
<td colspan=2 align=center><a href="advertiser_disp.cgi?pmode=A" target="_top"><img src="/mail-images/add.gif" border=0></a></td></tr>
	<tr><td>&nbsp;</td></tr>
	<TR bgColor="#509C10" height=15>
		<TD colspan="13" align=center width="45%" height=15>
		<font face="Verdana,Arial,Helvetica,sans-serif" color="white" size="3">
		<b>List of Current Advertisers</font>
		<font face="Verdana,Arial,Helvetica,sans-serif" color="white" size="2">
		(click name to edit the advertiser)</b></font></TD>
	</TR>
	<TR> 
	<TD bgcolor="#EBFAD1" align="left" width="02%">&nbsp;</td>
	<TD bgcolor="#EBFAD1" align="left" width="10%">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2> 
		<b><a href="advertiser_list.cgi?sord=advertiser_name&oord=$temp_sord">Name</a></b></font></td>
	<TD bgcolor="#EBFAD1" align="middle"><b><a href="advertiser_list.cgi?sord=payout,advertiser_name&oord=$temp_sord">Requested By</a></b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width="7%"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>SID</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width="7%"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>Cake Creative ID</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width="7%"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>Cake Offer ID</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width="7%">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2>
		<b>Creative<br>Modified</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width="7%">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2>
		<b>Approved</b></font></td>
	<TD bgcolor="#EBFAD1" align="middle" width="10%">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2>
		<b>Last<br>Run Date</b></font></td>
	<TD bgcolor="#EBFAD1" align="middle" width="10%">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2>
		<b>Suppression<br>List</b></font></td>
	<TD bgcolor="#EBFAD1" align="middle" width="10%">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2>
		<b>Rotation<br>Modified</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width="10%">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2>
		<b><a href="advertiser_list.cgi?sord=category_name,advertiser_name&oord=$temp_sord">Category</a></b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width="8%">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2>
		<b><a href="advertiser_list.cgi?sord=contact_company,advertiser_name&oord=$temp_sord">Company Name</a></b></font></td>
	<TD bgcolor="#EBFAD1" align="middle" width="5%">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2>
		<b><a href="advertiser_list.cgi?sord=payout,advertiser_name&oord=$temp_sord">Payin</a></b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width="30%"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>Notes</b></font></td>
	</TR> 

end_of_html
}
else
{
	print << "end_of_html" ;

	<TABLE cellSpacing=1 cellPadding=0 width="100%" border=0>
	<TBODY>
	<TR>
<td colspan=2 align=center><a href="mainmenu.cgi" target="_top"><img src="/mail-images/home_blkline.gif" border=0></a></td>
<td colspan=1 align=center><a href="adv_copy.cgi" target="_top"><img src="/mail-images/copy.gif" border=0></a></td>
<td colspan=2 align=center><a href="advertiser_disp.cgi?pmode=A" target="_top"><img src="/mail-images/add.gif" border=0></a></td></tr>
	<tr><td>&nbsp;</td></tr>
	<TR bgColor="#509C10" height=15>
		<TD colspan="13" align=center width="45%" height=15>
		<font face="Verdana,Arial,Helvetica,sans-serif" color="white" size="3">
		<b>List of Current Advertisers</font>
		<font face="Verdana,Arial,Helvetica,sans-serif" color="white" size="2">
		(click name to edit the advertiser)</b></font></TD>
	</TR>
	<TR> 
	<TD bgcolor="#EBFAD1" align="left" width="02%">&nbsp;</td>
	<TD bgcolor="#EBFAD1" align="left" width="20%">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2> 
		<b><a href="advertiser_list.cgi?sord=advertiser_name&oord=$temp_sord">Name</a></b></font></td>
	<TD bgcolor="#EBFAD1" align="middle"><b><a href="advertiser_list.cgi?sord=payout,advertiser_name&oord=$temp_sord">Requested By</a></b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width="7%">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>Status</b></font></td>
	<TD bgcolor="#EBFAD1" align="left">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>(Status) Date</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width="7%">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>SID</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width="7%"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>Cake Creative ID</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width="7%"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>Cake Offer ID</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width="7%">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2>
		<b>Creative<br>Modified</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width="7%"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>Approved</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width="7%"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>Last<br>Run Date</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width="30%"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>Notes</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width="5%"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>Priority</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width="10%">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2>
		<b><a href="advertiser_list.cgi?sord=category_name,advertiser_name&oord=$temp_sord">Category</a></b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width="8%">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2>
		<b><a href="advertiser_list.cgi?sord=contact_company,advertiser_name&oord=$temp_sord">Company Name</a></b></font></td>
	<TD bgcolor="#EBFAD1" align="middle" width="5%">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2>
		<b><a href="advertiser_list.cgi?sord=payout,advertiser_name&oord=$temp_sord">Payin</a></b></font></td>
	</TR> 

end_of_html
}
#
	if ($aid_list ne "")
	{
		#$sql = "select advertiser_info.advertiser_id,advertiser_name,category_name,company_name,payout,advertiser_info.status,test_flag,manager_name,pending_date,active_date,testing_date,inactive_date_set,approval_requested_date,url_count,pixel_placed,pixel_requested,list_name,last_updated,filedate,vendor_supp_list_id,datediff(curdate(),last_updated),pixel_verified,advertiser_rating,advertiser_info.sid,advertiser_info.physical_addr,advertiser_info.priority,advertiser_info.cake_creativeID from advertiser_info,category_info,company_info,vendor_supp_list_info,CampaignManager cm where cm.manager_id=advertiser_info.manager_id and advertiser_info.status in ('A','S','I','R','C','W','B') and advertiser_info.company_id=company_info.company_id and advertiser_info.category_id=category_info.category_id and advertiser_info.vendor_supp_list_id=vendor_supp_list_info.list_id and advertiser_info.advertiser_id in ($aid_list) order by $sord";
		$sql = "select advertiser_info.advertiser_id,advertiser_name,category_name,company_name,payout,advertiser_info.status,test_flag,manager_name,pending_date,active_date,testing_date,inactive_date_set,approval_requested_date,url_count,pixel_placed,pixel_requested,md5_last_updated,md5_suppression,vendor_supp_list_id,datediff(curdate(),md5_last_updated),pixel_verified,advertiser_rating,advertiser_info.sid,advertiser_info.physical_addr,advertiser_info.priority,advertiser_info.cake_creativeID from advertiser_info,category_info,company_info,CampaignManager cm where cm.manager_id=advertiser_info.manager_id and advertiser_info.status in ('A','S','I','R','C','W','B') and advertiser_info.company_id=company_info.company_id and advertiser_info.category_id=category_info.category_id and advertiser_info.advertiser_id in ($aid_list) order by $sord";
	}
	else
	{
		#$sql = "select advertiser_info.advertiser_id,advertiser_name,category_name,company_name,payout,advertiser_info.status,test_flag,manager_name,pending_date,active_date,testing_date,inactive_date_set,approval_requested_date,url_count,pixel_placed,pixel_requested,list_name,last_updated,filedate,vendor_supp_list_id,datediff(curdate(),last_updated),pixel_verified,advertiser_rating,advertiser_info.sid,advertiser_info.physical_addr,advertiser_info.priority,advertiser_info.cake_creativeID from advertiser_info,category_info,company_info,vendor_supp_list_info,CampaignManager cm where cm.manager_id=advertiser_info.manager_id and advertiser_info.status in ('A','S','I','R','C','W','B') and advertiser_info.company_id=company_info.company_id and advertiser_info.category_id=category_info.category_id and advertiser_info.vendor_supp_list_id=vendor_supp_list_info.list_id order by $sord";
		$sql = "select advertiser_info.advertiser_id,advertiser_name,category_name,company_name,payout,advertiser_info.status,test_flag,manager_name,pending_date,active_date,testing_date,inactive_date_set,approval_requested_date,url_count,pixel_placed,pixel_requested,list_name,md5_last_updated,md5_suppression,vendor_supp_list_id,datediff(curdate(),md5_last_updated),pixel_verified,advertiser_rating,advertiser_info.sid,advertiser_info.physical_addr,advertiser_info.priority,advertiser_info.cake_creativeID from advertiser_info,category_info,company_info,CampaignManager cm where cm.manager_id=advertiser_info.manager_id and advertiser_info.status in ('A','S','I','R','C','W','B') and advertiser_info.company_id=company_info.company_id and advertiser_info.category_id=category_info.category_id order by $sord";
	}
	$sth = $dbhq->prepare($sql) ;
	$sth->execute();
	$reccnt = 0 ;
	while (($puserid, $name, $cname,$company_name,$payout,$cstatus,$test_flag,$manager_name,$pending_date,$active_date,$testing_date,$inactive_date_set,$request_date,$url_count,$pixel_placed,$pixel_requested,$md5_last_updated,$md5_suppression,$sid,$day_cnt,$pixel_verified,$old_adv_rating,$asid,$notes,$priority,$cake_creativeID) = $sth->fetchrow_array())
	{
		$reccnt++;
		if ( ($reccnt % 2) == 0 ) 
		{
			$bgcolor = "#EBFAD1" ;     # Light Green
		}
		else 
		{
			$bgcolor = "$alt_light_table_bg" ;     # Light Yellow
		}

		print qq{<TR bgColor=$bgcolor> \n} ;
		$sql = "select count(*) from creative where thumbnail ='' and advertiser_id=$puserid and status='A'";
		$sth1 = $dbhq->prepare($sql) ;
		$sth1->execute();
		($no_thumb_cnt) = $sth1->fetchrow_array();
		$sth1->finish();
		if ($no_thumb_cnt == 0)
		{
			print qq{	<TD align=left>&nbsp;</td> \n} ;
		}
		else
		{
			print qq{	<TD align=left><font color=red>X</font></td> \n} ;
		}
		### Print Advertiser Name ###
		if ($cstatus eq "S")
		{
        	print qq{	<TD align=left> \n } ;
			print qq{		<A HREF="advertiser_disp2.cgi?pmode=U&puserid=$puserid"><font color="red" face="Arial" size="2">$name</font></a></TD> \n } ;
		}
		elsif ($cstatus eq "I")
		{
        	print qq{	<TD align=left> \n } ;
			print qq{		<A HREF="advertiser_disp2.cgi?pmode=U&puserid=$puserid"><font color="green" face="Arial" size="2"><b>$name</b></font></a></TD> \n } ;
		}
		else
		{
        	print qq{	<TD align=left><font color="#509C10" face="Arial" size="2"> \n } ;
			print qq{		<A HREF="advertiser_disp2.cgi?pmode=U&puserid=$puserid">$name</a></font></TD> \n } ;
		}
        print qq{	<TD align=middle><font color="#509C10" face="Arial" size="2">$manager_name</font></TD> \n } ;
		if ($request_flag == 1)
		{
			my $status_date="";
			if ($cstatus eq "C")
			{
				print "<td>Pending</td>\n";
				$status_date=$pending_date;
			}
			elsif ($cstatus eq "B")
			{
				print "<td>Problem</td>\n";
			}
			elsif ($cstatus eq "W")
			{
				print "<td>In Progress</td>\n";
			}
			elsif ($cstatus eq "R")
			{
				print "<td>Requested</td>\n";
			}
			elsif ($cstatus eq "I")
			{
				print "<td>Inactive</td>\n";
				$status_date=$inactive_date_set;
			}
			elsif ($cstatus eq "P")
			{
				print "<td>Paused</td>\n";
			}
			elsif ($cstatus eq "A")
			{
				if ($test_flag eq "U")
				{
					print "<td>Update</td>\n";
				}
				elsif ($test_flag eq "T")
				{
					print "<td>Testing</td>\n";
					$status_date=$testing_date;
				}
				elsif ($test_flag eq "P")
				{
					print "<td>Paused</td>\n";
				}
				else
				{
					print "<td>Active</td>\n";
					$status_date=$active_date;
				}
			}
			print "<td>$status_date</td>";
		}
		$cake_offerID="";
		if ($cake_creativeID > 0)
		{
			$sql="select offerID from CakeCreativeOfferJoin where creativeID=$cake_creativeID and offerID > 0 limit 1";
			my $sthc=$dbhu->prepare($sql);
			$sthc->execute();
			($cake_offerID)=$sthc->fetchrow_array();
			$sthc->finish();
		}
        print qq{	<TD align=left><font color="#000000" face="Arial" size="2">$asid</font></TD> \n } ;
        print qq{	<TD align=left><font color="#000000" face="Arial" size="2">$cake_creativeID</font></TD> \n } ;
        print qq{	<TD align=left><font color="#000000" face="Arial" size="2">$cake_offerID</font></TD> \n } ;
		$sql = "select max(creative_date) from creative where advertiser_id=$puserid and status='A'"; 
		$sth1 = $dbhq->prepare($sql) ;
		$sth1->execute();
		($cdate) = $sth1->fetchrow_array();
		$sth1->finish();
		### Creative Date ###
        print qq{	<TD align=left><font color="#000000" face="Arial" size="2">$cdate</font></TD> \n } ;
		$sql = "select date_format(max(date_approved),'%Y-%m-%d') from advertiser_tracking where advertiser_id=$puserid union select date_format(max(date_approved),'%Y-%m-%d') from creative where advertiser_id=$puserid order by 1 desc";
		$sth1 = $dbhq->prepare($sql) ;
		$sth1->execute();
		($cdate) = $sth1->fetchrow_array();
		$sth1->finish();
		if ($cdate ne "")
		{
			my $tcnt;
#			$sql = "select sum(CampaignHistory.messageCount) from CampaignHistory,campaign where CampaignHistory.campaignID=campaign.campaign_id and campaign.advertiser_id=?";
#			$sth1 = $dbhq->prepare($sql) ;
#			$sth1->execute($puserid);
#			($tcnt) = $sth1->fetchrow_array();
#			$sth1->finish();
			if ($tcnt eq "")
			{
				$tcnt = 0;
			}
			if ($tcnt > 0)
			{	
        		print qq{	<TD align=left><font color="#000000" face="Arial" size="2">$cdate</font></TD> \n } ;
			}
			else
			{
        		print qq{	<TD align=left><font color="red" face="Arial" size="2">$cdate</font></TD> \n } ;
			}
		}
		else
		{
			if ($request_date ne "")
			{
				$request_date = "<font color=red>R" . $request_date . "</font>";
			}
        	print qq{	<TD align=left><font color="#000000" face="Arial" size="2">$cdate</font>$request_date</TD> \n } ;
		}
		#
		# check mediactivate
		#
		my $tcnt;
		$sql = "select count(*) from advertiser_tracking where advertiser_id=$puserid and url like '%mediactivate%'";
		$sth1 = $dbhq->prepare($sql) ;
		$sth1->execute();
		($tcnt) = $sth1->fetchrow_array();
		$sth1->finish();
		if ($tcnt > 0)
		{
			if ($pixel_placed eq "Y")
			{
				$mediactivate_str="<font color=\"#509C10\" face=\"Arial\" size=\"2\">Y";
			}
			else
			{
				if ($pixel_requested eq "Y")
				{
					$mediactivate_str="<font color=\"red\" face=\"Arial\" size=\"2\">YR";
				}
				else
				{
					$mediactivate_str="<font color=\"red\" face=\"Arial\" size=\"2\">Y";
				}
			}
		}
		else
		{
			$mediactivate_str="<font color=\"#509C10\" face=\"Arial\" size=\"2\">N";
		}

#        print qq{	<TD align=middle>$mediactivate_str</font></TD> \n } ;
        #$sql = "select date(max(sent_datetime)) from campaign where advertiser_id=$puserid and scheduled_date >= date_sub(curdate(),interval 14 day)"; 
        $sql = "select max(send_date) from unique_campaign where advertiser_id=$puserid and send_date >= date_sub(curdate(),interval 14 day)"; 
        $sth1a = $dbhq->prepare($sql) ;
        $sth1a->execute();
       	($sdate) = $sth1a->fetchrow_array();
       	$sth1a->finish();
        print qq{	<TD align=middle><font color="black" face="Arial" size="2">$sdate</font></TD> \n } ;
		if (($md5_suppression eq "Y") and ($sid == 0))
		{
			$supp_name="Self";
			$filedate="";
			$last_updated=$md5_last_updated;
		}
		elsif (($md5_suppression eq "Y") and ($sid > 0))
		{
			$sql="select advertiser_name from advertiser_info where advertiser_id=$sid";
        	$sth1a = $dbhq->prepare($sql) ;
        	$sth1a->execute();
       		($supp_name) = $sth1a->fetchrow_array();
       		$sth1a->finish();

			$filedate="";
			$last_updated=$md5_last_updated;
		}
		else
		{
			$sql="select list_name,filedate,last_updated,datediff(curdate(),last_updated) from vendor_supp_list_info where list_id=$sid";
        	$sth1a = $dbhq->prepare($sql) ;
        	$sth1a->execute();
       		($supp_name,$filedate,$last_updated,$day_cnt) = $sth1a->fetchrow_array();
       		$sth1a->finish();
			if ($filedate eq "0000-00-00")
			{
				$filedate="";
			}
		}
		if ($request_flag == 0)
		{
        $sql = "select date(max(date_modified)) from advertiser_setup where advertiser_id=$puserid"; 
        $sth1a = $dbhq->prepare($sql) ;
        $sth1a->execute();
       	($mdate) = $sth1a->fetchrow_array();
       	$sth1a->finish();
		## print 3rd Party URL Flag ##
            if ($supp_name ne "NONE")
            {
                if ($filedate eq "")
                {
                    if ($day_cnt <= 7)
                    {
                        print qq { <td><font color="#000000" face="Arial" size="1"><a href="/cgi-bin/supplist_addnames.cgi?tid=$sid" target="_top">$supp_name</a><br>$last_updated</font></td> };
                    }
                    else
                    {
                        print qq { <td><font color="red" face="Arial" size="1"><a href="/cgi-bin/supplist_addnames.cgi?tid=$sid" target="_top">$supp_name</a><br>$last_updated</font></td> };
                    }
                }
                else
                {
                    $sql = "select datediff(curdate(),'$filedate')";
                    $sth1a = $dbhq->prepare($sql) ;
                    $sth1a->execute();
                    ($day_cnt) = $sth1a->fetchrow_array();
                    $sth1a->finish();
                    if ($day_cnt <= 7)
                    {
                        print qq { <td><font color="#000000" face="Arial" size="1"><a href="/cgi-bin/supplist_addnames.cgi?tid=$sid" target="_top">$supp_name</a><br>$filedate</font></td> };
                    }
                    else
                    {
                        print qq { <td><font color="red" face="Arial" size="1"><a href="/cgi-bin/supplist_addnames.cgi?tid=$sid" target="_top">$supp_name</a><br>$filedate</font></td> };
                    }
                }
            }
            else
            {
            print qq {<td><font color="red" face="Arial" size="1">$supp_name<br>$last_updated</font></td> };
            }
        print qq{	<TD align=middle><font color="#509C10" face="Arial" size="2">$mdate</font></TD> \n } ;
		}
		else
		{
        	print qq{	<TD align=middle><font color="#509C10" face="Arial" size="2">$notes</font></TD> \n } ;
        	print qq{	<TD align=middle><font color="#509C10" face="Arial" size="2">$priority</font></TD> \n } ;
		}
        print qq{	<TD align=left><font color="#509C10" face="Arial" size="2">$cname</font></TD> \n } ;
        print qq{	<TD align=left><font color="#509C10" face="Arial" size="2">$company_name</font></TD> \n } ;
        print qq{	<TD align=middle><font color="#509C10" face="Arial" size="2">$payout</font></TD> \n } ;
		if ($request_flag == 0)
		{
        	print qq{	<TD align=middle><font color="#509C10" face="Arial" size="2">$notes</font></TD> \n } ;
		}
		print qq{</TR> \n} ;

	}  # end while statement

	$sth->finish();

	print << "end_of_html" ;

	<TR><td align=center colspan=5><br>
	<A HREF="mainmenu.cgi" target="_top">
	<IMG name="BtnHome" src="$images/home_blkline.gif" hspace=7  border="0" width="72"  height="21" ></A>
	</td></tr>

	</tbody>
	</table>

end_of_html
} # end sub disp_body


#===============================================================================
# Sub: disp_footer
#===============================================================================
sub disp_footer
{
	print << "end_of_html";
</td>
</tr>
</tbody>
</table>
</td>
</tr>
<tr>
<td>
end_of_html

	util::footer();
	$util->clean_up();
} # end sub disp_footer

sub getFromInfo {

	my ($from) = @_;
	
	my $sql = qq(select
 distinct ai.advertiser_name
from
 campaign c,
 advertiser_setup adv,
 advertiser_info ai
where
  adv.advertiser_id = c.advertiser_id
and
 adv.advertiser_id = ai.advertiser_id
and
 adv.class_id=4
and
 c.scheduled_date >= date_sub(curdate(),interval 14 day)
and
( adv.from1 in (select from_id from advertiser_from where advertiser_from like "%$from%")
or
 adv.from2 in (select from_id from advertiser_from where advertiser_from like "%$from%")
or
 adv.from3 in (select from_id from advertiser_from where advertiser_from like "%$from%")
or
 adv.from4 in (select from_id from advertiser_from where advertiser_from like "%$from%")
or
 adv.from5 in (select from_id from advertiser_from where advertiser_from like "%$from%")
or
 adv.from6 in (select from_id from advertiser_from where advertiser_from like "%$from%")
or
 adv.from7 in (select from_id from advertiser_from where advertiser_from like "%$from%")
or
 adv.from8 in (select from_id from advertiser_from where advertiser_from like "%$from%")
or
 adv.from9 in (select from_id from advertiser_from where advertiser_from like "%$from%")
or
 adv.from10 in (select from_id from advertiser_from where advertiser_from like "%$from%"))
order by
 ai.advertiser_name);
 
  	my @adv_from;
 	
 	my $sth = $dbhq->prepare($sql) ;
	$sth->execute();
	
	while (my($aname) = $sth->fetchrow_array()){
		push (@adv_from,$aname);
	}
	
	my $advertiser_names = "'" . join("', '", @adv_from) . "'";
	
	return $advertiser_names;
 	
}


sub getSubjectInfo {
	
	my ($subject) = @_;

	my $sql = qq(select
 distinct ai.advertiser_name
from
 campaign c,
 advertiser_setup adv,
 advertiser_info ai
where
  adv.advertiser_id = c.advertiser_id
and
 adv.advertiser_id = ai.advertiser_id
and
 adv.class_id=4
and
 c.scheduled_date >= date_sub(curdate(),interval 14 day)
and
( adv.subject1 in (select subject_id from advertiser_subject where advertiser_subject like "%$subject%")
or
 adv.subject2 in (select subject_id from advertiser_subject where advertiser_subject like "%$subject%")
or
 adv.subject3 in (select subject_id from advertiser_subject where advertiser_subject like "%$subject%")
or
 adv.subject4 in (select subject_id from advertiser_subject where advertiser_subject like "%$subject%")
or
 adv.subject5 in (select subject_id from advertiser_subject where advertiser_subject like "%$subject%")
or
 adv.subject6 in (select subject_id from advertiser_subject where advertiser_subject like "%$subject%")
or
 adv.subject7 in (select subject_id from advertiser_subject where advertiser_subject like "%$subject%")
or
 adv.subject8 in (select subject_id from advertiser_subject where advertiser_subject like "%$subject%")
or
 adv.subject9 in (select subject_id from advertiser_subject where advertiser_subject like "%$subject%")
or
 adv.subject10 in (select subject_id from advertiser_subject where advertiser_subject like "%$subject%"))
order by
 ai.advertiser_name);
 
   	my @adv_subject;
 	
 	my $sth = $dbhq->prepare($sql) ;
	$sth->execute();
	
	while (my($aname) = $sth->fetchrow_array()){
		push (@adv_subject,$aname);
	}
	
	my $advertiser_names = "'" . join("', '", @adv_subject) . "'";
	
	return $advertiser_names;
	
}


