#!/usr/bin/perl
#===============================================================================
# Purpose: Displays a List of Footer Content
# File   : footer_content_list.cgi
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
my $mesg = $query->param('mesg');
my ($sth, $reccnt, $sql, $dbh ) ;
my $sth1;
my ($content_id,$content_name,$content_date,$inactive_date,$content_html);

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
	$heading_text = "&nbsp;&nbsp;&nbsp;Date: $curdate" ;

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

	<TABLE cellSpacing=0 cellPadding=0 width=900 bgColor=#ffffff border=0>
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
	my ($vid, $name, $privacy_text, $unsub_text);
	my $send_str;

	print << "end_of_html" ;
	<center><a href="/cgi-bin/footer_content.cgi"><img src="/mail-images/add.gif" border=0></a><br><br>
	<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
	<TBODY>
	<TR bgColor="#509C10" height=15>
		<TD colspan="6" align=center height=15>
		<font face="Verdana,Arial,Helvetica,sans-serif" color="white" size="3">
		<b>List of Current Footer Contents</font>
		<font face="Verdana,Arial,Helvetica,sans-serif" color="white" size="2">
		(click name to edit the footer)</b></font></TD>
	</TR>
	<TR> 
	<TD bgcolor="#EBFAD1" align="left" width="02%">&nbsp;</td>
	<TD bgcolor="#EBFAD1" align="left" width="10%">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2> 
		<b>Name</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width=10%><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>Date</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width="10%"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>Inactive Date</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width="60%"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>Categories</b></font></td>
	<TD bgcolor="#EBFAD1" align="center" width=8%><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2></font></td>
	</TR> 

end_of_html

	#===========================================================================
	# Loop - Get footer variations 
	#===========================================================================

	$sql = "select content_id,content_name,content_date,inactive_date,content_html from footer_content"; 
	$sth = $dbh->prepare($sql) ;
	$sth->execute();
	$reccnt = 0 ;
	while (($content_id,$content_name,$content_date,$inactive_date,$content_html) = $sth->fetchrow_array())
	{
		if ($inactive_date eq "0000-00-00")
		{
			$inactive_date="";
		}
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
		print qq{		<A HREF="footer_content.cgi?cid=$content_id">$content_name</a></font></TD> \n } ;
        print qq{	<TD align=left><font color="#509C10" face="Arial" size="2">$content_date</font></TD> \n } ;
        print qq{	<TD align=left><font color="#509C10" face="Arial" size="2">$inactive_date</font></TD> \n } ;
		$sql="select category_name from category_info,content_category where category_info.category_id=content_category.category_id and content_id=$content_id";
		my $sth1;
		$sth1=$dbh->prepare($sql);
		$sth1->execute();
		my $cat_str="";
		my $temp_name;
		while (($temp_name) = $sth1->fetchrow_array())
		{
			$cat_str = $cat_str . $temp_name . ",";
		}
		$sth1->finish();
		$_ = $cat_str;
		chop;
		$cat_str = $_;
		print qq{ <td>$cat_str</td> };
        print qq{	<TD align=left><font color="#509C10" face="Arial" size="2"><a href="/cgi-bin/footer_content_del.cgi?cid=$content_id">Delete</a>&nbsp;&nbsp;<a href="/cgi-bin/footer_content_preview.cgi?cid=$content_id" target=_blank>Preview</a>&nbsp;&nbsp;<a href="/cgi-bin/footer_content_www_spam.cgi?cid=$content_id">Run Spam</a></font></TD> \n } ;
		print qq{</TR> \n} ;

	}  # end while statement

	$sth->finish();

	print << "end_of_html" ;

	<TR><td align=center colspan=6><br>
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


