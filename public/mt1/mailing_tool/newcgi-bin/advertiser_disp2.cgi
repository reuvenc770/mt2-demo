#!/usr/bin/perl
#===============================================================================
# Purpose: Edit advertiser data (eg 'user' table).
# Name   : advertiser_disp.cgi (edit_advertiser_info.cgi)
#
#--Change Control---------------------------------------------------------------
# 01/05/04  Jim Sobeck  Creation
# 10/27/05  Jim Sobeck  Added advertiser rating
# 04/11/06	Jim Sobeck	Added creative deploy link
# 05/29/07	Jim Sobeck	Added hitpath tracking pixel
#==============================================================================

#-----  include Perl Modules ---------
use strict;
use CGI;
use util;

#------  get some objects to use later ---------
my $util = util->new;
my $query = CGI->new;
my $sth;
my $sql;
my $dbh;
my $errmsg;
my ($fname,$lname,$address,$address2,$city,$state,$zip,$phone,$email_addr);
my $countryID;
my ($status, $max_names, $max_mailings, $username);
my $password;
my $internal_email_addr;
my $physical_addr;
my $cstatus;
my $copywriter;
my $test_flag;
my $exclude_from_brands_w_articles;
my $override_brand_from;
my $bank_cc;
my $ssn;
my $adv_phone;
my $field_cnt;
my $page_cnt;
my $allow_3rd;
my $allow_strongmail;
my $allow_creative_deletion;
my $md5_suppression;
my $auto;
my $tid;
my $addr;
my $mailername;
my $seedlist;
my $approval_str;
my ($puserid, $pmesg);
my $idate;
my $company;
my $website_url;
my $company_phone;
my $user_type;
my $this_user_type;
my $images = $util->get_images_url;
my $privacy_policy_url;
my $account_type;
my $unsub_option;
my $prepop;
my $advertiser_rating;
my $name;
my $friendly_name;
my $pmode;
my $pmesg;
my ($cname,$phone,$cemail,$company,$aim,$website,$username,$password,$notes);
my $manager_name;
my $manager_email;
my $affiliate_name;
my $offer_type;
my $payout;
my $payout_pounds;
my $payout_euro;
my $payout_aud;
my $payout_br;
my $payout_no;
my $payout_sk;
my $payout_dkk;
my $direct_suppression_url;
my $passcard;
my $cpasscard;
my $advertiser_url;
my $landing_page;
my $ecpm;
my $exclude_days;
my $pixel_placed;
my $pass_tracking;
my $pixel_type;
my $scrub_percent;
my $margin_percent;
my $allocationCap;
my $allocationCapCnt;
my $sourceInternal;
my $sourceNetwork;
my $sourceDisplay;
my $sourceLinkOut;
my $sourceSearch;
my $sourceSocial;
my $sourceIncent;
my $sourceGold;
my $sourceOrange;
my $sourceGreen;
my $sourcePurple;
my $sourceBlue;
my $sourceOrigin;
my $pixel_requested;
my $pixel_verified;
my $direct_track;
my $tracking_pixel;
my $vendor_suppid;
my $supp_file;
my $supp_url;
my $supp_username;
my $supp_password;
my $auto_download;
my ($track_internally,$unsub_link,$unsub_image);
my $unsub_text;
my $unsub_use;
my $replace_flag;
my ($third_pixel,$third_tracking_pixel1,$third_tracking_pixel2);
my $hitpath_tracking_pixel;
my $defaultCurrency;
my $adv_catid;
my $priority;
my $affiliate_id;
my $date_requested;
my $manager_id;
my $intelligence_source;
my $testing_reason;
my $yesmail_listid;
my $yesmail_listname;
my $yesmail_divisionid;
my $tracking;

#------  connect to the util database -----------
my ($dbhq,$dbhu)=$util->get_dbh();

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
	$util->clean_up();
    exit(0);
}

$sql = "select user_type from user where user_id = $user_id";
$sth = $dbhq->prepare($sql);
$sth->execute();
($this_user_type) = $sth->fetchrow_array() ;
$sth->finish();
$this_user_type="A";
my $sid;
my $cake_creativeID;
my $auto_url_sid;
my $auto_cake_creativeid;
my $linkType;
my $company_id;
my $website_id;
my $ctracking_id;
my $contact_id;

#--------------------------------
# get CGI Form fields
#--------------------------------
$puserid = $query->param('puserid');
$pmesg   = $query->param('pmesg');

#------  Get the information about the user for display  --------
$sql = "select advertiser_name,email_addr,internal_email_addr,physical_addr,status,offer_type,payout,ecpm,tracking_pixel,pixel_placed,pixel_requested,exclude_days,vendor_supp_list_id,suppression_file,suppression_url,auto_download,suppression_username,suppression_password,track_internally,unsub_link,unsub_image,category_id,advertiser_url,pixel_verified, pre_pop,advertiser_rating,inactive_date,third_party_pixel,third_tracking_pixel1,third_tracking_pixel2,auto_optimize,direct_track,hitpath_tracking_pixel,allow_3rdparty,allow_strongmail,friendly_advertiser_name,test_flag,exclude_from_brands_w_articles,payout_pounds,md5_suppression,sid,allow_creative_deletion,unsub_text,unsub_use,replace_flag,priority,company_id,website_id,contact_id,override_brand_from,auto_url_sid,date_requested,intelligence_source,testing_reason,direct_suppression_url,passcard,bank_cc,ssn,phone,field_cnt,page_cnt,manager_id,pass_tracking,pixel_type,sourceInternal,sourceNetwork,sourceDisplay,sourceLinkOut,payout_euro,yesmail_listid,yesmail_listname,yesmail_divisionID,auto_cake_creativeID,landing_domain,countryID,cake_creativeID,sourceSearch,sourceSocial,sourceIncent,scrub_percent,allocationCap,margin_percent,allocationCapCnt,payout_aud,payout_br,linkType,payout_no,sourceGold,sourceOrange,sourceGreen,sourcePurple,sourceBlue,sourceOrigin,payout_sk,payout_dkk,defaultCurrency,tracking_id from advertiser_info where advertiser_id = $puserid"; 
$sth = $dbhq->prepare($sql);
$sth->execute();
($name,$email_addr,$internal_email_addr,$physical_addr,$cstatus,$offer_type,$payout,$ecpm,$tracking_pixel,$pixel_placed,$pixel_requested,$exclude_days,$vendor_suppid,$supp_file,$supp_url,$auto_download,$supp_username,$supp_password,$track_internally,$unsub_link,$unsub_image,$adv_catid,$advertiser_url,$pixel_verified,$prepop,$advertiser_rating,$idate,$third_pixel,$third_tracking_pixel1,$third_tracking_pixel2,$auto,$direct_track,$hitpath_tracking_pixel,$allow_3rd,$allow_strongmail,$friendly_name,$test_flag,$exclude_from_brands_w_articles,$payout_pounds,$md5_suppression,$sid,$allow_creative_deletion,$unsub_text,$unsub_use,$replace_flag,$priority,$company_id,$website_id,$contact_id,$override_brand_from,$auto_url_sid,$date_requested,$intelligence_source,$testing_reason,$direct_suppression_url,$passcard,$bank_cc,$ssn,$adv_phone,$field_cnt,$page_cnt,$manager_id,$pass_tracking,$pixel_type,$sourceInternal,$sourceNetwork,$sourceDisplay,$sourceLinkOut,$payout_euro,$yesmail_listid,$yesmail_listname,$yesmail_divisionid,$auto_cake_creativeid,$landing_page,$countryID,$cake_creativeID,$sourceSearch,$sourceSocial,$sourceIncent,$scrub_percent,$allocationCap,$margin_percent,$allocationCapCnt,$payout_aud,$payout_br,$linkType,$payout_no,$sourceGold,$sourceOrange,$sourceGreen,$sourcePurple,$sourceBlue,$sourceOrigin,$payout_sk,$payout_dkk,$defaultCurrency,$ctracking_id) = $sth->fetchrow_array();
$sth->finish();
if ($payout_pounds == 0)
{
	$payout_pounds="";
}
if ($payout_euro == 0)
{
	$payout_euro="";
}
if ($payout_aud == 0)
{
	$payout_aud="";
}
if ($payout_br == 0)
{
	$payout_br="";
}
if ($payout_no == 0)
{
	$payout_no="";
}
if ($payout_sk == 0)
{
	$payout_sk="";
}
if ($payout_dkk == 0)
{
	$payout_dkk="";
}
if ($test_flag eq "Y")
{
	$cstatus="T";
}
elsif ($test_flag eq "P")
{
	$cstatus="P";
}
elsif ($test_flag eq "U")
{
	$cstatus="U";
}
#
#------  Get the information about the user for display  --------
#
#$sql = "select contact_name,contact_phone,contact_email,contact_company,contact_aim,contact_website,contact_username,contact_password,contact_notes from advertiser_contact_info where advertiser_id = $puserid"; 
$sql = "select company_name,contact_notes,physical_addr,manager_name,affiliate_id,CampaignManager.email_addr,passcard from company_info,CampaignManager where company_info.manager_id=CampaignManager.manager_id and company_id=?"; 
$sth = $dbhq->prepare($sql);
$sth->execute($company_id);
($company,$notes,$addr,$manager_name,$affiliate_id,$manager_email,$cpasscard) = $sth->fetchrow_array();
$sth->finish();
$sql = "select name from AffiliatePlatform where affiliate_id=?"; 
$sth = $dbhq->prepare($sql);
$sth->execute($affiliate_id);
($affiliate_name) = $sth->fetchrow_array();
$sth->finish();
$sql = "select contact_name,contact_phone,contact_email,contact_aim from company_info_contact where contact_id=?"; 
$sth = $dbhq->prepare($sql);
$sth->execute($contact_id);
($cname,$phone,$cemail,$aim) = $sth->fetchrow_array();
$sth->finish();
$sql = "select website,username,password from company_info_website where website_id=?"; 
$sth = $dbhq->prepare($sql);
$sth->execute($website_id);
($website,$username,$password) = $sth->fetchrow_array();
$sth->finish();
my ($tname,$link_params,$number_of_params,$default_flag);
$sql = "select name,link_params,number_of_params,default_flag from company_info_tracking cit join AffiliatePlatform af on cit.affiliate_id=af.affiliate_id where tracking_id=?"; 
$sth = $dbhq->prepare($sql);
$sth->execute($ctracking_id);
($tname,$link_params,$number_of_params,$default_flag) = $sth->fetchrow_array();
if ($tname ne "")
{
	$tracking=$tname." - ".$link_params." -   Number of Params:".$number_of_params." - Default:".$default_flag;
}
$sth->finish();
$sql="select email_addr from company_seedlist where company_id=? order by email_addr";
$sth = $dbhq->prepare($sql);
$sth->execute($company_id);
my $email_addr; 
$seedlist="";
while (($email_addr) = $sth->fetchrow_array())
{
	$seedlist=$seedlist.$email_addr.",";
}
$sth->finish();
if ($seedlist ne "")
{
	chop($seedlist);
}
$sql="select email_addr from company_approval where company_id=? order by email_addr";
$sth = $dbhq->prepare($sql);
$sth->execute($company_id);
$approval_str="";
while (($email_addr) = $sth->fetchrow_array())
{
	$approval_str=$approval_str.$email_addr.",";
}
$sth->finish();
if ($approval_str ne "")
{
	chop($approval_str);
}
if ( $name eq "" ) 
{
	$errmsg = $dbhu->errstr();
    util::logerror("<br><br>Error Getting user information for AdvertiserID: $puserid &nbsp;&nbsp;$errmsg");
	exit(99) ;
}

