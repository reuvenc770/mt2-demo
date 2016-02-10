#!/usr/bin/perl

# *****************************************************************************************
# upload_images_save.cgi
#
# this page saves an image to a users upload directory
#
# History
# Grady Nash   09/14/2001		Creation 
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
my $file_name;
my $file_handle;
my $file_problem;
my $imagedir;
my $BytesRead;
my $Buffer;
my $Bytes;
my $up_file;
my $images = $util->get_images_url;
my $bin_dir_http;
my $document_root;

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

# check for upload_file specified by user

if (!($query->param('upload_file'))) 
{
	util::logerror("You forgot to select a file to upload!");
    $util->clean_up();
    exit(0);
}

# check format of filename, make sure it is a valid filename

if ( $query->param('upload_file') =~ /([^\/\\]+)$/ ) 
{
	$file_name = $1;                # set file_name to $1 var - (file-name no path)
	$file_name =~ s/^\.+//;         # say what...
	$file_name =~ s/\s/_/g;         # replace WhiteSpace with UnderScore global
	$file_handle = $query->param('upload_file');
	binmode($file_handle);
}
else 
{
	$file_problem = $query->param('upload_file');
	util::logerror("Bad File Name: $file_problem, File name can't have a slash in it!\n Rename it and try again!") ;
    $util->clean_up();
	exit(0);
}

# open the file 

$imagedir = "${document_root}$username";
$up_file = $file_name;
unless (open(OUTFILE, ">$imagedir/$up_file")) 
{
	util::logerror("There was an error opening the Output File\n, Check directory premissions!\n");
    $util->clean_up();
	exit(0);
}

# copy the output file to the place where it belongs

binmode(OUTFILE);
undef $BytesRead;
undef $Buffer;

while ($Bytes = read($file_handle,$Buffer,1024)) 
{
	$BytesRead += $Bytes;
	print OUTFILE $Buffer;
}

# finished writing file, now close

close($file_handle);
close(OUTFILE);

# fix file permissions

chmod (0777, "$imagedir/$up_file");

# tell the user the file upload worked
# print out the html page

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
			color=#509C10 size=3><B>Upload Images</B></FONT> </TD>
		</TR>
		<TR>
		<TD><IMG height=8 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>

		<TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
		<TBODY>
		<TR>
		<TD><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
			Your image upload was Sucessfull.<br>
			You can reference the file as 
			<a href="${bin_dir_http}$username/$up_file">${bin_dir_http}$username/$up_file</a><br>
			</FONT></TD>
		</TR>
		<TR>
		<TD><IMG height=20 src="$images/spacer.gif"></TD>
		</TR>
		<TR>
		<TD align=center>
			<a href="mainmenu.cgi">
			<img src="$images/home_blkline.gif" border=0></a></td>
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
