#!/usr/bin/perl
#===============================================================================
# Purpose: Update client info - (eg table 'user' data).
# Name   : client_upd.cgi (update_client_info.cgi)
#
#--Change Control---------------------------------------------------------------
# 08/03/01  Jim Sobeck  Creation
# 08/15/01  Mike Baker  Change to allow 'Admin' User to Update other fields.
# 11/26/07  Jim Sobeck  Added logic to use client_thirdparty table
#===============================================================================

# include Perl Modules
use strict;
use CGI;
use util;
use Data::Dumper;
use CGI::Carp qw(fatalsToBrowser);

# get some objects to use later
my $util = util->new;
my $query = CGI->new;
my ($sth, $sql, $dbh, $errmsg ) ;
my ($fname,$lname,$address,$address2,$city,$state,$zip,$phone,$email_addr);
my ($user_type, $max_names, $max_mailings, $status, $pmode, $puserid);
my $flatfile;
my ($password, $username, $old_username);
my ($medid, $medpw);
my ($imedid, $imedpw);
my ($rmedid, $rmedpw);
my $hitpath_id;
my $medid;
my $tstr;
my $rows;
my $medpw;
my $tid;
my $mailer_name;
my $thitpath;
my ($rev_share, $mailing_cpm, $broker_fee, $rev_threshold, $adj);
my ($cl_type, $cl_company, $cl_main_name, $cl_main_email, $cl_tech_name, $cl_tech_email, $ftp_url, $upl_freq, $ftp_user, $ftp_pw, $rt_pw);
my $tempsource;
my ($pmesg, $old_email_addr) ;
my $company;
my $website_url;
my $company_phone;
my $images = $util->get_images_url;
my $admin_user;
my $account_type;
my $privacy_policy_url;
my $unsub_option;
my $overall_db;
my $newest_db;
my $double_optin;
my $disable_triggers;
my $product_client;
my $clientTypeId;
my $realtime_flatfile;
my $clientGroupNames;
my $clientGroupName;
my $showRecordProcessing;
my $showUniqueCounts;
my $revenueDisplayTypeID;
my $revenueDisplayTypeLabel;
my $revenueDisplayTypeIDLabel;
my $previousClientGrouping;
my $resetPassword;
my $checkOCDataTest;
my $minimumAcceptableRecordDate;
my $clientRecordSourceURL;
my $clientRecordIP;
my $clientRecordCaptureDate;
my $cakeSubaffiliateID;
my $uniqueProfileID;
my $hasClientGroupRestriction;
my $countryID;
my $CheckGlobalSuppression;
my $OrangeClient;

#------ connect to the util database ------------------
my ($dbhq,$dbhu)=$util->get_dbh();

#----------- check for login ------------------
my $user_id = util::check_security();
if ($user_id == 0) 
{
    print "Location: ../notloggedin.cgi\n\n";
	$util->clean_up();
    exit(0);
}

&get_cgi_form_fields();
&validate_modes();
&validate_username();

