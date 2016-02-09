#!/usr/bin/perl
#===============================================================================
# Purpose: Update advertiser info - (eg table 'user' data).
# Name   : advertiser_upd2.cgi (update_advertiser_info.cgi)
#
#--Change Control---------------------------------------------------------------
# 01/04/05  Jim Sobeck  Creation
#===============================================================================

# include Perl Modules
use strict;
use CGI;
#use Finance::Currency::Convert::XE;
use HTML::LinkExtor;
use App::MailReader::CompetitiveAnalysis::Save;
use App::WebAutomation::ImageHoster;
use util;

# get some objects to use later
my $util = util->new;
my $query = CGI->new;
my ($sth, $sql, $dbh, $errmsg ) ;
my ($fname,$lname,$address,$address2,$city,$state,$zip,$phone,$email_addr);
my $idate;
my ($user_type, $max_names, $max_mailings, $status, $pmode, $puserid);
my ($password, $username, $old_username);
my ($pmesg, $old_email_addr) ;
my $global_text;
my $company;
my $old_cstatus;
my $allow_3rd;
my $allow_strongmail;
my $allow_creative_deletion;
my $tstatus;
my $cname;
	my $sth1;
	my $aname;
	my $vid;
my $website_url;
my $company_phone;
my $images = $util->get_images_url;
my $admin_user;
my $account_type;
my $privacy_policy_url;
my $unsub_option;
my $name;
my $friendly_name;
my $internal_email_addr;
my $physical_addr;
my $offer_type ;
my $pixel_placed ;
my $pass_tracking;
my $pixel_type;
my $sourceinternal;
my $sourcenetwork;
my $sourcedisplay;
my $sourcelink;
my $sourcesearch;
my $sourcesocial;
my $sourceincent;
my $sourceGold;
my $sourceOrange;
my $sourceGreen;
my $sourcePurple;
my $sourceBlue;
my $sourceOrigin;
my $old_pixel_placed ;
my $pixel_verified;
my $direct_track;
my $oldpixelverified;
my $pixel_requested;
my $tracking_pixel;
my $filedate;
my $prepop;
my $advertiser_rating;
my $payout ;
my $scrub_percent;
my $margin_percent;
my $allocationCap;
my $allocationCapCnt;
my $payout_pounds;
my $payout_euro;
my $payout_aud;
my $payout_br;
my $payout_no;
my $payout_sk;
my $payout_dkk;
my $direct_suppression_url;
my $passcard;
my $md5_suppression;
my $advertiser_url;
my $landing_page;
my $old_advertiser_url;
my $ecpm;
my $suppid1 ;
my $md5suppid;
my $supp_file ;
my $supp_url ;
my $auto_download ;
my $supp_username ;
my $supp_password ;
my $track_internally ;
my $unsub_link ;
my $unsub_image ;
my $unsub_text;
my $unsub_use;
my $replace_flag;
my $category_id ;
my $backto;
my $upload_dir;
my $upload_dir1;
my ($third_pixel,$third_tracking_pixel1,$third_tracking_pixel2);
my $hitpath_tracking_pixel;
my $defaultCurrency;
my $hitpath_id;

my $immediate_upload=$query->param('immediate_upload');
my $countryID=$query->param('countryID');
$upload_dir="/var/www/util/creative";
if ($immediate_upload eq "Y")
{
	$upload_dir1="/var/www/html/supplist";
}
else
{
	$upload_dir1="/var/www/html/new_supplist";
}
my $data={};
$data->{'imageCollectionID'}="000000000000001";
$ENV{'IMAGE_HOSTER_SSH_KEY'}="/var/www/.ssh/images.sav";
my $imageHoster = App::WebAutomation::ImageHoster->new($data);

$pmesg="";
srand();
my $rid=rand();
my $cstatus;
my $exclude_from_brands_w_articles;
my $override_brand_from;
my $bank_cc;
my $adv_phone;
my $ssn;
my $field_cnt;
my $page_cnt;
my $auto;

#----------- check for login ------------------
my $user_id = util::check_security();
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}


