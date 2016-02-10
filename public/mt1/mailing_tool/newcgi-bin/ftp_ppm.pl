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
$server="ppmorl05.precisionplay.com";
$username="SV";
$password="Gl204#20^6";
$to_dir="/users/SV";
if ($ARGV[0] eq "")
{
$sql="select date_format(date_sub(curdate(),interval 1 day),'%m.%d.%Y')";
$sth = $dbhq->prepare($sql);
$sth->execute();
($cdate)=$sth->fetchrow_array();
$sth->finish();
}
else
{
	$cdate=$ARGV[0];
}
print "Cdate $cdate\n";

my $some_dir="/data4/data/new";
opendir(DIR, $some_dir);
@dots = grep { /^ppm_/ && /\_$cdate\.txt/ } readdir(DIR);
closedir DIR;
print "$cdate - $#dots\n";
if ($#dots >= 0)
{
     $ftp = Net::FTP->new("$server", Timeout => 60, Debug => 0, Passive => 0)
      or print "Cannot connect to $server: $@\n";
	if ($ftp)
	{
    	$ftp->login($username,$password) or print "Cannot login ", $ftp->message;
		$ftp->cwd($to_dir);
		$ftp->ascii();
		$i=0;
		while ($i <= $#dots)
		{
			my $tname = $dots[$i]; 
			print "$tname\n";
			$tname =~ s/_$cdate.txt//;
			$tname =~ s/ppm_//;
			$sql="select replace(first_name,' ','') from user where user_id in (22,93,4,8,61,78,21,19,2,88,24,29,3,33,43,44,105,108,120,31) and replace(first_name,' ','')='$tname'";
			$sth = $dbhq->prepare($sql);
			$sth->execute();
			if (($tname) = $sth->fetchrow_array())
			{
                $_=$dots[$i];
               	$ftp->put("/data4/data/new/$dots[$i]") or print "put failed ", $ftp->message;
                   print "Sent $dots[$i]\n";
			}
			else
			{
##				print "Removing $dots[$i]\n";
##				unlink("/data4/data/new/$dots[$i]");
			}
			$sth->finish();
			$i++;
		}
		$ftp->quit;
	}
}
