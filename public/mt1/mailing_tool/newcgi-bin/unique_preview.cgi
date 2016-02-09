#!/usr/bin/perl

# ******************************************************************************
# unique_preview.cgi
#
# this page presents the user with a preview of the unique deal 
#
# History
# Jim Sobeck, 05/20/08, Creation 
# ******************************************************************************

# include Perl Modules

use strict;
use lib "/usr/lib/perl5/site_perl/production";
use App::Mail::MtaRandomization;
use util_mail;
use util;

my $util = util->new;
my $query = CGI->new;
my $dbh;
my $sth;
my $sth1;
my $sth2;
my $nid;
my $unsub_img;
my $unsub_url;
my $privacy_img;
my $brand_unsub;
my $cunsub_img;
my $rows;
my $sql;
my $uid = $query->param('uid');
my $format = "H";
my $images = $util->get_images_url;
my $template_id;
my $aid;
my $brand_id;
my $content_html;
my $mailing_domain;
my $creative_id;
my $client_id;
my ($cwc3,$cwcid,$cwprogid,$cr,$landing_page);
my $bname;
my $addr1;
my $addr2;
my $nl_id;
my $subject_id;
my $from_id;
my $headerid;
my $email_addr;
my $tstr;
my $include_wiki;
my $header_id;
my $footer_id;

my $mtaRandom = App::Mail::MtaRandomization->new();
# connect to the util database
my ($dbhq,$dbhu)=$util->get_dbh();

my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}

$sql="select mailing_template,advertiser_id,nl_id,mailing_domain,creative_id,subject_id,from_id,templateID,email_addr,include_wiki,header_id,footer_id from unique_campaign where unq_id=$uid";
$sth=$dbhu->prepare($sql);
$sth->execute();
($template_id,$aid,$nl_id,$mailing_domain,$creative_id,$subject_id,$from_id,$headerid,$email_addr,$include_wiki,$header_id,$footer_id)=$sth->fetchrow_array();
$sth->finish();
my $rest_str;
($tstr,$rest_str)=split(',',$email_addr);
#
$sql="select brand_id from client_brand_info where client_id=64 and status='A' and nl_id=$nl_id"; 
$sth = $dbhq->prepare($sql) ;
$sth->execute();
($brand_id) = $sth->fetchrow_array();
$sth->finish();
#
$sql="select html_code from brand_template where template_id=$template_id";
$sth = $dbhq->prepare($sql) ;
$sth->execute();
($content_html) = $sth->fetchrow_array();
$sth->finish();
$content_html = $mtaRandom->templateReplacement({'text' => $content_html});

my $header_str="";
if ($header_id > 0)
{
	$sql="select header_code from Headers where header_id=$header_id";
	$sth=$dbhu->prepare($sql);
	$sth->execute();
	($header_str)=$sth->fetchrow_array();
	$sth->finish();
}
$content_html=~s/{{HEADER}}/$header_str/g;
my $footer_str="";
if ($footer_id > 0)
{
	$sql="select footer_code from Footers where footer_id=$footer_id";
	$sth=$dbhu->prepare($sql);
	$sth->execute();
	($footer_str)=$sth->fetchrow_array();
	$sth->finish();
}
$content_html=~s/{{FOOTER}}/$footer_str/g;

my $image_domain;
my $mail_domain;
if ($mailing_domain eq "ALL")
{
	$sql="select url from brand_url_info where brand_id=$brand_id and url_type='O'";
	$sth = $dbhq->prepare($sql) ;
	$sth->execute();
	($image_domain) = $sth->fetchrow_array();
	$sth->finish();
}
else
{
	$image_domain=$mailing_domain;
}
$mail_domain = $image_domain; 

$sql="select privacy_img,unsub_img,client_id,brand_name,mailing_addr1,mailing_addr2 from client_brand_info where brand_id=$brand_id";
$sth = $dbhq->prepare($sql);
$sth->execute();
($privacy_img,$brand_unsub,$nid,$bname,$addr1,$addr2) = $sth->fetchrow_array();
$sth->finish();

