#!/usr/bin/perl
#===============================================================================
# File: batch_send_email.pl
#
# Batch job that adds Subscribers
#
# History
# 05/24/2002	Jim Sobeck		Added logic for edealsdirect
#===============================================================================

# include Perl Modules

use strict;
use File::Copy;
use pms;
use pms_mail;

# declare variables

my $EDEALSDIRECT_USER = 33;
my $pms = pms->new;
my $dbh;
my $add_sub_dir;
my $file;
my $user_id;
my $sql;
my $sth;
my $sth1;
my $errmsg;
my $rows;
my %hsh_fl_pos_names;
my ($email_addr,  $email_type);
my ($camp_id, $fromaddress, $subject, $read_receipt);
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

$| = 1;    # don't buffer output for debugging log

# connect to the pms database 

$pms->db_connect();
$dbh = $pms->get_dbh;

my $bin_dir_http;

	$sql = "select parmval from sysparm where parmkey = 'SYSTEM_MGR_ADDR'";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	($email_mgr_addr) = $sth->fetchrow_array();
	$sth->finish();

	$sql = "select parmval from sysparm where parmkey = 'BIN_DIR_HTTP'"; 
	$sth = $dbh->prepare($sql); 
	$sth->execute();
	($bin_dir_http) = $sth->fetchrow_array();
	$sth->finish();

	# get html email footer from sysparm table
	$sql = "select parmval from sysparm where parmkey = 'HTML_EMAIL_FOOTER'";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	($html_email_footer) = $sth->fetchrow_array();
	$sth->finish();

	# get text email footer from sysparm table
	$sql = "select parmval from sysparm where parmkey = 'TEXT_EMAIL_FOOTER'";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	($text_email_footer) = $sth->fetchrow_array();
	$sth->finish();

# open the Add Subscribers directory looking for files
opendir(DIR, "/var/www/pms/mailfiles/");
while (defined($file = readdir(DIR)))
{
    if ($file eq "." || $file eq ".." || $file eq "working" || $file eq "sav")
    {
        # skip files . and .. and the working directory
        next;
    }

	# found a file to upload

	$cdate = localtime();
	print "Processing file $file starting at $cdate\n";

	process_file("/var/www/pms/mailfiles/", $file);

	# close up everything and exit - only process one file

	$cdate = localtime();
	print "Finished processing $file at $cdate\n";

	closedir(DIR);
	$pms->clean_up();   
	exit(0) ;
}
# if got to here, did not find any files to process

closedir(DIR);
$pms->clean_up();   
exit(0) ;

# ******************************************************************
# end of main - begin subroutines
# ******************************************************************

