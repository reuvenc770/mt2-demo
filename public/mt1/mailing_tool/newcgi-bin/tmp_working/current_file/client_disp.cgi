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
my ($rev_share, $mailing_cpm, $broker_fee);
my $password;
my ($puserid, $pmesg);
my $company;
my $website_url;
my $company_phone;
my $user_type;
my $this_user_type;
my $images = $util->get_images_url;
my $privacy_policy_url;
my $account_type;
my $unsub_option;

#------  connect to the util database -----------
$util->db_connect();
$dbh = $util->get_dbh;

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
	$util->clean_up();
    exit(0);
}

$sql = "select user_type from user where user_id = $user_id";
$sth = $dbh->prepare($sql);
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
if ( $pmode ne "A"  and  $pmode ne "U" ) 
{	#---- Invalid MODE - Mode MUST = 'A' (add)  or  'U' (update)  ---------
	util::logerror("<br><br><b>Invalid</b> Mode: <b>$pmode</b> - The Mode MUST equal 'A' or 'U'.") ;
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
	$mailing_cpm = '';
	$broker_fee = '';
	$rev_share = '';
}
else
{
	#------  Get the information about the user for display  --------
	$sql = "select first_name,last_name,address,address2,city,state,zip,phone,
		email_addr, user_type, status, max_names, max_mailings, username, password,
		company, website_url, company_phone, account_type, privacy_policy_url, unsub_option,mediactivate_id, mediactivate_pw,
		rev_share, mailing_cpm, broker_fee
		from user where user_id = $puserid";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	($fname,$lname,$address,$address2,$city,$state,$zip,$phone,$email_addr,
		$user_type, $status,$max_names,$max_mailings,$username,$password,
		$company, $website_url, $company_phone, $account_type, 
		$privacy_policy_url, $unsub_option,$mediactivate_id, $mediactivate_pw, $rev_share, $mailing_cpm, $broker_fee) = $sth->fetchrow_array();
	$sth->finish();
	
	if ( $email_addr eq "" ) 
	{
		$errmsg = $dbh->errstr();
	    util::logerror("<br><br>Error Getting user information for UserID: $puserid &nbsp;&nbsp;$errmsg");
		exit(99) ;
	}
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
			<a href="privacy_policy.cgi">Privacy Policy</a> for details.<BR></FONT></TD>
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
        if (document.edit_client.fname.value == "")
        {
            alert("You MUST enter a value for the Contact First Name field."); 
			document.edit_client.fname.focus();
            return false;
        }
        if (document.edit_client.lname.value == "")
        {
            alert("You MUST enter a value for the Contact Last Name field."); 
			document.edit_client.lname.focus();
            return false;
        }
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
                    <TD vAlign=center noWrap align=right width="20%">
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
						First Name: </FONT></TD>
                    <TD vAlign=center align=left>
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
						<INPUT size=20 maxlength=20 value="$fname" name=fname>
end_of_html
	
	#-----------------------------------------------------
	# Only Admin users (eg user_type = 'A') view Status
	#-----------------------------------------------------
	if ( $this_user_type eq "A" ) 
	{
		print qq{ &nbsp;&nbsp;&nbsp;Status: };
		#------ Toggle CHECKED based on Status value --------------
		if ( $status eq "A" )
		{
			print qq{ <input type="radio" name="status" value="A" CHECKED>Active } ;
			print qq{ &nbsp;<input type="radio" name="status" value="D">Deleted } ;
		}
		else
		{
	    	print qq{ <input type="radio" name="status" value="A">Active } ;
			print qq{ &nbsp;<input type="radio" name="status" value="D" CHECKED>Deleted } ;
		}
	}
	else
	{
		print qq{<input type="hidden" value="$status" name="status"> } ;
	}
		

	print << "end_of_html" ;
						</FONT></TD>
					</TR>
                    <TR> <!-- -------- Contact Last Name -------------- -->
                    <TD vAlign=center noWrap align=right width="20%">
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
						Last Name: </FONT></TD>
                    <TD vAlign=center align=left>
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
						<INPUT size=40 maxlength=40 value="$lname" name=lname></FONT></TD>
					</TR>

                    <TR> <!-- -------- Contact User Name -------------- -->
                    <TD vAlign=center noWrap align=right width="20%">
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
						User Name: </FONT></TD>
                    <TD vAlign=center align=left>
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
						<INPUT size=20 maxlength=15 value="$username" name=username></FONT></TD>
						<INPUT type="hidden" name="old_username" value="$username">
					</TR>

                    <TR> <!-- -------- Contact Password Name -------------- -->
                    <TD vAlign=center noWrap align=right width="20%">
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
						Password: </FONT></TD>
                    <TD vAlign=center align=left>
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
						<INPUT type=text size=20 maxlength=15 value="$password" name=password>
				<!--		&nbsp;&nbsp;Verify Password: 
						<INPUT type=password size=20 maxlength=15 value="$password" 
						name=password_verify> --></FONT></TD>
					</TR>
                    <TR> 
                    <TD vAlign=center noWrap align=right width="20%">
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>Mediactive Id: </FONT></TD>
                    <TD vAlign=center align=left>
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2><INPUT type=text size=20 maxlength=15 value="$mediactivate_id" name=medid>
						</FONT></TD>
					</TR>
                    <TR> 
                    <TD vAlign=center noWrap align=right width="20%">
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>Mediactive Pw: </FONT></TD>
                    <TD vAlign=center align=left>
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2><INPUT type=text size=20 maxlength=15 value="$mediactivate_pw" name=medpw>
						</FONT></TD>
					</TR>
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

                    <TR>  <!-- --------- Company PHone ----------------- -->
                    <TD vAlign=center noWrap align=right width="20%">
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
						Company Phone: </FONT></TD>
                    <TD vAlign=center align=left>
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
						<INPUT size=40 value="$company_phone" MAXLENGTH=35 name=company_phone> 
                        </FONT></TD>
					</TR>
