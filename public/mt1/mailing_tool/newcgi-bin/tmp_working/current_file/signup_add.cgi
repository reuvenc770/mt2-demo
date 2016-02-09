#!/usr/bin/perl
#===============================================================================
# Purpose: Add a user to 'email_user' table and add to 'list_member' table.
# File   : signup_add.cgi
#
# Input  : From clients signup form
#
# Output : 
#   1. Add record (if not already there) to 'email_user' table
#   2. Add record to 'list_member' table
#
#--Change Control---------------------------------------------------------------
# Mike Baker, 8/17/01  Created.
# SW 03/07/02 Modified to use generic routines so util logos do not display
#===============================================================================

#-----------------------
# include Perl Modules
#-----------------------
use strict;
use CGI;
use util;

#--------------------------------
# declare all local variables
#--------------------------------
my $util = util->new;
my $query = CGI->new;
my ($sth, $rows, $sql, $dbh ) ;
my ($first_name, $last_name, $address ) ;
my ($address2, $city, $state, $zip, $country, $birth_date, $gender ) ;
my ($marital_status, $occupation, $income, $education, $area_code, $phone ) ;
my ($email_addr, $email_type, $username, $password ) ;
my $job_status;
my ($list_id, $list_name);
my ($optin_flag,$thankyou_mail_template,$double_mail_template);
my $user_id;
my ($company, $website_url, $company_phone);
my ($contact_first_name, $contact_last_name, $contact_email_addr);
my $email_body;
my $subject;
my $confirm;
my $cgi_dir = $util->get_cgi_dir;
my $mesg;
my $email_user_id;

# connect to the util database

$util->db_connect();
$dbh = $util->get_dbh;

# get value of all fields on the form

&get_cgi_fields();

# get information about this list. When the user clicks on his email a hidden field
# named list_id will be passed here. Example: list_id = 312. 
# The following sql code will return a single record
# that looks something like this:
#	list_name = "Premium Survey 1"
#	optin_flag = S
#	thankyou_mail_template = "Thank-you for completing our survey! As promised, here's the 
#		coupon code you can use to receive FREE GROUND SHIPPING on any order you place today!
#		Coupon Code: FS652"
#	double_mail_template = "Thanks"
#	user_id = 17

$sql = "select list_name,optin_flag,thankyou_mail_template,double_mail_template,user_id
	from list where list_id = $list_id";
$sth = $dbh->prepare($sql);
$sth->execute();
($list_name,$optin_flag,$thankyou_mail_template,$double_mail_template,
	$user_id) = $sth->fetchrow_array();
$sth->finish();

# read contact information for the company that sent the email. The user_id is obtained
# from the previous sql code above. It might return something like this:
# company = "Premium Ink"
# website_url = "http://www.premiumink.com"
# company_phone = "877-9-INKJET"
# first_name = "Karl"
# last_name = "Hauser"
# email_addr = "newsletter@premiumink.com"

$sql = "select company, website_url, company_phone, first_name, last_name, email_addr 
	from user where user_id = $user_id";
$sth = $dbh->prepare($sql);
$sth->execute();
($company, $website_url, $company_phone, $contact_first_name, $contact_last_name, 
	$contact_email_addr) = $sth->fetchrow_array();
$sth->finish();

# handle single or double optin logic. The optin flag is obtained from the first
# sql statement above. In general, a Single Optin means that a client will be sent
# a thankyou email without having to click further on something when he gets that
# email. A double optin means that the client WILL have to click on a link in a
# thankyou email. This is done for anti-spam purposes. Double optins are preferable in
# one aspect as they provide 1) guaranteed correct email address, and 2) evidence that
# the client HAS in fact chosen to join a list. The downside of this is that the client
# needs to respond to the double optin and thus you may lose the client.

# The value "confirm" seems NEVER to get set. Perhaps a programming error

if ($optin_flag eq "S" || ($optin_flag eq "D" && $confirm == 1))
{
	# single optin or double optin and this is the confirmation link - add this member

	&process_adds();

	# send this user the Thank You email

	&send_thankyou_email();
}
elsif ($optin_flag eq "D")
{
	# double optin - send this member the Double Optin Email 

	&send_double_email();

	$mesg = qq { <font color="#000000"><br><br><b>Thank You.</b>.  <br>
		A Confirmation Email has been sent to <br>
		<font color="#3300FF"><b>$email_addr</b></font> 
		for the Email List: <font color="#3300FF"><b>$list_name</b></font>. <br>
		You must click the link in the email to confirm your subscription.<br> };
		generic_message($mesg);
}

$util->clean_up();
exit(0);

#----------------------------
# End Main Logic
#----------------------------


