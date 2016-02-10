#!/usr/bin/perl

# ******************************************************************************
# expertsender_export.cgi
#
# ******************************************************************************

# include Perl Modules

use strict;
use Net::FTP;
use CGI;
use util;
use File::Type;
use HTML::LinkExtor;
use HTML::FormatText::WithLinks;
use WWW::Curl::easy;
use URI::Split qw(uri_split uri_join);
use File::Basename;
use App::Mail::MtaRandomization;
use App::WebAutomation::ImageHoster;
use MIME::Base64;
use URI::Escape;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $ft = File::Type->new();
my $sth;
my $sth1;
my $sql;
my $temp_id;
##my $dbh;
my $url_id;
my $bid;
my $unsub_use;
my $country;
my $chklinkstr="";
my $unsub_text;
my $got_it;
my $errmsg;
my $images = $util->get_images_url;
my $oldsubAffiliateID;
my $curl;
my $footer_subdomain;
my $rest_str;
my $sdate1;
my $curtime;
my $sdate;
my $aname;
my $linkType;
my $offer_type;
my $supp_name;
my $pexicom_last_upload;
my $bname;
my $header_text;
my $footer_text;
my $network_name;
my $tracking_str;
my $num_subject;
my $unsub_flag;
my $include_images;
my $physical_addr;
my $temp_val;
my $num_from;
my $num_creative;
my $subject_str;
my $from_str;
my $dir2;
my $creative_name;
my $oflag;
my $html_code;
my $global_text;
my $global_subAffiliateID;
my $aflag;
my $rname;
my $rloc;
my $rdate;
my $remail;
my $remailid;
my $rcdate;
my $rip;
my $rcid;
my $link_id;
my $global_domain;
my $img_domain;
my $sid;
my $catid;
my $vid;
my $subdomain_name;
my $unsub_url;
my $unsub_img;
my $advertiser_url;
my $new_camp_id;
my $mon;
my $day;
my $year;
my $mailer_name;
my $temp_cid;
my $profile_id;
my $pname;
my $suppression_path;
my $creative_path;
my $ftp_ip;
my $ftp_username;
my $ftp_password;
my $mflag_str;
my $temp_str;
my $zeta=18;
my $c1;
my $cdate;
my $advertiser_unsub_id;
my $linkstr;
my $master_str;
my $template_name;
my $client_name;

# connect to the util database
my $dbhq;
my $dbhu;
($dbhq,$dbhu)=$util->get_dbh();

# check for login
my $user_id = util::check_security();
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
	$util->clean_up();
    exit(0);
}
my $cake_domain=$util->getConfigVal("CAKE_REDIR_DOMAIN");
my $xlme_cake_domain=$util->getConfigVal("XLME_CAKE_REDIR_DOMAIN");
my $xlme_affiliate=$util->getConfigVal("XLME_AFFILIATE");
my $esp_cake_domain=$util->getConfigVal("ESP_CAKE_REDIR_DOMAIN");
my $esp_cpm_cake_domain=$util->getConfigVal("ESP_CPM_CAKE_REDIR_DOMAIN");
#
my $aid= $query->param('aid');
my $cid= $query->param('creative');
my $csubject= $query->param('csubject');
my $cfrom= $query->param('cfrom');
my $clientid= $query->param('clientid');
my $templateid= $query->param('templateid');
my $redir_domain=$query->param('redir_domain');;
my $content_domain=$query->param('content_domain');;
my $esp=$query->param('esp');
my $send_date = $query->param('send_date');
my $subAffiliateID = $query->param('subAffiliateID');
my $affiliateID = $query->param('AffiliateID');
my $cakeDomain = $query->param('cakeDomain');
my $newurl = $query->param('newurl');
if ($newurl eq "")
{
	$newurl="N";
}
my $one_image= $query->param('one_image');
if ($one_image eq "")
{
	$one_image="N";
}

my $imageHoster;
if ($one_image eq "Y")
{
	my $data={};
	$data->{'imageCollectionID'}="000000000000001";
	$ENV{'IMAGE_HOSTER_SSH_KEY'}="/var/www/.ssh/images.sav";
	$imageHoster = App::WebAutomation::ImageHoster->new($data);
}
my $redir_random_str="";

