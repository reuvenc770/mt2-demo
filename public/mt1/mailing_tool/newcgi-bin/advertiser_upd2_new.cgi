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
my $company;
my $old_cstatus;
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
my $internal_email_addr;
my $physical_addr;
my $offer_type ;
my $pixel_placed ;
my $pixel_verified;
my $oldpixelverified;
my $pixel_requested;
my $tracking_pixel;
my $filedate;
my $prepop;
my $advertiser_rating;
my $payout ;
my $advertiser_url;
my $ecpm;
my $suppid1 ;
my $supp_file ;
my $supp_url ;
my $auto_download ;
my $supp_username ;
my $supp_password ;
my $track_internally ;
my $unsub_link ;
my $unsub_image ;
my $category_id ;
my $backto;
my $upload_dir;
my $upload_dir1;
my $immediate_upload=$query->param('immediate_upload');
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
$ENV{'IMAGE_HOSTER_SSH_KEY'}="/var/www/.ssh/images.key";
my $imageHoster = App::WebAutomation::ImageHoster->new($data);

$pmesg="";
srand();
my $rid=rand();
my $cstatus;

#----------- check for login ------------------
my $user_id = util::check_security();
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}


    #---------------------------------------------------
    # Get the information about the user from the form
    #---------------------------------------------------
    $name = $query->param('name');
    $idate = $query->param('idate');
    $email_addr = $query->param('email_addr');
    $physical_addr = $query->param('address');
    $internal_email_addr = $query->param('internal_email_addr');
    $cstatus = $query->param('cstatus');
    $puserid = $query->param('puserid');
	$offer_type = $query->param('deal_type');
	$pixel_placed = $query->param('pixel_placed');
	$oldpixelverified = $query->param('oldpixelverified');
	$pixel_verified = $query->param('pixel_verified');
	$pixel_requested = $query->param('pixel_requested');
	$prepop= $query->param('prepop');
	$advertiser_rating = $query->param('advertiser_rating');
	$tracking_pixel = $query->param('tracking_pixel');
	$tracking_pixel =~ s/"/\"/g;
	$filedate = $query->param('filedate');
	$payout = $query->param('payout');
	$advertiser_url = $query->param('orig_advertiser_url');
    my $upload_filehandle = $query->upload("supp_file");
	if ($payout eq "")
	{
		$payout = 0;
	}
	$ecpm = $query->param('ecpm');
	$suppid1 = $query->param('suppid1');
	$supp_file = $query->param('supp_file');
	$supp_url = $query->param('supp_url');
	$auto_download = $query->param('auto_download');
	$supp_username = $query->param('supp_username');
	$supp_password = $query->param('supp_password');
	$track_internally = $query->param('track_internally');
	$unsub_link = $query->param('unsub_link');
	$unsub_image = $query->param('unsub_image');
	my $old_unsub = $query->param('old_unsub');
    $unsub_image =~ s/.*[\/\\](.*)/$1/;
    $supp_file =~ s/.*[\/\\](.*)/$1/;
	$category_id = $query->param('category_id');
	$backto = $query->param('backto');
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
	$sql = "select list_id from vendor_supp_list_info where list_name='$aname'"; 
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
		$sql = "insert into vendor_supp_list_info(list_name,list_cnt) values('$aname',0)";
		my $rows = $dbhu->do($sql);
		#
		# Get id of just added list
		#
		$sql = "select list_id from vendor_supp_list_info where list_name='$aname'";
		$sth1 = $dbhu->prepare($sql);
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
open (LOG,">/tmp/jim.a");
print LOG "in if\n";
	#
	# Check to see if suppression list already exists
	#
$sql = "select list_id,advertiser_name from advertiser_info,vendor_supp_list_info where advertiser_id=$puserid and advertiser_info.advertiser_name=list_name"; 
$sth1 = $dbhq->prepare($sql);
$sth1->execute();
($vid,$aname) = $sth1->fetchrow_array();
$sth1->finish;
	$sql = "update vendor_supp_list_info set filedate='$filedate',last_updated='$filedate' where list_id=$vid";
