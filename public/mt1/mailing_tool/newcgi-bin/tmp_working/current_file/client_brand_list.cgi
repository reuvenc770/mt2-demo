#!/usr/bin/perl
#===============================================================================
# Purpose: Displays a List of Brands for Clients
# File   : client_brand_list.cgi
#
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
my $cid = $query->param('cid');
my ($sth, $reccnt, $sql, $dbh ) ;
my $sth1;
my $images = $util->get_images_url;
my $alt_light_table_bg = $util->get_alt_light_table_bg;

# ------- connect to the util database ---------
$util->db_connect();
$dbh = $util->get_dbh;

# ------- check for login - if not logged in then Exit --------------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}

&disp_header();
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

	<TABLE cellSpacing=0 cellPadding=0 width=860 bgColor=#ffffff border=0>
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
	my ($bid,$bname,$ourl,$yurl,$o_imageurl,$y_imageurl);
	my ($bgcolor) ;
	my $ourl_str = "";
	my $yurl_str = "";
	my $o_imageurl_str = "";
	my $y_imageurl_str = "";

	print << "end_of_html" ;
	<center><a href="/cgi-bin/edit_client_brand.cgi?bid=0&cid=$cid&mode=A"><img src="/images/add.gif" border=0></a>&nbsp;&nbsp;&nbsp;<a href="/cgi-bin/brand_copy.cgi?cid=$cid&mode=A"><img src="/images/copy.gif" border=0></a><p>
	<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
	<TBODY>
	<TR bgColor="#509C10" height=15>
		<TD colspan="6" align=center height=15>
		<font face="Verdana,Arial,Helvetica,sans-serif" color="white" size="3">
		<b>List of Current Brands</font>
		<font face="Verdana,Arial,Helvetica,sans-serif" color="white" size="2">
		(click brand to edit)</b></font></TD>
	</TR>
	<TR> 
	<TD bgcolor="#EBFAD1" align="left" width="02%">&nbsp;</td>
	<TD bgcolor="#EBFAD1" align="left">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2> 
		<b>Brand</b></font></td>
	<TD bgcolor="#EBFAD1" align="left"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>Function</b></font></td>
	<TD bgcolor="#EBFAD1" align="left"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>Others Mailing URL</b></font></td>
	<TD bgcolor="#EBFAD1" align="left"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>Yahoo Mailing URL</b></font></td>
	<TD bgcolor="#EBFAD1" align="left"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>Others Image URL</b></font></td>
	<TD bgcolor="#EBFAD1" align="left"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>Yahoo Image URL</b></font></td>
	</TR> 

end_of_html

	#===========================================================================
	# Loop - Get ALL Lists that belong to the User
	#===========================================================================

	$sql = "select brand_id,brand_name from client_brand_info where client_id=$cid and status='A' order by brand_name"; 
	$sth = $dbh->prepare($sql) ;
	$sth->execute();
	$reccnt = 0 ;
	while (($bid,$bname) = $sth->fetchrow_array())
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

		$ourl_str = "";
		$yurl_str = "";
		$o_imageurl_str = "";
		$y_imageurl_str = "";
		my $temp_str = "";
		$sql="select url from brand_url_info where brand_id=$bid and url_type='O'"; 
		$sth1 = $dbh->prepare($sql) ;
		$sth1->execute();
		while (($temp_str) = $sth1->fetchrow_array())
		{
			$ourl_str = $ourl_str . $temp_str . "<br>";
		}
		$sth1->finish();
		my $temp_str = "";
		$sql="select url from brand_url_info where brand_id=$bid and url_type='Y'"; 
		$sth1 = $dbh->prepare($sql) ;
		$sth1->execute();
		while (($temp_str) = $sth1->fetchrow_array())
		{
			$yurl_str = $yurl_str . $temp_str . "<br>";
		}
		$sth1->finish();
		my $temp_str = "";
		$sql="select url from brand_url_info where brand_id=$bid and url_type='OI'"; 
		$sth1 = $dbh->prepare($sql) ;
		$sth1->execute();
		while (($temp_str) = $sth1->fetchrow_array())
		{
			$o_imageurl_str = $o_imageurl_str . $temp_str . "<br>";
		}
		$sth1->finish();
		my $temp_str = "";
		$sql="select url from brand_url_info where brand_id=$bid and url_type='YI'"; 
		$sth1 = $dbh->prepare($sql) ;
		$sth1->execute();
		while (($temp_str) = $sth1->fetchrow_array())
		{
			$y_imageurl_str = $y_imageurl_str . $temp_str . "<br>";
		}
		$sth1->finish();
		print qq{<TR bgColor=$bgcolor> \n} ;
		print qq{	<TD align=left width="02%">&nbsp;</td> \n} ;
        print qq{	<TD align=left width="10%"><font color="#509C10" face="Arial" size="2"> \n } ;
		print qq{		<A HREF="edit_client_brand.cgi?bid=$bid&cid=$cid&mode=U">$bname</a></font></TD> \n } ;
		print "<td><A HREF=\"del_client_brand.cgi?bid=$bid\">Delete</a></td>\n";
        print qq{	<TD align=left"><font color="#509C10" face="Arial" size="2"><b>$ourl_str</b></font></TD> \n } ;
        print qq{	<TD align=left"><font color="#509C10" face="Arial" size="2"><b>$yurl_str</b></font></TD> \n } ;
        print qq{	<TD align=left"><font color="#509C10" face="Arial" size="2"><b>$o_imageurl_str</b></font></TD> \n } ;
        print qq{	<TD align=left"><font color="#509C10" face="Arial" size="2"><b>$y_imageurl_str</b></font></TD> \n } ;
		print qq{</TR> \n} ;

	}  # end while statement

	$sth->finish();

	print << "end_of_html" ;

	<TR><td align=center colspan=5><br>
	<A HREF="/cgi-bin/client_list.cgi">
	<IMG name="BtnHome" src="$images/home_blkline.gif" hspace=7  border="0" width="72"  height="21" ></A>
	</td></tr>

	</tbody>
	</table>

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
