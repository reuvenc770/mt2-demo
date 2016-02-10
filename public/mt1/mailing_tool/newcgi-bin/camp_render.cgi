#!/usr/bin/perl

# *****************************************************************************************
# camp_render.cgi
#
# this page presents the user with a render of the campaign email
#
# History
# *****************************************************************************************

# include Perl Modules

use strict;
use MIME::Base64;
use util;

my $util = util->new;
my $query = CGI->new;
my $dbh;
my $sth;
my $sql;
my $rows;
my $errmsg;
my $email_addr;
my $email_user_id;
my $first_name;
my $last_name;
my $campaign_id = $query->param('campaign_id');
my $images = $util->get_images_url;
my $subject;
my $body_text;
my $first_part;
my $testvar;
my $pos;
my $pos2;
my $the_rest;
my $end_pos;
my $selected_bg_color = "#509C10";
my $not_selected_bg_color = "#E3FAD1";
my $selected_tl_gif = "$images/blue_tl.gif";
my $selected_tr_gif = "$images/blue_tr.gif";
my $not_selected_tl_gif = "$images/lt_purp_tl.gif";
my $not_selected_tr_gif = "$images/lt_purp_tr.gif";
my $selected_text_color = "#FFFFFF";
my $not_selected_text_color = "#509C10";
my @bg_color;
my @tl_gif;
my @tr_gif;
my @text_color;
my $k;
my $from_addr;
my $other_addr;
my $aid;
my $footer_color;
my $internal_flag;
my $unsub_url;
my $unsub_image;
my $cunsub_image;
my $csite;
my $content_id;
my $footer_content_id;
my $format="H";
my $cdeploy="N";

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

$sql = "select default_subject,default_from,advertiser_id,unsub_image,content_id from creative where creative_id = $campaign_id";
$sth = $dbhq->prepare($sql);
$sth->execute();
($subject,$from_addr,$aid,$cunsub_image,$footer_content_id) = $sth->fetchrow_array();
$sth->finish();
$sql = "select advertiser_subject from advertiser_subject where subject_id = $subject and status='A'";
$sth = $dbhq->prepare($sql);
$sth->execute();
($subject) = $sth->fetchrow_array();
$sth->finish();
if ($subject eq "")
{
	$subject = "No subject selected";
}
$sql = "select advertiser_from from advertiser_from where from_id = $from_addr and status='A'";
$sth = $dbhq->prepare($sql);
$sth->execute();
($from_addr) = $sth->fetchrow_array();
$sth->finish();
if ($from_addr eq "")
{
	$from_addr = "{{{{FOOTER_SUBDOMAIN}}";
}
#
# Get unsub information and internal_flag
#
$sql = "select track_internally,unsub_link,unsub_image from advertiser_info where advertiser_id=$aid";
$sth = $dbhq->prepare($sql);
$sth->execute();
($internal_flag,$unsub_url,$unsub_image) = $sth->fetchrow_array();
$sth->finish();

if ($cunsub_image eq "NONE")
{
	$unsub_image="";
}
$email_user_id = 0; 

# Get the email template and substitute all the field data
my $bid=1;
my $the_email =  get_template($dbhu,$bid,$campaign_id,336,1,0,0,"",'Y');
$the_email=~s/{{DOMAIN}}/www.credithelpadvisor.com/g;
$the_email=~s/{{IMG_DOMAIN}}/www.credithelpadvisor.com/g;
print "Content-type: text/html\n\n";
print<<"end_of_html";
<html><head><title>Rendered HTML</title></head>
<body>
<textarea cols=80 rows=28>$the_email</textarea>
</body>
</html>
end_of_html


