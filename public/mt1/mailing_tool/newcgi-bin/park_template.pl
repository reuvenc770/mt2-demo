#!/usr/bin/perl 
use strict;
use File::Copy;
use util;
use HTML::LinkExtor;
use WWW::Curl::easy;
use URI::Split qw(uri_split uri_join);
use File::Basename;
use vars qw($hrINIT);

# declare variables

my $util = util->new;
my $dbh;
my $add_sub_dir;
my $file;
my $user_id;
my $csite;
my $csite1;
my $csite2;
my $sql;
my $sth1;
my $errmsg;
my ($scheme, $auth, $path, $query, $frag);
my $name;
my $new_name;
my $suffix;
my $rows;
my %hsh_fl_pos_names;
my ($email_addr,  $email_type);
my ($camp_id, $fromaddress, $subject);
my $email_user_id;
my ($log_file, $file_name, $file_out);
my ($TRUE, $FALSE);
$TRUE  = 1 ;
$FALSE = 0 ;
my $list_id;
my $notify_email_addr;
my $mail_mgr_addr;
my $list_name;
my $reccnt_tot = 0 ;
my $reccnt_good = 0 ;
my $reccnt_bad = 0 ;
my $cdate;
my $email_mgr_addr;
my $html_template;
my $text_template;
my $aol_template;
my $html_email_footer;
my $text_email_footer;
my $refid;
my $footer_color;
my $internal_flag;
my $unsub_url;
my $unsub_img;
my $cunsub_img;
my $global_text;
my $global_added;
my $img_added;
my $img_dir;
my $BASE_DIR;
my $aid;
my $img_cnt=0;

init();
$| = 1;    # don't buffer output for debugging log

# connect to the util database 
my $dbhq;
my $dbhu;
($dbhq,$dbhu)=$util->get_dbh();

my $qSel=qq^SELECT brand_id AS bID, header_text AS txtH, footer_text AS txtF FROM client_brand_info WHERE brand_id=?^;
my $sth=$dbhq->prepare($qSel);
$sth->execute($hrINIT->{bID});
my $hrBrand=$sth->fetchrow_hashref;
$sth->finish;
die "Can't find brand $hrINIT->{bID}" unless $hrBrand->{bID};


