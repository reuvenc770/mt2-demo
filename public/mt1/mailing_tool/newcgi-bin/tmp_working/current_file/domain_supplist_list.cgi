#!/usr/bin/perl
#===============================================================================
# Purpose: Displays a List of suppression Lists 
# File   : domain_supplist_list.cgi
#
#--Change Control---------------------------------------------------------------
# Jim Sobeck, 01/26/04  Created.
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
my ($sth, $reccnt, $sql, $dbh ) ;
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

	util::header("List of Domain Suppression Lists");    # Print HTML Header
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
	my ($puserid, $username, $name, $email_addr,$internal_email_addr,$physical_addr,$cstatus,$cnt); 
	my ($bgcolor) ;

	print << "end_of_html" ;

	<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
	<TBODY>
	<TR>
<td colspan=3 align=center><a href="/cgi-bin/mainmenu.cgi"><img src="/mail-images/home_blkline.gif" border=0></a></td>
<td colspan=3 align=center><a href="/cgi-bin/domain_supplist_add.cgi"><img src="/mail-images/add.gif" border=0></a></td></tr>
	<tr><td>&nbsp;</td></tr>
	<TR bgColor="#509C10" height=15>
		<TD colspan="4" align=center width="45%" height=15>
		<font face="Verdana,Arial,Helvetica,sans-serif" color="white" size="3">
		<b>List of Current Domain Suppression Lists</font><br>
		<font face="Verdana,Arial,Helvetica,sans-serif" color="white" size="2">
		(click name to add domains to a list)</b></font></TD>
	</TR>
	<TR> 
	<TD bgcolor="#EBFAD1" align="left" width="02%">&nbsp;</td>
	<TD bgcolor="#EBFAD1" align="left" width="5%">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2> 
		<b>ID</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width="55%">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2> 
		<b>Name</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width="15%">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2> 
		<b>List Cnt</b></font></td>
	</TR> 

end_of_html

	#===========================================================================
	# Loop - Get ALL Lists that belong to the User
	#===========================================================================

	$sql = "select list_id,list_name,list_cnt from vendor_domain_supp_list_info order by list_name"; 
	$sth = $dbh->prepare($sql) ;
	$sth->execute();
	$reccnt = 0 ;
	while (($puserid,$name,$cnt) = $sth->fetchrow_array())
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
		print qq{	<TD align=left>&nbsp;</td> \n} ;
        print qq{	<TD align=left><font color="#509C10" face="Arial" size="2"> \n } ;
		print qq{		$puserid</font></TD> \n } ;
		if ($puserid > 0)
		{
        	print qq{	<TD align=left><font color="#509C10" face="Arial" size="2"> \n } ;
			print qq{		<A HREF="/cgi-bin/domain_supplist_addnames.cgi?tid=$puserid">$name</a></font></TD> \n } ;
		}
		else
		{
        	print qq{	<TD align=left><font color="#509C10" face="Arial" size="2"> \n } ;
			print qq{		$name</font></TD> \n } ;
		}
		print qq{	<TD align=left>$cnt</td> \n} ;
		print qq{</TR> \n} ;

	}  # end while statement

	$sth->finish();

	print << "end_of_html" ;

	<TR><td align=center colspan=5><br>
	<A HREF="/cgi-bin/mainmenu.cgi">
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

