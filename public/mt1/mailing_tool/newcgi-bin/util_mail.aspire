################################################################
####   util_mail.pm  - utility package for PMS that handles	 ####
####				  mailing and previewing of mail         ####
#################################################################

package util_mail;

use strict;
use vars '$AUTOLOAD';
use CGI;
use util;
use Lib::Database::Perl::Interface::Server;
use HTML::LinkExtor;
use WWW::Curl::easy;
use URI::Split qw(uri_split uri_join);
use File::Basename;
use Net::SMTP::TLS;
use Ecelerity::Injector;
my $util;
my $global_dbh;
my $global_text;
my $global_added;
my $g_link_id;
my $BASE_DIR;
my $dname;
my $subdomain_name;
my $city;
my $state;
my $zip;
my $fdomain;
my $img_cnt;
my $aspireurl;
my @user_arr = (
    ["",""],
    ["xlmx.com","xlmx.com"],
    ["Magneticmix.com","magneticmix.com"],
    ["OpenGains.com","opengains.com"],
    ["xlmx.com","xlmx.com"]
);

sub initialize 
{
	my $self = shift;
	unless (defined $util)
	{
		$util = util->new; 
	}
	$aspireurl=$util->getAspireURL();
}

sub AUTOLOAD
{
	my ($self) = @_;
	$AUTOLOAD =~ /.*::get(_\w+)/;
	exists $self->{$1};
	return $self->{$1}	# return attribute
}

sub new 
{
	my $this = shift;
	my $class = ref($this) || $this;
	my $self = {};
	bless $self, $class;
	$self->initialize();
	return $self;
}

sub clean_up
{
	my $self = shift;
	exit(0);
}

sub get_html
{
	my ($dbh,$camp_id) = @_;
	unless (defined $util)
	{
		$util = util->new; 
	}
	my $sth1;
	my $sql;
	my $html_template;

	$global_dbh = $dbh;
		
	$sql = "select html_code from creative where creative_id=$camp_id";
	$sth1 = $dbh->prepare($sql);
	$sth1->execute();
	($html_template) = $sth1->fetchrow_array();
	$sth1->finish();
	return $html_template;
}
# **********************************************************************************
#	This routine is used for previewing an email
# **********************************************************************************

sub mail_preview
{
	my ($dbh,$camp_id,$format,$email_addr,$email_user_id,$user_id,$footer_color,$aid,$internal_flag,$unsub_url,$unsub_img,$content_id,$subname,$cdeploy,$unsub_use,$unsub_text) = @_;
	unless (defined $util)
	{
		$util = util->new; 
	}

	my $sth1;
	my $sql;
	my $template_text;
	my $html_template;
	my $text_template;
	my $aol_template;
	my $new_text;
	my $new_text1;
	my ($cwc3,$cwcid,$cwprogid,$cr,$landing_page);

	# Get the text for this campaign
	$global_dbh = $dbh;
		
	$sql = "select html_code,comm_wizard_c3,comm_wizard_cid,comm_wizard_progid,cr,landing_page from creative where creative_id=$camp_id";
	$sth1 = $dbh->prepare($sql);
	$sth1->execute();
	($html_template,$cwc3,$cwcid,$cwprogid,$cr,$landing_page) = $sth1->fetchrow_array();
	$sth1->finish();

	my $rstr=util::get_random();
   	$html_template=~ s/{{IMG_DOMAIN}}/affiliateimages.com\/$rstr/g;

	# Select template to use based on format user can accept
	$template_text = $html_template;
	
	# Call routine to get the campaigns info and do all the substitution in the template
	# it returns the template text with the fields substitutied with the data
						
	$new_text = template_substit($dbh,$camp_id,$email_addr,$template_text,$format,$email_user_id,$new_text1,$user_id,$footer_color,$aid,$internal_flag,$unsub_url,$unsub_img,$content_id,$subname,$cdeploy,$cwc3,$cwcid,$cwprogid,$cr,$landing_page,$unsub_use,$unsub_text);
	return ($new_text);
}

# ****************************************************************************
#	This routine is used for previewing an email
# ******************************************************************************
sub mail_draft_preview
{
	my ($dbh,$camp_id,$format,$email_addr,$email_user_id,$user_id,$footer_color,$aid,$internal_flag,$unsub_url,$unsub_img,$content_id,$subname,$cdeploy) = @_;
	unless (defined $util)
	{
		$util = util->new; 
	}

	my $sth1;
	my $sql;
	my $html_template;
	my $text_template;
	my $aol_template;
	my $new_text;
	my $new_text1;

	# Get the text for this campaign
	$global_dbh = $dbh;
		
	$sql = "select html_code from draft_creative where creative_id=$camp_id";
	$sth1 = $dbh->prepare($sql);
	$sth1->execute();
	($html_template) = $sth1->fetchrow_array();
	$sth1->finish();

	# Call routine to get the campaigns info and do all the substitution in the template
	# it returns the template text with the fields substitutied with the data
	$new_text = template_substit($dbh,$camp_id,$email_addr,$html_template,$format,$email_user_id,$new_text1,$user_id,$footer_color,$aid,$internal_flag,$unsub_url,$unsub_img,$content_id,$subname,$cdeploy,'',0,0,'','',"IMAGE","");
	return ($new_text);
}

# *************************************************************************************
# sub mail_sendtest
# This routine is used for sending a single test email or a "Tell a Friend" email
# *************************************************************************************