$content_html=~ s/{{BRAND_PRIVACY}}/<a href="http:\/\/{{FOOTER_SUBDOMAIN}}.{{DOMAIN}}\/cgi-bin\/redir1.cgi?eid=0&amp;cid={{CID}}&amp;em=&amp;id=51&amp;nid=$nid" target=_blank><img src="http:\/\/www.{{IMG_DOMAIN}}\/fimg\/${brand_id}_2.jpg" border=0>/g;
$content_html=~ s/{{PRIVACY_URL}}/http:\/\/{{FOOTER_SUBDOMAIN}}.{{DOMAIN}}\/cgi-bin\/redir1.cgi?eid=0&amp;cid={{CID}}&amp;em=&amp;id=51&amp;nid=$nid/g;
$content_html=~ s/{{BRAND_UNSUB}}/<a href="http:\/\/{{FOOTER_SUBDOMAIN}}.{{DOMAIN}}\/cgi-bin\/redir1.cgi?eid=0&amp;cid={{CID}}&amp;em=&amp;id=42&amp;nid=$nid" target=_blank><img src="http:\/\/www.{{IMG_DOMAIN}}\/fimg\/${brand_id}_1.jpg" border=0><\/a>/g;
$content_html=~ s/{{UNSUB_URL}}/http:\/\/{{FOOTER_SUBDOMAIN}}.{{DOMAIN}}\/cgi-bin\/redir1.cgi?eid=0&amp;cid={{CID}}&amp;em=&amp;id=42&amp;nid=$nid/g;

my $html_template;
$sql = "select html_code,comm_wizard_c3,comm_wizard_cid,comm_wizard_progid,cr,landing_page from creative where creative_id=$creative_id";
$sth1 = $dbhq->prepare($sql);
$sth1->execute();
($html_template,$cwc3,$cwcid,$cwprogid,$cr,$landing_page) = $sth1->fetchrow_array();
$sth1->finish();
$html_template=~s/<html>//gi;
$html_template=~s/<\/html>//gi;
$html_template=~s/<body>//gi;
$html_template=~s/<\/body>//gi;
$html_template=~ s/{{IMG_DOMAIN}}/www.affiliateimages.com/g;
$content_html=~s/{{CREATIVE}}/$html_template/g;

