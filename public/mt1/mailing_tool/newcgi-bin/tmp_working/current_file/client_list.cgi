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
use util;

#--------------------------------
# get some objects to use later
#--------------------------------
my $util = util->new;
my $query = CGI->new;
my $username = $query->param('username');
my $mesg = $query->param('mesg');
my ($sth, $reccnt, $sql, $dbh ) ;
my $sth1;
my $sth1a;
my $sname;
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
<center><a href="/client_schedule.html"><FONT face="verdana,arial,helvetica,sans serif" color=#000000 size=2>Clients Schedule</FONT></a>
</center>
	<FORM name="list_upd_form" action="list_upd.cgi" method="post">
	<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
	<TBODY>
	<TR bgColor="#509C10" height=15>
		<TD colspan="7" align=center width="100%" height=15>
		<font face="Verdana,Arial,Helvetica,sans-serif" color="white" size="3">
		<b>List of Current Clients</font>
		<font face="Verdana,Arial,Helvetica,sans-serif" color="white" size="2">
		(click username to edit the client)</b></font></TD>
	</TR>
	<TR> 
	<TD bgcolor="#EBFAD1" align="middle" width="05%">ID</td>
	<TD bgcolor="#EBFAD1" align="left" width="10%">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2> 
		<b>Username</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width="25%">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2>
		<b>Name (Last, First)</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width="20%">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2>
		<b>City, State, Zip</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width="25%">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2>
		<b>Brands/Hosts</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width="10%">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2>
		<b>&nbsp;</b></font></td>
	</TR> 

end_of_html

	#===========================================================================
	# Loop - Get ALL Lists that belong to the User
	#===========================================================================

	$sql = "select user_id, username, last_name, first_name, city, state, zip, status from user where status='A' order by user_id";
	$sth = $dbh->prepare($sql) ;
	$sth->execute();
	$reccnt = 0 ;
	while (($puserid, $username, $lname, $fname, $city, $state, 
		$zip, $status) = $sth->fetchrow_array())
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

		if ( $status eq "D" )
		{
			$status = "Deleted" ;
		}
		else
		{
			$status = "Active" ;
		}

		print qq{<TR bgColor=$bgcolor> \n} ;
		print qq{	<TD align=middle>$puserid</td> \n} ;
        print qq{	<TD align=left><font color="#509C10" face="Arial" size="2"> \n } ;
		print qq{		<A HREF="client_disp.cgi?pmode=U&puserid=$puserid">$username</a></font></TD> \n } ;
        print qq{	<TD align=left><font color="black" face="Arial" size="2">$lname, $fname</font></TD> \n } ;
        print qq{	<TD align=left><font color="black" face="Arial" size="2">$city, $state, $zip</font></TD> \n } ;
        print qq{	<TD align=left><font color="black" face="Arial" size="2"> \n } ;
my $sth1;
		$sql = "select brand_id,brand_name from client_brand_info where client_id=$puserid and status='A' order by brand_name"; 
		$sth1 = $dbh->prepare($sql) ;
		$sth1->execute();
		my $bname;
		my $bid;
		while (($bid,$bname) = $sth1->fetchrow_array())
		{
			print "<a href=\"/cgi-bin/edit_client_brand.cgi?bid=$bid&cid=$puserid&mode=U\"><b>$bname</b></a>";
			$sql = "select server_name from brand_host where brand_id=$bid and server_type='O' order by server_name";
			$sth1a = $dbh->prepare($sql) ;
			$sth1a->execute();
			print "&nbsp;(";
			my $temp_str="";
			while (($sname) = $sth1a->fetchrow_array())
			{
				$temp_str = $temp_str . $sname. ",";	
			}
			$_ = $temp_str;
			chop;
			$temp_str = $_;
			print "$temp_str";
			$sth1a->finish();
			$sql = "select server_name from brand_host where brand_id=$bid and server_type='Y' order by server_name";
			$sth1a = $dbh->prepare($sql) ;
			$sth1a->execute();
			print "&nbsp; - ";
			$temp_str="";
			while (($sname) = $sth1a->fetchrow_array())
			{
				$temp_str = $temp_str . $sname. ",";	
			}
			$_ = $temp_str;
			chop;
			$temp_str = $_;
			print "$temp_str";
			$sql = "select server_name from brand_host where brand_id=$bid and server_type='H' order by server_name";
			$sth1a = $dbh->prepare($sql) ;
			$sth1a->execute();
			print "&nbsp; - ";
			$temp_str="";
			while (($sname) = $sth1a->fetchrow_array())
			{
				$temp_str = $temp_str . $sname. ",";	
			}
			$_ = $temp_str;
			chop;
			$temp_str = $_;
			print "$temp_str";
			$sql = "select server_name from brand_host where brand_id=$bid and server_type='A' order by server_name";
			$sth1a = $dbh->prepare($sql) ;
			$sth1a->execute();
			print "&nbsp; - ";
			$temp_str="";
			while (($sname) = $sth1a->fetchrow_array())
			{
				$temp_str = $temp_str . $sname. ",";	
			}
			$_ = $temp_str;
			chop;
			$temp_str = $_; 
			print "$temp_str";
			print ")<br>";
		}
		$sth1->finish();
        print qq{	</font></TD> \n } ;
        print qq{	<TD align=center><font color="#509C10" face="Arial" size="2"><a href="/cgi-bin/client_brand_list.cgi?cid=$puserid">Brands</a></font></TD> \n } ;
		print qq{</TR> \n} ;

	}  # end while statement

	$sth->finish();

	print << "end_of_html" ;

	<TR><td align=center colspan=5><br>
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