my $ctitle="Mailing Tool";
print "Content-Type: text/html;charset-utf-8\n\n";
print << "end_of_html";
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>$ctitle - Email Tool</title>
<script language="JavaScript">
function auto_popup()
{
   document.location="/cgi-bin/auto_popup.cgi?aid=$puserid";
}
function remove_unapproved()
{
   document.location="/cgi-bin/adv_remove_unapproved.cgi?aid=$puserid";
}
function delete_tracking(ctype)
{
	if (ProcessForm('A'))
	{
		if (ctype == 'N') 
		{
			document.edit_advertiser.backto.value="/cgi-bin/disp_tracking.cgi?aid=$puserid&tid="+document.edit_advertiser.advertiser_url.value;
		}
		else
		{
			value = document.edit_advertiser["advertiser_url_" + ctype].value;
			document.edit_advertiser.backto.value="/cgi-bin/disp_tracking.cgi?aid=$puserid&tid="+value;
		}
		document.edit_advertiser.submit();
	}
}
function edit_creative()
{
	if (ProcessForm('A'))
	{
	document.edit_advertiser.backto.value="/cgi-bin/edit_creative.cgi?aid=$puserid&cid="+document.edit_advertiser.creative.value;
	document.edit_advertiser.submit();
	}
}
function copy_creative()
{
	if (ProcessForm('A'))
	{
	document.edit_advertiser.backto.value="/cgi-bin/copy_creative.cgi?aid=$puserid&cid="+document.edit_advertiser.creative.value;
	document.edit_advertiser.submit();
	}
}
function delete_creative()
{
	if (ProcessForm('A'))
	{
        if (confirm("Are you sure you want to delete the creative?"))
		{
			document.edit_advertiser.backto.value="/cgi-bin/delete_creative.cgi?aid=$puserid&cid="+document.edit_advertiser.creative.value;
			document.edit_advertiser.submit();
		}
	}
}
function activate_creative()
{
	if (ProcessForm('A'))
	{
        if (confirm("Are you sure you want to activate the creative?"))
		{
			document.edit_advertiser.backto.value="/cgi-bin/activate_creative.cgi?aid=$puserid&cid="+document.edit_advertiser.creative.value;
			document.edit_advertiser.submit();
		}
	}
}
function activate_subject()
{
	if (ProcessForm('A'))
	{
        if (confirm("Are you sure you want to activate the subject?"))
		{
			document.edit_advertiser.backto.value="/cgi-bin/activate_subject.cgi?aid=$puserid&sid="+document.edit_advertiser.csubject.value;
			document.edit_advertiser.submit();
		}
	}
}
function activate_from()
{
	if (ProcessForm('A'))
	{
        if (confirm("Are you sure you want to activate the from?"))
		{
			document.edit_advertiser.backto.value="/cgi-bin/activate_from.cgi?aid=$puserid&fid="+document.edit_advertiser.from.value;
			document.edit_advertiser.submit();
		}
	}
}
function delete_unsubimg()
{
	if (ProcessForm('A'))
	{
	document.edit_advertiser.backto.value="/cgi-bin/delete_unsubimg.cgi?aid=$puserid&cid="+document.edit_advertiser.creative.value;
	document.edit_advertiser.submit();
	}
}
function subject()
{
	if (ProcessForm('A'))
	{
	document.edit_advertiser.backto.value="/cgi-bin/subject.cgi?aid=$puserid";
	document.edit_advertiser.submit();
	}
}
function edit_subject()
{
	if (ProcessForm('A'))
	{
	document.edit_advertiser.backto.value="/cgi-bin/edit_subject.cgi?aid=$puserid&sid="+document.edit_advertiser.csubject.value;
	document.edit_advertiser.submit();
	}
}
function delete_subject()
{
	if (ProcessForm('A'))
	{
	document.edit_advertiser.backto.value="/cgi-bin/del_subject.cgi?aid=$puserid&sid="+document.edit_advertiser.csubject.value;
	document.edit_advertiser.submit();
	}
}
function default_subject()
{
	if (ProcessForm('A'))
	{
	document.edit_advertiser.backto.value="/cgi-bin/default_subject.cgi?aid=$puserid&sid="+document.edit_advertiser.csubject.value;
	document.edit_advertiser.submit();
	}
}
function tracking(ctype)
{
	if (ProcessForm('A'))
	{
		document.edit_advertiser.backto.value="/cgi-bin/tracking.cgi?aid=$puserid&ctype="+ctype;
		document.edit_advertiser.submit();
	}
}
function gen_tracking(ctype)
{
	if (ProcessForm('A'))
	{
	document.edit_advertiser.backto.value="/cgi-bin/gen_tracking.cgi?aid=$puserid&ctype="+ctype;
	document.edit_advertiser.submit();
	}
}
function delete_c3()
{
	if (ProcessForm('A'))
	{
	document.edit_advertiser.backto.value="/cgi-bin/delete_c3.cgi?aid=$puserid";
	document.edit_advertiser.submit();
	}
}
function add_from()
{
	if (ProcessForm('A'))
	{
	document.edit_advertiser.backto.value="/cgi-bin/from.cgi?aid=$puserid";
	document.edit_advertiser.submit();
	}
}
function edit_from()
{
	if (ProcessForm('A'))
	{
	document.edit_advertiser.backto.value="/cgi-bin/edit_from.cgi?aid=$puserid&sid="+document.edit_advertiser.from.value;
	document.edit_advertiser.submit();
	}
}
function delete_from()
{
	if (ProcessForm('A'))
	{
	document.edit_advertiser.backto.value="/cgi-bin/del_from.cgi?aid=$puserid&sid="+document.edit_advertiser.from.value;
	document.edit_advertiser.submit();
	}
}
function default_from()
{
	if (ProcessForm('A'))
	{
	document.edit_advertiser.backto.value="/cgi-bin/default_from.cgi?aid=$puserid&sid="+document.edit_advertiser.from.value;
	document.edit_advertiser.submit();
	}
}
function contact()
{
	if (ProcessForm('A'))
	{
	document.edit_advertiser.backto.value="/cgi-bin/contact.cgi?aid=$puserid";
	document.edit_advertiser.submit();
	}
}
function company()
{
	if (ProcessForm('A'))
	{
	document.edit_advertiser.backto.value="/cgi-bin/company_info.cgi?aid=$puserid";
	document.edit_advertiser.submit();
	}
}
function approval()
{
	if (ProcessForm('A'))
	{
	document.edit_advertiser.backto.value="/cgi-bin/approval.cgi?aid=$puserid";
	document.edit_advertiser.submit();
	}
}
function iapproval()
{
	if (ProcessForm('A'))
	{
	document.edit_advertiser.backto.value="/cgi-bin/approval.cgi?aid=$puserid&i=1";
	document.edit_advertiser.submit();
	}
}
function pixelrequest()
{
	if (ProcessForm('A'))
	{
	document.edit_advertiser.backto.value="/cgi-bin/send_pixel_request.cgi?aid=$puserid";
	document.edit_advertiser.submit();
	}
}
function update_seeds()
{
	if (ProcessForm('A'))
	{
	document.edit_advertiser.backto.value="/cgi-bin/advertiser_seedlist.cgi?aid=$puserid";
	document.edit_advertiser.submit();
	}
}
function preview_creative()
{
	if (ProcessForm('A'))
	{
	document.edit_advertiser.backto.value="/cgi-bin/camp_preview.cgi?format=H&campaign_id="+document.edit_advertiser.creative.value;
	document.edit_advertiser.submit();
	}
}
function validate_creative()
{
	if (ProcessForm('A'))
	{
	document.edit_advertiser.backto.value="/cgi-bin/validate.cgi?campaign_id="+document.edit_advertiser.creative.value;
	document.edit_advertiser.submit();
	}
}
function add_creative()
{
	url="/newcgi-bin/add_creative.cgi?aid=$puserid";
    var newwin = window.open(url, "Creative", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,width=900,height=500,left=25,top=50");
    newwin.focus();
}
function update_approval()
{
	if (ProcessForm('A'))
	{
	document.edit_advertiser.backto.value="/cgi-bin/advertiser_approval.cgi?aid=$puserid";
	document.edit_advertiser.submit();
	}
}
function addSubject(value,text)
{
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    edit_advertiser.csubject.add(newOpt);
}

function set_inactive()
{
	var d = new Date();
	var month_str = d.getMonth() + 1;
	if (month_str < 10)
	{
		month_str = "0" + month_str;
	}
	var year_str = d.getFullYear();
	var day_str = d.getDate();
	if (day_str < 10)
	{
		day_str = "0" + day_str;
	}
	var dstr=year_str + "-" + month_str + "-" + day_str; 
	document.edit_advertiser.idate.value=dstr;
}
   	function ProcessForm(Mode)
   	{
        var iopt;
        // validate your data first
        iopt = check_mandatory_fields();
        if (iopt == 0)
        {
            return false;
        }

        // if ok, go on to save
        return true;
    }

    function check_mandatory_fields()
    {
        if (document.edit_advertiser.name.value == "")
        {
            alert("You MUST enter a value for the Advertiser Name field."); 
			document.edit_advertiser.name.focus();
            return false;
        }
		if ((document.edit_advertiser.auto_url_sid.value != "") && (document.edit_advertiser.auto_cake_creativeid.value != ""))
		{
            alert("You CANNOT enter a value for both Auto URL SID and Auto Cake Creative fields."); 
			document.edit_advertiser.auto_url_sid.focus();
            return false;
		}
		var chk=document.getElementById('tracking_chk');
        if (chk.checked)
        {
		}
		else
		{
            alert("QA Advertiser URL for Missing Offer Link Parameters/Subids."+"\\n\\n"+"CONFIRM Cake Offer Links, Contracts & Creative Link Overrides have values appended."); 
			document.edit_advertiser.tracking_chk.focus();
            return false;
        }
		return true;
	}
</script>
</head>
<body link="#000000" vlink="#000000" alink="#000000">
<center>
<TABLE cellSpacing=0 cellPadding=0 border=0 bgcolor='#FFFFFF'>
<TBODY>
<TR bgcolor='#FFFFFF'>
<TD>
	<table border="0" cellpadding="0" cellspacing="0" width="800" align='center' border=0>
	<tr>
	<TD width=248 bgcolor='#FFFFFF'>
		<img border="0" src="/mail-images/header.gif"></TD>
	<TD width=328 bgcolor='#FFFFFF'>&nbsp;</TD>
	</tr> 
	<tr>
	<td colspan=3>
		<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
		<td align='center'><b><font face="Arial" size="2" color='#FFFFFF'>&nbsp;Edit Advertiser Information</FONT></b></td>
		</tr>
		<tr>
		<td>
		</td>
		</tr>
		</table>
	</td>
	</tr>
	</table>
end_of_html
	
if ( $pmesg ne "" ) 
{
	#---------------------------------------------------------------------------
	# Display mesg (if present) from module that called this module.
	#---------------------------------------------------------------------------
	print qq{ <script language="JavaScript">  \n } ;
	print qq{ 	alert("$pmesg");  \n } ;
	print qq{ </script>  \n } ;
}

print << "end_of_html";
</TD>
</TR>
<TR>
<TD vAlign=top align=left bgColor=#FFFFFF>

    <TABLE cellSpacing=0 cellPadding=10 bgColor=#FFFFFF border=0 width="100%">
    <TBODY>
    <TR>
    <TD vAlign=top align=left bgColor=#ffffff colSpan=10>

        <TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
        <TBODY>
        <TR>
        <TD vAlign=center align=left><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 
            size=3><B>Advertiser Information</B> </FONT></TD>
		</TR>
        <TR>
        <TD><IMG height=3 src="$images/spacer.gif"></TD>
		</TR> 
		</TBODY>
		</TABLE>

        <TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
        <TBODY>
        <TR>
        <TD colSpan=10><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
end_of_html

	print qq{ To UPDATE the advertiser information please make  \n } ;
	print qq{ the appropriate changes and select <B>Save</B>. \n } ;

print << "end_of_html" ;
			</FONT></TD>
		</TR>
		</TBODY>
		</TABLE>
end_of_html

if ($this_user_type eq "A") 
{
print <<"end_of_html";

        <FORM name=edit_advertiser action="advertiser_upd2.cgi" method=post onsubmit="return ProcessForm('A');" ENCTYPE="multipart/form-data" accept-charset="UTF-8">
		<input type=hidden name=backto value="">
		<input type=hidden name=oldpixelverified value="$pixel_verified">
		<input type=hidden name=old_unsub value="$unsub_image">

        <TABLE cellSpacing=0 cellPadding=0 width="100%" bgColor=#ffffff border=0>
        <TBODY>
        <TR>
        <TD>
            <TABLE cellSpacing=0 cellPadding=5 width="100%" border=0>
            <TBODY>
            <TR>
            <TD align=middle>

                <TABLE cellSpacing=0 cellPadding=0 width="100%" bgColor=#E3FAD1 border=0>
                <TBODY>
                <TR valign=top bgColor=#509C10>
                <TD vAlign=top align=left height=15><IMG src="$images/blue_tl.gif" 
					border=0 width="7" height="7"></TD>
                <TD height=15><IMG height=1 src="$images/spacer.gif" width=3 border=0></TD>
                <TD align=middle height=15>

                    <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
                    <TBODY>
                    <TR bgColor=#509C10 height=15>
                    <TD align=middle width="100%"><FONT 
						face=Verdana,Arial,Helvetica,sans-serif color=white size=2>
						<B>Advertiser Information</B></FONT></TD>
					</TR>
					</TBODY>
					</TABLE>
				</TD>
                <TD height=1><IMG height=1 src="$images/spacer.gif" width=3 border=0></TD>
                <TD vAlign=top align=right bgColor=#509C10 height=1><IMG 
                    src="$images/blue_tr.gif" border=0 width="7" height="1"></TD>
				</TR>
                <TR bgColor=#E3FAD1>
                <TD><IMG height=1 src="$images/spacer.gif" width=3></TD>
                <TD align=middle><IMG height=1 src="$images/spacer.gif" width=3></TD>
                <TD align=middle>
                    <TABLE cellSpacing=0 valign=top cellPadding=0 width="100%" border=0>
                    <TBODY>
                    <TR> <!-- -------- Advertiser Name -------------- -->
                    <TD vAlign=center noWrap align=right width="20%">
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
						Advertiser Name: </FONT></TD>
                    <TD vAlign=center align=left>
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
						<INPUT size=40 maxlength=80 value="$name" name=name>
						</FONT>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/cgi-bin/replace_url.cgi?aid=$puserid">Find/Replace URLs</a>&nbsp;&nbsp;<a href="/cgi-bin/rep_adv_subject_creative.cgi?aid=$puserid" target=_blank>Subject/Creative Stats</a>&nbsp;&nbsp;<a href="/cgi-bin/rep_adv_main.cgi?aid=$puserid" target=_blank>Subject/Creative Raw Stats</a>&nbsp;&nbsp;<a href="/cgi-bin/replace_advertiser_frame.cgi?aid=$puserid">Replace Advertiser</a>&nbsp;&nbsp;<a href="/cgi-bin/adv_preview.cgi?aid=$puserid" target=_blank>Preview All Creatives</a></td>
					</TR>
                    <TR> <!-- -------- Friendly Advertiser Name -------------- -->
                    <TD vAlign=center noWrap align=right width="20%">
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>Friendly Advertiser Name: </FONT></TD>
                    <TD vAlign=center align=left>
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
						<INPUT size=40 maxlength=80 value="$friendly_name" name=friendly_name></FONT>&nbsp;&nbsp;<a href="/cgi-bin/advertiser_setup_new.cgi?aid=$puserid">Setup Creative/Subject/From Rotation</a>&nbsp;&nbsp;<a href="/cgi-bin/hitpath_creative_deploy_it.cgi?aid=$puserid">Deploy HitPath Creative</a>&nbsp;&nbsp;<a href="/cgi-bin/creative_export_adv.cgi?aid=$puserid" target=_blank>Download All Creatives</a>&nbsp;&nbsp;<a href=advertiser_upload.cgi?aid=$puserid>Files</a>&nbsp;&nbsp;<a href="Javascript:auto_popup();">Auto populate</a>&nbsp;&nbsp;<a href="Javascript:remove_unapproved();">Remove Unapproved</a></TD>
					</TR>

					<TR> <!-- -------- Physical Address (Changed to notes) -------------- -->
                    <TD vAlign=center noWrap align=right width="20%">
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
						Campaign Notes: </FONT></TD>
                    <TD vAlign=center align=left>
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
						<textarea name=address rows=5 cols=80>$physical_addr</textarea></FONT>&nbsp;&nbsp;<a href="/cgi-bin/emailreach_creative.cgi?aid=$puserid" target=_blank>EmailReach - Test All Creative</a>&nbsp;&nbsp;<a href="/cgi-bin/del_mon_tag.cgi?aid=$puserid&cytpe=D" target=_blank>Delivery Monitor - Test All Creative</a></TD>
						<INPUT type="hidden" name="puserid" value="$puserid">
						<INPUT type="hidden" name="pmode" value="$pmode">
					</TR>
                    <TR>
                    <TD align=middle><IMG height=3 src="$images/spacer.gif" width=3></TD>
					</TR>
<tr><td>&nbsp;</td></tr>
<tr><td valign=top><a href="javascript:company();">Company Info</a>: <br>&nbsp;</b></td>
<td>
<table border="1" width="59%" id="table1">
	<tr>
		<td width="119"><b>Company Name</b></td>
		<td><A HREF="company_disp.cgi?pmode=U&company_id=$company_id" target=_blank>$company</a></td>
	</tr>
	<tr>
		<td width="119"><b>Campaign Manager</b></td>
		<td>$manager_name</td>
	</tr>
	<tr>
		<td width="119"><b>Main Contact</b></td>
		<td>$cname</td>
	</tr>
	<tr>
		<td width="119"><b>Phone</b></td>
		<td>$phone</td>
	</tr>
	<tr>
		<td width="119"><b>Email</b></td>
		<td><a href="mailto:$cemail">$cemail</a></td>
	</tr>
	<tr>
		<td width="119"><b>AIM</b></td>
		<td>$aim</td>
	</tr>
	<tr>
		<td width="119"><b>Reporting Website</b></td>
		<td><a href="$website" target=_blank>$website</a></td>
	</tr>
	<tr>
		<td width="119"><b>Username</b></td>
		<td>$username</td>
	</tr>
	<tr>
		<td width="119"><b>Password</b></td>
		<td>$password</td>
	</tr>
	<tr>
		<td width="119"><b>Physical Address</b></td>
		<td>$addr</td>
	</tr>
	<tr>
		<td width="119"><b>Notes</b></td>
		<td>$notes</td>
	</tr>
	<tr>
		<td width="119"><b>Affiliate Platform</b></td>
		<td>$affiliate_name</td>
	</tr>
	<tr>
		<td width="119"><b>Seedlist</b></td>
		<td>$seedlist</td>
	</tr>
	<tr>
		<td width="119"><b><a href="javascript:approval();">Approval Email Addresses</a></b></td>
		<td>$approval_str</td>
	</tr>
end_of_html
if ($cpasscard eq "")
{
	print "<tr><td width=119><b>Passcard</b></td><td></td></tr>\n";
}
else
{
	print "<tr><td width=119><b>Passcard</b></td><td><a href=\"file://10.128.1.69/intern/#passcards/$cpasscard\">$cpasscard</a></td></tr>\n";
}
print<<"end_of_html";
	<tr>
		<td width="119"><input type=checkbox name=tracking_chk id=tracking_chk value="Y"><b>Tracking</b></td>
		<td>$tracking</a></td>
	</tr>
end_of_html
my $sname;
srand(rand time());
my @c=split(/ */, "bcdfghjklmnprstvwxyz");
my @v=split(/ */, "aeiou");
$sname = $c[int(rand(20))];
$sname = $sname . $v[int(rand(5))];
$sname = $sname . $c[int(rand(20))];
$sname = $sname . $v[int(rand(5))];
$sname = $sname . $c[int(rand(20))];
$sname = $sname . int(rand(999999));
$sql = "delete from approval_list where advertiser_id=$puserid and date_added < date_sub(curdate(),interval 7 day)";
my $rows=$dbhu->do($sql);
$sql = "insert into approval_list(advertiser_id,uid,date_added) values($puserid,'$sname',now())";
my $rows=$dbhu->do($sql);
print<<"end_of_html";
</table>
</td></tr>
	<tr><td></td><td><a href="/cgi-bin/contact.cgi?aid=$puserid" target=_new>Show Old Company Info</a></td></tr>
<tr><td>&nbsp;</td></tr>
<tr><td valign=top><a href="view_thumbnails.cgi?aid=$puserid" target=_blank><b>Creative:</b></a></td>
<td><select name="creative">
end_of_html
#
#   Get creative 
#
$sql = "select creative_id, creative_name,original_flag, trigger_flag, approved_flag,mediactivate_flag,internal_approved_flag,copywriter,status from creative where status in ('A','I') and advertiser_id=$puserid order by creative_name";
$sth = $dbhq->prepare($sql);
$sth->execute();
my $cid;
my $cname;
my $oflag;
my $tflag;
my $aflag;
my $mflag;
my $internal_aflag;
my $temp_str;
my $creative_status;

while (($cid,$cname,$oflag,$tflag,$aflag,$mflag,$internal_aflag,$copywriter,$creative_status) = $sth->fetchrow_array())
{
	$temp_str = $cid . " - " .$cname . " (";
	if ($creative_status eq "A")
	{
		$temp_str=$temp_str . "Active - ";
	}
	elsif ($creative_status eq "I")
	{
		$temp_str=$temp_str . "Inactive - ";
	}
	elsif ($creative_status eq "D")
	{
		$temp_str=$temp_str . "Deleted - ";
	}
	if ($tflag eq "Y")
	{
		$temp_str = $temp_str . "TRIGGER - ";
	}
	if ($oflag eq "Y")
	{
		$temp_str = $temp_str . "O ";
	}
	else
	{
		$temp_str = $temp_str . "A ";
	}
	if ($copywriter eq "Y")
	{
		$temp_str = $temp_str . "C ";
	}
	if ($mflag eq "Y")
	{
		$temp_str = $temp_str . " - M ";
	}
	if ($aflag eq "Y")
	{
		$temp_str = $temp_str . "- AA ";
	}
	else
	{
		$temp_str = $temp_str . "- NA! ";
	}
	if ($internal_aflag eq "Y")
	{
		$temp_str = $temp_str . "- IA)";
	}
	else
	{
		$temp_str = $temp_str . ")";
	}
	
	print "<option value=$cid>$temp_str</option>\n";
}
$sth->finish();
print<<"end_of_html";
</select><input type="button" value="Add" name="B21" onClick="add_creative();"><input type="button" value="Edit" onClick="edit_creative();"><input type="button" value="Delete" name="B22" onClick="delete_creative();"><input type="button" value="Activate" name="B22" onClick="activate_creative();"><input type="button" value="Clear Stats" name="B28"><input type="button" value="Preview" onClick="preview_creative();"><input type="button" value="Validate" onClick="validate_creative();"><input type="button" value="Copy" onClick="copy_creative();">&nbsp;&nbsp;<a href="/cgi-bin/adv_preview.cgi?aid=$puserid" target=_blank>View All Creatives</a<br><br></td></tr>
<tr><td valign=top><b><a href="Javascript:subject();">Subject</a>: </b>(Select a default (ALL is for clearing stats))</td> 
<td><select name="csubject">
</select>
<script language="JavaScript">
end_of_html
#
#	Get subjects for advertiser
#
$sql = "select subject_id,advertiser_subject,approved_flag,original_flag,internal_approved_flag,copywriter,status from advertiser_subject where advertiser_id=$puserid and status in ('A','I') order by advertiser_subject";
$sth = $dbhq->prepare($sql);
$sth->execute();
my $csubject;
my $isid;
my $aflag;
my $oflag;
my $subject_status;
while (($isid,$csubject,$aflag,$oflag,$internal_aflag,$copywriter,$subject_status) = $sth->fetchrow_array())
{
    $temp_str = $isid . " - " . $csubject. " (";
	if ($subject_status eq "A")
	{
		$temp_str=$temp_str . "Active - ";
	}
	elsif ($subject_status eq "I")
	{
		$temp_str=$temp_str . "Inactive - ";
	}
	elsif ($subject_status eq "D")
	{
		$temp_str=$temp_str . "Deleted - ";
	}
    if ($oflag eq "Y")
    {
        $temp_str = $temp_str . "O ";
    }
    else
    {
        $temp_str = $temp_str . "A ";
    }
    if ($copywriter eq "Y")
    {
        $temp_str = $temp_str . "C ";
    }
	if ($aflag eq "Y")
	{
		$temp_str = $temp_str . "- AA ";
	}
	else
	{
		$temp_str = $temp_str . "- NA! ";
	}
	if ($internal_aflag eq "Y")
	{
		$temp_str = $temp_str . "- IA)";
	}
	else
	{
		$temp_str = $temp_str . ")";
	}
	$temp_str=~s/"/\\"/g;
	print "addSubject($isid,\"$temp_str\");\n";
}
$sth->finish();
print<<"end_of_html";
</script>
<input type="button" value="Add" name="B21" onClick="subject();"><input type="button" value="Edit" onClick="edit_subject();"><input type="button" value="Delete" name="B22" onClick="delete_subject();"><input type="button" value="Activate" name="B22" onClick="activate_subject();"><input type="button" value="Set as Default" onClick="default_subject();"><input type="button" value="Clear Stats" name="B29"><br><br></td></tr> 
<tr><td valign=top><b><a href="JavaScript:add_from();">From</a>: </b>(Select a default (ALL is for clearing stats))</td>
<td><select name="from">
end_of_html
#
#	Get from lines for advertiser
#
$sql = "select from_id,advertiser_from,approved_flag,original_flag,internal_approved_flag,copywriter,status from advertiser_from where advertiser_id=$puserid order by advertiser_from";
$sth = $dbhq->prepare($sql);
$sth->execute();
my $cfrom;
my $fid;
my $aflag;
my $oflag;
my $internal_aflag;
my $from_status;
while (($fid,$cfrom,$aflag,$oflag,$internal_aflag,$copywriter,$from_status) = $sth->fetchrow_array())
{
    $temp_str = $fid. " - ". $cfrom . " (";
	if ($from_status eq "A")
	{
		$temp_str=$temp_str . "Active - ";
	}
	elsif ($from_status eq "I")
	{
		$temp_str=$temp_str . "Inactive - ";
	}
	elsif ($from_status eq "D")
	{
		$temp_str=$temp_str . "Deleted - ";
	}
    if ($oflag eq "Y")
    {
        $temp_str = $temp_str . "O ";
    }
    else
    {
        $temp_str = $temp_str . "A ";
    }
    if ($copywriter eq "Y")
    {
        $temp_str = $temp_str . "C ";
    }
	if ($aflag eq "Y")
	{
		$temp_str = $temp_str . "- AA ";
	}
	else
	{
		$temp_str = $temp_str . "- NA! ";
	}
	if ($internal_aflag eq "Y")
	{
		$temp_str = $temp_str . "- IA)";
	}
	else
	{
		$temp_str = $temp_str . ")";
	}
	print "<option value=\"$fid\">$temp_str</option>\n";
}
$sth->finish();
print<<"end_of_html";
</select><input type="button" value="Add" onClick="add_from();"><input type="button" value="Edit" onClick="edit_from();"><input type="button" value="Delete" onClick="delete_from();"><input type="button" value="Activate" name="B22" onClick="activate_from();"><input type="button" value="Set as Default" onClick="default_from();"><input type="button" value="Clear Stats" name="B29"><br><br></td></tr> 
<tr><td>&nbsp;</td></tr>
<tr><td valign=top><b><a href="javascript:iapproval();">Internal Approval Page</a> </b><br></td></tr>

<tr><td>&nbsp;</td></tr>
<tr><td colspan=2>
<table width=50% bgColor=#E3FAD1>
<tr><th align=left><a href=advertiser_upload.cgi?aid=$puserid>Uploaded Files</a></th></tr>
end_of_html
my $file;
opendir(DIR, "/home/adv/$puserid");
while (defined($file = readdir(DIR)))
{
    if ($file eq "." || $file eq "..")
    {
        # skip files . and ..
        next;
    }
	print "<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"http://mailingtool.routename.com:83/adv/$puserid/$file\" target=_blank>$file</a></td></tr>\n";
}
closedir(DIR);
print<<"end_of_html";
</table></td></tr>
<tr><td>&nbsp;</td></tr>
<tr><td> <b>Status:</b><br></td>
<td align=left>
end_of_html
my @STATUS=("R","T","A","U","I","P","C","B","W");
my @STATUS1=("Requested","Testing","Active","Update","Inactive","Paused","Pending","Waiting for Pixel","Waiting for Approval");
my $i=0;
while ($i <= $#STATUS)
{
	if ($cstatus eq $STATUS[$i])
	{
		print "<input type=radio checked name=cstatus value=\"".$STATUS[$i]."\">".$STATUS1[$i]."&nbsp;&nbsp;";
	}
	else
	{
		print "<input type=radio name=cstatus value=\"".$STATUS[$i]."\">".$STATUS1[$i]."&nbsp;&nbsp;";
	}
	$i++;
}
print<<"end_of_html";
<br><br></td>
</tr>
<tr><td> <b>Country:</b><br></td>
<td><select name=countryID>
end_of_html
my $cID;
my $country_name;
my $sthdd1;
my $cnt1;
$sql="select countryID,countryName from Country where visible=1 order by countryName"; 
my $sthdd=$dbhu->prepare($sql);
$sthdd->execute();
while (($cID,$country_name)=$sthdd->fetchrow_array())
{
	if ($cID == $countryID)
	{
		print "<option value=$cID selected>$country_name</option>";
	}
	else
	{
		print "<option value=$cID>$country_name</option>";
	}
}
$sthdd->finish();
print<<"end_of_html";
</select></td><br><br></tr>
<tr><td> <b>Auto URL SID <i>Seperate multiple sids by comma(i.e., 24,35).  First one will be for {{URL}}, second for {{URL1}}, etc</i>:</b></td><td><input type=text name=auto_url_sid id=auto_url_side value="$auto_url_sid" size=30 maxlength=255></td>
<br><br></tr>
end_of_html
if ($linkType eq "XLME")
{
	print qq^<tr><td> <b>Link Type: </b></td><td><select name=linkType><option value=XLM>XLM</option><option value=XLME selected>XLME</option></select></td></tr>^;
}
else
{
	print qq^<tr><td> <b>Link Type: </b></td><td><select name=linkType><option value=XLM selected>XLM</option><option value=XLME>XLME</option></select></td></tr>^;
}
print<<"end_of_html";
<tr><td> <b>Auto CAKE Creative ID <i>Seperate multiple sids by comma(i.e., 24,35).  First one will be for {{URL}}, second for {{URL1}}, etc</i>:</b></td><td><input type=text name=auto_cake_creativeid id auto_cake_creativeid value="$auto_cake_creativeid" size=30 maxlength=255></td>
<br><br></tr>
<tr><td height=10>&nbsp;</td></tr>
<tr>
  <td valign=top><b>HitPath Tracking Pixel:</b></td>
  <td><input type=text name="hitpath_tracking_pixel" value="$hitpath_tracking_pixel" maxlength=255 size=120></td>
</tr>
<tr><td valign=top> <b>Advertiser URL:</b><br></td>
<td><input maxLength="500" size="180" name="orig_advertiser_url" value="$advertiser_url"></td></tr>
<tr><td valign=top> <b>Landing Page:</b><br></td>
<td><input maxLength="500" size="180" name="landing_page" value='$landing_page'><br><br></td></tr>
<tr><td><b>Default Currency:</td>
<td><select name=defaultCurrency>
end_of_html
my @CURR=("AUD","BR","Danish Krone","Dollars","Euro","Norwegian Krona","Pounds","Swedish Kroner");
foreach my $c (@CURR)
{
	if ($defaultCurrency eq $c)
	{
		print qq^<option value=$c selected>$c</option>^;
	}
	else
	{
		print qq^<option value=$c>$c</option>^;
	}
}
print<<"end_of_html";
</select>
</td></tr>
<tr><td>
<b>Payin(Pounds):</b><br></td>
<td><input maxLength="255" size="50" value="$payout_pounds" name="payout_pounds"><br><br></td></tr>
<tr><td>
<b>Payin(Euro):</b><br></td>
<td><input maxLength="255" size="50" value="$payout_euro" name="payout_euro"><br><br></td></tr>
<tr><td> <b>Payin(Dollars):</b><br></td>
<td>
											<input maxLength="255" size="50" value="$payout" name="payout"><br><br>
</td></tr>
<tr><td>
<b>Payin(AUD):</b><br></td>
<td><input maxLength="255" size="50" value="$payout_aud" name="payout_aud"><br><br></td></tr>
<tr><td>
<b>Payin(BR):</b><br></td>
<td><input maxLength="255" size="50" value="$payout_br" name="payout_br"><br><br></td></tr>
<tr><td>
<b>Payin(Norwegian Krona):</b><br></td>
<td><input maxLength="255" size="50" value="$payout_no" name="payout_no"><br><br></td></tr>
<tr><td>
<b>Payin(Swedish Kroner):</b><br></td>
<td><input maxLength="255" size="50" value="$payout_sk" name="payout_sk"><br><br></td></tr>
<tr><td>
<b>Payin(Danish Krone):</b><br></td>
<td><input maxLength="255" size="50" value="$payout_dkk" name="payout_dkk"><br><br></td></tr>
<input type=hidden name=ecpm value="$ecpm">
<tr><td> <b>Scrub Percent:</b><br></td>
<td>
											<input maxLength="3" size="4" value="$scrub_percent" name="scrub_percent"><br><br>
</td></tr>
<tr><td> <b>Allocation Cap:</b><br></td>
<td>
											<input maxLength="10" size="10" value="$allocationCapCnt" name="allocationCapCnt">&nbsp;&nbsp;<select name=allocationCap>
end_of_html
my @ACAP=("None","Monthly","Weekly","Daily");
foreach my $a (@ACAP)
{
	if ($a eq $allocationCap)
	{
		print "<option selected value=$a>$a</option>";
	}
	else
	{
		print "<option value=$a>$a</option>";
	}
}
print<<"end_of_html";
</select><br><br>
</td></tr>
<tr><td> <b>Margin Percent:</b><br></td>
<td>
											<input maxLength="3" size="4" value="$margin_percent" name="margin_percent"><br><br>
</td></tr>
<tr height=20><td colspan=2></td></tr>
<tr><td> <b>Type:</b></td>
<td>
end_of_html
if ($offer_type eq "CPA")
{
	print "<input type=\"radio\" CHECKED value=\"CPA\" name=\"deal_type\"> CPA\n";
}
else
{
	print "<input type=\"radio\" value=\"CPA\" name=\"deal_type\"> CPA\n";
}
if ($offer_type eq "CPC")
{
	print "<input type=\"radio\" CHECKED value=\"CPC\" name=\"deal_type\"> CPC\n";
}
else
{
	print "<input type=\"radio\" value=\"CPC\" name=\"deal_type\"> CPC\n";
}
if ($offer_type eq "CPS")
{
	print "<input type=\"radio\" CHECKED value=\"CPS\" name=\"deal_type\"> CPS\n";
}
else
{
	print "<input type=\"radio\" value=\"CPS\" name=\"deal_type\"> CPS\n";
}
if ($offer_type eq "CPM")
{
	print "<input type=\"radio\" CHECKED value=\"CPM\" name=\"deal_type\"> CPM\n";
}
else
{
	print "<input type=\"radio\" value=\"CPM\" name=\"deal_type\"> CPM\n";
}
if ($offer_type eq "REV")
{
	print "<input type=\"radio\" CHECKED value=\"REV\" name=\"deal_type\"> REV Share\n";
}
else
{
	print "<input type=\"radio\" value=\"REV\" name=\"deal_type\"> REV Share\n";
}
print<<"end_of_html";
<br><br>
</td></tr>
<br></td></tr>
<tr><td>
<b><u>Existing Suppression Files</u>: </b>(to be used if there are multiple 
offers using the same file)<br></td>
<td><select name="suppid1">
end_of_html
$sql="select list_id,list_name from vendor_supp_list_info where status='A' order by list_name";
$sth = $dbhq->prepare($sql);
$sth->execute();
my $lsid;
my $tsname;
while (($lsid,$tsname) = $sth->fetchrow_array())
{
	if ($vendor_suppid == $lsid)
	{
		print "<option selected value=$lsid>$tsname</option>\n";
	}
	else
	{
		print "<option value=$lsid>$tsname</option>\n";
	}
} 
$sth->finish();
print<<"end_of_html";
</select>
</td></tr>
<tr><td valign=top><b>Suppression File:</b><br></td>
<td><input type="file" maxLength="255" size="50" name="supp_file" value=$supp_file><br></td></tr>
<tr><td valign=top><b>File Date(yyyy-mm-dd):</b><br></td>
<td><input type="text" maxLength="20" size="20" name="filedate">&nbsp;&nbsp;Upload Immediately&nbsp;<input type=checkbox value="Y" name="immediate_upload"></td></tr>
<tr><td valign=top><b>MD5 Suppression: 
end_of_html
if ($md5_suppression eq "Y")
{
	print "<input type=checkbox name=\"md5_suppression\" checked value=Y>\n";
}
else
{
	print "<input type=checkbox name=\"md5_suppression\" value=Y>\n";
}
print<<"end_of_html";
<br><br>
</td></tr>
<tr><td>
<b><u>Advertiser to use for MD5</u>: </b>(to be used only if MD5 checked)<br></td>
<td><select name="md5suppid"><option value=0>Self</option>
end_of_html
$sql="select advertiser_id,advertiser_name,md5_last_updated from advertiser_info where md5_suppression='Y' and vendor_supp_list_id=0 and md5_last_updated >= date_sub(curdate(),interval 30 day) and advertiser_id != $puserid order by 1"; 
$sth = $dbhq->prepare($sql);
$sth->execute();
my $lsid;
my $tsname;
my $tdate;
while (($lsid,$tsname,$tdate) = $sth->fetchrow_array())
{
	if (($vendor_suppid == $lsid) and ($md5_suppression eq "Y"))
	{
		print "<option selected value=$lsid>$tsname ($tdate)</option>\n";
	}
	else
	{
		print "<option value=$lsid>$tsname ($tdate)</option>\n";
	}
} 
$sth->finish();
print<<"end_of_html";
</select>
</td></tr>
<tr><td valign=top><b>Suppression URL: (just to know where to access the files for quick reference)</b><br></td>
<td><input maxLength="255" size="50" value="$supp_url" name="supp_url">
end_of_html
if ($auto_download eq "Y")
{
	print "<input type=\"checkbox\" name=\"auto_download\" checked value=\"Y\">(check box for auto download once a week)";
}
else
{
	print "<input type=\"checkbox\" name=\"auto_download\" value=\"Y\">(check box for auto download once a week)";
}
print<<"end_of_html";
<br></td></tr>
<tr><td valign=top><b>Username:<br> </b></td>
<td><input maxLength="255" size="50" name="supp_username" value="$supp_username"><br></td></tr>
<tr><td valign=top><b>Password:</b><br></td>
<td><input maxLength="255" size="50" name="supp_password" value="$supp_password"><br><br></td></tr>
<tr><td valign=top><b>Advertiser Unsubscribe URL</b><br></td>
<td><input maxLength="255" size="50" name="unsub_link" value="$unsub_link"><br><br></td></tr>
<tr><td>&nbsp;</td></tr>
<tr><td><b>Direct Suppression URL:</b></td>
<td><input maxLength="255" size="50" value="$direct_suppression_url" name="direct_suppression_url"></td></tr>
<tr><td>&nbsp;</td></tr>
<input type=hidden name=direct_track value=$direct_track>
end_of_html
print "<tr><td valign=top><b>Unsubscribe To Use: </b><br></td>\n";
if ($unsub_use eq "IMAGE")
{
	print "<td><input type=radio name=unsub_use value=IMAGE checked>Image&nbsp;&nbsp;<input type=radio value=TEXT name=unsub_use>Text</td>\n";
}
else
{
	print "<td><input type=radio name=unsub_use value=IMAGE>Image&nbsp;&nbsp;<input type=radio value=TEXT name=unsub_use checked>Text</td>\n";
}
print "</tr>";
if ($unsub_image ne "")
{
    $_=$unsub_image;
    if ( /\// )
    {
		print "<tr><td valign=top><b>Unsubscribe Image: <a href=\"http://www.affiliateimages.com/images/$unsub_image\" target=\"_blank\">$unsub_image</a></b><br></td>\n";
	}
	else
	{
		print "<tr><td valign=top><b>Unsubscribe Image: <a href=\"http://www.affiliateimages.com/images/unsub/$unsub_image\" target=\"_blank\">$unsub_image</a></b><br></td>\n";
	}
}
else
{
	print "<tr><td valign=top><b>Unsubscribe Image: </b><br></td>\n";
}
print<<"end_of_html";
<td><input type="file" maxLength="255" size="50" name="unsub_image" value="">&nbsp;&nbsp;<input type="button" value="Delete" name="B22" onClick="delete_unsubimg();"><br><br><br></td></tr>
<tr><td colspan=2>
end_of_html
if ($replace_flag eq "N")
{
print<<"end_of_html";
<b>Do not automatically replace href's with {{ADV_UNSUB_URL}}</b><input type=checkbox checked name="replace_flag" value="N"><br><br>
end_of_html
}
else
{
print<<"end_of_html";
<b>Do not automatically replace href's with {{ADV_UNSUB_URL}}</b><input type=checkbox name="replace_flag" value="N"><br><br>
end_of_html
}
$unsub_text=~s/&reg;/[[reg]]/g;
$unsub_text=~s/&tm;/[[tm]]/g;
$unsub_text=~s/&dagger;/[[dagger]]/g;
$unsub_text=~s/&trade;/[[trade]]/g;
$unsub_text=CGI::escapeHTML($unsub_text);
print<<"end_of_html";
</td></tr>
<tr><td valign=top><b>Unsubscribe Text:</b></td><td><textarea name=unsub_text cols=80 rows=5>$unsub_text</textarea></td></tr>
<tr><td height=10></td></tr>
<tr><td valign=top><b>Category:</b><br></td>
<td><select name="category_id">
end_of_html
#
#	Get categories
#
$sql = "select category_id,category_name from category_info where status = 'A' order by category_name";
$sth = $dbhq->prepare($sql);
$sth->execute();
my $catid;
my $cname;
while (($catid,$cname) = $sth->fetchrow_array())
{
	if ($adv_catid == $catid)
	{
		print "<option selected value=\"$catid\">$cname</option>\n";
	}
	else
	{
		print "<option value=\"$catid\">$cname</option>\n";
	}
}
$sth->finish();
print<<"end_of_html";
</select><br><br></td></tr>
<tr><td colspan=2>&nbsp;</td></tr>
<tr><td colspan=2><hr width-=100% height=2></td></tr>
<tr><td> <b>Pixel Verified:</b><br></td>
<td>
end_of_html
if ($pixel_verified eq "Y")
{
	print "<input type=radio CHECKED value=Y name=pixel_verified> Y\n";
}
else
{
	print "<input type=radio value=Y name=pixel_verified> Y\n";
}
if ($pixel_verified eq "N")
{
	print "<input type=radio CHECKED value=N name=pixel_verified> N\n";
}
else
{
	print "<input type=radio value=N name=pixel_verified> N\n";
}
if ($pixel_verified eq "?")
{
	print "<input type=radio CHECKED value=? name=pixel_verified> ?\n";
}
else
{
	print "<input type=radio value=? name=pixel_verified> ?\n";
}
print<<"end_of_html";
<br><br>
</td></tr>
<input type=hidden name=third_pixel value=$third_pixel>
<input type=hidden name=third_tracking_pixel1 value=$third_tracking_pixel1>
<input type=hidden name=third_tracking_pixel2 value=$third_tracking_pixel2>
<input type=hidden name=tracking_pixel value=$tracking_pixel>
<tr><td><a href="javascript:pixelrequest();">Send Pixel Request</a> </td></tr>
<tr><td colspan=2>
<b>Pixel Requested</b>
end_of_html
if ($pixel_requested eq "Y")
{
	print "<input type=\"checkbox\" name=\"pixel_requested\" checked value=\"Y\">\n";
}
else
{
	print "<input type=\"checkbox\" name=\"pixel_requested\" value=\"Y\">\n";
}
print<<"end_of_html";
<b>Pixel Placed</b>
end_of_html
if ($pixel_placed eq "Y")
{
	print "<input type=\"checkbox\" name=\"pixel_placed\" checked value=\"Y\">\n";
}
else
{
	print "<input type=\"checkbox\" name=\"pixel_placed\" value=\"Y\">\n";
}
print<<"end_of_html";
</td></tr>
<tr height=20><td colspan=2></td></tr>
<tr><td valign=top><b><a href="Javascript:tracking('N');">Redirect URL</a>: (in parens = listname)</b><br></td>
<td><select name=advertiser_url>
end_of_html
#$sql = "select tracking_id,url,code,date_added,date_format(date_added,\"%m/%d/%y\"),company,daily_deal from advertiser_tracking, user where advertiser_id=$puserid and client_id=user.user_id and daily_deal in ('N','T','Y') order by code";
$sql = "select tracking_id,url,code,date_added,date_format(date_added,\"%m/%d/%y\"),company,daily_deal from advertiser_tracking, user where advertiser_id=$puserid and client_id=user.user_id and daily_deal in ('N','T','Y') order by company";
$sth = $dbhq->prepare($sql);
$sth->execute();
my ($url,$code,$date_added,$fdate,$company,$tracking_id,$daily_deal);
while (($tracking_id,$url,$code,$date_added,$fdate,$company,$daily_deal) = $sth->fetchrow_array())
{
	if ($daily_deal eq "Y")
	{
		print "<option value=\"$tracking_id\">$url ($code - $company - Daily) $fdate</option>\n";
	}
	elsif ($daily_deal eq "T")
	{
		print "<option value=\"$tracking_id\">$url ($code - $company - Trigger) $fdate</option>\n";
	}
	else
	{
		print "<option value=\"$tracking_id\">$url ($code - $company) $fdate</option>\n";
	}
}
$sth->finish();
print<<"end_of_html";
</select><input type="button" value="Delete" name="B22" onClick=delete_tracking('N');>&nbsp;&nbsp;<input type="button" value="Gen URLs" name="B22" onClick=gen_tracking('N');>&nbsp;&nbsp;<input type="button" value="Remove c3" name="B22" onClick=delete_c3();></br></br></td></tr>
<tr><td valign=top><b><a href="/cgi-bin/send_approval.cgi?aid=$puserid&i=1&cemail=ALL&uid=$sname" target=_blank">Send Internal Approval:</a> </b><br></td>
<td><select name=iapproval size=2>
<option value="group.approvals\@zetainteractive.com">group.approvals\@zetainteractive.com</option>
end_of_html
if ($manager_email ne "")
{
	print "<option value=\"$manager_email\">$manager_email</option>\n";
}
print<<"end_of_html";
</select>
</td></tr>
<tr><td colspan=2><hr width-=100% height=2></td></tr>
<tr><td valign=top><b>Seeded E-mail Addresses:</b>(get's any e-mail that goes out for this advertiser)<br></td>
<td><select name=seeded>
end_of_html
$sql="select email_addr from advertiser_seedlist where advertiser_id=$puserid order by email_addr";
$sth = $dbhq->prepare($sql);
$sth->execute();
my $email_addr; 
while (($email_addr) = $sth->fetchrow_array())
{
	print "<option value=$email_addr>$email_addr</option>\n";
}
$sth->finish();
print<<"end_of_html";
</select><input type="button" value="Update Seeds" name="B22" onClick=update_seeds();><br><br><br><br></td></tr>
<tr><td colspan=2><i>These apply only to advertisers in Testing</i></td></tr>
<tr><td><b>Date Requested(yyyy-mm-dd):</b></td><td><input type=text name=date_requested value="$date_requested" size=11 maxlength=10></td></tr>
<tr><td><b>Requested By:</b></td><td><select name=manager_id>
end_of_html
$sql="select manager_id,manager_name from CampaignManager order by manager_id";
$sth=$dbhu->prepare($sql);
$sth->execute();
my $mid;
my $mname;
while (($mid,$mname)=$sth->fetchrow_array())
{
	if ($mid == $manager_id)
	{
		print "<option value=$mid selected>$mname</option>\n";
	}
	else
	{
		print "<option value=$mid>$mname</option>\n";
	}
}
$sth->finish();
if ($sourceInternal eq "Y")
{
	$sourceInternal="checked";
}
else
{
	$sourceInternal="";
}
if ($sourceNetwork eq "Y")
{
	$sourceNetwork="checked";
}
else
{
	$sourceNetwork="";
}
if ($sourceDisplay eq "Y")
{
	$sourceDisplay="checked";
}
else
{
	$sourceDisplay="";
}
if ($sourceLinkOut eq "Y")
{
	$sourceLinkOut="checked";
}
else
{
	$sourceLinkOut="";
}
if ($sourceSearch eq "Y")
{
	$sourceSearch="checked";
}
else
{
	$sourceSearch="";
}
if ($sourceSocial eq "Y")
{
	$sourceSocial="checked";
}
else
{
	$sourceSocial ="";
}
if ($sourceIncent eq "Y")
{
	$sourceIncent ="checked";
}
else
{
	$sourceIncent="";
}
if ($sourceGold eq "Y")
{
	$sourceGold ="checked";
}
else
{
	$sourceGold="";
}
if ($sourceOrange eq "Y")
{
	$sourceOrange ="checked";
}
else
{
	$sourceOrange="";
}
if ($sourceGreen eq "Y")
{
	$sourceGreen ="checked";
}
else
{
	$sourceGreen="";
}
if ($sourcePurple eq "Y")
{
	$sourcePurple ="checked";
}
else
{
	$sourcePurple="";
}
if ($sourceBlue eq "Y")
{
	$sourceBlue ="checked";
}
else
{
	$sourceBlue="";
}
if ($sourceOrigin eq "Y")
{
	$sourceOrigin ="checked";
}
else
{
	$sourceOrigin="";
}
print<<"end_of_html";
</select><br><br></td></tr>
<tr><td><b>Traffic Source</b></td><td><input type=checkbox value=Y name=sourceinternal $sourceInternal>Internal&nbsp;&nbsp;<input type=checkbox value=Y name=sourceGold $sourceGold>Gold (-IG)&nbsp;&nbsp;<input type=checkbox value=Y name=sourceOrange $sourceOrange>Orange (-EE)&nbsp;&nbsp;<input type=checkbox value=Y name=sourceGreen $sourceGreen>Green (-ZI)&nbsp;&nbsp;<input type=checkbox value=Y name=sourcePurple $sourcePurple>Purple (-ZX)&nbsp;&nbsp;<input type=checkbox value=Y name=sourceBlue $sourceBlue>Blue (-IB)&nbsp;&nbsp;<input type=checkbox value=Y name=sourceOrigin $sourceOrigin>Origin (-OI)&nbsp;&nbsp;</br><input type=checkbox value=Y name=sourcenetwork $sourceNetwork>Network&nbsp;&nbsp;<input type=checkbox value=Y name=sourcedisplay $sourceDisplay>Display&nbsp;&nbsp;<input type=checkbox value=Y name=sourcelink $sourceLinkOut>Link out&nbsp;&nbsp;<input type=checkbox value=Y name=sourcesearch $sourceSearch>Search&nbsp;&nbsp;<input type=checkbox value=Y name=sourcesocial $sourceSocial>Social&nbsp;&nbsp;<input type=checkbox value=Y name=sourceincent $sourceIncent>Incentivized&nbsp;&nbsp;<br><br></td></tr>
<tr><td><b>Yesmail Suppression Listid:</b></td><td><input type=text name=yesmail_listid value="$yesmail_listid" size=10 maxlength=10></td></tr>
<tr><td><b>Yesmail Suppression Listname:</b></td><td><input type=text name=yesmail_listname value="$yesmail_listname" size=50 maxlength=50></td></tr>
<tr><td><b>Yesmail Division:</b></td><td><select name=yesmail_divisionid>
end_of_html
my @DIV=("","","","","","","Misc","UK");
my $i=0;
while ($i <= $#DIV)
{
	if (($DIV[$i] eq "") and ($i > 0))
	{
		$i++;
		next;
	}
	if ($i == $yesmail_divisionid)
	{
		print "<option selected value=$i>$DIV[$i]</option>\n";
	}
	else
	{
		print "<option value=$i>$DIV[$i]</option>\n";
	}
	$i++;
}
print<<"end_of_html";
</select><br><br></td></tr>
<tr><td><b>Intelligence Source:</b></td><td><input type=text name=intelligence_source value="$intelligence_source" size=50 maxlength=255></td></tr>
<tr><td><b>Reason for Testing:</b></td><td><input type=text name=testing_reason value="$testing_reason" size=50 maxlength=255><br><br></td></tr>
<tr><td> <b>Priority:</b></td>
<td><select name=priority>
end_of_html
my $i=1;
while ($i <= 200)
{
	if ($i == $priority)
	{
		print "<option selected value=$i>$i</option>\n";
	}
	else
	{
		print "<option value=$i>$i</option>\n";
	}
	$i++;
}
print<<"end_of_html";
</select> <i>Note: Doesnt apply to Active, Inactive, or Paused Advertisers</i><br><br></td></tr>
<tr><td> <b>Allow 3rd Party:</b><br></td>
end_of_html
	if ($allow_3rd eq "Y")
	{
		print "<td align=left><input type=radio checked name=allow_3rd value=\"Y\">Yes&nbsp;&nbsp;&nbsp;<input type=radio name=allow_3rd value=\"N\">No&nbsp;&nbsp;&nbsp<br><br></td>\n";
	}
	else
	{
		print "<td align=left><input type=radio name=allow_3rd value=\"Y\">Yes&nbsp;&nbsp;&nbsp;<input type=radio checked name=allow_3rd value=\"N\">No&nbsp;&nbsp;&nbsp<br><br></td>\n";
	}
print<<"end_of_html";
</tr><tr><td> <b>Allow Strongmail:</b><br></td>
end_of_html
	if ($allow_strongmail eq "Y")
	{
		print "<td align=left><input type=radio checked name=allow_strongmail value=\"Y\">Yes&nbsp;&nbsp;&nbsp;<input type=radio name=allow_strongmail value=\"N\">No&nbsp;&nbsp;&nbsp<br><br></td>\n";
	}
	else
	{
		print "<td align=left><input type=radio name=allow_strongmail value=\"Y\">Yes&nbsp;&nbsp;&nbsp;<input type=radio checked name=allow_strongmail value=\"N\">No&nbsp;&nbsp;&nbsp<br><br></td>\n";
	}
print<<"end_of_html";
</tr>
</tr><tr><td> <b>Allow Creative Deletion:</b><br></td>
end_of_html
	if ($allow_creative_deletion eq "Y")
	{
		print "<td align=left><input type=radio checked name=allow_creative_deletion value=\"Y\">Yes&nbsp;&nbsp;&nbsp;<input type=radio name=allow_creative_deletion value=\"N\">No&nbsp;&nbsp;&nbsp<br><br></td>\n";
	}
	else
	{
		print "<td align=left><input type=radio name=allow_creative_deletion value=\"Y\">Yes&nbsp;&nbsp;&nbsp;<input type=radio checked name=allow_creative_deletion value=\"N\">No&nbsp;&nbsp;&nbsp<br><br></td>\n";
	}
if ($auto_url_sid == 0)
{
	$auto_url_sid="";
}
my $cake_offerID="";
if ($cake_creativeID > 0)
{
	$sql="select offerID from CakeCreativeOfferJoin where creativeID=$cake_creativeID";
    $sth=$dbhu->prepare($sql);
    $sth->execute();
    ($cake_offerID)=$sth->fetchrow_array();
    $sth->finish();
}
print<<"end_of_html";
</tr>
<tr><td> <b>SID::</b></td><td>$sid</td></tr>
<tr><td> <b>Cake Creative ID::</b></td><td>$cake_creativeID</td></tr>
<tr><td> <b>Cake Offer ID::</b></td><td>$cake_offerID<br><br></td></tr>
<tr><td><b>Inactive Date(yyyy-mm-dd):</b><br></td><td><input type=text name=idate value="$idate" maxlength=10 size=10></td></tr><br>
<tr><td> <b>Auto Optimize:</b><br></td>
end_of_html
	if ($auto eq "Y")
	{
		print "<td align=left><input type=radio name=autoopt value=\"Y\" checked>Yes&nbsp;&nbsp;&nbsp;<input type=radio name=autoopt value=\"N\">No<br></td></tr>\n";
	}
	else
	{
		print "<td align=left><input type=radio name=autoopt value=\"Y\" >Yes&nbsp;&nbsp;&nbsp;<input type=radio name=autoopt value=\"N\" checked>No<br></td></btr>\n";
	}
print<<"end_of_html";
<tr><td> <b>Pre-pop Supported:</b><br></td>
<td>
end_of_html
my $check1=($prepop eq 'Y') ? 'CHECKED' : '';
my $check2=($prepop eq 'N') ? 'CHECKED' : '';
print qq^	<input type='radio' value='Y' name='prepop' $check1>Y&nbsp;
		<input type='radio' value='N' name='prepop' $check2>N\n^;
print<<"end_of_html";
<br><br>
</td></tr>
<input type=hidden name=advertiser_rating value=$advertiser_rating>
<tr>
  <td valign=top><b>Advertiser Populating Pixel:</b></td>
end_of_html
if ($pass_tracking eq "Y")
{
	print "<td><input type=radio name=\"pass_tracking\" value=Y checked>Yes<input type=radio name=\"pass_tracking\" value=N>No</td>\n";
}
else
{
	print "<td><input type=radio name=\"pass_tracking\" value=Y>Yes<input type=radio name=\"pass_tracking\" value=N checked>No</td>\n";
}
print<<"end_of_html";
</tr>
<tr height=20><td colspan=2></td></tr>
<tr>
  <td valign=top><b>Pixel Type:</b></td>
end_of_html
if ($pixel_type eq "iframe")
{
	print "<td><input type=radio name=\"pixel_type\" value=iframe checked>&lt;iframe&gt; pixel<input type=radio name=\"pixel_type\" value=img>&lt;img&gt; pixel</td>\n";
}
else
{
	print "<td><input type=radio name=\"pixel_type\" value=iframe>&lt;iframe&gt; pixel<input type=radio name=\"pixel_type\" value=img checked>&lt;img&gt; pixel</td>\n";
}
print<<"end_of_html";
</tr>
<tr height=20><td colspan=2></td></tr>
<br>
<br>
<tr><td valign=top> <b>Days to exclude: </b><br></td>
<td>
end_of_html
if (substr($exclude_days,0,1) eq "Y")
{
	print "<input type=checkbox checked value=1 name=ex_monday>Monday</option>\n";
}
else
{
	print "<input type=checkbox value=1 name=ex_monday>Monday</option>\n";
}
if (substr($exclude_days,1,1) eq "Y")
{
	print "<input type=checkbox checked value=2 name=ex_tuesday>Tuesday</option>\n";
}
else
{
	print "<input type=checkbox value=2 name=ex_tuesday>Tuesday</option>\n";
}
if (substr($exclude_days,2,1) eq "Y")
{
	print "<input type=checkbox checked value=3 name=ex_wednesday>Wednesday</option>\n";
}
else
{
	print "<input type=checkbox value=3 name=ex_wednesday>Wednesday</option>\n";
}
if (substr($exclude_days,3,1) eq "Y")
{
	print "<input type=checkbox checked value=4 name=ex_thursday>Thursday</option>\n";
}
else
{
	print "<input type=checkbox value=4 name=ex_thursday>Thursday</option>\n";
}
if (substr($exclude_days,4,1) eq "Y")
{
	print "<input type=checkbox checked value=5 name=ex_friday>Friday</option>\n";
}
else
{
	print "<input type=checkbox value=5 name=ex_friday>Friday</option>\n";
}
if (substr($exclude_days,5,1) eq "Y")
{
	print "<input type=checkbox checked value=6 name=ex_saturday>Saturday</option>\n";
}
else
{
	print "<input type=checkbox value=6 name=ex_saturday>Saturday</option>\n";
}
if (substr($exclude_days,6,1) eq "Y")
{
	print "<input type=checkbox checked value=7 name=ex_sunday>Sunday</option>\n";
}
else
{
	print "<input type=checkbox value=7 name=ex_sunday>Sunday</option>\n";
}
print<<"end_of_html";
</td></tr>
<tr><td><b>Advertiser Passcard:</b></td>
<td><input maxLength="255" size="50" value="$passcard" name="passcard"></td></tr>
<tr><td>&nbsp;</td></tr>
<tr><td colspan=2 valign=top>
end_of_html
if ($track_internally eq "Y")
{
	print "<input type=checkbox name=track_internally value=Y checked><b>Unsubscribe Link Tracked by Us<br></b>\n";
}
else
{
	print "<input type=checkbox name=track_internally value=Y><b>Unsubscribe Link Tracked by Us<br></b>\n";
}
$sql="select third_party_id,mailer_name from third_party_defaults where status='A' and third_party_id != 10 order by mailer_name";
my $sth1a=$dbhu->prepare($sql);
$sth1a->execute();
while (($tid,$mailername)=$sth1a->fetchrow_array())
{
	print "<tr><td valign=top><b><a href=\"Javascript:tracking('$tid');\">$mailername Redirect URL</a>: (in parens = listname)</b><br></td>\n";
	print "<td><select name=advertiser_url_$tid>\n";
	$sql = "select tracking_id,url,code,date_added,date_format(date_added,\"%m/%d/%y\"),company,daily_deal from advertiser_tracking, user where advertiser_id=$puserid and client_id=user.user_id and daily_deal = '$tid' order by mediactivate_id";
	$sth = $dbhq->prepare($sql);
	$sth->execute();
	my ($url,$code,$date_added,$fdate,$company,$tracking_id,$daily_deal);
	while (($tracking_id,$url,$code,$date_added,$fdate,$company,$daily_deal) = $sth->fetchrow_array())
	{
		print "<option value=\"$tracking_id\">$url ($code - $company) $fdate</option>\n";
	}
	$sth->finish();
	print "</select><input type=button value=Delete name=B22 onClick=delete_tracking('$tid');>&nbsp;&nbsp;<input type=button value=\"Gen URLs\" name=\"B22\" onClick=gen_tracking('$tid');></br></br></td></tr>\n";
}
$sth1a->finish();
print qq^
<tr><td valign=top><b>Exclude from Brands with Articles:</b></td>
<td>
^;
for ('Y','N') {
	my $checked=$exclude_from_brands_w_articles eq "$_" ? "CHECKED" : "";
	print qq^<input type=radio name="exclude_from_brands_w_articles" value="$_" $checked> $_ &nbsp;^;
}
print<<"end_of_html";
  </td>
</tr>
end_of_html
print qq^
<tr><td valign=top><b>Override Brand From:</b></td>
<td>
^;
for ('Y','N') {
	my $checked=$override_brand_from eq "$_" ? "CHECKED" : "";
	print qq^<input type=radio name="override_brand_from" value="$_" $checked> $_ &nbsp;^;
}
print<<"end_of_html";
  </td>
</tr>
end_of_html
print qq^
<tr><td valign=top><b>Bank/CC:</b></td>
<td>
^;
for ('Y','N') {
	my $checked=$bank_cc eq "$_" ? "CHECKED" : "";
	print qq^<input type=radio name="bank_cc" value="$_" $checked> $_ &nbsp;^;
}
print<<"end_of_html";
  </td>
</tr>
end_of_html
print qq^
<tr><td valign=top><b>SSN:</b></td>
<td>
^;
for ('Y','N') {
	my $checked=$ssn eq "$_" ? "CHECKED" : "";
	print qq^<input type=radio name="ssn" value="$_" $checked> $_ &nbsp;^;
}
print<<"end_of_html";
  </td>
</tr>
end_of_html
print qq^
<tr><td valign=top><b>Phone:</b></td>
<td>
^;
for ('Y','N') {
	my $checked=$adv_phone eq "$_" ? "CHECKED" : "";
	print qq^<input type=radio name="adv_phone" value="$_" $checked> $_ &nbsp;^;
}
print<<"end_of_html";
  </td>
</tr>
<tr><td valign=top><b>Fields:</b></td>
<td><select name=field_cnt>
end_of_html
my $i=1;
while ($i <= 20)
{
	if ($field_cnt == $i)
	{
		print "<option selected value=$i>$i</option>\n";
	}
	else
	{
		print "<option value=$i>$i</option>\n";
	}
	$i++;
}
print<<"end_of_html";
</select></td></tr>
<tr><td valign=top><b>Pages:</b></td>
<td><select name=page_cnt>
end_of_html
my $i=1;
while ($i <= 10)
{
	if ($page_cnt == $i)
	{
		print "<option selected value=$i>$i</option>\n";
	}
	else
	{
		print "<option value=$i>$i</option>\n";
	}
	$i++;
}
print<<"end_of_html";
</select></td></tr>
                    <TR>
                    <TR>
                    <TD align=middle><IMG height=3 src="$images/spacer.gif" width=3></TD>
					</TR>
					</TBODY>
					</TABLE>
				</TD>
                <TD align=middle><IMG height=3 src="$images/spacer.gif" width=3></TD>
                <TD><IMG height=3 src="$images/spacer.gif" width=3></TD>
				</TR>
                <TR bgColor=#E3FAD1>
                <TD colSpan=5><IMG height=3 src="$images/spacer.gif" width=1 border=0></TD>
				</TR>
                <TR bgColor=#E3FAD1 height=10>
                <TD vAlign=bottom align=left><IMG height=7 src="$images/lt_purp_bl.gif" 
					width=7 border=0></TD>
                <TD><IMG height=3 src="$images/spacer.gif" width=1 border=0></TD>
                <TD align=middle bgColor=#E3FAD1><IMG height=3 src="$images/spacer.gif" 
					width=1 border=0>
					<IMG height=3 src="$images/spacer.gif" width=1 border=0></TD>
                <TD><IMG height=3 src="$images/spacer.gif" width=1 border=0></TD>
                <TD vAlign=bottom align=right><IMG height=7 src="$images/lt_purp_br.gif" 
					width=7 border=0></TD>
				</TR>
				</TBODY>
				</TABLE>
			</TD>
			</TR>
			</TBODY>
			</TABLE>
		</TD>
		</TR>
        <TR>
        <TD>

            <TABLE cellSpacing=0 cellPadding=7 width="100%" border=0>
            <TBODY>
            <TR>
			<td align="center" width="50%">
				<A HREF="mainmenu.cgi" target=_top>
				<IMG src="$images/home_blkline.gif" border=0></A>&nbsp;&nbsp;&nbsp;<a href="advertiser_list.cgi"><img src="$images/advertisers.gif" border=0></a>&nbsp;&nbsp;&nbsp;<a href="advertiser_list.cgi"><img src="$images/cancel_x.gif" border=0></a>&nbsp;&nbsp;<input type="image" name="BtnAdd" src="$images/save.gif" border=0></TD>	
			<td align="center" width="50%">
			</td>
			</tr>
			</table>

		</TD>
		</TR>
		</TBODY>
		</TABLE>

		</FORM>

	</TD>
	</TR>
	</TBODY>
	</TABLE>

</TD>
</TR>
<TR>
<TD noWrap align=left height=17>
end_of_html
}

$util->footer();
$util->clean_up();
exit(0);
