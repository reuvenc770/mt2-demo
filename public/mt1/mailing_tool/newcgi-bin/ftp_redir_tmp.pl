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
$username="transferdat";

$server="nethosters01";
	$ftp = Net::FTP->new("$server", Timeout => 20, Debug => 0, Passive => 0)
      or print "Cannot connect to $server: $@\n";

	if ($ftp)
	{
	$ftp->login($username,'18years') or print "Cannot login ", $ftp->message;
	$ftp->ascii();
    $ftp->put("${some_dir}/redir.dat") or print "put failed ", $ftp->message;
	$ftp->quit;
	print "Sent redir.dat to $server\n";
	}
$server="webstream3";
	$ftp = Net::FTP->new("$server", Timeout => 20, Debug => 0, Passive => 0)
      or print "Cannot connect to $server: $@\n";

	if ($ftp)
	{
	$ftp->login($username,'18years') or print "Cannot login ", $ftp->message;
	$ftp->ascii();
    $ftp->put("${some_dir}/redir.dat") or print "put failed ", $ftp->message;
	$ftp->quit;
	print "Sent redir.dat to $server\n";
	}
