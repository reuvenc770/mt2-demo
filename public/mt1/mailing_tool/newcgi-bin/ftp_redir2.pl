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
my $filter="";

my $machine=$ARGV[0];
if ($machine) {
	$filter=qq|and server='$machine'|;
}
my $dbhq;
my $dbhu;
($dbhq,$dbhu)=$util->get_dbh();
$username="transferdat";

$sql = "select server from server_config where inService=1 and type='mailer' $filter order by server"; 
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($server) = $sth->fetchrow_array())
{

	$ftp = Net::FTP->new("$server", Timeout => 20, Debug => 0, Passive => 0)
      or print "Cannot connect to $server: $@\n";

	if ($ftp) {
		if ($ftp->login($username,'18years')) {
			$ftp->ascii();
			if ($ftp->put("${some_dir}/redir.dat")) {
				print "Successfully transfer redir.dat to $server\n";
				$ftp->quit;
			}
			else {
				 print "put failed ", $ftp->message ."\n";
			}
		}
		else {
			print "Cannot login ", $ftp->message ."\n";
		}
	}
}
$sth->finish();
