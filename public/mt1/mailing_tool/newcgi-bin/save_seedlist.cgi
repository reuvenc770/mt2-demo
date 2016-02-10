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
my $seedlist_freq=$query->param('seedlist_freq');
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

	$sql = "delete from delivery_test_seeds where type='UNIQUE'";
	$rows = $dbhu->do($sql);
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

$log_file = "/tmp/seedlist_${user_id}.error.log";
open(LOG,"> $log_file") || die ( "Could NOT open file $log_file" ) ;
print LOG "<html><head><title>Error Log</title></head><body>" ;
print LOG "<center>Error Log File Summary </center>";

$sql="update sysparm set parmval='$seedlist_freq' where parmkey='SEEDLIST_FREQ'";
$rows = $dbhu->do($sql);

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

	util::header("Add SeedList");

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

	$sql = "insert into delivery_test_seeds(email_addr,type) values('$email_addr','UNIQUE')";
	$rows = $dbhu->do($sql);
   	if ($dbhu->err() != 0)
   	{
       	$errmsg = $dbhu->errstr();
       	util::logerror("Updating delivery_test_seeds record: $sql: $errmsg");
       	exit(0);
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
