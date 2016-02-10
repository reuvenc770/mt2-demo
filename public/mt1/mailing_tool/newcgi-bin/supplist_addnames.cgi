#!/usr/bin/perl
#===============================================================================
# Purpose: Displays the HTML page to add 'names' recs to suppression lists.
# File   : supplist_add.cgi
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
my $name;
my $tid;
my $unique_id;
my $file;

#----- connect to the util database -----
###$util->db_connect();

my ($dbhq,$dbhu)=$util->get_dbh();
###$dbh = $util->get_dbh;

#----- check for login --------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}

$tid = $query->param('tid');
$sql = "select list_name,unique_id from vendor_supp_list_info where list_id=$tid";
$sth = $dbhq->prepare($sql);
$sth->execute();
($name,$unique_id) = $sth->fetchrow_array();
$sth->finish();
# print out html screen

util::header("ADD EMAIL ADDRESSES - $name");
print << "end_of_html";
</TD>
</TR>
<TR>
<TD vAlign=top align=left bgColor=#FFFFFF>

	<TABLE cellSpacing=0 cellPadding=10 bgColor=#FFFFFF border=0>
	<TBODY>
	<TR>
	<TD vAlign=top align=left bgColor=#ffffff colSpan=10>

end_of_html
my $supp_url;
my $suppuser;
my $supppass;
$sql="select suppression_url,suppression_username,suppression_password from advertiser_info where vendor_supp_list_id=$tid and advertiser_name='$name'";
$sth = $dbhq->prepare($sql);
$sth->execute();
($supp_url,$suppuser,$supppass) = $sth->fetchrow_array();
$sth->finish();
print<<"end_of_html";
		<center>
		<table>
		<tr><td><b>Suppression URL: </b></td><td>$supp_url</td></tr>
		<tr><td><b>Username: </b></td><td>$suppuser</td></tr>
		<tr><td><b>Password: </b></td><td>$supppass</td></tr>
		</table>
		</center>
<br>
		<TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
		<TBODY>
		<TR>
		<TD vAlign=center align=left><FONT face="verdana,arial,helvetica,sans serif" 
			color=#509C10 size=3> <B> Add New Email Addresses To $name</B> </FONT></TD>
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
			Enter a file with email addresses or enter email addresses manually.  <BR></FONT></TD>
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

				<FORM action="supplist_add_file.cgi" method="get" encType="multipart/form-data">
				<input type=hidden name=tid value=$tid>

				<TABLE cellSpacing=0 cellPadding=0 width="100%" bgColor="$light_table_bg" border=0>
				<TBODY>
				<TR align=top bgColor=#509C10 height=18>
				<TD vAlign=top align=left height=15><IMG height=7 src="$images/blue_tl.gif" 
					width=7 border=0></TD>
				<TD height=15><IMG height=1 src="$images/spacer.gif" width=3 border=0></TD>
				<TD align=middle height=15>
					<FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=2>
					<B>File Upload</B> </FONT></TD>
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
					<TD><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
						Your file will be processed offline and you will receive an email 
						when it is finished<br><br></font></TD>
					</TR>
					<TR>
					<TD><IMG height=4 src="$images/spacer.gif"></TD>
					</TR>
					<TR>
					<TD><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
						Enter the File Name to Upload in the text box below.</FONT></TD>
					</TR>
					<TR>
					<TD><IMG height=4 src="$images/spacer.gif"></TD>
					</TR>
					<TR>
					<TD><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>Uploaded File: <select name=ftp_file><option value="">None</option>
end_of_html
opendir(DIR, "/home/supp");
while (defined($file = readdir(DIR)))
{
    if ($file eq "." || $file eq "..")
    {
        # skip files . and ..
        next;
    }
	print "<option value=\"$file\">$file</option>\n";
}
closedir(DIR);
print<<"end_of_html";
</select></td>
					</TR>
					<TR>
					<TD><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
						<INPUT type=file name="upfile" size="50">
						<INPUT type="submit" value="Upload File Now"></FONT></TD>
					</TR>
					<TR>
					<TD><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
						File Date(yyyy-mm-dd): <INPUT type=text name="filedate" size="20">&nbsp;&nbsp;Upload Immediately <input type=checkbox value="Y" name="immediate_upload">
						</FONT></TD>
					</TR>
					<TR>
					<TD><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
						Unique ID: <INPUT type=text name="unique_id" value="$unique_id" size="20">&nbsp;&nbsp;
						</FONT></TD>
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

			<TABLE cellSpacing=0 cellPadding=5 width="100%" border=0>
			<TBODY>
			<TR>
			<TD align=middle>

				<FORM action="supplist_add_manual.cgi" method="post">
				<input type=hidden name=tid value=$tid>
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
						<TEXTAREA name="email_list_text_area" rows=10 wrap=off cols=45></TEXTAREA>
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
				<A HREF="mainmenu.cgi">
				<IMG src="$images/home_blkline.gif" border="0"></A></td>
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