sub get_template
{
	my ($dbh,$bid,$creative_id,$template_id,$client_id,$mailing_domain_id,$binding_id, $fcode,$encrypt_link)=@_;
	my $sql;
	my $sth;
	my $master_str;
	my $brand_unsub_img;
	my ($aid,$internal_flag,$unsub_url,$unsub_img,$cunsub_img);
	my $creative_html;
	my $link_id=0;
	my $rows;
	my $url_prefix;
	my $img_prefix;
	my $temp_str;
	my $brand_name;
	my $articleid;
    my $headerid;
    my $blurbid;
	my ($article_font,$author,$article);
	my $headline;
	my $blurb;
	my $addr1;
	my $addr2;
	my $header_str;
	my $article_headline;
	my $article_author;
	my $brand_unsub_str;
	my $enc_cr;
	my $enc_email;

	if ($encrypt_link eq "")
	{
		$encrypt_link = "Y";
	}
	if ($template_id == 0)
	{
		$sql="select html_code,unsub_img,brand_name,mailing_addr1,mailing_addr2 from brand_template,client_brand_info,brand_template_join where brand_template_join.template_id=brand_template.template_id and client_brand_info.brand_id=".$bid." and brand_template_join.brand_id=client_brand_info.brand_id order by rand() limit 1";
	}
	else
	{
		$sql="select html_code,unsub_img,brand_name,mailing_addr1,mailing_addr2 from brand_template,client_brand_info where brand_template.template_id=$template_id and client_brand_info.brand_id=".$bid;
	}
	$sth=$dbh->prepare($sql);
	$sth->execute();
	($master_str,$brand_unsub_img,$brand_name,$addr1,$addr2)=$sth->fetchrow_array();
	$sth->finish();

	if ($creative_id > 0)
	{
		$sql="select creative.advertiser_id,track_internally,unsub_link,advertiser_info.unsub_image,creative.unsub_image from creative,advertiser_info where creative_id=$creative_id and creative.advertiser_id=advertiser_info.advertiser_id";
		$sth=$dbh->prepare($sql);
		$sth->execute();
		($aid,$internal_flag,$unsub_url,$unsub_img,$cunsub_img)=$sth->fetchrow_array();
		$sth->finish();
		if ($cunsub_img eq "")
		{
			$unsub_img="";
		}
    	if ($internal_flag eq "Y")
		{
        	$unsub_url = "http://{{DOMAIN}}/cgi-bin/adv_unsub.cgi?id=$aid&email_addr={{EMAIL_ADDR}}";
    	}
		my $temp_str="";
		my $temp_str1="";

		$sql="select html_code from creative where creative_id=".$creative_id;
		$sth=$dbh->prepare($sql);
		$sth->execute();
		($creative_html)=$sth->fetchrow_array();
		$sth->finish();
    	$creative_html=~ s/<html>//;
    	$creative_html=~ s/<HTML>//;
    	$creative_html=~ s/<\/html>//;
    	$creative_html=~ s/<\/HTML>//;
    	$creative_html=~ s/<body>//;
    	$creative_html=~ s/<BODY>//;
    	$creative_html=~ s/<\/body>//;
    	$creative_html=~ s/<\/BODY>//;
		$master_str=~s/{{CREATIVE}}/$creative_html/g;

		if ($unsub_url ne "")
		{
	    $sql = "select link_id from links where refurl='$unsub_url'";
		$sth=$dbh->prepare($sql);
		$sth->execute();
		if (($link_id)=$sth->fetchrow_array())
		{
			$sth->finish();
		}
		else
		{
			$sth->finish();
	    	$sql = "insert into links(refurl,date_added) values('$unsub_url',now())";
			$rows=$dbh->do($sql);
	        $sql = "select link_id from links where refurl='$unsub_url'";
			$sth=$dbh->prepare($sql);
			$sth->execute();
			($link_id)=$sth->fetchrow_array();
			$sth->finish();
	    }
		}
	    $url_prefix = '{{DOMAIN}}';
	    $img_prefix = '{{IMG_DOMAIN}}';
	    my $enc2a= encode_base64($link_id);
		chop($enc2a);
	    my $enc2b= encode_base64('0');
		chop($enc2b);
	    $enc_cr= encode_base64($creative_id);
		chop($enc_cr);
	    $enc_email= encode_base64('test@test.com');
		chop($enc_email);
	    if ($unsub_img ne "")
	    {
			$unsub_img=~s/\.gif//;
			$unsub_img=~s/\.jpg//;
			$unsub_img=~s/\.jpeg//;
			$unsub_img=~s/\.bmp//;
			$unsub_img=~s/\.png//;
	        $_=$unsub_img;
	        if ( /\// )
	        {
				if ($encrypt_link eq 'N')
				{
	        		$temp_str = "<a href=\"http://$url_prefix/cgi-bin/redir1.cgi?eid={{EMAIL_USER_ID}}&cid=0&em={EMAIL_ADDR}}&id=$link_id&n={{NID}}&f={{F}}&s={{S}}&c={{CRID}}\" target=\"_blank\"><img src=\"http://$img_prefix/images/$unsub_img\" border=0></a><br><br>";
				}
				else
				{
	        		$temp_str = "<a href=\"http://$url_prefix/x/$enc2b|$enc2b|$enc_email|$enc2a|$enc2b|$enc2b|$enc2b|$enc_cr|||||||.html\" target=\"_blank\"><img src=\"http://$img_prefix/images/$unsub_img\" border=0></a><br><br>";
				}
	        }
	        else
	        {
				if ($encrypt_link eq 'N')
				{
	        		$temp_str = "<a href=\"http://$url_prefix/cgi-bin/redir1.cgi?eid={{EMAIL_USER_ID}}&cid=0&em={{EMAIL_ADDR}}&id=$link_id&n={{NID}}&f={{F}}&s={{S}}&c={{CRID}}\" target=\"_blank\"><img src=\"http://$img_prefix/images/unsub/$unsub_img\" border=0></a><br><br>";
				}
				else
				{
	        		$temp_str = "<a href=\"http://$url_prefix/x/$enc2b|$enc2b|$enc_email|$enc2a|$enc2b|$enc2b|$enc2b|$enc_cr|||||||.html\" target=\"_blank\"><img src=\"http://$img_prefix/images/unsub/$unsub_img\" border=0></a><br><br>";
				}
	        }
	    }
		if ($encrypt_link eq 'N')
		{
	   		$temp_str1 = "http://$url_prefix/cgi-bin/redir1.cgi?eid={{EMAIL_USER_ID}}&cid=0&em={{EMAIL_ADDR}}&id=$link_id&n={{NID}}&f={{F}}&s={{S}}&c={{CRID}}";
		}
		else
		{
	   		$temp_str1 = "http://$url_prefix/x/$enc2b|$enc2b|$enc_email|$enc2a|$enc2b|$enc2b|$enc2b|$enc_cr|||||||.html";
		}
	    $master_str =~ s/{{ADV_UNSUB}}/$temp_str/g;
	    $master_str =~ s/{{ADV_UNSUB_URL}}/$temp_str1/g;
	}
	else
	{
		if ($fcode ne "")
		{
			$master_str =~ s/{{CREATIVE}}/$fcode/g;
		}
		else
		{
			$master_str =~ s/{{CREATIVE}}//g;
		}
		$master_str =~ s/{{ADV_UNSUB}}//g;
		$master_str =~ s/{{ADV_UNSUB_URL}}//g;
	}
	#
	# Get article and blurb information
	#
	$sql="select article_id from brand_article where brand_id=? order by rand()";
	$sth=$dbh->prepare($sql);
	$sth->execute($bid);
	if (($articleid)=$sth->fetchrow_array())
	{
    	$sth->finish();
    	$sql="select headline_id from article_headline where article_id=$articleid order by rand()";
    	$sth=$dbh->prepare($sql);
    	$sth->execute();
    	if (($headerid)=$sth->fetchrow_array())
    	{
    	}
    	else
    	{
        	$headerid=0;
    	}
    	$sth->finish();

    	$sql="select blurb_id from article_blurb where article_id=$articleid order by rand()";
    	$sth=$dbh->prepare($sql);
    	$sth->execute();
    	if (($blurbid)=$sth->fetchrow_array())
    	{
    	}
    	else
    	{
        	$blurbid=0;
    	}
    	$sth->finish();
	}
	else
	{
    	$sth->finish();
    	$articleid=0;
    	$headerid=0;
    	$blurbid=0;
		$article_headline="";
		$article_font="";
		$article_author="";
		$article="";
	}
	if ($articleid > 0)
	{
    	$sql="select article_font,headline,author,html_code from article where article_id=?";
    	$sth = $dbh->prepare($sql);
    	$sth->execute($articleid);
    	($article_font,$article_headline,$author,$article)=$sth->fetchrow_array();
    	$sth->finish();
    	if ($author ne "")
    	{
        	$author="by ". $author;
    	}
	}
	if ($headerid > 0)
	{
    	$sql="select headline from article_headline where headline_id=?";
    	$sth = $dbh->prepare($sql);
    	$sth->execute($headerid);
    	($headline)=$sth->fetchrow_array();
    	$sth->finish();
	}
	if ($blurbid > 0)
	{
    	$sql="select blurb from article_blurb where blurb_id=?";
    	$sth = $dbh->prepare($sql);
    	$sth->execute($blurbid);
    	($blurb)=$sth->fetchrow_array();
    	$sth->finish();
	}
	
    $master_str=~s/{{HEADLINE}}/$headline/g;
    $master_str=~s/{{BLURB}}/$blurb/g;
    $master_str=~s/{{ARTICLE_FONT}}/$article_font/g;
    $master_str=~s/{{ARTICLE}}/$article/g;
    $master_str=~s/{{ARTICLE_HEADLINE}}/$article_headline/g;
    $master_str=~s/{{ARTICLE_AUTHOR}}/$author/g;

	my $enc1= encode_base64('51');
	chop($enc1);
	my $enc2= encode_base64('42');
	chop($enc2);
    my $enc2b= encode_base64('0');
    chop($enc2b);
	if ($encrypt_link eq 'N')
	{
		$master_str =~ s/{{BRAND_PRIVACY}}/<a href="http:\/\/{{DOMAIN}}\/cgi-bin\/redir1.cgi?eid={{EMAIL_USER_ID}}&cid=0&em={{EMAIL_ADDR}}&id=51&n={{NID}}" target=_blank><img src="http:\/\/{{IMG_DOMAIN}}\/fimg\/${bid}_2.jpg" border=0>/;
		$master_str =~ s/{{PRIVACY_URL}}/http:\/\/{{DOMAIN}}\/cgi-bin\/redir1.cgi?eid={{EMAIL_USER_ID}}&cid=0&em={{EMAIL_ADDR}}&id=51&n={{NID}}"/;
	}
	else
	{
		$master_str =~ s/{{BRAND_PRIVACY}}/<a href="http:\/\/{{DOMAIN}}\/x\/$enc2b|$enc2b|$enc_email|$enc1|$enc2b||||||||||.html" target=_blank><img src="http:\/\/{{IMG_DOMAIN}}\/fimg\/${bid}_2.jpg" border=0>/;
		$master_str =~ s/{{PRIVACY_URL}}/http:\/\/{{DOMAIN}}\/x\/$enc2b|$enc2b|$enc_email|$enc1|$enc2b||||||||||.html/;
	}

	if ($brand_unsub_img ne "")
	{
		$brand_unsub_img=~s/\.gif//;
		$brand_unsub_img=~s/\.jpg//;
		$brand_unsub_img=~s/\.jpeg//;
		$brand_unsub_img=~s/\.bmp//;
		$brand_unsub_img=~s/\.png//;
		if ($encrypt_link eq 'N')
		{
    		$master_str =~ s/{{BRAND_UNSUB}}/<a href="http:\/\/{{DOMAIN}}\/cgi-bin\/redir1.cgi?eid={{EMAIL_USER_ID}}&cid={{CID}}&em={{EMAIL_ADDR}}&id=42&n={{NID}}" target=_blank><img src="http:\/\/{{IMG_DOMAIN}}\/fimg\/$brand_unsub_img" border=0><\/a>/;
		}
		else
		{
    		$master_str =~ s/{{BRAND_UNSUB}}/<a href="http:\/\/{{DOMAIN}}\/x\/$enc2b|$enc2b|$enc_email|$enc2|$enc2b||||||||||.html" target=_blank><img src="http:\/\/{{IMG_DOMAIN}}\/fimg\/$brand_unsub_img" border=0><\/a>/;
		}
    	$brand_unsub_str = $brand_unsub_img;
	}
	else
	{
		if ($encrypt_link eq 'N')
		{
    		$master_str =~ s/{{BRAND_UNSUB}}/<a href="http:\/\/{{DOMAIN}}\/cgi-bin\/redir1.cgi?eid={{EMAIL_USER_ID}}&cid={{CID}}&em={{EMAIL_ADDR}}&id=42&n={{NID}}" target=_blank><img src="http:\/\/{{IMG_DOMAIN}}\/fimg\/${bid}_1.jpg" border=0><\/a>/;
		}
		else
		{
    		$master_str =~ s/{{BRAND_UNSUB}}/<a href="http:\/\/{{DOMAIN}}\/x\/$enc2b|$enc2b|$enc_email|$enc2|$enc2b||||||||||.html" target=_blank><img src="http:\/\/{{IMG_DOMAIN}}\/fimg\/${bid}_1.jpg" border=0><\/a>/;
		}
    	$brand_unsub_str = ${bid}."_1.jpg";
	}
	$master_str =~ s/{{UNSUB_URL}}/http:\/\/{{DOMAIN}}\/x\/$enc2b|$enc2b|$enc_email|$enc2|$enc2b||||||||||.html/g;
	$master_str =~ s/{{CLIENT_BRAND}}/$brand_name/g;
	$master_str =~ s\<BODY\<body\;
    my $pos1 = index($master_str, "<body");
    my $pos2 = index($master_str, ">",$pos1);
	my $enc_did=encode_base64($mailing_domain_id);
	chop($enc_did);
	my $enc_binding=encode_base64($binding_id);
	chop($enc_binding);
	my $tracking_str = "<IMG SRC=\"http://{{DOMAIN}}/o/$enc2b|$enc2b|$enc2b|$enc2b|$enc_cr|$enc_did|$enc_binding|\" border=0 height=1 width=1>";
##  header with only open track for emusic. change by ST 10/12.
	$_=$master_str;
	if (/{{TRACKING}}/)
	{
    		$master_str =~ s\{{TRACKING}}\${tracking_str}\g;
	}
	else 	
	{
			substr($master_str,$pos1,$pos2-$pos1+1) = "<body>";
    		$master_str =~ s\<body>\<body>${tracking_str}\;
	}
	$master_str =~ s/{{MAILING_ADDR1}}/$addr1/g;
	$master_str =~ s/{{MAILING_ADDR2}}/$addr2/g;
		
#	$master_str=replace_ids($master_str);
	
     $master_str =~ s/{{URL}}/http:\/\/{{DOMAIN}}\/x\/$enc2b|$enc2b|$enc_email|$enc2b|$enc2b|$enc2b|$enc2b|$enc_cr|||||||.html/g;
	return($master_str);
}
