#!/usr/bin/perl
#===============================================================================
# Purpose: Edit client demographic data (eg 'user' table).
# Name   : client_disp.cgi (edit_client_info.cgi)
#
#--Change Control---------------------------------------------------------------
# 08/03/01  Jim Sobeck  Creation
# 08/15/01  Mike Baker  Change to allow 'Admin' User to Update other fields.
#===============================================================================

#-----  include Perl Modules ---------
use strict;
use CGI;
use util;
use Data::Dumper;
use CGI::Carp qw(fatalsToBrowser);

#------  get some objects to use later ---------
my $util = util->new;
my $query = CGI->new;
my $sth;
my $sql;
my $dbh;
my $errmsg;
my ($fname,$lname,$address,$address2,$city,$state,$zip,$phone,$email_addr);
my ($status, $max_names, $max_mailings, $username);
my ($mediactivate_id, $mediactivate_pw);
my ($rev_share, $mailing_cpm, $broker_fee, $rev_threshold, $adj, $cl_type);
my ($cl_company, $cl_main_name, $cl_main_email, $cl_tech_name, $cl_tech_email, $ftp_url, $upl_freq, $ftp_user, $ftp_pw, $rt_pw);
my $tempsource;
my $password;
my ($puserid, $pmesg);
my $company;
my $website_url;
my $company_phone;
my $realtime_flatfile;
my $user_type;
my $this_user_type;
my $images = $util->get_images_url;
my $privacy_policy_url;
my $account_type;
my $unsub_option;
my $flatfile_flag;
my $hitpath_id;
my $cbs_hitpath_id;
my $dosmonos2_hitpath_id;
my $slks_hitpath_id;
my $overall_db;
my $newest_db;	
my $double_optin;
my $disable_triggers;
my $product_client;
my $clientTypeId;
my $clientTypeName;
my $clientData;
my $minAcceptableRecordDate;
my $clientRecordSourceURL;
my $clientRecordIP;
my $clientRecordCaptureDate;
my $cakeSubaffiliateID;
my $uniqueProfileID;
my $hasClientGroupRestriction;


#------  connect to the util database -----------
###$util->db_connect();

my ($dbhq,$dbhu)=$util->get_dbh();
###$dbh = $util->get_dbh;

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0) 
{
    print "Location: ../notloggedin.cgi\n\n";
	$util->clean_up();
    exit(0);
}

$sql = "select user_type from UserAccounts where user_id = $user_id";
$sth = $dbhq->prepare($sql);
$sth->execute();
($this_user_type) = $sth->fetchrow_array() ;
$sth->finish();

#--------------------------------
# get CGI Form fields
#--------------------------------
$puserid = $query->param('puserid');
my $pmode   = $query->param('pmode');
$pmesg   = $query->param('pmesg');
$pmode = uc($pmode);
if ( $pmode ne "A"  and  $pmode ne "U" and $pmode ne "C") 
{	#---- Invalid MODE - Mode MUST = 'A' (add)  or  'U' (update)  ---------
	util::logerror("<br><br><b>Invalid</b> Mode: <b>$pmode</b> - The Mode MUST equal 'A' or 'U' or 'C'.") ;
	exit(99) ;
}

