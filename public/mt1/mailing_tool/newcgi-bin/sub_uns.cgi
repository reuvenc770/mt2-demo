#!/usr/bin/perl
#===============================================================================
# Purpose: Logical Unsubscribe of 'list_member' recs.
# File   : sub_uns.cgi
#
#--Change Control---------------------------------------------------------------
#  Aug 2, 2001  Mike Baker  Created.
#  Feb 8, 2002  Jim Sobeck  Add logging of emails not removed
#  Apr 2, 2003  Jim Sobeck	Modified to handle new Database Layout
#===============================================================================

#-----------------------
# include Perl Modules
#-----------------------
use strict;
use CGI;
use util;
use Data::Dumper;
use Lib::Database::Perl::Interface::Unsubscribe;

$|=1 ;   # set OUTPUT_AUTOFLUSH to true

my $util = util->new;
my $query = CGI->new;
my $dbh;
my $rows;
my $sql;
my $tid;
my $tot_good;
my $tot_bad;
my $tot_dup;
my (@list_array, $upload_file, $email_list_text_area) ;
my ($list_id, $list_name, $email_addr, @email_array);
my $alt_light_table_bg = $util->get_alt_light_table_bg;
my $images = $util->get_images_url;
my $email_user_id;
$tot_good = 0;
$tot_bad = 0;
$tot_dup = 0;
my ($BytesRead, $Buffer, $Bytes ) ;
my (@temp_file, %confirm);
my $tmp_file;


# ------- Get fields from html Form post -----------------

#@list_array = $query->param('list_chkbox');
$upload_file = $query->param('upload_file');
my $client_id = $query->param('client_id');
$email_list_text_area = $query->param('email_list_text_area');
my $global = $query->param('global');

# ----- connect to the util database -------
my ($dbhq,$dbhu)=$util->get_dbh();
my $unsubscribeInterface = Lib::Database::Perl::Interface::Unsubscribe->new();


# ----- check for login -------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}
#----- Pass control to PROCESS_FILE  or  PROCESS_LIST  -------
if ( $upload_file ne "" ) 
{
	&process_file() ;
}
else
{
	&process_list();
}

#---- Print Delete Statistics -----------------
&print_uns_summary();


$util->clean_up();
exit(0) ;



#===============================================================================
# Sub: process_list
#  1. Split html TEXTAREA field into separate fields - One for each Email Addr
#  2. Loop for ALL 'Lists' selected
#  3. Pass control to add 'list_member' recs
#===============================================================================
sub process_list 
{
	my ($add_good_cnt, $add_bad_cnt, $add_dup_cnt, $tot_email_cnt ) ;
	my ($list_id, $email_addr, @email_array) ;
	my ($email_exists, $sth1, $sth2 ) ;

	#-----------------------------------------------------------------------------
	#   1. Remove Space, NewLine, CR, FF, Tab from text string 
	#   2. if Mult Pipes Exist together change to Single Pipe char (eg from 2-999)
	#   3. Split text line via Pipe char into Array to get individual Email Addrs
	#-----------------------------------------------------------------------------
	open (LOG, ">> /tmp/sub_uns_web1.log");
	$email_list_text_area =~ s/[ \n\r\f\t]/\|/g ;    
	$email_list_text_area =~ s/\|{2,999}/\|/g ;           
	@email_array = split '\|', $email_list_text_area ;

	print LOG "@email_array\n";
	foreach $email_addr (@email_array)
	{
		print LOG "$email_addr\n";
		&uns_upd_list_member($email_addr);
	}
	close LOG;

} # end of sub


