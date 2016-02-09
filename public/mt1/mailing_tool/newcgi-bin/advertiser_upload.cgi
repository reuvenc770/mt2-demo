#!/usr/bin/perl
#===============================================================================
# Purpose: Displays uploaded files 
# File   : Advertiser_upload.cgi 
#
#--Change Control---------------------------------------------------------------
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
my ($dbhq,$dbhu)=$util->get_dbh();

#----- check for login --------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}

my $aid = $query->param('aid');
$sql = "select advertiser_name from advertiser_info where advertiser_id=$aid"; 
$sth = $dbhq->prepare($sql);
$sth->execute();
($name) = $sth->fetchrow_array();
$sth->finish();
# print out html screen

util::header("Advertiser Files - $name");
print << "end_of_html";
</TD>
</TR>
<TR>
<TD vAlign=top align=left bgColor=#FFFFFF>

	<TABLE cellSpacing=0 cellPadding=10 bgColor=#FFFFFF border=0>
	<TBODY>
	<TR>
	<TD vAlign=top align=left bgColor=#ffffff colSpan=10>
<br>
		<TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
		<TBODY>
		<TR>
		<TD><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
			Enter a file with creative assets.  <BR></FONT></TD>
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

				<FORM action="advertiser_add_file.cgi" method="post" encType="multipart/form-data">
				<input type=hidden name=aid value=$aid>

				<TABLE cellSpacing=0 cellPadding=0 width="100%" bgColor="$light_table_bg" border=0>
				<TBODY>
				<TR bgColor=#E3FAD1>
				<TD colSpan=5><IMG height=3 src="$images/spacer.gif" width=1 border=0></TD>
				</TR>
					<TR>
					<TD><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
						<INPUT type=file name="upfile" size="50">
						<INPUT type="submit" value="Upload File Now"></FONT></TD>
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
				</TBODY>
				</TABLE>

				</FORM>

			</TD>
			</TR>
			</TBODY>
			</TABLE>

		</TD>
		</TR>
<tr><td>Check One or More Files to Delete or select a filename to download</td></tr>
		<TR>
		<TD>
<form method=post action=advertiser_del_file.cgi>
<input type=hidden name=aid value=$aid>
<table width=50% bgColor=#E3FAD1>
<tr><th width=25 align=right></th><th align=left>Filename</th></tr>
end_of_html
opendir(DIR, "/home/adv/$aid");
while (defined($file = readdir(DIR)))
{
    if ($file eq "." || $file eq "..")
    {
        # skip files . and ..
        next;
    }
	print "<tr><td align=right><input type=checkbox name=chkbox value=\"$file\"></td><td align=left><a href=\"http://mailingtool.routename.com:83/adv/$aid/$file\">$file</a></td></tr>\n";
}
closedir(DIR);
print<<"end_of_html";
<tr><td colspan=2 align=middle><input type=submit value="Delete"></td></tr>
</table>
		</TD>
		</TR>
		<TR>
		<TD>

			<TABLE cellSpacing=0 cellPadding=7 width="100%" border=0>
			<TBODY>
			<TR>
			<TD width=100% align=center>
				<A HREF="advertiser_disp2.cgi?puserid=$aid&pmode=U">
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
