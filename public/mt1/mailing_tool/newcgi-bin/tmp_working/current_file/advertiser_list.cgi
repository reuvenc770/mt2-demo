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
my $sort_str;
my $temp_sord;
my $aid;
my $aname;
my ($supp_name,$last_updated,$filedate,$sid);
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
my $ad_name;
my $old_adv_rating;
my $trigger;
my $trigger2;
if ($sord eq "")
{
	$sord="advertiser_name";
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
$util->db_connect();
$dbh = $util->get_dbh;

# ------- check for login - if not logged in then Exit --------------
my $user_id = util::check_security();
$user_id=1;
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}

&disp_header();
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
	my ($heading_text, $username, $curdate) ;

	$curdate = $util->date(0,2) ;
	$heading_text = "User: $username &nbsp;&nbsp;&nbsp;Date: $curdate" ;

    print "Content-Type: text/html\n\n";
print << "end_of_html";
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Mailing System EMail System</title>
</head>
<body link="#000000" vlink="#000000" alink="#000000">
<TABLE cellSpacing=0 cellPadding=0 align=left bgColor=#ffffff border=0>
<TBODY>
<TR vAlign=top>
<TD noWrap align=left>
    <table border="0" cellpadding="0" cellspacing="0" width="900">
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
	<TABLE cellSpacing=0 cellPadding=0 bgColor=#FFFFFF border=0>
	<TBODY>
	<TR>
	<TD vAlign=top align=left bgColor=#FFFFFF colSpan=10>

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
	my ($puserid, $username, $name, $email_addr,$internal_email_addr,$physical_addr,$cstatus,$cname,$company_name,$payout,$cdate,$request_date,$url_count); 
	my ($bgcolor) ;

	print << "end_of_html" ;

	<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
	<TBODY>
	<TR>
<td colspan=2 align=center><a href="mainmenu.cgi" target="_top"><img src="/mail-images/home_blkline.gif" border=0></a></td>
<td colspan=1 align=center><a href="adv_copy.cgi" target="_top"><img src="/mail-images/copy.gif" border=0></a></td>
<td colspan=2 align=center><a href="advertiser_disp.cgi?pmode=A" target="_top"><img src="/mail-images/add.gif" border=0></a></td></tr>
	<tr><td>&nbsp;</td></tr>
	<TR bgColor="#509C10" height=15>
		<TD colspan="7" align=center width="45%" height=15>
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
	<TD bgcolor="#EBFAD1" align="left" width="7%">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2>
		<b>Creative<br>Modified</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width="7%">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2>
		<b>Approved</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width="6%">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2>
		<b>Trigger/<br>Daily</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width="6%">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2>
		<b>Advertiser<br>Rating</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width="6%">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2>
		<b>Pixel<br>Verified</b></font></td>
	<TD bgcolor="#EBFAD1" align="middle" width="10%">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2>
		<b>Last<br>Run Date</b></font></td>
	<TD bgcolor="#EBFAD1" align="middle" width="15%">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2>
		<b>Suppression<br>List</b></font></td>
	<TD bgcolor="#EBFAD1" align="middle" width="10%">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2>
		<b>Rotation<br>Modified</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width="6%">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2>
		<b><a href="advertiser_list.cgi?sord=url_count,advertiser_name&oord=$temp_sord">URL(s)</a></b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width="10%">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2>
		<b><a href="advertiser_list.cgi?sord=category_name,advertiser_name&oord=$temp_sord">Category</a></b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width="8%">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2>
		<b><a href="advertiser_list.cgi?sord=contact_company,advertiser_name&oord=$temp_sord">Company Name</a></b></font></td>
	<TD bgcolor="#EBFAD1" align="middle" width="5%">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2>
		<b><a href="advertiser_list.cgi?sord=payout,advertiser_name&oord=$temp_sord">Payout</a></b></font></td>
	</TR> 

end_of_html

	#===========================================================================
	# Loop - Get ALL Lists that belong to the User
	#===========================================================================