#
$sql = "select unsub_link,advertiser_info.unsub_image,creative.unsub_image from creative,advertiser_info where creative_id=$creative_id and creative.advertiser_id=advertiser_info.advertiser_id and advertiser_info.advertiser_id=$aid";
$sth = $dbhq->prepare($sql);
$sth->execute();
($unsub_url,$unsub_img,$cunsub_img) = $sth->fetchrow_array();
$sth->finish();
if ($cunsub_img eq "NONE")
{
	$unsub_img="";
}
# replace unsubscribe field with the proper link
my $temp_str = "";
my $temp_str1 = "";
if ($aid != 0)
{
   	my $link_id=0;
	if ($unsub_url ne "")
	{
    $sql = "select link_id from links where refurl='$unsub_url'";
    $sth2 = $dbhq->prepare($sql);
    $sth2->execute();
    if (($link_id) = $sth2->fetchrow_array())
    {
    	$sth2->finish();
    }
    else
    {
    	$sth2->finish();
        $sql = "insert into links(refurl,date_added) values('$unsub_url',now())";
        $rows = $dbhu->do($sql);
        $sql = "select link_id from links where refurl='$unsub_url'";
        $sth2 = $dbhu->prepare($sql);
        $sth2->execute();
        ($link_id) = $sth2->fetchrow_array();
        $sth2->finish();
    }
	}
	if ($unsub_img eq "")
	{
	}
	else
	{
    	$_=$unsub_img;
    	if ( /\// )
    	{
    		$temp_str = "<a href=\"http://{{FOOTER_SUBDOMAIN}}.{{DOMAIN}}/cgi-bin/redir1.cgi?eid={{EMAIL_USER_ID}}&amp;cid=0&amp;em={{EMAIL_ADDR}}&amp;id=$link_id\" target=\"_blank\"><img src=\"http://{{IMG_DOMAIN}}/mimages/$unsub_img\" border=0 alt=\"Unsub\"></a><br><br>";
		}
		else
		{
        	$temp_str = "<a href=\"http://{{FOOTER_SUBDOMAIN}}.{{DOMAIN}}/cgi-bin/redir1.cgi?eid={{EMAIL_USER_ID}}&amp;cid=0&amp;em={{EMAIL_ADDR}}&amp;id=$link_id\" target=\"_blank\"><img src=\"http://{{IMG_DOMAIN}}/mimages/unsub/$unsub_img\" border=0 alt=\"Unsub\"></a><br><br>";
		}
	}
    $temp_str1 = "http://{{FOOTER_SUBDOMAIN}}.{{DOMAIN}}/cgi-bin/redir1.cgi?eid={{EMAIL_USER_ID}}&amp;cid=0&amp;em={{EMAIL_ADDR}}&amp;id=$link_id";
}
my $link_id;
$sql = "select link_id from advertiser_tracking where advertiser_id=$aid and client_id=$nid and daily_deal='N'";
$sth1 = $dbhu->prepare($sql);
$sth1->execute();
($link_id) = $sth1->fetchrow_array();
$sth1->finish();
if ($cwc3 ne "")
{
	$content_html =~ s/{{URL}}/http:\/\/{{DOMAIN}}\/cgi-bin\/redir1.cgi?eid={{EMAIL_USER_ID}}&amp;cid={{CID}}&amp;em={{EMAIL_ADDR}}&amp;id=$link_id&cwc3=$cwc3&cwcid=$cwcid&cwprogid=$cwprogid&cr=$cr&l=$landing_page/g;
}
else
{
	$content_html =~ s/{{URL}}/http:\/\/{{DOMAIN}}\/cgi-bin\/redir1.cgi?eid={{EMAIL_USER_ID}}&amp;cid={{CID}}&amp;em={{EMAIL_ADDR}}&amp;id=$link_id&cwcid=$cwcid&cwprogid=$cwprogid&cr=$cr&l=$landing_page/g;
}
$content_html =~ s\{{ADV_UNSUB}}\$temp_str\;
$content_html =~ s\{{LINK_ID}}\$link_id\;
$content_html =~ s\{{ADV_UNSUB_URL}}\$temp_str1\g;
$content_html=~ s/{{DOMAIN}}/${mail_domain}/g;
$content_html=~ s/{{IMG_DOMAIN}}/${image_domain}/g;
$content_html=~ s/{{FOOTER_SUBDOMAIN}}/www/g;
$content_html=~ s/{{CID}}/0/g;
$content_html=~ s/{{EMAIL_USER_ID}}/0/g;
$content_html=~ s/{{CLIENT_BRAND}}/$bname/g;
$content_html=~ s/{{MAILING_ADDR1}}/$addr1/g;
$content_html=~ s/{{MAILING_ADDR2}}/$addr2/g;
$content_html=~ s/{{EMAIL_ADDR}}/test\@test.com/g;
my $timestr = util::date(0,0);
$content_html=~ s/{{DATE}}/$timestr/g;
my $wiki_text="";
if ($include_wiki eq "Y")
{
    $wiki_text = $mtaRandom->wikiText();
}
$content_html=~s/\<\/body\>/$wiki_text\<\/body\>/i;
my $hstr=$mtaRandom->generateMailingHeaders($headerid);
$hstr=~s/##CID##/0/g;
$hstr=~s/##ENC_CID##/0/g;
$hstr=~s/##MAIL_DOMAIN##/$mail_domain/g;
if ($cwc3 ne "")
{
    $hstr =~ s/##URL##/http:\/\/{{DOMAIN}}\/cgi-bin\/redir1.cgi?eid={{EMAIL_USER_ID}}&amp;cid={{CID}}&amp;em={{EMAIL_ADDR}}&amp;id=$link_id&cwc3=$cwc3&cwcid=$cwcid&cwprogid=$cwprogid&cr=$cr&l=$landing_page/g;
}
else
{
    $hstr =~ s/##URL##/http:\/\/{{DOMAIN}}\/cgi-bin\/redir1.cgi?eid={{EMAIL_USER_ID}}&amp;cid={{CID}}&amp;em={{EMAIL_ADDR}}&amp;id=$link_id&cwcid=$cwcid&cwprogid=$cwprogid&cr=$cr&l=$landing_page/g;
}
$hstr=~s/{{CID}}/0/g;
$hstr=~ s/{{DOMAIN}}/${mail_domain}/g;
$hstr=~ s/{{EMAIL_USER_ID}}/0/g;
$hstr=~ s/{{EMAIL_ADDR}}/$tstr/g;

#--------------------------------
        print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>

<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Test Campaign Preview</title>
</head>

<body>
<center><h2>Unique Campaign Preview</h2>
<hr width=80%>
<br>
</center>
end_of_html
print "$hstr\n";
print "$content_html\n";
print<<"end_of_html";
</body>
</html>
end_of_html
