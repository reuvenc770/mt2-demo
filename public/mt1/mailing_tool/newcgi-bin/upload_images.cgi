#!/usr/bin/perl

# *****************************************************************************************
# upload_images.cgi
#
# this page is used by a client to upload images to their homedir
#
# History
# Grady Nash, 9/14/01, Creation
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $images = $util->get_images_url;
my $sth;
my $sql;
my $dbh;
my $rows;
my $errmsg;
my $user_id;
my $bin_dir_http;

# connect to the util database

###$util->db_connect();

my ($dbhq,$dbhu)=$util->get_dbh();
###$dbh = $util->get_dbh;

# check for login

$user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}

# lookup directory from sysparm

$sql = "select parmval from sysparm where parmkey = 'BIN_DIR_HTTP'";
$sth = $dbhq->prepare($sql);
$sth->execute();
($bin_dir_http) = $sth->fetchrow_array();
$sth->finish();

# print out the html page

util::header("Upload Images");

print << "end_of_html";
</TD>
</TR>
<TR>
<TD vAlign=top align=left bgColor=#999999>

	<TABLE cellSpacing=0 cellPadding=10 bgColor=#999999 border=0 width="100%">
	<TBODY>
	<TR>
	<TD vAlign=top align=left bgColor=#ffffff colSpan=10>

		<TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
		<TBODY>
		<TR>
		<TD vAlign=center align=left><FONT face="verdana,arial,helvetica,sans serif" 
			color=#509C10 size=3><B>Upload Images</B></FONT> </TD>
		</TR>
		<TR>
		<TD><IMG height=3 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>

		<TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
		<TBODY>
		<TR>
		<TD colSpan=10><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
			Use the screen below to upload your own images to your personal 
			directory on the web.  After uploading an image, you can reference it
			as ${bin_dir_http}your_username/your_image.gif</FONT></TD>
		</TR>
		<TR>
		<TD><IMG height=5 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>

		<FORM action="upload_images_save.cgi" method="post" encType="multipart/form-data">

		<TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
		<TBODY>
		<TR>
		<TD vAlign=top>

			<TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
			<TBODY>
			<TR>
			<TD vAlign=top align=center>

                <!-- Begin main body area -->

                <TABLE cellSpacing=0 cellPadding=0 width=100% bgColor=#E3FAD1 border=0>
                <TBODY>
                <TR bgColor=#509C10>
                <TD vAlign=top align=left height=15><IMG src="$images/blue_tl.gif"
                    border=0 width="7" height="7"></TD>
                <TD align=middle height=15><FONT face="verdana,arial,helvetica,sans serif"
                    color=#ffffff size=2><B>Image File Upload</B></FONT></TD>
                <TD vAlign=top align=right bgColor=#509C10 height=15><IMG
                    src="$images/blue_tr.gif" border=0 width="7" height="7"></TD>
                </TR>
                <TR>
                <TD colSpan=3>&nbsp;&nbsp; <FONT face="verdana,arial,helvetica,sans serif"
                    color=#509C10 size=2><B>Filename</B></FONT></TD>
                </TR>
               	<TR>
                <TD colSpan=3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<INPUT type="file" size="50" name="upload_file"></TD>
                </TR>
                <TR>
                <TD colSpan=3>&nbsp;</TD>
				</TR>
                <TD colSpan=3 align=center>
					<table cellpadding="0" cellspacing="0" border="0" width=100%>
					<tr>
					<td width=50% align=center>
						<a href=mainmenu.cgi>
						<img src="$images/home_blkline.gif" border=0></a></td>
					<td width=50% align=center>
						<input type="image" src="$images/save.gif" border=0></TD>
					</tr>
					</table>
				</td>
				</TR>
                <TR>
                <TD colSpan=3>&nbsp;</TD>
				</TR>
				<TR>
				<TD vAlign=bottom align=left colSpan=2><IMG height=7 src="$images/lt_purp_bl.gif" 
					width=7 border=0></TD>
				<TD vAlign=bottom align=right><IMG height=7 src="$images/lt_purp_br.gif" 
					width=7 border=0></TD>
				</TR>
				</TBODY>
				</TABLE>

				<!-- End main body area -->

			</TD>
			</TR>
			</TBODY>
			</TABLE>

		</TD>
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
<TD noWrap align=left height=17>
end_of_html

$util->footer();

# exit function

$util->clean_up();
exit(0);
