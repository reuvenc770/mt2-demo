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
my $total_cnt;
my $total_unscnt;
my $total_alreadycnt;



# ----- connect to the pms database -------
$pms->db_connect();
$dbh = $pms->get_dbh;

# ----- check for login -------
my $user_id = 1; 
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
	my ($list_id, $email_addr, @email_array,$email_id) ;
	my ($status, $email_exists, $sth1, $sth2 ) ;
	my ($file_name, $file_handle, $file_problem, $file_in, $line, @rest_of_line);
	my ($ary_len, $i);
	my $upload_dir_unix;

	$file_in = "chk.txt" ;
	open(SAVED,"<$file_in") || &pms::logerror("Error - could NOT open Input SAVED file: $file_in");

	#----- Loop Reading the File of Email Addrs - do til EOF ------------------
	$total_cnt = 0;
	$total_unscnt = 0;
	$total_alreadycnt = 0;
	open (LOG, "> /tmp/chk.log");
	while (<SAVED>) 
	{
		chomp;                       # remove Carriage Return (if exists)
		$line = $_;
		$line =~ s///g ;      # remove ^M from Email Addr (if exists)
		$line =~ s/\t/|/g ;
		$line =~ s/,/|/g ;
		($email_id, $email_addr) = split('\|', $line) ;
		$email_addr =~ s/\s.*$// ;   # remove from 1st white space at end of addr thru end of line
		$email_addr =~ s/\s//g ;     # remove all white space global
		
		$total_cnt++;
		&uns_upd_list_member($email_addr);
	} 

	close SAVED;
	close LOG;
	print "Total Count = $total_cnt\n";
	print "Total Unsubscribe Count = $total_unscnt\n";
	print "Total Already Unsubscribed Count = $total_alreadycnt\n";
#	unlink($file_in) || &pms::logerror("Error - could NOT Remove file: $file_in");  # del file_in

} # end of sub


#===============================================================================
# Sub: uns_upd_list_member
#===============================================================================
sub uns_upd_list_member
{
	my ($email_addr) = @_ ;
	my ($email_exists, $sth1, $sth2, $status, $i,$emailid,$list_id) ; 
	
	# ---- See if Email Addr Already Exists for specified List -----

	$email_exists = 0 ;
	$sql = "select list_member.list_id,list_member.email_user_id,list_member.status from list_member, email_user
		where 
		list_member.email_user_id = email_user.email_user_id and
		email_user.email_addr = '$email_addr' and list_id=9 and list_member.status='A'";
	$sth1 = $dbh->prepare($sql) ;
	$sth1->execute();
	while (($list_id,$emailid,$status) = $sth1->fetchrow_array())
	{
		if ($status eq "A")
		{
			$sql = "update list_member set status = 'U',unsubscribe_datetime=now()
				where list_id = 9 and email_user_id = $emailid";
			$rows = $dbh->do($sql) ;
			$total_unscnt++;
			print "Removing: $email_addr\n";
		}
	}
	$sth1->finish();
} # end of sub



#===============================================================================
# Sub: uns_upd_email_user
#   If user is NOT active in any 'list_member' rec then set 'email_user' status
#   to logical delete.
#===============================================================================

sub uns_upd_email_user
{
	my ($email_addr) = @_ ;

	$sql = "update email_user set status = 'D' where email_addr = '$email_addr'";
	$rows = $dbh->do($sql);
} 
