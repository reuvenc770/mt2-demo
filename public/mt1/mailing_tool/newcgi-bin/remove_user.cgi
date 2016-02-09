#!/usr/bin/perl
#===============================================================================
# File   : remove_user.cgi
#
#--Change Control---------------------------------------------------------------
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
my $tot_good;
my $tot_bad;
my $tot_dup;
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
my $table="email_list";
my $eid = $query->param('eid');
my $email = $query->param('emailAddress');
my $suppressionReasonCode = $query->param('suppressionReasonCode');

$eid=~s/I//;
$eid=~s/S//;
my $global = $query->param('global');

# ----- connect to the util database -------
my ($dbhq,$dbhu)=$util->get_dbh();

# ----- check for login -------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}
my $unsubscribeInterface=Lib::Database::Perl::Interface::Unsubscribe->new();

&uns_upd_list_member($email);

#---- Print Delete Statistics -----------------
&print_uns_summary();


$util->clean_up();
exit(0) ;

#===============================================================================
# Sub: uns_upd_list_member
#===============================================================================
sub uns_upd_list_member
{
	my ($email) = @_ ;
	my ($email_exists, $sth1, $sth2, $status, $i) ; 
	my $email_addr;
	my $tid;
	my $list_id;
	my $teid;
	my $params;
	my $tlist_id;
	my $itable;
	
	# ---- See if Email Addr Already Exists for specified List -----
	$email_exists = 0 ;
	$sql = qq|select email_addr,status,email_user_id,client_id from email_list where email_addr = "$email"|; 
	$sth1 = $dbhq->prepare($sql) ;
	$sth1->execute();
	($email_addr,$status,$email_user_id,$list_id) = $sth1->fetchrow_array();
	$sth1->finish;

	if ($email_addr) 
	{
		$sth1->finish();
		# Update Existing list_member rec -- Reset Status, Subscribe and UnSubscribe fields
		if (($global eq "Y") or ($global eq "D"))
		{
			$sql="select client_id,em.email_user_id from email_list em where em.status='A' and em.email_addr=?"; 
			$sth1 = $dbhq->prepare($sql) ;
			$sth1->execute($email_addr);
			while (($tid,$teid) = $sth1->fetchrow_array())
			{
				$sql = "insert into manual_removal(email_addr,removal_date,client_id) values('$email_addr',now(),$tid)";
				$rows = $dbhu->do($sql) ;
				my $errors=$unsubscribeInterface->logUnsubscribe( { 'client_id' => $tid, 'emailAddress' => $email_addr } );
			}
			$sth1->finish();
			my $errors=$unsubscribeInterface->unsubscribeAll( { 'emailAddress' => $email_addr } );
			$errors=$unsubscribeInterface->unsubscribeUniques( { 'emailAddress' => $email_addr } );
			util::addGlobal( { 'emailAddress' => $email_addr, 'suppressionReasonCode' =>  $suppressionReasonCode } );
		}
		if ( $status ne 'A' ) 
		{	
		# Record was Previously Removed 
			$tot_dup = $tot_dup + 1 ;
		}
		else
		{		
			my $errors=$unsubscribeInterface->unsubscribe( { 'unsubscribeTable' => $itable, 'emailAddress' => $email_addr } );
			$tot_good = $tot_good + 1 ;
		}
		
		## update UniqueEmailList with unsub date
		util::updateUniqueEmailListUnsub($email_addr);
	}
	
	## add them to suppression anyway even they are not in email_list they may be archived
	else
	{
		util::addGlobal( { 'emailAddress' => $email, 'suppressionReasonCode' =>  $suppressionReasonCode } );
		$tot_good = $tot_good + 1 ;
		#$tot_bad = $tot_bad + 1 ;
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
