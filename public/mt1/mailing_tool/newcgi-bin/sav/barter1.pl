#!/usr/bin/perl
#-----------------------
# include Perl Modules
#-----------------------
use strict;
use CGI;
use pms;

$|=1 ;   # set OUTPUT_AUTOFLUSH to true

my $pms = pms->new;
#my $query = CGI->new;
my $dbh;
my $rows;
my $sql;
my (@list_array, $upload_file, $email_list_text_area) ;
my ($list_id, $list_name, $email_addr, @email_array);
my (%hash_good_cnt, %hash_bad_cnt, %hash_prev_rem_cnt) ;
my $alt_light_table_bg = $pms->get_alt_light_table_bg;
my $images = $pms->get_images_url;
my $email_user_id;
my $uns_cnt;
my $already_cnt;
my $total_cnt;



# ----- connect to the pms database -------
$pms->db_connect();
$dbh = $pms->get_dbh;

# ----- check for login -------
my $user_id = 1; 
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $pms->clean_up();
    exit(0);
}

	&process_file() ;

$pms->clean_up();
exit(0) ;



#===============================================================================
# Sub: process_file
#  1. Open file
#  2. Loop - Read File til EOF 
#  3. Update 'list_member' for Logical Delete/Remove (eg set status = R )
#      - set proper counts (eg good, bad, total)
#===============================================================================
sub process_file 
{
	my ($list_id, $email_addr, @email_array) ;
	my ($status, $email_exists, $sth1, $sth2 ) ;
	my ($file_name, $file_handle, $file_problem, $file_in, $line, @rest_of_line);
	my ($ary_len, $i);
	my $upload_dir_unix;

	# get upload subdir
	$total_cnt = 0;
	$uns_cnt = 0;
	$already_cnt = 0;
	$file_in = "barter.txt" ;
	open(SAVED,"<$file_in") || &pms::logerror("Error - could NOT open Input SAVED file: $file_in");

	#----- Loop Reading the File of Email Addrs - do til EOF ------------------
	open (LOG, ">> /var/www/pms/uploads/new_barter.txt");
	while (<SAVED>) 
	{
		chomp;                       # remove Carriage Return (if exists)
		$line = $_;
		$line =~ s///g ;      # remove ^M from Email Addr (if exists)
		$line =~ s/\t/|/g ;
		$line =~ s/,/|/g ;
		($email_addr, @rest_of_line) = split('\|', $line) ;
		$email_addr =~ s/\s.*$// ;   # remove from 1st white space at end of addr thru end of line
#		$email_addr =~ s/\s//g ;     # remove all white space global
		
		&uns_upd_list_member($email_addr);
	} 

	close SAVED;
	close LOG;

} # end of sub


#===============================================================================
# Sub: uns_upd_list_member
#===============================================================================
sub uns_upd_list_member
{
	my ($email_addr) = @_ ;
	print "Getting info for $email_addr\n";
	my ($email_exists, $sth1, $sth2, $status, $i,$emailid,$list_id) ; 
	my ($fname,$lname,$addr,$city,$state,$zip,$gender);
	
	# ---- See if Email Addr Already Exists for specified List -----

	$sql = "select first_name,last_name,address,city,state,zip,gender from email_user where email_addr = '$email_addr'"; 
	$sth1 = $dbh->prepare($sql) ;
	$sth1->execute();
	if (($fname,$lname,$addr,$city,$state,$zip,$gender) = $sth1->fetchrow_array())
	{
			printf LOG "%s|%s|%s|%s|%s|%s|%s|%s\n",$email_addr,$fname,$lname,$addr,$city,$state,$zip,$gender;
	}
	$sth1->finish();
} # end of sub