## - JES - Removed 09/20 - &validate_email_addr();
	
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
	$fname = $query->param('username');
	$lname = $query->param('username');
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
	$flatfile= $query->param('flatfile');
	$pmode = $query->param('pmode');
	$puserid = $query->param('puserid');
	$password = $query->param('password');
	$medid= $query->param('medid');
	$medpw= $query->param('medpw');
	$hitpath_id=$query->param('hitpath_id');
	$rev_share= $query->param('rev_share');
	$mailing_cpm= $query->param('mailing_cpm');
	$broker_fee = $query->param('broker_fee');
	$rev_threshold = $query->param('rev_threshold');
	$adj = $query->param('adjustment');
	$username = $query->param('username');
	$old_username = $query->param('old_username');
	$company = $query->param('company');
	$website_url = $query->param('website_url');
	$company_phone = $query->param('company_phone');
	$account_type = $query->param('account_type');
	$privacy_policy_url = $query->param('privacy_policy_url');
	$unsub_option = $query->param('unsub_option');
	$cl_type = $query->param('cl_type');
	$cl_company = $query->param('cl_company');
	$cl_main_name= $query->param('cl_main_name');
	$cl_main_email= $query->param('cl_main_email');
	$cl_tech_name= $query->param('cl_tech_name');
	$cl_tech_email= $query->param('cl_tech_email');
	$upl_freq= $query->param('upl_freq');
	$ftp_url= $query->param('ftp_url');
	$ftp_user= $query->param('ftp_user');
	$ftp_pw= $query->param('ftp_pw');
	$rt_pw= $query->param('rt_pw');
	$tempsource= $query->param('tempsource');
	$overall_db=$query->param('overall_db');
	$newest_db=$query->param('newest_db');
	$double_optin=$query->param('double_optin');
	$disable_triggers=$query->param('disable_triggers');
	$product_client =$query->param('product_client');
	$realtime_flatfile=$query->param('realtime_flatfile');
	$clientTypeId = $query->param('clientTypeId'); 
	$countryID = $query->param('countryID'); 
	$CheckGlobalSuppression= $query->param('CheckGlobalSuppression'); 
	$OrangeClient = $query->param('OrangeClient'); 
	
	$clientGroupNames = $query->param('clientGroupNames'); 
	$clientGroupName = $query->param('clientGroupName'); 
	$showRecordProcessing = $query->param('showRecordProcessing'); 
	$showUniqueCounts = $query->param('showUniqueCounts'); 
	$revenueDisplayTypeID = $query->param('revenueDisplayTypeID') || 1; 
	$revenueDisplayTypeIDLabel = $query->param('revenueDisplayTypeLabel'); 
	$previousClientGrouping = $query->param('previousClientGrouping'); 
	
	$uniqueProfileID = $query->param('uniqueProfileID'); 
	
	$hasClientGroupRestriction = $query->param('hasClientGroupRestriction') || 0; 
		
	if($hitpath_id eq '')
	{
		$hitpath_id = 'null';
	}
	
	if($clientTypeId eq '')
	{
		$clientTypeId = 'null';
	}
	
	if($countryID eq '')
	{
		$countryID = 'null';	
	}
	if ($CheckGlobalSuppression eq '')
	{
		$CheckGlobalSuppression='Y';
	}
	if ($OrangeClient eq '')
	{
		$OrangeClient='N';
	}
	
	if($uniqueProfileID eq '')
	{
		$uniqueProfileID = 0;
	}
	
	$resetPassword = $query->param('resetPassword') || 0; 
	
	$checkOCDataTest = $query->param('checkOCDataTest') || 0; 
	
	if($query->param('minimumAcceptableRecordDate') eq '')
	{
		$minimumAcceptableRecordDate = 'NULL';
	}
	
	else
	{
		$minimumAcceptableRecordDate = qq|"| . $query->param('minimumAcceptableRecordDate') . qq|"|;
	}
	
	$clientRecordSourceURL = $query->param('clientRecordSourceURL'); 
	$clientRecordIP = $query->param('clientRecordIP'); 
	
	## featured was disabled bc of careless use by India team
	$clientRecordCaptureDate = 'NULL';
		
	$cakeSubaffiliateID = $query->param('cakeSubaffiliateID') || '';

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
#	$sth = $dbhq->prepare($sql);
#	$sth->execute();
#	($admin_user) = $sth->fetchrow_array() ;
#	$sth->finish();
#	if ( $admin_user ne "A" ) 
#	{	#---- User was NOT an Administrator -- Display message and stop ---------
# 		$go_back = qq{<br><a href="$ENV{'HTTP_REFERER'}">Back</a>\n };
# 		$go_home = qq{&nbsp;&nbsp;<a href="../mainmenu.cgi?userid=$user_id">Home</a>\n };
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
 		$go_home = qq{&nbsp;&nbsp;<a href="../mainmenu.cgi?userid=$user_id">Home</a>\n };
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
	
	my $params = {
		'newClient'				  => 0,
		'userID' 				  => $puserid,
		'showRecordProcessing'    => $showRecordProcessing, 
		'showUniqueCounts'		  => $showUniqueCounts,
		'revenueDisplayTypeID'	  => $revenueDisplayTypeID,
		'newClientGroupingName'   => $clientGroupName,
		'clientGroupingNameList' => $clientGroupNames,
		'revenueDisplayTypeLabel' => $revenueDisplayTypeLabel,
		'previousClientGrouping'  => $previousClientGrouping,
	};
	
	#print Dumper($params);
	
	my $clientGroupingID = modifyClientGrouping($params);

	updateClientStatsSettings($params);
	
	## remove client from ClientGroup
	if($status ne 'A')
	{
		my $sql = qq|delete from ClientGroupClients where client_id = $puserid|;
		$dbhu->do($sql);
	}
	
	my $dateDeleted = 'null';
	
	if($status eq 'D')
	{
		$dateDeleted = qq|current_date()|;
	}
	
	my $changeSql = qq|
	insert into 
		UserTableChangeLog
	select 
		$user_id, current_date(), u.*
	from 
		user u
	where 
		u.user_id = $puserid
	|;
	
	$dbhu->do($changeSql);
	
	## update all client groupings/settings
	$sql = qq|
	update user 
	set 
		first_name="$fname", 
		last_name="$lname", 
		address="$address",
		address2="$address2", 
		city="$city", 
		state="$state", 
		zip="$zip", 
		phone="$phone",
		email_addr="$email_addr", 
		status="$status", 
		user_type="$user_type", 
		password="$password", 
		username="$username",
		company = "$company",
		website_url = "$website_url",
		account_type = "$account_type",
		hitpath_id="$hitpath_id",
		client_type="$cl_type",
		client_main_name="$cl_main_name",
		upl_freq="$upl_freq",
		ftp_url="$ftp_url",
		ftp_user="$ftp_user",
		ftp_pw="$ftp_pw",
		rt_pw="$rt_pw",
		clientTypeId = $clientTypeId,
		clientStatsGroupingID = $clientGroupingID,
		newClient = $resetPassword,
		dateDeleted = $dateDeleted,
		checkPreviousOC = $checkOCDataTest,
		minimumAcceptableRecordDate = $minimumAcceptableRecordDate,
		clientRecordSourceURL = "$clientRecordSourceURL",
		clientRecordIP = "$clientRecordIP",
		clientRecordCaptureDate = $clientRecordCaptureDate,
		cakeSubaffiliateID = $cakeSubaffiliateID,
		uniqueProfileID = $uniqueProfileID,
		hasClientGroupRestriction = $hasClientGroupRestriction,
		countryID = $countryID,
		CheckGlobalSuppression='$CheckGlobalSuppression',
		OrangeClient='$OrangeClient'
	where user_id = $puserid|;
	
	$dbhu->do($sql);
	#print STDERR "$sql \n";
	
	if ($dbhu->err() != 0)
	{
		$errmsg = $dbhu->errstr();
	    $pmesg = "Error - Updating user record for UserID: $puserid $errmsg";
		open(LOG,">/tmp/jim.log");
		print LOG "$sql\n";
		close LOG;
	}
	else
	{
	    $pmesg = "Successful UPDATE of Contact Info!" ;
	}
	
	$sql="delete from client_thirdparty where user_id=$puserid";
	$rows = $dbhu->do($sql);
	$sql="select third_party_id,mailer_name from third_party_defaults where status='A' and third_party_id != 10 order by mailer_name";
	my $sth1=$dbhu->prepare($sql);
	$sth1->execute();
	while (($tid,$mailer_name)=$sth1->fetchrow_array())
	{
		$tstr="med_".$tid;
		$medid=$query->param($tstr);
		$tstr="medpw_".$tid;
		$medpw=$query->param($tstr);
		$tstr="hitpath_".$tid;
		$thitpath=$query->param($tstr);
		$sql="insert into client_thirdparty(user_id,third_party_id,mediactivate_id,mediactivate_pw,hitpath_id) values($puserid,$tid,'$medid','$medpw','$thitpath')";	
		$rows = $dbhu->do($sql);
	}
	$sth1->finish();

} 

