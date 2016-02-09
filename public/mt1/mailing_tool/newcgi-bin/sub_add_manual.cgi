#!/usr/bin/perl
#===============================================================================
# File   : sub_add_manual.cgi
#
# Add Subscribers manually 
#
# History
# Grady Nash, 10-30-2001
#===============================================================================

#-----------------------
# include Perl Modules
#-----------------------
use strict;
use CGI qw(:standard);
use util;

$|=1 ;   # set OUTPUT_AUTOFLUSH to true

my $util = util->new;
my $query = new CGI;
my $dbh;
my %hsh_fl_pos_names;
my ($email_addr,  $email_type,       $gender);
my ($first_name,  $middle_name,      $last_name);
my ($birth_date,  $address,          $address2);
my ($city,        $state,            $zip);
my ($country,     $marital_status,   $occupation);
my ($job_status,  $household_income, $education_level);
my $email_user_id;
my ($log_file, $file_name, $file_out);
my ($reccnt_tot, $reccnt_good, $reccnt_bad);
my $html_dir_unix;
my $html_dir_http;
my ($TRUE, $FALSE);
$TRUE  = 1 ;
$FALSE = 0 ;
my (@list_array, $email_list_text_area) ;
my ($list_name, @email_array);
my $images = $util->get_images_url;
my $alt_light_table_bg = $util->get_alt_light_table_bg;
my $light_table_bg = $util->get_light_table_bg;
my ($upload_file_opt, $file_has_errors) ;
my (@file_layout_array, @type_array, @length_array, %values_hash);
my (%fname_fpos_hash, %fpos_fname_hash, %fields_hash);
my (@fields_array, $field, $fld_pos, $i) ;
my $errmsg;
my (%state_hash, %email_type_hash);
$email_list_text_area = $query->param('email_list_text_area');
my $sql;
my $sth;
my $rows;

# ----- connect to the util database -------

###$util->db_connect();

my ($dbhq,$dbhu)=$util->get_dbh();
###$dbh = $util->get_dbh;

# ----- check for login -------

my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}

# get some directories from sysparm

$sql = "select parmval from sysparm where parmkey = 'HTML_DIR_UNIX'";
$sth = $dbhq->prepare($sql) ;
$sth->execute();
($html_dir_unix) = $sth->fetchrow_array();
$sth->finish();

$sql = "select parmval from sysparm where parmkey = 'HTML_DIR_HTTP'";
$sth = $dbhq->prepare($sql) ;
$sth->execute();
($html_dir_http) = $sth->fetchrow_array();
$sth->finish();

#---- Open HTML Error Log file ---------------------------------------------

$log_file = $html_dir_unix . "sub_add_${user_id}.error.log";
open(LOG,"> $log_file") || die ( "Could NOT open file $log_file" ) ;
print LOG "<html><head><title>Error Log</title></head><body>" ;
print LOG "<center>Error Log File Summary </center>";

# figure out which list was selected

my $list_id = $query->param('list_id_man');
if (!$list_id)
{
	util::logerror("You must pick a list before pressing Add Addresses Now");
    $util->clean_up();
    exit(0);
}

# process the email address list and add to the db

process_list();

# close the error log file

close LOG ;

# show the user the summary

print_summary();

if ( $file_has_errors )
{ 
	print_html_error_file();
}

$util->footer();

$util->clean_up();   
exit(0) ;


#--------- End Main Logic ------------------------------



#===============================================================================
# Sub: process_list
#  1. Split html TEXTAREA field into separate fields - One for each Email Addr
#  2. Loop for ALL 'Lists' selected
#  3. Pass control to add 'list_member' recs
#===============================================================================
sub process_list 
{
	my ($invalid_rec, $list_email_addr);
	my $emailtype = $query->param('emailtype');

	#-----------------------------------------------------------------------------
	#   1. Remove Space, NewLine, CR, FF, Tab from text string 
	#   2. if Mult Pipes Exist together change to Single Pipe char (eg from 2-999)
	#   3. Split text line via Pipe char into Array to get individual Email Addrs
	#-----------------------------------------------------------------------------
	$email_list_text_area =~ s/[ \n\r\f\t]/\|/g ;    
	$email_list_text_area =~ s/\|{2,999}/\|/g ;           
	@email_array = split '\|', $email_list_text_area ;

	$file_has_errors = $FALSE ;              # assume File/List is Valid
	$reccnt_bad  = 0 ;
	$reccnt_good = 0 ;
	$reccnt_tot  = 0 ;

	foreach $list_email_addr (@email_array)
	{
		$reccnt_tot++;
	
		&set_fields_null();

		$email_addr = $list_email_addr ;
		$invalid_rec = edit_rec() ; 

		if ( $invalid_rec ) 
		{
			$reccnt_bad++;
			$file_has_errors = $TRUE ;
		}
		else                                 # Record is Valid - do Adds/Updates as necessary
		{
			# add to email_user table first

			$email_type = $emailtype;
			&add_upd_email_user();

			# count this as a good one

			$reccnt_good++;
		}
	} 
} 