#===============================================================================
# Sub: get_cgi_fields
#===============================================================================
sub get_cgi_fields
{
	$first_name = $query->param('first_name') ;
	$last_name= $query->param('last_name') ;
	$address = $query->param('address') ;
	$address2 = $query->param('address2') ;
	$city = $query->param('city') ;
	$state = $query->param('state') ;
	$zip  = $query->param('zip') ;
	$country = $query->param('country') ;
	$birth_date = $query->param('bday') ;
	$marital_status = $query->param('marital') ;
	$occupation = $query->param('occupation') ;
	$income = $query->param('income') ;
	$education = $query->param('education') ;
	$job_status = $query->param('job') ;
	$area_code = $query->param('areacode') ;
	$phone = $query->param('phone') ;
	$email_addr = $query->param('email') ;
	$email_type = $query->param('emailtype') ;
	$list_id = $query->param('list_id') ;
	$confirm = $query->param('confirm');

} # end sub get_cgi_fields 


#===============================================================================
# Sub: process_adds
#===============================================================================
sub process_adds
{
	my $counter;

	#----------------------------------------------------------------------------
	# If email_user for specified email_addr does NOT exist then Add email_user 
	#----------------------------------------------------------------------------

	$sql = "select count(*) from email_user where email_addr = '$email_addr'";
	$sth = $dbh->prepare($sql) ;
	$sth->execute();
	($counter) = $sth->fetchrow_array();
	$sth->finish();

	if ($counter == 0) 
	{
		# add this person to the email_user table

		&add_email_user();
	
		# Get the email_user_id just entered

		$sql = "select last_insert_id()";
		$sth = $dbh->prepare($sql);
		$sth->execute();
		($email_user_id) = $sth->fetchrow_array();
		$sth->finish();
	}
	else
	{
		# this person is already in the email_user table.  Get their email_user_id

		$sql = "select email_user_id from email_user where email_addr = '$email_addr'";
		$sth = $dbh->prepare($sql);
		$sth->execute();
		($email_user_id) = $sth->fetchrow_array();
		$sth->finish();

		# update this persons email_user record

		update_email_user();
	}

	#----------------------------------------------------------------------
	# If list_member does NOT exist then ADD list_member 
	#---------------------------------------------------------------------- 
	$sql = "select count(*) from list_member where email_user_id = $email_user_id
		and list_id = $list_id"; 
	$sth = $dbh->prepare($sql) ; 
	$sth->execute(); 
	($counter) = $sth->fetchrow_array() ; 
	if ($counter > 0) 
	{ 
		$mesg = qq { <br><br><font color="#000000">The Email Address: 
			<font color="#3300FF"><b>$email_addr</b></font>
			has already been signed up<br> 
			for the List: <font color="#3300FF"><b>$list_name</b></font>. };
			generic_message($mesg) ;		
		exit(0);
	}
	else
	{
		&add_list_member();
	}
	$sth->finish();

} # end sub process_adds 


#===============================================================================
# Sub: add_email_user
#===============================================================================
sub add_email_user
{
	$phone = $area_code . " " . $phone;
	if ($zip eq "") 
	{
		$zip = "null" ;
	}
	else
	{
		$zip = $dbh->quote($zip);
	}

	if ($marital_status eq "") 
	{
		$marital_status = "null" ;
	}

	if ($occupation eq "") 
	{
		$occupation = "null" ;
	}

	if ($income eq "") 
	{
		$income = "null" ;
	}

	if ($education eq "") 
	{
		$education = "null" ;
	}

	$sql = "insert into email_user (status, first_name, last_name, 
		address, address2, city, state, zip, country, birth_date, 
		gender, marital_status, occupation, income, education, 
		phone, email_addr, create_datetime, user_id, email_type)
		values ('A', '$first_name', '$last_name', 
		'$address', '$address2', '$city', '$state', $zip, '$country', '$birth_date', 
		'$gender', $marital_status, $occupation, $income, $education, 
		'$phone', '$email_addr', now(), $user_id, '$email_type')";
	$rows = $dbh->do($sql) ;
	if ( $rows != 1 ) 
	{
		$mesg = qq{<font color="#000000">Invalid!  Your Sign-Up was NOT successful.<br>} .
			"<br>Please re-check your email address entry and try signing up again." ;
			generic_message($mesg) ;		
		exit(0);
	}

} # end sub add_email_user