#===============================================================================
# Sub: process_file
#  1. Open file
#  2. Loop - Read File til EOF 
#  3. Update 'list_member' for Logical Delete/Remove (eg set status = R )
#      - set proper counts (eg good, bad, total)
#===============================================================================
sub process_file 
{
	my ($list_id, $email_addr, @email_array) ;
	my ($status, $email_exists, $sth1, $sth2 ) ;
	my ($file_name, $file_handle, $file_problem, $file_in, $line, @rest_of_line);
	my ($ary_len, $i);
	my $upload_dir_unix;

	# get upload subdir

	$sql = "select parmval from sysparm where parmkey = 'UPLOAD_DIR_UNIX'";
	$sth1 = $dbhq->prepare($sql) ;
	$sth1->execute();
	($upload_dir_unix) = $sth1->fetchrow_array();
	$sth1->finish();

	# deal with filename passed to this script

	if ( $upload_file =~ /([^\/\\]+)$/ ) 
	{
		$file_name = $1;                # set file_name to $1 var - (file-name no path)
		$file_name =~ s/^\.+//;         # say what...
		$file_name =~ s/\s/_/g;         # replace WhiteSpace with UnderScore global
		$file_handle = $upload_file ;
	}
	else 
	{
		$file_problem = $query->param('upfile');
		&error("Bad File Name: $file_problem, File name can't have a slash in it!\n Rename it and try again!" ) ;
		exit(0);
	}

	#---- Open file and save File to Unix box ---------------------------

	$file_in = "${upload_dir_unix}sub_uns.cgi.${user_id}" ;
	open(SAVED,">$file_in") || &util::logerror("Error - could NOT open Output SAVED file: $file_in");
    binmode($file_handle);
binmode(SAVED);
undef $BytesRead;
undef $Buffer;

while ($Bytes = read($file_handle,$Buffer,1024))
{
	print LOG "<$Buffer>\n";
    $BytesRead += $Bytes;
    print SAVED $Buffer;
}
	close SAVED;
	close($file_handle);

$confirm{$file_handle} = $BytesRead;
@temp_file = <CGItemp*>;
foreach $tmp_file (@temp_file)
{
    unlink ("$tmp_file");
}

	open(SAVED,"<$file_in") || &util::logerror("Error - could NOT open Input SAVED file: $file_in");

	#----- Loop Reading the File of Email Addrs - do til EOF ------------------
	open (LOG, "> /tmp/sub_uns_web.log");
	print LOG "<$upload_file>\n";
	while (<SAVED>) 
	{
		chomp;                       # remove Carriage Return (if exists)
		$line = $_;
		$line =~ s///g ;      # remove ^M from Email Addr (if exists)
		$line =~ s/\t/|/g ;
		$line =~ s/,/|/g ;
		($email_addr, @rest_of_line) = split('\|', $line) ;
		$email_addr =~ s/\s.*$// ;   # remove from 1st white space at end of addr thru end of line
		$email_addr =~ s/\s//g ;     # remove all white space global
		print LOG "<$email_addr>\n";
		
		&uns_upd_list_member($email_addr);
	} 

	close SAVED;
	close LOG;
	unlink($file_in) || &util::logerror("Error - could NOT Remove file: $file_in");  # del file_in

} # end of sub


#===============================================================================
# Sub: uns_upd_list_member
#===============================================================================
sub uns_upd_list_member
{
	my ($email_addr) = @_ ;
	my ($email_exists, $sth1, $sth2, $status, $i) ; 
	my $cnt;
	my $list_id;
	my $params;
	my $teid;

	
	# ---- See if Email Addr Already Exists for specified List -----

	if ($email_addr =~ /[^a-z0-9\@\_\.\-]/)
    {
		$tot_bad = $tot_bad + 1 ;
		return;
    }
	$email_exists = 0 ;
	if ($client_id > 0)
	{
		$sql = "select status,email_user_id,client_id from email_list where email_addr = '$email_addr' and client_id=$client_id"; 
	}
	else
	{
		$sql = "select status,email_user_id,client_id from email_list where email_addr = '$email_addr'"; 
	}
	$sth1 = $dbhq->prepare($sql) ;
	$sth1->execute();

	# ------- If Email Addr Does NOT Exist for the specified List then Add it ------ 

	$email_addr =~ s/\s//g ;   # remove ALL white space from EmailAddr
	$cnt = 0;
	while (($status,$email_user_id,$list_id) = $sth1->fetchrow_array())
	{
		$cnt++;
		if (( $status ne 'A' ) && ($status ne 'P'))
		{	
			# Record was Previously Removed 
			$tot_dup = $tot_dup + 1 ;
		}
		else
		{		
			# Update Existing list_member rec -- Reset Status, Subscribe and UnSubscribe fields
			my $errors = $unsubscribeInterface->unsubscribeEidAll( {'eID' => $email_user_id} );
			$tot_good = $tot_good + 1 ;
		}
	}
	$sth1->finish();
	if ($cnt == 0)
	{
		$tot_bad++;
	}
	if ($global eq "Y")
	{
		$sql = "select client_id,em.email_user_id from email_list em where email_addr = '$email_addr' and em.status='A'"; 
        $sth1 = $dbhq->prepare($sql) ;
        $sth1->execute();
        while (($tid,$teid) = $sth1->fetchrow_array())
        {
        	$sql = "insert into manual_removal(email_addr,removal_date,client_id) values('$email_addr',now(),$tid)";
            $rows = $dbhu->do($sql) ;
			my $errors=$unsubscribeInterface->logUnsubscribe( { 'emailAddress' => $email_addr, 'client_id' => $tid } );
        }
        $sth1->finish();
		util::addGlobal({ 'emailAddress' => $email_addr});
		my $errors = $unsubscribeInterface->unsubscribeAll( {'emailAddress' => $email_addr} );
		$errors = $unsubscribeInterface->unsubscribeUniques( {'emailAddress' => $email_addr} );
	}

} # end of sub