my $bin_dir_http;

	$sql = "select parmval from sysparm where parmkey = 'BIN_DIR_HTTP'"; 
	$sth = $dbhq->prepare($sql); 
	$sth->execute();
	($bin_dir_http) = $sth->fetchrow_array();
	$sth->finish();

	# get html email footer from sysparm table
	$sql = "select parmval from sysparm where parmkey = 'HTML_EMAIL_FOOTER'";
	$sth = $dbhq->prepare($sql);
	$sth->execute();
	($html_email_footer) = $sth->fetchrow_array();
	$sth->finish();

	$camp_id=$hrINIT->{crtvID};
	$email_type = "H";

	$sql = "select creative.advertiser_id,track_internally,unsub_link,advertiser_info.unsub_image,creative.unsub_image from creative,advertiser_info where creative_id=$camp_id and creative.advertiser_id=advertiser_info.advertiser_id";
	$sth = $dbhq->prepare($sql);
	$sth->execute();
	($aid,$internal_flag,$unsub_url,$unsub_img,$cunsub_img) = $sth->fetchrow_array();
	$sth->finish(); 
	if ($cunsub_img eq "NONE")
	{
		$unsub_img="";
	}
	$user_id = 2;
	$csite="{{DOMAIN}}";
	$csite1=$csite;
	$csite2="{{DOMAIN}}";
	$footer_color="Black";

    if ($internal_flag eq "Y")
    {
    	$unsub_url = "http://{{DOMAIN}}/cgi-bin/adv_unsub.cgi?id=$aid&amp;email_addr={{EMAIL_ADDR}}";
    }


	# Get the text for this campaign
	$sql = "select html_code from creative where creative_id=$camp_id";
	$sth1 = $dbhq->prepare($sql);
	$sth1->execute();
	($html_template) = $sth1->fetchrow_array();
	$sth1->finish();

	my $the_email;
	my $format;
	my $template_text;
	my $new_text1;


	open (MAIL,">${camp_id}-$hrINIT->{bID}.raw");
	$format = "H";
	$template_text = $html_template;

	# Call routine to get the campaigns info and do all the substitution in the template
	# it returns the template text with the fields substitutied with the data
	$the_email = template_substit($dbh,$camp_id,$template_text,$format,$new_text1,$refid);
    $the_email =~ s/\xc2//g;
    $the_email =~ s/\xa0//g;
    $the_email =~ s/\xb7//g;
    $the_email =~ s/\x85//g;
    $the_email =~ s/\x95//g;
    $the_email =~ s/\xae//g;
    $the_email =~ s/\x99//g;
    $the_email =~ s/\xa9//g;
    $the_email =~ s/\x92//g;
    $the_email =~ s/\x93//g;
    $the_email =~ s/\x94//g;
    $the_email =~ s/\x95//g;
    $the_email =~ s/\x96//g;
    $the_email =~ s/\x97//g;
    $the_email =~ s/\x82//g;
	$the_email =~ s/{{FOOTER_TEXT}}/$hrBrand->{txtF}/g;
	$the_email =~ s/{{HEADER_TEXT}}/$hrBrand->{txtH}/g;
	
	$the_email =~ s/{{EMAIL_ADDR}}/%%\$email%%/g;
	$the_email =~ s/{{NAME}}/%%\$first_name%%/g;
	$the_email =~ s/{{DOMAIN}}/%%\$_sdomain%%/g;
	$the_email =~ s/{{CID}}/%%\$cid%%/g;
	$the_email =~ s/{{EMAIL_USER_ID}}/%%\$email_user_id%%/g;
	$the_email =~ s/{{FID}}/%%\$fid%%/g;
	$the_email =~ s/{{SID}}/%%\$sid%%/g;
	$the_email =~ s/{{CRID}}/$camp_id/g;
	$the_email =~ s/{{URL}}/%%\$url%%/g;
	$the_email =~ s/{{IMG_DOMAIN}}/%%\$img_domain%%/g;
	$the_email =~ s/{{FOOTER_SUBDOMAIN}}/%%\$footer_subdomain%%/g;
	$the_email =~ s/{{NID}}/%%\$nid%%/g;
	$the_email =~ s/{{FOOTER_STR}}/%%\$footer_str%%/g;
	$the_email =~ s/{{FOOTER_TEXT}}/%%\$footer_text%%/g;
	$the_email =~ s/{{HEADER_TEXT}}/%%\$header_text%%/g;
	my $em_msg=qq^From: {{FROM}}
To: {{EMAIL}}
Subject: {{SUBJECT}}
Content-Type: text/html; charset="ISO-8859-1"

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
$the_email^;

	open(MAIL, ">$hrINIT->{crtvID}-$hrINIT->{bID}.raw");
	print MAIL "$em_msg";
	close MAIL;
	$util->clean_up();
	exit(0) ;				

# ******************************************************************************
# sub template_substit()
# this routine builds the body of an email, by looking up all the campaigns values
# and reading the campaigns template and substituting all the fields.
# ******************************************************************************

