#!/usr/bin/perl
# *****************************************************************************************
# send_email.pl
#
# Batch program that runs from cron to send the emails
# schedule the email
#
# History
# Jim Sobeck,   08/07/01,   Created
# *****************************************************************************************

# send_email.pl 

use strict;
use lib "/var/www/pms/src";
use pms;
use pms_mail;

my $pms = pms->new;
my $sth;
my $sth1;
my $sth2;
my $dbh;
my $sql;
my $rows;
my $cdate = localtime();
my $program = "send_email.pl";
my $errmsg;
my $email_mgr_addr;
my $bin_dir_http;

# connect to the pms database

$pms->db_connect();
$dbh = $pms->get_dbh;

# lookup the system mail address

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

# Send any mail that needs to be sent

send_powermail();

# Send any Follow Me mail that needs to be sent

#send_followme();

# end of program

$pms->clean_up();
exit(0);

# ***********************************************************************
# sub send_powermail
# ***********************************************************************

sub send_powermail
{
	my $campaign_id;

	# Check to see if any campaigns to process

	$sql = "select campaign_id from campaign where status='S' and 
		scheduled_date <= current_date() order by campaign_id"; 
	$sth = $dbh->prepare($sql);
	$sth->execute();
	if (($campaign_id) = $sth->fetchrow_array())
	{
		$sth->finish();
		
		# Mark the campaign as pending
		
		$sql = "update campaign set status='P' where campaign_id=$campaign_id";
		$rows = $dbh->do($sql);
		if ($dbh->err() != 0)
		{
    		$errmsg = $dbh->errstr();
       		print "Error updating campaign: $sql : $errmsg";
    		$pms->errmail($dbh,$program,$errmsg,$sql);
		}
	
		# Send e-mail
		
		$cdate = localtime();
		print "Sending email for Campaign $campaign_id at $cdate\n";
		mail_send($campaign_id);
		
		# Mark the campaign as sent 
		
		$sql = "update campaign set status='C',sent_datetime=now() where campaign_id=$campaign_id";
		$rows = $dbh->do($sql);
		if ($dbh->err() != 0)
		{
    		$errmsg = $dbh->errstr();
       		print "Error updating campaign: $sql : $errmsg";
    		$pms->errmail($dbh,$program,$errmsg,$sql);
		}
	}
	else
	{
		$sth->finish();
	}

}

# ***********************************************************************
# sub send_followme
# ***********************************************************************

sub send_followme
{
	my $fm_campaign_id;
	my $num_days;
	my $email_user_id;
	my $sth6;
	my $count;

	$cdate = localtime();
	print "send_followme: starting at $cdate\n";

	# read each active follow me campaigni

	$sql = "select fm_campaign_id, num_days
    	from fm_campaign where status = 'E'";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	while (($fm_campaign_id, $num_days) = $sth->fetchrow_array())
	{
		print "send_followme: examining follow me campaign $fm_campaign_id\n";

		# find anyone who clicked on any campaign / article in this follow me campaign

		$sql = "select distinct campaign_history.email_user_id
    		from campaign_history, fm_campaign_rule, email_user
			where fm_campaign_rule.status = 'A' and
			fm_campaign_rule.fm_campaign_id = $fm_campaign_id and
			fm_campaign_rule.campaign_id = campaign_history.campaign_id and
			fm_campaign_rule.article_num = campaign_history.article_num and
			to_days(now()) - to_days(action_datetime) >= $num_days and
			campaign_history.email_user_id = email_user.email_user_id and 
			email_user.status = 'A'";
		$sth2 = $dbh->prepare($sql);
		$sth2->execute();
		while (($email_user_id) = $sth2->fetchrow_array())
		{
			print "send_followme: found email_user ($email_user_id) to send follow me email\n";

			# check to make sure this user has not already gotten an email
			# from this follow me campaign.  A person can get only one
			# follow me email per follow me campaign

			$sql = "select count(*) from fm_email where email_user_id = $email_user_id and
				fm_campaign_id = $fm_campaign_id and action = 'S'";
			$sth6 = $dbh->prepare($sql);
			$sth6->execute();
			($count) = $sth6->fetchrow_array();
			$sth6->finish();

			if ($count == 0)
			{
				$cdate = localtime();
				print "Sending follow me email at $cdate\n";
				print "follow me Campaign = $fm_campaign_id\n";
				print "num_days = $num_days\n";

				# send follow me email specified by campaign/article/days to this person

				mail_send_followme($email_user_id, $fm_campaign_id);
			}
			else
			{
				print "Not sending follow me email cause they already got it\n";
			}
		}
		$sth2->finish();	
	}
	$sth->finish();	

	print "send_followme: finished\n";
}

