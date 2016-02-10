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
$server="www.razormedia.net";
$username="miked";
$password="mike";
$sql="select date_format(date_sub(curdate(),interval 1 day),'%m.%d.%Y')";
$sth = $dbhq->prepare($sql);
$sth->execute();
($cdate)=$sth->fetchrow_array();
$sth->finish();
print "Cdate $cdate\n";

my $some_dir="/data4/data/new";
opendir(DIR, $some_dir);
@dots = grep { /^rz_/ && /\_$cdate\.txt/ } readdir(DIR);
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
			my $tname = $dots[$i]; 
			$tname =~ s/^rz_//;
			$tname =~ s/_$cdate.txt//;
#			$sql="select replace(first_name,' ','') from user where user_id in (18,16,22,8,29,4,19,27,17,21,26,3,39) and replace(first_name,' ','')='$tname'";
			$sql="select replace(first_name,' ','') from user where user_id in (18,16,22,8,29,4,19,27,17,21,26,39) and replace(first_name,' ','')='$tname'";
			$sth = $dbhq->prepare($sql);
			$sth->execute();
			if (($tname) = $sth->fetchrow_array())
			{
                $ftp->cwd($tname);
    			$ftp->put("/data4/data/new/$dots[$i]") or print "put failed ", $ftp->message;
                $ftp->cwd("..");
				print "Sent $dots[$i]\n";
			}
			else
			{
#				print "Removing $dots[$i]\n";
#				unlink("/data4/data/new/$dots[$i]");
			}
			$sth->finish();
			$i++;
		}
		$ftp->quit;
	}
}