if ( $pmode eq "A"  or  $puserid eq "" ) 
{
	# defaults for new user
	$fname       = "" ;
	$lname       = "" ;
	$address     = "" ;
	$address2    = "" ;
	$city        = "" ;
	$state       = "" ;
	$zip         = "" ;
	$phone       = "" ;
	$email_addr  = "" ;
	$status      = "A" ;
	$user_type   = "N" ;
	$max_names   = 5000 ;
	$max_mailings = 5000 ;
	$account_type = "BRONZE";
	$website_url = "http://";
	$unsub_option = "ONE LIST";
	$mediactivate_id = "";
	$mediactivate_pw = "";
	$hitpath_id="";
	$mailing_cpm = '';
	$broker_fee = '';
	$rev_share = '';
	$flatfile_flag='Y';
	$overall_db=0;
	$newest_db=0;
	$clientTypeId='';
}
elsif ($pmode eq "C")
{
	# defaults for new user
	$fname       = "" ;
	$lname       = "" ;
	$status      = "A" ;
	$user_type   = "N" ;
	$max_names   = 5000 ;
	$max_mailings = 5000 ;
	$account_type = "BRONZE";
	$website_url = "http://";
	$unsub_option = "ONE LIST";
	$mediactivate_id = "";
	$mediactivate_pw = "";
	$hitpath_id="";
	$mailing_cpm = '';
	$broker_fee = '';
	$rev_share = '';
	$flatfile_flag='Y';
	$overall_db=0;
	$newest_db=0;
	$sql = "select address,address2,city,state,zip,phone,email_addr, client_type, client_main_name, client_main_email, clientStatsGroupingID,clientTypeID, countryID,CheckGlobalSuppression,OrangeClient
	from 
		user u
	where u.user_id = $puserid";
	$sth = $dbhq->prepare($sql);
	$sth->execute();
	$clientData = $sth->fetchrow_hashref();

	## map existing variables to hash ref
	$address = $clientData->{'address'};
	$address2 = $clientData->{'address2'};
	$city = $clientData->{'city'};
	$state = $clientData->{'state'};
	$zip = $clientData->{'zip'};
	$phone = $clientData->{'phone'};
	$email_addr = $clientData->{'email_addr'};
	$cl_type  = $clientData->{'client_type'};
	$cl_company  = $clientData->{'client_company'};
	$cl_main_name  = $clientData->{'client_main_name'};
	$cl_main_email  = $clientData->{'client_main_email'};
	$clientTypeId = $clientData->{'clientTypeId'};
	$sth->finish();
	$puserid=0;
	$pmode="A";
}
else
{
	#------  Get the information about the user for display  --------
	$sql = "select 
	first_name,last_name,address,address2,city,state,zip,phone,email_addr, user_type, 
	status, max_names, max_mailings, username, password,company, website_url, company_phone, account_type,
	privacy_policy_url, unsub_option,mediactivate_id, mediactivate_pw, rev_share, mailing_cpm, broker_fee, 
	rev_threshold, adjustment, client_type, client_company, client_main_name, client_main_email, client_tech_name, 
	client_tech_email, upl_freq, ftp_url, ftp_user, ftp_pw, rt_pw,flatfile_flag,hitpath_id,
	overall_record_db,newest_record_db, double_optin, disable_triggers, product_client, clientTypeId,realtime_flatfile, TempSource, 
	u.clientStatsGroupingID,
	css.revenueDisplayTypeID,
	css.revenueDisplayTypeID,
	css.showRecordProcessing,
	css.showUniqueCounts,
	rdt.revenueDisplayTypeName,
	rdt.revenueDisplayTypeLabel,
	u.checkPreviousOC as checkOCDataTest,
	-- ufl.checkOCDataTest,
	u.minimumAcceptableRecordDate,
	u.clientRecordSourceURL,
	u.clientRecordIP,
	u.clientRecordCaptureDate,
	u.cakeSubaffiliateID,
	u.uniqueProfileID,
	u.hasClientGroupRestriction,
	u.countryID,
	u.CheckGlobalSuppression,
	u.OrangeClient
	from 
		user u
		LEFT OUTER JOIN ClientStatsGrouping csg ON u.clientStatsGroupingID = csg.clientStatsGroupingID
		LEFT OUTER JOIN ClientStatsSettings css ON u.user_id = css.clientID
		LEFT OUTER JOIN ClientStatsRevenueDisplayType rdt ON css.revenueDisplayTypeID = rdt.revenueDisplayTypeID
		LEFT OUTER JOIN user_file_layout ufl on u.user_id = ufl.user_id

	where u.user_id = $puserid";
	$sth = $dbhq->prepare($sql);
	$sth->execute();
	
#	($fname,$lname,$address,$address2,$city,$state,$zip,$phone,$email_addr,
#	$user_type, $status,$max_names,$max_mailings,$username,$password, $company, 
#	$website_url, $company_phone, $account_type,$privacy_policy_url, $unsub_option,
#	$mediactivate_id, $mediactivate_pw, $rev_share, $mailing_cpm, $broker_fee, $rev_threshold, 
#	$adj, $cl_type, $cl_company, $cl_main_name, $cl_main_email, $cl_tech_name, $cl_tech_email, 
#	$upl_freq, $ftp_url, $ftp_user, $ftp_pw, $rt_pw,$flatfile_flag,$hitpath_id,$overall_db,
#	$newest_db, $double_optin,$disable_triggers, $product_client, $clientTypeId,
#	$realtime_flatfile,$tempsource) = $sth->fetchrow_array();
	
	$clientData = $sth->fetchrow_hashref();
	
	## map existing variables to hash ref
	$fname = $clientData->{'first_name'};
	$lname = $clientData->{'last_name'};
	$address = $clientData->{'address'};
	$address2 = $clientData->{'address2'};
	$city = $clientData->{'city'};
	$state = $clientData->{'state'};
	$zip = $clientData->{'zip'};
	$phone = $clientData->{'phone'};
	$email_addr = $clientData->{'email_addr'};
	$user_type  = $clientData->{'user_type'};
	$status = $clientData->{'status'};
	$max_names = $clientData->{'max_names'};
	$max_mailings = $clientData->{'max_mailings'};
	$username = $clientData->{'username'};
	$password  = $clientData->{'password'};
	$company  = $clientData->{'company'};
	$website_url  = $clientData->{'website_url'};
	$company_phone  = $clientData->{'company_phone'};
	$account_type = $clientData->{'account_type'};
	$privacy_policy_url  = $clientData->{'privacy_policy_url'};
	$unsub_option = $clientData->{'unsub_option'};
	$mediactivate_id  = $clientData->{'mediactivate_id'};
	$mediactivate_pw  = $clientData->{'mediactivate_pw'};
	$rev_share  = $clientData->{'rev_share'};
	$mailing_cpm  = $clientData->{'mailing_cpm'};
	$broker_fee = $clientData->{'broker_fee'};
	$rev_threshold  = $clientData->{'rev_threshold'};
	$adj = $clientData->{'adjustment'};
	$cl_type  = $clientData->{'client_type'};
	$cl_company  = $clientData->{'client_company'};
	$cl_main_name  = $clientData->{'client_main_name'};
	$cl_main_email  = $clientData->{'client_main_email'};
	$cl_tech_name  = $clientData->{'client_tech_name'};
	$cl_tech_email  = $clientData->{'client_tech_email'};
	$upl_freq  = $clientData->{'upl_freq'};
	$ftp_url  = $clientData->{'ftp_url'};
	$ftp_user = $clientData->{'ftp_user'};
	$ftp_pw = $clientData->{'ftp_pw'};
	$rt_pw = $clientData->{'rt_pw'};
	$flatfile_flag = $clientData->{'flatfile_flag'};
	$hitpath_id = $clientData->{'hitpath_id'};
	$overall_db = $clientData->{'overall_record_db'};
	$newest_db  = $clientData->{'newest_record_db'};
	$double_optin = $clientData->{'double_optin'};
	$disable_triggers  = $clientData->{'disable_triggers'};
	$product_client = $clientData->{'product_client'};
	$clientTypeId = $clientData->{'clientTypeId'};
	$realtime_flatfile = $clientData->{'realtime_flatfile'};
	$tempsource = $clientData->{'TempSource'};
	$minAcceptableRecordDate = $clientData->{'minimumAcceptableRecordDate'};
	$clientRecordSourceURL = $clientData->{'clientRecordSourceURL'};
	$clientRecordIP = $clientData->{'clientRecordIP'};
	$clientRecordCaptureDate = $clientData->{'clientRecordCaptureDate'};
	$cakeSubaffiliateID = $clientData->{'cakeSubaffiliateID'};
	$uniqueProfileID = $clientData->{'uniqueProfileID'};
	$hasClientGroupRestriction = $clientData->{'hasClientGroupRestriction'};
		
	$sth->finish();
	
#	if ( $email_addr eq "" ) 
#	{
#		$errmsg = $dbhu->errstr();
#	    util::logerror("<br><br>Error Getting user information for UserID: $puserid &nbsp;&nbsp;$errmsg");
#		exit(99) ;
#	}
}