#===============================================================================
# Sub: print_summary
#===============================================================================
sub print_summary
{
	my ($list_cnt, $color, $tot_good, $tot_dup, $tot_bad);
	my ($sth1, $list_name) ;
    my ($error_file_html) ;
	$color = "$light_table_bg" ;

    $error_file_html = $log_file ;
    $error_file_html =~ s/^.*\/// ;   # Del beg path info to get the file_name
    $error_file_html = $html_dir_http . $error_file_html ;

	# get name of the list
	
	$sql = "select list_name from list where list_id = $list_id";
	$sth1 = $dbhq->prepare($sql) ;
	$sth1->execute();
	($list_name) = $sth1->fetchrow_array();
	$sth1->finish();

	util::header("Add Subscriber Status");

	print << "end_of_html";
</TD>
</TR>
<TR>
<TD vAlign=top align=left bgColor=#999999>

	<TABLE cellSpacing=0 cellPadding=10 bgColor=#FFFFFF border=0 width=100%>
	<TBODY>
	<TR>
	<TD vAlign=top align=left bgColor=#FFFFFF colSpan=4>

		<TABLE cellSpacing=0 cellPadding=0 width="100%" bgColor=#ffffff border=0>
		<TBODY>
		<TR>
		<TD vAlign=center align=left><FONT face="verdana,arial,helvetica,sans serif" 
			color=#509C10 size=3><B>Confirmation</B></FONT></TD>
		</TR>
		<tr>
		<td><img height="20" src="$images/spacer.gif"></td>
		</tr>
		</TBODY>
		</TABLE>
	
		<TABLE bgcolor="$light_table_bg" cellSpacing=0 cellPadding=0 width="100%" border=0>
		<TBODY>
		<TR bgColor=#509C10 height=15>
		<TD colspan="4" align=middle width="100%" height=15>
			<font face="Verdana,Arial,Helvetica,sans-serif" color="white" size="2">
			<b>Summary of Subscriber Emails Added</b></font></TD>
		</TR>
		<TR bgColor=#509C10 height=15>
		<td colspan=4 >&nbsp;</td>
		</tr>
 		<TR> 
 		<TD width="5%">&nbsp;</td>
 		<TD align="right" width="45%">	 
 			<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
			List&nbsp;&nbsp;</FONT></TD> 
 		<TD align="left" width="45%">	 
 			<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 
 			size=2><b>$list_name</b></FONT></TD> 
 		<TD width="5%">&nbsp;</td>
 		</TR> 
 		<TR> 
 		<TD>&nbsp;</td>
 		<TD align="right">	 
 			<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
			Total Records Processed&nbsp;&nbsp;</FONT></TD> 
 		<TD align="left">	 
 			<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
			<b>$reccnt_tot</b></FONT></TD> 
 		<TD>&nbsp;</td>
 		</TR> 
 		<TR> 
 		<TD>&nbsp;</td>
 		<TD align="right">	 
 			<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 
 			size=2>Good Records Added/Updated&nbsp;&nbsp;</FONT></TD> 
 		<TD align="left">	 
 			<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 
 			size=2><b>$reccnt_good</b></FONT></TD> 
 		<TD>&nbsp;</td>
 		</TR> 
 		<TR> 
 		<TD>&nbsp;</td>
 		<TD align="right">	 
 			<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
			Error Records Not Added/Updated&nbsp;&nbsp;</FONT></TD> 
 		<TD align="left">	 
 			<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
			<b>$reccnt_bad</b></FONT></TD> 
 		<TD>&nbsp;</td>
 		</TR> 

end_of_html

	if ( $file_has_errors )
	{
		print qq{ <tr>
			<td colspan=4 width="100%"><br>
				<a href="$error_file_html">Click here to view records with errors 
				for the uploaded file</a></td>
			</tr> \n } ;
	}

	print << "end_of_html" ;
		<tr>
		<td colcpan=4><img src="$images/spacer.gif" height="15"></td>
		</tr>
		<tr>
		<td colspan=4 align="center" width="100%">

			<table cellpadding="0" cellspacing="0" border="0" width="100%">
			<tr>
			<td width="50%" align="center">
				<a href="mainmenu.cgi">
				<img src="$images/home_blkline.gif" border="0"></a></td>
			<td width="50%" align="center">
				<a href="sub_disp_add.cgi">
				<img src="$images/previous_arrow.gif" border="0"></a></td>
			</tr>
			</table>

		</tr>
		</tbody>
		</table>

	</td>
	</tr>
	</tbody>
	</table>

</TD>
</TR>
<TR>
<TD noWrap align=left height=17>
end_of_html

}  # end sub print_summary 