sub template_substit()
{
	my ($dbh,$camp_id,$template_text,$format,$new_text1,$refid) = @_;
	my $sql;
	my $sth1;
	my $sth2;
	my $tracking_str;
	my $image_url;
	my $title;
	my $subtitle;
	my $date_str;
	my $greeting;
	my $introduction;
	my $closing;
	my $promotion_name;
	my $promotion_desc;
	my $promotion_image_url;
	my $promotion_link;
	my $promotion_link_name;
	my $contact_name;
	my $contact_email;
	my $contact_url;
	my $contact_phone; 
	my $contact_company; 
	my $show_ad_top;
	my $show_ad_bottom;
	my $show_popup;
	my $article_num;
	my $article_title;
	my $article_text;
	my $article_link;
	my $article_link_name;
	my $article_image_url;
	my $first_name;
	my $last_name;
	my $address;
	my $address2;
	my $city;
	my $state;
	my $zip;
	my $phone;
	my $country;
	my $birth_date;
	my $gender;
	my $ads_url;
	my $top_ad_opt;
	my $top_ad_code;
	my $bottom_ad_opt;
	my $bottom_ad_code;
	my $client_id;
	my $hidden_text;
	my $timestr;
	my $curtime;
	my $physical_addr;
	my $temp_str;
	my $link_id;
	my $refurl;
	my $content_str;

    use URI::Escape;

	# If tracking in text then replace with correct information
	$_ = $template_text;
#	$template_text =~ s/{{CID}}/$camp_id/g;
	$template_text =~ s\<BODY\<body\;
    my $pos1 = index($template_text, "<body");
    my $pos2 = index($template_text, ">",$pos1);
	substr($template_text,$pos1,$pos2-$pos1+1) = "<body>";
#	$tracking_str = "<IMG SRC=\"http://{{DOMAIN}}/cgi-bin/open_email1.cgi?id={{EMAIL_USER_ID}}&amp;cid={{CID}}&amp;f={{FID}}&amp;s={{SID}}&amp;c={{CRID}}&amp;u={{URLID}}\" border=0 height=1 width=1 alt=\"open\">";
	$tracking_str = "<IMG SRC=\"http://{{DOMAIN}}/cgi-bin/open_email1.cgi?id={{EMAIL_USER_ID}}&amp;cid={{CID}}&amp;f={{FID}}&amp;s={{SID}}&amp;c={{CRID}}&amp;\" border=0 height=2 width=2 alt=\"open\">";
	$template_text =~ s\<body>\<body><center><p STYLE="font-size:10pt; font-family:arial">{{HEADER_TEXT}}</p></center><p>${tracking_str}\;
    $template_text =~ s\<HEAD\<head\gi;
    $_ = $template_text;
    if (/<head/)
    {
        my $got_head = 1;
    }
    else
    {
        $template_text =~ s/<body>/<head><meta http-equiv="Content-Type" content="text\/html; charset=windows-1252"><title>{{EMAIL_USER_ID}}<\/title><\/head><body>/;
    }
	$template_text =~ s/{{TRACKING}}//g;
    if (/{{HEADER_INFO}}/)
    {
       	$tracking_str = "<i>This message is being brought to you by {{DOMAIN}}.  If you are receiving this message in error, please see bottom of page.</i><p>";
        $template_text =~ s/{{HEADER_INFO}}/$tracking_str/g;
    }
	#
	# Check for CONTENT_HEADER tag
	#
	$content_str = "";
	$template_text =~ s/{{CONTENT_HEADER}}/$content_str/g;
	$content_str = "";
	$_ = $template_text;
	$template_text =~ s/{{CONTENT_HEADER_TEXT}}/$content_str/g;
    if (/{{TIMESTAMP}}/)
    {
        $timestr = util::date($curtime,5);
        $template_text =~ s/{{TIMESTAMP}}/$timestr/g;
    }
	if (/{{REFID}}/)
	{
		$template_text =~ s/{{REFID}}/$refid/g;
	}
	$template_text =~ s/{{CLICK}}//g;

	$contact_url="http://www.{{DOMAIN}}";
	$contact_email="offers\@$csite";
	$contact_name="$csite Offers";
	$contact_company="$csite";

	# Add special Unsubscribe footer to the bottom of every email - 
	# this is hard coded here because the users cannot remove this from the emails that
	# go out

		# substitute end of page (closing body tag) with all the unsubscribe
		# footer stuff that must go on the bottom of every email, adding the
		# closing body tag back on
		$template_text =~ s\</BODY>\</body>\;

		if ($footer_color eq "WHITE")
		{
			$template_text =~ s\</body>\<p><HR width="90%" SIZE=1><p><center>
<table cellspacing=0 cellpadding=0 width=600 border=0>
<tr><td align=center>
<font face="Verdana,Arial" size="1" color=white>
</font>
<br>
{{UNSUBSCRIBE}} 
</td></tr></table></center></p>\;
		}
		else
		{
			$template_text =~ s\</body>\<p><HR width="90%" SIZE=1><p><center>
<table cellspacing=0 cellpadding=0 width=600 border=0>
<tr><td align=center>
<font face="Verdana,Arial" size="1">
</font>
<br>
{{UNSUBSCRIBE}} 
</td></tr></table></center></p>\;
		}

		# append the html email footer to the end of the email
		$template_text =~ s\</HTML>\</html>\;
		$template_text =~ s\</html>\\;
		$template_text .= $html_email_footer;

		# now add end body tag to close the html email

		$template_text .= "<p STYLE=\"font-size:10pt; font-family:arial\">{{FOOTER_TEXT}}</p></body></html>";


		#
		# Replace Re-directs
		#
			$global_added = 0;
			$img_added = 0;
			$global_text = $template_text;
			$img_dir = get_name() . "_" . $camp_id;
			my $p = HTML::LinkExtor->new(\&cb); 
			$p->parse($template_text); 
			$template_text = $global_text;
			$sql = "select parmval from sysparm where parmkey='BASE_DIR'";
			$sth1 = $dbhq->prepare($sql);
			$sth1->execute();
			($BASE_DIR) = $sth1->fetchrow_array();
			$sth1->finish;
			if ($img_added == 1)
			{
				my @args = ("${BASE_DIR}newcgi-bin/cp_img.sh $img_dir");
				system(@args) == 0 or die "system @args failed: $?";
			}
    $template_text =~ s/www.affiliateimages.com/{{IMG_DOMAIN}}/g;
    $template_text =~ s/affiliateimages.com/{{IMG_DOMAIN}}/g;
#    	$template_text =~ s/{{CID}}/$camp_id/g;

		# replace unsubscribe field with the proper link

		
					$temp_str = "";
					if ($aid != 0)
					{
						if ($unsub_img eq "")
						{
						$sql = "select physical_addr from advertiser_info where advertiser_id=$aid"; 
						$sth2 = $dbhq->prepare($sql);
						$sth2->execute();
						($physical_addr) = $sth2->fetchrow_array();
						$sth2->finish();
#						$temp_str = "<font size=-2>To stop receiving email promotions from this ADVERTISER ONLY.<br><a href=\"$unsub_url\">FOLLOW THIS LINK</a> or contact the advertiser at: <br>$physical_addr</font><br><br>";
						}
                    	else
                    	{
							my $link_id;
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
my $BASE_DIR;
my $refurl;
$sql = "select parmval from sysparm where parmkey='BASE_DIR'";
$sth1 = $dbhq->prepare($sql);
$sth1->execute();
($BASE_DIR) = $sth1->fetchrow_array();
$sth1->finish;
open(FILE,"> ${BASE_DIR}logs/redir.dat") or die "can't open file : $!";
$sql = "select link_id,refurl from links order by link_id";
$sth1 = $dbhq->prepare($sql);
$sth1->execute();
while (($link_id,$refurl) = $sth1->fetchrow_array())
{
    print FILE "$link_id|$refurl\n";
}
$sth1->finish();
close(FILE);
my @args = ("${BASE_DIR}newcgi-bin/cp_redir.sh");
system(@args) == 0 or die "system @args failed: $?";

							$sql = "select link_id from links where refurl='$unsub_url'";
							$sth2 = $dbhq->prepare($sql);
							$sth2->execute();
							($link_id) = $sth2->fetchrow_array();
							$sth2->finish();
							}
                        	$temp_str = "<a href=\"http://{{FOOTER_SUBDOMAIN}}.{{DOMAIN}}/cgi-bin/redir1.cgi?eid={{EMAIL_USER_ID}}&amp;cid=0&amp;em={{EMAIL_ADDR}}&amp;id=$link_id\" target=\"_blank\"><img src=\"http://www.{{IMG_DOMAIN}}/images/unsub/$unsub_img\" border=0 alt=\"Unsub\"></a><br><br>";
                    	}
					}
			if ($footer_color eq "WHITE")
			{
                $template_text =~ s\{{UNSUBSCRIBE}}\$temp_str<a href="http://{{FOOTER_SUBDOMAIN}}.{{DOMAIN}}/cgi-bin/redir1.cgi?eid={{EMAIL_USER_ID}}&amp;cid={{CID}}&amp;em={{EMAIL_ADDR}}&amp;id=42&amp;nid={{NID}}" target=_blank><img src="http://www.{{IMG_DOMAIN}}/fimg/{{FOOTER_STR}}_1.jpg" border=0 alt="end this subscription"></a><br><a href="http://{{FOOTER_SUBDOMAIN}}.{{DOMAIN}}/cgi-bin/redir1.cgi?cid=0&amp;eid={{EMAIL_USER_ID}}&amp;id=51&amp;nid={{NID}}" target=_blank><img src="http://www.{{IMG_DOMAIN}}/fimg/{{FOOTER_STR}}_2.jpg" border=0 alt="privacy policy"></a>\g;
			}
			else
			{
                $template_text =~ s\{{UNSUBSCRIBE}}\$temp_str<a href="http://{{FOOTER_SUBDOMAIN}}.{{DOMAIN}}/cgi-bin/redir1.cgi?eid={{EMAIL_USER_ID}}&amp;cid={{CID}}&amp;em={{EMAIL_ADDR}}&amp;id=42&amp;nid={{NID}}" target=_blank><img src="http://www.{{IMG_DOMAIN}}/fimg/{{FOOTER_STR}}_1.jpg" border=0 alt="end this subscription"></a><br><a href="http://{{FOOTER_SUBDOMAIN}}.{{DOMAIN}}/cgi-bin/redir1.cgi?cid=0&amp;eid={{EMAIL_USER_ID}}&amp;id=51&amp;nid={{NID}}" target=_blank><img src="http://www.{{IMG_DOMAIN}}/fimg/{{FOOTER_STR}}_2.jpg" border=0 alt="privacy policy"></a>\g;
			}

		# substitute <br> for the carriage returns if displaying html page

		$introduction =~ s/\n/<br>/g;
		$closing =~ s/\n/<br>/g;
		$promotion_desc =~ s/\n/<br>/g;

	# contact fields

    $template_text =~ s/{{CONTACT_EMAIL}}/$contact_email/g;
    $template_text =~ s/{{CONTACT_URL}}/$contact_url/g;