sub mail_sendtest
{
	my ($dbh,$camp_id,$cemail,$email_type,$friend_email_user_id,$user_id,$cdeploy,$cdraft,$ip) = @_;

	unless (defined $util)
	{
		$util = util->new; 
	}
	$aspireurl=$util->getAspireURL();
	my $sth;
	my $sql;
	my $subject;
	my $from_addr;
	my $email_mgr_addr;
	my $email_user_id;
	my $rows;
	my $errmsg;
	my $footer_color;
	my $aid;
	my $internal_flag;
	my $unsub_url;
	my $unsub_img;
	my $cunsub_img;
	my $content_id;
	my $subname;
	my $footer_content_id;
	my $the_email;
	my $unsub_use;
	my $unsub_text;

	# default the email_type flag to HTML if it is blank

	if ($email_type eq "") 
	{
		$email_type = "H";
	}

	# lookup this email address to get this person's id.  Add them if they don't exist
	# in the email_user table

	$email_user_id = 0;
	$g_link_id = 0;

	# Get the mail information for the campaign being used
	if ($cdraft eq "Y")
	{
		$sql = "select draft_creative.advertiser_id,track_internally,unsub_link,advertiser_info.unsub_image from draft_creative,advertiser_info where creative_id=$camp_id and draft_creative.advertiser_id=advertiser_info.advertiser_id";
		$sth = $dbh->prepare($sql);
		$sth->execute();
		($aid,$internal_flag,$unsub_url,$unsub_img) = $sth->fetchrow_array();
		$sth->finish();
		close(LOG2);
		$unsub_img = "";
		$footer_content_id=0;
		$subject="Subject goes here";
		$from_addr="info\@domain.com";
		# get the mail template and replace with data for this campaign
		$the_email = mail_draft_preview($dbh,$camp_id,$email_type,$cemail,$email_user_id,$user_id,$footer_color,$aid,$internal_flag,$unsub_url,$unsub_img,$content_id,$subname,$cdeploy);
	}
	else
	{
		$sql = "select default_subject,default_from,creative.advertiser_id,track_internally,unsub_link,advertiser_info.unsub_image,creative.unsub_image,content_id,advertiser_info.unsub_use,advertiser_info.unsub_text  from creative,advertiser_info where creative_id=$camp_id and creative.advertiser_id=advertiser_info.advertiser_id";
		$sth = $dbh->prepare($sql);
		$sth->execute();
		if (($subject,$from_addr,$aid,$internal_flag,$unsub_url,$unsub_img,$cunsub_img,$footer_content_id,$unsub_use,$unsub_text) = $sth->fetchrow_array())
		{
			$sth->finish();
			if ($cunsub_img eq "NONE")
			{
				$unsub_img = "";
			}
		}
		$sql = "select advertiser_subject from advertiser_subject where subject_id = $subject";
		$sth = $dbh->prepare($sql);
		$sth->execute();
		($subject) = $sth->fetchrow_array();
		$sth->finish();
		if ($subject eq "")
		{
    		$subject = "No subject selected";
		}
		$sql = "select advertiser_from from advertiser_from where from_id = $from_addr";
		$sth = $dbh->prepare($sql);
		$sth->execute();
		($from_addr) = $sth->fetchrow_array();
		$sth->finish();
		if ($from_addr eq "")
		{
    		$from_addr = "{{FOOTER_SUBDOMAIN}}";
		}
		# get the mail template and replace with data for this campaign
		$the_email = mail_preview($dbh,$camp_id,$email_type,$cemail,$email_user_id,$user_id,$footer_color,$aid,$internal_flag,$unsub_url,$unsub_img,$content_id,$subname,$cdeploy,$unsub_use,$unsub_text);
	}
		if ($internal_flag eq "Y")
		{
			$unsub_url = "http://$subdomain_name/cgi-bin/adv_unsub.cgi?id=$aid&email_addr={{EMAIL_ADDR}}";
		}
		
		$from_addr =~ s/{{DOMAIN}}/$dname/;
        $from_addr =~ s/{{LOC}}/Your Area/;
        $from_addr =~ s/{{CLIENT_NETWORK}}/Peaks Network/;
		my $sth1;
		$sql="select brand_name,subdomain_name from category_brand_info,brandsubdomain_info,client_brand_info,advertiser_info where brandsubdomain_info.category_id=advertiser_info.category_id and advertiser_info.advertiser_id=$aid and brandsubdomain_info.subdomain_id=category_brand_info.subdomain_id and category_brand_info.brand_id=7 and category_brand_info.brand_id=client_brand_info.brand_id";
        $sth1 = $dbh->prepare($sql);
        $sth1->execute();
		my $bname;
		my $sdomain_name;
        ($bname,$sdomain_name) = $sth1->fetchrow_array();
        $sth1->finish();
		$sdomain_name=~ s/{{BRAND}}/$bname/g;
        my $fsubdomain;
        my $rest_str;
        ($fsubdomain,$rest_str) = split '\.',$fdomain;
        $from_addr =~ s/{{FOOTER_SUBDOMAIN}}/$sdomain_name/;
		($mta_ip)=getMTA($ip);
		my $inj = Ecelerity::Injector->new( $mta_ip, 1825, 20);
		my $msg="";
		$_ = $from_addr;
		if (/@/)
		{
 			$msg.="From: $from_addr\n";
		}
		else
		{
			my $temp_from = $from_addr;
			$temp_from =~ s/ //g;
 			$msg.="From: $from_addr <$temp_from\@$sdomain_name.$dname>\n";
		}
   		$msg.="To: $cemail\n";
		my $tstr;
		($tstr,$rest_str) = split ',',$cemail;
		$subject =~ s/{{EMAIL_ADDR}}/$tstr/;
		$subject =~ s/{{DOMAIN}}/$dname/;
		$subject =~ s/{{SUB_DOMAIN}}/$subdomain_name/;
		my $tstr;
		($tstr,$rest_str) = split ',',$cemail;
		$subject =~ s/{{NAME}}/$tstr/;
        $subject =~ s/{{LOC}}/Your Area/;
        $subject =~ s/{{CLIENT_NETWORK}}/Peaks Network/;
        $subject =~ s/{{ZIP}}/Your Area/;
        $subject =~ s/{{NOOFOFFENDERS}}/numerous/g;
		my $tstr;
		($tstr,$rest_str) = split ',',$cemail;
        $subject =~ s/{{FULLNAME}}/$tstr/;
   		$msg.="Reply-To: approval\@zetainteractive.com\n";
		my $sth1;
		my $cname;
		my $aname;
		if ($cdraft eq "Y")
		{
        	$sql = "select creative_name,advertiser_name from draft_creative,advertiser_info where creative_id=$camp_id and draft_creative.advertiser_id=advertiser_info.advertiser_id"; 
		}
		else
		{
        	$sql = "select creative_name,advertiser_name from creative,advertiser_info where creative_id=$camp_id and creative.advertiser_id=advertiser_info.advertiser_id"; 
		}
        $sth1 = $dbh->prepare($sql);
        $sth1->execute();
        ($cname,$aname) = $sth1->fetchrow_array();
        $sth1->finish();
   		$msg.="Subject: [TEST - $aname - $cname] $subject\n";
		my $date_str = $util->date(6,6);
		$msg.="Date: $date_str\n";
		$msg.="X-Destination-ID: $cemail:$camp_id:\n";
		
		# add header for mail type

		if ($email_type eq "H" || $email_type eq "A")
		{
			# Add special header for HTML e-mail
			$msg.="Mime-Version: 1.0\n";
    		$msg.="Content-Type: text/html; charset=us-ascii\n\n";
		}
		elsif (($email_type eq "T") || ($email_type eq "D"))
		{
			# Add special header for text e-mail
        	$msg.="Content-Type: text/plain; charset=\"iso-8859-1\"\n\n";
		}

		# print out the mail body

		open (LOG,"> /tmp/a.a");
    srand(rand time());
    my @c=split(/ */, "bcdfghjklmnprstvwxyz");
    my @v=split(/ */, "aeiou");
    my $sname;
    $sname = $c[int(rand(20))];
    $sname = $sname . $v[int(rand(5))];
    $sname = $sname . $c[int(rand(20))];
    $sname = $sname . $v[int(rand(5))];
    $sname = $sname . $c[int(rand(20))];
    $sname = $sname . int(rand(999999));
    $sql = "insert into approval_list(advertiser_id,uid,date_added) values($aid,'$sname',now())";
    my $rows=$dbh->do($sql);
		my $temp_email = "approval\@zetainteractive.com";
    	#$the_email =~ s\{{HEAD}}\This approval e-mail has been sent to you from XL Marketing.&nbsp; For approval of these ads and variations of this campaign (subject lines, from lines, tracking URLs), please visit our <a target="_blank" href="${aspireurl}cgi-bin/advapproval.cgi?aid=$aid&amp;uid=$sname">advertiser approval page</a>. Email Approval at <a href="mailto:$temp_email">$temp_email</a> or call Neal at 212-880-2510 x3411 with questions/concerns</p>\;
    	$the_email =~ s\{{HEAD}}\This approval e-mail has been sent to you.&nbsp; For approval of these ads and variations of this campaign (subject lines, from lines, tracking URLs), please visit our <a target="_blank" href="${aspireurl}cgi-bin/advapproval.cgi?aid=$aid&amp;uid=$sname">advertiser approval page</a>. Email Approval at <a href="mailto:$temp_email">$temp_email</a> or call Neal at 212-880-2510 x3411 with questions/concerns</p>\;
		if ($footer_content_id > 0)
		{
			my $content_html;
			$sql="select content_html from footer_content where content_id=$footer_content_id";
        	$sth1 = $dbh->prepare($sql);
        	$sth1->execute();
        	($content_html) = $sth1->fetchrow_array();
			$sth1->finish();
			$the_email =~ s/<\/body>/$content_html<\/body>/; 
			$the_email =~ s/<\/BODY>/$content_html<\/BODY>/; 
		}
		print LOG $the_email;
		close LOG;
		$msg.="<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n";
		$msg.=$the_email;
		$inj->mail($tstr,$tstr,$msg,0);
}

# *************************************************************************************
# sub template_substit()
# this routine builds the body of an email, by looking up all the campaigns values
# and reading the campaigns template and substituting all the fields.
# *************************************************************************************

