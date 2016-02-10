#!/usr/bin/perl
#===============================================================================
# Purpose: Displays a List of Clients
# File   : client_list.cgi
#
# Input  :
#   1. mesg - If present - display mesg from list_upd.cgi or list_add.cgi.
#
# Output :
#   1. Display 2 Forms - 1. Adds New List Names, 2. Update(s) List values.
#   2. Pass control to 'list_add.cgi' to - Add NEW rec to 'list' table  or 
#   3. Pass control to 'list_upd.cgi' to - Update (1:M) 'list' recs
#
#--Change Control---------------------------------------------------------------
# Mike Baker, 8/16/01  Created.
#===============================================================================

#-----------------------
# include Perl Modules
#-----------------------
use strict;
use CGI;
use pma;

#--------------------------------
# get some objects to use later
#--------------------------------
my $pms = pma->new;
my $query = CGI->new;
my $username = $query->param('username');
my $mesg = $query->param('mesg');
my ($sth, $reccnt, $sql, $dbh ) ;
my $images = $pms->get_images_url;
my $alt_light_table_bg = $pms->get_alt_light_table_bg;

# ------- connect to the pms database ---------
$pms->db_connect();
$dbh = $pms->get_dbh;

# ------- check for login - if not logged in then Exit --------------
my $user_id = pma::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $pms->clean_up();
    exit(0);
}

&disp_header();
if ( $mesg ne "" ) {
	#---------------------------------------------------------------------------
	# Display mesg (if present) from module that called this module.
	#---------------------------------------------------------------------------
	print qq{ <script language="JavaScript">  \n } ;
	# print qq{ 	alert("The specified List Records have been SUCCESSFULLY updated!");  \n } ;
	print qq{ 	alert("$mesg");  \n } ;
	print qq{ </script>  \n } ;
}
&disp_body();
&write_java();
&disp_footer();
#------------------------
# End Main Logic
#------------------------



#===============================================================================
# Sub: disp_header - Header for PMS System (close bogus tbls to disp correctly)
#===============================================================================
sub disp_header
{
	my ($heading_text, $username, $curdate) ;

	$curdate = $pms->date(0,2) ;
	$heading_text = "User: $username &nbsp;&nbsp;&nbsp;Date: $curdate" ;

	pma::header($heading_text);    # Print HTML Header
	#-----------------------------------------------------------
	#  BEGIN HEADER FIX 
	#-----------------------------------------------------------
	print << "end_of_html";
	</TD>
	</TR>
	<TR>
	<TD vAlign=top align=left bgColor=#FFFFFF>
	<TABLE cellSpacing=0 cellPadding=0 bgColor=#FFFFFF border=0>
	<TBODY>
	<TR>
	<TD vAlign=top align=left bgColor=#FFFFFF colSpan=10>

	<TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
	<TBODY>
	<TR>
	<TD>&nbsp;</TD></TR>
	</TBODY>
	</TABLE>
end_of_html
	#-----------------------------------------------------------
	#  END HEADER FIX 
	#-----------------------------------------------------------

} # end sub disp_header



#===============================================================================
# Sub: disp_body
#===============================================================================
sub disp_body
{
	my ($puserid, $username, $fname, $lname, $city, $state, $zip, $status);
	my ($bgcolor) ;

	print << "end_of_html" ;

	<FORM name="list_upd_form" action="list_upd.cgi" method="post">
	<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
	<TBODY>
	<TR bgColor="#509C10" height=15>
		<TD colspan="5" align=center width="45%" height=15>
		<font face="Verdana,Arial,Helvetica,sans-serif" color="white" size="3">
		<b>List of Current Clients</font>
		<font face="Verdana,Arial,Helvetica,sans-serif" color="white" size="2">
		(click username to edit the client)</b></font></TD>
	</TR>
	<TR> 
	<TD bgcolor="#EBFAD1" align="left" width="02%">&nbsp;</td>
	<TD bgcolor="#EBFAD1" align="left" width="30%">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2> 
		<b>Client</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width="30%">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2>
		<b>FTP Directory</b></font></td>
	</TR> 

end_of_html

	#===========================================================================
	# Loop - Get ALL Lists that belong to the User
	#===========================================================================

	$sql = "select client_name,ftp_dir from tranzact_file_layout order by client_name"; 
	$sth = $dbh->prepare($sql) ;
	$sth->execute();
	$reccnt = 0 ;
	my $ftp_dir;
	while (($username, $ftp_dir) = $sth->fetchrow_array())
	{
		$reccnt++;
		if ( ($reccnt % 2) == 0 ) 
		{
			$bgcolor = "#EBFAD1" ;     # Light Green
		}
		else 
		{
			$bgcolor = "$alt_light_table_bg" ;     # Light Yellow
		}

		print qq{<TR bgColor=$bgcolor> \n} ;
		print qq{	<TD align=left width="02%">&nbsp;</td> \n} ;
        print qq{	<TD align=left width="30%"><font color="#509C10" face="Arial" size="2"> \n } ;
		print qq{		<A HREF="fl_disp.cgi?puserid=$username">$username</a></font></TD> \n } ;
        print qq{	<TD align=left width="30%"><font color="#509C10" face="Arial" size="2">$ftp_dir</font></TD> \n } ;
		print qq{</TR> \n} ;

	}  # end while statement

	$sth->finish();

	print << "end_of_html" ;

	<TR><td align=center colspan=5><br>
	<A HREF="client_disp.cgi?pmode=A">
	<IMG name="BtnHome" src="$images/add.gif" hspace=7  border="0"></A>
	<A HREF="mainmenu.cgi">
	<IMG name="BtnHome" src="$images/home_blkline.gif" hspace=7  border="0" width="72"  height="21" ></A>
	</td></tr>

	</tbody>
	</table>
	</FORM> 

end_of_html
} # end sub disp_body


#===============================================================================
# Sub: disp_footer
#===============================================================================
sub disp_footer
{
	print << "end_of_html";
</td>
</tr>
</tbody>
</table>
</td>
</tr>
<tr>
<td>
end_of_html

	pma::footer();
	$pms->clean_up();
} # end sub disp_footer



#===============================================================================
# Sub: write_java
#===============================================================================
sub write_java
{
	print << "end_of_html" ;

	<!-- ------------------- JAVA SCRIPT ----------------------------------- -->
    <script language="JavaScript">
	//------------------------------------------------------
	// Update the list table
	//------------------------------------------------------
    function Save()
    {
		confirm("Are you sure you want to Update the Lists?");
        document.list_upd_form.submit();
        return true;
    }

	//--------------------------------------------------------------------------
	// Check that 'list_name' is NOT Null if Add List being done.
	//--------------------------------------------------------------------------
    function ValidateAdd()
    {
    	if ( document.list_add_form.list_name.value == "" ) 
		{
    		alert("You MUST enter a List Name to add a New List!");
			document.list_add_form.list_name.focus();
    		return false ;
		}
		else
		{
			document.list_add_form.submit();
    		return true ;
		}
    }

	//--------------------------------------------------------------------------
	// Set the 'list_upd_form' field - chg_ind_X (where X = List ID) value to
	// list_id variable passed in.  This is done whenever a CHANGE is made to
	// the list_name or status fields (eg done to ID change fields for Update).
	//--------------------------------------------------------------------------
    function ChgInd(FieldName, ListId)
    {
    	var ObjName;
    	// ---Worked --> document.list_upd_form(FieldName).checked = true ;
    	document.list_upd_form(FieldName).value = ListId ;
    	// alert(document.list_upd_form(FieldName).value + " = " + ListId );
    	return true ;
    }

    </script>

end_of_html

} # end sub write_java
