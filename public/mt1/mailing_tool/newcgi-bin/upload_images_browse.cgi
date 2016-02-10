#!/usr/bin/perl
# *****************************************************************************************
# upload_images_browse.cgi
#
# this page shows the user a listing of images they have uploaded
#
# History
# Grady Nash   09/27/2001		Creation 
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $sql;
my $dbh;
my $username;
my $user_id;
my $imagedir;
my $images = $util->get_images_url;
my $bin_dir_http;
my $document_root;
my $reccnt;
my $bgcolor;
my $file;
my $light_table_bg = $util->get_light_table_bg;
my $alt_light_table_bg = $util->get_alt_light_table_bg;
my $table_header_bg = $util->get_table_header_bg;
my $delfile;

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

# get some parameters from sysparm

$sql = "select parmval from sysparm where parmkey = 'BIN_DIR_HTTP'";
$sth = $dbhq->prepare($sql);
$sth->execute();
($bin_dir_http) = $sth->fetchrow_array();
$sth->finish();

$sql = "select parmval from sysparm where parmkey = 'DOCUMENT_ROOT'";
$sth = $dbhq->prepare($sql);
$sth->execute();
($document_root) = $sth->fetchrow_array();
$sth->finish();

# get this users username

$sql = "select username from user where user_id = $user_id";
$sth = $dbhq->prepare($sql);
$sth->execute();
($username) = $sth->fetchrow_array();
$sth->finish();

# build the pathname for this users image directory

$imagedir = "${document_root}$username";

# show the user the directory listing

util::header("UPLOAD IMAGES");
print << "end_of_html";
</TD>
</TR>
<TR>
<TD vAlign=top align=left bgColor=#999999>

	<TABLE cellSpacing=0 cellPadding=10 bgColor=#999999 border=0 width="100%">
	<TBODY>
	<TR>
	<TD vAlign=top align=left bgColor=#ffffff>

		<TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
		<TBODY>
		<TR>
		<TD vAlign=center align=left><FONT face="verdana,arial,helvetica,sans serif" 
			color=#509C10 size=3><B>Upload Image Directory</B></FONT> </TD>
		</TR>
		<TR>
		<TD><IMG height=5 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>

		<TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
        <TBODY>
        <TR>
        <TD colSpan=10><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
            This is a list of all the files that you have uploaded.  These files can
			be referenced in any html email as 
			${bin_dir_http}$username/your_image.gif</FONT></TD>
        </TR>
        <TR>
        <TD><IMG height=5 src="$images/spacer.gif"></TD>
        </TR>
        </TBODY>
        </TABLE>

    	<TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=$alt_light_table_bg border=0>
    	<TBODY>
    	<TR bgColor=$table_header_bg>
    	<TD vAlign=top align=left height=15>
			<IMG src="$images/blue_tl.gif" border=0 width="7" height="7"></TD>
    	<TD align=left height=15>
			<FONT face="verdana,arial,helvetica,sans serif" color=#FFFFFF size=2><B>
			Image files for $username</B></FONT></TD>
    	<TD>&nbsp;</td>
    	<TD vAlign=top align=right height=15>
			<IMG src="$images/blue_tr.gif" border=0 width="7" height="7"></TD>
    	</TR>
end_of_html

opendir(DIR, $imagedir);
while (defined($file = readdir(DIR)))
{
	if ($file eq "." || $file eq "..")
	{
		# skip files . and ..
		next;
	}

    $reccnt++;
    if (($reccnt % 2) == 0) 
	{
        $bgcolor = "$light_table_bg";
    }
    else 
	{
       $bgcolor = "$alt_light_table_bg";
    }

	# replace funky characters in filename with url escape sequences

	$delfile = $file;
	$delfile =~ s/&/%26/g;			# & is %26

	# print out row to the screen

    print qq {
			<tr bgcolor=$bgcolor>
			<td colspan=4 height=7><img src="$images/spacer.gif" height=7></td>
			</tr> \n
			<TR bgColor=$bgcolor>
            <TD>&nbsp;</TD>
            <TD><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
				<a href="${bin_dir_http}$username/$file">$file</a></font></td>
            <TD><img src="${bin_dir_http}$username/$file" border=1><br>
				<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
				${bin_dir_http}$username/$file</font></TD>
            <TD><FONT face="verdana,arial,helvetica,sans serif" size=2>
				<a href="upload_images_delete.cgi?file=$delfile">Delete</a></font></td>
			</tr> \n
			<tr bgcolor=$bgcolor>
			<td colspan=4 height=7><img src="$images/spacer.gif" height=7></td>
			</tr> \n};
}
closedir(DIR);

print << "end_of_html";
		</TBODY>
		</TABLE>

	</TD>
	</TR>
	<TR>
	<TD bgcolor=#ffffff>

        <TABLE cellSpacing=0 cellPadding=7 width="100%" border=0>
        <TBODY>
        <TR>
        <TD align=center>
            <a href="mainmenu.cgi">
            <IMG src="$images/home_blkline.gif" border=0></a></TD>
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

# exit function

$util->clean_up();
exit(0);
