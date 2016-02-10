#!/usr/bin/perl

use Net::FTP;
use lib "/var/www/html/newcgi-bin";
use util;

my $util = util->new;
my $dbh;
my $sql;
my $sth;
my $server;
my $ip;
my $username;
my $to_dir;

my $dbhq;
my $dbhu;
my $cdate;
($dbhq,$dbhu)=$util->get_dbh();
$server="64.65.63.57";
$username="Admupload";
$password="dataupload";
$sql="select date_format(date_sub(curdate(),interval 1 day),'%c%e%Y')";
$sth = $dbhq->prepare($sql);
$sth->execute();
($cdate)=$sth->fetchrow_array();
$sth->finish();
print "Cdate $cdate\n";

my $some_dir="/data4/data/new";
opendir(DIR, $some_dir);
@dots = grep { /^TargetMailAds_Others_/ && /\_$cdate\.txt/ } readdir(DIR);
closedir DIR;
print "$cdate - $#dots\n";
if ($#dots >= 0)
{    
     $ftp = Net::FTP->new("$server", Timeout => 20, Debug => 0, Passive => 0)
      or print "Cannot connect to $server: $@\n";
	if ($ftp)
	{
    	$ftp->login($username,$password) or print "Cannot login ", $ftp->message;
		$ftp->ascii();
		$i=0;
		while ($i <= $#dots)
		{
    		$ftp->put("/data4/data/new/$dots[$i]") or print "put failed ", $ftp->message;
			print "Sent $dots[$i]\n";
			$i++;
		}
		$ftp->quit;
	}
}
opendir(DIR, $some_dir);
@dots = grep { /^TargetMailAds_Hotmail_/ && /\_$cdate\.txt/ } readdir(DIR);
closedir DIR;
print "$cdate - $#dots\n";
if ($#dots >= 0)
{    
     $ftp = Net::FTP->new("$server", Timeout => 20, Debug => 0, Passive => 0)
      or print "Cannot connect to $server: $@\n";
	if ($ftp)
	{
    	$ftp->login($username,$password) or print "Cannot login ", $ftp->message;
		$ftp->ascii();
		$i=0;
		while ($i <= $#dots)
		{
    		$ftp->put("/data4/data/new/$dots[$i]") or print "put failed ", $ftp->message;
			print "Sent $dots[$i]\n";
			$i++;
		}
		$ftp->quit;
	}
}
