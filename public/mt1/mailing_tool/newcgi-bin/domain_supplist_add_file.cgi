#!/usr/bin/perl
#===============================================================================
# File   : domain_supplist_add_file.cgi
#
# Add Subscribers
#
#--Change Control---------------------------------------------------------------
# Jim Sobeck,	01/26/2004 
#===============================================================================

#-----------------------
# include Perl Modules
#-----------------------
use strict;
use CGI qw(:standard);
use util;

$|=1 ;   # set OUTPUT_AUTOFLUSH to true

my $util = util->new;
my $query = new CGI;
my $dbh;
my $sql;
my $sth;
my $list_name;
my $images = $util->get_images_url;
my $alt_light_table_bg = $util->get_alt_light_table_bg;
my $light_table_bg = $util->get_light_table_bg;
my $table_text_color = $util->get_table_text_color;
my $errmsg;
my $up_file;
my $list_id = $query->param('tid');
my $add_sub_dir;
my (@temp_file, %confirm);
my ($file_name, $file_handle, $file_problem) ;
my ($BytesRead, $Buffer, $Bytes ) ;
my $tmp_file;
my $email_addr;

# ----- connect to the util database -------

###$util->db_connect();

my ($dbhq,$dbhu)=$util->get_dbh();
###$dbh = $util->get_dbh;

# ----- check for login -------

my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}

# get subdir to add files to

$add_sub_dir = "/var/www/util/domain_supplist/"; 

# get this users email address
$email_addr = "jpersi\@tranzactmedia.com"; 

# get filename and check it out

if ( $query->param('upfile') =~ /([^\/\\]+)$/ ) 
{
	$file_name = $1;                # set file_name to $1 var - (file-name no path)
	$file_name =~ s/^\.+//;         # say what...
	$file_name =~ s/\s/_/g;         # replace WhiteSpace with UnderScore global
	$file_handle = $query->param('upfile');
	binmode($file_handle);
}
else 
{
	$file_problem = $query->param('upfile');
	util::logerror("Bad File Name: $file_problem");
    $util->clean_up();
	exit(0);
}

$up_file = $file_name;

# create the new output file

unless (open(OUTFILE, ">${add_sub_dir}${list_id}_${up_file}")) 
{
	&error("There was an error opening the Output File\n, Check directory premissions!\n") ; 
	exit(0);
}

# read from uploaded file and write out new output file

binmode(OUTFILE);
undef $BytesRead;
undef $Buffer;
			
while ($Bytes = read($file_handle,$Buffer,1024)) 
{
	$BytesRead += $Bytes;
	print OUTFILE $Buffer;
}

$confirm{$file_handle} = $BytesRead;

# close everthing

close($file_handle);
close(OUTFILE);  

# clean up temp file

@temp_file = <CGItemp*>;
foreach $tmp_file (@temp_file)
{
	unlink ("$tmp_file");
}

# find out name of list

$sql = "select list_name from vendor_domain_supp_list_info where list_id = $list_id";
$sth = $dbhq->prepare($sql);
$sth->execute();
($list_name) = $sth->fetchrow_array();
$sth->finish();

# print screen to tell the user what happened

util::header("Add Domain File Status");    # Print HTML Header

print << "end_of_html";
</TD>
</TR>
<TR>
<TD vAlign=top align=left bgColor=#999999>

	<TABLE cellSpacing=0 cellPadding=10 bgColor=#FFFFFF border=0 width=100%>
	<TBODY>
	<TR>
	<TD vAlign=top align=left bgColor=#FFFFFF colSpan=4>

		<TABLE cellSpacing=0 cellPadding=0 width="100%" bgColor=#ffffff border=0>
		<TBODY>
		<TR>
		<TD><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=3>
			<B>Confirmation</B></FONT></TD>
		</TR>
		<tr>
		<td><img height="20" src="$images/spacer.gif"></td>
		</tr>
		</TBODY>
		</TABLE>
	
		<TABLE bgcolor="$light_table_bg" cellSpacing=0 cellPadding=0 width="600" border=0>
		<TBODY>
		<TR bgColor=#509C10 height=15>
		<TD align=middle colspan="2">
			<font face="Verdana,Arial,Helvetica,sans-serif" color="white" size="2">
			<b>Status of Domain File Upload</b></font></TD>
		</TR>
		<tr>
		<td colspan="2"><img src="$images/spacer.gif" height="15"></td>
		</tr>
 		<TR> 
		<td width="70">&nbsp;</td>
 		<TD><FONT face="verdana,arial" color="$table_text_color" size=2>
			Your File <b>$up_file</b> was loaded successfully</font></td>
		</tr>
		<tr>
		<td colspan="2"><img src="$images/spacer.gif" height="5"></td>
		</tr>
		<tr>
		<td width="70">&nbsp;</td>
 		<TD><FONT face="verdana,arial" color="$table_text_color" size=2>
			File contains $confirm{$file_handle} bytes</FONT></TD> 
 		</TR> 
		<tr>
		<td colspan="2"><img src="$images/spacer.gif" height="5"></td>
		</tr>
		<tr>
		<td width="70">&nbsp;</td>
 		<TD><FONT face="verdana,arial" color="$table_text_color" size=2>
			File will be loaded into list <b>$list_name</b></FONT></TD> 
 		</TR> 
		<tr>
		<td colspan="2"><img src="$images/spacer.gif" height="5"></td>
		</tr>
		<tr>
		<td width="70">&nbsp;</td>
 		<TD><FONT face="verdana,arial" color="$table_text_color" size=2>
			File processing will begin shortly.  An email will be sent to 
			<b>$email_addr</b> to let you know when the file processing is finished.</FONT></TD> 
 		</TR> 
		<tr>
		<td colspan="2"><img src="$images/spacer.gif" height="25"></td>
		</tr>
		<tr>
		<td width="70">&nbsp;</td>
		<td align="center" width="100%">

			<table cellpadding="0" cellspacing="0" border="0" width="100%">
			<tr>
			<td width="50%" align="center">
				<a href="/cgi-bin/mainmenu.cgi">
				<img src="$images/home_blkline.gif" border="0"></a></td>
			<td width="50%" align="center">
				<a href="/cgi-bin/domain_supplist_addnames.cgi/tid=$list_id">
				<img src="$images/previous_arrow.gif" border="0"></a></td>
			</tr>
			</table>

		</tr>
		</tbody>
		</table>

	</td>
	</tr>
	</tbody>
	</table>

</TD>
</TR>
<TR>
<TD noWrap align=left height=17>
end_of_html

$util->footer();

$util->clean_up();
exit(0);

