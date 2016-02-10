#!/usr/bin/perl
#===============================================================================
# File: test_spam.pl
#
#
# History
#===============================================================================

# include Perl Modules

#use strict;
use File::Copy;
use util;
use util_mail;

# declare variables

my $util = util->new;
my $add_sub_dir;
my $file;
my $user_id;
my $sql;
my $sth;
my $sth1;
my $errmsg;
my $content_id;
my $rows;
my $header;
my $footer;
my %hsh_fl_pos_names;
my ($email_addr,  $email_type);
my ($camp_id, $fromaddress, $subject);
my $to_addr;
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
my $reccnt_queued = 0 ;
my $cdate;
my $email_mgr_addr;
my $refid;
my $master_str;
my $cdraft;
my $aid;
my $subject_id;
my $from;

$| = 1;    # don't buffer output for debugging log

# connect to the util database
my $dbhq;
my $dbhu;
($dbhq,$dbhu)=$util->get_dbh();

    $camp_id = $ARGV[0];
	$aid=$ARGV[1];
#	my $subject_id=$ARGV[2];
#	my $from=$ARGV[3];
    $to_addr = $ARGV[2];
	$cdraft = $ARGV[3];
	$sql = "select default_subject,default_from from creative where creative_id = $camp_id";
	$sth = $dbhq->prepare($sql);
	$sth->execute();
	($subject_id,$from,) = $sth->fetchrow_array();
	$sth->finish();
	process_file($camp_id,$aid,$subject_id,$from);
    $util->clean_up();

exit(0) ;

# ******************************************************************
# end of main - begin subroutines
# ******************************************************************