my $footer_id= $query->param('footer_id');
my $emailfield;
my $espLabel;
my $eidfield;
my $espID;
######
my $mtaRandom;
#
$c1=$esp;
$sql="select espID,espLabel,eidField,emailField from ESP where espName='$esp'";
$sth=$dbhu->prepare($sql);
$sth->execute();
($espID,$espLabel,$eidfield,$emailfield)=$sth->fetchrow_array();
$sth->finish();
#
#	Get information about the advertiser 
#
$sql = " select advertiser_name,vendor_supp_list_id,unsub_link,unsub_image,advertiser_url,unsub_use,unsub_text,curdate(),linkType,offer_type,countryCode from advertiser_info ai join Country c on c.countryID=ai.countryID where advertiser_id=?"; 
$sth = $dbhq->prepare($sql);
$sth->execute($aid);
($aname,$temp_id,$unsub_url,$unsub_img,$advertiser_url,$unsub_use,$unsub_text,$cdate,$linkType,$offer_type,$country)=$sth->fetchrow_array();
$sth->finish();
if ($offer_type eq "CPM")
{
	$esp_cake_domain=$esp_cpm_cake_domain;
}
if ($espLabel eq "AlphaS")
{
	$esp_cake_domain="gravitypresence.com";
}
if ($espLabel eq "AlphaD")
{
	$esp_cake_domain="i.soltrail.com";
}
if ($espLabel eq "GotClick")
{
	$esp_cake_domain="quickpixel.net";
}
if ($espLabel eq "AlphaHYD")
{
	$esp_cake_domain=$cakeDomain;
}
$sql = " select creative_name,html_code from creative where creative_id=?";
$sth = $dbhq->prepare($sql);
$sth->execute($cid);
($creative_name,$html_code)=$sth->fetchrow_array();
$sth->finish();
$_=$html_code;
if ((/redir1.cgi/) and (/&ccID=/))
{
    $html_code =~ s/\&sub=/\&XXX=/g;
    $html_code =~ s/\&amp;/\&/g;
    $global_text = $html_code;
	if (($newurl eq "Y") or ($newurl eq "G"))
	{
        $global_subAffiliateID=$subAffiliateID;
       	my $p = HTML::LinkExtor->new(\&cb2);
       	$p->parse($html_code);
	}
	elsif (($esp eq "ALP001") or ($esp eq "GotClick") or ($esp eq "ALP002") or ($esp eq "PACK1"))
	{
       	my $p = HTML::LinkExtor->new(\&cb1);
       	$p->parse($html_code);
	}
    $html_code = $global_text;
}
elsif ((/redir1.cgi/) and ($esp eq "PACK1"))
{
    $html_code =~ s/\&sub=/\&XXX=/g;
    $html_code =~ s/\&amp;/\&/g;
    $global_text = $html_code;
    my $p = HTML::LinkExtor->new(\&cb3);
    $p->parse($html_code);
    $html_code = $global_text;
}
if ($send_date eq "")
{
	$send_date=$cdate;
}
if ($unsub_url eq "")
{
	$advertiser_unsub_id=0;
}
else
{
	$sql = "select link_id from links where refurl='$unsub_url'";
    $sth=$dbhu->prepare($sql);
    $sth->execute();
    if (($advertiser_unsub_id)=$sth->fetchrow_array())
    {
    }
    else
    {
    	$sql = "insert into links(refurl,date_added) values('$unsub_url',now())";
        my $rows=$dbhu->do($sql);
        $sql = "select link_id from links where refurl='$unsub_url'";
        $sth=$dbhu->prepare($sql);
        $sth->execute();
        ($advertiser_unsub_id)=$sth->fetchrow_array();
	}
    my $iret=util::checkLink($unsub_url);
    if ($iret)
    {
    	$chklinkstr.="$aid,$advertiser_unsub_id,$aname,$esp,$country\n";
    }
}
$sql="select cakeSubAffiliateID from user where user_id=?";
$sth = $dbhu->prepare($sql);
$sth->execute($clientid);
($oldsubAffiliateID)=$sth->fetchrow_array();
$sth->finish();

