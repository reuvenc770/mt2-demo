#!/usr/bin/perl
#===============================================================================
# File: footer_content_spam_new.pl
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

$| = 1;    # don't buffer output for debugging log

# connect to the util database
my $dbhq;
my $dbhu;
($dbhq,$dbhu)=$util->get_dbh();

    $camp_id = $ARGV[0];
    $to_addr = $ARGV[1];
	process_file($camp_id);
    $util->clean_up();

exit(0) ;

# ******************************************************************
# end of main - begin subroutines
# ******************************************************************

sub process_file 
{
	my ($camp_id) = @_;
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
	my $content_name;

    $sql = "select content_name,content_html from footer_content where content_id=$camp_id";
    $sth = $dbhq->prepare($sql);
    $sth->execute();
    ($content_name,$content_html) = $sth->fetchrow_array();
    $sth->finish();
	$fromaddress = "Spam Report for $content_name <information\@xlmx.com>";
	$master_str="From: $fromaddress\n";
	$master_str = $master_str . "To: {{EMAIL_ADDR}}\n";
	$master_str = $master_str . "Subject: {{SUBJECT}}\n";
	$master_str = $master_str . "Date: {{MAILDATE}}\n";
	$master_str = $master_str . "Message-ID: <{{MSGID}}>\n";
	$master_str = $master_str . "X-Destination-ID: {{EMAIL_ADDR}}:{{CID}}:\n";
	$master_str = $master_str . "Mime-Version: 1.0\n";
	$master_str = $master_str . "Content-Type: text/html; charset=ISO-8859-1\n\n";
	$master_str = $master_str . "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n";
	$master_str = $master_str . "<html>\n";
	$master_str = $master_str . "<head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=windows-1252\"><title>{{EMAIL_USER_ID}}</title></head><body>\n";
	$master_str = $master_str . $content_html;
	$master_str = $master_str . "</body></html>\n";
    send_mail($to_addr,0,0,$master_str);
}
sub send_mail
{
    my ($email_addr,$email_user_id,$refid,$template_text) = @_;
	my $new_text1;
	my $content_str;

	# begin to build the email for this member
	open(MAIL, ">/home/tmp/mesg_spam.dat");
   	$template_text =~ s/{{CID}}/$camp_id/g;
   	$template_text =~ s/{{FID}}/0/g;
   	$template_text =~ s/{{SID}}/0/g;
   	$template_text =~ s/{{CRID}}/0/g;
    $template_text =~ s/{{IMG_DOMAIN}}/affiliateimages.com/g;
    $template_text =~ s/{{SUBJECT}}/Spam Report/g;
    $template_text =~ s/{{URL}}/http:\/\/$redir_domain\/cgi-bin\/redir1.cgi?eid={{EMAIL_USER_ID}}&cid=$camp_id&em={{EMAIL_ADDR}}&id=0&f=0&s=0&c=0/g;
   	$template_text =~ s/{{EMAIL_ADDR}}/$email_addr/g;
   	$template_text =~ s/{{EMAIL_USER_ID}}/$email_user_id/g;
    $template_text =~ s/{{FOOTER_DOMAIN}}/tonicmix.com/g;
    $template_text =~ s/{{SNAME}}/David/g;
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
	$dname = "xlmx.com";
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