sub process_file 
{
	my ($camp_id,$aid,$subject_id,$from_id) = @_;
	my $therest;
	my $line;
	my $invalid_rec;
	my $input_file;
	my $sql;
	my $new_text;
	my $master_str;
	my $camp_name;
	my $footer_content_id;
	my $content_html;
	my $temp_master_str;

	my $qAdv=qq|SELECT advertiser_name FROM advertiser_info WHERE advertiser_id=$aid|;
	my $sAdv=$dbhq->prepare($qAdv);
	$sAdv->execute;
	my $adName=$sAdv->fetchrow;
	$sAdv->finish;

	my $qSub=qq|SELECT advertiser_subject FROM advertiser_subject WHERE subject_id=$subject_id|;
	my $sSub=$dbhq->prepare($qSub);
	$sSub->execute;
	my $c_subject=$sSub->fetchrow;
	$sSub->finish;

	my $qFrom=qq|SELECT advertiser_from FROM advertiser_from WHERE from_id=$from_id|;
	my $Fsth=$dbhq->prepare($qFrom);
	$Fsth->execute;
	my $c_from=$Fsth->fetchrow;
	$Fsth->finish;

#warn "$c_subject == $c_from\n";
	if ($cdraft eq "Y")
	{
		$master_str='From: {{FROMADDR}}
To: {{EMAIL_ADDR}}
Reply-To: {{FOOTER_DOMAIN}}{{CID}}@{{DOMAIN}}
Subject: {{SUBJECT}}
Date: {{MAILDATE}}
Message-ID: <{{MSGID}}>
X-Destination-ID: {{EMAIL_ADDR}}:{{CID}}:
Mime-Version: 1.0
Content-Type: text/html; charset=ISO-8859-1

<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">';
    	$sql = "select creative_name,html_code from draft_creative where creative_id=$camp_id";
    	$sth = $dbhq->prepare($sql);
    	$sth->execute();
    	($camp_name,$temp_master_str) = $sth->fetchrow_array();
    	$sth->finish();
		$footer_content_id=0;
		$subject=$c_subject;
##		$subject="Subject goes here";
		$fromaddress = "$c_from <information\@wealthpurse.com>";
		$master_str = $master_str . $temp_master_str;
		$master_str=~ s/{{FROMADDR}}/$fromaddress/g;
	}
	else
	{
    	$sql = "select default_from,default_subject,creative_name,content_id from creative where creative_id=$camp_id";
    	$sth = $dbhq->prepare($sql);
    	$sth->execute();
    	($fromaddress, $subject, $camp_name,$footer_content_id) = $sth->fetchrow_array();
    	$sth->finish();
		$fromaddress = "$c_from <information\@wealthpurse.com>";
##		$fromaddress = "Spam Report for $camp_name <information\@wealthpurse.com>";
		open(TEMPLATE,"</var/www/html/templates/camp_${camp_id}.txt");
		$master_str = "";
		while (<TEMPLATE>)
		{
			if (/^From:/)
			{
				$master_str = $master_str . "From: $c_from <information\@wealthpurse.com>\n";
			}
			else
			{
				$master_str = $master_str . $_;
			}
		}
		close(TEMPLATE);
	}
	my $subject=$c_subject; 
##	my $subject="Spam Report for ADV: $adName - CREATIVE: $camp_name"; 
	$master_str =~ s/{{DOMAIN}}/tonicmix.com/g;
	if ($footer_content_id > 0)
	{
		$sql="select content_html from footer_content where content_id=$footer_content_id";
    	$sth = $dbhq->prepare($sql);
    	$sth->execute();
		($content_html) = $sth->fetchrow_array();
		$sth->finish();
		$master_str =~ s/<\/body>/$content_html<\/body>/; 
	}
    send_mail($aid,$to_addr,0,0,$master_str,$subject);
}
sub send_mail
{
    my ($aid,$email_addr,$email_user_id,$refid,$template_text,$subject) = @_;
	my $new_text1;
	my $content_str;

        $sql="select brand_name,subdomain_name,header_text,footer_text from category_brand_info,brandsubdomain_info,client_brand_info,advertiser_info where brandsubdomain_info.category_id=advertiser_info.category_id and advertiser_info.advertiser_id=$aid and brandsubdomain_info.subdomain_id=category_brand_info.subdomain_id and category_brand_info.brand_id=7 and category_brand_info.brand_id=client_brand_info.brand_id";
        $sth1 = $dbhq->prepare($sql);
        $sth1->execute();
        my $bname;
        my $sdomain_name;
        ($bname,$sdomain_name,$header,$footer) = $sth1->fetchrow_array();
        $sth1->finish();
        $sdomain_name=~ s/{{BRAND}}/$bname/g;
        my $fsubdomain;
        my $rest_str;
	# begin to build the email for this member
	open(MAIL, ">/home/tmp/mesg_spam_${camp_id}.dat");
   	$template_text =~ s/{{CID}}/$camp_id/g;
   	$template_text =~ s/{{FID}}/0/g;
   	$template_text =~ s/{{SID}}/0/g;
   	$template_text =~ s/{{CRID}}/0/g;
    $template_text =~ s/{{IMG_DOMAIN}}/affiliateimages.com/g;
    $template_text =~ s/{{SUBJECT}}/$subject/g;
    $template_text =~ s/{{URL}}/http:\/\/$redir_domain\/cgi-bin\/redir1.cgi?eid={{EMAIL_USER_ID}}&cid=$camp_id&em={{EMAIL_ADDR}}&id=0&f=0&s=0&c=0/g;
   	$template_text =~ s/{{EMAIL_ADDR}}/$email_addr/g;
   	$template_text =~ s/{{EMAIL_USER_ID}}/$email_user_id/g;
    $template_text =~ s/{{FOOTER_DOMAIN}}/tonicmix.com/g;
    $template_text =~ s/{{FOOTER_SUBDOMAIN}}/$sdomain_name/g;
    $template_text =~ s/{{FOOTER_STR}}/7/g;
    $template_text =~ s/{{HEADER_TEXT}}/$header/g;
    $template_text =~ s/{{FOOTER_TEXT}}/$footer/g;
    $template_text =~ s/{{SNAME}}/David/g;
    $template_text =~ s/{{NAME}}/David/g;
    my ( $sec, $min, $hour, $mday, $mon, $year, $wday, $yday, $isdst )= localtime( time );
    my $mesg_id = $year . $mon . $mday . $hour . $min . $sec . "." . $email_user_id . ".qmail@" . "tonicmix.com";
    $template_text =~ s/{{MSGID}}/$mesg_id/;
    my $date_str = $util->date(6,6);
   	$template_text =~ s/{{MAILDATE}}/$date_str/g;
	#
	# Check for CONTENT_HEADER tag
	#
	$content_str = "";
	$_ = $template_text;
	if ((/{{CONTENT_HEADER}}/) && ($content_id >= 2))
	{
		if ($content_id == 2)
		{
			$content_str = "<center><table cellSpacing=\"0\" cellPadding=\"0\" width=\"600\" border=\"0\" id=\"table37\"><tr style=\"BACKGROUND-COLOR: #668099\" bgColor=\"#668099\"><td style=\"TEXT-ALIGN: left\" vAlign=\"top\" width=\"20\"><img height=\"15\" src=\"images/spacer.gif\" width=\"5\" border=\"0\"></td><td style=\"PADDING-LEFT: 5px; TEXT-ALIGN: center\" width=\"100%\"><font face=\"Verdana\" color=\"#FFFFFF\"><span style=\"FONT-FAMILY: Arial; font-weight:700\">Daily Horoscopes</span></font></td><td style=\"TEXT-ALIGN: right\" vAlign=\"top\" width=\"20\"><img height=\"20\" alt=\")\" src=\"http://www.affiliateimages.com/images/templates/diag_corner_tr.gif\" width=\"20\" border=\"0\"></td></tr></table><table style=\"BACKGROUND-COLOR: #668099\" cellSpacing=\"2\" cellPadding=\"0\" width=\"600\" bgColor=\"#668099\" border=\"0\" id=\"table38\"><tr><td style=\"BACKGROUND-COLOR: #99cccc\" bgColor=\"#99cccc\">&nbsp;</td></tr><tr style=\"BACKGROUND-COLOR: #ffffff\" bgColor=\"#ffffff\"><td style=\"PADDING-LEFT: 5px\" vAlign=\"top\" align=\"left\"><font face=\"Arial\" size=\"2\"><br>For centuries, horoscopes and astrology have been used to help guide us and better understand who we are. Our horoscopes cover many areas including love, friendships, and business. <br><br><b>Check out your daily horoscope below.</b></font></td></tr><tr><td style=\"BACKGROUND-COLOR: #ffffff\" vAlign=\"top\" align=\"left\" bgColor=\"#ffffff\"></td></tr></table><table cellSpacing=\"0\" cellPadding=\"0\" width=\"600\" border=\"0\" id=\"table40\"><tr style=\"BACKGROUND-COLOR: #668099\" bgColor=\"#668099\"><td style=\"TEXT-ALIGN: left\" vAlign=\"bottom\"><img height=\"20\" src=\"http://www.affiliateimages.com/images/templates/diag_corner_bl.gif\" width=\"20\" border=\"0\"></td><td style=\"TEXT-ALIGN: right\" vAlign=\"bottom\"><img height=\"20\" src=\"http://www.affiliateimages.com/images/templates/diag_corner_br.gif\" width=\"20\" border=\"0\"></td></tr></table><font face=\"Arial\" color=\"#808080\"><b>Today's Offer</b></font>";
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
			$content_str = "<center><table cellSpacing=\"0\" cellPadding=\"0\" width=\"600\" border=\"0\" id=\"table37\"><tr style=\"BACKGROUND-COLOR: #668099\" bgColor=\"#668099\"><td style=\"TEXT-ALIGN: left\" vAlign=\"top\" width=\"20\"><img height=\"15\" src=\"images/spacer.gif\" width=\"5\" border=\"0\"></td><td style=\"PADDING-LEFT: 5px; TEXT-ALIGN: center\" width=\"100%\"><font face=\"Verdana\" color=\"#FFFFFF\"><span style=\"FONT-FAMILY: Arial; font-weight:700\">Daily Horoscopes</span></font></td><td style=\"TEXT-ALIGN: right\" vAlign=\"top\" width=\"20\"><img height=\"20\" alt=\")\" src=\"http://www.affiliateimages.com/images/templates/diag_corner_tr.gif\" width=\"20\" border=\"0\"></td></tr></table><table style=\"BACKGROUND-COLOR: #668099\" cellSpacing=\"2\" cellPadding=\"0\" width=\"600\" bgColor=\"#668099\" border=\"0\" id=\"table38\"><tr><td style=\"BACKGROUND-COLOR: #99cccc\" bgColor=\"#99cccc\">&nbsp;</td></tr><tr style=\"BACKGROUND-COLOR: #ffffff\" bgColor=\"#ffffff\"><td style=\"PADDING-LEFT: 5px\" vAlign=\"top\" align=\"left\"><font face=\"Arial\" size=\"2\"><br>For centuries, horoscopes and astrology have been used to help guide us and better understand who we are. Our horoscopes cover many areas including love, friendships, and business. <br><br><b>Check out your daily horoscope below.</b></font><br><br><hr><center><b>Today's Offer</b></center><br>";
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
    		open(IN2,"</var/www/util/data/topnews.txt") || print "Error - could not open input file: /var/www/util/data/news.txt";
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
		$subdomain_name = $sname . "." . $dname;
	}
	else
	{
		$subdomain_name = $subname . "." . $dname;
	}
	$template_text =~ s/{{CONTENT_TEXT}}/$content_str/g;
	print MAIL "$template_text\n";
	close MAIL;
			
}