sub template_substit()
{
	my ($dbh,$camp_id,$email_addr,$template_text,$format,$email_user_id,$new_text1,$user_id,$footer_color,$aid,$internal_flag,$unsub_url,$unsub_img,$content_id,$subname,$cdeploy,$cwc3,$cwcid,$cwprogid,$cr,$landing_page,$unsub_use,$unsub_text) = @_;
	my $EDEALSDIRECT_USER = 33;
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
	my $tell_a_friend;
	my $bin_dir_http;
	my $ads_url;
	my $top_ad_opt;
	my $top_ad_code;
	my $bottom_ad_opt;
	my $bottom_ad_code;
	my $client_id;
	my $email_footer;
	my $hidden_text;
	my $timestr;
	my $curtime;
	my $physical_addr;
	my $temp_str;
	my $refurl;
	my $content_str;

    use URI::Escape;

	# find some system parameters

	if ($internal_flag eq "Y")
	{
		$unsub_url = "http://{{DOMAIN}}/cgi-bin/adv_unsub.cgi?id=$aid&email_addr={{EMAIL_ADDR}}";
	}
	$sql = "select parmval from sysparm where parmkey = 'BIN_DIR_HTTP'";
	$sth2 = $dbh->prepare($sql);
	$sth2->execute();
	($bin_dir_http) = $sth2->fetchrow_array();
	$sth2->finish();

	# If tracking in text then replace with correct information
	
	$_ = $template_text;
    $template_text =~ s\{{CID}}\$camp_id\g;
    $template_text =~ s\<BODY\<body\;
    my $pos1 = index($template_text, "<body");
    my $pos2 = index($template_text, ">",$pos1);
    substr($template_text,$pos1,$pos2-$pos1+1) = "<body>";
	if ($cdeploy eq "Y")
	{
		$tracking_str = "";
	}
	else
	{
		$tracking_str = "<IMG SRC=\"http://{{IMG_DOMAIN}}/cgi-bin/open_email1.cgi?id=$email_user_id&amp;cid=$camp_id\" border=0 height=1 width=1>";
	}
##    $template_text =~ s\<body>\<body>{{HEAD}}<p STYLE="font-size:10pt; font-family:arial"><center>{{NAME}}, If you cannot view the images in this message, enable images or <a href="{{URL}}">visit here</a> to see this epromo.</p></center><br>${tracking_str}\;
    $template_text =~ s\<body>\<body>{{HEAD}}<p STYLE="font-size:10pt; font-family:arial">${tracking_str}\;
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
	#
	# Check for CONTENT_HEADER tag
	#
	$content_str = "";
	$_ = $template_text;
	if ((/{{CONTENT_HEADER}}/) && ($content_id >= 2))
	{
		if ($content_id == 2)
		{
			$content_str = "<center><table cellSpacing=\"0\" cellPadding=\"0\" width=\"600\" border=\"0\" id=\"table37\"><tr style=\"BACKGROUND-COLOR: #668099\" bgColor=\"#668099\"><td style=\"TEXT-ALIGN: left\" vAlign=\"top\" width=\"20\"><img height=\"15\" src=\"images/spacer.gif\" width=\"5\" border=\"0\"></td><td style=\"PADDING-LEFT: 5px; TEXT-ALIGN: center\" width=\"100%\"><font face=\"Verdana\" color=\"#FFFFFF\"><span style=\"FONT-FAMILY: Arial; font-weight:700\">Daily Horoscopes</span></font></td><td style=\"TEXT-ALIGN: right\" vAlign=\"top\" width=\"20\"><img height=\"20\" alt=\")\" src=\"http://www.{{IMG_DOMAIN}}/images/templates/diag_corner_tr.gif\" width=\"20\" border=\"0\"></td></tr></table><table style=\"BACKGROUND-COLOR: #668099\" cellSpacing=\"2\" cellPadding=\"0\" width=\"600\" bgColor=\"#668099\" border=\"0\" id=\"table38\"><tr><td style=\"BACKGROUND-COLOR: #99cccc\" bgColor=\"#99cccc\">&nbsp;</td></tr><tr style=\"BACKGROUND-COLOR: #ffffff\" bgColor=\"#ffffff\"><td style=\"PADDING-LEFT: 5px\" vAlign=\"top\" align=\"left\"><font face=\"Arial\" size=\"2\"><br>For centuries, horoscopes and astrology have been used to help guide us and better understand who we are. Our horoscopes cover many areas including love, friendships, and business. <br><br><b>Check out your daily horoscope below.</b></font></td></tr><tr><td style=\"BACKGROUND-COLOR: #ffffff\" vAlign=\"top\" align=\"left\" bgColor=\"#ffffff\"></td></tr></table><table cellSpacing=\"0\" cellPadding=\"0\" width=\"600\" border=\"0\" id=\"table40\"><tr style=\"BACKGROUND-COLOR: #668099\" bgColor=\"#668099\"><td style=\"TEXT-ALIGN: left\" vAlign=\"bottom\"><img height=\"20\" src=\"http://www.{{IMG_DOMAIN}}/images/templates/diag_corner_bl.gif\" width=\"20\" border=\"0\"></td><td style=\"TEXT-ALIGN: right\" vAlign=\"bottom\"><img height=\"20\" src=\"http://www.{{IMG_DOMAIN}}/images/templates/diag_corner_br.gif\" width=\"20\" border=\"0\"></td></tr></table><font face=\"Arial\" color=\"#808080\"><b>Today's Offer</b></font>";
		}
		elsif ($content_id == 3)
		{
    		open(IN3,"</var/www/util/data/almanac_header.txt") || print "Error - could not open input file: /var/www/util/data/almanac_header.txt";
    		while (<IN3>)
    		{
				$content_str = $content_str . $_;
			}
			close(IN3);
			$content_str =~ s/&apos;/'/g;
		}
		elsif ($content_id == 4)
		{
    		open(IN3,"</var/www/util/data/topnews_header.txt") || print "Error - could not open input file: /var/www/util/data/topnews_header.txt";
    		while (<IN3>)
    		{
				$content_str = $content_str . $_;
			}
			close(IN3);
			$content_str =~ s/&apos;/'/g;
		}
		elsif ($content_id == 5)
		{
    		open(IN3,"</var/www/util/data/news_header.txt") || print "Error - could not open input file: /var/www/util/data/news_header.txt";
    		while (<IN3>)
    		{
				$content_str = $content_str . $_;
			}
			close(IN3);
			$content_str =~ s/&apos;/'/g;
		}
	}
	$template_text =~ s/{{CONTENT_HEADER}}/$content_str/g;
	#
	# Check for CONTENT_HEADER_TEXT tag
	#
	$content_str = "";
	$_ = $template_text;
	if ((/{{CONTENT_HEADER_TEXT}}/) && ($content_id >= 2))
	{
		if ($content_id == 2)
		{
			$content_str = "<center><table cellSpacing=\"0\" cellPadding=\"0\" width=\"600\" border=\"0\" id=\"table37\"><tr style=\"BACKGROUND-COLOR: #668099\" bgColor=\"#668099\"><td style=\"TEXT-ALIGN: left\" vAlign=\"top\" width=\"20\"><img height=\"15\" src=\"images/spacer.gif\" width=\"5\" border=\"0\"></td><td style=\"PADDING-LEFT: 5px; TEXT-ALIGN: center\" width=\"100%\"><font face=\"Verdana\" color=\"#FFFFFF\"><span style=\"FONT-FAMILY: Arial; font-weight:700\">Daily Horoscopes</span></font></td><td style=\"TEXT-ALIGN: right\" vAlign=\"top\" width=\"20\"><img height=\"20\" alt=\")\" src=\"http://www.{{IMG_DOMAIN}}/images/templates/diag_corner_tr.gif\" width=\"20\" border=\"0\"></td></tr></table><table style=\"BACKGROUND-COLOR: #668099\" cellSpacing=\"2\" cellPadding=\"0\" width=\"600\" bgColor=\"#668099\" border=\"0\" id=\"table38\"><tr><td style=\"BACKGROUND-COLOR: #99cccc\" bgColor=\"#99cccc\">&nbsp;</td></tr><tr style=\"BACKGROUND-COLOR: #ffffff\" bgColor=\"#ffffff\"><td style=\"PADDING-LEFT: 5px\" vAlign=\"top\" align=\"left\"><font face=\"Arial\" size=\"2\"><br>For centuries, horoscopes and astrology have been used to help guide us and better understand who we are. Our horoscopes cover many areas including love, friendships, and business. <br><br><b>Check out your daily horoscope below.</b></font><br><br><hr><center><b>Today's Offer</b></center>";
		}
		elsif ($content_id == 3)
		{
    		open(IN3,"</var/www/util/data/almanac_header_text.txt") || print "Error - could not open input file: /var/www/util/data/almanac_header_text.txt";
    		while (<IN3>)
    		{
				$content_str = $content_str . $_;
			}
			close(IN3);
			$content_str =~ s/&apos;/'/g;
		}
		elsif ($content_id == 4)
		{
    		open(IN3,"</var/www/util/data/topnews_header_text.txt") || print "Error - could not open input file: /var/www/util/data/topnews_header_text.txt";
    		while (<IN3>)
    		{
				$content_str = $content_str . $_;
			}
			close(IN3);
			$content_str =~ s/&apos;/'/g;
		}
		elsif ($content_id == 5)
		{
    		open(IN3,"</var/www/util/data/news_header_text.txt") || print "Error - could not open input file: /var/www/util/data/news_header_text.txt";
    		while (<IN3>)
    		{
				$content_str = $content_str . $_;
			}
			close(IN3);
			$content_str =~ s/&apos;/'/g;
		}
	}
	$template_text =~ s/{{CONTENT_HEADER_TEXT}}/$content_str/g;
	#
	# Check to see if content tag
	#
	$content_str = "";
	$_ = $template_text;
	$dname = "wealthpurse.com";
	if ((/{{CONTENT}}/) && ($content_id >= 2))
	{
		if ($content_id == 2)
		{
    		open(IN2,"</var/www/util/data/horoscope.txt") || print "Error - could not open input file: /var/www/util/data/horoscope.txt";
			$dname = "horoscopes365.com";
    	while (<IN2>)
    	{
			$content_str = $content_str . $_;
		}
		close(IN2);
		$content_str =~ s/&apos;/'/g;
		}
		elsif ($content_id == 3)
		{
    		open(IN2,"</var/www/util/data/almanac.txt") || print "Error - could not open input file: /var/www/util/data/almanac.txt";
			$dname = "todaysalmanac.com";
    	while (<IN2>)
    	{
			$content_str = $content_str . $_;
		}
		close(IN2);
		$content_str =~ s/&apos;/'/g;
		}
		elsif ($content_id == 4)
		{
    		open(IN2,"</var/www/util/data/topnews.txt") || print "Error - could not open input file: /var/www/util/data/topnews.txt";
			$dname = "entertainmentnewsclips.com";
    	while (<IN2>)
    	{
			$content_str = $content_str . $_;
		}
		close(IN2);
		$content_str =~ s/&apos;/'/g;
		}
		elsif ($content_id == 5)
		{
    		open(IN2,"</var/www/util/data/news.txt") || print "Error - could not open input file: /var/www/util/data/news.txt";
			$dname = "bizarreheadlines.com";
    	while (<IN2>)
    	{
			$content_str = $content_str . $_;
		}
		close(IN2);
		$content_str =~ s/&apos;/'/g;
		}
		elsif ($content_id == 6)
		{
			$dname="savingsalon.com";
		}	

	}
	$template_text =~ s/{{CONTENT}}/$content_str/g;
	#
	# Check to see if content_text tag
	#
	$content_str = "";
	$_ = $template_text;
	if ((/{{CONTENT_TEXT}}/) && ($content_id >= 2))
	{
		if ($content_id == 2)
		{
    		open(IN2,"</var/www/util/data/horoscope_text.txt") || print "Error - could not open input file: /var/www/util/data/horoscope_text.txt";
			$dname = "horoscopes365.com";
    	while (<IN2>)
    	{
			$content_str = $content_str . $_;
		}
		close(IN2);
		$content_str =~ s/&apos;/'/g;
		}
		elsif ($content_id == 3)
		{
    		open(IN2,"</var/www/util/data/almanac_text.txt") || print "Error - could not open input file: /var/www/util/data/almanac_text.txt";
			$dname = "todaysalmanac.com";
    	while (<IN2>)
    	{
			$content_str = $content_str . $_;
		}
		close(IN2);
		$content_str =~ s/&apos;/'/g;
		}
		elsif ($content_id == 4)
		{
    		open(IN2,"</var/www/util/data/topnews_text.txt") || print "Error - could not open input file: /var/www/util/data/topnews_text.txt";
			$dname = "entertainmentnewsclips.com";
    	while (<IN2>)
    	{
			$content_str = $content_str . $_;
		}
		close(IN2);
		$content_str =~ s/&apos;/'/g;
		}
		elsif ($content_id == 5)
		{
    		open(IN2,"</var/www/util/data/news_text.txt") || print "Error - could not open input file: /var/www/util/data/news_text.txt";
			$dname = "bizarreheadlines.com";
    	while (<IN2>)
    	{
			$content_str = $content_str . $_;
		}
		close(IN2);
		$content_str =~ s/&apos;/'/g;
		}
		elsif ($content_id == 6)
		{
			$dname="savingsalon.com";
		}	

	}
	srand(rand time());
	my @c=split(/ */, "bcdfghjklmnprstvwxyz");
	my @v=split(/ */, "aeiou");
	my $sname;
	$sname = $c[int(rand(20))];
	$sname = $sname . $v[int(rand(5))];
	$sname = $sname . $c[int(rand(20))];
	$sname = $sname . $v[int(rand(5))];
	$sname = $sname . $c[int(rand(20))];
	if ($subname eq "")
	{
		$subdomain_name = "www." . $dname;
	}
	else
	{
		$subdomain_name = $subname . "." . $dname;
	}
	$template_text =~ s/{{CONTENT_TEXT}}/$content_str/g;

	if (/{{HEADER_INFO}}/)
	{
		$tracking_str = "<i>This message is being brought to you by $dname.  If you are receiving this message in error, please see bottom of page.</i><p>";
		$template_text =~ s/{{HEADER_INFO}}/$tracking_str/g;
	}
	if (/{{REFID}}/)
	{
		$template_text =~ s/{{REFID}}/CV38950/g;
	}
	if (/{{TIMESTAMP}}/)
	{
		$timestr = util::date($curtime,5);
		$template_text =~ s/{{TIMESTAMP}}/$timestr/g;
	}
	
	$template_text =~ s/{{CLICK}}//g;

	$contact_url="http://www.$dname";
	$contact_email="offers\@$dname";
	$contact_name="$dname Offers";
	$contact_company="$dname";

		# Add special Unsubscribe footer to the bottom of every email - 
		# this is hard coded here because the users cannot remove this from the emails that
		# go out

		if ($format eq "H" or $format eq "A")
		{
			if ($format eq "H")
			{
#				$hidden_text = "<html>
#<!-- " . $new_text1;
#				$hidden_text .= "
#-->";
				$template_text =~ s\<HTML>\<html>\;
#				$template_text =~ s\<html>\$hidden_text\;
			}
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
			$template_text =~ s\</HTML>\</html>\;
			$template_text =~ s\</html>\\;

			# add tell a friend box at the bottom if needed

			if ($tell_a_friend eq "Y")
			{
				$template_text .= qq { <p><center>
<table cellspacing=0 cellpadding=0 width=600 border=0>
<tr><td align=center>
<font face="Verdana,Arial" size="1">
<form action="${bin_dir_http}tellfriend.cgi" method="post">
<input type="hidden" name="id" value="$email_user_id">
<input type="hidden" name="cid" value="$camp_id">
Send a copy of this email to a Friend
<input type="text" size="20" name="femail">
<input type="submit" value="Send"></form>
</td></tr></table>
</center></p>\n };

			}

			# get html email footer from sysparm table

			$sql = "select parmval from sysparm where parmkey = 'HTML_EMAIL_FOOTER'";
			$sth2 = $dbh->prepare($sql);
			$sth2->execute();
			($email_footer) = $sth2->fetchrow_array();
			$sth2->finish();

			# append the html email footer to the end of the email
			$template_text .= $email_footer;

			# now add end body tag to close the html email
			if ($cdeploy eq "Y")
			{
        		$template_text .= "</body></html>";
			}
			else
			{	
#        		$template_text .= "<p STYLE=\"font-size:10pt; font-family:arial\">Intended recipient: {{EMAIL_ADDR}}<br>If you prefer not to receive these epromos from us, <a href=\"http://{{DOMAIN}}/cgi-bin/redir1.cgi?eid={{EMAIL_USER_ID}}&amp;cid=$camp_id&amp;em={{EMAIL_ADDR}}&amp;id=42\" target=_blank>follow this link</a>.</p></body></html>";
        		$template_text .= "</body></html>";
			}

			$_=$template_text;
			#
			# Replace Re-directs
			#
				$global_added = 0;
				$img_cnt = 0;
#				$global_text = $template_text;
#				my $p = HTML::LinkExtor->new(\&cb); 
#				$p->parse($template_text); 
#				$template_text = $global_text;
    		$template_text =~ s/{{CID}}/$camp_id/g;

			# replace unsubscribe field with the proper link

					$temp_str = "";
        			if ($unsub_use eq "TEXT")
        			{
            			$temp_str=$unsub_text;
        			}
        			else
        			{
					if ($unsub_img eq "")
					{
						$sql = "select physical_addr from advertiser_info where advertiser_id=$aid"; 
						$sth2 = $dbh->prepare($sql);
						$sth2->execute();
						($physical_addr) = $sth2->fetchrow_array();
						$sth2->finish();
#						$temp_str = "<font size=-2>To stop receiving email promotions from this ADVERTISER ONLY.<br><a href=\"$unsub_url\">FOLLOW THIS LINK</a> or contact the advertiser at: <br>$physical_addr</font><br><br>";
					}
					else
					{
							my $link_id=0;
							if ($unsub_url ne "")
							{
							$sql = "select link_id from links where refurl='$unsub_url'";
							$sth2 = $dbh->prepare($sql);
							$sth2->execute();
							if (($link_id) = $sth2->fetchrow_array())
							{
								$sth2->finish();
							}
							else
							{
								$sth2->finish();
            					$sql = "insert into links(refurl,date_added) values('$unsub_url',now())";
            					my $rows = $dbh->do($sql);
								$sql = "select link_id from links where refurl='$unsub_url'";
								$sth2 = $dbh->prepare($sql);
								$sth2->execute();
								($link_id) = $sth2->fetchrow_array();
								$sth2->finish();
							}
							}
						if ($cdeploy eq "Y")
						{
        					$_=$unsub_img;
        					if ( /\// )
        					{
								my ($t1,@t2)=split('\/',$unsub_img);
								if (length($t1) == 1)
								{
                       				$temp_str = "<a href=\"unsub_url\" target=\"_blank\"><img src=\"http://www.{{IMG_DOMAIN}}/images/$unsub_img\" border=0 alt=\"Unsub\"></a><br><br>";
								}
								else
								{
                       				$temp_str = "<a href=\"unsub_url\" target=\"_blank\"><img src=\"http://www.{{IMG_DOMAIN}}/images/$unsub_img\" border=0 alt=\"Unsub\"></a><br><br>";
								}
							}
							else
							{
                       			$temp_str = "<a href=\"unsub_url\" target=\"_blank\"><img src=\"http://www.{{IMG_DOMAIN}}/images/unsub/$unsub_img\" border=0 alt=\"Unsub\"></a><br><br>";
							}
						}
						elsif ($cdeploy eq "V")
						{
        					$_=$unsub_img;
        					if ( /\// )
        					{
								my ($t1,@t2)=split('\/',$unsub_img);
								if (length($t1) == 1)
								{
                       				$temp_str = "<a href=\"http://{{DOMAIN}}\" target=\"_blank\"><img src=\"http://www.{{IMG_DOMAIN}}/images/$unsub_img\" border=0 alt=\"Unsub\"></a><br><br>";
								}
								else
								{
                       				$temp_str = "<a href=\"http://{{DOMAIN}}\" target=\"_blank\"><img src=\"http://www.{{IMG_DOMAIN}}/images/$unsub_img\" border=0 alt=\"Unsub\"></a><br><br>";
								}
							}
							else
							{
                       			$temp_str = "<a href=\"http://{{DOMAIN}}\" target=\"_blank\"><img src=\"http://www.{{IMG_DOMAIN}}/images/unsub/$unsub_img\" border=0 alt=\"Unsub\"></a><br><br>";
							}
						}
						else
						{
        					$_=$unsub_img;
        					if ( /\// )
        					{
								my ($t1,@t2)=split('\/',$unsub_img);
								if ($unsub_url ne "")
								{
                       				$temp_str = "<a href=\"http://{{DOMAIN}}/cgi-bin/redir1.cgi?eid={{EMAIL_USER_ID}}&amp;cid=$camp_id&amp;em={{EMAIL_ADDR}}&amp;id=$link_id\" target=\"_blank\"><img src=\"http://www.{{IMG_DOMAIN}}/images/$unsub_img\" border=0 alt=\"Unsub\"></a><br><br>";
								}
								else
								{
                       				$temp_str = "<img src=\"http://www.{{IMG_DOMAIN}}/images/$unsub_img\" border=0 alt=\"Unsub\"><br><br>";
								}
							}
							else
							{
								if ($unsub_url ne "")
								{
                       				$temp_str = "<a href=\"http://{{DOMAIN}}/cgi-bin/redir1.cgi?eid={{EMAIL_USER_ID}}&amp;cid=$camp_id&amp;em={{EMAIL_ADDR}}&amp;id=$link_id\" target=\"_blank\"><img src=\"http://www.{{IMG_DOMAIN}}/images/unsub/$unsub_img\" border=0 alt=\"Unsub\"></a><br><br>";
								}
								else
								{
                       				$temp_str = "<img src=\"http://www.{{IMG_DOMAIN}}/images/unsub/$unsub_img\" border=0 alt=\"Unsub\"><br><br>";
								}
							}
						}
					}
					}
				if ($footer_color eq "WHITE")
				{
					if ($cdeploy eq "Y") 
					{
                		$template_text =~ s\{{UNSUBSCRIBE}}\$temp_str\g;
					}
					elsif ($cdeploy eq "V") 
					{
                		$template_text =~ s\{{UNSUBSCRIBE}}\$temp_str<a href="http://{{DOMAIN}}" target=_blank><img src="http://www.{{IMG_DOMAIN}}/fimg/{{FOOTER_STR}}_1.jpg" border=0 alt="end this subscription"></a><br><a href="http://{{DOMAIN}}/cgi-bin/privacy.cgi" target=_blank><img src="http://www.{{IMG_DOMAIN}}/fimg/{{FOOTER_STR}}_2.jpg" border=0 alt="privacy policy"></a>\g;
					}
					else
					{
                		$template_text =~ s\{{UNSUBSCRIBE}}\$temp_str<a href="http://{{DOMAIN}}/cgi-bin/unsubscr1.cgi?email={{EMAIL_USER_ID}}&amp;cid=$camp_id&amp;em={{EMAIL_ADDR}}" target=_new><img src="http://www.{{IMG_DOMAIN}}/fimg/{{FOOTER_STR}}_1.jpg" border=0 alt="end this subscription"></a><br><a href="http://{{DOMAIN}}/cgi-bin/privacy.cgi" target=_new><img src="http://www.{{IMG_DOMAIN}}/fimg/{{FOOTER_STR}}_2.jpg" border=0 alt="privacy policy"></a>\g;
					}
				}
				else
				{
					if ($cdeploy eq "Y") 
					{
                		$template_text =~ s\{{UNSUBSCRIBE}}\$temp_str\g;
					}
					elsif ($cdeploy eq "V") 
					{
                		$template_text =~ s\{{UNSUBSCRIBE}}\$temp_str<a href="http://{{DOMAIN}}" target=_new><img src="http://www.{{IMG_DOMAIN}}/fimg/{{FOOTER_STR}}_1.jpg" border=0 alt="end this subscription"></a><br><a href="http://{{DOMAIN}}/cgi-bin/privacy.cgi" target=_new><img src="http://www.{{IMG_DOMAIN}}/fimg/{{FOOTER_STR}}_2.jpg" border=0 alt="privacy policy"></a>\g;
					}
					else
					{
                		$template_text =~ s\{{UNSUBSCRIBE}}\$temp_str<a href="http://{{DOMAIN}}/cgi-bin/unsubscr1.cgi?email={{EMAIL_USER_ID}}&amp;cid=$camp_id&amp;em={{EMAIL_ADDR}}" target=_new><img src="http://www.{{IMG_DOMAIN}}/fimg/{{FOOTER_STR}}_1.jpg" border=0 alt="end this subscription"></a><br><a href="http://{{DOMAIN}}/cgi-bin/privacy.cgi" target=_new><img src="http://www.{{IMG_DOMAIN}}/fimg/{{FOOTER_STR}}_2.jpg" border=0 alt="privacy policy"></a>\g;
					}
				}

			$_=$template_text;
			if (/{{ADV_UNSUB_URL}}/)
			{

				my $link_id=0;
				if ($unsub_url ne "")
				{
				$sql = "select link_id from links where refurl='$unsub_url'";
				$sth2 = $dbh->prepare($sql);
				$sth2->execute();
				if (($link_id) = $sth2->fetchrow_array())
				{
					$sth2->finish();
				}
				else
				{
					$sth2->finish();
            		$sql = "insert into links(refurl,date_added) values('$unsub_url',now())";
            		my $rows = $dbh->do($sql);
					$sql = "select link_id from links where refurl='$unsub_url'";
					$sth2 = $dbh->prepare($sql);
					$sth2->execute();
					($link_id) = $sth2->fetchrow_array();
					$sth2->finish();
				}	
				}
				$template_text=~s/{{ADV_UNSUB_URL}}/http:\/\/{{DOMAIN}}\/cgi-bin\/redir1.cgi?eid={{EMAIL_USER_ID}}&amp;cid=$camp_id&amp;em={{EMAIL_ADDR}}&amp;id=$link_id/g; 
			}
			# substitute <br> for the carriage returns if displaying html page

			$introduction =~ s/\n/<br>/g;
			$closing =~ s/\n/<br>/g;
			$promotion_desc =~ s/\n/<br>/g;
		}
		else
		{
			# get text email footer from sysparm table

			$sql = "select parmval from sysparm where parmkey = 'TEXT_EMAIL_FOOTER'";
			$sth2 = $dbh->prepare($sql);
			$sth2->execute();
			($email_footer) = $sth2->fetchrow_array();
			$sth2->finish();

			# add unsubscribe footer for text emails
			if ($camp_id != 0) 
			{
					$template_text .= "--------------------------------------------------------------
This is an email promotion being sent to you by $dname. 
To stop all future mailings, follow the link below or send your email address to:
                 $dname
					350 Third Avenue #717
					New York, NY  10010
http://{{DOMAIN}}/cgi-bin/unsubscr1.cgi?email=$email_user_id&amp;cid=$camp_id&amp;em={{EMAIL_ADDR}}";
					# append the text email footer from sysparms
					if ($aid != 1)
					{
						my $physical_addr;
						$sql = "select physical_addr from advertiser_info where advertiser_id=$aid"; 
						$sth2 = $dbh->prepare($sql);
						$sth2->execute();
						($physical_addr) = $sth2->fetchrow_array();
						$sth2->finish();
						$template_text .= "

To stop receiving email promotions from this ADVERTISER ONLY.
Follow the link below or contact the advertiser at:

$physical_addr
$unsub_url";
					}
					$template_text .= $email_footer;
			}
		}

		# contact fields

    	$template_text =~ s/{{CONTACT_EMAIL}}/$contact_email/g;
    	$template_text =~ s/{{CONTACT_URL}}/$contact_url/g;
#    	$template_text =~ s/{{CONTACT_PHONE}}/$contact_phone/g;
    	$template_text =~ s/{{CONTACT_NAME}}/$contact_name/g;
    	$template_text =~ s/{{CONTACT_COMPANY}}/$contact_company/g;

		# removed this field - not using any more
    	#$template_text =~ s\{{EDIT}}\<a href="${bin_dir_http}edit_member.cgi?email=$email_user_id">edit</a>\g;

		# personalization fields

		my $temp_id = rand();
		#
		# Get the link_id for Tonicnetwork
		#
		my $link_id;
		if ($g_link_id != 0)
	    {
			$link_id = $g_link_id;
		}
		else
		{	
        	$sql = "select link_id from advertiser_tracking where advertiser_id=$aid and client_id=1 and daily_deal='N' and link_num=1";
        	$sth1 = $dbh->prepare($sql);
        	$sth1->execute();
        	($link_id) = $sth1->fetchrow_array();
        	$sth1->finish();
		}
		if ($cdeploy eq "Y")
		{			
			$template_text =~ s/{{URL}}/%url%/g;
		}
		elsif ($cdeploy eq "V")
		{		
			$template_text =~ s/{{URL}}/http:\/\/{{DOMAIN}}/g;
		}
		else
		{		
			my $t1str="http:\/\/{{DOMAIN}}\/cgi-bin\/redir1.cgi?eid={{EMAIL_USER_ID}}&amp;cid=$camp_id&amp;em={{EMAIL_ADDR}}&amp;id=$link_id";
			if ($cwc3 eq "")
			{
			}
			else
			{
				$t1str=$t1str . "&amp;cwc3=$cwc3";
			}
			if (($cwcid eq "") or ($cwcid == 0))
			{
			}
			else
			{
				$t1str=$t1str . "&amp;cwcid=$cwcid";
			}
			if (($cwprogid eq "") or ($cwprogid == 0))
			{
			}
			else
			{
				$t1str=$t1str . "&amp;cwprogid=$cwprogid";
			}
			if ($cr eq "") 
			{
			}
			else
			{
				$t1str=$t1str . "&amp;cr=$cr";
			}
			if (($landing_page eq "") or ($landing_page == 0))
			{
			}
			else
			{
				$t1str=$t1str . "&amp;l=$landing_page";
			}
			$template_text =~ s/{{URL}}/$t1str/g;
			my $i=1;
			while ($i <= 29)
			{
				$_=$template_text;
				if (/{{URL$i}}/)
				{
					my $tlink=getLinkID($dbh,$aid,$i);
					my $temps=$t1str;
					$temps=~s/id=$link_id/id=$tlink/g;
					$template_text =~ s/{{URL$i}}/$temps/g;
				}
				$i++;
			}
		}
		my $tstr;
        my $rest_str;
		($tstr,$rest_str) = split ',',$email_addr;
    	$template_text =~ s/{{EMAIL_ADDR}}/$tstr/g;
    	$template_text =~ s/{{LINK_ID}}/$link_id/g;
    	$template_text =~ s/{{DOMAIN}}/$dname/g;
    	$template_text =~ s/{{IMG_DOMAIN}}/affiliateimages.com/g;
##        $sql = "select domain_name from client_category_info,advertiser_info where advertiser_info.category_id=client_category_info.category_id and advertiser_info.advertiser_id=$aid and user_id=2";
		$sql="select brand_name,subdomain_name,brandsubdomain_info.subdomain_id,mailing_addr1,mailing_addr2 from category_brand_info,brandsubdomain_info,client_brand_info,advertiser_info where brandsubdomain_info.category_id=advertiser_info.category_id and advertiser_info.advertiser_id=$aid and brandsubdomain_info.subdomain_id=category_brand_info.subdomain_id and category_brand_info.brand_id=7 and category_brand_info.brand_id=client_brand_info.brand_id";
        $sth1 = $dbh->prepare($sql);
        $sth1->execute();
		my $bname;
		my $sdomain_name;
		my $sid;
		my $url_id;
		my $curl;
		my $trand;
		my $addr1;
		my $addr2;
        ($bname,$sdomain_name,$sid,$addr1,$addr2) = $sth1->fetchrow_array();
        $sth1->finish();
        $sql = "select url_id,url,rand() from brand_url_info where brand_id=7 and url_type='O' order by 2";
        $sth1 = $dbh->prepare($sql);
        $sth1->execute();
		($url_id,$curl,$trand) = $sth1->fetchrow_array();
		$sth1->finish();	
		$sdomain_name=~ s/{{BRAND}}/$bname/g;
        $template_text =~ s/{{FOOTER_DOMAIN}}/$sdomain_name/g;
##		my $footer_str = "7_" . $sid . "_" . $url_id;
		my $footer_str = "7";
        $template_text =~ s/{{FOOTER_STR}}/$footer_str/g;
        $template_text =~ s/{{CLIENT_BRAND}}/$bname/g;
        $template_text =~ s/{{MAILING_ADDR1}}/$addr1/g;
        $template_text =~ s/{{MAILING_ADDR2}}/$addr2/g;
        $template_text =~ s/{{CLIENT_BRAND}}/$bname/g;
    	$template_text =~ s/{{SUB_DOMAIN}}/$subdomain_name/g;
    	$template_text =~ s/{{NAME}}/Firstname/g;
    	$template_text =~ s/{{FNAME}}/Firstname/g;
    	$template_text =~ s/{{LNAME}}/Lastname/g;
        if ($zip eq "")
        {
            $zip = "Your Area";
        }
        $template_text =~ s/{{ZIP}}/$zip/g;
        $template_text =~ s/{{NOOFOFFENDERS}}/numerous/g;
        $template_text =~ s/{{LOC}}/Your Area/g;
        $template_text =~ s/{{CLIENT_NETWORK}}/Peaks Network/g;
		my $tstr;
        my $rest_str;
		($tstr,$rest_str) = split ',',$email_addr;
        $template_text =~ s/{{FULLNAME}}/$tstr/g;
    	$template_text =~ s/{{EMAIL_USER_ID}}/$email_user_id/g;
    my $temp_str = $util->date(4,4);
    $template_text =~ s/{{DATE}}/$temp_str/g;
    	$template_text =~ s/{{RAND_ID}}/$temp_id/g;
#    	$template_text =~ s/{{FIRSTNAME}}/$first_name/g;
#    	$template_text =~ s/{{LASTNAME}}/$last_name/g;
#    	$template_text =~ s/{{ADDRESS}}/$address/g;
#    	$template_text =~ s/{{ADDRESS2}}/$address2/g;
#    	$template_text =~ s/{{CITY}}/$city/g;
#    	$template_text =~ s/{{STATE}}/$state/g;
#    	$template_text =~ s/{{ZIP}}/$zip/g;
#    	$template_text =~ s/{{COUNTRY}}/$country/g;
#    	$template_text =~ s/{{PHONE}}/$phone/g;
#    	$template_text =~ s/{{BIRTH_DATE}}/$birth_date/g;
#    	$template_text =~ s/{{GENDER}}/$gender/g;

	    # Get the client id for this campaign

#    	$sql = "select user_id from campaign where campaign_id=$camp_id"; 
#    	$sth1 = $dbh->prepare($sql);
#    	$sth1->execute();
#    	($client_id) = $sth1->fetchrow_array();
#    	$sth1->finish();

		# Check to see if need to do Popup Ads
		$template_text =~ s\{{POPUP_AD}}\\g;

		# check if need to add ad banners

		$template_text =~ s\{{TOP_AD}}\\g;
		$template_text =~ s\{{BOTTOM_AD}}\\g;

		$tracking_str = "${bin_dir_http}redir1.cgi?id=$email_user_id&amp;cid=$camp_id&amp;l=";

	return ($template_text);
}

sub repl_template()
{
	my ($dbh,$template_id,$user_id,$format) = @_;
	unless (defined $util)
	{
		$util = util->new; 
	}
	my $sql;
	my $sth;
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
	my $first_name;
	my $last_name;
	my $bin_dir_http;
	my $top_ad_opt;
	my $top_ad_code;
	my $bottom_ad_opt;
	my $bottom_ad_code;
	my $template_text;
	my $email_addr;
	my ($company, $website_url, $company_phone);

	# find some system parameters

	$sql = "select parmval from sysparm where parmkey = 'BIN_DIR_HTTP'";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	($bin_dir_http) = $sth->fetchrow_array();
	$sth->finish();

	# read the template

	$sql = "select ";
	if ($format eq "H")
	{
		$sql = $sql . "html_template ";
	}
	elsif ($format eq "T")
	{
		$sql = $sql . "text_template ";
	}
	elsif ($format eq "A")
	{
		$sql = $sql . "aol_template ";
	}
	$sql = $sql . "from template where template_id = $template_id";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	($template_text) = $sth->fetchrow_array();
	$sth->finish();

	# read current users email address and other stuff

	$sql = "select first_name, last_name, email_addr, company, website_url,
		company_phone from user where user_id = $user_id";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	($first_name, $last_name, $email_addr, $company, $website_url,
        $company_phone) = $sth->fetchrow_array();
	$sth->finish();

	# read defaults for this template

	if ($format ne "T")
	{
		# substitute <br> for the carriage returns if displaying html page
		$introduction =~ s/\n/<br>/g;
		$closing =~ s/\n/<br>/g;
		$promotion_desc =~ s/\n/<br>/g;
	}

	$template_text =~ s/{{TRACKING}}//g;
	$template_text =~ s/{{CONTENT}}//g;
	$template_text =~ s/{{CLICK}}//g;
	$template_text =~ s/{{TITLE}}/$title/g;
    $template_text =~ s/{{SUBTITLE}}/$subtitle/g;
    $template_text =~ s/{{DATE_STR}}/$date_str/g;
	my $temp_str = $util->date(4,4);
    $template_text =~ s/{{DATE}}/$temp_str/g;
    $template_text =~ s/{{GREETING}}/$greeting/g;
    $template_text =~ s/{{INTRODUCTION}}/$introduction/g;
    $template_text =~ s/{{CLOSING}}/$closing/g;

    $template_text =~ s/{{CONTACT_EMAIL}}/$email_addr/g;
    $template_text =~ s/{{CONTACT_URL}}/$website_url/g;
    $template_text =~ s/{{CONTACT_PHONE}}/$company_phone/g;
    $template_text =~ s/{{CONTACT_NAME}}/$first_name $last_name/g;
    $template_text =~ s/{{CONTACT_COMPANY}}/$company/g;
    $template_text =~ s/{{EMAIL_ADDR}}/$email_addr/g;
    $template_text =~ s/{{NAME}}/Firstname/g;
    if ($city ne "")
    {
        $template_text =~ s/{{LOC}}/$city $state/g;
    }
    elsif ($state ne "")
    {
        $template_text =~ s/{{LOC}}/$state/g;
    }
    else
    {
        $template_text =~ s/{{LOC}}/Your Area/g;
    }
    $template_text =~ s/{{CLIENT_NETWORK}}/Peaks Network/g;
    if ($zip eq "")
    {
        $zip = "Your Area";
    }
    $template_text =~ s/{{ZIP}}/$zip/g;
        $template_text =~ s/{{NOOFOFFENDERS}}/numerous/g;
    $template_text =~ s/{{FULLNAME}}/$first_name $last_name/g;
	my $temp_id = rand();
   	$template_text =~ s/{{RAND_ID}}/$temp_id/g;
    $template_text =~ s/{{FIRSTNAME}}/$first_name/g;
    $template_text =~ s/{{LASTNAME}}/$last_name/g;
	$template_text =~ s\{{TOP_AD}}\\g;
	$template_text =~ s\{{BOTTOM_AD}}\\g;

	return ($template_text);
}
sub cb 
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

	 #
	 # Process image tags
	 #
     if (($tag eq "img") or ($tag eq "background") or ($url1 eq "background") or (($tag eq "input") and ($url1 eq "src")))
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
            open HEAD, "> /var/www/util/logs/head.out";
			print HEAD "$url2\n";
            ($scheme, $auth, $path, $query, $frag) = uri_split($url2);
            ($name,$frag,$suffix) = fileparse($path);
			print HEAD "$url2\n";
            my $repl_url = $scheme . "://" . $auth . $frag . $name;
            print HEAD "URL = <$repl_url>\n";
            print HEAD "Name = <$name>\n";
            print HEAD "Suffix = <$suffix>\n";
			print HEAD "Img Count = $img_cnt\n";
            my $curl = WWW::Curl::easy->new();
            $curl->setopt(CURLOPT_NOPROGRESS, 1);
#            $curl->setopt(CURLOPT_MUTE, 0);
            $curl->setopt(CURLOPT_FOLLOWLOCATION, 1);
            $curl->setopt(CURLOPT_TIMEOUT, 30);
            $curl->setopt(CURLOPT_WRITEHEADER, *HEAD);
			$img_cnt++;
			($temp_name,$temp_str) = split '\.',$name;
			$temp_name = "img_" . $img_cnt . "." . $temp_str;
            open BODY, "> /var/www/util/tmpimg/$temp_name";
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
            $global_text =~ s/$repl_url${name}/http:\/\/{{IMG_DOMAIN}}\/images\/img_{{CID}}\/$temp_name/gi;
        }
	}
}

