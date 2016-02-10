#!/usr/bin/perl
    use Net::FTP;
use lib "/var/www/html/newcgi-bin";
use util;

my $util = util->new;
my $dbh;
my $sql;
my $sth;
my $some_dir="/var/www/util/new_tmpmailfiles1";
my $server;
my $ip;
my $username;
my $to_dir;

my $dbhq;
my $dbhu;
($dbhq,$dbhu)=$util->get_dbh();
#
$sql = "select distinct server,ipPri from server_config,brand_host where inService=1 and server=brand_host.server_name and server_type='C' order by server";
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($server,$ip) = $sth->fetchrow_array())
{
$_ = $server;
if (($server eq "interland") || ($server eq "interland2") || ($server eq "interland3") || ($server eq "interland4") || ($server eq "interland5"))
{
	$username="backend1";
}
else
{
	$username="backend";
}

$to_dir="/var/www/util/mailfiles";
opendir(DIR, $some_dir);
@dots = grep { /\_$server\_/ } readdir(DIR);
closedir DIR;
print "$server - $#dots\n";

if ($#dots >= 0)
{    
     $ftp = Net::FTP->new("$ip", Timeout => 20, Debug => 0, Passive => 0)
      or print "Cannot connect to $server: $@\n";
	if ($ftp)
	{

    $ftp->login($username,'!noacce$$')
      or print "Cannot login ", $ftp->message;


    $ftp->cwd($to_dir);

	$ftp->ascii();

	$i=0;
	while ($i <= $#dots)
	{
    	$ftp->put("/var/www/util/new_tmpmailfiles1/$dots[$i]") or print "put failed ", $ftp->message;
		print "Sent $dots[$i]\n";
    	unlink("/var/www/util/new_tmpmailfiles1/$dots[$i]");
		$i++;
	}
	$ftp->quit;
	}
}
}
$sth->finish();
