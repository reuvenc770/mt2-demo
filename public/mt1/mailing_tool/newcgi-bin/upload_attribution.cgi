#!/usr/bin/perl
#===============================================================================
# File   : upload_ipgroup.cgi 
#
#--Change Control---------------------------------------------------------------
#===============================================================================

#-----------------------
# include Perl Modules
#-----------------------
use strict;
use CGI;
use util;

$|=1 ;   # set OUTPUT_AUTOFLUSH to true

my $util = util->new;
my $query = CGI->new;
my $dbh;
my $rows;
my $sql;
my $alt_light_table_bg = $util->get_alt_light_table_bg;
my $images = $util->get_images_url;


# ------- Get fields from html Form post -----------------


# ----- check for login -------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}
my $username;
my $setAttribution;
my $ctime;
my ($dbhq,$dbhu)=$util->get_dbh();

$sql = "select username, setAttribution,now() from UserAccounts where user_id = ?";
my $sth = $dbhq->prepare($sql) ;
$sth->execute($user_id);
($username, $setAttribution,$ctime) = $sth->fetchrow_array();
$sth->finish();
if ($setAttribution eq "N")
{
	open(LOG2,">>/tmp/attribution.log");
    print LOG2 "$ctime - $username\n";
    close(LOG2);
    print "Content-type: text/html\n\n";
    print<<"end_of_html";
<html><head><title>Attribution Error</title></head>
<body>
<center><h3>You do not have permission to set Client Attribution.  This attempt has been logged.</h3><br>
<a href="/cgi-bin/mainmenu.cgi"><img src="/images/home_blkline.gif" border=0></a>
</center>
</body>
</html>
end_of_html
	exit(0);
}
my $upload_file = $query->param('upload_file');

#----- Pass control to PROCESS_FILE  or  PROCESS_LIST  -------
if ( $upload_file ne "" ) 
{
	&process_file() ;
}
print "Location: unique_attribution.cgi\n\n";
exit(0) ;


sub process_file 
{
	my ($file_name, $file_handle, $file_problem, $file_in, $line, @rest_of_line);
	my $sql;
	my $sth;
	my $sth1;
	my @MDOM;
	my $upload_dir_unix;
	my ($attr_id,$client_id,@rest_of_line);

	# get upload subdir
	$sql = "select parmval from sysparm where parmkey = 'UPLOAD_DIR_UNIX'";
	$sth1 = $dbhq->prepare($sql) ;
	$sth1->execute();
	($upload_dir_unix) = $sth1->fetchrow_array();
	$sth1->finish();

	# deal with filename passed to this script

	if ( $upload_file =~ /([^\/\\]+)$/ ) 
	{
		$file_name = $1;                # set file_name to $1 var - (file-name no path)
		$file_name =~ s/^\.+//;         # say what...
		$file_name =~ s/\s/_/g;         # replace WhiteSpace with UnderScore global
		$file_handle = $upload_file ;
	}
	else 
	{
		$file_problem = $query->param('upfile');
		&error("Bad File Name: $file_problem, File name can't have a slash in it!\n Rename it and try again!" ) ;
		exit(0);
	}

	#---- Open file and save File to Unix box ---------------------------

	$file_in = "${upload_dir_unix}attrib.${user_id}" ;
	open(SAVED,">$file_in") || &logerror("Error - could NOT open Output SAVED file: $file_in");
	$file_handle = $upload_file ;
	print SAVED <$file_handle> ;
	close SAVED;

    my ($sec, $min, $hr, $day, $month, $year, $wkdy, $yrdy, $isDST)=localtime();
    $month+=1; $year+=1900;
    open(LOG,">>/tmp/upload_attribution_$month$day$year.log");
    print LOG "$hr:$sec - $user_id\n";
	$sql="delete from UniqueAttribution"; 
	$rows=$dbhu->do($sql);

	open(SAVED,"<$file_in") || &logerror("Error - could NOT open Input SAVED file: $file_in");
	while (<SAVED>) 
	{
		chomp;                       # remove Carriage Return (if exists)
		$line = $_;
		$line =~ s///g ;      # remove ^M from Email Addr (if exists)
		$line =~ s/\t/|/g ;
		$line =~ s/,/|/g ;
    	print LOG "$hr:$sec - $user_id <$line>\n";
		($attr_id,$client_id,@rest_of_line) = split('\|', $line) ;
		$sql="insert into UniqueAttribution(client_id,level) values($client_id,$attr_id)";
		$rows=$dbhu->do($sql);
	} 
	close SAVED;
	close LOG;
	unlink($file_in) || &logerror("Error - could NOT Remove file: $file_in");  # del file_in

} # end of sub