sub updateClientStatsSettings {
	
	my ($params) = @_;
	
	my $whereClause = qq|clientID = $params->{'userID'}|;
	
	if($params->{'clientGroupName'}){
		$whereClause = qq|csg.clientStatsGroupingLabel = "$params->{'clientGroupName'}"|;
	}
	
	my $sql = qq|
	update 
		ClientStatsSettings css
		JOIN user u on css.clientID = u.user_id
		LEFT OUTER JOIN ClientStatsGrouping csg ON u.clientStatsGroupingID = csg.clientStatsGroupingID
	set 
		css.revenueDisplayTypeID = $params->{'revenueDisplayTypeID'},
		css.showRecordProcessing = $params->{'showRecordProcessing'},
		css.showUniqueCounts 	 = $params->{'showUniqueCounts'}
	where 
		$whereClause|;
	
	#print "$sql \n";
	$dbhu->do($sql);

}

sub insertClientStatsSettings {
	
	my ($params) = @_;

	my $sql = qq|
	insert ignore into ClientStatsSettings
		(clientID, revenueDisplayTypeID, showRecordProcessing, showUniqueCounts)
	values
		($params->{'userID'}, $params->{'revenueDisplayTypeID'}, $params->{'showRecordProcessing'}, $params->{'showUniqueCounts'})
	|;

	$dbhu->do($sql);

}

