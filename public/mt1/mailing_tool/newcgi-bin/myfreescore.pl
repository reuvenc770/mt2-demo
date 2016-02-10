#!/usr/bin/perl

use Net::SFTP::Foreign;
use Net::FTP;
use lib "/var/www/html/newcgi-bin";
use util;

$|=1 ;   # set OUTPUT_AUTOFLUSH to true

my $util = util->new;
($dbhq,$dbhu)=$util->get_dbh();

my $server="secure.pic-online.com";
my $host="ftp.aspiremail.com";
#
# Get all the optin files and copied to ftp on ftp.aspiremail.com
#
my %args = (
    user => 'Zeta', 
    password => 'EfyWicYMU1' 
);
my $sftp = Net::SFTP::Foreign->new($server, %args);
$sftp->setcwd("Output/OptIn");
my @remote_files=@{$sftp->ls()};
foreach my $f (@remote_files)
{
	$_=$f->{filename};
	if (/MFSNOptin_/)
	{
		$sftp->get($f->{filename});
		print "Getting $f->{filename} - ".$sftp->status()."\n";
		my $status=$sftp->status();
		$ftp = Net::FTP->new("$host", Timeout => 20, Debug => 0, Passive => 0) or print "Cannot connect to $host: $@\n";
		if ($ftp)
		{
    		$ftp->login('orangemyfreescorenow','MgePmAJV') or print "Cannot login ", $ftp->message;
    		$ftp->ascii();
			$ftp->put($f->{filename});
			$sftp->remove($f->{filename});
			print "Removed $f->{filename} - ".$sftp->status()."\n";
		}
	}
}
$sftp->disconnect;
#
# get Optout files and remove them from client 2608
#
$sftp = Net::SFTP::Foreign->new($server, %args);
$sftp->setcwd("Output/OptOut");
my @remote_files=@{$sftp->ls()};
foreach my $f (@remote_files)
{
	$_=$f->{filename};
	if (/MFSNZetaOptOut/)
	{
		$sftp->get($f->{filename});
		print "Getting $f->{filename} - ".$sftp->status()."\n";
		my $status=$sftp->status();
		$sftp->remove($f->{filename});
		print "Removed $f->{filename} - ".$sftp->status()."\n";
		open(IN,"<$f->{filename}");
		while (<IN>)
		{
			my $line=$_;
			my @flds=split(",",$line);
			my $em=$flds[4];
			if ($em ne "Email")
			{
				print "Unsubbing $em\n";
				uns_upd_list_member($em);
			}
		}
		close(IN);
	}
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
	$sql = "select email_user_id,email_list.status,client_id from email_list where email_addr= '$email_addr' and client_id=2608"; 
	$sth2 = $dbhq->prepare($sql) ;
	$sth2->execute();
	while (($emailid,$cstatus,$user_id) = $sth2->fetchrow_array())
	{
		if ($cstatus eq "A")
		{
			$sql = "update email_list set status = 'U', unsubscribe_date=curdate(),unsubscribe_time=curtime() where email_user_id=$emailid and status ='A'"; 
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
} # end of sub
