#!/usr/bin/perl
#===============================================================================
# Purpose: Displays a List of Creatives 
# File   : creative_list.cgi
#
# Input  :
#   1. mesg - If present - display mesg from list_upd.cgi or list_add.cgi.
#
# Output :
#--Change Control---------------------------------------------------------------
# Jim Sobeck, 02/08/05  Created.
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
my $cid;
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
	my ($puserid, $username, $name, $email_addr,$internal_email_addr,$physical_addr,$cstatus); 
	my ($bgcolor) ;

	print << "end_of_html" ;
	<script language=JavaScript>
	function add_creative(aid)
	{
		self.parent.location="/cgi-bin/add_creative.cgi?backto=creative&aid="+aid;
	}
	function edit_creative(aid,cid)
	{
		self.parent.location="/cgi-bin/edit_creative.cgi?backto=creative&aid="+aid+"&cid="+cid;
	}
	function delete_creative(aid,cid)
	{
		self.parent.location="/cgi-bin/delete_creative.cgi?backto=creative&aid="+aid+"&cid="+cid;
	}
	function preview_creative(cid) {
		window.open("/cgi-bin/camp_preview.cgi?campaign_id="+cid+"&format=H",'Preview','toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,width=900,height=500,left=25,top=50');
	}
	</script>
	<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
	<TBODY>
	<TR>
<td colspan=5 align=center><a href="mainmenu.cgi"><img src="/mail-images/home_blkline.gif" border=0></a></td></tr>
	<tr><td>&nbsp;</td></tr>
	<TR bgColor="#509C10" height=15>
		<TD colspan="6" align=center width="45%" height=15>
		<font face="Verdana,Arial,Helvetica,sans-serif" color="white" size="3">
		<b>List of Current Advertisers/Creatives</font>
		<font face="Verdana,Arial,Helvetica,sans-serif" color="white" size="2">
		</b></font></TD>
	</TR>
	<TR> 
	<TD bgcolor="#EBFAD1" align="left" width="02%">&nbsp;</td>
	<TD bgcolor="#EBFAD1" align="left" width="20%">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2> 
		<b>Advertiser Name</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width="20%">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2>
		<b>Creative Name</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width="10%">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2>
		<b>Functions</b></font></td>
	</TR> 

end_of_html

	#===========================================================================
	# Loop - Get ALL Lists that belong to the User
	#===========================================================================

	$sql = "select advertiser_id,advertiser_name from advertiser_info where status='A' and advertiser_id != 1 order by advertiser_name";
	$sth = $dbhq->prepare($sql) ;
	$sth->execute();
	$reccnt = 0 ;
	while (($puserid, $name) = $sth->fetchrow_array())
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
		print qq{		<a href="view_thumbnails.cgi?aid=$puserid" target=_blank>$name</a></font></TD> \n } ;
		$sql = "select creative_id,creative_name from creative where advertiser_id=$puserid and status='A' order by creative_name";
	$sth1 = $dbhq->prepare($sql) ;
	$sth1->execute();
    print qq{	<TD align=left><font color="#509C10" face="Arial" size="2"><select name=cid$puserid>\n };
	while (($cid, $name) = $sth1->fetchrow_array())
	{
        print qq{	<option value=$cid>$name</option> \n } ;
	}
    print qq{	</font></select></TD> \n } ;
	$sth1->finish();
        print qq{	<TD align=left><font color="#509C10" face="Arial" size="2"><input type=button value="Add" onClick=add_creative($puserid);>&nbsp;&nbsp;<input type=button Value="Edit" onClick=edit_creative($puserid,cid$puserid.value);>&nbsp;&nbsp;<input type=button value="Delete" onClick=delete_creative($puserid,cid$puserid.value);><input type=button value="Preview" onClick="javascript:preview_creative(cid$puserid.value);"></font></TD> \n } ;
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

