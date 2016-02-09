#!/usr/bin/perl
#===============================================================================
# Purpose: Displays the HTML page to EXPORT list_member recs.
# File   : sub_disp_exp.cgi
#
#--Change Control---------------------------------------------------------------
# Mike Baker, 8/03/01  Created.
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
my $images = $util->get_images_url;

&disp_util_header();
&disp_sub_exp_body();
&disp_util_footer();

#-------------------
# End Main Logic
#-------------------



#===============================================================================
# Sub: disp_util_header
#===============================================================================
sub disp_util_header
{

	util::header("EXPORT SUBSCRIBERS");    # Print HTML Header

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
	&nbsp;</FONT></TD></TR>
	</TBODY>
	</TABLE>
end_of_html
	#-----------------------------------------------------------
	#  END HEADER FIX 
	#-----------------------------------------------------------

} # end sub disp_util_header



#===============================================================================
# Sub: disp_sub_exp_body
#===============================================================================
sub disp_sub_exp_body
{
	my ($dbh, $sth, $sql, $list_id, $list_name ) ;

	# ----- connect to the util database -------
	$util->db_connect();
	$dbh = $util->get_dbh;

	# ----- check for login -------------------
	my $user_id = util::check_security();
	if ($user_id == 0)
	{
   	 	print "Location: notloggedin.cgi\n\n";
	    $util->clean_up();
	    exit(0);
	}

	print << "end_of_html" ;

	<!-- tbl-06 Body-01 Tbl (Entire Body less Header and Footer ) -->
	<TABLE cellSpacing=0 cellPadding=10 bgColor=#999999 border=0>
	<TBODY>
	<TR>
	<TD vAlign=top align=left bgColor=#ffffff colSpan=10><!-- doing ct-tbl-open -->

	<!-- tbl-07 Body-02 ( 1st Title (eg Show Subscriber List) -->
	<TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
	<TBODY>
	<TR><!-- doing ct-tbl-cell-open -->
	<TD vAlign=center align=left><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=3>
	<B>Export Subscriber List</B> </FONT>
	</TD></TR>
	<TR>
	<TD><IMG height=3 src="$images/spacer.gif"></TD></TR></TBODY>
	</TABLE>

	<TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
	<TBODY>
	<TR>
	<TD><font face="verdana,arial,helvetica,sans serif" color="#509C10" size="2">
		This system can produce a list of subscriber email addresses. Select the 
		appropriate interest category or the <B>All Subscribers</B> option and then 
		click on the <B>View Subscribers</B> button.  The list of email addresses 
		will be displayed in a separate window.<BR></font></TD></TR>
	<TR>
	<TD><IMG height=5 src="$images/spacer.gif"></TD>
	</TR>
	</TBODY>
	</TABLE>

	<TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
	<TBODY>
	<TR>
	<TD><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
	To write the list of email addresses to a file, select the appropriate <B>Export</B> button. 
	For a spreadsheet or database package, use <B>Export as CSV</B>. For a word processing package 
	or a text editor, use <B>Export as Text</B>.<BR></FONT></TD></TR>
	<TR>
	<TD><IMG height=5 src="$images/spacer.gif"></TD></TR></TBODY>
	</TABLE>

	<FORM name="sub_disp_exp_form" action="fl_sub_exp.cgi" method="post">

	<TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>

	<TBODY>
	<TR>
	<TD>

	<!-- tbl-11 Body-06 ( 'Select a Listing' graphic (drop down-Home Button) ) -->
	<TABLE cellSpacing=0 cellPadding=5 width="100%" border=0>
	<TBODY>
	<TR>
	<TD align=middle>

	<!-- tbl-12 Body-07 ( Redefine -> 'Select a Listing' graphic (drop down-Home Button) ) -->
	<TABLE cellSpacing=0 cellPadding=0 width=400 bgColor=#E3FAD1 border=0>
	<TBODY>
	<TR align=top bgColor=#509C10 height=18>
	<TD vAlign=top align=left height=15><IMG src="$images/blue_tl.gif" border=0 width="7" height="7"></TD>
	<TD height=15><IMG height=1 src="$images/spacer.gif" width=3 border=0></TD>
	<TD align=middle height=15>

	<!-- tbl-13 Body-07 ( 'Select a Listing' - Heading 'Select a Listing' ) -->
	<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
	<TBODY>
	<TR bgColor=#509C10 height=15>
	<TD align=middle width="100%" height=15><FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=2>
	<B>Select a listing option</B> 
	</FONT></TD></TR></TBODY>
	</TABLE>
	<!-- tbl-13 end -->

	</TD>
	<TD height=15><IMG height=1 src="$images/spacer.gif" width=3 border=0></TD>
	<TD vAlign=top align=right bgColor=#509C10 height=15>
	<IMG src="$images/blue_tr.gif" border=0 width="7" height="7"></TD></TR>
	<TR bgColor=#E3FAD1>
	<TD colSpan=5><IMG height=3 src="$images/spacer.gif" width=1 border=0></TD></TR>
	<TR bgColor=#E3FAD1>
	<TD><IMG height=3 src="$images/spacer.gif" width=3></TD> 
	<TD align=middle><IMG height=3 src="$images/spacer.gif" width=3></TD>
	<TD align=middle>

	<!-- tbl-14 Body-08 ( Select a Listing - Encompass Drop List and Buttons ) -->
	<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
	<TBODY>
	<TR>
	<TD align=middle><IMG height=3 src="$images/spacer.gif" width=3></TD></TR>
	<TR>
	<TD height=8>&nbsp;</TD></TR>
	<TR align=middle><!-- doing ct-tbl-cell-open -->
	<TD vAlign=center align=middle><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
	<SELECT name="select_list"> 
	<OPTION value="ALL">** All Subscribers **</OPTION>
	<OPTION value="OPT-OUT">** All Opt-outs **</OPTION>

end_of_html

	#--------------------------------------
	# Get Active lists for Select List
	#--------------------------------------
	$sql = qq{select l.list_id, l.list_name } . 
	       qq{from   list l where  l.user_id  =  $user_id } .
    	   qq{and l.status = 'A' } .
    	   qq{order by l.list_name } ;
	$sth = $dbh->prepare($sql) ;
	$sth->execute();

	# print qq{ </tr><td>sql = $sql </td></tr><tr><td> \n } ;

	while ( ($list_id, $list_name ) = $sth->fetchrow_array() )
	{
		# print qq{ ListId: $list_id, ListName: $list_name, Email: $email_addr, Type: $email_type, Sub: $subscribe_datetime <br> \n } ;
		# print OUTFILE qq{ ListId: $list_id, ListName: $list_name, Email: $email_addr, Type: $email_type, Sub: $subscribe_datetime \n } ;
		print qq{ <OPTION value="$list_id">$list_name</OPTION> } ;
	}
	$sth->finish();

	print << "end_of_html" ;

	</SELECT> 

	</FONT></TD></TR>
	<TR align=middle>
	<TD><IMG height=7 src="$images/spacer.gif"></TD></TR>
	<TR>
	<TD>&nbsp;</TD></TR>
	<TR align=middle>
	<TD>

	<!-- tbl-15 Body-09 ( Select a Listing - Encompass View List Button ) -->
<!--	<TABLE cellSpacing=0 cellPadding=7 width="100%" border=0>  -->
<!--	<TBODY>  -->
<!--	<TR>  -->
<!--	<TD align=middle>  -->
<!--	<IMG alt=" View email addresses for the selected option" src="$images/view_list.gif" border=0 width="72" height="21">   -->
<!--	</TD></TR></TBODY>  -->
<!--	</TABLE>  -->
	<!-- tbl-15 end -->

	</TD>
	</TR>

<!--	<TR>   -->
<!--	<TD>&nbsp;</TD>   -->
<!--	</TR>   -->
	<TR align=middle>
	<TD>

	<!-- tbl-16 Body-10 ( Select a Listing - Encompass Export CSV, Text Buttons ) -->
	<TABLE cellSpacing=0 cellPadding=7 width="100%" border=0>
	<TBODY>
	<TR>
	<TD align=middle><!-- Action Template -->
	<!-- <INPUT title="Export subscriber email addresses to a CSV file using the selected option" type=image hspace=7 src="$images/export_csv.gif" border=0 name=action_csv width="102" height="20"> -->
	<!-- <INPUT title="Export subscriber email addresses to a text file using the selected option" type=image hspace=7 src="$images/export_text.gif" border=0 name=action_txt width="102" height="20">  -->
	<INPUT name="BtnExportCSV" value="CSV" type=image hspace=7 src="$images/export_csv.gif"  border=0 width="102" height="20">
	<INPUT name="BtnExportTXT" value="TXT" type=image hspace=7 src="$images/export_text.gif" border=0 width="102" height="20">
	</TD></TR></TBODY>
	</TABLE>
	<!-- tbl-16 end -->

	</TD></TR>
	<TR align=middle>
	<TD colSpan=10></TD></TR>
	<TR>
	<TD align=middle><IMG height=3 src="$images/spacer.gif" width=3></TD></TR></TBODY>
	</TABLE>
	<!-- tbl-14 end -->

	</TD>
	<TD align=middle><IMG height=3 src="$images/spacer.gif" width=3></TD>
	<TD><IMG height=3 src="$images/spacer.gif" width=3></TD></TR>
	<TR bgColor=#E3FAD1>
	<TD colSpan=5><IMG height=3 src="$images/spacer.gif" width=1 border=0></TD></TR>
	<TR bgColor=#E3FAD1 height=10>
	<TD vAlign=bottom align=left><IMG height=7 src="$images/lt_purp_bl.gif" width=7 border=0></TD>
	<TD><IMG height=3 src="$images/spacer.gif" width=1 border=0></TD> 
	<TD align=middle bgColor=#E3FAD1><IMG height=3 src="$images/spacer.gif" width=1 border=0>
	<IMG height=3 src="$images/spacer.gif" width=1 border=0></TD> 
	<TD><IMG height=3 src="$images/spacer.gif" width=1 border=0></TD>
	<TD vAlign=bottom align=right>
	<IMG height=7 src="$images/lt_purp_br.gif" width=7 border=0></TD></TR></TBODY>
	</TABLE>
	<!-- tbl-12 end -->

	</TD></TR></TBODY>
	</TABLE>
	<!-- tbl-11 end -->

	</TD></TR><!-- entering default Actiongroup -->
	<TR>
	<TD>&nbsp;</TD></TR>
	<TR>
	<TD>

	<!-- tbl-17 Body-11 ( Select a Listing - Encompass Home Button Image ) -->
	<TABLE cellSpacing=0 cellPadding=7 width="100%" border=0>
	<TBODY>
	<TR>
	<TD align="center">
	<A HREF="mainmenu.cgi">
	<IMG name="BtnHome" src="$images/home_blkline.gif" hspace=7  width="76"  height="23" border=0></A>
	<!-- <INPUT type=image hspace=7 src="$images/home_blkline.gif" border=0 name=action_finish width="76" height="23">  -->
	</TD></TR></TBODY>
	</TABLE>
	<!-- tbl-17 end -->

	</TD></TR><!-- doing editform not wrap --></TBODY>
	</TABLE>
	<!-- tbl-10 end -->

	</FORM></TD></TR></TBODY>
	</TABLE>
	<!-- tbl-06 end -->
end_of_html

} # end sub disp_sub_exp_body



#===============================================================================
# Sub: disp_util_footer
#===============================================================================
sub disp_util_footer
{
	print "</TABLE>" ;  #  <!-- Fix -->
	util::footer();
	$util->clean_up();
} # end sub disp_util_footer