#   $template_text =~ s/{{CONTACT_PHONE}}/$contact_phone/g;
    $template_text =~ s/{{CONTACT_NAME}}/$contact_name/g;
    $template_text =~ s/{{CONTACT_COMPANY}}/$contact_company/g;

	# personalization fields

#   	$template_text =~ s/{{EMAIL_ADDR}}/$email_addr/g;
#   	$template_text =~ s/{{EMAIL_USER_ID}}/$email_user_id/g;

    # Get the client id for this campaign

	$template_text =~ s\{{POPUP_AD}}\\g;
	$template_text =~ s\{{TOP_AD}}\\g;
	$template_text =~ s\{{BOTTOM_AD}}\\g;
	$tracking_str = "${bin_dir_http}redir1.cgi?id={{EMAIL_USER_ID}}&amp;cid={{CID}}&amp;l=";
	return ($template_text);
}
sub get_name
{
srand(rand time());
my @c=split(/ */, "bcdfghjklmnprstvwxyz");
my @v=split(/ */, "aeiou");
my $sname;
my $i;
$sname = $c[int(rand(20))];
$sname = $sname . $v[int(rand(5))];
$sname = $sname . $c[int(rand(20))];
$sname = $sname . $v[int(rand(5))];
$sname = $sname . $c[int(rand(20))];
return $sname;
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

     if (($tag eq "img") or ($tag eq "background") or ($url1 eq "background"))
     {
        $_ = $url2;
        if ((/DOMAIN/) || (/IMG_DOMAIN/))
        {
            my $nomove= 1;
        }
        else
        {
            #
            # Get directory and filename
            #
            ($scheme, $auth, $path, $query1, $frag) = uri_split($url2);
            ($name,$frag,$suffix) = fileparse($path);
            my $repl_url = $scheme . "://" . $auth . $frag;
            $img_cnt++;
			my $time_str = time();
            if ($query1 ne "")
            {
                $repl_url = $repl_url . $name . "?" . $query1;
                $new_name = get_name() . "_${time_str}_${img_cnt}.gif";
            }
			else
			{
                my $temp_str;
                ($temp_str,$ext) = split('\.',$name);
                $new_name = get_name() . "_${img_cnt}.${ext}";
			}
            my $curl = WWW::Curl::easy->new();
            $curl->setopt(CURLOPT_NOPROGRESS, 1);
            $curl->setopt(CURLOPT_MUTE, 0);
            $curl->setopt(CURLOPT_FOLLOWLOCATION, 1);
            $curl->setopt(CURLOPT_TIMEOUT, 30);
            open HEAD, ">head.out";
            $curl->setopt(CURLOPT_WRITEHEADER, *HEAD);
            open BODY, "> /var/www/util/tmpimg/$new_name";
            $curl->setopt(CURLOPT_FILE,*BODY);
            $curl->setopt(CURLOPT_URL, $url2);
            my $retcode=$curl->perform();
            if ($retcode == 0)
            {
            }
            else
            {
   # We can acces the error message in $errbuf here
#    print STDERR "$retcode / ".$curl->errbuf."\n";
    print "not ";
            }
            close HEAD;
            $repl_url =~ s/\?/\\?/g;
            $repl_url =~ s/\&/\\&/g;
			if ($query1 eq "")
			{
            	$global_text =~ s/$repl_url${name}/http:\/\/{{IMG_DOMAIN}}\/images\/$img_dir\/$new_name/gi;
			}
			else
			{
            	$global_text =~ s/$repl_url/http:\/\/{{IMG_DOMAIN}}\/images\/$img_dir\/$new_name/gi;
			}
			$img_added = 1;
        }
	}
}


sub init {
    for (my $i=0; $i < @ARGV; $i++) {
        if ($ARGV[$i] eq '-d') {
            $hrINIT->{debug}=1;
        }
        elsif ($ARGV[$i] eq '-v') {
            $hrINIT->{verbose}=1;
        }
        elsif ($ARGV[$i] eq '--3P') {
            $hrINIT->{TP}=$ARGV[$i + 1];
        }
        elsif ($ARGV[$i] eq '--crtvID') {
            $hrINIT->{crtvID}=$ARGV[$i + 1];
        }
        elsif ($ARGV[$i] eq '--brandID') {
            $hrINIT->{bID}=$ARGV[$i + 1];
        }
    }
    $hrINIT->{debug}||=0;
    $hrINIT->{verbose}||=0;
    $hrINIT->{TP}||='';
    $hrINIT->{crtvID}||='';
    $hrINIT->{bID}||='';
    die "usage: $0 --crtvID [CrtvID] --brand [BrID]" unless $hrINIT->{crtvID} && $hrINIT->{bID};
}

