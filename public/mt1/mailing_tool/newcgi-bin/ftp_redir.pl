#!/usr/bin/perl

use Net::FTP;
use lib "/var/www/html/newcgi-bin";
use util;

my $util = util->new;
my $dbh;
my $sql;
my $sth;
my $some_dir="/var/www/html/logs";
my $server;
my $username;
my $to_dir;

my $dbhq;
my $dbhu;
($dbhq,$dbhu)=$util->get_dbh();

my $data=`ls -al $some_dir/redir.dat`;
$data=~s/\n|\r//;
my @data=split(' ', $data);
 
if ($data[4] < 1000) {  ## redir.dat less than 1000 bytes ##
	sendmail();
}
else {
	$username="transferdat";
	$sql = "select server from server_config where inService=1 and type='mailer' order by server"; 
	$sth = $dbhq->prepare($sql);
	$sth->execute();
	while (($server) = $sth->fetchrow_array()) {
			print "Sending redir.dat to $server\n";
		$ftp = Net::FTP->new("$server", Timeout => 120, Debug => 1, Passive => 0) or print "Cannot connect to $server: $@\n";
		print "After connect\n";
		if ($ftp) {
			$ftp->login($username,'18years') or print "Cannot login ", $ftp->message;
			$ftp->ascii();
			print "After login\n";
			$ftp->put("${some_dir}/redir.dat") or print "put failed ", $ftp->message;
			print "After put\n";
			$ftp->quit;
			print "Sent redir.dat to $server\n";
		}
	}
	$sth->finish();
}
$dbhq->disconnect;
$dbhu->disconnect;
exit;

sub sendmail {

    open (MAIL,"| /usr/sbin/sendmail -t");
    my $from_addr = "Redir.dat <info\@spirevision.com>";
    print MAIL "From: $from_addr\n";
    print MAIL "To: eneuner\@spirevision.com,jim\@spirevision.com,chad\@spirevision.com,thota\@spirevision.com\n";
    print MAIL "Subject: redir.dat file on Master DB is in abnormal size!\n";
    print MAIL "X-Priority: 1\n";
    print MAIL "X-MSMail-Priority: High\n\n";
	print MAIL "redir.dat on Master DB is too small, check /var/www/html/logs\n";
    close MAIL;
}
