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
$server="imail7";
$username="intela";
$password="XPlewtVC";
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
@dots = grep { /^3rd_/ && /\_$cdate\.txt/ } readdir(DIR);
closedir DIR;
print "$cdate - $#dots\n";
if ($#dots >= 0)
{
     $ftp = Net::FTP->new("$server", Timeout => 60, Debug => 0, Passive => 0)
      or print "Cannot connect to $server: $@\n";
	if ($ftp)
	{
    	$ftp->login($username,$password) or print "Cannot login ", $ftp->message;
		$ftp->ascii();
		$i=0;
		while ($i <= $#dots)
		{
			my $tname = $dots[$i]; 
			print "$tname\n";
			$tname =~ s/_$cdate.txt//;
			$tname =~ s/3rd_//;
			$sql="select replace(first_name,' ','') from user where user_id in (8,10,19,22) and replace(first_name,' ','')='$tname'";
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