# for testing
##my $user_id = 129; # type N
#my $user_id = 8; # type A


    #---------------------------------------------------
    # Get the information about the user from the form
    #---------------------------------------------------
    $name = $query->param('name');
    $friendly_name = $query->param('friendly_name');
    $idate = $query->param('idate');
    $email_addr = $query->param('email_addr');
    $physical_addr = $query->param('address');
    $internal_email_addr = $query->param('internal_email_addr');
    $cstatus = $query->param('cstatus');
	$exclude_from_brands_w_articles = $query->param('exclude_from_brands_w_articles');
	$override_brand_from = $query->param('override_brand_from');
	$bank_cc= $query->param('bank_cc');
	$ssn= $query->param('ssn');
	$adv_phone= $query->param('adv_phone');
	$field_cnt= $query->param('field_cnt');
	$page_cnt= $query->param('page_cnt');
    my $priority = $query->param('priority');
    my $auto_url_sid = $query->param('auto_url_sid');
    my $auto_cake_creativeid = $query->param('auto_cake_creativeid');
    my $linkType= $query->param('linkType');
	if ($linkType eq "")
	{
		$linkType="XLM";
	}
	$auto_cake_creativeid=~s/ //g;
    $allow_3rd= $query->param('allow_3rd');
	if ($allow_3rd eq "")
	{
		$allow_3rd="Y";
	}
    $allow_strongmail= $query->param('allow_strongmail');
	if ($allow_strongmail eq "")
	{
		$allow_strongmail="Y";
	}
    $allow_creative_deletion= $query->param('allow_creative_deletion');
	if ($allow_creative_deletion eq "")
	{
		$allow_creative_deletion ="Y";
	}
    $auto = $query->param('autoopt');
    $puserid = $query->param('puserid');
	$offer_type = $query->param('deal_type');
	$pixel_placed = $query->param('pixel_placed');
	$pass_tracking = $query->param('pass_tracking');
	$pixel_type= $query->param('pixel_type');
	$sourceinternal= $query->param('sourceinternal');
	if ($sourceinternal eq "")
	{
		$sourceinternal="N";
	}
	$sourcenetwork= $query->param('sourcenetwork');
	if ($sourcenetwork eq "")
	{
		$sourcenetwork="N";
	}
	$sourcedisplay= $query->param('sourcedisplay');
	if ($sourcedisplay eq "")
	{
		$sourcedisplay="N";
	}
	$sourcelink= $query->param('sourcelink');
	if ($sourcelink eq "")
	{
		$sourcelink="N";
	}
	$sourcesearch= $query->param('sourcesearch');
	if ($sourcesearch eq "")
	{
		$sourcesearch="N";
	}
	$sourcesocial = $query->param('sourcesocial');
	if ($sourcesocial eq "")
	{
		$sourcesocial ="N";
	}
	$sourceincent = $query->param('sourceincent');
	if ($sourceincent eq "")
	{
		$sourceincent="N";
	}
	$sourceGold = $query->param('sourceGold');
	if ($sourceGold eq "")
	{
		$sourceGold="N";
	}
	$sourceOrange= $query->param('sourceOrange');
	if ($sourceOrange eq "")
	{
		$sourceOrange="N";
	}
	$sourceGreen= $query->param('sourceGreen');
	if ($sourceGreen eq "")
	{
		$sourceGreen="N";
	}
	$sourcePurple= $query->param('sourcePurple');
	if ($sourcePurple eq "")
	{
		$sourcePurple="N";
	}
	$sourceBlue= $query->param('sourceBlue');
	if ($sourceBlue eq "")
	{
		$sourceBlue="N";
	}
	$sourceOrigin= $query->param('sourceOrigin');
	if ($sourceOrigin eq "")
	{
		$sourceOrigin="N";
	}
	$oldpixelverified = $query->param('oldpixelverified');
	$pixel_verified = $query->param('pixel_verified');
	$direct_track= $query->param('direct_track');
	$pixel_requested = $query->param('pixel_requested');
	$prepop= $query->param('prepop');
	$advertiser_rating = $query->param('advertiser_rating');
	$tracking_pixel = $query->param('tracking_pixel');
	$tracking_pixel =~ s/"/\"/g;
	$filedate = $query->param('filedate');
	if ($filedate eq "0000-00-00")
	{
		$filedate="";
	}
	$payout = $query->param('payout');
	$scrub_percent = $query->param('scrub_percent');
	if ($scrub_percent eq "")
	{
		$scrub_percent=0;
	}
	$margin_percent = $query->param('margin_percent');
	if ($margin_percent eq "")
	{
		$margin_percent=0;
	}
	$allocationCap= $query->param('allocationCap');
	$allocationCapCnt = $query->param('allocationCapCnt');
	if ($allocationCapCnt eq "")
	{
		$allocationCapCnt=0;
	}
	$direct_suppression_url = $query->param('direct_suppression_url');
	$direct_suppression_url=~s/'/''/g;
	$passcard = $query->param('passcard');
	$passcard=~s/'/''/g;
	$payout_pounds = $query->param('payout_pounds');
	if ($payout_pounds eq "")
	{
		$payout_pounds=0;
	}
	$payout_euro= $query->param('payout_euro');
	if ($payout_euro eq "")
	{
		$payout_euro=0;
	}
	$payout_aud= $query->param('payout_aud');
	if ($payout_aud eq "")
	{
		$payout_aud=0;
	}
	$payout_br = $query->param('payout_br');
	if ($payout_br eq "")
	{
		$payout_br=0;
	}
	$payout_no = $query->param('payout_no');
	if ($payout_no eq "")
	{
		$payout_no=0;
	}
	$payout_sk = $query->param('payout_sk');
	if ($payout_sk eq "")
	{
		$payout_sk=0;
	}
	$payout_dkk = $query->param('payout_dkk');
	if ($payout_dkk eq "")
	{
		$payout_dkk=0;
	}
	$md5_suppression = $query->param('md5_suppression');
	if ($md5_suppression eq "")
	{
		$md5_suppression="N";
	}
#	if (($payout_pounds ne "") and ($payout_pounds > 0))
#	{
#  		my $obj = Finance::Currency::Convert::XE->new();
#  		$payout= $obj->convert(
#                    'source' => 'GBP',
#                    'target' => 'USD',
#                    'value' => $payout_pounds,
#                    'format' => 'number'
#            );
#	}
#	if (($payout_euro ne "") and ($payout_euro > 0))
#	{
#  		my $obj = Finance::Currency::Convert::XE->new();
#  		$payout= $obj->convert(
#                    'source' => 'EUR',
#                    'target' => 'USD',
#                    'value' => $payout_euro,
#                    'format' => 'number'
#            );
#	}
	$advertiser_url = $query->param('orig_advertiser_url');
	$landing_page= $query->param('landing_page');
    my $upload_filehandle = $query->upload("supp_file");
	if ($payout eq "")
	{
		$payout = 0;
	}
	$ecpm = $query->param('ecpm');
	$suppid1 = $query->param('suppid1');
	$md5suppid = $query->param('md5suppid');
	if ($md5_suppression eq "Y")
	{
		$suppid1=$md5suppid;
	}
	$supp_file = $query->param('supp_file');
	$supp_url = $query->param('supp_url');
	$auto_download = $query->param('auto_download');
	$supp_username = $query->param('supp_username');
	$supp_password = $query->param('supp_password');
	$track_internally = $query->param('track_internally');
	$unsub_link = $query->param('unsub_link');
	$unsub_image = $query->param('unsub_image');
	$unsub_text= $query->param('unsub_text');
	$unsub_use= $query->param('unsub_use');
	$replace_flag = $query->param('replace_flag');
	if ($replace_flag eq "")
	{
		$replace_flag="Y";
	}
	my $old_unsub = $query->param('old_unsub');
    $unsub_image =~ s/.*[\/\\](.*)/$1/;
    $supp_file =~ s/.*[\/\\](.*)/$1/;
	$category_id = $query->param('category_id');
	$backto = $query->param('backto');
	$third_pixel=$query->param('third_pixel');
	$third_tracking_pixel1=$query->param('third_tracking_pixel1');
	$third_tracking_pixel2=$query->param('third_tracking_pixel2');
	$hitpath_tracking_pixel=$query->param('hitpath_tracking_pixel');
	$defaultCurrency=$query->param('defaultCurrency');
#
#	Get the exclude days
#
	my $ex_monday = $query->param('ex_monday');
	my $ex_tuesday = $query->param('ex_tuesday');
	my $ex_wednesday = $query->param('ex_wednesday');
	my $ex_thursday = $query->param('ex_thursday');
	my $ex_friday = $query->param('ex_friday');
	my $ex_saturday = $query->param('ex_saturday');
	my $ex_sunday = $query->param('ex_sunday');
	my $ex_str;
	$ex_str="NNNNNNN";
	if ($ex_monday ne "")
	{
		substr($ex_str,0,1) = 'Y';
	}
	if ($ex_tuesday ne "")
	{
		substr($ex_str,1,1) = 'Y';
	}
	if ($ex_wednesday ne "")
	{
		substr($ex_str,2,1) = 'Y';
	}
	if ($ex_thursday ne "")
	{
		substr($ex_str,3,1) = 'Y';
	}
	if ($ex_friday ne "")
	{
		substr($ex_str,4,1) = 'Y';
	}
	if ($ex_saturday ne "")
	{
		substr($ex_str,5,1) = 'Y';
	}
	if ($ex_sunday ne "")
	{
		substr($ex_str,6,1) = 'Y';
	}
	my @abbr= $query->param('abbr');
	my $date_requested=$query->param('date_requested');
	my $manager_id=$query->param('manager_id');
	my $intelligence_source=$query->param('intelligence_source');
	my $yesmail_listid=$query->param('yesmail_listid');
	my $yesmail_listname=$query->param('yesmail_listname');
	my $yesmail_divisionid=$query->param('yesmail_divisionid');
	if ($yesmail_listid eq "")
	{
		$yesmail_listid=0;
	}
	$intelligence_source=~s/'/''/g;
	my $testing_reason=$query->param('testing_reason');
	$testing_reason=~s/'/''/g;