sub modifyClientGrouping {
	
	my ($params) = @_;
	
	$params->{'clientGroupName'} = $params->{'clientGroupingNameList'};
	
	my $clientStatsGroupingID = 'NULL';

	## insert new group
	if($params->{'newClientGroupingName'} ne '')
	{
		$clientStatsGroupingID = insertClientStatsGrouping($params->{'newClientGroupingName'});
		
		$params->{'newClientGroupingName'} =~ tr/[A-Z]/[a-z]/;
		$params->{'clientGroupName'} = $params->{'newClientGroupingName'};
		
	}
	
	## get/set id
	elsif(
		($params->{'clientGroupingNameList'} ne '') 
	&& 
		($params->{'newClientGroupingName'} eq '')
	)
	{
		
#		#use existing list for client setup
#		if($params->{'newClient'}){
#			$params->{'clientGroupName'} = $params->{'clientGroupingNameList'};
#		}
#		
#		#user previous client grouping so we can update it later
#		elsif($params->{'previousClientGrouping'} ne ''){
#			$params->{'clientGroupName'} = $params->{'previousClientGrouping'};
#		}
	
		my $sql = qq|
		select 
			clientStatsGroupingID 
		from 
			ClientStatsGrouping 
		where 
			clientStatsGroupingLabel = "$params->{'clientGroupName'}"
			
		|;
		
		#die Dumper($params);
		#print STDERR "$sql \n";
			
		my $sth = $dbhu->prepare($sql);
		$sth->execute();
		
		my $clientData = $sth->fetchrow_hashref();
		
		$clientStatsGroupingID = $clientData->{'clientStatsGroupingID'};
		
	}
		
#	print "clientStatsGroupingID is $clientStatsGroupingID \n";
#	print "clientGroupName is $params->{'clientGroupName'} \n";
#	print "previousClientGrouping is $params->{'previousClientGrouping'} \n";
#	print "newClient is $params->{'newClient'} \n";
		 	
	#update all clients with new client stats grouping
	if(
		(!$params->{'newClient'})
	&&
		($params->{'previousClientGrouping'} ne '')
	&&
		($params->{'clientGroupName'} ne $params->{'previousClientGrouping'})
	)
	{
				
		my $sql = qq|
		update user 
			set clientStatsGroupingID = $clientStatsGroupingID
		WHERE 
			clientStatsGroupingID = (select clientStatsGroupingID from ClientStatsGrouping 
				WHERE clientStatsGroupingLabel = "$params->{'previousClientGrouping'}")
		AND user_id = "$params->{'userID'}"
		|;
				
		$dbhu->do($sql);
		#print "$sql \n";
		
	}
	
	#print STDERR "client grouping id is $clientStatsGroupingID\n";

	return($clientStatsGroupingID);
	
}