sub process_file 
{
	my ($dir, $file) = @_;
	my $therest;
	my $line;
	my $invalid_rec;
	my $input_file;
	my $sql;
	my $new_text;


	# Select template to use based on format user can accept
	print "Processing File $file in Directory $dir\n";

	# move the file to the working directory

	$input_file = "${dir}working/$file";
	rename "$dir$file", "$input_file";
	if ($!)
	{
		print "Error moving file to $input_file: $!\n";
		$pms->clean_up();   
		exit(0) ;
	}
	
	print "Opening input file $input_file\n";
	open(IN2,"<$input_file") || print "Error - could not open input file: $input_file";

	# loop reading records in input file

	while (<IN2>)
	{
		$reccnt_tot++; 
		chomp;                               # remove last char if LineFeed etc

		$line = $_ ;
		if ($reccnt_tot == 1)
		{
		    ($camp_id, $fromaddress, $subject, $read_receipt,$user_id) = split '\|', $line ;      
		    print "Campaign id = $camp_id being sent for $fromaddress\n";
		    print "Subject: $subject\n";
			# Get the text for this campaign
			$sql = "select html_template,text_template,aol_template 
				from campaign_template where campaign_id=$camp_id";
			$sth1 = $dbh->prepare($sql);
			$sth1->execute();
			($html_template,$text_template,$aol_template) = $sth1->fetchrow_array();
			$sth1->finish();
		}
		else
		{
			if ($camp_id == 27)
			{
		    	($email_addr, $email_type, $email_user_id,$refid) = split '\|', $line ;
			}
			else
			{
		    	($email_addr, $email_type, $email_user_id) = split '\|', $line ;
			}
		    send_mail($camp_id,$fromaddress,$subject,$read_receipt,$email_addr,$email_type,$email_user_id,$refid);
		}


		if (($reccnt_tot%1000) == 0)
		{
			print "processing record $reccnt_tot email address = $email_addr\n";
		}
	}
	close IN2;

	print "Done processing $input_file\n";
	print "record count total = $reccnt_tot\n";
#	print "record count good = $reccnt_good\n";
#	print "record count bad = $reccnt_bad\n";

	# send email notification to use

	# delete the file from the working directory
	unlink($input_file) || print "Error - could NOT Remove file: $input_file\n";
}
sub send_mail
{
    my ($camp_id,$from_addr,$subject,$read_receipt,$email_addr,$email_type,$email_user_id,$refid) = @_;
	my $list_id;
	my $cemail;
	my $the_email;
	my $format;
	my $template_text;
	my $new_text1;

	$cemail = $email_addr;
#	print "Sending email to $cemail\n";

	# if email_type is blank - then default it to H just in case

	if ($email_type eq "")
	{
		$email_type = "H";
	}

	# begin to build the email for this member

	open (MAIL,"| /usr/lib/sendmail -t");
	print MAIL "Reply-To: $from_addr\n"; 
	if ($camp_id == 13)
	{
   		print MAIL "From: Matt Morrow <offers\@jumpjive.com>\n";
	}
	elsif ($camp_id == 19)
	{
   		print MAIL "From: Lori Anderson <offers\@jumpjive.com>\n";
	}
	elsif ($camp_id == 38)
	{
   		print MAIL "From: Lori Anderson <offers\@jumpjive.com>\n";
	}
	elsif ($camp_id == 40)
	{
   		print MAIL "From: Lori Anderson <offers\@jumpjive.com>\n";
	}
	elsif ($camp_id == 23)
	{
   		print MAIL "From: Judy Harrison <offers\@jumpjive.com>\n";
	}
	elsif ($camp_id == 25)
	{
   		print MAIL "From: WorkFromHome <offers\@jumpjive.com>\n";
	}
	elsif ($camp_id == 26)
	{
   		print MAIL "From: WorkFromHome <offers\@jumpjive.com>\n";
	}
	elsif ($camp_id == 42)
	{
   		print MAIL "From: Special Unit Director <offers\@jumpjive.com>\n";
	}
	else
	{
   		print MAIL "From: $from_addr\n";
	}
   	print MAIL "To: $cemail\n";
   	print MAIL "Subject: $subject\n";
	print MAIL "X-Email: $cemail:$camp_id:\n";
			
	# If read receipt requested add special header information
	if ($read_receipt eq "Y")
	{
		print MAIL "Mime-Version: 1.0\n";
		print MAIL "Content-Type: multipart/alternative;\n";
   		print MAIL "    boundary=\"----=_NextPart_www.7qxqtzqv0swu60.mailboundry.com\"\n";
		print MAIL "Disposition-Notification-To: $email_mgr_addr\n";
		print MAIL "X-Confirm-Reading-To: $email_mgr_addr\n";
		print MAIL "Return-Receipt-To: $email_mgr_addr\n";
		print MAIL "X-Read-Notification: Courtesy of JumpJive.com\n";
		print MAIL "------=_NextPart_www.7qxqtzqv0swu60.mailboundry.com\n";
	}

	# Add header for email type to specify mime type
	if ($email_type eq "H")
	{
		# Add special header for HTML e-mail
		print MAIL "Mime-Version: 1.0\n";
   		print MAIL "Content-Type: text/html; charset=us-ascii\n\n";
	}
	elsif ($email_type eq "A")
	{
		# Add special header for AOL 5.0 and older e-mail
   		print MAIL "Content-Type: text/x-aol\n\n";
	}
	elsif (($email_type eq "T") || ($email_type eq "D"))
	{
		# Add special header for text e-mail
		print MAIL "Content-Type: text/plain; charset=\"iso-8859-1\"\n\n";
	}

	$format = $email_type;
	if ($format eq "H")
	{
		$template_text = $html_template;
		$new_text1 = template_substit($dbh,$camp_id,$email_addr,$text_template,"T",$email_user_id,$refid);
	}
	elsif ($format eq "T")
	{
		$template_text = $text_template;
	}
	elsif ($format eq "A")
	{
		$template_text = $aol_template;
	}
	else 
	{
		$template_text = $text_template;
	}
	
	# Call routine to get the campaigns info and do all the substitution in the template
	# it returns the template text with the fields substitutied with the data
						
	$the_email = template_substit($dbh,$camp_id,$email_addr,$template_text,$format,$email_user_id,$new_text1,$refid);

	print MAIL $the_email;

	if ($read_receipt eq "Y")
	{
		print MAIL "\n";
		print MAIL "------=_NextPart_www.7qxqtzqv0swu60.mailboundry.com\n";
	}
	close MAIL;
			
	# write record to campaign_history
#	$sql = "insert into campaign_history (campaign_id, email_user_id, action,action_datetime) values ($camp_id, $email_user_id, 'S', now())";
#	$rows = $dbh->do($sql);
#   	if ($dbh->err() != 0)
#   	{
#   		$errmsg = $dbh->errstr();
#		print "Problem writing campaign_history record: $sql; $errmsg\n";
#   		exit(0);
#   	}
#	print "Email Sent to $cemail\n";
}

