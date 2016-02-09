#!/usr/bin/perl

# ******************************************************************************
# expertsender_creative.cgi
#
# History
# ******************************************************************************

# include Perl Modules

use strict;
use Net::FTP;
use CGI;
use util;
use App::Mail::MtaRandomization;
use HTML::LinkExtor;
use MIME::Base64;
use WWW::Curl::easy;
use URI::Escape;
use URI::Split qw(uri_split uri_join);
use File::Basename;

# get some objects to use later
my $util = util->new;
my $query = CGI->new;
my $sth;
my $sth1;
my $sql;
my ($aname,$temp_id,$unsub_url,$unsub_img,$advertiser_url,$unsub_use,$unsub_text);
my $linkType;
my $offer_type;
my $supp_name;
my $html_code;
my $master_str;
my $global_text;
my $global_subAffiliateID;
my $camp_cnt;
my $IMG;
my $cid;
my $sent_cnt;
my $creative_name;
my $aflag;
my $oflag;
my $mtaRandom;
my $tracking_str;
my $curtime;
my $bid;
my $c1="whatcounts";
my $cdate;
my $espLabel;
my $global_domain;
my $global_text;
my $rname;
my $remail;
my $linkstr;
my $img_prefix;
my $advertiser_unsub_id;
my $subAffiliateID;
my $oldsubAffiliateID;

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
my $creative= $query->param('creative');
my $csubject= $query->param('csubject');
my $cfrom= $query->param('cfrom');
my $esp= $query->param('esp');
if (($aid eq "") or ($creative eq "") or ($csubject eq "") or ($cfrom eq ""))
{
	print "Content-Type: text/html\n\n";
	print<<"end_of_html";
<html>
<body>
<center><h3>One or more of advertiser, creative, subject, or from line not selected.</h3>
<br>
<a href="/cgi-bin/expertsender_main.cgi?esp=$esp" target=_top>Back</a>
</center>
</body>
</html>
end_of_html
	exit();
}
my $clientid= $query->param('clientid');
my $templateid= $query->param('templateid');
my $redir_domain=$query->param('redir_domain');
my $content_domain=$redir_domain;
my $send_date = $query->param('send_date');
my $footer_id= $query->param('footer_id');
my $affiliateID= $query->param('AffiliateID');
my $cakeDomain= $query->param('cakeDomain');
my $newurl = $query->param('newurl');
my $one_image = $query->param('one_image');
if ($newurl eq "")
{
	$newurl="N";
}
my $redir_random_str="";
my $emailfield;
my $eidfield;
my $espID;
$c1=$esp;
$sql="select espID,espLabel,eidField,emailField from ESP where espName='$esp'";
$sth=$dbhu->prepare($sql);
$sth->execute();
($espID,$espLabel,$eidfield,$emailfield)=$sth->fetchrow_array();
$sth->finish();
#
#	Get information about the advertiser 
#
$sql = " select advertiser_name,vendor_supp_list_id,unsub_link,unsub_image,advertiser_url,unsub_use,unsub_text,curdate(),linkType,offer_type from advertiser_info where advertiser_id=?"; 
$sth = $dbhq->prepare($sql);
$sth->execute($aid);
($aname,$temp_id,$unsub_url,$unsub_img,$advertiser_url,$unsub_use,$unsub_text,$cdate,$linkType,$offer_type)=$sth->fetchrow_array();
$sth->finish();
#
if ($offer_type eq "CPM")
{
	$esp_cake_domain=$esp_cpm_cake_domain;
}
if ($espLabel eq "AlphaS")
{
	$esp_cake_domain="gravitypresence.com";
	$redir_domain="gravitypresence.com";
	$affiliateID=417;
}
elsif ($espLabel eq "AlphaD")
{
	$esp_cake_domain="i.soltrail.com";
	$redir_domain="i.soltrail.com";
	$affiliateID=765;
}
elsif ($espLabel eq "AlphaHYD")
{
	$esp_cake_domain=$cakeDomain;
	$redir_domain=$cakeDomain;
}
elsif ($espLabel eq "GotClick")
{
	$esp_cake_domain="quickpixel.net";
	$redir_domain="quickpixel.net";
	$affiliateID=488;
}
if ($send_date eq "")
{
	$send_date=$cdate;
}
my $exflag;
$sql="select substr(exclude_days,dayofweek(date_add('$send_date',interval 6 day)),1) from advertiser_info where advertiser_id=?";;
$sth=$dbhu->prepare($sql);
$sth->execute($aid);
($exflag)=$sth->fetchrow_array();
$sth->finish();
if ($exflag eq "Y")
{
    print "Content-type: text/html\n\n";
    print<<"end_of_html";
<html><head><title>Error</title></head>
<body>
<h3>Error: Date Scheduled is an Excluded Day for $aname<br>Please Select Non-Excluded Day to Generate.</h3> 
</body>
</html>
end_of_html
exit();
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
}
if (($espLabel eq "AlphaS") or ($espLabel eq "GotClick") or ($espLabel eq "AlphaD"))
{
	$subAffiliateID=$query->param('subAffiliateID');
}
else
{
	$sql="start transaction";
	my $rows=$dbhu->prepare($sql);
	$sql="select parmval from sysparm where parmkey='ESP_CAMPAIGNID' for update";
	$sth=$dbhu->prepare($sql);
	$sth->execute();
	($subAffiliateID)=$sth->fetchrow_array();
	$sth->finish();
	$subAffiliateID++;
	$sql="update sysparm set parmval='$subAffiliateID' where parmkey='ESP_CAMPAIGNID'";
	$rows=$dbhu->do($sql);
	$sql="commit";
	$rows=$dbhu->do($sql);
}
#
$sql="insert ignore into EspAdvertiserJoin(subAffiliateID,advertiserID,espID,creativeID,subjectID,fromID,sendDate) values($subAffiliateID,$aid,$espID,$creative,$csubject,$cfrom,'$send_date')";
my $rows=$dbhu->do($sql);
#
$sql="select cakeSubAffiliateID from user where user_id=?"; 
$sth=$dbhu->prepare($sql);
$sth->execute($clientid);
($oldsubAffiliateID)=$sth->fetchrow_array();
$sth->finish();

