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
use URI::Escape;
use LWP 5.64;
use lib "/var/www/html/newcgi-bin";
use util;

$|=1 ;   # set OUTPUT_AUTOFLUSH to true

my $util = util->new;
my $dbhu;
my $dbhq;
my $rows;
my $sql;
my $sth;
my $em;
my $ftp;

($dbhq,$dbhu)=$util->get_dbh();

my $host = "54.186.245.168";
my $username="mailingtool";
my $password="1nt3l@";
$ftp = Net::FTP->new("$host", Timeout => 20, Debug => 0, Passive => 0) or print "Cannot connect to $host: $@\n";
if ($ftp)
{
    $ftp->login($username,$password) or print "Cannot login ", $ftp->message;
    $ftp->ascii();
    $ftp->cwd("unsubs");
    $ftp->cwd("outgoing");
    foreach my $file($ftp->ls)
    {
		if (($file eq ".") or ($file eq ".."))
		{
			next;
		}
		$ftp->get($file) or die "Get Failed ",$ftp->message;
		print "Got file: $file\n";
		process_file($file);
		$ftp->delete($file);
	}
    $ftp->quit;
}

exit(0) ;
sub process_file
{
	my ($file)=@_;
	my $line;
	my $email_addr;
	my @rest_of_line;

	open(SAVED,"<$file");
	while (<SAVED>) 
	{
		chomp;                       # remove Carriage Return (if exists)
		$line = $_;
		$line =~ s/\t/|/g ;
		$line =~ s/,/|/g ;
		$line =~ s/"//g ;
		($email_addr, @rest_of_line) = split(';', $line) ;
		$email_addr =~s/ //g;
		$_=$email_addr;
		if (/\@/)
		{
			print "<$email_addr>\n";
			&uns_upd_list_member($email_addr);
		}
	} 

	close SAVED;
}
sub uns_upd_list_member
{
	my ($email_addr) = @_ ;
	my ($email_exists, $sth1, $sth2, $status, $i,$emailid,$list_id,$cstatus) ; 
	my $user_id;

	my $emailTable;	
	# ---- See if Email Addr Already Exists for specified List -----

	$email_addr =~ s/'/''/g;
	$email_exists = 0 ;
	$sql = "select email_user_id,client_id from email_list where email_addr= '$email_addr' and status='A'"; 
	$sth2 = $dbhq->prepare($sql) ;
	$sth2->execute();
	while (($emailid,$user_id) = $sth2->fetchrow_array())
	{
		$sql = "update email_list set status = 'U', unsubscribe_date=curdate(),unsubscribe_time=curtime() where email_user_id=$emailid and status='A'"; 
		$rows = $dbhu->do($sql) ;
		
		$sql = "insert into unsub_log(email_addr,unsub_date,client_id) values('$email_addr',curdate(),$user_id)";
		$rows = $dbhu->do($sql);
			
		print "Removed $email_addr\n";
	}
	$sth2->finish();
} # end of sub