sub insertClientStatsGrouping {
	
	my ($clientGroupName) = @_;
	
	my $baseClientGroupName = $clientGroupName;
	
	$clientGroupName =~ tr/[A-Z]/[a-z]/; 
	
	my $sql = qq|
	insert ignore into ClientStatsGrouping
		(clientStatsGroupingName, clientStatsGroupingLabel)
	values
		("$baseClientGroupName", "$clientGroupName")
	|;

	$dbhu->do($sql);
	
	return(lastInsertID());
	
	#print "$sql \n";
	
}

sub updateClientStatsGrouping {
	
	my ($params) = @_;
	
	my $updateQuery = qq|
	update 
		ClientStatsGrouping
	set 
	where clientStatsGroupingLabel = "$params->{'revenueDisplayTypeLabel'}"|;
	
	$dbhu->do($updateQuery);
	
	my $sql = "select clientStatsGroupingID from ClientStatsGrouping";
	my $sth = $dbhu->prepare($sql);
	$sth->execute();

	my $data = $sth->fetchrow_hashref();
	
	return($data->{'clientStatsGroupingID'});
	
}

sub lastInsertID {

	my $sql = "select LAST_INSERT_ID() as lastInsertID";
	my $sth = $dbhu->prepare($sql);
	$sth->execute();
	
	my $data = $sth->fetchrow_hashref();

	return($data->{'lastInsertID'});
	
}
#===============================================================================
# Sub: insert_client
#===============================================================================
sub insert_client
{
	my $rows;
	
	my $params = {
		'newClientGroupingName'   => $clientGroupName,
		'clientGroupingNameList'  => $clientGroupNames,
		'newClient'				  => 1,
	};

	my $clientStatsGroupingID = modifyClientGrouping($params);
	
	#lowercase and get rid spaces
	$username = lc($username);
	$username =~ s/\s+//g;
	
	#lowercase and get rid spaces
	$password = lc($password);
	$password =~ s/\s+//g;

	## this shouldnt be hard coded but it is :)
	my $cakeAffiliateID = 13;

	$sql = qq|insert into user (
	first_name, 
	last_name, 
	address, 
	address2, 
	city, 
	state, 
	zip, 
	phone, 
	email_addr, 
	status, 
	user_type, 
	username, 
	password, 
	company, 
	website_url, 
	account_type,  
	client_type, 
	client_company, 
	client_main_name, 
	upl_freq, 
	ftp_url, 
	ftp_user, 
	ftp_pw, 
	rt_pw,
	hitpath_id, 
	clientTypeId, 
	clientStatsGroupingID, 
	checkPreviousOC, 
	minimumAcceptableRecordDate,
	clientRecordSourceURL,
	clientRecordIP,
	clientRecordCaptureDate, 
	cakeSubaffiliateID,
	uniqueProfileID, 
	cakeAffiliateID,
	hasClientGroupRestriction,
	countryID,
	CheckGlobalSuppression,
	OrangeClient) 
	
	values (
	"$fname", 
	"$lname", 
	"$address", 
	"$address2", 
	"$city", 
	"$state", 
	"$zip", 
	"$phone", 
	"$email_addr", 
	"$status", 
	"$user_type", 
	"$username", 
	"$password", 
	"$company", 
	"$website_url",  
	"$account_type", 
	"$cl_type", 
	"$cl_company", 
	"$cl_main_name", 
	"$upl_freq", 
	"$ftp_url", 
	"$ftp_user", 
	"$ftp_pw", 
	"$rt_pw",
	"$hitpath_id",
	$clientTypeId,
	$clientStatsGroupingID, 
	$checkOCDataTest,
	$minimumAcceptableRecordDate, 
	"$clientRecordSourceURL", 
	"$clientRecordIP", 
	$clientRecordCaptureDate, 
	(select max(cakeSubAffiliateID) + 1 from user as u),
	$uniqueProfileID, 
	$cakeAffiliateID,
	$hasClientGroupRestriction,
	$countryID,"$CheckGlobalSuppression","$OrangeClient")|;
	
	#print STDERR "$sql \n";

	$sth = $dbhu->do($sql);
	if ($dbhu->err() != 0)
	{
	    $pmesg = "Error - Inserting user record: $sql - $errmsg";
	}
	else
	{
	    $pmesg = "Successful INSERT of Contact Info!" ;
	}
	
	# get id of client just inserted 
	$sql = "select last_insert_id()";
	$sth = $dbhu->prepare($sql);
	$sth->execute();
	($puserid) = $sth->fetchrow_array() ;
	
	if (($cl_type ne "ESP") and ($OrangeClient eq "Y"))
	{
		my $gname="Orange".$company;
		$sql = "insert into ClientGroup(userID, group_name,status,excludeFromSuper,BusinessUnit) values ($user_id, $gname,'A','N','Orange')";
		my $rows = $dbhu->do($sql);

		my $client_group_id;
		$sql = "select last_insert_id()";
		$sth = $dbhu->prepare($sql);
		$sth->execute();
		($client_group_id) = $sth->fetchrow_array() ;

		$sql="insert into ClientGroupClients(client_group_id,client_id) values($client_group_id,$puserid)";
		$rows = $dbhu->do($sql);

		if ($countryID == 1)
		{
			$sql="insert ignore into ClientGroupClients(client_group_id,client_id) values(8598,$puserid)";
			$rows = $dbhu->do($sql);
		}
		elsif ($countryID == 235)
		{
			$sql="insert ignore into ClientGroupClients(client_group_id,client_id) values(8599,$puserid)";
			$rows = $dbhu->do($sql);
		}
	}

	gen_tracking($puserid);
	
	my $statsParams = {
		'userID' 				  => $puserid,
		'showRecordProcessing'    => $showRecordProcessing, 
		'showUniqueCounts'		  => $showUniqueCounts,
		'revenueDisplayTypeID'	  => $revenueDisplayTypeID,
		'revenueDisplayTypeLabel' => $revenueDisplayTypeLabel,
	};
	
	insertClientStatsSettings($statsParams);

	$sql = qq|insert ignore into user_file_layout (checkOCDataTest) values ($checkOCDataTest)|;
	$dbhu->do($sql);
	
	$sql = "insert into client_category_info select category_id,$puserid,NULL from category_info";
	$rows = $dbhu->do($sql);

	$sql="insert into CopyScheduleClient(client_id) values($puserid)";
	$rows = $dbhu->do($sql);

	$sql="delete from client_thirdparty where user_id=$puserid";
	$rows = $dbhu->do($sql);

	$sql="select third_party_id,mailer_name from third_party_defaults where status='A' and third_party_id != 10 order by mailer_name";
	my $sth1=$dbhu->prepare($sql);
	$sth1->execute();
	while (($tid,$mailer_name)=$sth1->fetchrow_array())
	{
		$medid=$query->param('med_$tid');
		$medpw=$query->param('medpw_$tid');
		$thitpath=$query->param('hitpath_$tid');
		$sql="insert into client_thirdparty(user_id,third_party_id,mediactivate_id,mediactivate_pw,hitpath_id) values($puserid,$tid,'$medid','$medpw','$thitpath')";	
		$rows = $dbhu->do($sql);
	}
	$sth1->finish();

	# insert default signup form for this user

#	$sql = "insert into user_signup_form (user_id, show_first_name, show_last_name, show_zip)
#    	values ($puserid, 'Y', 'Y', 'Y')";
#	$rows = $dbhu->do($sql);
	addBrand($puserid);

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

		$sth = $dbhq->prepare($sql);
		$sth->execute();
		($rows) = $sth->fetchrow_array() ;
		$sth->finish();

		if ( $rows > 0 )   
		{	#---- Clients MUST have Unique Email Addrs (eg No Dup Emails) -----
 			$go_back = qq{<br><br><a href="$ENV{'HTTP_REFERER'}">Back</a>\n };
 			$go_home = qq{&nbsp;&nbsp;<a href="../mainmenu.cgi?userid=$user_id">Home</a>\n };
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
		$sth = $dbhq->prepare($sql);
		$sth->execute();
		($rows) = $sth->fetchrow_array() ;
		$sth->finish();

		if ( $rows > 0 )   
		{	#---- Clients MUST have Unique Email Addrs (eg No Dup Emails) -----
 			$go_back = qq{<br><br><a href="$ENV{'HTTP_REFERER'}">Back</a>\n };
 			$go_home = qq{&nbsp;&nbsp;<a href="../mainmenu.cgi?userid=$user_id">Home</a>\n };
			$mesg = qq{ <font color="#509C10"><br><br><b><font color="red">Invalid</font></b> } . 
				qq{- A Client already exits with the UserName: <font color="red">$username</font>.} . 
				qq{<Br>Each Client MUST have a unique Username!</font> } ;
			$mesg = $mesg . $go_back . $go_home ;
			util::logerror($mesg) ;
			exit(99) ;
		}
	}
} # end sub - validate_username