#	$sql = "update advertiser_info set url_count=(select count(*) from advertiser_tracking where advertiser_tracking.advertiser_id=advertiser_info.advertiser_id)";
#	my $rows = $dbh->do($sql);
#
#	Add logic for displaying advertiser by selection criteria
#
$sql="";
my $search = $query->param('search');
if ($search eq "Y")
{
my $client_id = $query->param('client_id');
my $catid= $query->param('catid');
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
my $adv_rating= $query->param('adv_rating');
my $adv_rating_value= $query->param('adv_rating_value');
$tables = "advertiser_info";
if (($client_id > 0) || ($last_run1 ne ""))
{
	$tables = $tables . ",campaign";
}

if ($client_id > 0)
{
	$sql = "select distinct advertiser_info.advertiser_id,advertiser_name from $tables where advertiser_info.status in ('A','S','I') and campaign.advertiser_id=advertiser_info.advertiser_id and profile_id in (select profile_id from list_profile where client_id=$client_id) and campaign.status != 'W' and campaign.deleted_date is null";
}
else
{
$sql = "select distinct advertiser_info.advertiser_id,advertiser_name from $tables where advertiser_info.status in ('A','S','I')";
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
			if ($last_run1 eq ">")
			{
				$sql = $sql . " and campaign.sent_datetime $last_run3 date_sub(curdate(),interval $last_run4 day)";
			}
			else
			{
				$sql = $sql . " and campaign.advertiser_id in (select advertiser_id from campaign group by advertiser_id having max(sent_datetime) $last_run3 date_sub(curdate(),interval $last_run4 day))";
			}
		}
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
if ($catid ne "58")
{
		$sql = $sql . " and (advertiser_info.category_id=$catid";
		if ($catid1 ne "58")
		{
			$sql = $sql . " or advertiser_info.category_id=$catid1";
		}
		if ($catid2 ne "58")
		{
			$sql = $sql . " or advertiser_info.category_id=$catid2";
		}
		$sql = $sql . ")";
}
	$sth = $dbh->prepare($sql) ;
	$sth->execute();
	$aid_list="0,";
open(LOG,">/tmp/adv.log");
print LOG "<$sql>\n";
close LOG;
	while (($aid,$aname) = $sth->fetchrow_array())
	{
		$aid_list = $aid_list . $aid . ",";
	}
	$sth->finish();
	$_ = $aid_list;
	chop;
	$aid_list=$_;
}