# *************************************************************************************
# sub mail_approvaltest
# This routine is used for sending a single test email or a "Tell a Friend" email
# *************************************************************************************

sub mail_approvaltest
{
	my ($dbh,$cemail,$user_id,$aid,$aname,$sname,$internal,$cstatus,@textads) = @_;
    unless (defined $util)
    {
        $util = util->new;
    }
	$aspireurl=$util->getAspireURL();
	my $sth;
	my $sql;
	my $subject;
	my $from_addr;
	my $email_mgr_addr;
	my $email_user_id;
	my $rows;
	my $errmsg;
	my $footer_color;
	my $internal_flag;
	my $unsub_url;
	my $unsub_img;
	my $content_id;
	my $subname;
	my $email_type;
	my $the_email;

	# default the email_type flag to HTML if it is blank
	$email_type = "H";
	$email_user_id = 0;

	my $smtp = Net::SMTP::TLS->new("smtp.gmail.com", Port => 587, User => 'approval@zetainteractive.com', Password => "45MLs!29f");
#	$from_addr = "Spirevision Approval Team <approval\@zetainteractive.com>";
#	$from_addr = "XL Marketing Campaign Setup <approval\@zetainteractive.com>";
	$from_addr = "Campaign Setup <approval\@zetainteractive.com>";
	$smtp->mail($from_addr);
	$_=$cemail;
	if (/,/)
	{
		my @EM=split(',',$cemail);
		foreach my $em (@EM)
		{
			$smtp->to($em);
		}
	}
	else
	{
		$smtp->to($cemail);
	}
	if ($internal != 1)
	{
		$smtp->cc("approval\@zetainteractive.com");
	}
	$smtp->data();
	$smtp->datasend("From: $from_addr\n");
	$smtp->datasend("To: $cemail\n");
	if ($internal != 1)
	{
		$smtp->datasend("CC: approval\@zetainteractive.com\n");
	}
   	$smtp->datasend("Reply-To: approval\@zetainteractive.com\n");
	if ($internal != 1)
	{
#   		$smtp->datasend("Subject: Re: We cannot mail your campaign, $aname yet...\n");
   		$smtp->datasend("Subject: Complete This Approval Page if you Would Like Traffic\n");
	}
	else
	{
#  		$smtp->datasend("Subject: $aname approval needed\n");
   		$smtp->datasend("Subject: Complete This Approval Page if you Would Like Traffic\n");
	}
	my $date_str = $util->date(6,6);
	$smtp->datasend("Date: $date_str\n");
	$smtp->datasend("X-Priority: 1\n");
	$smtp->datasend("X-MSMail-Priority: High\n");
	# Add special header for HTML e-mail
	$smtp->datasend("Mime-Version: 1.0\n");
   	$smtp->datasend("Content-Type: text/html; charset=us-ascii\n\n");

	# print out the mail body
	my $temp_email = "approval\@zetainteractive.com";
	if (($cstatus ne "C") and ($cstatus ne "B"))
	{
		#$the_email = "<html><head><title>Advertiser Approval Email</title></head><body>This approval e-mail has been sent to you from XL Marketing, regarding the <FONT color=#800000><STRONG><EM>$aname</EM></STRONG> </FONT><FONT color=#000000>campaign. </p>We have updated creative assets for the <FONT color=#800000><STRONG><EM>$aname</EM></STRONG> </FONT>campaign which have been automatically set to an <b>Approved</b> status.  If no action is taken within 24 hours, these assets will be automatically mailed by our system.<br> <P class=MsoNormal align=left>We have modified the&nbsp;following campaign attributes:</P> <UL>";
		$the_email = "<html><head><title>Advertiser Approval Email</title></head><body>This approval e-mail has been sent to you, regarding the <FONT color=#800000><STRONG><EM>$aname</EM></STRONG> </FONT><FONT color=#000000>campaign. </p>We have updated creative assets for the <FONT color=#800000><STRONG><EM>$aname</EM></STRONG> </FONT>campaign which have been automatically set to an <b>Approved</b> status.  If no action is taken within 24 hours, these assets will be automatically mailed by our system.<br> <P class=MsoNormal align=left>We have modified the&nbsp;following campaign attributes:</P> <UL>";
	}
	else
	{
		#$the_email = "<html><head><title>Advertiser Approval Email</title></head><body>This confirmation e-mail has been sent to you from XL Marketing, regarding the <FONT color=#800000><STRONG><EM>$aname</EM></STRONG> </FONT><FONT color=#000000>campaign. </p><P class=MsoNormal align=left>We have modified the&nbsp;following campaign attributes:</P> <UL>";
		$the_email = "<html><head><title>Advertiser Approval Email</title></head><body>This confirmation e-mail has been sent to you, regarding the <FONT color=#800000><STRONG><EM>$aname</EM></STRONG> </FONT><FONT color=#000000>campaign. </p><P class=MsoNormal align=left>We have modified the&nbsp;following campaign attributes:</P> <UL>";
	}
	$smtp->datasend($the_email);
	my $i=0;
	while ($i <= $#textads)
	{
		$smtp->datasend("<LI><DIV class=MsoNormal align=left><EM><FONT color=#800000><STRONG>$textads[$i]</STRONG></FONT></EM></DIV></LI>\n");
		$i++;
	}
	$smtp->datasend("</ul><OL><LI><DIV class=MsoNormal align=left>Please click on the <a target='blank' href=\"${aspireurl}cgi-bin/advapproval.cgi?aid=$aid&amp;uid=$sname&amp;i=$internal\">advertiser confirmation page</a></DIV><LI><DIV class=MsoNormal align=left>Uncheck/unapprove any assets that should not be associated with this campaign</DIV><LI><DIV class=MsoNormal align=left>Scroll to the bottom of the page </DIV><LI><DIV class=MsoNormal align=left>Enter your information in the Approved By and E-mail fields and then click the <EM>Submit</EM> Button</DIV></LI></OL><DIV class=MsoNormal align=left>You may also email <a href=\"mailto:$temp_email\">$temp_email</a> or call Neal at 212.880.2510 x3411 with questions or concerns.  Please complete this advertiser approval form as soon as possible - we cannot send traffic to your offer until we receive your approval.</p><DIV class=MsoNormal align=left>&nbsp;</DIV><DIV class=MsoNormal align=left>Thanks!</DIV></body></html>");
	$smtp->dataend();
	$smtp->quit;
}

sub getLinkID
{
	my ($dbh,$aid,$link_num)=@_;
	my $tlink_id;

	$link_num++;
    my $sql = "select link_id from advertiser_tracking where advertiser_id=$aid and client_id=1 and daily_deal='N' and link_num=$link_num";
    my $sth1 = $dbh->prepare($sql);
    $sth1->execute();
    ($tlink_id) = $sth1->fetchrow_array();
    $sth1->finish();
	return $tlink_id;
}

sub getMTA
{
	my ($ip)=@_;

    my @ip_array;
	my $mta_ip;
   	$ip_array[0]=$ip;
    my $params={};
    $params->{'mailingIp'}=\@ip_array;
    $params->{'withFailedData'}=1;
    $params->{'withRdns'}=1;
	my $serverInterface     = Lib::Database::Perl::Interface::Server->new();
    my ($errors, $results) = $serverInterface->getMailingIpsAssigned($params);
	undef $serverInterface;
    for my $server (@$results)
    {
		$mta_ip=$server->{'mtaManagementIp'};
        my $privateIP=$server->{'mtaPrivateManagementIp'};
	}
	return($mta_ip);

1;