sub gen_tracking
{
	my ($input_client_id)=@);
	my $url;
	my $aid;
	my $sth1;
	my $hitpath_id;
	my $mid;
	my $client_id;
	my $sth2;
	my $thirdparty_hitpath_id;
	my $thitpath_id;
	my @type = ( "N");
	
	$util->genLinks($dbhu,0,$input_client_id);
}

sub addBrand
{
	my ($client_id)=@_;
	my $bid=4243;
	my $newbid;

	my $sql = "insert into client_brand_info(client_id,brand_name,brand_fullname,others_ns1,others_ns2,yahoo_ns1,yahoo_ns2,others_ip,yahoo_ip,mailing_addr1,mailing_addr2,phone,whois_email,abuse_email,personal_email,dns_host, clean_host, others_host,yahoo_host,footer_text,header_text,footer_variation,footer_color_id,footer_bg_color_id,cleanser_ns1,cleanser_ns2,footer_font_id,notes,aolw_flag,aol_comments,brand_type,third_party_id,font_type,font_size,align,nl_id,from_address, display_name, replace_domain, brand_priority,num_domains_rotate,client_type,privacy_img,newsletter_header,newsletter_footer,unsub_img,subject) select $client_id,brand_name,brand_fullname,others_ns1,others_ns2,yahoo_ns1,yahoo_ns2,others_ip,yahoo_ip,mailing_addr1,mailing_addr2,phone,whois_email,abuse_email,personal_email,dns_host, clean_host, others_host,yahoo_host,footer_text,header_text,footer_variation,footer_color_id,footer_bg_color_id,cleanser_ns1,cleanser_ns2,footer_font_id,notes,aolw_flag,aol_comments,brand_type,third_party_id,font_type,font_size,align,nl_id,from_address, display_name, replace_domain, brand_priority,num_domains_rotate,client_type,privacy_img,newsletter_header,newsletter_footer,unsub_img,subject from client_brand_info where brand_id=$bid";
	my $rows=$dbhu->do($sql);

	$sql="select LAST_INSERT_ID()";
	$sth=$dbhq->prepare($sql);
	$sth->execute();
	($newbid)=$sth->fetchrow_array();
	$sth->finish();

	$sql = "insert into category_brand_info(brand_id,subdomain_id) select $newbid,subdomain_id from category_brand_info where brand_id=$bid";
	$rows=$dbhu->do($sql);

	$sql="insert into brand_url_info(brand_id,url_type,url) select $newbid,url_type,LCASE(url) from brand_url_info where brand_id=$bid";
	$rows=$dbhu->do($sql);

	$sql="insert into brand_available_domains(brandID,domain,type,rank,inService) select $newbid,LCASE(domain),type,rank,inService from brand_available_domains where brandID=$bid";
	$rows=$dbhu->do($sql);

	$sql="insert into brand_advertiser_info(brand_id,advertiser_id,domain_name) select $newbid,advertiser_id,domain_name from brand_advertiser_info where brand_id=$bid";
	$rows=$dbhu->do($sql);
}
