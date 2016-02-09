#!/usr/bin/perl
#===============================================================================
# Purpose: Update client info - (eg table 'user' data).
# Name   : client_upd.cgi (update_client_info.cgi)
#
#--Change Control---------------------------------------------------------------
# 08/03/01  Jim Sobeck  Creation
# 08/15/01  Mike Baker  Change to allow 'Admin' User to Update other fields.
#===============================================================================

# include Perl Modules
use strict;
use CGI;
use util;

# get some objects to use later
my $util = util->new;
my $query = CGI->new;
my ($sth, $sql, $dbh, $errmsg ) ;
my ($fname,$lname,$address,$address2,$city,$state,$zip,$phone,$email_addr);
my ($user_type, $max_names, $max_mailings, $status, $pmode, $puserid);
my ($password, $username, $old_username);
my ($medid, $medpw);
my ($rev_share, $mailing_cpm, $broker_fee);
my ($pmesg, $old_email_addr) ;
my $company;
my $website_url;
my $company_phone;
my $images = $util->get_images_url;
my $admin_user;
my $account_type;
my $privacy_policy_url;
my $unsub_option;

#------ connect to the util database ------------------
$util->db_connect();
$dbh = $util->get_dbh;

#----------- check for login ------------------
my $user_id = util::check_security();
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
	$util->clean_up();
    exit(0);
}

###&check_admin_user();    # mebtest

&get_cgi_form_fields();

&validate_modes();

&validate_username();

&validate_email_addr();
	
if ($pmode eq "A" )
{
	&insert_client();
}
else
{
	&update_client();
}

# go to next screen

print "Location: client_disp.cgi?pmode=$pmode&puserid=$puserid&pmesg=$pmesg\n\n";
$util->clean_up();
exit(0);


#===============================================================================
# Sub: get_cgi_form_fields - Set vars from CGI Form Fields
#===============================================================================
sub get_cgi_form_fields
{
	#---------------------------------------------------
	# Get the information about the user from the form 
	#---------------------------------------------------
	$fname = $query->param('fname');
	$lname = $query->param('lname');
	$address = $query->param('address');
	$address2 = $query->param('address2');
	$city = $query->param('city');
	$state = $query->param('state');
	$zip = $query->param('zip');
	$phone = $query->param('phone');
	$email_addr = $query->param('email_addr');
	$old_email_addr = $query->param('old_email_addr');
	$user_type = $query->param('user_type');
	$max_names = $query->param('max_names');
	$max_mailings = $query->param('max_mailings');
	$status = $query->param('status');
	$pmode = $query->param('pmode');
	$puserid = $query->param('puserid');
	$password = $query->param('password');
	$medid= $query->param('medid');
	$medpw= $query->param('medpw');
	$rev_share= $query->param('rev_share');
	$mailing_cpm= $query->param('mailing_cpm');
	$broker_fee = $query->param('broker_fee');
	$username = $query->param('username');
	$username = $query->param('username');
	$old_username = $query->param('old_username');
	$company = $query->param('company');
	$website_url = $query->param('website_url');
	$company_phone = $query->param('company_phone');
	$account_type = $query->param('account_type');
	$privacy_policy_url = $query->param('privacy_policy_url');
	$unsub_option = $query->param('unsub_option');
	
	#---- Set to Upper Case ----------
	$state = uc($state) ;
	$pmode = uc($pmode) ;
	$user_type = uc($user_type);

	#---- Set Max Names to Default of 5 for Demo Users ------------
	if ( $user_type eq "D" ) 
	{
		$max_names = 5 ;
	}

} # end sub get_cgi_form_fields
#
#
#===============================================================================
# Sub: check_admin_user - User must have USER_TYPE = 'A' for this function
#===============================================================================
#sub check_admin_user
#{
#	my ($mesg, $go_back, $go_home, $admin_user) ;
#	#------  Get user Type (MUST be 'A' for Admin ------------
#	$admin_user = "";
#	$sql = "select user_type from user where user_id = $user_id";
#	$sth = $dbh->prepare($sql);
#	$sth->execute();
#	($admin_user) = $sth->fetchrow_array() ;
#	$sth->finish();
#	if ( $admin_user ne "A" ) 
#	{	#---- User was NOT an Administrator -- Display message and stop ---------
# 		$go_back = qq{<br><a href="$ENV{'HTTP_REFERER'}">Back</a>\n };
# 		$go_home = qq{&nbsp;&nbsp;<a href="mainmenu.cgi?userid=$user_id">Home</a>\n };
#		$mesg = "<br><br><b>Invalid</b> - User MUST be an Administrator to use this function!" ;
#		$mesg = $mesg . $go_back . $go_home ;
#		util::logerror($mesg) ;
#		exit(99) ;
#	}
#
#} # end sub - check_admin_user
#
#
#===============================================================================
# Sub: validate_modes - Valid modes are 'A' Add, and 'U' Update - else Stop
#===============================================================================
sub validate_modes
{
	my($go_home, $go_back, $mesg) ;
	#--------------------------------
	# get CGI Form fields
	#--------------------------------
	my $puserid = $query->param('puserid');
	my $pmode   = $query->param('pmode');
	$pmode = uc($pmode);
	if ( $pmode ne "A"  and  $pmode ne "U" ) 
	{	#---- Invalid MODE - Mode MUST = 'A' (add)  or  'U' (update)  ---------
		$go_back = qq{<br><a href="$ENV{'HTTP_REFERER'}">Back</a>\n };
 		$go_home = qq{&nbsp;&nbsp;<a href="mainmenu.cgi?userid=$user_id">Home</a>\n };
		$mesg = qq{<br><br><b>Invalid</b> Mode: <b>$pmode</b> - The Mode MUST equal 'A' or 'U'.} ;
		$mesg = $mesg . $go_back . $go_home ;
		util::logerror($mesg) ;
	}

} # end sub - validate_modes