#===============================================================================
# Sub: add_upd_email_user
#===============================================================================
sub add_upd_email_user
{
	my ($sql_ins, $sql_upd, $key, $value);
	my ($rec_found);

	$email_addr =~ s/^\s*// ;    # remove leading  white space
	$email_addr =~ s/\s*$// ;    # remove trailing white space

	#--- Does email_user already exist? --------------------

	$sql = "select count(*), email_user_id from member_list 
		where  email_addr = '$email_addr' 
		group  by email_user_id";
	$sth = $dbhq->prepare($sql) ;
	$sth->execute();
	($rec_found, $email_user_id) = $sth->fetchrow_array();
	if ($rec_found eq "")
	{
		$rec_found = $FALSE ;
	}
	$sth->finish();

	#--- If Numeric Fields are Null then set var to 'null' -----
	if ( $marital_status eq "" )    { $marital_status = "null" ; }
	if ( $occupation eq "" )        { $occupation = "null" ; }
	if ( $job_status eq "" )        { $job_status = "null" ; }
	if ( $household_income eq "" )  { $household_income = "null" ; }
	if ( $education_level eq "" )   { $education_level = "null" ; }

	if ($rec_found)
	{	
		#---- Format Update statement (only chg fields where value is present) ----
	
		$sql_upd = qq{update member_list set status = 'A',} ;
#		if ($email_type ne "" )        { $sql_upd = $sql_upd . qq{email_type = '$email_type',}; }
		if ($gender ne "" )            { $sql_upd = $sql_upd . qq{gender = '$gender',}; }
		if ($first_name ne "" )        { $sql_upd = $sql_upd . qq{first_name = '$first_name',}; }
#		if ($middle_name ne "" )       { $sql_upd = $sql_upd . qq{middle_name = '$middle_name',}; }
		if ($last_name ne "" )         { $sql_upd = $sql_upd . qq{last_name = '$last_name',}; }
		if ($birth_date ne "" )        { $sql_upd = $sql_upd . qq{birth_date = $birth_date,}; }
		if ($address ne "" )           { $sql_upd = $sql_upd . qq{address = '$address',}; }
		if ($address2 ne "" )          { $sql_upd = $sql_upd . qq{address2 = '$address2',}; }
		if ($city ne "" )              { $sql_upd = $sql_upd . qq{city = '$city',}; }
		if ($state ne "" )             { $sql_upd = $sql_upd . qq{state = '$state',}; }
		if ($zip ne "" )               { $sql_upd = $sql_upd . qq{zip = '$zip',}; }
		if ($country ne "" )           { $sql_upd = $sql_upd . qq{country = '$country',}; }
#		if ($marital_status ne "" )    { $sql_upd = $sql_upd . qq{marital_status = $marital_status,}; }
#		if ($occupation ne "" )        { $sql_upd = $sql_upd . qq{occupation = $occupation,}; }
#		if ($job_status ne "" )        { $sql_upd = $sql_upd . qq{job_status = $job_status,}; }
#		if ($household_income ne "" )  { $sql_upd = $sql_upd . qq{income = $household_income,}; }
#		if ($education_level ne "" )   { $sql_upd = $sql_upd . qq{education = $education_level,}; }

		$sql_upd =~ s/,$//;              # remove trailing comma 
		$sql_upd = $sql_upd . qq{ where email_user_id = $email_user_id };

		# Update member_list table

		$rows = $dbhu->do($sql_upd);
   		if ($dbhu->err() != 0)
    	{
        	$errmsg = $dbhu->errstr();
        	util::logerror("Updating member_list record: $sql_upd : $errmsg");
        	exit(0);
    	}
	}
	else
	{	
		#---- Format Insert statement (email_user_id is auto Increment)-------------

		$sql_ins = qq{insert into member_list (list_id, status, subscribe_datetime,
			email_addr, gender, 
			first_name, last_name, 
			birth_date, address, address2, 
			city, state, zip, 
			country ) 
			values ($list_id, 'A', curdate(),
			'$email_addr', '$gender', 
			'$first_name', '$last_name', 
			'$birth_date', '$address', '$address2', 
			'$city', '$state', '$zip', 
			'$country') };

		# Insert member_list table

		$rows = $dbhu->do($sql_ins);
   		if ($dbhu->err() != 0)
    	{
        	$errmsg = $dbhu->errstr();
        	util::logerror("Inserting member_list record: $sql_ins : $errmsg");
        	exit(0);
    	}
	}

} # end sub add_upd_email_user

