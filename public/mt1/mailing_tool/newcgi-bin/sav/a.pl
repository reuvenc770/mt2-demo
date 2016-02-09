#!/usr/bin/perl
#===============================================================================
# File   : proc_tanis.pl 
#
#--Change Control---------------------------------------------------------------
#  Sept 30, 2002  Jim Sobeck Created.
#===============================================================================

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
my $total_cnt;
my $total_found;



# ----- connect to the pms database -------
$pms->db_connect();
$dbh = $pms->get_dbh;

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

	$sql = "select parmval from sysparm where parmkey = 'UPLOAD_DIR_UNIX'";
	$sth1 = $dbh->prepare($sql) ;
	$sth1->execute();
	($upload_dir_unix) = $sth1->fetchrow_array();
	$sth1->finish();

	$file_in = "/var/jim/tanis/subscribers.15.export" ;
	open(SAVED,"<$file_in") || &pms::logerror("Error - could NOT open Input SAVED file: $file_in");

	#----- Loop Reading the File of Email Addrs - do til EOF ------------------
	$total_cnt = 0;
	$total_found = 0;
	open (LOG, ">> /tmp/tanis1.log");
	while (<SAVED>) 
	{
		chomp;                       # remove Carriage Return (if exists)
		$line = $_;
		$line =~ s///g ;      # remove ^M from Email Addr (if exists)
		$line =~ s/\t/|/g ;
		$line =~ s/,/|/g ;
		($email_addr, @rest_of_line) = split('\|', $line) ;
		$email_addr =~ s/\s.*$// ;   # remove from 1st white space at end of addr thru end of line
		$email_addr =~ s/\s//g ;     # remove all white space global
		
		$total_cnt++;
		if (($total_cnt%1000) == 0)
		{
			print LOG "Processed $total_cnt records - $email_addr\n";
		}
#		print LOG "Processing $email_addr\n";
		&uns_upd_list_member($email_addr);
	} 

	close SAVED;
	print LOG "Total Count = $total_cnt\n";
	print LOG  "Total Found Count = $total_found\n";
	close LOG;
} # end of sub


#===============================================================================
# Sub: uns_upd_list_member
#===============================================================================
sub uns_upd_list_member
{
	my ($email_addr) = @_ ;
	my ($email_exists, $sth1, $sth2, $status, $i,$emailid,$list_id, $estatus) ; 
	
	$sql = "insert into member_list(list_id,email_addr,subscribe_datetime,status) values(56,'$email_addr',now(),'A')";
	$rows = $dbh->do($sql) ;
	$total_found++;
} # end of sub