# ***********************************************************************
# This routine is used for sending all email for a single campaign
# ***********************************************************************

sub mail_send
{
	my ($camp_id) = @_;
	my $subject;
	my $from_addr;
	my $read_receipt;
	my $list_id;
	my $email_user_id;
	my $list_str;
	my $cemail;
	my $email_type;
	my $the_email;

	# Get the mail information for the campaign being used

	$sql = "select subject, from_addr, read_receipt from campaign where campaign_id=$camp_id";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	if (($subject,$from_addr,$read_receipt) = $sth->fetchrow_array())
	{
		$sth->finish();
		
		# Get all of the lists for the campaign which are active
		
		$sql = "select list.list_id from list,campaign_list 
			where campaign_id=$camp_id and status='A' and 
			list.list_id=campaign_list.list_id";
		$sth = $dbh->prepare($sql);
		$sth->execute();
		$list_str = "";
		while (($list_id) = $sth->fetchrow_array())
		{
			if ($list_str eq "")
			{
				$list_str = $list_id;
			}
			else
			{
				$list_str = $list_str . ',' . $list_id;
			}
		}
		$sth->finish();
	
		print "Lists for campaign $camp_id are: $list_str\n";
	
		# Now get a list of all the members and start processing
		
#		$sql = "select distinct email_user_id from list_member 
		$sql = "select distinct email_addr, email_type from email_user,list_member 
			where list_id in ($list_str) and list_member.status='A' and email_user.email_user_id=list_member.email_user_id";
		$sth = $dbh->prepare($sql);
		$sth->execute();
		while (($cemail,$email_type) = $sth->fetchrow_array())
		{
			# find out email user id for this member

#			$sql = "select email_addr, email_type from email_user
			$sql = "select email_user_id from email_user
				where email_addr = '$cemail'";
			$sth2 = $dbh->prepare($sql);
			$sth2->execute();
			($email_user_id) = $sth2->fetchrow_array();
			$sth2->finish();

			print "Sending email to $cemail\n";

			# if email_type is blank - then default it to H just in case

			if ($email_type eq "")
			{
				$email_type = "H";
			}

			# begin to build the email for this member

			open (MAIL,"| /usr/lib/sendmail -t ");
			print MAIL "Reply-To: $from_addr\n"; 
    		print MAIL "From: $from_addr\n";
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
				print MAIL "X-Read-Notification: Courtesy of Lead Dog\n";
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

			$the_email = &pms_mail::mail_preview($dbh,$camp_id,$email_type,$cemail,$email_user_id);

			print MAIL $the_email;

			if ($read_receipt eq "Y")
			{
				print MAIL "\n";
				print MAIL "------=_NextPart_www.7qxqtzqv0swu60.mailboundry.com\n";
			}
			close MAIL;
			
			# write record to campaign_history
			
			$sql = "insert into campaign_history (campaign_id, email_user_id, action,
				action_datetime) values ($camp_id, $email_user_id, 'S', now())";
    		$rows = $dbh->do($sql);
    		if ($dbh->err() != 0)
    		{
        		$errmsg = $dbh->errstr();
				print "Problem writing campaign_history record: $sql; $errmsg\n";
        		exit(0);
    		}
			print "Email Sent to $cemail\n";
		}
		$sth->finish();
	}
	else
	{
		$sth->finish();
	}
	print "Finished sending mail for $camp_id\n";

}

# ***********************************************************************
# This routine is used for sending a single follow me email
# ***********************************************************************