#
	if ($aid_list ne "")
	{
		$sql = "select advertiser_info.advertiser_id,advertiser_name,category_name,contact_company,payout,advertiser_info.status,approval_requested_date,url_count,pixel_placed,pixel_requested,list_name,last_updated,filedate,vendor_supp_list_id,datediff(curdate(),last_updated),pixel_verified,advertiser_rating from advertiser_info,category_info,advertiser_contact_info,vendor_supp_list_info where advertiser_info.status in ('A','S','I') and advertiser_info.advertiser_id=advertiser_contact_info.advertiser_id and advertiser_info.category_id=category_info.category_id and advertiser_info.vendor_supp_list_id=vendor_supp_list_info.list_id and advertiser_info.advertiser_id in ($aid_list) order by $sord";
	}
	else
	{
		$sql = "select advertiser_info.advertiser_id,advertiser_name,category_name,contact_company,payout,advertiser_info.status,approval_requested_date,url_count,pixel_placed,pixel_requested,list_name,last_updated,filedate,vendor_supp_list_id,datediff(curdate(),last_updated),pixel_verified,advertiser_rating from advertiser_info,category_info,advertiser_contact_info,vendor_supp_list_info where advertiser_info.status in ('A','S','I') and advertiser_info.advertiser_id=advertiser_contact_info.advertiser_id and advertiser_info.category_id=category_info.category_id and advertiser_info.vendor_supp_list_id=vendor_supp_list_info.list_id order by $sord";
	}
	$sth = $dbh->prepare($sql) ;
	$sth->execute();
	$reccnt = 0 ;
	while (($puserid, $name, $cname,$company_name,$payout,$cstatus,$request_date,$url_count,$pixel_placed,$pixel_requested,$supp_name,$last_updated,$filedate,$sid,$day_cnt,$pixel_verified,$old_adv_rating) = $sth->fetchrow_array())
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
		$sth1 = $dbh->prepare($sql) ;
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
		$sql = "select max(creative_date) from creative where advertiser_id=$puserid and status='A'"; 
		$sth1 = $dbh->prepare($sql) ;
		$sth1->execute();
		($cdate) = $sth1->fetchrow_array();
		$sth1->finish();
        print qq{	<TD align=left><font color="#000000" face="Arial" size="2">$cdate</font></TD> \n } ;
		$sql = "select date_format(max(date_approved),'%Y-%m-%d') from advertiser_tracking where advertiser_id=$puserid union select date_format(max(date_approved),'%Y-%m-%d') from creative where advertiser_id=$puserid order by 1 desc";
		$sth1 = $dbh->prepare($sql) ;
		$sth1->execute();
		($cdate) = $sth1->fetchrow_array();
		$sth1->finish();
		if ($cdate ne "")
		{
			my $tcnt;
			$sql = "select sum(sent_cnt) from campaign_log where campaign_id in (select campaign_id from campaign where advertiser_id=$puserid)";
			$sth1 = $dbh->prepare($sql) ;
			$sth1->execute();
			($tcnt) = $sth1->fetchrow_array();
			$sth1->finish();
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
		$sth1 = $dbh->prepare($sql) ;
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
        $sql = "select max(date(sent_datetime)) from campaign where advertiser_id=$puserid"; 
        $sth1a = $dbh->prepare($sql) ;
        $sth1a->execute();
       	($sdate) = $sth1a->fetchrow_array();
       	$sth1a->finish();
        $sql = "select date(max(date_modified)) from advertiser_setup where advertiser_id=$puserid"; 
        $sth1a = $dbh->prepare($sql) ;
        $sth1a->execute();
       	($mdate) = $sth1a->fetchrow_array();
       	$sth1a->finish();
		$trigger = 0;
		$trigger2 = 0;
        $sql = "select count(*) from creative where advertiser_id=$puserid and trigger_flag='Y'"; 
        $sth1a = $dbh->prepare($sql) ;
        $sth1a->execute();
       	($trigger) = $sth1a->fetchrow_array();
       	$sth1a->finish();
		$sql = "select count(*) from campaign where status='W' and deleted_date is null and advertiser_id=$puserid";
       	$sth1a = $dbh->prepare($sql) ;
       	$sth1a->execute();
       	($trigger2) = $sth1a->fetchrow_array();
       	$sth1a->finish();
		if (($trigger > 0) || ($trigger2 > 0))
		{
        	print qq{	<TD align=middle><font color="red">Y</font></TD> \n } ;
		}
		else
		{
        	print qq{	<TD align=middle>N</font></TD> \n } ;
		}
#		$trigger = 0;
#		$trigger2 = 0;
#		$sql="select trigger_creative,trigger_creative2 from advertiser_setup where advertiser_id=$puserid";
#       	$sth1a = $dbh->prepare($sql) ;
#       	$sth1a->execute();
#       	($trigger,$trigger2) = $sth1a->fetchrow_array();
#       	$sth1a->finish();
#		if (($trigger > 0) || ($trigger2 > 0))
#		{
#        	print qq{	<TD align=middle><font color="red">Y</font></TD> \n } ;
#		}
#		else
#		{
#        	print qq{	<TD align=middle>N</font></TD> \n } ;
#		}
       	print qq{	<TD align=middle><font color="black">$old_adv_rating</font></TD> \n } ;
       	print qq{	<TD align=middle><font color="black">$pixel_verified</font></TD> \n } ;
        print qq{	<TD align=middle><font color="black" face="Arial" size="2">$sdate</font></TD> \n } ;
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
                    $sth1a = $dbh->prepare($sql) ;
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
        print qq{	<TD align=middle><font color="#509C10" face="Arial" size="2">$url_count</font></TD> \n } ;
        print qq{	<TD align=left><font color="#509C10" face="Arial" size="2">$cname</font></TD> \n } ;
        print qq{	<TD align=left><font color="#509C10" face="Arial" size="2">$company_name</font></TD> \n } ;
        print qq{	<TD align=middle><font color="#509C10" face="Arial" size="2">$payout</font></TD> \n } ;
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

