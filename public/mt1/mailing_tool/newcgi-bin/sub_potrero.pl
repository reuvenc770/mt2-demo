#!/usr/bin/perl
#===============================================================================
# Purpose: Logical Unsubscribe of 'list_member' recs.
# File   : sub_uns.cgi
#
#--Change Control---------------------------------------------------------------
#  Aug 2, 2001  Mike Baker  Created.
#  Feb 8, 2002  Jim Sobeck  Add logging of emails not removed
#===============================================================================

#-----------------------
# include Perl Modules
#-----------------------
use strict;
use CGI;
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
my $UNS;



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

	$file_in = $ARGV[0] ;
	open(SAVED,"<$file_in") || &util::logerror("Error - could NOT open Input SAVED file: $file_in");

	#----- Loop Reading the File of Email Addrs - do til EOF ------------------
	$total_cnt = 0;
	$total_unscnt = 0;
	$total_alreadycnt = 0;
	open (LOG, ">> /tmp/sub_uns.log");
	while (<SAVED>) 
	{
		chomp;                       # remove Carriage Return (if exists)
		$line = $_;
		$line =~ s/
//g ;      # remove ^M from Email Addr (if exists)
		$line =~ s/\t/|/g ;
		$line =~ s/,/|/g ;
		($email_addr, @rest_of_line) = split('\|', $line) ;
#		$email_addr =~ s/\s.*$// ;   # remove from 1st white space at end of addr thru end of line
		$email_addr =~ s/ //g ;     # remove all white space global
		$email_addr =~ s/"//g;
		$email_addr=~s///g;
		$email_addr=~s/\n//g;
		print "<$email_addr>\n";
		
		$total_cnt++;
		&uns_upd_list_member($email_addr);
	} 

	close SAVED;
	close LOG;
	print "Total Count = $total_cnt\n";
	print "Total Unsubscribe Count = $total_unscnt\n";
	print "Total Already Unsubscribed Count = $total_alreadycnt\n";
	unlink($file_in) || &util::logerror("Error - could NOT Remove file: $file_in");  # del file_in
	foreach (keys %{$UNS})
	{
		my $cid=$_;
		foreach (keys %{$UNS->{$cid}})
		{
			my $ltype=$_;
			print "$cid,$ltype,$UNS->{$cid}{$ltype}\n";
		}
	}

} # end of sub


#===============================================================================
# Sub: uns_upd_list_member
#===============================================================================
sub uns_upd_list_member
{
	my ($email_addr) = @_ ;
	my ($email_exists, $sth1, $sth2, $status, $i,$emailid,$list_id,$cstatus) ; 
	my $lid;
	my $user_id;
	my $ltype;

	my $emailTable;	
	# ---- See if Email Addr Already Exists for specified List -----

	$email_addr =~ s/'/''/g;
	$email_exists = 0 ;
	$sql = "select email_user_id,email_list.status,client_id from email_list where email_addr= '$email_addr' and client_id in (703,704,705,706,707,708)";
	$sth2 = $dbhq->prepare($sql) ;
	$sth2->execute();
	while (($emailid,$cstatus,$user_id) = $sth2->fetchrow_array())
	{
		if (($cstatus eq "A") || ($cstatus eq "P") || ($cstatus eq "G") || ($cstatus eq "L"))
		{
			$sql = "update email_list set status = 'U', unsubscribe_date=curdate(),unsubscribe_time=curtime() where email_user_id=$emailid and status in ('A','P')"; 
			$rows = $dbhu->do($sql) ;
			
			$sql = "insert into unsub_log(email_addr,unsub_date,client_id) values('$email_addr',curdate(),$user_id)";
			$rows = $dbhu->do($sql);
			
			$total_unscnt++;
			print "Removed $email_addr\n";
		}
		else
		{
			$total_alreadycnt++;
#			print "Already removed $email_addr\n";
		}
	}
	$sth2->finish();

#	util::addGlobal($email_addr);
} # end of sub