#===============================================================================
# Sub: print_uns_summary
#===============================================================================
sub print_uns_summary
{

	my ($list_cnt, $color);
	my ($sth1, $list_name) ;

util::header("UNSUBSCRIBE SUBSCRIBERS SUMMARY");    # Print HTML Header
#-----------------------------------------------------------
#  BEGIN HEADER FIX 
#-----------------------------------------------------------
print << "end_of_html";
</TD>
</TR>
<TR>
<TD vAlign=top align=left bgColor=#FFFFFF>

<TABLE cellSpacing=0 cellPadding=10 bgColor=#FFFFFF border=0>
<TBODY>
<TR>
<TD vAlign=top align=left bgColor=#FFFFFF colSpan=10>

<TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
<TBODY>
<TR>
<TD vAlign=center align=left><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=3>
<B>Confirmation</B></FONT></TD></TR>
</TBODY>
</TABLE>
end_of_html
#-----------------------------------------------------------
#  END HEADER FIX 
#-----------------------------------------------------------


	print << "end_of_html" ;
	
	<!--  BEGIN My Tbl Definition -------------------------------------------- -->
	<!--  Form Buttons - Images ---------------------------------------- -->

	<TABLE align="left" cellSpacing=0 cellPadding=0 width="100%" border=0>
	<TBODY>
	<TR bgColor=#509C10 height=15>
	<TD colspan="6" align=middle width="100%" height=15>
	<font face="Verdana,Arial,Helvetica,sans-serif" color="white" size="2">
	<b>Summary of Subscriber Emails Unsubscribed from Lists</b></font></TD>
	</TR>
	<TR bgColor=#509C10 height=15>
	<td colspan="6" >&nbsp;</td>
	</tr>

	<!-- bgColor = "#509C10" ;     # Dark Green     -->
	<!-- bgcolor = "#EBFAD1" ;     # Light Green    -->
	<!-- bgcolor = "$alt_light_table_bg" ;     # Light Yellow   -->

	<!-- Table Headings ----------------------------------------------------- -->
	<TR> 
		<TD bgcolor="#509C10" align="left" width="05%">&nbsp;</td>
		<TD bgcolor="#509C10" align="center" width="25%">	 
		<FONT face="verdana,arial,helvetica,sans serif" color=white size=2><b>List Name</b></font></td>
		<TD bgcolor="#509C10" align="right" width="15%"><FONT face="verdana,arial,helvetica,sans serif" color=white size=2>
			<b>Number<br>Unsubscribe</b></font></td>
		<TD bgcolor="#509C10" align="right" width="15%"><FONT face="verdana,arial,helvetica,sans serif" color=white size=2>
			<b>Number<br>Previously<br>Unsubscribed</b></font></td>
		<TD bgcolor="#509C10" align="right" width="15%"><FONT face="verdana,arial,helvetica,sans serif" color=white size=2>
			<b>Invalid<br>Unsubscribes</b></font></td>
		<TD bgcolor="#509C10" align="left" width="05%">&nbsp;</td>
	</TR> 
end_of_html

	print << "end_of_html" ;

	<!-- Table TOTAL Line(s) -------------------------------------------------- -->
	<TR> 
		<TD bgcolor="#EBFAD1" align="left"  width="5%" >&nbsp;</td>
		<TD bgcolor="#EBFAD1" align="left"  width="25%">&nbsp;</td>
		<TD bgcolor="#EBFAD1" align="right" width="15%">&nbsp;</td>
		<TD bgcolor="#EBFAD1" align="right" width="15%">&nbsp;</td>
		<TD bgcolor="#EBFAD1" align="right" width="15%">&nbsp;</td>
		<TD bgcolor="#EBFAD1" align="left"  width="5%" >&nbsp;</td>
	</TR> 
	<TR> 
		<TD bgcolor="#EBFAD1" align="left" width="5%">&nbsp;</td>
		<TD bgcolor="#EBFAD1" align="left" width="25%">	 
		<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2><b>Totals</b></FONT></TD> 
		<TD bgcolor="#EBFAD1" align="right" width="15%"><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2><b>$tot_good</b></td>
		<TD bgcolor="#EBFAD1" align="right" width="15%"><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2><b>$tot_dup</b></td>
		<TD bgcolor="#EBFAD1" align="right" width="15%"><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2><b>$tot_bad</b></td>
		<TD bgcolor="#EBFAD1" align="left" width="5%">&nbsp;</td>
	</TR> 

	<tr>
	<td colspan="6" bgcolor="#EBFAD1" align="center" width="100%"><br>
	<a href="/newcgi-bin/mainmenu.cgi"><INPUT type="image" src="$images/home_blkline.gif" hspace=7  width="72"  height="23" border=0></a>
	</td>
	</tr>

	</tbody>
	</table>

</td>
</tr>
<tr>
<td>
end_of_html

	util::footer();
}  
sub display
{
    my ($message, $displayValue)    = @_;

    print "\n" . '*' x 30 ."\n\n";
    print "$message: " . Dumper($displayValue) . "\n";
}
