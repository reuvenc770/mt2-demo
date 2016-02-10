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
my $manager_id;
my $affiliate_id;
my $passcard;
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
	<TABLE width=100% cellSpacing=0 cellPadding=0 bgColor=#FFFFFF border=0>
	<TBODY>
	<TR>
	<TD vAlign=top align=left bgColor=#FFFFFF colSpan=10>

	<TABLE cellSpacing=2 cellPadding=0 width=100% bgColor=#ffffff border=0>
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
	my $notes;

	print << "end_of_html" ;
	<center><a href="company_disp.cgi?mode=A"><img src="/images/add.gif" border=0></a></center>
	<br>
	<TABLE cellSpacing=2 cellPadding=0 width="100%" border=0>
	<TBODY>
	<TR bgColor="#509C10" height=15>
		<TD colspan="8" align=center width="100%" height=15>
		<font face="Verdana,Arial,Helvetica,sans-serif" color="white" size="3">
		<b>List of Current Companies</font>
		<font face="Verdana,Arial,Helvetica,sans-serif" color="white" size="2">
		(click company to edit the company)</b></font></TD>
	</TR>
	<TR> 
	<TD bgcolor="#EBFAD1" align="left" width="15%"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2> <b>Company</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width="20%"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2> <b>Campaign<br>Manager</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width="20%"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2> <b>Affiliate<br>Platform</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width="20%"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2> <b>Passcard</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width="20%"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2> <b>Websites</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width="20%"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2> <b>Contacts</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width="15%"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>Physical Address</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width="15%"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>Notes</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width="10%">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2>
		<b>&nbsp;</b></font></td>
	</TR> 

end_of_html

	#===========================================================================
	# Loop - Get ALL Lists that belong to the User
	#===========================================================================

	$sql = "select company_id,company_name,physical_addr,contact_notes,manager_id,affiliate_id,passcard from company_info where status='A' order by company_name"; 
	$sth = $dbhq->prepare($sql) ;
	$sth->execute();
	$reccnt = 0 ;
	while (($company_id, $company_name, $addr,$notes,$manager_id,$affiliate_id,$passcard) = $sth->fetchrow_array())
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

		my $manager_name;
		$sql="select manager_name from CampaignManager where manager_id=?";
		$sth1=$dbhq->prepare($sql);
		$sth1->execute($manager_id);
		($manager_name)=$sth1->fetchrow_array();
		$sth1->finish();
		my $affiliate_name;
		$sql="select name from AffiliatePlatform where affiliate_id=?";
		$sth1=$dbhq->prepare($sql);
		$sth1->execute($affiliate_id);
		($affiliate_name)=$sth1->fetchrow_array();
		$sth1->finish();

		my $website;
		my $username;
		my $password;
		my $wstr="";
		$sql="select website,username,password from company_info_website where company_id=? order by website";
		$sth1=$dbhq->prepare($sql);
		$sth1->execute($company_id);
		while (($website,$username,$password)=$sth1->fetchrow_array())
		{
			$wstr=$wstr.$website." - ".$username."/".$password."<br>";
		}
		$sth1->finish();

		my $name;
		my $phone;
		my $email;
		my $aim;
		my $cstr="";
		$sql="select contact_name,contact_phone,contact_email,contact_aim from company_info_contact where company_id=? order by contact_name";
		$sth1=$dbhq->prepare($sql);
		$sth1->execute($company_id);
		while (($name,$phone,$email,$aim)=$sth1->fetchrow_array())
		{
			$cstr=$cstr.$name." - ".$email."<br>".$phone." - ".$aim."<br>";
		}
		$sth1->finish();
		print qq{<TR bgColor=$bgcolor> \n} ;
        print qq{	<TD align=left><font color="#509C10" face="Arial" size="2"> \n } ;
		print qq{		<A HREF="company_disp.cgi?pmode=U&company_id=$company_id">$company_name</a></font></TD> \n } ;
        print qq{	<TD align=left><font color="black" face="Arial" size="2">$manager_name</font></TD> \n } ;
        print qq{	<TD align=left><font color="black" face="Arial" size="2">$affiliate_name</font></TD> \n } ;
        print qq{	<TD align=left><font color="black" face="Arial" size="2">$passcard</font></TD> \n } ;
        print qq{	<TD align=left><font color="black" face="Arial" size="2">$wstr</font></TD> \n } ;
        print qq{	<TD align=left><font color="black" face="Arial" size="2">$cstr</font></TD> \n } ;
        print qq{	<TD align=left><font color="black" face="Arial" size="2">$addr</font></TD> \n } ;
        print qq{	<TD align=left><font color="black" face="Arial" size="2">$notes</font></TD> \n } ;
        print qq{	</font></TD> \n } ;
		print qq{ <td><a href="company_contact.cgi?company_id=$company_id">Contacts</a>&nbsp;&nbsp;<a href="company_website.cgi?company_id=$company_id">Website(s)</a><br><a href="company_website.cgi?company_id=$company_id">Tracking</a>&nbsp;&nbsp;<a href="company_del.cgi?company_id=$company_id">Delete</a></td> };
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


