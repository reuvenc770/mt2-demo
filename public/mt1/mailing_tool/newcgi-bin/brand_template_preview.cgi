#!/usr/bin/perl
#===============================================================================
# Name   : brand_template_preview.cgi 
#
#--Change Control---------------------------------------------------------------
#===============================================================================

#-----  include Perl Modules ---------
use strict;
use CGI;
use util;

#------  get some objects to use later ---------
my $util = util->new;
my $query = CGI->new;
my $sql;
my $sth;
my $sth1;
my $sth2;
my $rows;
my $unsub_img;
my $cunsub_img;
my $creative_id;
my $unsub_url;
my $dbh;
my $cid;
my $cname;
my $content_name;
my $content_html;
my $inactive_date;
my $content_date;
my $author;
my $headline;
my $article_font;
my $field;
my ($privacy_img,$brand_unsub);
my $nid;
my $header_image;
my $footer_image;
my $cwc3;
my $cwcid;
my $cwprogid;
my $cr;
my $landing_page;

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}
#------  connect to the util database -----------
my ($dbhq,$dbhu)=$util->get_dbh();
my $temp_id=$query->param('temp_id');
my $aid=$query->param('aid');
my $brand_id=$query->param('bid');
#
$sql="select html_code from brand_template where template_id=$temp_id";
$sth = $dbhq->prepare($sql) ;
$sth->execute();
($content_html) = $sth->fetchrow_array();
$sth->finish();
my $image_domain;
my $mail_domain;
$sql="select url from brand_url_info where brand_id=$brand_id and url_type='O'";
$sth = $dbhq->prepare($sql) ;
$sth->execute();
($image_domain) = $sth->fetchrow_array();
$sth->finish();
$sql="select url from brand_url_info where brand_id=$brand_id and url_type='O'"; 
$sth = $dbhq->prepare($sql) ;
$sth->execute();
($mail_domain) = $sth->fetchrow_array();
$sth->finish();
$sql="select privacy_img,unsub_img,client_id from client_brand_info where brand_id=$brand_id";
$sth = $dbhq->prepare($sql);
$sth->execute();
($privacy_img,$brand_unsub,$nid) = $sth->fetchrow_array();
$sth->finish();

$content_html=~ s/{{BRAND_PRIVACY}}/<a href="http:\/\/{{FOOTER_SUBDOMAIN}}.{{DOMAIN}}\/cgi-bin\/redir1.cgi?eid=0&amp;cid={{CID}}&amp;em=&amp;id=51&amp;nid=$nid" target=_blank><img src="http:\/\/www.{{IMG_DOMAIN}}\/fimg\/${brand_id}_2.jpg" border=0>/g;
$content_html=~ s/{{PRIVACY_URL}}/http:\/\/{{FOOTER_SUBDOMAIN}}.{{DOMAIN}}\/cgi-bin\/redir1.cgi?eid=0&amp;cid={{CID}}&amp;em=&amp;id=51&amp;nid=$nid/g;
$content_html=~ s/{{BRAND_UNSUB}}/<a href="http:\/\/{{FOOTER_SUBDOMAIN}}.{{DOMAIN}}\/cgi-bin\/redir1.cgi?eid=0&amp;cid={{CID}}&amp;em=&amp;id=42&amp;nid=$nid" target=_blank><img src="http:\/\/www.{{IMG_DOMAIN}}\/fimg\/${brand_id}_1.jpg" border=0><\/a>/g;
$content_html=~ s/{{UNSUB_URL}}/http:\/\/{{FOOTER_SUBDOMAIN}}.{{DOMAIN}}\/cgi-bin\/redir1.cgi?eid=0&amp;cid={{CID}}&amp;em=&amp;id=42&amp;nid=$nid/g;
#
#	Get advertiser unsub stuff
#
$sql="select creative1_id from advertiser_setup where advertiser_id=$aid and class_id=4";
$sth = $dbhq->prepare($sql);
$sth->execute();
($creative_id) = $sth->fetchrow_array();
$sth->finish();

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
    		$temp_str = "<a href=\"http://{{FOOTER_SUBDOMAIN}}.{{DOMAIN}}/cgi-bin/redir1.cgi?eid={{EMAIL_USER_ID}}&amp;cid=0&amp;em={{EMAIL_ADDR}}&amp;id=$link_id\" target=\"_blank\"><img src=\"http://{{IMG_DOMAIN}}/images/$unsub_img\" border=0 alt=\"Unsub\"></a><br><br>";
		}
		else
		{
        	$temp_str = "<a href=\"http://{{FOOTER_SUBDOMAIN}}.{{DOMAIN}}/cgi-bin/redir1.cgi?eid={{EMAIL_USER_ID}}&amp;cid=0&amp;em={{EMAIL_ADDR}}&amp;id=$link_id\" target=\"_blank\"><img src=\"http://{{IMG_DOMAIN}}/images/unsub/$unsub_img\" border=0 alt=\"Unsub\"></a><br><br>";
		}
	}
    $temp_str1 = "http://{{FOOTER_SUBDOMAIN}}.{{DOMAIN}}/cgi-bin/redir1.cgi?eid={{EMAIL_USER_ID}}&amp;cid=0&amp;em={{EMAIL_ADDR}}&amp;id=$link_id";
}
my $link_id;
$sql = "select link_id from advertiser_tracking where advertiser_id=$aid and client_id=2 and daily_deal='N'";
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
$content_html =~ s\{{LINK_ID}}\$link_id\;
$content_html =~ s\{{ADV_UNSUB}}\$temp_str\;
$content_html =~ s\{{ADV_UNSUB_URL}}\$temp_str1\g;
$content_html=~ s/{{DOMAIN}}/${mail_domain}/g;
$content_html=~ s/{{IMG_DOMAIN}}/${image_domain}/g;
$content_html=~ s/{{FOOTER_SUBDOMAIN}}/www/g;
$content_html=~ s/{{CID}}/0/g;
$content_html=~ s/{{EMAIL_USER_ID}}/0/g;
$content_html=~ s/{{EMAIL_ADDR}}/test\@test.com/g;
my $timestr = util::date(0,0);
$content_html=~ s/{{DATE}}/$timestr/g;

#--------------------------------
        print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>

<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Brand Template Preview</title>
</head>

<body>
end_of_html
print "$content_html\n";
print<<"end_of_html";
</body>
</html>
end_of_html
