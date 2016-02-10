#!/usr/bin/perl

# *****************************************************************************************
# send_pixel_request.cgi
#
# this page sends pixel request 
#
# History
# *****************************************************************************************

# include Perl Modules

use strict;
use util_mail;
use util;

my $util = util->new;
my $query = CGI->new;
my $dbh;
my $sql;
my $sth;
my $aid = $query->param('aid');
my $email_str;
my $aname;
my $cid;
my $friendly_name;
my $adv_url;
my $hitpath_var;
my $hitpath_pixel;
my $cemail;
my $from_addr;

# connect to the util database
my ($dbhq,$dbhu)=$util->get_dbh();

# check for login
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}
$sql = "select advertiser_name,company_id,friendly_advertiser_name,advertiser_url,hitpath_tracking_pixel from advertiser_info where advertiser_id=$aid"; 
$sth = $dbhu->prepare($sql);
$sth->execute();
($aname,$cid,$friendly_name,$adv_url,$hitpath_pixel) = $sth->fetchrow_array();
$sth->finish();

$sql = "select cm.contact_email from company_info_contact cm where cm.company_id=$cid"; 
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($cemail) = $sth->fetchrow_array())
{
	$email_str = $email_str . $cemail . ",";
}
$sth->finish();
$_ = $email_str;
chop;
$email_str=$_;

if ($adv_url ne "") 
{
    $hitpath_var = get_hitpath_var($adv_url);
    $adv_url =~ s/jjhitjj/TrackingVariable/g;
}
if ($hitpath_pixel ne "")
{
    $hitpath_pixel=~ s/TrackingVariable/{$hitpath_var}/;
    $hitpath_pixel=~ s/TransactionIDHere/{TransactionIDHere}/;
}
$sql="select tracking_id,url,date_format(date_approved,'%m/%d/%y'),approved_by from advertiser_tracking where advertiser_id=$aid and client_id=1 and daily_deal='N' order by url"; 
$sth = $dbhu->prepare($sql);
$sth->execute();
my $sid;
my $url;
my $date_approved;
my $approved_by;
while (($sid,$url,$date_approved,$approved_by) = $sth->fetchrow_array())
{
	$url =~ s/{{CID}}//g;
	$url =~ s/{{EMAIL_USER_ID}}//g;
	$url =~ s/{{EMAIL_ADDR}}//g;

	my $tstr;
	my $i=index($url,"&c1");
	if ($i > -1)
	{
		$tstr=substr($url,0,$i);
	}
	$url=$tstr;	
}
$sth->finish();

open (MAIL,"| /usr/sbin/sendmail -t");
$from_addr = "Spirevision Approval Team <approval\@zetainteractive.com>";
print MAIL "From: $from_addr\n";
print MAIL "To: $cemail\n";
print MAIL "Cc: group.approvals\@zetainteractive.com\n";
print MAIL "Subject: $friendly_name Pixel Verification Request\n"; 
my $date_str = $util->date(6,6);
print MAIL "Date: $date_str\n";

print MAIL "Dear Advertiser, \n";
print MAIL "\n";
print MAIL "You are receiving this email because we, at Spire Vision, are in the closing stages of setting up your offer, $friendly_name, but require a pixel to be placed and verified before completion. \n";
print MAIL "\n";
print MAIL "Please note that in order for the pixel to be fired, our HID variable must be populated with the value we are passing into your $hitpath_var variable in your Advertiser URL. \n";
print MAIL "\n";
print MAIL "----------------\n";
print MAIL "\n";
print MAIL "$friendly_name\n";
print MAIL "\n";
print MAIL "ADVERTISER URL: \n";
print MAIL "$adv_url \n";
print MAIL "\n";
print MAIL "IFRAME PIXEL: \n";
print MAIL "<iFrame frameborder=0 height=1 width=1 src='$hitpath_pixel'></iFrame> \n";
print MAIL "\n";
print MAIL "Note: We prefer an iframe pixel for the most accurate tracking, but if your system does not support iframe pixels, you can use the following image pixel: \n";
print MAIL "\n";
print MAIL "IMAGE PIXEL:\n";
print MAIL "<img border=0 height=1 width=1 src='$hitpath_pixel'> \n";
print MAIL "\n";
print MAIL "If you need a secure pixel, you can add an \"s\"(i.e. https). \n";
print MAIL "----------------\n";
print MAIL "\n";
print MAIL "After the pixel has been placed, PLEASE TEST IT BY PLACING A TEST LEAD USING THE FOLLOWING URL:\n";
print MAIL "$url\n";
print MAIL "\n";
print MAIL "Kindly inform us when this process is completed so we can verify that we are tracking properly.  Please let us know if you have any questions or need assistance.\n";
print MAIL "\n";
print MAIL "Thanks,\n";
print MAIL "\n";
print MAIL "The Spire Vision Setup Team\n";
print MAIL "\n";
close MAIL;

print "Location:advertiser_disp2.cgi?puserid=$aid&mode=U\n\n";

exit(0);

sub get_hitpath_var
{
	my ($t1)=@_;
	my $var1;
	my $var2;
	my @flds;
	my $t2;
	my $t3;
	$_=$t1;
	if (/&/)
	{	
	(@flds)=split('&',$t1);
	my $i=0;
	while ($i <= $#flds)
	{
		($var1,$var2)=split('=',$flds[$i]);
		if ($var2 eq "jjhitjj")
		{
			$_=$var1;
			if (/\?/)
			{
				($t2,$t3)=split('\?',$var1);
				$var1=$t3;
			}
			return $var1;
		}
		$i++;
	}	
	}
	else
	{
		(@flds)=split('\?',$t1);
		($var1,$var2)=split('=',$flds[1]);
		if ($var2 eq "jjhitjj")
		{
			return $var1;
		}
	}
	return "TrackingVariable";
}
