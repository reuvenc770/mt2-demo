#!/usr/bin/perl
#===============================================================================
# Purpose: Displays a List of 3rdparty mailers 
# File   : 3rdparty_list.cgi
#
#--Change Control---------------------------------------------------------------
#===============================================================================

#-----------------------
# include Perl Modules
#-----------------------
use strict;
use CGI;
use util;

#--------------------------------
# get some objects to use later
#--------------------------------
my $util = util->new;
my $query = CGI->new;
my $mesg = $query->param('mesg');
my ($sth, $reccnt, $sql) ;
my $sth1;
my $sth1a;
my $sname;
my $images = $util->get_images_url;
my $alt_light_table_bg = $util->get_alt_light_table_bg;

# ------- connect to the util database ---------
###$util->db_connect();

my ($dbhq,$dbhu)=$util->get_dbh();
###$dbh = $util->get_dbh;

# ------- check for login - if not logged in then Exit --------------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}

&disp_header();
if ( $mesg ne "" ) {
	#---------------------------------------------------------------------------
	# Display mesg (if present) from module that called this module.
	#---------------------------------------------------------------------------
	print qq{ <script language="JavaScript">  \n } ;
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

	$curdate = $util->date(0,2) ;
	$heading_text = "User: $username &nbsp;&nbsp;&nbsp;Date: $curdate" ;

	util::header($heading_text);    # Print HTML Header
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

	<TABLE cellSpacing=2 cellPadding=0 width=900 bgColor=#ffffff border=0>
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
<center><a href="/add_3rdparty_mailer.html"><img src="/images/add.gif" border=0></a>
</center>
	<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
	<TBODY>
	<TR bgColor="#509C10" height=15>
		<TD colspan="12" align=center width="100%" height=15>
		<font face="Verdana,Arial,Helvetica,sans-serif" color="white" size="3">
		<b>List of Current 3rd Party Mailers</font>
		<font face="Verdana,Arial,Helvetica,sans-serif" color="white" size="2">
		(click name to edit the mailer)</b></font></TD>
	</TR>
	<TR> 
	<TD bgcolor="#EBFAD1" align="middle" width="05%">ID</td>
	<TD bgcolor="#EBFAD1" align="left" width="10%">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2> 
		<b>Mailer</b></font></td>
	<TD bgcolor="#EBFAD1" align="middle"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>#<br>Subject</b></font></td>
	<TD bgcolor="#EBFAD1" align="middle"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>#<br>From</b></font></td>
	<TD bgcolor="#EBFAD1" align="middle"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>#<br>Creative</b></font></td>
	<TD bgcolor="#EBFAD1" align="middle"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>Name<br>Replace</b></font></td>
	<TD bgcolor="#EBFAD1" align="middle"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>Loc<br>Replace</b></font></td>
	<TD bgcolor="#EBFAD1" align="middle"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>Date<br>Replace</b></font></td>
	<TD bgcolor="#EBFAD1" align="middle"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>Email<br>Replace</b></font></td>
	<TD bgcolor="#EBFAD1" align="middle"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>CID<br>Replace</b></font></td>
	<TD bgcolor="#EBFAD1" align="middle"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>EMAIL_USER_ID<br>Replace</b></font></td>
	<TD bgcolor="#EBFAD1" align="middle"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>Include<br>Unsub</b></font></td>
	</TR> 

end_of_html

	$sql = "select third_party_id,mailer_name,num_subject,num_from,num_creative,name_replace,loc_replace,date_replace,email_replace,cid_replace,include_unsubscribe,emailid_replace from third_party_defaults where status='A' order by mailer_name"; 
	$sth = $dbhq->prepare($sql) ;
	$sth->execute();
	$reccnt = 0 ;
	my ($id,$mname,$num_subject,$num_from,$num_creative,$rname,$rloc,$rdate,$remail,$rcid,$unsub_flag,$remailid);
	while (($id,$mname,$num_subject,$num_from,$num_creative,$rname,$rloc,$rdate,$remail,$rcid,$unsub_flag,$remailid) = $sth->fetchrow_array())
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
		print qq{	<TD align=middle>$id</td> \n} ;
        print qq{	<TD align=left><font color="#509C10" face="Arial" size="2"> \n } ;
		print qq{		<A HREF="/cgi-bin/3rdparty_edit.cgi?id=$id"></font>\n } ;
        print qq{	<font color="black" face="Arial" size="2">$mname</font></a></TD> \n } ;
        print qq{	<TD align=middle><font color="black" face="Arial" size="2">$num_subject</font></TD> \n } ;
        print qq{	<TD align=middle><font color="black" face="Arial" size="2">$num_from</font></TD> \n } ;
        print qq{	<TD align=middle><font color="black" face="Arial" size="2">$num_creative</font></TD> \n } ;
        print qq{	<TD align=middle><font color="black" face="Arial" size="2">$rname</font></TD> \n } ;
        print qq{	<TD align=middle><font color="black" face="Arial" size="2">$rloc</font></TD> \n } ;
        print qq{	<TD align=middle><font color="black" face="Arial" size="2">$rdate</font></TD> \n } ;
        print qq{	<TD align=middle><font color="black" face="Arial" size="2">$remail</font></TD> \n } ;
        print qq{	<TD align=middle><font color="black" face="Arial" size="2">$rcid</font></TD> \n } ;
        print qq{	<TD align=middle><font color="black" face="Arial" size="2">$remailid</font></TD> \n } ;
        print qq{	<TD align=middle><font color="black" face="Arial" size="2">$unsub_flag</font></TD> \n } ;
		print qq{</TR> \n} ;

	}  # end while statement

	$sth->finish();

	print << "end_of_html" ;

	<TR><td align=center colspan=12><br>
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

	util::footer();
	$util->clean_up();
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