my $link_id;
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
}
if ($link_id eq "")
{
	$link_id=0;
}
}
my $xlink3;
my $xlink1;
my $unsublink=qq|http://$redir_domain/cgi-bin/redir1.cgi?eid=$eidfield&cid=1&em=$emailfield&id=42&n=$clientid&f=$cfrom&s=$csubject&c=$creative&tid=$templateid&footerid=$footer_id&ctype=U|;
if (($newurl eq "Y") or ($newurl eq "G"))
{
	$redir_random_str=util::get_random();
	$unsublink=qq^http://$redir_domain/z/$redir_random_str/$eidfield|1|42|U^;
}
if (($esp eq "ALP001") or ($esp eq "GotClick") or ($esp eq "ALP002"))
{
	my $end=index($linkstr,"&s2=");
	$xlink3=substr($linkstr,0,$end);
	$xlink1=$unsub_url;
	$unsublink=qq|http://$redir_domain/cgi-bin/unsubscribe.cgi|;
}
elsif ($esp eq "PACK1")
{
	my $end=index($linkstr,"&s2=");
	$xlink3=substr($linkstr,0,$end);
	$xlink1=$unsub_url;
	$unsublink=qq|http://$redir_domain/cgi-bin/unsubscribe.cgi|;
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
		$xlink3="http://$redir_domain/cgi-bin/redir1.cgi?eid=$eidfield&cid=1&em=$emailfield&id=$link_id&n=$clientid&f=$cfrom&s=$csubject&c=$creative&tid=$templateid&footerid=$footer_id&ctype=R";
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
		$xlink1="http://$redir_domain/cgi-bin/redir1.cgi?eid=$eidfield&cid=1&em=$emailfield&id=$advertiser_unsub_id&n=$clientid&f=$cfrom&s=$csubject&c=$creative&tid=$templateid&footerid=$footer_id&ctype=A";
		if ($espLabel eq "ZetaMail")
		{
			$xlink1.="&zetablastid=%%BLASTID%%";
		}
	}
}


my $random_string=util::get_random();
my $random_string1=util::get_random();
$img_prefix=$content_domain."/".$random_string."/".$random_string1;

$sql="select html_code from brand_template where template_id=?";
$sth = $dbhq->prepare($sql);
$sth->execute($templateid);
($master_str)=$sth->fetchrow_array();
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
print "Content-type: text/html\n\n";
print<<"end_of_html";
<html>

<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>$espLabel Creative</title>
</head>

<body>

