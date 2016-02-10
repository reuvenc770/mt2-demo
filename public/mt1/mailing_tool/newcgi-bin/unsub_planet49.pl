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
my $total_cnt;
my $cdate;
my $total_unscnt;
my $total_alreadycnt;
my $sth;
my $em;
my $ftp;

($dbhq,$dbhu)=$util->get_dbh();
my $browser=LWP::UserAgent->new;

$sql="select date_format(date_sub(curdate(),interval 1 day),'%m_%d')";
$sth=$dbhu->prepare($sql);
$sth->execute();
($cdate)=$sth->fetchrow_array();
$sth->finish();
$sql="select email_addr from unsub_log where client_id in (726,1012) and unsub_date >= date_sub(curdate(),interval 1 day) and unsub_date < curdate()";
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($em)=$sth->fetchrow_array())
{
	my $url2="http://www.planet49.co.uk/cgi-bin/u.pl?{".uri_escape($em);
	my $response=$browser->get($url2);
#	print "Response: ".$response->content;
}
$sth->finish();
my $host = "ftp.aspiremail.com";
$ftp = Net::FTP->new("$host", Timeout => 20, Debug => 0, Passive => 0) or print "Cannot connect to $host: $@\n";
if ($ftp)
{
    $ftp->login('P49unsub','rCtentKp') or print "Cannot login ", $ftp->message;
    $ftp->ascii();
    $ftp->cwd("P49Unsubs");
    foreach my $file($ftp->ls)
    {
		$ftp->get($file) or die "Get Failed ",$ftp->message;
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
			$total_cnt++;
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
	$sql = "select email_user_id,email_list.status,client_id from email_list where email_addr= '$email_addr'"; 
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
		}
	}
	$sth2->finish();
	util::addGlobal($email_addr);
} # end of sub