#===============================================================================
# Sub: edit_rec
#===============================================================================
sub edit_rec
{
	my ($line) = @_ ;
	my ($cat_id, $field_position, $table_name, $db_field, $ftype, $flength);
	my ($invalid_rec, $invalid_fld, $no_match_found);
	my ($fld_len, @values_array, $value, $log_mesg, $reccnt_mesg);
	my ($str_usa_states) ;

	$reccnt_mesg = qq{<br><br>Record <b>${reccnt_tot}:</b> $line<br>};
	$invalid_rec = $FALSE;                         # assume record are valid

 	#----- Email Addr Edits -----------------------------------------
 	if ( $email_addr eq "" ) 
 	{
 		$log_mesg = $reccnt_mesg ;
 		$reccnt_mesg = "" ;
		$log_mesg = $log_mesg . qq{&nbsp;&nbsp;&nbsp;Field: <b><font color="blue">Email Address</font></b><br>};
 		$invalid_rec = $TRUE;
 		$log_mesg = $log_mesg . 
 			qq{&nbsp;&nbsp;&nbsp;Invalid!  The Email Address field is Null.  This field is <b>MANDATORY</b>!<br> \n};
 		print LOG $log_mesg;
 	}
	else
 	{
 		# Email must have at least @ and . 
		my $pos_at = index($email_addr, "\@");
		my $pos_dot = index($email_addr, "\.");
		if ($pos_at >= 0 && $pos_dot >= 0)
 		{
 			# valid Email Addr
 		}
 		else
 		{
 			$log_mesg = $reccnt_mesg ;
 			$reccnt_mesg = "" ;
 			$invalid_rec = $TRUE;
			$log_mesg = $log_mesg . qq { &nbsp;&nbsp;&nbsp;Field: <b><font color="blue">
				Email Address</font></b><br>
  				&nbsp;&nbsp;&nbsp;Invalid Email Address: <font color="blue">$email_addr</font>.  
				The Email Address MUST contain at least 1 @ and . (eg period).<br> \n};
 			print LOG $log_mesg;
 		}
 	}

	#----- Email Type Edits ----------------
	if ($email_type ne "" )
	{
		if ($email_type ne 'H'  &&  $email_type ne 'T'  &&  
			$email_type ne 'D'  &&  $email_type ne 'A')
		{
 			$log_mesg = $reccnt_mesg ;
 			$reccnt_mesg = "" ;
			$log_mesg = $log_mesg . qq{&nbsp;&nbsp;&nbsp;Field: <b><font color="blue">Email Type</font></b><br>};
 			$invalid_rec = $TRUE;
 			$log_mesg = $log_mesg . 
  				qq{&nbsp;&nbsp;&nbsp;Invalid Email Type: <font color="blue">$email_type</font>  Valid values are 'A', 'D', 'H', 'T' (Aol, Dont Know, Html, Text).<br> \n};
 			print LOG $log_mesg;
 		}
 	}
 	else
 	{
 		$email_type = 'H' ;  # default to 'H' (eg HTML)
 	}
		
	#----- Gender Edits ----------------
	if ($gender ne "" )
	{
		if ( $gender ne 'M'  &&  $gender ne 'F' )
		{
 			$log_mesg = $reccnt_mesg ;
 			$reccnt_mesg = "" ;
			$log_mesg = $log_mesg . qq{&nbsp;&nbsp;&nbsp;Field: <b><font color="blue">Gender </font></b><br>};
 			$invalid_rec = $TRUE;
 			$log_mesg = $log_mesg . 
  				qq{&nbsp;&nbsp;&nbsp;Invalid Gender: <font color="blue">$gender</font>  Valid values are 'F', 'M' (Male, Female).<br> \n};
 			print LOG $log_mesg;
 		}
 	}
		
	#----- Birth Date ----------------
	if ($birth_date ne "" )
	{
		# if ( $birth_date =~ m/^\D*$/ )
		if ( $birth_date =~ m/\D/ )
		{
 			$log_mesg = $reccnt_mesg ;
 			$reccnt_mesg = "" ;
			$log_mesg = $log_mesg . qq{&nbsp;&nbsp;&nbsp;Field: <b><font color="blue">Birth Date </font></b><br>};
 			$invalid_rec = $TRUE;
 			$log_mesg = $log_mesg . 
  				qq{&nbsp;&nbsp;&nbsp;Invalid Birth Date: <font color="blue">$birth_date</font>  Valid values xxxxx.<br> \n};
 			print LOG $log_mesg;
 		}
 	}

 	#----- Country ----------------
	# commented this test - allow anything to go in country field - Grady, 10/10/2001
#	if ($country ne "" )
#	{
#		if ( $country ne 'USA'  &&  $country ne 'CAN' )
#		{
# 			$log_mesg = $reccnt_mesg ;
# 			$reccnt_mesg = "" ;
#			$log_mesg = $log_mesg . qq{&nbsp;&nbsp;&nbsp;Field: <b><font color="blue">Country 
#				</font></b><br>};
# 			$invalid_rec = $TRUE;
# 			$log_mesg = $log_mesg . qq{&nbsp;&nbsp;&nbsp;Invalid Country: <font color="blue">
#				$country</font>  Valid values are USA, CAN.<br> \n};
# 			print LOG $log_mesg;
# 		}
# 	}
		
 	#----- State/Province ----------------
	if ($state ne "" )
	{
		if ($country eq "USA" || $country eq "" )
		{
			$country = 'USA';
			$str_usa_states = qq{AK AL AR AZ CA CO CT DC DE FL GA HI IA ID IL IN KS KY LA 
				MA MD ME MI MN MO MS MT NC ND NE NH NJ NM NV NY OH OK OR 
				PA RI SC SD TN TX UT VA VT WA WI WV WY};
			if ( $str_usa_states =~ m/$state/ )
			{
				# Valid USA State found
			}
			else
			{
 				$invalid_rec = $TRUE;
 				$log_mesg = $reccnt_mesg ;
 				$reccnt_mesg = "" ;
				$log_mesg = $log_mesg . qq { &nbsp;&nbsp;&nbsp;Field: <b><font color="blue">
					State/Province </font></b><br>&nbsp;&nbsp;&nbsp;Invalid State/Province: 
					<font color="blue">$state</font> Valid values are state codes for 
					USA and province codes for Canada.<br> \n };
 				print LOG $log_mesg;
			}
		}
 	}
		
 	#----- Marital Status ----------------
	if ($marital_status ne "" )
	{
		if ( $marital_status < "1"  ||  $marital_status > "6"  || $marital_status =~ m/\D/  )
		{
 			$log_mesg = $reccnt_mesg ;
 			$reccnt_mesg = "" ;
			$log_mesg = $log_mesg . qq{&nbsp;&nbsp;&nbsp;Field: <b><font color="blue">Marital Status </font></b><br>};
 			$invalid_rec = $TRUE;
 			$log_mesg = $log_mesg . 
  				qq{&nbsp;&nbsp;&nbsp;Invalid Marital Status: <font color="blue">$marital_status</font>  Valid values range from 1 - 6.  See instructions for more details.<br> \n};
 			print LOG $log_mesg;
 		}
 	}
		
 	#----- Occupation ----------------
	if ($occupation ne "" )
	{
		if ( $occupation < "1"  ||  $occupation > "19" || $occupation =~ m/\D/  )
		{
 			$log_mesg = $reccnt_mesg ;
 			$reccnt_mesg = "" ;
			$log_mesg = $log_mesg . qq{&nbsp;&nbsp;&nbsp;Field: <b><font color="blue">Occupation </font></b><br>};
 			$invalid_rec = $TRUE;
 			$log_mesg = $log_mesg . 
  				qq{&nbsp;&nbsp;&nbsp;Invalid Occupation: <font color="blue">$occupation</font>  Valid values range from 1 - 19.  See instructions for more details.<br> \n};
 			print LOG $log_mesg;
 		}
 	}
		
 	#----- Job Status ----------------
	if ($job_status ne "" )
	{
		if ( $job_status < "1"  ||  $job_status > "14"  || $job_status =~ m/\D/  )
		{
 			$log_mesg = $reccnt_mesg ;
 			$reccnt_mesg = "" ;
			$log_mesg = $log_mesg . qq{&nbsp;&nbsp;&nbsp;Field: <b><font color="blue">Job Status </font></b><br>};
 			$invalid_rec = $TRUE;
 			$log_mesg = $log_mesg . 
  				qq{&nbsp;&nbsp;&nbsp;Invalid Job Status: <font color="blue">$job_status</font>  Valid values range from 1 - 14.  See instructions for more details.<br> \n};
 			print LOG $log_mesg;
 		}
 	}
		
 	#----- Income ----------------
	if ($household_income ne "" )
	{
		if ( $household_income < "1"  ||  $household_income > "17"  || $household_income =~ m/\D/  )
		{
 			$log_mesg = $reccnt_mesg ;
 			$reccnt_mesg = "" ;
			$log_mesg = $log_mesg . qq{&nbsp;&nbsp;&nbsp;Field: <b><font color="blue">Household Income </font></b><br>};
 			$invalid_rec = $TRUE;
 			$log_mesg = $log_mesg . 
  				qq{&nbsp;&nbsp;&nbsp;Invalid Household Income: <font color="blue">$household_income</font>  Valid values range from 1 - 17.  See instructions for more details.<br> \n};
 			print LOG $log_mesg;
 		}
 	}
		
 	#----- Education ----------------
	if ($education_level ne "" )
	{
		if ( $education_level < "1"  ||  $education_level > "6"  || $education_level =~ m/\D/  )
		{
 			$log_mesg = $reccnt_mesg ;
 			$reccnt_mesg = "" ;
			$log_mesg = $log_mesg . qq{&nbsp;&nbsp;&nbsp;Field: <b><font color="blue">Education Level </font></b><br>};
 			$invalid_rec = $TRUE;
 			$log_mesg = $log_mesg . 
  				qq{&nbsp;&nbsp;&nbsp;Invalid Education Level: <font color="blue">$education_level</font>  Valid values range from 1 - 6.  See instructions for more details.<br> \n};
 			print LOG $log_mesg;
 		}
 	}
		
	return $invalid_rec ;

} 

#===============================================================================
# Sub: print_html_error_file
#===============================================================================
sub print_html_error_file
{
	my ($line) ;
	open(ERROR_FILE,"< $log_file") || die ( "Could NOT open file $log_file for input" ) ;
	while (<ERROR_FILE>)
	{
		$line = $_ ;
		print $line;
	}
	close ERROR_FILE ;
} 

#===============================================================================
# Sub: set_fields_null
#===============================================================================
sub set_fields_null
{
 	$email_addr  = "";
 	$email_type  = "";
 	$gender      = "";
 	$first_name  = "";
 	$middle_name = "";
 	$last_name   = "";
 	$birth_date  = "";
 	$address     = "";
 	$address2    = "";
 	$city        = "";
 	$state       = "";
 	$zip         = "";
 	$country     = "";
 	$marital_status  = "";
 	$occupation  = "";
 	$job_status  = "";
 	$household_income = "";
 	$education_level  = "";

} 