$sql = "select url from advertiser_tracking where advertiser_id=? and client_id=? and daily_deal='N' and link_num=1"; 
$sth = $dbhq->prepare($sql);
$sth->execute($aid,$clientid);
($linkstr)=$sth->fetchrow_array();
$sth->finish();
$linkstr=~s/{{CID}}/$c1/;
$linkstr=~s/{{FOOTER}}/{{FOOTER}}_${send_date}/;
if ($esp eq "PACK1")
{
	$linkstr=~s/s1=$oldsubAffiliateID/s1=&s5=SSoptSS/;
}
else
{
	$linkstr=~s/s1=$oldsubAffiliateID/s1=$subAffiliateID/;
}
if ($esp eq "GotClick")
{
	if ($linkType eq "XLME")
	{
		$linkstr=~s/a=$xlme_affiliate/a=15548/;
		$linkstr=~s/a=3219/a=15548/;
	}
	else
	{
		$linkstr=~s/a=13/a=$affiliateID/;
	}
}
elsif ($esp eq "ALP001")
{
	if ($linkType eq "XLME")
	{
		$linkstr=~s/a=$xlme_affiliate/a=15445/;
		$linkstr=~s/a=3219/a=15445/;
	}
	else
	{
		$linkstr=~s/a=13/a=$affiliateID/;
	}
}
elsif ($esp eq "ALP002")
{
	if ($linkType eq "XLME")
	{
		$linkstr=~s/a=$xlme_affiliate/a=$affiliateID/;
		$linkstr=~s/a=3219/a=$affiliateID/;
	}
	else
	{
		$linkstr=~s/a=13/a=$affiliateID/;
	}
}
elsif ($esp eq "PACK1")
{
	if ($linkType eq "XLME")
	{
		$linkstr=~s/a=$xlme_affiliate/a=$affiliateID/;
		$linkstr=~s/a=3219/a=$affiliateID/;
	}
	else
	{
		$linkstr=~s/a=13/a=$affiliateID/;
	}
}
else
{
	if ($linkType eq "XLME")
	{
		$linkstr=~s/a=$xlme_affiliate/a=15480/;
		$linkstr=~s/a=3219/a=15480/;
	}
	else
	{
		$linkstr=~s/a=13/a=$affiliateID/;
	}
}
$linkstr=~s/$cake_domain/$esp_cake_domain/;
#$linkstr=~s/$xlme_cake_domain/$esp_cake_domain/;
#
# Check for link
#
$link_id="";
if (($esp ne "ALP001") and ($esp ne "GotClick") and ($esp ne "ALP002") and ($esp ne "PACK1"))
{
while (($link_id eq "") and ($linkstr ne ""))
{
	$sql="select link_id from links where refurl=?";
	$sth=$dbhq->prepare($sql);
	$sth->execute($linkstr);
	if (($link_id)=$sth->fetchrow_array())
	{
	}
	else
	{
		$sql="insert ignore into links(refurl,date_added) values('$linkstr',now())";
		my $rows=$dbhu->do($sql);
	}
    my $iret=util::checkLink($linkstr);
    if ($iret)
    {
    	$chklinkstr.="$aid,$link_id,$aname,$esp,$country\n";
    }
}
if ($link_id eq "")
{
	$link_id=0;
}
}
my $xlink3;
my $xlink1;
if (($esp eq "ALP001") or ($esp eq "GotClick") or ($esp eq "ALP002") or ($esp eq "PACK1"))
{
    my $end=index($linkstr,"&s2=");
    $xlink3=substr($linkstr,0,$end);
    $xlink1=$unsub_url;
}
else
{
	if ($newurl eq "Y")
	{
		$redir_random_str=util::get_random();
		$xlink3="http://$redir_domain/z/$redir_random_str/$eidfield|1|$link_id|R";
	}
	elsif ($newurl eq "G")
	{
		$xlink3=util::get_gmail_url("REDIRECT",$redir_domain,$eidfield,$link_id);
	}
	else
	{
		$xlink3="http://$redir_domain/cgi-bin/redir1.cgi?eid=$eidfield&cid=1&em=$emailfield&id=$link_id&n=$clientid&f=$cfrom&s=$csubject&c=$cid&tid=$templateid&footerid=$footer_id&ctype=R";
	if ($espLabel eq "ZetaMail")
	{
		$xlink3.="&zetablastid=%%BLASTID%%";
	}
	}
	if ($newurl eq "Y")
	{
		$redir_random_str=util::get_random();
		$xlink1="http://$redir_domain/z/$redir_random_str/$eidfield|1|$advertiser_unsub_id|A";
	}
	elsif ($newurl eq "G")
	{
		$xlink1=util::get_gmail_url("ADVUNSUB",$redir_domain,$eidfield,$advertiser_unsub_id);
	}
	else
	{
		$xlink1="http://$redir_domain/cgi-bin/redir1.cgi?eid=$eidfield&cid=1&em=$emailfield&id=$advertiser_unsub_id&n=$clientid&f=$cfrom&s=$csubject&c=$cid&tid=$templateid&footerid=$footer_id&ctype=A";
	if ($espLabel eq "ZetaMail")
	{
		$xlink1.="&zetablastid=%%BLASTID%%";
	}
	}
}
my $unsublink=qq|http://$redir_domain/cgi-bin/redir1.cgi?eid=$eidfield&cid=1&em=$emailfield&id=42&n=$clientid&f=$cfrom&s=$csubject&c=$cid&tid=$templateid&footerid=$footer_id&ctype=U|;
if (($newurl eq "Y") or ($newurl eq "G"))
{
	$redir_random_str=util::get_random();
	$unsublink=qq^http://$redir_domain/z/$redir_random_str/$eidfield|1|42|U^;
}
if (($esp eq "ALP001") or ($esp eq "GotClick") or ($esp eq "ALP002") or ($esp eq "PACK1"))
{
	$unsublink=qq|http://$redir_domain/cgi-bin/unsubscribe.cgi|;
}


my $random_string=util::get_random();
my $random_string1=util::get_random();
my $img_prefix=$content_domain."/".$random_string."/".$random_string1;

