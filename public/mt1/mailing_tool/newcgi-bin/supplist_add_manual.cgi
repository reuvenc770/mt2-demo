#!/usr/bin/perl
#===============================================================================
# File   : supplist_add_manual.cgi
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
my $list_id;
$email_list_text_area = $query->param('email_list_text_area');
$list_id =$query->param('tid');
my $sql;
my $sth;
my $rows;

# ----- connect to the util database -------

###$util->db_connect();

my ($dbhq,$dbhu)=$util->get_dbh();
my $dbh3 = DBI->connect("DBI:mysql:supp:suppressp.routename.com","db_user","sp1r3V");
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

$log_file = $html_dir_unix . "supplist_add_${user_id}.error.log";
open(LOG,"> $log_file") || die ( "Could NOT open file $log_file" ) ;
print LOG "<html><head><title>Error Log</title></head><body>" ;
print LOG "<center>Error Log File Summary </center>";

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
	my $temp_str;

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
		$invalid_rec = 1;
	
		$email_addr = $list_email_addr ;
		$sql = "select email_addr from vendor_supp_list where list_id=$list_id and email_addr='$email_addr'";
		$sth = $dbh3->prepare($sql) ;
		$sth->execute();
		($temp_str) = $sth->fetchrow_array();
		if ($temp_str eq "")
		{ 
			$invalid_rec = 0;
		}
		$sth->finish();

		if ($invalid_rec == 1) 
		{
			$reccnt_bad++;
			$file_has_errors = $TRUE ;
			print LOG "Email Address already exists: $email_addr<br>\n";
		}
		else                                 # Record is Valid - do Adds/Updates as necessary
		{
			# add to email_user table first
			&add_upd_list_member();
			$reccnt_good++;
		}
	} 
} 

#===============================================================================
# Sub: add_upd_list_member
#===============================================================================

sub add_upd_list_member
{
	my ($count, $sth1); 
	
	$sql = "insert ignore into vendor_supp_list(list_id,email_addr) values($list_id,'$email_addr')"; 
	$rows = $dbh3->do($sql);
   	if ($dbhu->err() != 0)
   	{
   		$errmsg = $dbhu->errstr();
   		util::logerror("Updating vendor_supp_list record: $sql : $errmsg");
   		exit(0);
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
	
	$sql = "select list_name from vendor_supp_list_info where list_id = $list_id";
	$sth1 = $dbhq->prepare($sql) ;
	$sth1->execute();
	($list_name) = $sth1->fetchrow_array();
	$sth1->finish();

	util::header("Add Names To Suppression List Status");

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
				<a href="supplist_list.cgi">
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