<table cellSpacing="0" cellPadding="0" align="left" bgColor="#ffffff" border="0" id="table2">
	<tr vAlign="top">
		<td noWrap align="left">
		<table cellSpacing="0" cellPadding="0" width="800" border="0" id="table3">
			<tr>
				<td width="248" bgColor="#ffffff" rowSpan="2">&nbsp;</td>
				<td width="328" bgColor="#ffffff">&nbsp;</td>
			</tr>
			<tr>
				<td width="468">
				<table cellSpacing="0" cellPadding="0" width="100%" border="0" id="table4">
					<tr>
						<td align="left"><b><font face="Arial" size="2">&nbsp;$espLabel Creative</font></b></td>
					</tr>
					<tr>
						<td align="right"><b>
						<a style="TEXT-DECORATION: none" href="/cgi-bin/logout.cgi">
						<font face="Arial" color="#509c10" size="2">Logout</font></a>&nbsp;&nbsp;&nbsp;
						</td>
					</tr>
				</table>
				</td>
			</tr>
		</table>
		<table cellSpacing="0" cellPadding="10" width="100%" bgColor="#ffffff" border="0" id="table5">
			<tr>
				<td vAlign="top" align="left" bgColor="#ffffff">
				<table cellSpacing="0" cellPadding="0" width="660" bgColor="#ffffff" border="0" id="table6">
					<tr>
						<td vAlign="center" align="left">
						<font face="verdana,arial,helvetica,sans se
rif" color="#509c10" size="3"><b>$aname - $subAffiliateID</b></font><table border="0" width="100%" id="table9">
							<tr>
								<td><b>
													Advertiser</b></td>
								<td>
													<b>$aname</b></td>
							</tr>
							<!-- <tr>
								<td><b>Suppression:</b></td>
								<td><b>
								<a href="http://www.aspiremail.com/supps/$supp_name.zip">
								http://www.aspiremail.com/supps/$supp_name.zip</a> </b></td>
							</tr> -->
						</table>
						</td>
					</tr>
					<tr>
						<td>
						<img height="3" src="/images/spacer.gif"></td>
					</tr>
				</table>
				<table cellSpacing="0" cellPadding="0" width="660" bgColor="#ffffff" border="0" id="table7">
					<tr>
						<td colSpan="10">
						&nbsp;</td>
					</tr>
				</table>
					<b><u>Subject Line(s):</u></b>
end_of_html
my $subject_str;
my $sid;
#$sql="select subject_id,advertiser_subject from advertiser_subject where advertiser_subject.status='A' and advertiser_subject.advertiser_id=$aid and ((approved_flag='Y' and date_approved < date_sub(now(),interval 24 hour)) or (approved_flag='Y' and approved_by != 'SpireVision') or (original_flag='Y')) order by advertiser_subject desc"; 
$sql="select subject_id,advertiser_subject from advertiser_subject where subject_id=$csubject"; 
$sth = $dbhq->prepare($sql);
$sth->execute();
if (($sid,$subject_str)=$sth->fetchrow_array())
{
	print "<br>$subject_str ($sid)\n";
}
$sth->finish();
print<<"end_of_html";
					<br><br>
					<b><u>From Line(s):</u></b>
end_of_html
my $from_str;
#$sql="select from_id,advertiser_from from advertiser_from where advertiser_from.advertiser_id=$aid and advertiser_from != '{{FOOTER_SUBDOMAIN}}' and status='A' and ((approved_flag='Y' and date_approved < date_sub(now(),interval 24 hour)) or (approved_flag='Y' and approved_by != 'SpireVision') or (original_flag='Y')) order by advertiser_from";
$sql="select from_id,advertiser_from from advertiser_from where from_id=$cfrom"; 
$sth = $dbhq->prepare($sql);
$sth->execute();
if (($sid,$from_str)=$sth->fetchrow_array())
{
	print "<br>$from_str ($sid)\n";
}
$sth->finish();
print<<"end_of_html";
					<br><br>