#===============================================================================
# Sub: update_email_user
#===============================================================================
sub update_email_user
{
	$phone = $area_code . " " . $phone;
	if ($zip eq "") 
	{
		$zip = "null" ;
	}

	if ($marital_status eq "") 
	{
		$marital_status = "null" ;
	}

	if ($occupation eq "") 
	{
		$occupation = "null" ;
	}

	if ($income eq "") 
	{
		$income = "null" ;
	}

	if ($education eq "") 
	{
		$education = "null" ;
	}

	$sql = "update email_user set first_name = '$first_name', 
		last_name = '$last_name', 
		address = '$address', 
		address2 = '$address2', 
		city = '$city', 
		state = '$state', 
		zip = '$zip', 
		country = '$country', 
		birth_date = '$birth_date', 
		gender = '$gender', 
		marital_status = $marital_status, 
		occupation = $occupation, 
		income = $income, 
		education = $education, 
		phone = '$phone', 
		email_type = '$email_type'
		where email_user_id = $email_user_id";
	$rows = $dbh->do($sql) ;
    if ($dbh->err() != 0)
    {
        my $errmsg = $dbh->errstr();
        util::logerror("Error updating email_user record $sql : $errmsg");
        exit(0);
    }

}

#===============================================================================
# Sub: add_list_member. Inserts this end user into the list_member table and then
# branches off to display a thank you message.
#===============================================================================
sub add_list_member
{
	$rows = 0 ;
	$sql = "insert into list_member (list_id, email_user_id, subscribe_datetime, 
		status) values ($list_id, $email_user_id, now(), 'A')";
	$rows = $dbh->do($sql) ;
	if ( $rows != 1 ) 
	{
		$mesg = qq{<font color="#000000"><br><br>Your Sign-Up was NOT successful!<br>} .
			qq{Email Addr: <font color=RED>$email_addr</font> was NOT signed up for the List: } .
			qq{<font color=RED>$list_name</font>.} ;
			generic_message($mesg) ;		
		exit(0);
	}
	else
	{
		$mesg = qq { <font color="#000000"><br><br><b>Congratulations</b>.  
			You've <b>SUCCESSFULLY</b> Signed up<br>
			the Email User: <font color="#3300FF"><b>$email_addr</b></font> 
			for the Email List: <font color="#3300FF"><b>$list_name</b></font>. };
			thankyou_message($mesg) ;		
	}

} # end sub add_list_member

#===============================================================================
# Sub: send_thankyou_email
#===============================================================================
sub send_thankyou_email
{
	# replace fields with data in thankyou mail template. The variables such
	# as contact_email_addr were obtained in a sql statement above. email_addr
	# is the email_addr of the end user that was entered on the form that he
	# received in the email. In that email give the user 2 links... 1 to edit
	# his record, and the other to unsubscribe

	$email_body = $thankyou_mail_template;
	$email_body =~ s/{{LIST_NAME}}/$list_name/g;
   	$email_body =~ s/{{CONTACT_EMAIL}}/$contact_email_addr/g;
   	$email_body =~ s/{{CONTACT_URL}}/$website_url/g;
   	$email_body =~ s/{{CONTACT_PHONE}}/$company_phone/g;
   	$email_body =~ s/{{CONTACT_NAME}}/$contact_first_name $contact_last_name/g;
   	$email_body =~ s/{{CONTACT_COMPANY}}/$company/g;
   	$email_body =~ s/{{EMAIL_ADDR}}/$email_addr/g;
   	$email_body =~ s\{{EDIT}}\<a href="${cgi_dir}edit_member.cgi?email=$email_user_id">edit</a>\g;
   	$email_body =~ s\{{UNSUBSCRIBE}}\<a href="${cgi_dir}unsubscribe.cgi?email=$email_user_id">unsubscribe</a>\g;
	$subject = "Thank You for subscribing to $list_name";

	# send the mail

	&send_mail();
}

#===============================================================================
# Sub: send_double_email
#===============================================================================
sub send_double_email
{
	# replace fields with data in double optin mail template

	use URI::Escape;

	$email_body = $double_mail_template;
	$email_body =~ s/{{LIST_NAME}}/$list_name/g;
   	$email_body =~ s/{{CONTACT_EMAIL}}/$contact_email_addr/g;
   	$email_body =~ s/{{CONTACT_URL}}/$website_url/g;
   	$email_body =~ s/{{CONTACT_PHONE}}/$company_phone/g;
   	$email_body =~ s/{{CONTACT_NAME}}/$contact_first_name $contact_last_name/g;
   	$email_body =~ s/{{CONTACT_COMPANY}}/$company/g;
   	$email_body =~ s/{{EMAIL_ADDR}}/$email_addr/g;
   	my $optin_url = "${cgi_dir}signup_add.cgi?email=$email_addr&list_id=$list_id&confirm=1&first_name=$first_name&last_name=$last_name&address=$address&address2=$address2&city=$city&state=$state&zip=$zip&country=$country&bday=$birth_date&marital=$marital_status&occupation=$occupation&income=$income&education=$education&job=$job_status&areacode=$area_code&phone=$phone&emailtype=$email_type";
	$optin_url = uri_escape($optin_url);
   	$email_body =~ s\{{DOUBLE_OPTIN_LINK}}\$optin_url  << Click To Confirm Your Subscription\g;
	$subject = "Subscription Confirmation: $list_name";

	# send the mail

	&send_mail();
}