#===============================================================================
# Sub: update_client
#===============================================================================
sub update_client
{
	$sql = "update user set first_name='$fname', last_name='$lname', address='$address',
		address2='$address2', city='$city', state='$state', zip='$zip', phone='$phone',
		email_addr='$email_addr', status='$status', max_names=$max_names, 
		max_mailings=$max_mailings, user_type='$user_type', password='$password', 
		username='$username',
		company = '$company',
		website_url = '$website_url',
		company_phone = '$company_phone',
		account_type = '$account_type',
		privacy_policy_url = '$privacy_policy_url',
		unsub_option = '$unsub_option',
		mediactivate_id='$medid',
		mediactivate_pw='$medpw',
		rev_share='$rev_share',
		mailing_cpm='$mailing_cpm',
		broker_fee='$broker_fee' 
		where user_id = $puserid";
	$sth = $dbh->do($sql);
	if ($dbh->err() != 0)
	{
		$errmsg = $dbh->errstr();
	    $pmesg = "Error - Updating user record for UserID: $puserid $errmsg";
		open(LOG,">/tmp/jim.log");
		print LOG "$sql\n";
		close LOG;
	}
	else
	{
	    $pmesg = "Successful UPDATE of Contact Info!" ;
	}

}  # end sub - update_client


#===============================================================================
# Sub: insert_client
#===============================================================================
sub insert_client
{
	my $rows;

	# add user to database

	$sql = "insert into user (first_name, last_name, address, address2, city,
		state, zip, phone, email_addr, status, max_names, max_mailings, user_type, 
		username, password, company, website_url, company_phone, account_type,
		privacy_policy_url, unsub_option,mediactivate_id,mediactivate_pw,rev_share,mailing_cpm, broker_fee) 
		values ('$fname', '$lname', '$address', '$address2', '$city',
		'$state', $zip, '$phone', '$email_addr', '$status', $max_names, $max_mailings, 
		'$user_type', '$username', '$password', '$company', '$website_url', '$company_phone',
		'$account_type', '$privacy_policy_url', '$unsub_option','$medid','$medpw','$rev_share','$mailing_cpm', '$broker_fee')";
	$sth = $dbh->do($sql);
	if ($dbh->err() != 0)
	{
	    $pmesg = "Error - Inserting user record: $sql - $errmsg";
	}
	else
	{
	    $pmesg = "Successful INSERT of Contact Info!" ;
	}

	# get id of client just inserted 

	$sql = "select last_insert_id()";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	($puserid) = $sth->fetchrow_array() ;
	$sth->finish();

	$sql = "insert into client_category_info select category_id,$puserid,NULL from category_info";
	$rows = $dbh->do($sql);

	# insert default signup form for this user

	$sql = "insert into user_signup_form (user_id, show_first_name, show_last_name, show_zip)
    	values ($puserid, 'Y', 'Y', 'Y')";
	$rows = $dbh->do($sql);

	$pmode = "U" ;

}  # end sub - insert_client


#===============================================================================
# Sub: validate_email_addr 
#  - If Email Addr has changed then check for Dups - If Dups - Mesg then stop
#===============================================================================

sub validate_email_addr 
{
	my ($rows, $mesg, $go_back, $go_home) ;
	
	if ( $email_addr ne $old_email_addr )
	{
		$rows = 0 ;
		$sql = "select count(*) from user where email_addr = '$email_addr' " ;

		$sth = $dbh->prepare($sql);
		$sth->execute();
		($rows) = $sth->fetchrow_array() ;
		$sth->finish();

		if ( $rows > 0 )   
		{	#---- Clients MUST have Unique Email Addrs (eg No Dup Emails) -----
 			$go_back = qq{<br><br><a href="$ENV{'HTTP_REFERER'}">Back</a>\n };
 			$go_home = qq{&nbsp;&nbsp;<a href="mainmenu.cgi?userid=$user_id">Home</a>\n };
			$mesg = qq{ <font color="#509C10"><br><br><b><font color="red">Invalid</font></b> } . 
				qq{- A Client already exits with the Email: <font color="red">$email_addr</font>.} . 
				qq{<Br>Each Client MUST have a unique Email Address!</font> } ;
			$mesg = $mesg . $go_back . $go_home ;
			util::logerror($mesg) ;
			exit(99) ;
		}
	}
} # end sub - validate_email_addr


#===============================================================================
# Sub: validate_username 
#===============================================================================
sub validate_username 
{
	my ($rows, $mesg, $go_back, $go_home) ;
	
	if ( $username ne $old_username )
	{
		$rows = 0 ;
		$sql = "select count(*) from user where username = '$username' " ;
		$sth = $dbh->prepare($sql);
		$sth->execute();
		($rows) = $sth->fetchrow_array() ;
		$sth->finish();

		if ( $rows > 0 )   
		{	#---- Clients MUST have Unique Email Addrs (eg No Dup Emails) -----
 			$go_back = qq{<br><br><a href="$ENV{'HTTP_REFERER'}">Back</a>\n };
 			$go_home = qq{&nbsp;&nbsp;<a href="mainmenu.cgi?userid=$user_id">Home</a>\n };
			$mesg = qq{ <font color="#509C10"><br><br><b><font color="red">Invalid</font></b> } . 
				qq{- A Client already exits with the UserName: <font color="red">$username</font>.} . 
				qq{<Br>Each Client MUST have a unique Username!</font> } ;
			$mesg = $mesg . $go_back . $go_home ;
			util::logerror($mesg) ;
			exit(99) ;
		}
	}
} # end sub - validate_username
