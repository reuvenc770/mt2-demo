#!/usr/bin/perl
#===============================================================================
#
#--Change Control---------------------------------------------------------------
#===============================================================================

#-----  include Perl Modules ---------
use strict;
use CGI;
use util;
use Lib::Database::Perl::Interface::Unsubscribe;

#------  get some objects to use later ---------
my $util = util->new;
my $query = CGI->new;
my $sth;
my $sth1;
my $sql;
my $dbh;
my $errmsg;
my ($status, $max_names, $max_mailings, $username);
my $password;
my $cstatus;
my $rows;
my $list_name;
my $etype;
my $company;
my $eid;
my ($puserid, $pmesg);
my $images = $util->get_images_url;
my $name;
my $pmode;
my $em;
my $reccnt;
my $tot_good=0;
my $tot_bad=0;
my $tot_dup=0;
my $table;

#------  connect to the util database -----------

my ($dbhq,$dbhu)=$util->get_dbh();

#-----  check for login  ------

my $user_id = util::check_security();
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
	$util->clean_up();
    exit(0);
}
my $suppfile = $query->param('suppfile');
my $suppressionReasonCode = $query->param('suppressionReasonCode');
my $cemail = $query->param('name');
my $unsubscribeInterface=Lib::Database::Perl::Interface::Unsubscribe->new();
if ($suppfile ne "")
{
	process_file($suppressionReasonCode);
}
else
{
	$cemail =~ s/[\n\r\f\t]/\|/g ;
	$cemail =~ s/\|{2,999}/\|/g ;
	my @em_array = split '\|', $cemail;


	foreach my $em(@em_array)
	{
		&uns_upd_list_member($em, $suppressionReasonCode);
	}
}

#---- Print Delete Statistics -----------------
&print_uns_summary();


$util->clean_up();
exit(0) ;

sub process_file
{
	my ($suppressionReasonCode)=@_;
	my ($file_name, $file_handle, $file_problem, $file_in, $line, @rest_of_line);
	my $upload_dir_unix;
	my $em;
	my ($BytesRead, $Buffer, $Bytes ) ;
	my (@temp_file, %confirm);
	my $tmp_file;

	# get upload subdir

	$sql = "select parmval from sysparm where parmkey = 'UPLOAD_DIR_UNIX'";
	$sth1 = $dbhq->prepare($sql) ;
	$sth1->execute();
	($upload_dir_unix) = $sth1->fetchrow_array();
	$sth1->finish();

	# deal with filename passed to this script

	if ( $suppfile =~ /([^\/\\]+)$/ ) 
	{
		$file_name = $1;                # set file_name to $1 var - (file-name no path)
		$file_name =~ s/^\.+//;         # say what...
		$file_name =~ s/\s/_/g;         # replace WhiteSpace with UnderScore global
		$file_handle = $suppfile;
	}
	else 
	{
		$file_problem = $query->param('suppfile');
		&error("Bad File Name: $file_problem, File name can't have a slash in it!\n Rename it and try again!" ) ;
		exit(0);
	}

	#---- Open file and save File to Unix box ---------------------------
	$file_in = "${upload_dir_unix}suppfile.${user_id}" ;
	open(SAVED,">$file_in") || &util::logerror("Error - could NOT open Output SAVED file: $file_in");
    binmode($file_handle);
	binmode(SAVED);
	undef $BytesRead;
	undef $Buffer;

	while ($Bytes = read($file_handle,$Buffer,1024))
	{
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
	while (<SAVED>) 
	{
		chomp;
		$em=$_;
		$em =~ s///g;
        if ($em=~ /[^a-z0-9\@\_\.\-]/)
        {
			$tot_bad++;
			next;
		}
		&uns_upd_list_member($em, $suppressionReasonCode);
	}
	close(SAVED);
}
#===============================================================================
# Sub: uns_upd_list_member
#===============================================================================
sub uns_upd_list_member
{
	my ($em, $suppressionReasonCode) = @_ ;
	my ($email_exists, $sth1, $sth2, $status, $i) ; 
	my $email_addr;
	my $tid;
	my $list_id;
	my $teid;
	my $params;
	my $tlist_id;
	
	# ---- See if Email Addr Already Exists for specified List -----
	$email_exists = 0 ;
	$sql="select client_id,em.email_user_id,em.status from email_list em where em.email_addr=?"; 
	$sth1 = $dbhq->prepare($sql) ;
	$sth1->execute($em);
	my $got_record=0;
	while (($tid,$teid,$status) = $sth1->fetchrow_array())
	{
		if ( $status ne 'A' ) 
		{	
			$got_record=1;
			$tot_dup = $tot_dup + 1 ;
		}
		else
		{		
			$got_record=2;
			$tot_good = $tot_good + 1 ;
			$sql = "insert into manual_removal(email_addr,removal_date,client_id) values('$em',now(),$tid)";
			$rows = $dbhu->do($sql) ;
			my $errors=$unsubscribeInterface->logUnsubscribe( { 'client_id' => $tid, 'emailAddress' => $em} );
		}
	}
	$sth1->finish();
	my $errors=$unsubscribeInterface->unsubscribeAll( { 'emailAddress' => $em} );
	$errors=$unsubscribeInterface->unsubscribeUniques( { 'emailAddress' => $em} );
	if ($got_record == 2)
	{
	}
	elsif ($got_record == 0)
	{
		$tot_good = $tot_good + 1 ;
	}
	util::addGlobal( { 'emailAddress' => $em, 'suppressionReasonCode' =>  $suppressionReasonCode } );
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
	<FORM name="sub_uns_mesg_form" action=mainmenu.cgi method=post>

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
	<INPUT type="image" src="$images/home_blkline.gif" hspace=7  width="72"  height="23" border=0>
	</td>
	</tr>

	</tbody>
	</table>
	</FORM> 

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