## set client types

my $ctQuery = qq|
SELECT 
	COLUMN_TYPE
FROM 
	INFORMATION_SCHEMA.COLUMNS
WHERE 
	TABLE_SCHEMA = 'new_mail'
	AND TABLE_NAME = 'user'
	AND COLUMN_NAME = 'client_type'
|;

$sth = $dbhq->prepare($ctQuery);
$sth->execute();
	
my $data = $sth->fetchrow_hashref();

$data->{'COLUMN_TYPE'} =~ s/^enum//g;
$data->{'COLUMN_TYPE'} =~ s/[\(\)\']//g;

my @clientTypes = split(/\,/, $data->{'COLUMN_TYPE'});

#set double optin radio defaults
my $doubleopt_checked_yes = '';
my $doubleopt_checked_no  = '';

if($double_optin eq 'Y') {
	$doubleopt_checked_yes = 'CHECKED';
}

else {
	$doubleopt_checked_no = 'CHECKED';
}

my $disable_triggers_checked_yes = '';
my $disable_triggers_checked_no  = '';

if($disable_triggers eq 'Y') {
	$disable_triggers_checked_yes = 'CHECKED';
}

else {
	$disable_triggers_checked_no = 'CHECKED';
}

my $product_client_checked_yes = '';
my $product_client_checked_no  = '';

if($product_client eq 'Y') {
	$product_client_checked_yes = 'CHECKED';
}

else {
	$product_client_checked_no = 'CHECKED';
}
my $realtime_flatfile_checked_yes = '';
my $realtime_flatfile_checked_no  = '';

if($realtime_flatfile eq 'Y') {
	$realtime_flatfile_checked_yes = 'CHECKED';
}

else {
	$realtime_flatfile_checked_no = 'CHECKED';
}

# setup for selecting proper account type

my $account_type_b = "";
my $account_type_s = "";
my $account_type_g = "";
my $account_type_p = "";
if ($account_type eq "BRONZE")
{
	$account_type_b = "selected";
}
elsif ($account_type eq "SILVER")
{
	$account_type_s = "selected";
}
elsif ($account_type eq "GOLD")
{
	$account_type_g = "selected";
}
elsif ($account_type eq "PLATINUM")
{
	$account_type_p = "selected";
}

# setup for selecting proper unsubscribe option

my $unsub_one = "";
my $unsub_all = "";
if ($unsub_option eq "ONE LIST")
{
	$unsub_one = "selected";
}
else
{
	$unsub_all = "selected";
}

# print out html page

util::header("Edit Contact Information");
	
if ( $pmesg ne "" ) 
{
	#---------------------------------------------------------------------------
	# Display mesg (if present) from module that called this module.
	#---------------------------------------------------------------------------
	print qq{ <script language="JavaScript">  \n } ;
	print qq{ 	alert("$pmesg");  \n } ;
	print qq{ </script>  \n } ;
	$query->param(-name=>'pmesg',-value=>"") ;
}

print << "end_of_html";
</TD>
</TR>
<TR>
<TD vAlign=top align=left bgColor=#FFFFFF>

    <TABLE cellSpacing=0 cellPadding=10 bgColor=#FFFFFF border=0 width="100%">
    <TBODY>
    <TR>
    <TD vAlign=top align=left bgColor=#ffffff colSpan=10>

        <TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
        <TBODY>
        <TR>
        <TD vAlign=center align=left><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 
            size=3><B>Client Information</B> </FONT></TD>
		</TR>
        <TR>
        <TD><IMG height=3 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>

        <TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
        <TBODY>
        <TR>
        <TD colSpan=10><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
end_of_html

if ( $pmode eq "A" )
{
	print qq{ To ADD client information please enter the appropriate fields \n } ;
	print qq{ and select <B>Add</B>. As a  \n } ;
	print qq{ reminder, we closely protect and safeguard your personal  \n } ;
	print qq{ information. See our website's  \n } ;
}
else
{
	print qq{ To UPDATE the client information please make  \n } ;
	print qq{ the appropriate changes and select <B>Save</B>. As a  \n } ;
	print qq{ reminder, we closely protect and safeguard your personal  \n } ;
	print qq{ information. See our website's  \n } ;
}

print << "end_of_html" ;
			<a href="../privacy_policy.cgi">Privacy Policy</a> for details.<BR></FONT></TD>
		</TR>
		</TBODY>
		</TABLE>
   	<script language="JavaScript">
   	function ProcessForm(Mode)
   	{
        var iopt;
        // validate your data first
        iopt = check_mandatory_fields();
        if (iopt == 0)
        {
            return false;
        }

        // if ok, go on to save
		document.edit_client.pmode.value = Mode ;
        document.edit_client.submit();
        return true;
    }

    function check_mandatory_fields()
    {
        if (document.edit_client.address.value == "")
        {
            alert("You MUST enter a value for the Customer Address field."); 
			document.edit_client.address.focus();
            return false;
        }
        if (document.edit_client.city.value == "")
        {
            alert("You MUST enter a value for the City field."); 
			document.edit_client.city.focus();
            return false;
        }
        if (document.edit_client.state.value == "")
        {
            alert("You MUST enter a value for the State field."); 
			document.edit_client.state.focus();
            return false;
        }
        if (document.edit_client.zip.value == "")
        {
            alert("You MUST enter a value for the Zip field."); 
			document.edit_client.zip.focus();
            return false;
        }
        if (document.edit_client.email_addr.value == "")
        {
            alert("You MUST enter a value for the Contact Email field."); 
			document.edit_client.email_addr.focus();
            return false;
        }
        if (document.edit_client.phone.value == "")
        {
            alert("You MUST enter a value for the Contact Phone field."); 
			document.edit_client.phone.focus();
            return false;
        }
end_of_html

if ($this_user_type eq "A") 
{
print << "end_of_html";
		// max_names MUST be Numeric Check..............
		if ( document.edit_client.max_names != "") 
		{
			if ( isNaN(document.edit_client.max_names.value)) 
			{   
				alert("The MAX NAMES field MUST be Numeric!!!");
				document.edit_client.max_names.focus();
				return false;
			}
		}

		// max_mailings MUST be Numeric Check..............
		if ( document.edit_client.max_mailings != "") 
		{
			if ( isNaN(document.edit_client.max_mailings.value)) 
			{   
				alert("The MAX MAILINGS field MUST be Numeric!!!");
				document.edit_client.max_mailings.focus();
				return false;
			}
		}

		// Demo Users MUST have MAX NAMES set to 5
		if ( document.edit_client.user_type[2].checked == true  &&  
		( document.edit_client.max_names.value > 5  || document.edit_client.max_names.value < 1  ))
		{
			// alert("If the User is a DEMO User then the MAX NAMES field MUST be set from 1 thru 5.");
			// document.edit_client.max_names.focus();
			// return false;
			document.edit_client.max_names.value = 5 ;
		}
end_of_html
	} # end-if checking to print admin_user Java code

	print << "end_of_html" ;
		// Validate Password 
		if (document.edit_client.password.value == "")
		{
			alert("Invalid Password.  You MUST enter a Password");
			document.edit_client.password.focus();
			return false;
		}
        return true;
    }
    </script>

        <FORM name=edit_client action="client_upd.cgi" method=post>

        <TABLE cellSpacing=0 cellPadding=0 width="100%" bgColor=#ffffff border=0>
        <TBODY>
        <TR>
        <TD>
            <TABLE cellSpacing=0 cellPadding=5 width="100%" border=0>
            <TBODY>
            <TR>
            <TD align=middle>

                <TABLE cellSpacing=0 cellPadding=0 width="100%" bgColor=#E3FAD1 border=0>
                <TBODY>
                <TR align=top bgColor=#509C10 height=18>
                <TD vAlign=top align=left height=15><IMG src="$images/blue_tl.gif" 
					border=0 width="7" height="7"></TD>
                <TD height=15><IMG height=1 src="$images/spacer.gif" width=3 border=0></TD>
                <TD align=middle height=15>

                    <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
                    <TBODY>
                    <TR bgColor=#509C10 height=15>
                    <TD align=middle width="100%" height=15><FONT 
						face=Verdana,Arial,Helvetica,sans-serif color=white size=2>
						<B>Contact Information</B></FONT></TD>
					</TR>
					</TBODY>
					</TABLE>
				</TD>
                <TD height=15><IMG height=1 src="$images/spacer.gif" width=3 border=0></TD>
                <TD vAlign=top align=right bgColor=#509C10 height=15><IMG 
                    src="$images/blue_tr.gif" border=0 width="7" height="7"></TD>
				</TR>
                <TR bgColor=#E3FAD1>
                <TD colSpan=5><IMG height=3 src="$images/spacer.gif" width=1 border=0></TD>
				</TR>
                <TR bgColor=#E3FAD1>
                <TD><IMG height=3 src="$images/spacer.gif" width=3></TD>
                <TD align=middle><IMG height=3 src="$images/spacer.gif" width=3></TD>
                <TD align=middle>
                    <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
                    <TBODY>
                    <TR>
                    <TD align=middle><IMG height=3 src="$images/spacer.gif" width=3></TD>
					</TR>
                    <TR> <!-- -------- Contact First Name -------------- -->
                    <TD vAlign=center noWrap align=right width="20%"></TD>
                    <TD vAlign=center align=left>

end_of_html
	
	#-----------------------------------------------------
	# Only Admin users (eg user_type = 'A') view Status
	#-----------------------------------------------------
	if ( $this_user_type eq "A" ) 
	{
		my ($activeChecked, $inactiveChecked, $deletedChecked);
		
		print qq{ &nbsp;&nbsp;&nbsp;Status: };
		#------ Toggle CHECKED based on Status value --------------
		if ( $status eq "A" )
		{
			$activeChecked = 'CHECKED';
		}
		elsif ( $status eq "P" )
		{
			$inactiveChecked = 'CHECKED';
		}
		else
		{
	    	$deletedChecked = 'CHECKED';
		}
		
		print qq{ <input type="radio" name="status" value="A" $activeChecked>Active } ;
		print qq{ &nbsp;<input type="radio" name="status" value="P" $inactiveChecked>Paused };
		print qq{ &nbsp;<input type="radio" name="status" value="D" $deletedChecked>Inactive };
		
	}
	else
	{
		print qq{<input type="hidden" value="$status" name="status"> } ;
	}
		
	my $pass_field_type=$username eq 'admin' ? "password" : "text";
	print << "end_of_html" ;
&nbsp;&nbsp;<a href="/newcgi-bin/gen.cgi?cid=$puserid">Gen URLs</a>
						</FONT></TD>
					</TR>
    
                    <TR> <!-- -------- Contact User Name -------------- -->
                    <TD vAlign=center noWrap align=right width="20%">
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
						User Name: </FONT></TD>
                    <TD vAlign=center align=left>
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
						<INPUT size=80 maxlength=100 value="$username" name=username></FONT></TD>
						<INPUT type="hidden" name="old_username" value="$username">
					</TR>

                    <TR> <!-- -------- Contact Password Name -------------- -->
                    <TD vAlign=center noWrap align=right width="20%">
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
						Password: </FONT></TD>
                    <TD vAlign=center align=left>
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
						<INPUT type="$pass_field_type" size=20 maxlength=15 value="$password" name=password>
                   	</TR> 
end_of_html

## only show Cake Subaffiliate ID when updating a client
## because its generated now
if($pmode eq 'U')
{
	print qq|
	<TR> 
		<TD vAlign=center noWrap align=right width="20%">
			<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>HitPath Id: </FONT>
		</TD>
		<TD vAlign=center align=left>
			<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2><INPUT type=text size=20 maxlength=15 value="$hitpath_id" name=hitpath_id></FONT>
		</TD>
	</TR>
	<TR> 
		<TD vAlign=center noWrap align=right width="20%">
			<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>Cake Subaffiliate ID: </FONT>
		</TD>
		<TD vAlign=center align=left>
			<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2><INPUT type=text size=20 maxlength=15 value="$cakeSubaffiliateID" name=cakeSubaffiliateID></FONT>
		</TD>
	</TR>
	|;	
}

$sql="select third_party_id,mailer_name from third_party_defaults where status='A' and third_party_id != 10 order by mailer_name";
my $sth1=$dbhu->prepare($sql);
$sth1->execute();
my $third_party_id;
my $mailer_name;
my $med_id;
my $med_pw;
my $hitpath_id;
while (($third_party_id,$mailer_name)=$sth1->fetchrow_array())
{
	$sql="select mediactivate_id,mediactivate_pw,hitpath_id from client_thirdparty where user_id=? and third_party_id=?";
	my $sth2=$dbhu->prepare($sql);
	$sth2->execute($puserid,$third_party_id);
	if (($med_id,$med_pw,$hitpath_id)=$sth2->fetchrow_array())
	{
	}
	else
	{
		$med_id="";
		$med_pw="";
		$hitpath_id="";
	}
	$sth2->finish();
#	print "<TR><TD vAlign=center noWrap align=right width=\"20%\"><FONT face=\"verdana,arial,helvetica,sans serif\" color=#509C10 size=2>$mailer_name Mediactive Id: </FONT></TD><TD vAlign=center align=left><FONT face=\"verdana,arial,helvetica,sans serif\" color=#509C10 size=2><INPUT type=text size=20 maxlength=15 value=\"$med_id\" name=med_${third_party_id}></FONT></TD></TR>\n";
	#print "<TR><TD vAlign=center noWrap align=right width=\"20%\"><FONT face=\"verdana,arial,helvetica,sans serif\" color=#509C10 size=2>$mailer_name Mediactive Pw: </FONT></TD><TD vAlign=center align=left><FONT face=\"verdana,arial,helvetica,sans serif\" color=#509C10 size=2><INPUT type=text size=45 maxlength=45 value=\"$med_pw\" name=medpw_${third_party_id}></FONT></TD></TR>\n";
#	print "<TR><TD vAlign=center noWrap align=right width=\"20%\"><FONT face=\"verdana,arial,helvetica,sans serif\" color=#509C10 size=2>$mailer_name HitPath Id: </FONT></TD><TD vAlign=center align=left><FONT face=\"verdana,arial,helvetica,sans serif\" color=#509C10 size=2><INPUT type=text size=20 maxlength=15 value=\"$hitpath_id\" name=hitpath_${third_party_id}></FONT></TD></TR>\n";
}
$sth1->finish();
print<<"end_of_html";
<!--                    
					<TR> 
                    <TD vAlign=center noWrap align=right width="20%">
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>Rev Share (decimal): </FONT></TD>
                    <TD vAlign=center align=left>
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2><INPUT type=text size=10 maxlength=15 value="$rev_share" name=rev_share>
						</FONT></TD>
					</TR>
                    <TR> 
                    <TD vAlign=center noWrap align=right width="20%">
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>Mailing CPM (decimal): </FONT></TD>
                    <TD vAlign=center align=left>
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2><INPUT type=text size=10 maxlength=15 value="$mailing_cpm" name=mailing_cpm>
						</FONT></TD>
					</TR>
                    <TR> 
                    <TD vAlign=center noWrap align=right width="20%">
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>Broker Fee (decimal): </FONT></TD>
                    <TD vAlign=center align=left>
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2><INPUT type=text size=10 maxlength=15 value="$broker_fee" name=broker_fee>
						</FONT></TD>
					</TR>
                    <TR> 
                    <TD vAlign=center noWrap align=right width="20%">
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>Rev Threshold(decimal): </FONT></TD>
                    <TD vAlign=center align=left>
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2><INPUT type=text size=10 maxlength=15 value="$rev_threshold" name=rev_threshold>
						</FONT></TD>
					</TR>
                    <TR> 
                    <TD vAlign=center noWrap align=right width="20%">
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>Adjustment (decimal): </FONT></TD>
                    <TD vAlign=center align=left>
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2><INPUT type=text size=10 maxlength=15 value="$adj" name=adjustment>
						</FONT></TD>
					</TR> 
 -->

					<TR> <!-- -------- Contact Address 1 -------------- -->
                    <TD vAlign=center noWrap align=right width="20%">
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
						Address: </FONT></TD>
                    <TD vAlign=center align=left>
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
						<INPUT size=50 maxlength=50 value="$address" name=address></FONT></TD>
					</TR>

					<TR> <!-- -------- Contact Address 2 -------------- --> 
                    <TD vAlign=center noWrap align=right width="20%">
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2> 
                        &nbsp;&nbsp;&nbsp; </FONT></TD>
                    <TD vAlign=center align=left>
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
						<INPUT size=50 maxlength=50 value="$address2" name=address2>
                        </FONT></TD>
					</TR>

					<TR> <!-- -------- Contact City -------------- --> 
                    <TD vAlign=center noWrap align=right width="20%">
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
						City: </FONT></TD>
                    <TD vAlign=center align=left>
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
						<INPUT size=50 maxlength=50 value="$city" name=city>
                        </FONT></TD>
					</TR>

					<TR> <!-- -------- Contact State / Zip  -------------- --> 
                    <TD vAlign=center noWrap align=right width="20%">
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
						State/Zip: </FONT></TD>
                    <TD vAlign=center align=left>
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
						<INPUT size=2 maxlength=2 value="$state" name=state>&nbsp;/&nbsp;
						<input size=10 value="$zip" maxlength=10 name=zip></FONT></TD>
					</TR>

                    <TR> <!-- -------- Contact Email -------------- --> 
                    <TD vAlign=center noWrap align=right width="20%">
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
						Email: </FONT></TD>
                    <TD vAlign=center align=left>
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
						<INPUT size=50 maxlength=80 value="$email_addr" name=email_addr> 
						</FONT></TD>
						<INPUT type="hidden" name="old_email_addr" value="$email_addr">
						<INPUT type="hidden" name="pmode" value="$pmode">
						<INPUT type="hidden" name="puserid" value="$puserid">
					</TR>

                    <TR>  <!-- --------- Contact Phone  ----------------- -->
                    <TD vAlign=center noWrap align=right width="20%">
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
						Phone: </FONT></TD>
                    <TD vAlign=center align=left>
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
						<INPUT size=30 value="$phone" MAXLENGTH=35 name=phone> 
                        </FONT></TD>
					</TR>

                    <TR>  <!-- --------- Company ----------------- -->
                    <TD vAlign=center noWrap align=right width="20%">
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
						Client Network: </FONT></TD>
                    <TD vAlign=center align=left>
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
						<INPUT size=30 value="$company" MAXLENGTH=80 name=company> 
                        </FONT></TD>
					</TR>

                    <TR>  <!-- --------- Website URL ----------------- -->
                    <TD vAlign=center noWrap align=right width="20%">
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
						Website URL: </FONT></TD>
                    <TD vAlign=center align=left>
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
						<INPUT size=40 value="$website_url" MAXLENGTH=255 name=website_url> 
                        </FONT></TD>
					</TR>

end_of_html
	

		print qq{<INPUT type="hidden" value="$max_names" name=max_names>  } ;
		print qq{<INPUT type="hidden" value="$max_mailings" name=max_mailings>  } ;
		print qq{<input type="hidden" value="$user_type" name="user_type"> } ;

	print << "end_of_html" ;
                   		</FONT></TD>
					</TR>
end_of_html

		print qq { <input type="hidden" name="account_type" value="$account_type"> \n };

##  new client section 
	my $lrList=[{	name=>'cl_type', title=>'Client Type', val=>$cl_type},
				{	name=>'cl_main_name', title=>'Client Main Name', val=>$cl_main_name},
				{	name=>'upl_freq', title=>'Upload Freq', val=>$upl_freq},
				{	name=>'ftp_url', title=>'FTP Url', val=>$ftp_url},
				{	name=>'ftp_user', title=>'FTP User', val=>$ftp_user},
				{	name=>'ftp_pw', title=>'FTP PW', val=>$ftp_pw},
				{	name=>'rt_pw', title=>'Real-Time PW', val=>$rt_pw},
				];
	foreach (@$lrList) {
		print qq^ <TR>  
                    <TD vAlign=top noWrap align=right width="20%">
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
						$_->{title}:</FONT></TD>
                    <TD vAlign=center align=left>
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>\n^;
		if ($_->{name} =~ m/upl_freq/) {
			print qq^		<select name='$_->{name}'>\n^;
			foreach ('TBD', 'RT', 'Daily', 'Weekly', 'Monthly') {
				my $selected=($upl_freq && $upl_freq eq $_) ? 'SELECTED' : '';
				print qq^		<option value='$_' $selected>$_</option>\n^;
			}
			print qq^		</select>\n^;
		}
		elsif ($_->{name} =~ m/cl_type/) {
			print qq^		<select name='$_->{name}'>\n^;
			foreach (sort @clientTypes) 
			{
				my $selected=($cl_type && $cl_type eq $_) ? 'SELECTED' : '';
				print qq^		<option value='$_' $selected>$_</option>\n^;
			}
			print qq^		</select>\n^;
		}
		elsif ($_->{name} =~ m/cl_main_email/) {
			print qq^		<input type='text' name='$_->{name}' value='$_->{val}' size='80' maxlength=400>\n^;
		}
		else {
			print qq^		<input type='text' name='$_->{name}' value='$_->{val}' size='35'>\n^;
		}
		print qq^</FONT></TD>
					</TR>\n^;
	}

clientGroupingSettings($clientData);
clientStatsSettings($clientData);

print << "end_of_html" ;
                   		</FONT></TD>
					</TR>
                    <TR>
                    <TD align=middle><IMG height=3 src="$images/spacer.gif" width=3></TD>
					</TR>
					</TBODY>
					</TABLE>
				</TD>
                <TD align=middle><IMG height=3 src="$images/spacer.gif" width=3></TD>
                <TD><IMG height=3 src="$images/spacer.gif" width=3></TD>
				</TR>
                <TR bgColor=#E3FAD1>
                <TD colSpan=5><IMG height=3 src="$images/spacer.gif" width=1 border=0></TD>
				</TR>
                <TR bgColor=#E3FAD1 height=10>
                <TD vAlign=bottom align=left><IMG height=7 src="$images/lt_purp_bl.gif" 
					width=7 border=0></TD>
                <TD><IMG height=3 src="$images/spacer.gif" width=1 border=0></TD>
                <TD align=middle bgColor=#E3FAD1><IMG height=3 src="$images/spacer.gif" 
					width=1 border=0>
					<IMG height=3 src="$images/spacer.gif" width=1 border=0></TD>
                <TD><IMG height=3 src="$images/spacer.gif" width=1 border=0></TD>
                <TD vAlign=bottom align=right><IMG height=7 src="$images/lt_purp_br.gif" 
					width=7 border=0></TD>
				</TR>
				</TBODY>
				</TABLE>
			</TD>
			</TR>
			</TBODY>
			</TABLE>
		</TD>
		</TR>
        <TR>
        <TD>

            <TABLE cellSpacing=0 cellPadding=7 width="100%" border=0>
            <TBODY>
            <TR>
			<td align="center" width="50%">
				<A HREF="../mainmenu.cgi">
				<IMG src="$images/home_blkline.gif" border=0></A></TD>	
			<td align="center" width="50%">
end_of_html

	if ( $pmode eq "A" )
	{
		print qq { 	<input type="image" name="BtnAdd" src="$images/add.gif" border=0 
						onClick="return ProcessForm('A');" > };
	}
	else
	{
		print qq { <input type="image" name="BtnAdd" src="$images/save.gif" border=0 
						onClick="return ProcessForm('U');" > }; 
	}

print << "end_of_html";
			</td>
			</tr>
			</table>

		</TD>
		</TR>
		</TBODY>
		</TABLE>

		</FORM>

	</TD>
	</TR>
	</TBODY>
	</TABLE>

</TD>
</TR>
<TR>
<TD noWrap align=left height=17>
end_of_html

$util->footer();
$util->clean_up();
exit(0);

sub getCountryDisplayList
{
	my ($clientData) = @_;

	my $output = qq|
	
	<select name='countryID'>
		<option value=''>Choose a country</option>
	|;
			
	my $sql = "select countryID, countryName from Country";
	my $sth = $dbhu->prepare($sql);
	$sth->execute();

	while (my $data = $sth->fetchrow_hashref()){
		
		my $selected = '';
		
		if($data->{'countryID'} eq $clientData->{'countryID'})
		{
			$selected = 'SELECTED';
		} 
		
		$output .= qq|<option value="$data->{'countryID'}" $selected>$data->{'countryName'}</option>|;
			
	}	
	
	$output .= qq|</select>|;
	
	return($output);
}

sub getOwnerTypeDisplayList
{
	my ($clientData) = @_;

	my $output = qq|
	
	<select name='clientTypeId'>
		<option value=''>Choose an owner type</option>
	|;
			
	my $sql = "select clientTypeID, clientTypeName from ClientType";
	my $sth = $dbhu->prepare($sql);
	$sth->execute();

	while (my $data = $sth->fetchrow_hashref()){
		
		my $selected = '';
		
		if($data->{'clientTypeID'} eq $clientData->{'clientTypeId'})
		{
			$selected = 'SELECTED';
		} 
		
		$output .= qq|<option value="$data->{'clientTypeID'}" $selected>$data->{'clientTypeName'}</option>|;
			
	}	
	
	$output .= qq|</select>|;
	
	return($output);
}

sub getRevenueTypeDisplayList {
		
	my ($clientData) = @_;

	#<input type='hidden' name='revenueDisplayTypeLabel' value = $clientData->{'revenueDisplayTypeLabel'}/>
	#<input type='hidden' name='revenueDisplayTypeName' value = $clientData->{'revenueDisplayTypeName'}/>
	
	my $output = qq|
	
	<select name='revenueDisplayTypeID'>
		<option value=''>Choose a revenue display type</option>
	|;
			
	my $sql = "select revenueDisplayTypeID, revenueDisplayTypeName from ClientStatsRevenueDisplayType";
	my $sth = $dbhu->prepare($sql);
	$sth->execute();

	while (my $data = $sth->fetchrow_hashref()){
		
		my $selected = '';
		
		if($data->{'revenueDisplayTypeID'} eq $clientData->{'revenueDisplayTypeID'}){
			$selected = 'SELECTED';
		} 
		
		$output .= qq|<option value="$data->{'revenueDisplayTypeID'}" $selected>$data->{'revenueDisplayTypeName'}</option>|;
			
	}	
	
	$output .= qq|</select>|;
	
	return($output);
	
}



sub getUniqueCountRadioButtons {
	
	my ($clientData) = @_;
	
	my $radioCheckedYes = '';
	my $radioCheckedNo  = 'checked';
	
	if($clientData->{'showUniqueCounts'}){
		$radioCheckedYes = 'checked';
		$radioCheckedNo  = '';
	}
		
	my $output  = qq|<input type="radio" name="showUniqueCounts" value="0" $radioCheckedNo />No  |;
	$output .= qq|&nbsp;&nbsp;<input type="radio" name="showUniqueCounts" value="1" $radioCheckedYes />Yes |;
	
	return($output);
	
}
sub getCheckGlobalSuppressionButtons {
	
	my ($clientData) = @_;
	
	my $radioCheckedYes = '';
	my $radioCheckedNo  = 'checked';
	
	if($clientData->{'CheckGlobalSuppression'} eq 'Y')
	{
		$radioCheckedYes = 'checked';
		$radioCheckedNo  = '';
	}
		
	my $output  = qq|<input type="radio" name="CheckGlobalSuppression" value="N" $radioCheckedNo />No  |;
	$output .= qq|&nbsp;&nbsp;<input type="radio" name="CheckGlobalSuppression" value="Y" $radioCheckedYes />Yes |;
	
	return($output);
	
}
sub getOrangeClientButtons {
	
	my ($clientData) = @_;
	
	my $radioCheckedYes = '';
	my $radioCheckedNo  = 'checked';
	
	if($clientData->{'OrangeClient'} eq 'Y')
	{
		$radioCheckedYes = 'checked';
		$radioCheckedNo  = '';
	}
		
	my $output  = qq|<input type="radio" name="OrangeClient" value="N" $radioCheckedNo />No  |;
	$output .= qq|&nbsp;&nbsp;<input type="radio" name="OrangeClient" value="Y" $radioCheckedYes />Yes |;
	
	return($output);
	
}

sub getRecordProcessingRadioButtons {
	
	my ($clientData) = @_;

	my $radioCheckedYes = '';
	my $radioCheckedNo  = 'checked';
	
	if($clientData->{'showRecordProcessing'}){
		$radioCheckedYes = 'checked';
		$radioCheckedNo  = '';
	}
		
	my $output  = qq|<input type="radio" name="showRecordProcessing" value="0" $radioCheckedNo />No  |;
	$output .= qq|&nbsp;&nbsp;<input type="radio" name="showRecordProcessing" value="1" $radioCheckedYes />Yes |;
	
	return($output);
	
}

sub getHasClientGroupRestrictionCheckBox
{
	my ($clientData) = @_;

	my $checked = '';
	
	if($clientData->{'hasClientGroupRestriction'})
	{
		$checked = 'checked';
	}

	return($checked);	
}

sub getOCDataTestCheckBox
{
	
	my ($clientData) = @_;

	my $checked = '';
	
	if($clientData->{'checkOCDataTest'})
	{
		$checked = 'checked';
	}

	return($checked);	
}

sub clientStatsSettings 
{
	
	my ($clientData) = @_;
	
	my $revenueDisplayList 			 = getRevenueTypeDisplayList($clientData);	
	my $uniqueCountRadioButtons 	 = getUniqueCountRadioButtons($clientData);
	my $globalSuppressionButtons	 = getCheckGlobalSuppressionButtons($clientData);
	my $OrangeClientButtons	 		 = getOrangeClientButtons($clientData);
	my $recordProcessingRadioButtons = getRecordProcessingRadioButtons($clientData);
	my $ocTestChecked   			 = getOCDataTestCheckBox($clientData);
	my $hasRestrictionsChecked 		 = getHasClientGroupRestrictionCheckBox($clientData);
	my $ownerTypeList 				 = getOwnerTypeDisplayList($clientData);	
	my $countryList 				 = getCountryDisplayList($clientData);	
	
	print qq|

<!-- revenue display type -->
<TR>
	<TD vAlign=center noWrap align=right width="20%">
		<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
		Revenue Display Type: </FONT>
	</TD>
	<TD vAlign=center align=left>
		$revenueDisplayList
	</TD>
</TR>

<!-- show record processing -->
<TR>
	<TD vAlign=center noWrap align=right width="20%">
		<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
		Show Record Processing: </FONT>
	</TD>
	<TD vAlign=center align=left>
		$recordProcessingRadioButtons
	</TD>
</TR>

<!-- unique record processing counts -->
<TR>
	<TD vAlign=center noWrap align=right width="20%">
		<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
		Show Unique Percent Stats: </FONT>
	</TD>
	<TD vAlign=center align=left>
		$uniqueCountRadioButtons
	</TD>
</TR>
<!-- check global suppresion-->
<TR>
	<TD vAlign=center noWrap align=right width="20%">
		<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
		Check Global Suppression: </FONT>
	</TD>
	<TD vAlign=center align=left>
		$globalSuppressionButtons 
	</TD>
</TR>

<!-- reset password -->
<TR>
	<TD vAlign=center noWrap align=right width="20%">
		<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
		Reset client password: </FONT>
	</TD>
	<TD vAlign=center align=left>
		<INPUT TYPE='checkbox' name='resetPassword' value='1'>
	</TD>
</TR>

<!-- alert mail ops of special client -->
<TR>
	<TD vAlign=center noWrap align=right width="20%">
		<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
		Client has client group restrictions: </FONT>
	</TD>
	<TD vAlign=center align=left>
		<INPUT TYPE='checkbox' name='hasClientGroupRestriction' value='1' $hasRestrictionsChecked>
	</TD>
</TR>


<!-- check previous OC during processing -->
<TR>
	<TD vAlign=center noWrap align=right width="20%">
		<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
		Check previous OC during processing: </FONT>
	</TD>
	<TD vAlign=center align=left>
		<INPUT TYPE='checkbox' name='checkOCDataTest' value='1' $ocTestChecked>
	</TD>
</TR>

<!-- set minimum acceptable date that we will process a record -->
<TR>
	<TD vAlign=center noWrap align=right width="20%">
		<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
		Minimum acceptable record date (YYYY-MM-DD format): </FONT>
	</TD>
	<TD vAlign=center align=left>
		<INPUT type=text size=20 maxlength=15 value="$minAcceptableRecordDate" name="minAcceptableRecordDate">
	</TD>
</TR>


<!-- begin set default source/ip/date for record processing -->
<TR>
	<TD vAlign=center noWrap align=right width="20%">
		<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
		Client record source URL: </FONT>
	</TD>
	<TD vAlign=center align=left>
		<INPUT type=text size=20 maxlength=60 value="$clientRecordSourceURL" name="clientRecordSourceURL">
	</TD>
</TR>

<TR>
	<TD vAlign=center noWrap align=right width="20%">
		<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
		Client record IP: </FONT>
	</TD>
	<TD vAlign=center align=left>
		<INPUT type=text size=20 maxlength=15 value="$clientRecordIP" name="clientRecordIP">
	</TD>
</TR>

<!-- end set default source/ip/date for record processing -->

<!-- begin set unique profile ID -->
<TR>
	<TD vAlign=center noWrap align=right width="20%">
		<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
		Unique Profile ID for flat files: </FONT>
	</TD>
	<TD vAlign=center align=left>
		<INPUT type=text size=20 maxlength=60 value="$uniqueProfileID" name="uniqueProfileID">
	</TD>
</TR>
<!-- end set unique profile ID -->

<!-- begin set client country -->
<TR>
	<TD vAlign=center noWrap align=right width="20%">
		<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
		Client Country: </FONT>
	</TD>
	<TD vAlign=center align=left>
		$countryList
	</TD>
</TR>
<!-- end set client country -->

<!-- begin set owner type -->
<TR>
	<TD vAlign=center noWrap align=right width="20%">
		<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
		Owner Type: </FONT>
	</TD>
	<TD vAlign=center align=left>
		$ownerTypeList
	</TD>
</TR>
<!-- Orange Client-->
<TR>
	<TD vAlign=center noWrap align=right width="20%">
		<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
		Orange Client: </FONT>
	</TD>
	<TD vAlign=center align=left>
		$OrangeClientButtons 
	</TD>
</TR>
<!-- end set owner type -->
	|;
	
}

sub clientGroupingSettings {
	
	my ($clientData) = @_;
	
	my $clientGroupings = getClientGroupings($clientData);
	
	print qq|
<TR>
	<TD vAlign=center noWrap align=right width="20%">
		<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
		List Owner: </FONT>
	</TD>
	<TD vAlign=center align=left>
		<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
		$clientGroupings :: Add new list owner <INPUT type=text size=20 maxlength=15 value="" name="clientGroupName">
		</FONT>
	</TD>
</TR>
	|;
	
	
}

sub getClientGroupings {
		
	my ($clientData) = @_;
		
	my $output = qq|
	<select name='clientGroupNames'>
		<option value=''>Choose a client group</option>
	|;
			
	my $sql = "select clientStatsGroupingID, clientStatsGroupingName, clientStatsGroupingLabel from ClientStatsGrouping order by clientStatsGroupingName";
	my $sth = $dbhu->prepare($sql);
	$sth->execute();
	
	my $setClientGrouping = '';

	while (my $data = $sth->fetchrow_hashref()){
		
		my $selected = '';
		
		if($data->{'clientStatsGroupingID'} == $clientData->{'clientStatsGroupingID'}){
			$selected = 'SELECTED';
			$setClientGrouping = $data->{'clientStatsGroupingLabel'};	
		} 
		
		$output .= qq|<option value="$data->{'clientStatsGroupingLabel'}" $selected> $data->{'clientStatsGroupingName'} </option>|;
			
	}	
	
	$output .= qq|</select> \n|;
	$output .= qq|<input type='hidden' name='previousClientGrouping' value="$setClientGrouping" />|;
	
	return($output);
	
}