end_of_html
	
	if ( $this_user_type eq "A" )
	{
		print qq{ <TR>  <!-- ------  Max Names, Mailed ----------------- --> } ;
		print qq{<TD vAlign=center noWrap align=right width="20%"> } ;
		print qq{<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2> } ;
		print qq{Max Names: </FONT></TD> } ;
		print qq{<TD vAlign=center align=left> } ;
		print qq{<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2> } ;
		print qq{<INPUT size=5 value="$max_names" MAXLENGTH=7 name=max_names>  } ;
		print qq{&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Max Mailings:  } ;
		print qq{<INPUT size=5 value="$max_mailings" MAXLENGTH=7 name=max_mailings>  } ;
		print qq{</FONT></TD> } ;
		print qq{</TR> } ;

		print qq{<TR>  <!-- ------  User Type ----------------- --> } ;
		print qq{<TD vAlign=center noWrap align=right width="20%"> } ;
		print qq{<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2> } ;
		print qq{User Type: </FONT></TD> } ;
		print qq{<TD vAlign=center align=left> } ;
		print qq{<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2> } ;


		#------ Toggle CHECKED based on USER_TYPE value --------------
		if ( $user_type eq "A" )       # Administrator User
		{
			print qq{ <input type="radio" name="user_type" value="A" CHECKED>Admin  } ;
			print qq{ &nbsp;&nbsp;<input type="radio" name="user_type" value="N">Normal  } ;
			print qq{ &nbsp;&nbsp;<input type="radio" name="user_type" value="R">Report  } ;
			print qq{ &nbsp;&nbsp;<input type="radio" name="user_type" value="D">Demo  } ;
		}
		elsif ( $user_type eq "R" )    # Report User
		{
			print qq{ <input type="radio" name="user_type" value="A">Admin  } ;
			print qq{ &nbsp;&nbsp;<input type="radio" name="user_type" value="N">Normal  } ;
			print qq{ &nbsp;&nbsp;<input type="radio" name="user_type" value="R" CHECKED>Report  } ;
			print qq{ &nbsp;&nbsp;<input type="radio" name="user_type" value="D">Demo  } ;
		}
		elsif ( $user_type eq "D" )    # Demo User
		{
			print qq{ <input type="radio" name="user_type" value="A">Admin  } ;
			print qq{ &nbsp;&nbsp;<input type="radio" name="user_type" value="N">Normal  } ;
			print qq{ &nbsp;&nbsp;<input type="radio" name="user_type" value="R">Report  } ;
			print qq{ &nbsp;&nbsp;<input type="radio" name="user_type" value="D" CHECKED>Demo  } ;
		}
		else                           # Normal User - default to 
		{
			print qq{ <input type="radio" name="user_type" value="A">Admin  } ;
			print qq{ &nbsp;&nbsp;<input type="radio" name="user_type" value="N" CHECKED>Normal  } ;
			print qq{ &nbsp;&nbsp;<input type="radio" name="user_type" value="R">Report  } ;
			print qq{ &nbsp;&nbsp;<input type="radio" name="user_type" value="D">Demo  } ;
		}

	} 
	else
	{	#----------------------------------------------------------
		# Set Admin fields as 'Hidden' so Insert/Update works OK
		#----------------------------------------------------------
		print qq{<INPUT type="hidden" value="$max_names" name=max_names>  } ;
		print qq{<INPUT type="hidden" value="$max_mailings" name=max_mailings>  } ;
		print qq{<input type="hidden" value="$user_type" name="user_type"> } ;

	}  # end-if displaying admin_user fields

	print << "end_of_html" ;
                   		</FONT></TD>
					</TR>
end_of_html

	if ( $this_user_type eq "A" )
	{
		print << "end_of_html";
                    <TR>  <!-- --------- account Type ----------------- -->
                    <TD vAlign=center noWrap align=right width="20%">
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
						Account Type: </FONT></TD>
                    <TD vAlign=center align=left>
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
						<select name=account_type> 
						<option value="BRONZE" $account_type_b>Bronze</option>
						<option value="SILVER" $account_type_s>Silver</option>
						<option value="GOLD" $account_type_g>Gold</option>
						<option value="PLATINUM" $account_type_p>Platinum</option>
						</select></FONT></TD>
					</TR>
end_of_html
	}
	else
	{
		print qq { <input type="hidden" name="account_type" value="$account_type"> \n };
	}

	print << "end_of_html";
                    <TR>  <!-- --------- Privacy Policy URL ----------------- -->
                    <TD vAlign=center noWrap align=right width="20%">
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
						Privacy Policy URL: </FONT></TD>
                    <TD vAlign=center align=left>
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
						<INPUT size=40 value="$privacy_policy_url" MAXLENGTH=255 
						name=privacy_policy_url></FONT></TD>
					</TR>

                    <TR>  <!-- --------- Unsubscribe Option ----------------- -->
                    <TD vAlign=top noWrap align=right width="20%">
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
						Member Unsubscribe Option:</FONT></TD>
                    <TD vAlign=center align=left>
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
						<select name=unsub_option> 
						<option value="ONE LIST"  $unsub_one>Unsubscribe from One List</option>
						<option value="ALL LISTS" $unsub_all>Unsubscribe from All Lists</option>
						</select> Defines what option list members are <br>
						shown when they click "unsubscribe".</FONT></TD>
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
				<A HREF="mainmenu.cgi">
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