sub mail_send_followme
{
	my ($email_user_id, $fm_campaign_id) = @_;
	my $subject;
	my $from_addr;
	my $read_receipt;
	my $email_addr;
	my $email_type;
	my $the_email;
	my $num_days;
	my $reccnt = 0;
	my $campaign_id;
	my $fm_article_num;
	my $fm_campaign_name;
	my $fm_template_id;
	my $header;
	my $footer;
	my $template_text;
	my $first_name;
	my $last_name;
	my ($article_title, $article_text, $article_link, $article_link_name, $article_image_url);
	my $tracking_str;
	my $repeat_part;
	my $this_article;
	my $sth3;
	my $sth4;
	my $sth5;
	my $pos;
	my $pos2;
	my $grab;
	my $first_part;
	my $last_part;
	my ($html_template,$text_template,$aol_template);
	my $article_num;

	# find out about this user

	$sql = "select email_addr, email_type, first_name, last_name 
		from email_user where email_user_id = $email_user_id";
	$sth4 = $dbh->prepare($sql);
	$sth4->execute();
	($email_addr, $email_type, $first_name, $last_name) = $sth4->fetchrow_array();
	$sth4->finish();

	print "Sending follow me email to $email_addr\n";

	# get header/footer and info from the follow me campaign found

	$sql = "select fm_campaign_name, fm_template_id, subject,
		from_addr, read_receipt, header, footer, num_days
		from fm_campaign
		where fm_campaign_id = $fm_campaign_id";
	$sth3 = $dbh->prepare($sql);
	$sth3->execute();
	($fm_campaign_name, $fm_template_id, $subject,
		$from_addr, $read_receipt, $header, $footer, $num_days) = $sth3->fetchrow_array();
	$sth3->finish();

	# get the html/text for this template

	$sql = "select html_template, text_template, aol_template
		from fm_template
		where fm_template_id = $fm_template_id";
	$sth3 = $dbh->prepare($sql);
	$sth3->execute();
	($html_template,$text_template,$aol_template) = $sth3->fetchrow_array();
	$sth3->finish();

	# Select template to use based on format user can accept

	if ($email_type eq "H")
	{
		$template_text = $html_template;
	}
	elsif ($email_type eq "T")
	{
		$template_text = $text_template;
	}
	elsif ($email_type eq "A")
	{
		$template_text = $aol_template;
	}
	else 
	{
		$template_text = $text_template;
	}
	
	# If tracking in text then replace with correct information
	
	$_ = $template_text;
	if (/{{TRACKING}}/)
	{
		$tracking_str = "<IMG SRC=\"${bin_dir_http}open_email.cgi?id=$email_user_id&fid=$fm_campaign_id\" border=0 height=1 width=1>\n";
		$template_text =~ s/{{TRACKING}}/$tracking_str/g;
	}

	# substitute <br> for the carriage returns in header
	# and footer if displaying html page

	if ($email_type ne "T")
	{
		$header =~ s/\n/<br>/g;
		$footer =~ s/\n/<br>/g;
	}

	# perform substitution for one time fields in the template

	$template_text =~ s/{{HEADER}}/$header/g;
	$template_text =~ s/{{FOOTER}}/$footer/g;
   #	$template_text =~ s/{{CONTACT_EMAIL}}/$contact_email/g;
   #	$template_text =~ s/{{CONTACT_URL}}/$contact_url/g;
   #	$template_text =~ s/{{CONTACT_PHONE}}/$contact_phone/g;
   #	$template_text =~ s/{{CONTACT_NAME}}/$contact_name/g;
   #	$template_text =~ s/{{CONTACT_COMPANY}}/$contact_company/g;
   	$template_text =~ s/{{EMAIL_ADDR}}/$email_addr/g;
   	$template_text =~ s/{{FIRSTNAME}}/$first_name/g;
		
	$tracking_str = "${bin_dir_http}redir.cgi?id=$email_user_id&fid=$fm_campaign_id&l=";

	# now break up the template into the first part, the repeating part, and the last part
	# get everything in the template up to the {{REPEAT}} field

   	$pos = index($template_text, "{{REPEAT}}");
   	$first_part = substr($template_text, 0, $pos);

	# get repeating section (part between {{REPEAT}} and {{/REPEAT}})

   	$pos2 = index($template_text, "{{/REPEAT}}");
	$grab = $pos2 - $pos + 11;
    $repeat_part = substr($template_text, $pos, $grab);
	$repeat_part =~ s\{{REPEAT}}\\g;
	$repeat_part =~ s\{{/REPEAT}}\\g;

	# get the rest, everything after the {{/REPEAT}}

	$last_part = substr($template_text, $pos2+11);

	# begin building the_email

	$the_email = $first_part;

	$sql = "insert into fm_email (fm_campaign_id, email_user_id, action, 
		action_datetime) values ($fm_campaign_id, $email_user_id, 'S', now())";
   	$rows = $dbh->do($sql);
   	if ($dbh->err() != 0)
   	{
   		$errmsg = $dbh->errstr();
		print "Problem writing fm_email record: $sql; $errmsg\n";
   		exit(0);
   	}

	# loop reading what this user clicked on related to this follow me campaign

	$sql = "select distinct fm_campaign_rule.campaign_id, fm_campaign_rule.article_num 
		from campaign_history, fm_campaign_rule
		where email_user_id = $email_user_id and
		campaign_history.campaign_id = fm_campaign_rule.campaign_id and
		campaign_history.article_num = fm_campaign_rule.article_num and
		fm_campaign_rule.fm_campaign_id = $fm_campaign_id and
		to_days(now()) - to_days(action_datetime) >= $num_days and
		action = 'C'";
	$sth4 = $dbh->prepare($sql);
	$sth4->execute();
	while (($campaign_id, $article_num) = $sth4->fetchrow_array())
	{
		# Get the follow me article

		print "Adding articles for campaign $campaign_id and article $article_num\n";

		$sql = "select fm_article_num
			from fm_campaign_rule 
			where fm_campaign_id = $fm_campaign_id and
			campaign_id = $campaign_id and
			article_num = $article_num";
		$sth5 = $dbh->prepare($sql);
		$sth5->execute();
		while (($fm_article_num) = $sth5->fetchrow_array())
		{

			# find information about this article

			$sql = "select fm_article_title, fm_article_text, fm_article_link,
				fm_article_link_name, fm_article_image_url 
				from fm_article where fm_campaign_id = $fm_campaign_id and
				fm_article_num = $fm_article_num";
			$sth3 = $dbh->prepare($sql);
			$sth3->execute();
			($article_title, $article_text, $article_link, $article_link_name,
				$article_image_url) = $sth3->fetchrow_array();
			$sth3->finish();

			print "Adding follow me article $fm_article_num\n";

			# start over with a fresh "repeat_part" each time

			$this_article = $repeat_part;
			$this_article =~ s/{{ARTICLE_TITLE}}/$article_title/g;
			if ($email_type ne "T")
			{
				# substitute <br> for the carriage returns if displaying html page
				$article_text =~ s/\n/<br>/g;
			}
			$this_article =~ s/{{ARTICLE_TEXT}}/$article_text/g;
			$this_article =~ s/{{ARTICLE_LINK}}/$tracking_str$fm_article_num/g;
			$this_article =~ s/{{ARTICLE_LINK_NAME}}/$article_link_name/g;
			$this_article =~ s/{{ARTICLE_IMAGE_URL}}/$article_image_url/g;
	
			# add this to the end of the email

			$the_email .= $this_article;
		}
		$sth5->finish();
	}
	$sth4->finish();

	# add the rest to the email 

	$the_email .= $last_part;

	print "Done building follow me for $email_addr\n";

	# begin to build the email for this member

	open (MAIL,"| /usr/lib/sendmail -t");
	print MAIL "Reply-To: $from_addr\n"; 
   	print MAIL "From: $from_addr\n";
   	print MAIL "To: $email_addr\n";
   	print MAIL "Subject: $subject\n";
	print MAIL "X-Email: $email_addr:$fm_campaign_id:\n";
			
	# If read receipt requested add special header information
			
	if ($read_receipt eq "Y")
	{
		print MAIL "Mime-Version: 1.0\n";
		print MAIL "Content-Type: multipart/alternative;\n";
       	print MAIL "    boundary=\"----=_NextPart_www.7qxqtzqv0swu60.mailboundry.com\"\n";
		print MAIL "Disposition-Notification-To: $email_mgr_addr\n";
		print MAIL "X-Confirm-Reading-To: $email_mgr_addr\n";
		print MAIL "Return-Receipt-To: $email_mgr_addr\n";
		print MAIL "X-Read-Notification: Courtesy of Lead Dog\n";
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

	# output body of the email

	print MAIL $the_email;

	# add read receipt stuff if needed

	if ($read_receipt eq "Y")
	{
		print MAIL "\n";
		print MAIL "------=_NextPart_www.7qxqtzqv0swu60.mailboundry.com\n";
	}

	# close the MAIL pipe - this sends the email

	close MAIL;

	print "Finished sending follow me mail to $email_addr\n";
}
