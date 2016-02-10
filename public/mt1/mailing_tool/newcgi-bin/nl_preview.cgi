#!/usr/bin/perl
#===============================================================================
# Name   : nl_preview.cgi 
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
my $aid;
my $bname;
my $addr1;
my $addr2;

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}
#------  connect to the util database -----------
my ($dbhq,$dbhu)=$util->get_dbh();
my $nl_id=$query->param('nl_id');
my $tid=$query->param('tid');
if ($tid eq "C")
{
	$field="nl_confirmation";
}
elsif ($tid eq "R")
{
	$field="nl_reminder";
}
elsif ($tid eq "N")
{
	$field="nl_template";
}
$sql="select $field,advertiser_id from newsletter where nl_id=$nl_id";
$sth = $dbhq->prepare($sql) ;
$sth->execute();
($content_html,$aid) = $sth->fetchrow_array();
$sth->finish();
my $image_domain;
my $mail_domain;
my $brand_id;
$sql="select url,newsletter.brand_id,newsletter.campaign_id from newsletter,brand_url_info where newsletter.brand_id=brand_url_info.brand_id and url_type='OI' and nl_id=$nl_id";
$sth = $dbhq->prepare($sql) ;
$sth->execute();
($image_domain,$brand_id,$cid) = $sth->fetchrow_array();
$sth->finish();
$sql="select url from brand_url_info where brand_id=$brand_id and url_type='O'"; 
$sth = $dbhq->prepare($sql) ;
$sth->execute();
($mail_domain) = $sth->fetchrow_array();
$sth->finish();
$sql="select privacy_img,unsub_img,client_id,newsletter_header,newsletter_footer,brand_name,mailing_addr1,mailing_addr2 from client_brand_info where brand_id=$brand_id";
$sth = $dbhq->prepare($sql);
$sth->execute();
($privacy_img,$brand_unsub,$nid,$header_image,$footer_image,$bname,$addr1,$addr2) = $sth->fetchrow_array();
$sth->finish();

$content_html=~ s/{{BRAND_PRIVACY}}/<a href="http:\/\/{{FOOTER_SUBDOMAIN}}.{{DOMAIN}}\/cgi-bin\/redir1.cgi?eid=0&amp;cid={{CID}}&amp;em=&amp;id=51&amp;nid=$nid" target=_blank><img src="http:\/\/www.{{IMG_DOMAIN}}\/fimg\/$privacy_img" border=0>/g;
$content_html=~ s/{{BRAND_UNSUB}}/<a href="http:\/\/{{FOOTER_SUBDOMAIN}}.{{DOMAIN}}\/cgi-bin\/redir1.cgi?eid=0&amp;cid={{CID}}&amp;em=&amp;id=42&amp;nid=$nid&nl_id=$nl_id" target=_blank><img src="http:\/\/www.{{IMG_DOMAIN}}\/fimg\/$brand_unsub" border=0><\/a>/g;
if ($tid eq "N")
{
	if ($header_image ne "")
	{
		$header_image="<img src=\"http://www.".$image_domain."/fimg/".$header_image. "\">";
	}
	if ($footer_image ne "")
	{
		$footer_image="<img src=\"http://www.".$image_domain."/fimg/".$footer_image."\">";
	}
#
#	Get advertiser unsub stuff
#
	$sql="select creative1_id from advertiser_setup where advertiser_id=$aid and class_id=4";
    $sth = $dbhq->prepare($sql);
    $sth->execute();
    ($creative_id) = $sth->fetchrow_array();
    $sth->finish();

	my $html_template;
    $sql = "select html_code from creative where creative_id=$creative_id";
    $sth1 = $dbhq->prepare($sql);
    $sth1->execute();
    ($html_template) = $sth1->fetchrow_array();
    $sth1->finish();
    $html_template=~s/<html>//gi;
    $html_template=~s/<\/html>//gi;
    $html_template=~s/<body>//gi;
    $html_template=~s/<\/body>//gi;
	$html_template=~ s/{{IMG_DOMAIN}}/www.affiliateimages.com/g;
    $content_html=~s/{{CREATIVE}}/$html_template/g;

#
    $sql = "select unsub_link,unsub_image,creative.unsub_image from creative,advertiser_info where creative_id=$creative_id and creative.advertiser_id=advertiser_info.advertiser_id and adveritser_info.advertiser_id=$aid";
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
    if ($aid != 0)
    {
        if ($unsub_img eq "")
        {
        }
        else
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
    }
    $content_html =~ s\{{ADV_UNSUB}}\$temp_str\;
#
#	Get article information
#
	my $article_id;
    $sql="select article_id from nl_article where nl_id=$nl_id order by rand()";
    $sth = $dbhq->prepare($sql);
    $sth->execute();
    ($article_id)=$sth->fetchrow_array();
    $sth->finish();

    my ($article_font,$article_headline,$author,$article);
	my $blurb;

    $sql="select blurb from article_blurb where article_id=$article_id order by rand()";
    $sth = $dbhq->prepare($sql);
    $sth->execute();
    ($blurb)=$sth->fetchrow_array();
    $sth->finish();

    $sql="select headline from article_headline where article_id=$article_id order by rand()";
    $sth = $dbhq->prepare($sql);
    $sth->execute();
    ($headline)=$sth->fetchrow_array();
    $sth->finish();

    $sql="select article_font,headline,author,html_code from article where article.article_id=$article_id";
    $sth = $dbhq->prepare($sql);
    $sth->execute();
    ($article_font,$article_headline,$author,$article)=$sth->fetchrow_array();
    $sth->finish();
    if ($author ne "")
    {
        $author="by ". $author;
    }

	$content_html=~ s/{{ARTICLE_AUTHOR}}/$author/g;
	$content_html=~ s/{{ARTICLE_HEADLINE}}/$article_headline/g;
	$content_html=~ s/{{ARTICLE}}/$article/g;
	$content_html=~ s/{{BLURB}}/$blurb/g;
	$content_html=~ s/{{ARTICLE_FONT}}/$article_font/g;
	$content_html=~ s/{{HEADER_IMAGE}}/$header_image/g;
	$content_html=~ s/{{FOOTER_IMAGE}}/$footer_image/g;
	$content_html=~ s/{{HEADLINE}}/$headline/g;
	$content_html=~ s/{{CLIENT_BRAND}}/$bname/g;
	$content_html=~ s/{{MAILING_ADDR1}}/$addr1/g;
	$content_html=~ s/{{MAILING_ADDR2}}/$addr2/g;
}
$content_html=~ s/{{DOMAIN}}/${mail_domain}/g;
$content_html=~ s/{{IMG_DOMAIN}}/${image_domain}/g;
$content_html=~ s/{{FOOTER_SUBDOMAIN}}/www/g;
$content_html=~ s/{{CID}}/$cid/g;
my $timestr = util::date(0,0);
$content_html=~ s/{{DATE}}/$timestr/g;

#--------------------------------
        print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>

<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Newsletter Preview</title>
</head>

<body>
end_of_html
print "$content_html\n";
print<<"end_of_html";
</body>
</html>
end_of_html
