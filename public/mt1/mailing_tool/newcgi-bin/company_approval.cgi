#!/usr/bin/perl
#===============================================================================
# File   : company_approval.cgi
#
#--Change Control---------------------------------------------------------------
# Jim Sobeck, 05/17/06  Created.
#===============================================================================

#-----------------------
# include Perl Modules
#-----------------------
use strict;
use CGI;
use util;

# declare variables

my $util = util->new;
my $query = CGI->new;
my ($sth, $reccnt, $sql, $dbh ) ;
my ($go_back, $go_home, $mesg, $list_id, $list_name, $chkbox_name);
my $images = $util->get_images_url;
my ($cat_id, $category, $db_field, $html_disp_order) ;
my (%file_fields, $field_position, $key, $value, $checked, $nbr_cols) ;
my ($file_layout_reccnt, $file_layout_str, $category_name);
my ($first_name, $last_name, $company);
my $alt_light_table_bg = $util->get_alt_light_table_bg;
my $light_table_bg = $util->get_light_table_bg;
my $rows;
my $errmsg;
my $company_id;
$company_id=$query->param('company_id');

#----- connect to the util database -----
my ($dbhq,$dbhu)=$util->get_dbh();

#----- check for login --------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}

# print out html screen

util::header("Company Approval List");
print << "end_of_html";
</TD>
</TR>
<TR>
<TD vAlign=top align=left bgColor=#FFFFFF>

	<TABLE cellSpacing=0 cellPadding=10 bgColor=#FFFFFF border=0>
	<TBODY>
	<TR>
	<TD vAlign=top align=left bgColor=#ffffff colSpan=10>

		<TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
		<TBODY>
		<TR>
		<TD vAlign=center align=left><FONT face="verdana,arial,helvetica,sans serif" 
			color=#509C10 size=3> <B> Company Approval List</B> </FONT></TD>
		</TR>
		<TR>
		<TD><IMG height=3 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>

		<TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
		<TBODY>
		<TR>
		<TD><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
			Enter email addresses manually.
			Only email addresses obtained with the addressee's permission
			should be used. <BR></FONT></TD>
		</TR>
		<TR>
		<TD><IMG height=5 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>

		<TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0> 
		<TBODY>
		<TR>
		<TD>

			<TABLE cellSpacing=0 cellPadding=5 width="100%" border=0>
			<TBODY>
			<TR>
			<TD align=middle>

				<FORM action="save_company_approval.cgi" method="post">
				<input type=hidden name="company_id" value="$company_id">

				<TABLE cellSpacing=0 cellPadding=0 width="100%" bgColor=#E3FAD1 border=0>
				<TBODY>
				<TR align=top bgColor=#509C10 height=18>
				<TD vAlign=top align=left height=15>
					<IMG height=7 src="$images/blue_tl.gif" width=7 border=0></TD>
				<TD height=15><IMG height=1 src="$images/spacer.gif" width=3 border=0></TD>
				<TD align=middle height=15>

					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					<TBODY>
					<TR bgColor=#509C10 height=15>
					<TD align=middle width="100%" height=15>
						<FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=2>
						<B>Manually Add Email Addresses</B> </FONT></TD>
					</TR>
					</TBODY>
					</TABLE>
				
				</TD>
				<TD height=15><IMG height=1 src="$images/spacer.gif" width=3 border=0></TD>
				<TD vAlign=top align=right bgColor=#509C10 height=15>
					<IMG height=7 src="$images/blue_tr.gif" width=7 border=0></TD>
				</TR>
				<TR bgColor=#E3FAD1>
				<TD colSpan=5><IMG height=3 src="$images/spacer.gif" width=1 border=0></TD>
				</TR>
				<TR bgColor=#E3FAD1>
				<TD><IMG height=3 src="$images/spacer.gif" width=3></TD>
				<TD align=middle><IMG height=3 src="$images/spacer.gif" width=3></TD>
				<TD align=middle>

					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					<TBODY>
					<TR>
					<TD align=middle height="5"><IMG height=3 src="$images/spacer.gif" width=3></TD>
					</TR>
					<TR>
					<TD><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
						Type email addresses in the box below.  Hit ENTER after each
						email address.  Each email address must be on a separate line.</FONT> </TD>
					</TR>
					<TR>
					<TD><IMG height=4 src="$images/spacer.gif"></TD>
					</TR>
					<TR>
					<TD>&nbsp;&nbsp;&nbsp;
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
						<TEXTAREA name="email_list_text_area" rows=10 wrap=off cols=45>
end_of_html
$sql = "select email_addr from company_approval where company_id=$company_id order by email_addr";
$sth = $dbhq->prepare($sql) ;
$sth->execute();
my $cemail;
while (($cemail) = $sth->fetchrow_array())
{
	print "$cemail\n";
}
$sth->finish();
print <<"end_of_html";
</TEXTAREA>
	    				<INPUT type="submit" value="Add Addresses Now"></FONT></TD>
					</TR>
					<TR>
					<TD><IMG height=3 src="$images/spacer.gif" width=3></TD>
					</TR>
					</TBODY>
					</TABLE>

				</TD>
				<TD align=middle><IMG height=3 src="$images/spacer.gif" width=3></TD>
				<TD><IMG height=3 src="$images/spacer.gif" width=3></TD>
				</TR>
				<TR bgColor=#E3FAD1>
				<TD colSpan=5><IMG height=3 src="$images/spacer.gif" width=1 border=0></TD>
				</TR>
				<TR bgColor=#E3FAD1 height=10>
				<TD vAlign=bottom align=left><IMG height=7 src="$images/lt_purp_bl.gif" 
					width=7 border=0></TD>
				<TD><IMG height=3 src="$images/spacer.gif" width=1 border=0></TD>
				<TD align=middle bgColor=#E3FAD1><IMG height=3 src="$images/spacer.gif" 
					width=1 border=0><IMG height=3 src="$images/spacer.gif" width=1 border=0></TD>
				<TD><IMG height=3 src="$images/spacer.gif" width=1 border=0></TD>
				<TD vAlign=bottom align=right><IMG height=7 src="$images/lt_purp_br.gif" 
					width=7 border=0></TD>
				</TR>
				</TBODY>
				</TABLE>

				</FORM>

			</TD>
			</TR>
			</TBODY>
			</TABLE>

		</TD>
		</TR>
		<TR>
		<TD>

			<TABLE cellSpacing=0 cellPadding=7 width="100%" border=0>
			<TBODY>
			<TR>
			<TD width=100% align=center>
				<A HREF="/mainmenu.cgi">
				<IMG src="$images/home_blkline.gif" border="0"></A>&nbsp;&nbsp;<a href="/cgi-bin/company_disp.cgi?company_id=$company_id"><img src="/images/cancel_blkline.gif" border=0></a></td>
			</TR>
			</TBODY>
			</TABLE>

		</TD>
		</TR>
		</TBODY>
		</TABLE>

	</TD>
	</TR>
	</TBODY>
	</TABLE>

</TD>
</TR>
<TR>
<TD noWrap align=left height=17>
end_of_html

$util->footer();

$util->clean_up();
exit(0);

#-------- End of Main Logic -----------------------------------