#

#------ connect to the util database ------------------
my ($dbhq,$dbhu)=$util->get_dbh();

#
#	Save suppression file - if one specified
#
if ($supp_file ne "")
{
	#
	# Check to see if suppression list already exists
	#
	$sql = "select advertiser_name from advertiser_info where advertiser_id=$puserid";
	$sth1 = $dbhq->prepare($sql);
	$sth1->execute();
	($aname) = $sth1->fetchrow_array();
	$sth1->finish();
	$sql = "select list_id from vendor_supp_list_info where list_name='$aname' and md5_suppression='$md5_suppression'"; 
	$sth1 = $dbhq->prepare($sql);
	$sth1->execute();
	if (($vid) = $sth1->fetchrow_array())
	{
		$sth1->finish;
	    #
	    # if FIledate specified then save off
	    #
	    if ($filedate ne "")
	    {
	        $sql = "update vendor_supp_list_info set temp_filedate='$filedate' where list_id=$vid";
	    }
	    else
	    {
	        $sql = "update vendor_supp_list_info set filedate=null,temp_filedate=null where list_id=$vid";
	    }
	    my $rows=$dbhu->do($sql);
	}
	else
	{
		$sth1->finish;
		#
		# Add new suppresion list
		#
		$sql = "select advertiser_name from advertiser_info where advertiser_id=$puserid";
		$sth1 = $dbhq->prepare($sql);
		$sth1->execute();
		($aname) = $sth1->fetchrow_array();
		$sth1->finish();
		$sql = "insert into vendor_supp_list_info(list_name,list_cnt,md5_suppression) values('$aname',0,'$md5_suppression')";
		my $rows = $dbhu->do($sql);
		#
		# Get id of just added list
		#
		$sql = "select list_id from vendor_supp_list_info where list_name='$aname'";
		$sth1 = $dbhq->prepare($sql);
		$sth1->execute();
		($vid) = $sth1->fetchrow_array();
		$sth1->finish();
		#
		# if FIledate specified then save off
		#
		if ($filedate ne "")
		{
			$sql = "update vendor_supp_list_info set temp_filedate='$filedate' where list_id=$vid";
		}
		else
		{
			$sql = "update vendor_supp_list_info set filedate=null,temp_filedate=null where list_id=$vid";
		}
		my $rows=$dbhu->do($sql);
		$suppid1 = $vid;
	}
#
	$aname =~ s/ /_/g;
    my $temp_str = $vid . "_" . $aname . ".txt";
    open UPLOADFILE, ">$upload_dir1/$temp_str";
    while ( <$upload_filehandle> )
    {
        print UPLOADFILE;
    }
    close UPLOADFILE;
}
elsif ($filedate ne "")
{
	#
	# Check to see if suppression list already exists
	#
$sql = "select list_id,advertiser_name from advertiser_info,vendor_supp_list_info where advertiser_id=$puserid and advertiser_info.advertiser_name=list_name"; 
$sth1 = $dbhq->prepare($sql);
$sth1->execute();
($vid,$aname) = $sth1->fetchrow_array();
$sth1->finish;
	$sql = "update vendor_supp_list_info set filedate='$filedate',last_updated='$filedate' where list_id=$vid";
	my $rows=$dbhu->do($sql);
}
if ($unsub_image ne "")
{
    my $upload_filehandle = $query->upload("unsub_image");
	my $params={};
	$params->{'image'}=$upload_filehandle;
	my ($imageHostingErrors, $newImageName, $imageExtension, $allImageProperties) = $imageHoster->setUpImageHosting($params);
	$unsub_image=$newImageName;
}
else
{
	$unsub_image = $old_unsub;
}
if ($replace_flag eq "Y") 
{
	$unsub_text =~ s/\&sub=/\&XXX=/g;
    $global_text = $unsub_text;
    my $p = HTML::LinkExtor->new(\&cb1);
    $p->parse($unsub_text);
    $unsub_text= $global_text;
}
$unsub_text=~s/'/''/g;
$unsub_text=~s/\[\[reg\]\]/&reg;/g;
$unsub_text=~s/\[\[tm\]\]/&tm;/g;
$unsub_text=~s/\[\[dagger\]\]/&dagger;/g;
$unsub_text=~s/\[\[trade\]\]/&trade;/g;
	&update_advertiser();
	if (($pixel_verified eq "Y") && ($oldpixelverified ne $pixel_verified))
	{ 
		$sql = "select advertiser_name from advertiser_info where advertiser_id=$puserid";
		$sth1 = $dbhq->prepare($sql);
		$sth1->execute();
		($aname) = $sth1->fetchrow_array();
		$sth1->finish();
   		open (MAIL,"| /usr/sbin/sendmail -t");
        my $from_addr = "Pixel Verified <info\@zetainteractive.com>";
        print MAIL "From: $from_addr\n";
        print MAIL "To: setup\@zetainteractive.com\n";
        print MAIL "Subject: Pixel Verified for $aname\n";
        my $date_str = $util->date(6,6);
        print MAIL "Date: $date_str\n";
        print MAIL "X-Priority: 1\n";
        print MAIL "X-MSMail-Priority: High\n";
        print MAIL "$aname pixel verified set to Y\n";
        close MAIL;
	}

# go to next screen

