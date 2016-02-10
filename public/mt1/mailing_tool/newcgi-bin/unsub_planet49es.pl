#!/usr/bin/perl
#===============================================================================
#
#--Change Control---------------------------------------------------------------
#===============================================================================

#-----------------------
# include Perl Modules
#-----------------------
use strict;
use Net::FTP;
use lib "/var/www/html/newcgi-bin";
use util;

$|=1 ;   # set OUTPUT_AUTOFLUSH to true

my $util = util->new;
my $dbhu;
my $dbhq;
my $rows;
my $sql;
my $total_cnt;
my $cdate;
my $cdate1;
my $total_unscnt;
my $total_alreadycnt;
my $sth;
my $em;
my $ftp;
my $dayint;

($dbhq,$dbhu)=$util->get_dbh();

$sql="select date_format(date_sub(curdate(),interval 1 day),'%m%d%Y'),date_format(date_sub(curdate(),interval 1 day),'%m%d')";
$sth=$dbhu->prepare($sql);
$sth->execute();
($cdate,$cdate1)=$sth->fetchrow_array();
$sth->finish();

my $filename="XLUnsub_".$cdate.".txt";
my $infile="Planet49es_unsub_".$cdate1.".txt";
open(LOG,">XLUnsub_$cdate.txt");
$sql="select email_addr from unsub_log where client_id =1123 and unsub_date >= date_sub(curdate(),interval 7 day) and unsub_date < curdate()";
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($em)=$sth->fetchrow_array())
{
	print LOG "$em\n";
}
$sth->finish();
close(LOG);
my $host = "ftp.aspiremail.com";
$ftp = Net::FTP->new("$host", Timeout => 20, Debug => 0, Passive => 0) or print "Cannot connect to $host: $@\n";
if ($ftp)
{
    $ftp->login('Planet49ESUnsub','yfKCg6Mu') or print "Cannot login ", $ftp->message;
    $ftp->ascii();
	$ftp->cwd("Incoming");
	$ftp->get($infile);
	$ftp->cwd("../Outgoing");
    $ftp->put($filename) or print "put failed ", $ftp->message;
    print "Sent $filename\n";
    $ftp->quit;
}
&process_file($infile) ;

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
	my ($file_in)=@_;
	my ($list_id, $email_addr, @email_array) ;
	my ($status, $email_exists, $sth1, $sth2 ) ;
	my ($file_name, $file_handle, $file_problem, $line, @rest_of_line);
	my ($ary_len, $i);

	open(SAVED,"<$file_in") || &util::logerror("Error - could NOT open Input SAVED file: $file_in");

	#----- Loop Reading the File of Email Addrs - do til EOF ------------------
	$total_cnt = 0;
	$total_unscnt = 0;
	$total_alreadycnt = 0;
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
	print "Total Count = $total_cnt\n";
	print "Total Unsubscribe Count = $total_unscnt\n";
	print "Total Already Unsubscribed Count = $total_alreadycnt\n";
	unlink($file_in) || &util::logerror("Error - could NOT Remove file: $file_in");  # del file_in
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
	$sql = "select email_user_id,status,client_id from email_list where email_addr= '$email_addr' and client_id = 1123";
	$sth2 = $dbhu->prepare($sql) ;
	$sth2->execute();
	while (($emailid,$cstatus,$user_id) = $sth2->fetchrow_array())
	{
		if ($cstatus eq "A")
		{
			my $emailList = 'email_list';
			
			$sql = "update $emailList set status = 'U', unsubscribe_date=curdate(),unsubscribe_time=curtime() where email_user_id=$emailid and status ='A'"; 
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
} # end of sub



