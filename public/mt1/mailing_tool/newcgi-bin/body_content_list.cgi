#!/usr/bin/perl
use strict;
use CGI;
use util;
use vars qw($DBH);
require "/var/www/html/newcgi-bin/modules/Common.pm";

my $util = util->new;

my $images = $util->get_images_url;
my $alt_light_table_bg = $util->get_alt_light_table_bg;
$DBH=Common::connect_db();

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
	my ($bgcolor) ;

	print << "end_of_html" ;
	<center><a href="/cgi-bin/body_content.cgi?type=add"><img src="/mail-images/add.gif" border=0></a><br><br>
	<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
	<TBODY>
	<TR bgColor="#509C10" height=15>
		<TD colspan="6" align=center height=15>
		<font face="Verdana,Arial,Helvetica,sans-serif" color="white" size="3">
		<b>List of Current Header Contents</font>
		<font face="Verdana,Arial,Helvetica,sans-serif" color="white" size="2">
		(click name to edit the Body)</b></font></TD>
	</TR>
	<TR> 
	<TD bgcolor="#EBFAD1" align="left" width="02%">&nbsp;</td>
	<TD bgcolor="#EBFAD1" align="left" width="10%">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2> 
		<b>Name</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width=10%><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>Date</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width=10%><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>Modified Date</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width="10%"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>Inactive Date</b></font></td>
	<TD bgcolor="#EBFAD1" align="center" width=8%><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2></font></td>
	</TR> 

end_of_html

	#===========================================================================
	# Loop - Get footer variations 
	#===========================================================================

	my $sql=qq|SELECT id,body_name,date_add,inactive_date,modified_date,body_content FROM body_content|;
	my $sth=$DBH->prepare($sql) ;
	$sth->execute();
	my $reccnt = 0 ;
	while (my $hr=$sth->fetchrow_hashref) {
		if ($hr->{inactive_date} eq "0000-00-00") {
			$hr->{inactive_date}="";
		}
		$reccnt++;
		my $bgcolor=$reccnt % 2==0 ? "#EBFAD1" : "$alt_light_table_bg"; 

		print qq{<TR bgColor=$bgcolor> \n} ;
		print qq{	<TD align=left>&nbsp;</td> \n} ;
        print qq{	<TD align=left><font color="#509C10" face="Arial" size="2"> \n } ;
		print qq{		<A HREF="body_content.cgi?type=show&bodyID=$hr->{id}">$hr->{body_name}</a></font></TD> \n } ;
        print qq{	<TD align=left><font color="#509C10" face="Arial" size="2">$hr->{date_add}</font></TD> \n } ;
        print qq{	<TD align=left><font color="#509C10" face="Arial" size="2">$hr->{modified_date}</font></TD> \n } ;
        print qq{	<TD align=left><font color="#509C10" face="Arial" size="2">$hr->{inactive_date}</font></TD> \n } ;
        print qq^
			<TD align=left><font color="#509C10" face="Arial" size="2"><a href="/cgi-bin/body_content.cgi?type=del&bodyID=$hr->{id}">Delete</a>&nbsp;&nbsp;<a href="/cgi-bin/body_content.cgi?type=preview&bodyID=$hr->{id}" target=_blank>Preview</a></TD> \n ^;
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
#	$util->clean_up();
$DBH->disconnect;
} # end sub disp_footer