$util->clean_up();
print "Cache-Control: no-cache\n";
print "Pragma: no-cache\n";
print "Expires: 0\n";
$_ = $backto;
if (/preview.cgi/)
{
	print "Content-type: text/html\n\n";
    print qq {
	<html></head>
    <script language="Javascript">
    var newwin = window.open("$backto", "Preview", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,width=900,height=500,left=25,top=50");
    newwin.focus();
    </script> \n };
    $pmesg="";
print<<"end_of_html";
</head>
<body>
<script language="JavaScript">
document.location="/cgi-bin/advertiser_disp2.cgi?pmode=$pmode&puserid=$puserid&pmesg=$pmesg&rid=$rid";
</script>
</body></html>
end_of_html
}
#elsif (/validate.cgi/)
#{
#    print qq {
#    <script language="Javascript">
#    var newwin = window.open("$backto", "Validate", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,width=900,height=500,left=25,top=50");
#    newwin.focus();
#    </script> \n };
#    $pmesg="";
#print<<"end_of_html";
#</head>
#<body>
#<script language="JavaScript">
#document.location="/cgi-bin/advertiser_disp2.cgi?pmode=$pmode&puserid=$puserid&pmesg=$pmesg&rid=$rid";
#</script>
#</body></html>
#end_of_html
#}
elsif (/approval.cgi/)
{
	print "Content-type: text/html\n\n";
    print qq {
	<html></head>
    <script language="Javascript">
    var newwin = window.open("$backto", "Approval", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,width=900,height=500,left=25,top=50");
    newwin.focus();
    </script> \n };
    $pmesg="";
print<<"end_of_html";
</head>
<body>
<script language="JavaScript">
document.location="/cgi-bin/advertiser_disp2.cgi?pmode=$pmode&puserid=$puserid&pmesg=$pmesg&rid=$rid";
</script>
</body></html>
end_of_html
}
elsif ($backto eq "")
{
	print "Location: /cgi-bin/advertiser_disp2.cgi?pmode=$pmode&puserid=$puserid&pmesg=$pmesg&rid=$rid\n\n";
}
else
{
	print "Location: $backto\n\n";
}
exit(0);


#===============================================================================
# Sub: update_advertiser
#===============================================================================
sub update_advertiser
{
	my $rows;
	my $cid;
	my $test_flag;
	my $old_test_flag;
	my $company_id;
	my $old_auto_url_sid;
	my $old_auto_cake_creativeid;
	my $old_linkType;
	my $landing_domain;
	my $tempurl;
	my $link;
	my $old_priority;

	$sql="select status,advertiser_url,test_flag,company_id,auto_url_sid,pixel_placed,landing_domain,priority,auto_cake_creativeID,linkType from advertiser_info where advertiser_id=$puserid";
	$sth1 = $dbhq->prepare($sql);
	$sth1->execute();
	($old_cstatus,$old_advertiser_url,$old_test_flag,$company_id,$old_auto_url_sid,$old_pixel_placed,$landing_domain,$old_priority,$old_auto_cake_creativeid,$old_linkType) = $sth1->fetchrow_array();
	$sth1->finish();
	$landing_domain=$landing_page;

	# log all changes
	$sql="insert into advertiser_infoChangeLog select curdate(),$user_id,curtime(),ai.* from advertiser_info ai where advertiser_id=$puserid";
	my $rows=$dbhu->do($sql);

	if ($landing_domain eq "") 
	{
		$advertiser_url=~s///g;
		$tempurl=$advertiser_url;
		$ENV{'PROXY_SERVER_KEY'} = '/var/www/.ssh/proxy1_dsa.prv';
		my $saveObject = App::MailReader::CompetitiveAnalysis::Save->new();
		my ($errors, $links) = $saveObject->getRedirectSequence($advertiser_url);
		foreach $link (@$links)
		{
			$link=~s///g;
			$_=$link;
			if (/http/)
			{
  				$tempurl=$link;
			}
		}
		if ($tempurl ne "")
		{
			my $t2;
			($landing_domain,$t2)=Lib::Database::Perl::Interface::CompetitiveAnalysis->splitURL($tempurl);
		}
	}
	$test_flag="N";
	my $temp_name=$name;
	$temp_name=~s/'/''/g;
	$friendly_name=~s/'/''/g;
	if ($cstatus eq "T")
	{
		$cstatus="A";
		$test_flag="Y";
	}
	elsif ($cstatus eq "P")
	{
		$cstatus="I";
		$test_flag="P";
	}
	elsif ($cstatus eq "U")
	{
		$cstatus="A";
		$test_flag="U";
	}
	
	if ($priority != $old_priority)
	{
		if (($cstatus eq "I") or ($cstatus eq "A" and $test_flag eq "N") or ($cstatus eq "A" and $test_flag eq "P"))
		{
			$priority=1;
		}
		else
		{
			$sql="update advertiser_info set priority=priority+1 where priority >= $priority and ((status not in ('A','I')) or (status='A' and test_flag in ('Y','U'))) and manager_id in (select m1.manager_id from CampaignManager m1,CampaignManager m2 where m1.MemberGroup=m2.MemberGroup and m2.manager_id=$manager_id)";
			$rows=$dbhu->do($sql);
		}
	}
	my $tnotes=$physical_addr;
	$tnotes=~s/'/''/g;
	$advertiser_url=~s/'/''/g;
	$sql = "update advertiser_info set pre_pop='$prepop', advertiser_rating='$advertiser_rating', advertiser_name='$temp_name', physical_addr='$tnotes',email_addr='$email_addr',internal_email_addr='$internal_email_addr',status='$cstatus',offer_type='$offer_type',tracking_pixel='$tracking_pixel',pixel_placed='$pixel_placed',pixel_verified='$pixel_verified',pixel_requested='$pixel_requested',payout=$payout,ecpm=$ecpm,vendor_supp_list_id=$suppid1,suppression_file='$supp_file',suppression_url='$supp_url',auto_download='$auto_download',suppression_username='$supp_username',suppression_password='$supp_password',track_internally='$track_internally',unsub_link='$unsub_link',unsub_image='$unsub_image',category_id=$category_id,exclude_days='$ex_str',advertiser_url='$advertiser_url',inactive_date='$idate', third_party_pixel='$third_pixel', third_tracking_pixel1='$third_tracking_pixel1', third_tracking_pixel2='$third_tracking_pixel2',auto_optimize='$auto',direct_track='$direct_track',hitpath_tracking_pixel='$hitpath_tracking_pixel',allow_3rdparty='$allow_3rd',allow_strongmail='$allow_strongmail',friendly_advertiser_name='$friendly_name',test_flag='$test_flag',exclude_from_brands_w_articles='$exclude_from_brands_w_articles',payout_pounds=$payout_pounds,md5_suppression='$md5_suppression',allow_creative_deletion='$allow_creative_deletion',unsub_text='$unsub_text',unsub_use='$unsub_use',replace_flag='$replace_flag',priority=$priority,override_brand_from='$override_brand_from',auto_url_sid='$auto_url_sid',date_requested='$date_requested',manager_id=$manager_id,intelligence_source='$intelligence_source',testing_reason='$testing_reason',direct_suppression_url='$direct_suppression_url',passcard='$passcard',bank_cc='$bank_cc',ssn='$ssn',phone='$adv_phone',field_cnt=$field_cnt,page_cnt=$page_cnt,landing_domain='$landing_domain',pixel_type='$pixel_type',pass_tracking='$pass_tracking',sourceInternal='$sourceinternal',sourceNetwork='$sourcenetwork',sourceDisplay='$sourcedisplay',sourceLinkOut='$sourcelink',payout_euro=$payout_euro,yesmail_listid=$yesmail_listid,yesmail_listname='$yesmail_listname',yesmail_divisionID=$yesmail_divisionid,auto_cake_creativeID='$auto_cake_creativeid',countryID=$countryID,sourceSearch='$sourcesearch',sourceSocial='$sourcesocial',sourceIncent='$sourceincent',scrub_percent=$scrub_percent,margin_percent=$margin_percent,allocationCap='$allocationCap',allocationCapCnt=$allocationCapCnt,payout_aud=$payout_aud,payout_br=$payout_br,linkType='$linkType',payout_no=$payout_no,sourceGold='$sourceGold',sourceOrange='$sourceOrange',sourceGreen='$sourceGreen',sourcePurple='$sourcePurple',sourceBlue='$sourceBlue',sourceOrigin='$sourceOrigin',payout_sk=$payout_sk,payout_dkk=$payout_dkk,defaultCurrency='$defaultCurrency' where advertiser_id = $puserid";
	$rows = $dbhu->do($sql);
	if (($md5_suppression eq "Y") and ($md5suppid > 0))
	{
		my $tdate;
		$sql="select md5_last_updated from advertiser_info where advertiser_id=$md5suppid";
		my $sthq=$dbhu->prepare($sql);
		$sthq->execute();
		($tdate)=$sthq->fetchrow_array();
		$sthq->finish();
		$sql="update advertiser_info set md5_last_updated='$tdate' where advertiser_id=$puserid";
		$rows = $dbhu->do($sql);
	}

#
	if (($old_cstatus ne "C") and ($cstatus eq "C"))
	{
		$sql="update advertiser_info set pending_date=curdate() where advertiser_id=$puserid";
		$rows = $dbhu->do($sql);
	}
	my $acnt;
	$sql="select count(*) from IO where advertiser_id=? and cancelIO='N'";
	my $sthq=$dbhu->prepare($sql);
	$sthq->execute($puserid);
	($acnt)=$sthq->fetchrow_array();
	$sthq->finish();
	if ($acnt == 0)
	{
		$sql="insert into IO(advertiser_id,IOAdvertiserID) select $puserid,IOAdvertiserID from IOAdvertiser where company_id=$company_id";
		$rows = $dbhu->do($sql);
		$sql="insert into IOAdvertiserInfo(advertiser_id,IOAdvertiserID,AdvertiserName,AdvertiserContact,Address,Address2,CityStateZip,Phone,Fax,Email,ReportURL,LoginUsername,LoginPassword,AccountingContact,AccountingPhone,AccountingFax,AccountingEmail) select $puserid,ioa.IOAdvertiserID,ioai.AdvertiserName,AdvertiserContact,Address,Address2,CityStateZip,Phone,Fax,Email,ReportURL,LoginUsername,LoginPassword,AccountingContact,AccountingPhone,AccountingFax,AccountingEmail from IOAdvertiser ioa, IOAdvertiserInfo ioai where company_id=$company_id and ioa.IOAdvertiserID=ioai.IOAdvertiserID and ioai.advertiser_id=0";
		$rows = $dbhu->do($sql);
	}
	if (($old_cstatus ne "A") and ($cstatus eq "A") and ($test_flag eq "N"))
	{
		my $acnt;
		$sql="update advertiser_info set active_date=curdate() where advertiser_id=$puserid";
		$rows = $dbhu->do($sql);
		#$sql="select count(*) from UniqueScheduleAdvertiser where advertiser_id=?";
		#my $stha=$dbhu->prepare($sql);
		#$stha->execute($puserid);
		#($acnt)=$stha->fetchrow_array();
		#$stha->finish();
		#if ($acnt == 0)
		#{
		#	my $usaname=$temp_name."-AUTO";
		#	$sql="insert into UniqueScheduleAdvertiser(advertiser_id,name) values($puserid,'$usaname')";
		#	$rows = $dbhu->do($sql);
		#	my $usaid;
		#	$sql="select LAST_INSERT_ID()";
		#	my $stha=$dbhu->prepare($sql);
		#	$stha->execute();
		#	($usaid)=$stha->fetchrow_array();
		#	$stha->finish();
		#	$sql="insert ignore into UniqueAdvertiserCreative(usa_id,creative_id) select $usaid,creative_id from creative where advertiser_id=$puserid and status='A'";
		#	$rows = $dbhu->do($sql);
		#	$sql="insert ignore into UniqueAdvertiserSubject(usa_id,subject_id) select $usaid,subject_id from advertiser_subject where advertiser_id=$puserid and status='A'";
		#	$rows = $dbhu->do($sql);
		#	$sql="insert ignore into UniqueAdvertiserFrom(usa_id,from_id) select $usaid,from_id from advertiser_from where advertiser_id=$puserid and status='A'";
		#	$rows = $dbhu->do($sql);
		#	#
		#	# set creative_id,subject_id,from_id in UniqueScheduleAdvertiser
		#	#
		#	my $c1;
		#	my $s1;
		#	my $f1;
		#	$sql="select creative_id from UniqueAdvertiserCreative where usa_id=? limit 1";
		#	my $sthb=$dbhu->prepare($sql);
		#	$sthb->execute($usaid);
		#	($c1)=$sthb->fetchrow_array();
		#	$sthb->finish();
		#	$sql="select subject_id from UniqueAdvertiserSubject where usa_id=? limit 1";
		#	$sthb=$dbhu->prepare($sql);
		#	$sthb->execute($usaid);
		#	($s1)=$sthb->fetchrow_array();
		#	$sthb->finish();
		##	$sql="select from_id from UniqueAdvertiserFrom where usa_id=? limit 1";
		#	$sthb=$dbhu->prepare($sql);
		#	$sthb->execute($usaid);
		#	($f1)=$sthb->fetchrow_array();
		#	$sthb->finish();
		#	if ($c1 eq "")
		#	{
		#		$c1=0;
		#	}
		#	if ($s1 eq "")
		#	{
		#		$s1=0;
		#	}
		#	if ($f1 eq "")
		#	{
		#		$f1=0;
		#	}
		#	$sql="update UniqueScheduleAdvertiser set creative_id=$c1,subject_id=$s1,from_id=$f1 where usa_id=$usaid";
		#	$rows=$dbhu->do($sql);
		#}
	}
	if (($old_cstatus ne "A") and ($cstatus eq "A") and ($test_flag eq "Y") and ($old_test_flag ne "Y"))
	{
		$sql="update advertiser_info set testing_date=curdate() where advertiser_id=$puserid";
		$rows = $dbhu->do($sql);
	}
	if (($old_cstatus ne "I") and ($cstatus eq "I")) 
	{
		$sql="update advertiser_info set inactive_date_set=curdate() where advertiser_id=$puserid";
		$rows = $dbhu->do($sql);
		recalc_priority();
	}
	if (($old_test_flag ne "N") and ($test_flag eq "N") and ($cstatus eq "A"))
	{
		recalc_priority();
	}
	elsif (($old_test_flag ne "P") and ($test_flag eq "P") and ($cstatus eq "A"))
	{
		recalc_priority();
	}
	if ($old_cstatus ne $cstatus)
	{
		$sql="update advertiser_info set chg_user_id=$user_id where advertiser_id=$puserid";
		$rows = $dbhu->do($sql);
	}
	
	if ($old_advertiser_url ne $advertiser_url)
	{
		$sql="update advertiser_info set advertiser_url_updated=curdate() where advertiser_id=$puserid";
		$rows = $dbhu->do($sql);
	}
	if (($cstatus eq "I") && ($old_cstatus ne "I"))
	{
        open (MAIL,"| /usr/sbin/sendmail -t");
        my $from_addr = "Advertiser Set Inactive<info\@zetainteractive.com>";
        print MAIL "From: $from_addr\n";
        print MAIL "To: alerts\@zetainteractive.com\n";
		print MAIL "Cc: nharris\@zetainteractive.com,amohd\@zetainteractive.com,jchittem\@zetainteractive.com,nvengal\@zetainteractive.com,vganugapati\@zetainteractive.com,vdhammana\@zetainteractive.com,mvaggu\@zetainteractive.com\n";
        print MAIL "Subject: Advertiser $name set inactive\n";
        my $date_str = $util->date(6,6);
        print MAIL "Date: $date_str\n";
        print MAIL "X-Priority: 1\n";
        print MAIL "X-MSMail-Priority: High\n";
        print MAIL "\nAdvertiser $name($puserid) has been set inactive thru the tool.\n";

		$sql="select campaign_name,status from campaign where deleted_date is null and advertiser_id=$puserid and status in ('W','T')";
		my $sth1a = $dbhq->prepare($sql);
		$sth1a->execute();
		while (($cname,$tstatus) = $sth1a->fetchrow_array())
		{
			if ($tstatus eq "D")
			{
				print MAIL "Daily Deal - $cname\n";
			}
			elsif ($tstatus eq "T")
			{
				print MAIL "Trigger Deal - $cname\n";
			}
		}
		$sth1a->finish();
        close(MAIL);
	}
	if (($cstatus eq "B") && ($old_cstatus ne "B"))
	{
        my $from_addr = "Advertiser Setup Problem <info\@zetainteractive.com>";
       	open (MAIL,"| /usr/sbin/sendmail -t");
        print MAIL "From: $from_addr\n";
        print MAIL "To: setup\@zetainteractive.com\n";
        print MAIL "Subject: Problem with setup of $puserid $name\n";
        my $date_str = $util->date(6,6);
        print MAIL "Date: $date_str\n";
		print MAIL "There are elements needed before setup can be completed for $puserid $name\n\n"; 
		print MAIL "Notes: $physical_addr\n\n";

		print MAIL "Mailing Tool: http://mailingtool.routename.com:83/cgi-bin/advertiser_disp2.cgi?pmode=U&puserid=$puserid\n";
		close(MAIL);
	}
	if (($cstatus eq "C") && ($old_cstatus ne "C"))
	{
        my $from_addr = "Advertiser Setup Complete <info\@zetainteractive.com>";
       	open (MAIL,"| /usr/sbin/sendmail -t");
        print MAIL "From: $from_addr\n";
        print MAIL "To: setup\@zetainteractive.com\n";
        print MAIL "Subject: Setup complete for $puserid $name\n";
        my $date_str = $util->date(6,6);
        print MAIL "Date: $date_str\n";
		print MAIL "$puserid $name needs to be reviewed before sending internal approval:\n"; 
		print MAIL "Mailing Tool: http://mailingtool.routename.com:83/cgi-bin/advertiser_disp2.cgi?pmode=U&puserid=$puserid\n\n";
		print MAIL "Notes: $physical_addr\n\n";
		close(MAIL);
		$backto="/cgi-bin/send_approval.cgi?aid=$puserid&i=1&cemail=ALL";
	}
#	if ((($test_flag eq "Y") and ($old_test_flag ne "Y")) or (($pixel_placed eq "Y") and ($old_pixel_placed eq "N")) or (($cstatus eq "A") and ($old_cstatus ne "A")))
	if (($test_flag eq "N") and ($cstatus eq "A") and ($old_cstatus ne "A"))
	{
		my $catname;
		my $managername;
		$sql="select category_name from category_info where category_id=?";
		$sth1=$dbhq->prepare($sql);
		$sth1->execute($category_id);
		($catname)=$sth1->fetchrow_array();
		$sth1->finish();
		$sql="select manager_name from company_info,CampaignManager where company_info.manager_id=CampaignManager.manager_id and company_info.company_id=?";
		$sth1=$dbhq->prepare($sql);
		$sth1->execute($company_id);
		($managername)=$sth1->fetchrow_array();
		$sth1->finish();

       	open (MAIL,"| /usr/sbin/sendmail -t");
        my $from_addr = "Campaign ready for mailing <info\@zetainteractive.com>";
        print MAIL "From: $from_addr\n";
#        print MAIL "To: campaignnotification\@zetainteractive.com\n";
        print MAIL "To: campaignreadyformailing\@zetainteractive.com\n";
        print MAIL "Subject: Campaign ready for mailing: $puserid $name\n";
        my $date_str = $util->date(6,6);
        print MAIL "Date: $date_str\n";
		print MAIL "$puserid $name is ready for mailing\n\n"; 
		print MAIL "Campaign Manager: $managername\n";
		print MAIL "Category: $catname\n";
		print MAIL "Payout: \$$payout\n";
		print MAIL "Notes: $physical_addr\n\n";
		print MAIL "From Line(s):\n";
my $subject_str;
my $tid;
$sql="select advertiser_from,from_id from advertiser_from where advertiser_from.advertiser_id=$puserid and advertiser_from != '{{FOOTER_SUBDOMAIN}}' and status='A' order by advertiser_from";
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($subject_str,$tid)=$sth->fetchrow_array())
{
    print MAIL "$tid - $subject_str\n";
}
$sth->finish();
		print MAIL "\nSubject Line(s):\n";
$sql="select advertiser_subject,subject_id from advertiser_subject where advertiser_subject.status='A' and advertiser_subject.advertiser_id=$puserid order by advertiser_subject desc";
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($subject_str,$tid)=$sth->fetchrow_array())
{
    print MAIL "$tid - $subject_str\n";
}
$sth->finish();
		print MAIL "\nCreative ID's & Names:\n";
my $cid;
my $cname;
$sql="select creative_id,creative_name from creative where advertiser_id=$puserid and status='A' order by creative_id"; 
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($cid,$cname)=$sth->fetchrow_array())
{
    print MAIL "$cid - $cname\n";
}
$sth->finish();
		print MAIL "\nDays to Exclude:\n";
		if ($ex_monday ne "")
		{
			print MAIL "Monday\t";
		}
		if ($ex_tuesday ne "")
		{
			print MAIL "Tuesday\t";
		}
		if ($ex_wednesday ne "")
		{
			print MAIL "Wednesday\t";
		}
		if ($ex_thursday ne "")
		{
			print MAIL "Thursday\t";
		}
		if ($ex_friday ne "")
		{
			print MAIL "Friday\t";
		}
		if ($ex_saturday ne "")
		{
			print MAIL "Saturday\t";
		}
		if ($ex_sunday ne "")
		{
			print MAIL "Sunday\t";
		}

		print MAIL "\n\nMailing Tool: http://mailingtool.routename.com:83/cgi-bin/advertiser_disp2.cgi?pmode=U&puserid=$puserid\n";
		close(MAIL);
	}
	if (($cstatus eq "B" or $cstatus eq "C") && ($old_cstatus ne "B" and $old_cstatus ne "C"))
	{
		my $cnt;
#		$sql="select count(*) from advertiser_info where status='R'";
#		$sth1 = $dbhq->prepare($sql);
#		$sth1->execute();
#		($cnt)=$sth1->fetchrow_array();
#		$sth1->finish();
#		if ($cnt < 4)
#		{
#	       	open (MAIL,"| /usr/sbin/sendmail -t");
#	        my $from_addr = "Setup Team <info\@zetainteractive.com>";
#	        print MAIL "From: $from_addr\n";
#	        print MAIL "To: sales\@zetainteractive.com,mailops\@zetainteractive.com\n";
#	        print MAIL "Subject: The Setup Team is available to setup additional campaigns\n";
#	        my $date_str = $util->date(6,6);
#	        print MAIL "Date: $date_str\n";
#			print MAIL "Please send additional setup requests to Liz.\n\n"; 
#			close(MAIL);
#		}
	}
	if (($cstatus eq "D") && ($old_cstatus ne "D"))
	{
        open (MAIL,"| /usr/sbin/sendmail -t");
        my $from_addr = "Advertiser Deleted <info\@zetainteractive.com>";
        print MAIL "From: $from_addr\n";
        print MAIL "To: alerts\@zetainteractive.com\n";
        print MAIL "Subject: Advertiser $name deleted\n";
        my $date_str = $util->date(6,6);
        print MAIL "Date: $date_str\n";
        print MAIL "X-Priority: 1\n";
        print MAIL "X-MSMail-Priority: High\n";
        print MAIL "\nAdvertiser $name($puserid) has been deleted thru the tool.\n";
		$sql="select campaign_name,status from campaign where deleted_date is null and advertiser_id=$puserid and status in ('W','T')";
		my $sth1a = $dbhq->prepare($sql);
		$sth1a->execute();
		while (($cname,$tstatus) = $sth1a->fetchrow_array())
		{
			if ($tstatus eq "D")
			{
				print MAIL "Daily Deal - $cname\n";
			}
			elsif ($tstatus eq "T")
			{
				print MAIL "Trigger Deal - $cname\n";
			}
		}
		$sth1a->finish();
        close(MAIL);
	}
	$auto_url_sid=~s/ //g;
	if (($old_auto_url_sid ne $auto_url_sid) and ($auto_url_sid ne "") and ($auto_url_sid ne "0"))
	{
		my @SID=split(',',$auto_url_sid);
		$_=$name;
		if (/PPC/)
		{
		}
		else
		{
			my $tpixel="http://affiliate.adiclicks.com/rd/ipx.php?hid=TrackingVariable&sid=".$SID[0]."&transid=TransactionIDHere";
       		$sql="update advertiser_info set hitpath_tracking_pixel='$tpixel' where advertiser_id=$puserid";
       		my $rows = $dbhu->do($sql);
		}
       	$sql="update advertiser_info set sid=$SID[0] where advertiser_id=$puserid";
       	my $rows = $dbhu->do($sql);
		$sql="delete from advertiser_tracking where advertiser_id=$puserid and daily_deal='N'";
       	$rows = $dbhu->do($sql);

	   	$sql = "select hitpath_id from user where user_id=1";
		$sth = $dbhq->prepare($sql);
		$sth->execute();
		($hitpath_id) = $sth->fetchrow_array();
		$sth->finish();

		my $redir_domain=$util->getConfigVal("REDIR_DOMAIN");
		my $sidcnt=1;

		foreach my $tsid (@SID)
		{
			my $url;
			my $turl;
			if ($util->getConfigVal("TOOL") eq "AHC")
			{
				$turl="http://".$redir_domain."/rd/r.php?sid=".$tsid."&pub=".$hitpath_id."&c1={{CID}}&c2={{EMAIL_USER_ID}}_{{CRID}}_{{F}}_{{S}}_{{TID}}_{{BINDING_ID}}_{{MID}}_{{HEADER}}_{{FOOTER}}&c3={{NID}}";
			}
			else
			{
				$turl="http://".$redir_domain."/rd/r.php?sid=".$tsid."&pub=".$hitpath_id."&c1={{CID}}&c2={{EMAIL_USER_ID}}_{{CRID}}_{{F}}_{{S}}_{{TID}}_{{BINDING_ID}}_{{MID}}_{{HEADER}}_{{FOOTER}}&c3={{EMAIL_ADDR}}";
			}
			$url=$turl;
			$url=~s/{{NID}}/1/g;
			$sql="insert into links(refurl,date_added) values('$url',now())";
			$rows = $dbhu->do($sql);
			#
			# Get id just added
			#
			my $lid;
			$sql="select max(link_id) from links where refurl='$url'";
			$sth1 = $dbhu->prepare($sql) ;
			$sth1->execute();
			($lid) = $sth1->fetchrow_array();
			$sth1->finish();
			#
			# Insert record into advertiser_tracking 
			#
			$sql="insert into advertiser_tracking(advertiser_id,url,code,date_added,client_id,link_id,daily_deal,link_num) values($puserid,'$url','$hitpath_id',curdate(),1,$lid,'N',$sidcnt)";
			$rows = $dbhu->do($sql);
	#
	
			$sql = "select hitpath_id,user_id from user where hitpath_id!= '' and user_id > 1 and status='A'";
			my $sth2 = $dbhq->prepare($sql);
			$sth2->execute();
			my $mid;
			my $client_id;
			while (($mid,$client_id) = $sth2->fetchrow_array())
			{
				my $temp_url = $turl;
				$temp_url =~ s/$hitpath_id/$mid/;
				$temp_url =~ s/{{NID}}/$client_id/;
				my $lid;
				$sql="select max(link_id) from links where refurl='$temp_url'";
				$sth1 = $dbhq->prepare($sql) ;
				$sth1->execute();
				($lid) = $sth1->fetchrow_array();
				$sth1->finish();
				if ($lid > 0)
				{
				}
				else
				{
					$sql="insert into links(refurl,date_added) values('$temp_url',now())";
					$rows = $dbhu->do($sql);
					$sql="select max(link_id) from links where refurl='$temp_url'";
					$sth1 = $dbhu->prepare($sql) ;
					$sth1->execute();
					($lid) = $sth1->fetchrow_array();
					$sth1->finish();
				}
				#
				# Insert record into advertiser_tracking
				#
				$sql="insert into advertiser_tracking(advertiser_id,url,code,date_added,client_id,link_id,daily_deal,link_num) values($puserid,'$temp_url','$mid',curdate(),$client_id,$lid,'N',$sidcnt)";
				$rows = $dbhu->do($sql);
			}
			$sth2->finish();
			$sidcnt++;
		}
	}
	if ((($old_auto_cake_creativeid ne $auto_cake_creativeid) and ($auto_cake_creativeid ne "") and ($auto_cake_creativeid ne "0")) or ($linkType ne $old_linkType))
	{
		my @CID=split(',',$auto_cake_creativeid);
		my $xlme_mid=$util->getConfigVal("XLME_AFFILIATE");
		my $redir_domain;
		if ($linkType eq "XLME")
		{
			$redir_domain=$util->getConfigVal("XLME_CAKE_REDIR_DOMAIN");
		}
		else
		{
			$redir_domain=$util->getConfigVal("CAKE_REDIR_DOMAIN");
		}
		my $cake_offerID;

		$sql="select offerID from CakeCreativeOfferJoin where creativeID=$CID[0]";
		$sth=$dbhu->prepare($sql);
		$sth->execute();
		($cake_offerID)=$sth->fetchrow_array();
		$sth->finish();
		#my $tpixel="http://cktrk.net/p.ashx?o=".$cake_offerID."&t=TRANSACTION_ID";
		my $tpixel="http://gztkr.mobi/?gmid=PROPERTY_ID&oxid=".$cake_offerID."&trxid=TRANSACTION_ID";
       	$sql="update advertiser_info set hitpath_tracking_pixel='$tpixel' where advertiser_id=$puserid";
       	my $rows = $dbhu->do($sql);
       	$sql="update advertiser_info set cake_creativeID=$CID[0] where advertiser_id=$puserid";
       	my $rows = $dbhu->do($sql);
		$sql="delete from advertiser_tracking where advertiser_id=$puserid and daily_deal='N'";
       	$rows = $dbhu->do($sql);

		my $cake_subaffiliateID;
		my $sidcnt=1;
		foreach my $tsid (@CID)
		{
			$sql = "select cakeAffiliateID,user_id,cakeSubAffiliateID from user where cakeSubAffiliateID != '' and cakeSubAffiliateID > 0 and cakeAffiliateID > 0 and status='A'";
			my $sth2 = $dbhq->prepare($sql);
			$sth2->execute();
			my $mid;
			my $client_id;
			while (($mid,$client_id,$cake_subaffiliateID) = $sth2->fetchrow_array())
			{
				if ($linkType eq "XLME")
				{
					$mid=$xlme_mid;
				}
				my $temp_url="http://".$redir_domain."/?a=".$mid."&c=".$tsid."&s1=".$cake_subaffiliateID."&s2={{EMAIL_USER_ID}}_{{CRID}}_{{F}}_{{S}}_{{TID}}&s3={{EMAIL_ADDR}}&s4={{CID}}&s5={{BINDING_ID}}_{{MID}}_{{HEADER}}_{{FOOTER}}";
				if ($offer_type eq "CPC")
				{
					$temp_url.="&p=c";
				}
				elsif ($offer_type eq "REV")
				{
					$temp_url.="&p=r";
				}
				my $lid;
				$sql="select max(link_id) from links where refurl='$temp_url'";
				$sth1 = $dbhq->prepare($sql) ;
				$sth1->execute();
				($lid) = $sth1->fetchrow_array();
				$sth1->finish();
				if ($lid > 0)
				{
				}
				else
				{
					$sql="insert into links(refurl,date_added) values('$temp_url',now())";
					$rows = $dbhu->do($sql);
					$sql="select max(link_id) from links where refurl='$temp_url'";
					$sth1 = $dbhu->prepare($sql) ;
					$sth1->execute();
					($lid) = $sth1->fetchrow_array();
					$sth1->finish();
				}
				#
				# Insert record into advertiser_tracking
				#
				$sql="insert into advertiser_tracking(advertiser_id,url,code,date_added,client_id,link_id,daily_deal,link_num) values($puserid,'$temp_url','$mid',curdate(),$client_id,$lid,'N',$sidcnt)";
				$rows = $dbhu->do($sql);
			}
			$sth2->finish();
			$sidcnt++;
		}
	}
	$sql = "update advertiser_info set url_count=(select count(*) from advertiser_tracking where advertiser_id=$puserid) where advertiser_id=$puserid";
	$rows = $dbhu->do($sql);