#===============================================================================
# Sub: send_email. variable email_body is set from module send_double_email
# or send_thankyou_email
#===============================================================================
sub send_mail
{
	open (MAIL,"| /usr/lib/sendmail -t");

	# Add mail header stuff
	print MAIL "Reply-To: $contact_email_addr\n"; 
   	print MAIL "From: $contact_email_addr\n";
   	print MAIL "To: $email_addr\n";
   	print MAIL "Subject: $subject\n";
			
	# Add header for text e-mail
	print MAIL "Content-Type: text/plain;       charset=\"iso-8859-1\"\n\n";

	# Add the email body
	print MAIL $email_body;

	close MAIL;
	
	# Send a copy to Scott
#	&send_Scott();
}

sub send_Scott ()
{
	shift;

	my $mailcmd = "/usr/lib/sendmail -t -R full -oi";
	my $from_addr = "system\@powermailsystems.com";
	my $to_addr = "swilson\@cancunassist.com";
	my $subject = "Message from signup_add.cgi";
	my $cdate = localtime();

	open (MAIL,"| /usr/lib/sendmail -t -R full -oi");
	print MAIL "Reply-To: $from_addr\n";
	print MAIL "From: $from_addr\n";
	print MAIL "To: $to_addr\n";
	print MAIL "Subject: $subject\n";
	print MAIL "Content-Type: text/plain;       charset=\"iso-8859-1\"\n\n";

	print MAIL "The following email was attempted to be sent: to $email_addr\n";
	print MAIL "As follows: $email_body\n";

	close MAIL;
}


#===============================================================================
# Sub: generic_message
#===============================================================================
sub generic_message
{
	my $message = shift;

	print "Content-Type: text/html\n\n";
print << "end_of_html";
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
</head>
<body link="#000000" vlink="#000000" alink="#000000">
<TABLE cellSpacing=0 cellPadding=0 align=left bgColor=#ffffff border=0>
<TBODY>
<TR vAlign=top>
<TD noWrap align=left>

	<table border="0" cellpadding="0" cellspacing="0" width="719">
	<tr>
	<TD width=248 bgColor=#FFFFFF rowSpan=2><img border="0" src="/images/email.jpg" 
		width="200" height="58"></TD>
	<TD width=328 bgColor=#FFFFFF>&nbsp;</TD>
	</tr>
	<tr>
	<td width="468">
		<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
		<td align="left"><b><font face="Arial" size="2">&nbsp;System Message</FONT></b>
			</td>
		</tr>
		<tr>
		<td align="right">&nbsp;</td>
		</tr>
		</table>
	</td>
	</tr>
	</table>
</td>
</tr>
<tr>
<td>
    <font face="Verdana,Arial" size="2" color="#509C10">$message</font>
    <br><br><br>
end_of_html

    generic_footer();
}

#===============================================================================
# Sub: generic_footer
#===============================================================================
sub generic_footer
{
print << "end_of_html";
	<br><p align="center">
	&nbsp;</p>
</td>
</tr>
</table>
</body>
</html>
end_of_html
}

#===============================================================================
# Sub: thankyou_message
#===============================================================================
sub thankyou_message
{
	my $message = shift;

	print "Content-Type: text/html\n\n";
print << "end_of_html";
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
</head>
<body link="#000000" vlink="#000000" alink="#000000">
<TABLE cellSpacing=0 cellPadding=0 align=left bgColor=#ffffff border=0>
<TBODY>
<TR vAlign=top>
<TD noWrap align=left>

	<table border="0" cellpadding="0" cellspacing="0" width="719">
	<tr>
	<TD width=400 bgColor=#FFFFFF rowSpan=2><img border="0" src="/images/thankyou.jpg" 
		width="400" height="100"></TD>
	<TD width=176 bgColor=#FFFFFF>&nbsp;</TD>
	</tr>
	<tr>
	<td width="468">
		<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
		<td align="left"><b><font face="Arial" size="2">&nbsp;</FONT></b>
			</td>
		</tr>
		<tr>
		<td align="right">&nbsp;</td>
		</tr>
		</table>
	</td>
	</tr>
	</table>
</td>
</tr>
<tr>
<td>
    <font face="Verdana,Arial" size="2" color="#509C10">&nbsp;</font>
    <br><br><br>
end_of_html

    generic_footer();
}
