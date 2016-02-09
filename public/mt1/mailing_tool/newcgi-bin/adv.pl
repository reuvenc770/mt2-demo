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
my $not_foundcnt;
my $total_unscnt;
my $total_alreadycnt;



# ----- connect to the util database -------
my $dbhq;
my $dbhu;
($dbhq,$dbhu)=$util->get_dbh();

# ----- check for login -------
my $user_id = 1; 
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}

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
	my ($status, $email_exists, $sth1, $sth2 ) ;
	my ($file_name, $file_handle, $file_problem, $file_in, $line, @rest_of_line);
	my ($adv_name,$aemail_addr,$primary,$phone,$email_addr,$address,$company_name,$website,$username,$password,$category,$status,$type,$payout,$supp_url,$supp_username,$supp_password,$approval); 
	my ($ary_len, $i);
	my $upload_dir_unix;
	my $cid;
	my $aid;

	# get upload subdir

	$sql = "select parmval from sysparm where parmkey = 'UPLOAD_DIR_UNIX'";
	$sth1 = $dbhq->prepare($sql) ;
	$sth1->execute();
	($upload_dir_unix) = $sth1->fetchrow_array();
	$sth1->finish();

	$file_in = $ARGV[0] ;
	open(SAVED,"<$file_in") || &util::logerror("Error - could NOT open Input SAVED file: $file_in");

	#----- Loop Reading the File of Email Addrs - do til EOF ------------------
	$total_cnt = 0;
	$total_unscnt = 0;
	$total_alreadycnt = 0;
	$not_foundcnt = 0;
	while (<SAVED>) 
	{
		chomp;                       # remove Carriage Return (if exists)
		$line = $_;
		$line =~ s///g ;      # remove ^M from Email Addr (if exists)
		$line =~ s/\t/|/g ;
#		$line =~ s/,/|/g ;
		($adv_name,$email_addr,$primary,$phone,$aemail_addr,$address,$company_name,$website,$username,$password,$category,$status,$type,$payout,$supp_url,$supp_username,$supp_password,$approval) = split('\|', $line) ;
		$email_addr =~ s/\s.*$// ;   # remove from 1st white space at end of addr thru end of line
		$email_addr =~ s/\s//g ;     # remove all white space global
		$total_cnt++;
		$payout =~ s/ //g;
		$payout =~ s/\$//g;
		$adv_name =~ s/'/''/g;
	$sql = "select category_id from category_info where category_name='$category'"; 
	$sth1 = $dbhq->prepare($sql) ;
	$sth1->execute();
	if (($cid) = $sth1->fetchrow_array())
	{
	}
	else
	{
		$cid = 58;
	}
	$sth1->finish();
		if ($adv_name ne "")
		{
		my $sql = "insert into advertiser_info(advertiser_name,email_addr,physical_addr,status,offer_type,payout,suppression_url,suppression_username,suppression_password,category_id) values('$adv_name','$email_addr','$address','A','$type','$payout','$supp_url','$supp_username','$supp_password',$cid)";
		$rows = $dbhu->do($sql) ;
	$sql = "select advertiser_id from advertiser_info where advertiser_name='$adv_name'"; 
	$sth1 = $dbhq->prepare($sql) ;
	$sth1->execute();
	($aid) = $sth1->fetchrow_array();
	$sth1->finish();
		
		$sql = "insert into advertiser_contact_info(advertiser_id,contact_name,contact_phone,contact_email,contact_company,contact_website,contact_username,contact_password) values($aid,'$primary','$phone','$aemail_addr','$company_name','$website','$username','$password')";
		$rows = $dbhu->do($sql) ;
		$sql = "insert into advertiser_approval values($aid,'$approval')";
		$rows = $dbhu->do($sql) ;
		}
	} 

	close SAVED;

} # end of sub