print LOG "$sql\n";
close LOG;
	my $rows=$dbhu->do($sql);
}
my $cdir;
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
    print qq {
    <script language="Javascript">
    var newwin = window.open("$backto", "Preview", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,width=900,height=500,left=25,top=50");
    newwin.focus();
    </script> \n };
    $pmesg="";
print<<"end_of_html";
<head>
<body>
<script language="JavaScript">
document.location="/cgi-bin/advertiser_disp2.cgi?pmode=$pmode&puserid=$puserid&pmesg=$pmesg&rid=$rid";
</script>
</body></html>
end_of_html
}
elsif (/approval.cgi/)
{
    print qq {
    <script language="Javascript">
    var newwin = window.open("$backto", "Approval", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,width=900,height=500,left=25,top=50");
    newwin.focus();
    </script> \n };
    $pmesg="";
print<<"end_of_html";
<head>
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
	print LOG "$backto\n";
	print "Location: $backto\n\n";
}
close(LOG);
exit(0);


#===============================================================================
# Sub: update_advertiser
#===============================================================================
sub update_advertiser
{
	my $rows;
	my $cid;

	$sql="select status from advertiser_info where advertiser_id=$puserid";
	$sth1 = $dbhq->prepare($sql);
	$sth1->execute();
	($old_cstatus) = $sth1->fetchrow_array();
	$sth1->finish();

	my $temp_name=$name;
	$temp_name=~s/'/''/g;
	$sql = "update advertiser_info set pre_pop='$prepop', advertiser_rating='$advertiser_rating', advertiser_name='$temp_name', physical_addr='$physical_addr',email_addr='$email_addr',internal_email_addr='$internal_email_addr',status='$cstatus',offer_type='$offer_type',tracking_pixel='$tracking_pixel',pixel_placed='$pixel_placed',pixel_verified='$pixel_verified',pixel_requested='$pixel_requested',payout=$payout,ecpm=$ecpm,vendor_supp_list_id=$suppid1,suppression_file='$supp_file',suppression_url='$supp_url',auto_download='$auto_download',suppression_username='$supp_username',suppression_password='$supp_password',track_internally='$track_internally',unsub_link='$unsub_link',unsub_image='$unsub_image',category_id=$category_id,exclude_days='$ex_str',advertiser_url='$advertiser_url',inactive_date='$idate' where advertiser_id = $puserid";
	$rows = $dbhu->do($sql);
	if (($cstatus eq "I") && ($old_cstatus ne "I"))
	{
        open (MAIL,"| /usr/sbin/sendmail -t");
        my $from_addr = "Advertiser Set Inactive<info\@zetainteractive.com>";
        print MAIL "From: $from_addr\n";
        print MAIL "To: alerts\@zetainteractive.com\n";
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
my $BASE_DIR;
my $refurl;
$sql = "select parmval from sysparm where parmkey='BASE_DIR'";
$sth1 = $dbhq->prepare($sql);
$sth1->execute();
($BASE_DIR) = $sth1->fetchrow_array();
$sth1->finish;
#open(FILE,"> ${BASE_DIR}logs/redir.dat") or die "can't open file : $!";
#$sql = "select link_id,refurl from links order by link_id";
#$sth1 = $dbhq->prepare($sql);
#$sth1->execute();
#while (($link_id,$refurl) = $sth1->fetchrow_array())
#{
#    print FILE "$link_id|$refurl\n";
#}
#$sth1->finish();
#close(FILE);
#my @args = ("${BASE_DIR}newcgi-bin/cp_redir_tmp.sh");
#system(@args) == 0 or die "system @args failed: $?";
		}
	}
#
#	Update creatives
#
my $BASE_DIR;
$sql = "select parmval from sysparm where parmkey='BASE_DIR'";
$sth1 = $dbhq->prepare($sql);
$sth1->execute();
($BASE_DIR) = $sth1->fetchrow_array();
$sth1->finish;
$sql = "select creative_id from creative where advertiser_id=$puserid and status='A'";
$sth1 = $dbhq->prepare($sql);
$sth1->execute();
while (($cid) = $sth1->fetchrow_array())
{
    my @args = ("${BASE_DIR}newcgi-bin/get_camp_new.pl","$cid");
    system(@args) == 0 or die "system @args failed: $?";
    my @args = ("${BASE_DIR}newcgi-bin/get_camp_3rdparty.pl","$cid");
    system(@args) == 0 or die "system @args failed: $?";
}
$sth1->finish;

}  # end sub - update_advertiser