$sql="select username from user where user_id=?";
$sth = $dbhq->prepare($sql);
$sth->execute($clientid);
($client_name)=$sth->fetchrow_array();
$sth->finish();

$sql="select html_code,template_name from brand_template where template_id=?";
$sth = $dbhq->prepare($sql);
$sth->execute($templateid);
($master_str,$template_name)=$sth->fetchrow_array();
$sth->finish();

$sql="select list_name from vendor_supp_list_info where list_id=?";
$sth = $dbhq->prepare($sql);
$sth->execute($temp_id);
($supp_name)=$sth->fetchrow_array();
$sth->finish();
#
$supp_name =~ s/ //g;
$supp_name =~ s/\)//g;
$supp_name =~ s/\(//g;
$supp_name =~ s/\&//g;
$supp_name =~ s/\://g;
$supp_name =~ s/\$//g;
$supp_name =~ s/\.//g;
$supp_name =~ s/\!//g;
#
my $subject_str;
my $sid;
$sql="select advertiser_subject from advertiser_subject where subject_id=$csubject"; 
$sth = $dbhq->prepare($sql);
$sth->execute();
($subject_str)=$sth->fetchrow_array();
$sth->finish();
$sql="select advertiser_from from advertiser_from where from_id=$cfrom"; 
$sth = $dbhq->prepare($sql);
$sth->execute();
($from_str)=$sth->fetchrow_array();
$sth->finish();
my $reccnt;
#
# Make changes to html_code
#
my $regExpCreativeHtml  = qr{</*(html|body)>}i;
$html_code=~ s/$regExpCreativeHtml//g;

my $t_html=$master_str;
$t_html=~s/{{CREATIVE}}/$html_code/g;
$html_code=$t_html;
$html_code =~ s/{{TRACKING}}/<IMG SRC="http:\/\/$redir_domain\/cgi-bin\/open.cgi?eid=$eidfield&cid=1&em=$emailfield&n=$clientid&f=$cfrom&s=$csubject&c=$cid&did=&binding=&tid=$templateid&openflag=1&nod=1&espID=$espID&subaff=$subAffiliateID" border=0 height=1 width=1>/g;
$html_code =~ s/{{CONTENT_HEADER}}//g;
$html_code =~ s/{{CONTENT_HEADER_TEXT}}//g;
$_ = $html_code;
if (/{{TIMESTAMP}}/)
{
	my $timestr = util::date($curtime,5);
    $html_code =~ s/{{TIMESTAMP}}/$timestr/g;
}
if (/{{REFID}}/)
{
	$html_code =~ s/{{REFID}}//g;
}
$html_code =~ s/{{CLICK}}//g;
# substitute end of page (closing body tag) with all the unsubscribe
# footer stuff that must go on the bottom of every email, adding the
# closing body tag back on
$html_code =~ s\</BODY>\</body>\;
$html_code =~ s\</HTML>\</html>\;

# now add end body tag to close the html email
$temp_str="";
if ($aid != 0)
{
	if ($unsub_use eq "TEXT")
	{
		$temp_str=$unsub_text;
	}
	else
	{
    	if ($unsub_img ne "")
       	{
        	my $regExpImage= qr{\.(jpg|jpeg|gif|bmp|png)}i;
            $unsub_img  =~ s/$regExpImage//g;

            if ( $unsub_img =~ /\// )
            {
            	if ($advertiser_unsub_id == 0)
                {
                	$temp_str = "<img src=\"http://$img_prefix/$unsub_img\" border=0><br><br>";
                }
                else
                {
                	$temp_str = "<a href=\"{{ADV_UNSUB_URL}}\"><img src=\"http://$img_prefix/$unsub_img\" border=0></a><br><br>";
                }
			}
            else
            {
            	if ($advertiser_unsub_id == 0)
                {
                	$temp_str = "<img src=\"http://$img_prefix/images/unsub/$unsub_img\" border=0><br><br>";
                }
                else
                {
                	$temp_str = "<a href=\"{{ADV_UNSUB_URL}}\"><img src=\"http://$img_prefix/images/unsub/$unsub_img\" border=0></a><br><br>";
                }
			}
		}
    }
}
$html_code=~ s/{{ADV_UNSUB}}/$temp_str/g;
my $footer_str="";
my $footer_name="";
if ($footer_id > 0)
{
	$sql="select footer_code,footer_name from Footers where footer_id=$footer_id";
	$sth = $dbhq->prepare($sql);
	$sth->execute();
	($footer_str,$footer_name)=$sth->fetchrow_array();
	$sth->finish();
}
#
my $i=1;
my $link_num;
while ($i <= 29)
{
	$_=$html_code;
	if (/{{URL$i}}/)
	{
		my $tlink_id;
		my $xlink;
		$link_num=$i+1;
		$sql = "select url from advertiser_tracking where advertiser_id=? and client_id=? and daily_deal='N' and link_num=$link_num"; 
		$sth = $dbhq->prepare($sql);
		$sth->execute($aid,$clientid);
		($linkstr)=$sth->fetchrow_array();
		$sth->finish();
        if (($esp eq "ALP001") or ($esp eq "GotClick") or ($esp eq "ALP002") or ($esp eq "PACK1"))
        {
        	my $end=index($linkstr,"&s2=");
            $xlink=substr($linkstr,0,$end);
			if ($linkType eq "XLME")
			{
				if ($esp eq "ALP001")
				{
            		$xlink=~s/a=$xlme_affiliate/a=15445/;
            		$xlink=~s/a=3219/a=15445/;
				}
				elsif ($esp eq "ALP002")
				{
            		$xlink=~s/a=$xlme_affiliate/a=765/;
            		$xlink=~s/a=3219/a=765/;
				}
				elsif ($esp eq "GotClick")
				{
            		$xlink=~s/a=$xlme_affiliate/a=15548/;
            		$xlink=~s/a=3219/a=15548/;
				}
				elsif ($esp eq "PACK1")
				{
            		$xlink=~s/a=$xlme_affiliate/a=$affiliateID/;
            		$xlink=~s/a=3219/a=$affiliateID/;
				}
				else
				{
            		$xlink=~s/a=$xlme_affiliate/a=15480/;
            		$xlink=~s/a=3219/a=15480/;
				}
			}
			else
			{
            	$xlink=~s/a=13/a=$affiliateID/;
			}
			$xlink=~s/$cake_domain/$esp_cake_domain/;
			#$xlink=~s/$xlme_cake_domain/$esp_cake_domain/;
        }
        else
        {
			$linkstr=~s/{{CID}}/$c1/;
			$linkstr=~s/{{FOOTER}}/{{FOOTER}}_${send_date}/;
			$linkstr=~s/s1=$oldsubAffiliateID/s1=$subAffiliateID/;
			if ($linkType eq "XLME")
			{
				$linkstr=~s/a=$xlme_affiliate/a=15480/;
				$linkstr=~s/a=3219/a=15480/;
			}
			else
			{
				$linkstr=~s/a=13/a=$affiliateID/;
			}
			$linkstr=~s/$cake_domain/$esp_cake_domain/;
			#$linkstr=~s/$xlme_cake_domain/$esp_cake_domain/;
			$tlink_id="";
			while (($tlink_id eq "") and ($linkstr ne ""))
			{
				$sql="select link_id from links where refurl=?";
				$sth=$dbhq->prepare($sql);
				$sth->execute($linkstr);
				if (($tlink_id)=$sth->fetchrow_array())
				{
				}
				else
				{
					$sql="insert ignore into links(refurl,date_added) values('$linkstr',now())";
					my $rows=$dbhu->do($sql);
				}
    			my $iret=util::checkLink($linkstr);
    			if ($iret)
    			{
    				$chklinkstr.="$aid,$tlink_id,$aname,$esp,$country\n";
    			}
			}
			if ($tlink_id eq "")
			{
				$tlink_id=0;
			}
			if ($newurl eq "Y")
			{
				$redir_random_str=util::get_random();
				$xlink="http://$redir_domain/z/$redir_random_str/$eidfield|1|$tlink_id|R";
			}
			elsif ($newurl eq "G")
			{
				$xlink=util::get_gmail_url("REDIRECT",$redir_domain,$eidfield,$tlink_id);
			}
			else
			{
				$xlink="http://$redir_domain/cgi-bin/redir1.cgi?eid=$eidfield&cid=1&em=$emailfield&id=$tlink_id&n=$clientid&f=$cfrom&s=$csubject&c=$cid&tid=$templateid&footerid=$footer_id&ctype=R";
			}
		}
		if ($espLabel eq "ZetaMail")
		{
			$xlink.="&zetablastid=%%BLASTID%%";
		}
		$html_code =~ s@{{URL$i}}@$xlink@g;
	}
	$i++;
}
my $addlink="http://{{DOMAIN}}/cgi-bin/add.cgi?mf=&fm=";
$html_code =~ s/{{Y_ADDADDR}}/$addlink/g;
$html_code =~ s/{{HEADER_TEXT}}//g;
$html_code =~ s/{{FOOTER_TEXT}}//g;
$html_code =~ s/{{URL}}/$xlink3/g;
if ($esp eq "PACK1")
{
   	$html_code =~ s/{{ADV_UNSUB_URL}}/\$\$adv_unsub_link\$\$/g;
}
else
{
   	$html_code =~ s/{{ADV_UNSUB_URL}}/$xlink1/g;
}
$html_code =~ s/{{FOOTER_STR}}//g;
$html_code =~ s/{{CID}}/1/g;
$html_code =~ s/{{CRID}}/$cid/g;
$html_code =~ s/{{FID}}//g;
$html_code =~ s/{{NID}}/$clientid/g;
$html_code =~ s/{{MID}}//g;
$html_code =~ s/{{LINK_ID}}/$link_id/g;
$html_code =~ s/{{F}}/$cfrom/g;
$html_code =~ s/{{S}}/$csubject/g;
$html_code =~ s/{{CWPROGID}}//g;
$html_code =~ s/{{HEADER}}//g;
$html_code =~ s/footerid={{FOOTER}}/footerid=$footer_id/g;
$html_code =~ s/{{FOOTER}}/$footer_str/g;
$html_code =~ s/{{UNSUB_URL}}/$unsublink/g;
$html_code =~ s/{{BINDING}}//g;
$html_code =~ s/{{TID}}/$templateid/g;
$html_code =~ s/{{EMAIL_ADDR}}/$emailfield/g;
$html_code =~ s/{{EMAIL_USER_ID}}/$eidfield/g;
if ($esp ne "PACK1")
{
	$creative_name=~s/ /-/g;
	$creative_name=~tr/a-zA-Z/\^/c;
	$creative_name=~s/\^//g;
}

my $file;
my $mtaRandom = App::Mail::MtaRandomization->new();
my $tmp_dir = "/data5/hitpath/$cid"; 
mkdir $tmp_dir;
$tmp_dir = "/data5/hitpath/$cid/images"; 
mkdir $tmp_dir;
$global_text=$html_code;
if ($one_image eq "Y")
{
	my $thtml=$html_code;
    $thtml=~s/{{DOMAIN}}/staging.affiliateimages.com/g;  
    $thtml=~s/{{IMG_DOMAIN}}/staging.affiliateimages.com/g;  
    $thtml=~s/^M//g;
    $thtml=~s/\\n//g;
    $thtml=~s/width=[\"\']*[0-9]+\%*[\"\']*//ig;
    $thtml=~s/"/\\"/g;
    my $cmd=`echo "$thtml" | /usr/bin/html2ps -o /tmp/$cid.ps 2>/dev/null;/usr/bin/convert -adjoin -size 600x600 /tmp/$cid.ps /data5/hitpath/$cid/images/t_$cid.jpg`;
    my $params={};
    my $tfile=$tmp_dir."/t_$cid.jpg";
    if (-e $tfile)
    {
    }
    else
    {
        my $tfile1=$tmp_dir."/t_".$cid."-0.jpg";
        rename($tfile1,$tfile);
    }
    my $tfile1=$tmp_dir."/t_".$cid."-1.jpg";
	unlink($tfile1);
    $tfile1=$tmp_dir."/t_".$cid."-2.jpg";
	unlink($tfile1);
}
else
{
	my $p = HTML::LinkExtor->new(\&cb);
	$p->parse($html_code);
	$html_code=$global_text;
	$html_code =~ s/{{IMG_DOMAIN}}/$content_domain/g;
	$html_code =~ s/{{DOMAIN}}/$redir_domain/g;
}
my $htmlfile;
my $txtfile;
if ($esp eq "PACK1")
{
	$htmlfile="/data5/hitpath/$cid/".$send_date."++".$aid."++".$aname."++".$cid."++".$creative_name."++".$cdate.".html";
	$txtfile="/data5/hitpath/$cid/".$send_date."++".$aid."++".$aname."++".$cid."++".$creative_name."++".$cdate.".txt";
}
else
{
	$htmlfile="/data5/hitpath/$cid/".$send_date."_".$aname."_".$creative_name."_".$cid."_".$link_id.".html";
	$txtfile="/data5/hitpath/$cid/".$send_date."_".$aname."_".$creative_name."_".$cid."_".$link_id.".txt";
}
open (OUT, "> $htmlfile");
print OUT "$html_code";
close(OUT);
my $f = HTML::FormatText::WithLinks->new(
        before_link => '',
        after_link => ' [%l]',
        unique_links => 1,
        footnone => '',
);
my $string=$f->parse($html_code);
$string=~s/\[IMAGE\]//g;
open(OUT,"> $txtfile");
print OUT "$string\n";
close(OUT);
open (OUT, "> /data5/hitpath/$cid/asset.txt");
print OUT "FROM: $from_str\n";
print OUT "SUBJECT: $subject_str\n";
print OUT "TEMPLATE: $template_name ($templateid)\n";
print OUT "CLIENT: $client_name\n";
print OUT "FOOTER: $footer_name\n";
close(OUT);
if ($esp eq "PACK1")
{
	my @args = ("/var/www/html/newcgi-bin/stuff_esp3.sh $cid $aid $send_date $cdate");
	system(@args) == 0 or die "system @args failed: $?";
	$file=$send_date."++".$aid."++".$cid."++".$cdate.".zip";
}
else
{
	$aname=~s/ /-/g;
	my @args = ("/var/www/html/newcgi-bin/stuff_esp.sh $cid $send_date $aname $redir_domain $templateid $subAffiliateID $c1");
	system(@args) == 0 or die "system @args failed: $?";
	$file=$subAffiliateID."_".$c1."_".$send_date."_".$aname."_".$redir_domain."_".$templateid.".zip";
}
if ($chklinkstr ne "")
{
	open (MAIL,"| /usr/sbin/sendmail -t");
	my $from_addr = "QA HHTP Redirect Alert <info\@zetainteractive.com>";
	print MAIL "From: $from_addr\n";
	print MAIL "To: dpezas\@zetainteractive.com,jhecht\@zetainteractive.com\n";
	print MAIL "Subject: QA HHTP Redirect Alert\n";
	my $date_str = $util->date(6,6);
	print MAIL "Date: $date_str\n";
	print MAIL "X-Priority: 1\n";
	print MAIL "X-MSMail-Priority: High\n";
	print MAIL "$chklinkstr\n";
	close MAIL;
}
print "Content-type: text/html\n\n";
print<<"end_of_html";
<html>

<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Export Images</title>
</head>

<body>
<b>Creative Name: </b>$creative_name<br>
<table>
<tr>
                                <td><b>Download:</b></td>
                                <td><b><a href="/hitpath/$file">$file</a></b></td>
                            </tr>
</table>
</body>
</html>
end_of_html
exit(0);

sub cb 
{
     my($tag, $url1, $url2, %links) = @_;
	my $query1;
	my $temp_id;
	my $sql;
	my $sth1;
	my $link_id;
	my $ext;
	my $scheme;
	my $auth;
	my $path;
	my $query1;
	my $frag;
	my $suffix;
	my $name;
	my $turl;

     if (($tag eq "img") or ($tag eq "background") or (($tag eq "img") and ($url1 eq "background")) or (($tag eq "input") and ($url1 eq "src")))
     {
		$turl=$url2;
        $_ = $url2;
        if ((/DOMAIN/) || (/IMG_DOMAIN/))
        {
          	$url2=~s/{{DOMAIN}}/staging.affiliateimages.com/g;  
          	$url2=~s/{{IMG_DOMAIN}}/staging.affiliateimages.com/g;  
        }
        #
        # Get directory and filename
        #
        ($scheme, $auth, $path, $query1, $frag) = uri_split($url2);
        ($name,$frag,$suffix) = fileparse($path);
		if ($name ne "open\.cgi")
		{
	        my $repl_url = $scheme . "://" . $auth . $frag;
			my $time_str = time();
	        if ($query1 ne "")
	        {
	        	$repl_url = $repl_url . $name . "?" . $query1;
	        }
	       	my $temp_str;
	        ($temp_str,$ext) = split('\.',$name);
			if ($ext eq "")
			{
				$name=$name.".jpg";
			}
	        my $curl = WWW::Curl::easy->new();
	        $curl->setopt(CURLOPT_NOPROGRESS, 1);
#	        $curl->setopt(CURLOPT_MUTE, 0);
	        $curl->setopt(CURLOPT_FOLLOWLOCATION, 1);
	        $curl->setopt(CURLOPT_TIMEOUT, 30);
	        open HEAD, ">/tmp/head.out";
	        $curl->setopt(CURLOPT_WRITEHEADER, *HEAD);
	        open BODY, "> /data5/hitpath/$cid/images/${name}";
	        $curl->setopt(CURLOPT_FILE,*BODY);
	        $curl->setopt(CURLOPT_URL, $url2);
	        my $retcode=$curl->perform();
	        if ($retcode == 0)
	        {
                my $response_code = $curl->getInfo(CURLINFO_HTTP_CODE);
				my $info = $curl->getinfo(CURLINFO_CONTENT_TYPE);
                # judge result and next action based on $response_code
	        }
	        else
	        {
	        }
	        close HEAD;
			my $tname=$name;
			my $file="/data5/hitpath/$cid/images/$name";
			my $type_from_file = $ft->checktype_filename($file);
			$_=$type_from_file;
			if (/gif/)
			{
				$tname=~s/.jpg/.gif/;
			}
			elsif (/x-png/)
			{
				$tname=~s/.jpg/.png/;
			}
			elsif (/x-bmp/)
			{
				$tname=~s/.jpg/.bmp/;
			}
			if ($name ne $tname)
			{
                my $outfile="/data5/hitpath/$cid/images/$tname";
                my @args = ("/var/www/html/newcgi-bin/rename.sh \"$file\" \"$outfile\"");
                system(@args) == 0 or die "system @args failed: $?";
			}
			if (($esp eq "BHIMG") or ($esp eq "BH001")
				or ($esp eq "BH002")
				or ($esp eq "BH003")
				or ($esp eq "BH004")
				or ($esp eq "BH005")
				or ($esp eq "BH006")
				or ($esp eq "BH007")
				or ($esp eq "BH008")
				or ($esp eq "BH009")
				or ($esp eq "BH010")
				or ($esp eq "BH011")
				or ($esp eq "BH012")
				or ($esp eq "BH013")
				or ($esp eq "BH014")
				or ($esp eq "BH015"))
			{
				$global_text=~s/$turl/http:\/\/${content_domain}\/$tname/g;
			}
			else
			{
				$global_text=~s/$turl/images\/$tname/g;
			}
		}
	}
}

sub genImage
{
	my ($name)=@_;
	my @EXT=(".png",".bmp",".gif",".jpg");
	my @CHARS=("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
	my $new_name;

	my $range=$#EXT-1;
	my $ind=int(rand($range));
	$range=$#CHARS-1;
	my $cind=int(rand($range));
    my $params = { 'minimum'   => 4, 'range'     => 8,'letters' =>0,'uppercase' => 0 };
    my $random_string= $mtaRandom->generateRandomString($params);
	$random_string=~tr/A-Z/a-z/;
    my $random_string1= $mtaRandom->generateRandomString($params);
	$random_string1=~tr/A-Z/a-z/;
	$new_name=$random_string.$CHARS[$cind].$name.$CHARS[$cind].$random_string1.$EXT[$ind];
	return $new_name;
}
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
			return;
		}
		elsif (/ccID/)
		{
			$url2 =~ s/\?/\\?/g;
			$url2=~ s/\[/\\[/g;
			my $end=index($url2,"&ccID=");
			my $ccID=substr($url2,$end);
			$ccID=~s/&ccID=//;
			if ($esp eq "PACK1")
			{
            	$global_text =~ s/"$url2"/"http:\/\/$redir_domain\/?a=$affiliateID&c=$ccID&s1=&s5=SSoptSS"/gi;
            	$global_text =~ s/$url2/"http:\/\/$redir_domain\/?a=$affiliateID&c=$ccID&s1=&s5=SSoptSS"/gi;
			}
			else
			{
            	$global_text =~ s/"$url2"/"http:\/\/$redir_domain\/?a=$affiliateID&c=$ccID&s1="/gi;
            	$global_text =~ s/$url2/"http:\/\/$redir_domain\/?a=$affiliateID&c=$ccID&s1="/gi;
			}
		}
	 }
}
sub cb2
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
			return;
		}
		elsif (/ccID/)
		{
			$url2 =~ s/\?/\\?/g;
			$url2=~ s/\[/\\[/g;
			my $end=index($url2,"&ccID=");
			my $ccID=substr($url2,$end);
			$ccID=~s/&ccID=//;
			my $turl="http://$esp_cake_domain/?a=$affiliateID&c=$ccID&s1=".$global_subAffiliateID."&s2={{EMAIL_USER_ID}}_".$cid."_".$cfrom."_".$csubject."_".$templateid."&s4=$c1&s5=0_0_0_0_".$send_date;
			if ($offer_type eq "CPC")
			{
				$turl.="&p=c";
			}
			my $tlink_id="";
			while (($tlink_id eq "") and ($turl ne ""))
			{
				$sql="select link_id from links where refurl=?";
				$sth=$dbhq->prepare($sql);
				$sth->execute($turl);
				if (($tlink_id)=$sth->fetchrow_array())
				{
				}
				else
				{
					$sql="insert ignore into links(refurl,date_added) values('$turl',now())";
					my $rows=$dbhu->do($sql);
				}
    			my $iret=util::checkLink($turl);
    			if ($iret)
    			{
    				$chklinkstr.="$aid,$tlink_id,$aname,$esp,$country\n";
    			}
			}
			my $newlink;
			if ($newurl eq "G")
			{
				$newlink=util::get_gmail_url("REDIRECT",$redir_domain,$eidfield,$tlink_id);
			}
			else
			{
				my $redir_random_str=util::get_random();
				$newlink="http://$redir_domain/z/$redir_random_str/$eidfield|1|$tlink_id|R";
			}

           	$global_text =~ s/"$url2"/"$newlink"/gi;
           	$global_text =~ s/$url2/"$newlink"/gi;
		}
	 }
}
sub cb3 
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
			return;
		}
		else
		{
			$url2 =~ s/\?/\\?/g;
			$url2=~ s/\[/\\[/g;
			my $end=index($url2,"&id=");
			my $ID=substr($url2,$end);
			$ID=~s/&id=//;
			my $refurl;
			my $sql="select refurl from links where link_id=?"; 
			my $sth=$dbhu->prepare($sql);
			$sth->execute($ID);
			($refurl)=$sth->fetchrow_array();
			$sth->finish();
           	$global_text =~ s/"$url2"/"$refurl"/gi;
           	$global_text =~ s/$url2/"$refurl"/gi;
		}
	 }
}
