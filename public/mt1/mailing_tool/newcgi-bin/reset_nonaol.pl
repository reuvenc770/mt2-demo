#!/usr/bin/perl
#-----------------------
# include Perl Modules
#-----------------------
use strict;
use CGI;
use lib "/var/www/html/newcgi-bin";
use util;

$|=1 ;   # set OUTPUT_AUTOFLUSH to true

my $util = util->new;
#my $query = CGI->new;
my $dbh;
my $rows;
my $sql;
my (@list_array, $upload_file, $email_list_text_area) ;
my ($list_id, $list_name, $email_addr, @email_array);
my (%hash_good_cnt, %hash_bad_cnt, %hash_prev_rem_cnt) ;
my $alt_light_table_bg = $util->get_alt_light_table_bg;
my $images = $util->get_images_url;
my $email_user_id;
my $total_cnt;
my $total_unscnt;
my $total_alreadycnt;



# ----- connect to the util database -------
my $dbhq;
my $dbhu;
($dbhq,$dbhu)=$util->get_dbh();

	&process_file() ;

$util->clean_up();
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

	$file_in = "/var/www/html/newcgi-bin/aol_non.txt"; 
	open(SAVED,"<$file_in") || &util::logerror("Error - could NOT open Input SAVED file: $file_in");

	#----- Loop Reading the File of Email Addrs - do til EOF ------------------
	$total_cnt = 0;
	$total_unscnt = 0;
	$total_alreadycnt = 0;
	while (<SAVED>) 
	{
		chomp;                       # remove Carriage Return (if exists)
		$line = $_;
		$line =~ s///g ;      # remove ^M from Email Addr (if exists)
		$line =~ s/\t/|/g ;
		$line =~ s/,/|/g ;
		($email_addr, @rest_of_line) = split('\|', $line) ;
		$total_cnt++;
		&uns_upd_list_member($email_addr);
	} 

	close SAVED;
} # end of sub


#===============================================================================
# Sub: uns_upd_list_member
#===============================================================================
sub uns_upd_list_member
{
	my ($email_addr) = @_ ;
	my ($email_exists, $sth1, $sth2, $status, $i,$emailid,$list_id,$cstatus) ; 
	my $user_id;
	my $nonaol_id;
	
	# ---- See if Email Addr Already Exists for specified List -----
	$sql = "select email_user_id,user_id from email_list,list where email_addr= '$email_addr' and email_list.list_id=list.list_id and list_type='BLOCK'";
	$sth2 = $dbhq->prepare($sql) ;
	$sth2->execute();
	while (($emailid,$user_id) = $sth2->fetchrow_array())
	{
		$sql="select list_id from list where user_id=$user_id and list_name='AOL-non clickers'";
		my $sth1 = $dbhq->prepare($sql) ;
		$sth1->execute();
		($nonaol_id) = $sth1->fetchrow_array();
		$sth1->finish();
		print "Moving $email_addr to $nonaol_id for user $user_id\n";
		$sql = "update email_list set list_id=$nonaol_id where email_user_id=$emailid"; 
		$rows = $dbhu->do($sql) ;
	}
	$sth2->finish();
	
} # end of sub