# ******************************************************************************
# sub template_substit()
# this routine builds the body of an email, by looking up all the campaigns values
# and reading the campaigns template and substituting all the fields.
# ******************************************************************************

sub template_substit()
{
	my ($dbh,$camp_id,$email_addr,$template_text,$format,$email_user_id,$new_text1,$refid) = @_;
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
	my $tell_a_friend;
	my $ads_url;
	my $top_ad_opt;
	my $top_ad_code;
	my $bottom_ad_opt;
	my $bottom_ad_code;
	my $client_id;
	my $hidden_text;
	my $timestr;
	my $curtime;

    use URI::Escape;

	# If tracking in text then replace with correct information
	$_ = $template_text;
	if (/{{TRACKING}}/)
	{
		$tracking_str = "<IMG SRC=\"http://www.jumpjive.com/cgi-bin/open_email.cgi?id=$email_user_id&cid=$camp_id\" border=0 height=1 width=1 alt=\"tracking img\">\n";
		$template_text =~ s/{{TRACKING}}/$tracking_str/g;
	}
    if (/{{HEADER_INFO}}/)
    {
		if ($user_id != $EDEALSDIRECT_USER)
		{
        	$tracking_str = "<i>You are receiving this message from JumpJive.com. If you do not wish to receive further messages from JumpJive.com, please click on the unsubscribe link below.</i><p>";
		}
		else
		{
			$tracking_str = "<i>You are receiving this message from eDealsDirect.com. If you do not wish to receive further messages from eDealsDirect.com, please click on the unsubscribe link below.</i><p>";
		}
        $template_text =~ s/{{HEADER_INFO}}/$tracking_str/g;
    }
    if (/{{TIMESTAMP}}/)
    {
        $timestr = pms::date($curtime,5);
        $template_text =~ s/{{TIMESTAMP}}/$timestr/g;
    }
	if (/{{REFID}}/)
	{
		$template_text =~ s/{{REFID}}/$refid/g;
	}
	$template_text =~ s/{{CLICK}}//g;

	if ($user_id != $EDEALSDIRECT_USER)
	{
		$contact_url="http://www.jumpjive.com";
		$contact_email="offers\@jumpjive.com";
		$contact_name="JumpJive.com Offers";
		$contact_company="JumpJive.com";
	}
	else
	{
        $contact_url="http://www.edealsdirect.com";
        $contact_email="deals\@edealsdirect.com";
        $contact_name="Edealsdirect.com Offers";
        $contact_company="eDealsDirect.com";
	}

	# Add special Unsubscribe footer to the bottom of every email - 
	# this is hard coded here because the users cannot remove this from the emails that
	# go out

	if ($format eq "H" or $format eq "A")
	{
		if ($format eq "H")
		{
			$hidden_text = "<html>
<!-- " . $new_text1;
			$hidden_text .= "
-->";
			$template_text =~ s\<HTML>\<html>\;
			$template_text =~ s\<html>\$hidden_text\;
		}
		# substitute end of page (closing body tag) with all the unsubscribe
		# footer stuff that must go on the bottom of every email, adding the
		# closing body tag back on
		$template_text =~ s\</BODY>\</body>\;

		if ($user_id != $EDEALSDIRECT_USER)
		{
			$template_text =~ s\</body>\<p><HR width="90%" SIZE=1><p><center>
<table cellspacing=0 cellpadding=0 width=600 border=0>
<tr><td align=middle>
<font face="Verdana,Arial" size="1"><i>Your privacy is extremely important to us. You requested to receive this mailing, by registering at JumpJive.com or by subscribing through one of our marketing partners. As a leader in permission-based email marketing, we are committed to delivering a highly rewarding experience, with offers that include bargains, entertainment, and money-making ideas.</i><p>
This email was sent to {{EMAIL_ADDR}},  
by <a href="{{CONTACT_URL}}"><font color="blue">{{CONTACT_COMPANY}}</font></a>.<br>
Click Here to {{UNSUBSCRIBE}}.
</td></tr><tr><td align=middle><font face="Verdana,Arial" size="1">Do not reply
to this e-mail to unsubscribe, it will not be processed.</font></td></tr></table></center></p>\;
		}
		else
		{
				$template_text =~ s\</body>\<p><HR width="90%" SIZE=1><p><center>
<table cellspacing=0 cellpadding=0 width=600 border=0>
<tr><td align=middle>
<font face="Verdana,Arial" size="1"><i>As a leader in premission-based email marketing, eDealsDirect takes your privacy very seriously. We are committed to delivering you a most rewarding online experience, with offers that include discounts, coupons, and moneymaking opportunities.  You requested to receive this mailing by registering with eDealsDirect or by subscribing through one of our marketing partners.</i><p>
However, if you wish to unsubscribe, please <a href="http://www.edealsdirect.com/unsubscribe.asp">click here</a> and your email address will be immediately and permanently removed from our systems.
</td></tr><tr><td align=middle><font face="Verdana,Arial" size="1"><br>&copy; Copyright 2002. eDealsDirect.com. All Rights Reserved.</font></td></tr></table></center></p>\;
		}

		# add tell a friend box at the bottom if needed
		if ($tell_a_friend eq "Y")
		{
			$template_text .= qq { <p><center>
<table cellspacing=0 cellpadding=0 width=600 border=0>
<tr><td align=middle>
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


		# append the html email footer to the end of the email
		$template_text =~ s\</HTML>\</html>\;
		$template_text =~ s\</html>\\;
		$template_text .= $html_email_footer;

		# now add end body tag to close the html email

		$template_text .= "</body></html>";

		# replace unsubscribe field with the proper link

   		$template_text =~ s\{{UNSUBSCRIBE}}\<a href="http://www.jumpjive.com/cgi-bin/unsubscribe.cgi?email=$email_user_id&cid=$camp_id">
<font color="blue">unsubscribe</font></a>\g;

		# substitute <br> for the carriage returns if displaying html page

		$introduction =~ s/\n/<br>/g;
		$closing =~ s/\n/<br>/g;
		$promotion_desc =~ s/\n/<br>/g;
	}
	else
	{

		# add unsubscribe footer for text emails
		if ($camp_id != 43)
		{
			if ($user_id != $EDEALSDIRECT_USER)
            {			
				$template_text .= "This email has been sent to {{EMAIL_ADDR}} by {{CONTACT_COMPANY}}

Follow this link to unsubscribe.  
http://www.jumpjive.com/cgi-bin/unsubscribe.cgi?email=$email_user_id&cid=$camp_id

Do not reply to this e-mail to unsubscribe, it will not be processed.
";
				# append the text email footer from sysparms
				$template_text .= $text_email_footer;
			}
			else
			{
				$template_text .= "This email has been sent to {{EMAIL_ADDR}} by {{CONTACT_COMPANY}}

Follow this link to unsubscribe.  
http://www.edealsdirect.com/unsubscribe.asp 
";
			}
		}
	}

	# contact fields

    $template_text =~ s/{{CONTACT_EMAIL}}/$contact_email/g;
    $template_text =~ s/{{CONTACT_URL}}/$contact_url/g;
#   $template_text =~ s/{{CONTACT_PHONE}}/$contact_phone/g;
    $template_text =~ s/{{CONTACT_NAME}}/$contact_name/g;
    $template_text =~ s/{{CONTACT_COMPANY}}/$contact_company/g;

	# personalization fields

   	$template_text =~ s/{{EMAIL_ADDR}}/$email_addr/g;
   	$template_text =~ s/{{EMAIL_USER_ID}}/$email_user_id/g;

    # Get the client id for this campaign

	$template_text =~ s\{{POPUP_AD}}\\g;

	$template_text =~ s\{{TOP_AD}}\\g;

	$template_text =~ s\{{BOTTOM_AD}}\\g;

	$tracking_str = "${bin_dir_http}redir.cgi?id=$email_user_id&cid=$camp_id&l=";
	return ($template_text);
}
