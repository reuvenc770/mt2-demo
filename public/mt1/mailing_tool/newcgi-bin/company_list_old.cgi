#!/usr/bin/perl
#===============================================================================
# Purpose: Displays a List of Companies
# File   : company_list.cgi
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
my $username = $query->param('username');
my $mesg = $query->param('mesg');
my ($sth, $reccnt, $sql, $dbh ) ;
my $sth1;
my $sth1a;
my $sname;
my $images = $util->get_images_url;
my $alt_light_table_bg = $util->get_alt_light_table_bg;

# ------- connect to the util database ---------
my ($dbhq,$dbhu)=$util->get_dbh();

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
	$heading_text = "Date: $curdate" ;

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

	<TABLE cellSpacing=2 cellPadding=0 width=1200 bgColor=#ffffff border=0>
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
	my ($company_id, $company_name, $contact_name, $addr,$email_addr);
	my $website;
	my $cusername;
	my $cpassword;

	print << "end_of_html" ;
	<center><a href="company_disp.cgi?mode=A"><img src="/images/add.gif" border=0></a></center>
	<br>
	<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
	<TBODY>
	<TR bgColor="#509C10" height=15>
		<TD colspan="7" align=center width="100%" height=15>
		<font face="Verdana,Arial,Helvetica,sans-serif" color="white" size="3">
		<b>List of Current Companies</font>
		<font face="Verdana,Arial,Helvetica,sans-serif" color="white" size="2">
		(click company to edit the company)</b></font></TD>
	</TR>
	<TR> 
	<TD bgcolor="#EBFAD1" align="middle" width="05%">ID</td>
	<TD bgcolor="#EBFAD1" align="left" width="10%">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2> 
		<b>Company</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width="25%">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2>
		<b>Name</b></font></td>
	<TD bgcolor="#EBFAD1" align="left">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2>
		<b>Reporting<br>Website</b></font></td>
	<TD bgcolor="#EBFAD1" align="left">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2>
		<b>Username</b></font></td>
	<TD bgcolor="#EBFAD1" align="left">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2>
		<b>Password</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width="25%">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2>
		<b>Email Addr</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width="10%">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2>
		<b>&nbsp;</b></font></td>
	</TR> 

end_of_html

	#===========================================================================
	# Loop - Get ALL Lists that belong to the User
	#===========================================================================

	$sql = "select company_id,contact_company,contact_name,physical_addr,email_addr,contact_website,contact_username,contact_password from company_info_old where status='A' order by contact_company"; 
	$sth = $dbhq->prepare($sql) ;
	$sth->execute();
	$reccnt = 0 ;
	while (($company_id, $company_name, $contact_name, $addr,$email_addr,$website,$cusername,$cpassword) = $sth->fetchrow_array())
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
		print qq{	<TD align=middle>$company_id</td> \n} ;
        print qq{	<TD align=left><font color="#509C10" face="Arial" size="2"> \n } ;
		print qq{		<A HREF="company_disp.cgi?pmode=U&company_id=$company_id">$company_name</a></font></TD> \n } ;
        print qq{	<TD align=left><font color="black" face="Arial" size="2">$contact_name</font></TD> \n } ;
        print qq{	<TD align=left><font color="black" face="Arial" size="2">$website</font></TD> \n } ;
        print qq{	<TD align=left><font color="black" face="Arial" size="2">$cusername</font></TD> \n } ;
        print qq{	<TD align=left><font color="black" face="Arial" size="2">$cpassword</font></TD> \n } ;
        print qq{	<TD align=left><font color="black" face="Arial" size="2">$email_addr \n } ;
        print qq{	</font></TD> \n } ;
		print qq{ <td><a href="company_del.cgi?company_id=$company_id">Delete</a></td> };
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


