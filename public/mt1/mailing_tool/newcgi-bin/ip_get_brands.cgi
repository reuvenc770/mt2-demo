#!/usr/bin/perl
#===============================================================================
# Purpose: Displays a List of Strongmail brands 
# File   : ip_get_brands.cgi
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
my ($sth, $reccnt, $sql, $dbh ) ;
my $sth1;

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

	<TABLE cellSpacing=0 cellPadding=0 width=1100 bgColor=#ffffff border=0>
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
	my ($pid, $pname, $company, $day_flag);
	my $bid;
	my $bname;
	my $lname;

	print << "end_of_html" ;
	<center>
	<form method=post action="/cgi-bin/disp_brand_ips.cgi" target="bottom">
	Brand: <select name=brand_id>
end_of_html
	$sql="select brand_id,brand_name,user.first_name from client_brand_info,user where brand_type='3rd Party' and client_brand_info.status='A' and client_brand_info.third_party_id=10 and client_brand_info.client_id=user.user_id order by brand_name"; 
	my $sth1a=$dbhq->prepare($sql);
	$sth1a->execute();
	while (($bid,$bname,$lname)=$sth1a->fetchrow_array())
	{
		print "<option value=$bid>$bname ($bid - $lname)</option>\n";
	}
	$sth1a->finish();
	print "</select>&nbsp;&nbsp;";
	print << "end_of_html" ;
&nbsp;&nbsp;<input type=submit value="Go"></form><br>
	<A HREF="mainmenu.cgi" target=_top>
	<IMG name="BtnHome" src="$images/home_blkline.gif" hspace=7  border="0" width="72"  height="21" ></A>
	</td></tr>

	</tbody>
	</table>
	</center>

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


