#!/usr/bin/perl
#===============================================================================
# File   : upload_trigger_csv.cgi 
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
my $upload_file = $query->param('upload_file');
my ($dbhq,$dbhu)=$util->get_dbh();

#----- Pass control to PROCESS_FILE  or  PROCESS_LIST  -------
if ( $upload_file ne "" ) 
{
	&process_file() ;
}
print "Location: trigger_client.cgi\n\n";
exit(0) ;


sub process_file 
{
	my ($file_name, $file_handle, $file_problem, $file_in, $line, @rest_of_line);
	my $sql;
	my $sth;
	my $sth1;
	my $upload_dir_unix;
	my ($client_id,$slot_id,$cday,$aid);
	my $dd_id;
	my $usa_id;
	my $uname;
	my $camp_id;
	my $old_aid;
	my $creative;
	my $subject;
	my $from;
	my $old_creative;
	my $old_subject;
	my $old_from;
	my $bid=4581;
	my @restline;
	my $cname;
	my $tname;
	my $ttype;
	my $fld;

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

	$file_in = "${upload_dir_unix}triggerlist.${user_id}" ;
	open(SAVED,">$file_in") || &logerror("Error - could NOT open Output SAVED file: $file_in");
	$file_handle = $upload_file ;
	print SAVED <$file_handle> ;
	close SAVED;

	open(SAVED,"<$file_in") || &logerror("Error - could NOT open Input SAVED file: $file_in");
	while (<SAVED>) 
	{
		chomp;                       # remove Carriage Return (if exists)
		$line = $_;
		$line =~ s///g ;      # remove ^M from Email Addr (if exists)
		$line =~ s/"//g ;
		$line =~ s/\t/|/g ;
		$line =~ s/,/|/g ;
		($cname,$tname,$ttype,@restline) = split('\|', $line) ;
		$sql="select user_id from user where first_name=?";
		$sth=$dbhu->prepare($sql);
		$sth->execute($cname);
		if (($client_id)=$sth->fetchrow_array())
		{
		}
		else
		{
			next;
		}
		$sth->finish();

		$sql="select dd_id from DailyDealSetting where settingType='Trigger' and name=?"; 
		$sth=$dbhu->prepare($sql);
		$sth->execute($tname);
		if (($dd_id)=$sth->fetchrow_array())
		{
		}
		else
		{
			next;
		}
		$ttype=~tr/A-Z/a-z/;
		if (($ttype eq "") or ($ttype eq "click"))
		{
			$fld="dd_id";
		}
		elsif ($ttype eq "open")
		{
			$fld="dd_id_open";
		}
		elsif ($ttype eq "conversion")
		{
			$fld="dd_id_conversion";
		}
		else
		{
			next;
		}
        $sql="update user set $fld=$dd_id where user_id=$client_id"; 
        $rows=$dbhu->do($sql);
	} 
	close SAVED;
	unlink($file_in) || &logerror("Error - could NOT Remove file: $file_in");  # del file_in

} # end of sub