end_of_html
my $reccnt;
$sql="select creative_id from creative where creative_id=$creative"; 
$sth =$dbhq->prepare($sql);
$sth->execute();
my ($copen,$cclick,$cindex);
my $click_cnt;
my $open_cnt;
my $thumbnail;
my $mflag;
$camp_cnt = 1;
$sql="create temporary table hitcreative (creative_id int(11) unsigned primary key,open_cnt int(11) unsigned,cclick float,click_cnt int(11) unsigned,sent_cnt int(11) unsigned)";
my $rows=$dbhu->do($sql);
while (($cid)=$sth->fetchrow_array())
{

	$sql = "select open_cnt,(((click_cnt)/open_cnt)*100),click_cnt,sent_cnt from creative_stat where creative_id=?"; 
	$sth1 = $dbhu->prepare($sql) ;
	$sth1->execute($cid);
	($open_cnt,$cclick,$click_cnt,$sent_cnt) = $sth1->fetchrow_array();
	$sth1->finish();
    if ($open_cnt eq "")
    {
    	$open_cnt=0;
    }
    if ($cclick eq "")
    {
    	$cclick = "0.00";
    }
    if ($click_cnt eq "")
    {
    	$click_cnt=0;
    }
    if ($sent_cnt eq "")
    {
    	$sent_cnt=0;
    }

	$sql="insert into hitcreative(creative_id,open_cnt,cclick,click_cnt,sent_cnt) values($cid,$open_cnt,$cclick,$click_cnt,$sent_cnt)";
	my $rows=$dbhu->do($sql);
}
$sth->finish();
$sql="select creative_id,open_cnt,cclick,click_cnt,sent_cnt from hitcreative order by cclick desc";
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($cid,$open_cnt,$cclick,$click_cnt,$sent_cnt)=$sth->fetchrow_array())
{
	$sql="select creative.creative_name,thumbnail,approved_flag,original_flag,html_code from creative where creative_id=?";
    $sth1 = $dbhq->prepare($sql) ;
    $sth1->execute($cid);
	($creative_name,$thumbnail,$aflag,$oflag,$html_code)=$sth1->fetchrow_array();
	$sth1->finish();
	$copen="";
	$cindex="";
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
		elsif (($esp eq "GotClick") or ($esp eq "ALP001") or ($esp eq "ALP002") or ($esp eq "PACK1"))
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
	if ($sent_cnt > 0)
	{
		my $temp=($open_cnt/$sent_cnt*100);
		$copen=sprintf("%5.2f",$temp);
		$temp=($click_cnt/$sent_cnt)*100000;
		$cindex=sprintf("%5.2f",$temp);
	}
	if ($copen eq "")
	{
		$copen = "0.00";
	}
	if ($cclick eq "")
	{
		$cclick = "0.00";
	}
	if ($cindex eq "")
	{
		$cindex = "0";
	}

	my $temp_str="";
    if ($oflag eq "Y")
    {
        $temp_str = $temp_str . "O ";
    }
    else
    {
        $temp_str = $temp_str . "A ";
    }
    if ($aflag eq "Y")
    {
        $temp_str = $temp_str . ")";
    }
    else
    {
        $temp_str = $temp_str . "- NA!)";
    }
	print "<b><u>Creative $camp_cnt: $creative_name ($cid) ($copen% Open - $cclick% Click - $cindex Index - $temp_str</u></b>&nbsp;&nbsp;&nbsp;<br>\n";
print<<"end_of_html";
	<font face="Tahoma">
					<img src="http://www.${content_domain}/images/thumbnail/$thumbnail" border="0"><br>
					<u><b>Thumbnail Image:</b></u><b> http://www.${content_domain}/images/thumbnail/$thumbnail</b><u><br>
					</u><b> 
end_of_html
	#
	# Make changes to html_code
	#
    my $regExpCreativeHtml  = qr{</*(html|body)>}i;
    $html_code=~ s/$regExpCreativeHtml//g;

	my $t_html=$master_str;
    $t_html=~s/{{CREATIVE}}/$html_code/g;
	$html_code=$t_html;
    $html_code =~ s/{{TRACKING}}/<IMG SRC="http:\/\/$redir_domain\/cgi-bin\/open.cgi?eid=$eidfield&cid=1&em=$emailfield&n=$clientid&f=$cfrom&s=$csubject&c=$creative&did=&binding=&tid=$templateid&openflag=1&nod=1&espID=$espID&subaff=$subAffiliateID" border=0 height=1 width=1>/g;
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
	if ($html_code =~ /{{FOOTER}}/)
	{
		if ($footer_id > 0)
		{
			$sql="select footer_code from Footers where footer_id=$footer_id";
			$sth = $dbhq->prepare($sql);
			$sth->execute();
			($footer_str)=$sth->fetchrow_array();
			$sth->finish();
		}
	}
open(LOG,">/tmp/j.j");
print LOG "<$footer_id> <$footer_str>\n";
close(LOG);
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
			if ($esp eq "ALP001")
			{
				my $end=index($linkstr,"&s2=");
				$xlink=substr($linkstr,0,$end);
				if ($linkType eq "XLME")
				{
					$xlink=~s/a=$xlme_affiliate/a=15445/;
					$xlink=~s/a=3219/a=15445/;
				}
				else
				{
					$xlink=~s/a=13/a=$affiliateID/;
				}
			}
			elsif ($esp eq "ALP002")
			{
				my $end=index($linkstr,"&s2=");
				$xlink=substr($linkstr,0,$end);
				if ($linkType eq "XLME")
				{
					$xlink=~s/a=$xlme_affiliate/a=$affiliateID/;
					$xlink=~s/a=3219/a=$affiliateID/;
				}
				else
				{
					$xlink=~s/a=13/a=$affiliateID/;
				}
			}
			elsif ($esp eq "GotClick")
			{
				my $end=index($linkstr,"&s2=");
				$xlink=substr($linkstr,0,$end);
				if ($linkType eq "XLME")
				{
					$xlink=~s/a=$xlme_affiliate/a=15548/;
					$xlink=~s/a=3219/a=15548/;
				}
				else
				{
					$xlink=~s/a=13/a=$affiliateID/;
				}
				$xlink=~s/$cake_domain/$esp_cake_domain/;
			}
			elsif ($esp eq "PACK1")
			{
				my $end=index($linkstr,"&s2=");
				$xlink=substr($linkstr,0,$end);
				if ($linkType eq "XLME")
				{
					$xlink=~s/a=$xlme_affiliate/a=$affiliateID/;
					$xlink=~s/a=3219/a=$affiliateID/;
				}
				else
				{
					$xlink=~s/a=13/a=$affiliateID/;
				}
				$xlink=~s/$cake_domain/$esp_cake_domain/;
			}
			else
			{
				$linkstr=~s/{{CID}}/$c1/;
				$linkstr=~s/{{FOOTER}}/{{FOOTER}}_${send_date}/;
				$linkstr=~s/s1=$oldsubAffiliateID/s1=$subAffiliateID/;
				$linkstr=~s/$cake_domain/$esp_cake_domain/;
				#$linkstr=~s/$xlme_cake_domain/$esp_cake_domain/;
				if ($linkType eq "XLME")
				{
					$linkstr=~s/a=$xlme_affiliate/a=15480/;
					$linkstr=~s/a=3219/a=15480/;
				}
				else
				{
					$linkstr=~s/a=13/a=$affiliateID/;
				}
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
					$xlink="http://$redir_domain/cgi-bin/redir1.cgi?eid=$eidfield&cid=1&em=$emailfield&id=$tlink_id&n=$clientid&f=$cfrom&s=$csubject&c=$creative&tid=$templateid&footerid=$footer_id&ctype=R";
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
    if ($espLabel eq "Campaigner")
    {
		$mtaRandom = App::Mail::MtaRandomization->new();
		$global_domain=$redir_domain;
        $global_text = $html_code;
        my $p = HTML::LinkExtor->new(\&cb);
        $p->parse($html_code);
        $html_code= $global_text;
    }
	my $addlink="http://{{DOMAIN}}/cgi-bin/add.cgi?mf=&fm=";
	$html_code =~ s/{{Y_ADDADDR}}/$addlink/g;
	$html_code =~ s/{{HEADER_TEXT}}//g;
	$html_code =~ s/{{FOOTER_TEXT}}//g;
    $html_code =~ s/http:\/\/{{URL}}/$xlink3/g;
    $html_code =~ s/{{URL}}/$xlink3/g;
	if ($esp eq "PACK1")
	{
    	$html_code =~ s/{{ADV_UNSUB_URL}}/\$\$adv_unsub_link\$\$/g;
	}
	else
	{
    	$html_code =~ s/{{ADV_UNSUB_URL}}/$xlink1/g;
	}
    $html_code =~ s/{{BINDING_ID}}//g;
	$html_code =~ s/{{FOOTER_STR}}//g;
	$html_code =~ s/{{CID}}/1/g;
	$html_code =~ s/{{CRID}}/$creative/g;
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
	$html_code =~ s/{{IMG_DOMAIN}}/$content_domain/g;
	$html_code =~ s/{{DOMAIN}}/$redir_domain/g;
	print "<u>Creative:</u><br>";
$html_code=CGI::escapeHTML($html_code);
	print "<b><textarea name=html_code$camp_cnt rows=15 cols=100>$html_code</textarea><br>\n";
print<<"end_of_html";
<form method=post action="/cgi-bin/esp_camp_preview.cgi" target=_blank>
<input type=hidden name=campaign_id valud=$cid>
<input type=hidden name=c value="$html_code">
<input type=hidden name=csubject value="$subject_str">
<input type=hidden name=cfrom value="$from_str">
<input type=image height="22" src="/images/preview_rev.gif" width="81" border="0"><br>
</form>
<form method=post action=expertsender_export.cgi target=_blank>
<input type=hidden name=aid value=$aid>
<input type=hidden name=creative value="$creative">
<input type=hidden name=cfrom value="$cfrom">
<input type=hidden name=csubject value="$csubject">
<input type=hidden name=clientid value=$clientid>
<input type=hidden name=templateid value=$templateid>
<input type=hidden name=redir_domain value=$redir_domain>
<input type=hidden name=content_domain value=$content_domain>
<input type=hidden name=esp value=$esp>
<input type=hidden name=footer_id value=$footer_id>
<input type=hidden name=send_date value="$send_date">
<input type=hidden name=subAffiliateID value="$subAffiliateID">
<input type=hidden name=AffiliateID value="$affiliateID">
<input type=hidden name=cakeDomain value="$cakeDomain">
<input type=hidden name=newurl value="$newurl">
<input type=hidden name=one_image value="$one_image">
<input type=submit value="Create Package">
</form>

end_of_html
	$creative_name=~s/ /_/g;
	$creative_name=~tr/a-zA-Z/\^/c;
	$creative_name=~s/\^//g;
	$camp_cnt++;
}
$sth->finish();
#$sql="drop table hitcreative";
#my $rows=$dbhu->do($sql);
print<<"end_of_html";
					</b><br>
					&nbsp;<table id="table8" cellPadding="5" width="66%" bgColor="white">
						<tr>
							<td align="middle" width="47%">
							<a href="/cgi-bin/expertsender_top.cgi?aid=$aid&clientid=$clientid&templateid=$templateid&esp=$esp">
							<img height="22" src="/images/home_blkline.gif" width="81" border="0"></a></td>
							<td align="middle" width="50%">
							&nbsp;</td>
						</tr>
					</table>
					</b>
				</td>
			</tr>
		</table>
		</td>
	</tr>
</table>
</body>
</html>
end_of_html
exit(0);

sub encodeCleanBase64
{
    my ($value) = @_;

    my $encodedValue   = encode_base64($value, '');

    $encodedValue   =~ s/=//go;

    return($encodedValue);
}

sub cb 
{
     my($tag, $url1, $url2, %links) = @_;
	my $query1;
	my $temp_id;
	my $sql;
	my $sth1;
	my $link_id;
	my $ext;
	my $cdir;
	my $new_name;

     if (($tag eq "img") or ($tag eq "background") or ($url1 eq "background") or (($tag eq "input") and ($url1 eq "src")))
     {
        $_ = $url2;
        if (/IMG_DOMAIN/)
        {
            #
            # Get directory and filename
            #
            my ($scheme, $auth, $path, $query1, $frag) = uri_split($url2);
            my ($name,$frag,$suffix) = fileparse($path);
            my $repl_url = $scheme . "://" . $auth . $frag;
            if ($query1 ne "")
            {
                $repl_url = $repl_url . $name . "?" . $query1;
            }
            else
            {
                my $temp_str;
            }
			$new_name=genImage($name);
           	$repl_url =~ s/\?/\\?/g;
           	$repl_url =~ s/\&/\\&/g;
           	$repl_url =~ s/\(/\\(/g;
           	$repl_url =~ s/\)/\\)/g;
			if ($query1 eq "")
			{
           		$global_text =~ s/$repl_url${name}/http:\/\/$global_domain\/images\/$new_name/gi;
			}
			else
			{
           		$global_text =~ s/$repl_url/http:\/\/$global_domain\/images\/$new_name/gi;
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
			my $turl="http://$esp_cake_domain/?a=$affiliateID&c=$ccID&s1=".$global_subAffiliateID."&s2={{EMAIL_USER_ID}}_".$creative."_".$cfrom."_".$csubject."_".$templateid."&s4=$c1&s5=0_0_0_0_".$send_date;
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
			}
			my $newlink;
			if ($newurl eq "Y")
			{
				my $redir_random_str=util::get_random();
				$newlink="http://$redir_domain/z/$redir_random_str/$eidfield|1|$tlink_id|R";
			}
			elsif ($newurl eq "G")
			{
				$newlink=util::get_gmail_url("REDIRECT",$redir_domain,$eidfield,$tlink_id);
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