#	if ($dbhu->err() != 0)
#	{
#		my $errmsg = $dbhu->errstr();
#		print LOG "Error: $errmsg\n";
#	    $pmesg = "Error - Updating user record for AdvertiserID: $puserid $errmsg";
#	}
#	else
#	{
	    $pmesg = "Successful UPDATE of Advertiser Info!" ;
#	}
my $link_id;
my $sth1;
	if ($unsub_link ne "")
	{
		$sql = "select link_id from links where refurl='$unsub_link'";
		$sth1 = $dbhq->prepare($sql);
		$sth1->execute();
		if (($link_id) = $sth1->fetchrow_array())
		{
			$sth1->finish();
		}
		else
		{
			$sth1->finish();
			$sql = "insert into links(refurl,date_added) values('$unsub_link',now())";
			$rows = $dbhu->do($sql);
my $refurl;
		}
	}
}  # end sub - update_advertiser

sub cb1 
{
     my($tag, $url1, $url2, %links) = @_;
my ($scheme, $auth, $path, $query, $frag);
my $name;
my $suffix;
	my $temp_id;
	my $sql;
	my $sth1;
	my $link_id;
	my $temp_name;
	my $temp_str;
	 if ((($tag eq "a") && ($url1 eq "href")) || (($tag eq "area") && ($url1 eq "href")))
	 {
		$_ = $url2;
		if ((/{{URL}}/) or (/{{ADV_UNSUB_URL}}/))
		{
			$temp_id = 0;
		}
        elsif ($url2 eq "")
        {
            $temp_id = 0;
        }
		else
		{
			$url2 =~ s/\?/\\?/g;
			$url2=~ s/\[/\\[/g;
            $global_text =~ s/"$url2"/"{{ADV_UNSUB_URL}}" target=_blank/gi;
            $global_text =~ s/$url2/"{{ADV_UNSUB_URL}}" target=_blank/gi;
		}
	 }
}
sub recalc_priority
{
	my $aid;
	my $priority;
    my $mgroup;

    $sql="select distinct MemberGroup from CampaignManager";
    my $sth2=$dbhu->prepare($sql);
    $sth2->execute();
    while (($mgroup)=$sth2->fetchrow_array())
    {
        $sql="select advertiser_id,priority from advertiser_info where ((status not in ('A','I')) or (status='A' and test_flag in ('Y','U'))) and manager_id in (select manager_id from CampaignManager where MemberGroup='".$mgroup."') order by priority";
		my $sth=$dbhu->prepare($sql);
		$sth->execute();
		my $i=1;
		while (($aid,$priority)=$sth->fetchrow_array())
		{
			if ($priority != $i)
			{
				$sql="update advertiser_info set priority=$i where advertiser_id=$aid";
				my $rows=$dbhu->do($sql);
			}
			$i++;
		}
		$sth->finish();
	}
	$sth2->finish();
}
