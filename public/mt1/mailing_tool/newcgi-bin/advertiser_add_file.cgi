#!/usr/bin/perl
#===============================================================================
# File   : advertiser_add_file.cgi
#
#
#--Change Control---------------------------------------------------------------
#===============================================================================

#-----------------------
# include Perl Modules
#-----------------------
use strict;
use CGI qw(:standard);
use util;
use File::Copy;

$|=1 ;   # set OUTPUT_AUTOFLUSH to true

my $util = util->new;
my $query = new CGI;
my $dbh;
my $sql;
my $sth;
my $sth1;
my $BASE_DIR;
my $list_name;
my $images = $util->get_images_url;
my $alt_light_table_bg = $util->get_alt_light_table_bg;
my $light_table_bg = $util->get_light_table_bg;
my $table_text_color = $util->get_table_text_color;
my $errmsg;
my $up_file;
my $aid = $query->param('aid');
my $add_sub_dir;
my (@temp_file, %confirm);
my ($file_name, $file_handle, $file_problem) ;
my ($BytesRead, $Buffer, $Bytes ) ;
my $tmp_file;
my $email_addr;

# ----- connect to the util database -------
my ($dbhq,$dbhu)=$util->get_dbh();

# ----- check for login -------

my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}

# get subdir to add files to
$add_sub_dir = "/home/adv/".$aid."/"; 
mkdir $add_sub_dir;
chmod(0777,$add_sub_dir);

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
unless (open(OUTFILE, ">${add_sub_dir}${up_file}")) 
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
print "Location: advertiser_upload.cgi?aid=$aid\n\n";
