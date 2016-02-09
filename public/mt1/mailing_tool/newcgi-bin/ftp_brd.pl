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
$sql="select mailer_ftp,ftp_username,ftp_password,record_path from third_party_defaults where mailer_name='Blue Rock Dove'";
$sth = $dbhq->prepare($sql);
$sth->execute();
($server,$username,$password,$to_dir)=$sth->fetchrow_array();
$sth->finish();
if ($ARGV[0] eq "")
{
$sql="select date_format(date_sub(curdate(),interval 1 day),'%c%e%Y')";
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
@dots = grep { /\_$cdate\.txt/ } readdir(DIR);
closedir DIR;
print "$cdate - $#dots\n";
if ($#dots >= 0)
{    
     $ftp = Net::FTP->new("$server", Timeout => 20, Debug => 0, Passive => 0)
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
			my $tstr=substr($tname,0,3);
			$_ = $tname;
			if (($tstr ne "tz_") && ($tstr ne "rz_") && ((/_AOL_/) || (/_Others_/)))
			{
				$tname =~ s/_$cdate.txt//;
				my $rest_str;
				($tname,$rest_str)=split('_',$tname);
				$sql="select replace(company,' ','') from user where user_id in (18,16,22,8,11,5,9,10,29,6,33,27,3,39,2,19,17,21,26,4) and replace(company,' ','')='$tname'";
				$sth = $dbhq->prepare($sql);
				$sth->execute();
				if (($tname) = $sth->fetchrow_array())
				{
					$ftp->cwd($tname);
    				$ftp->put("/data4/data/new/$dots[$i]") or print "put failed ", $ftp->message;
					$ftp->cwd("..");
					print "Sent $dots[$i]\n";
				}
				$sth->finish();
			}
			else
			{
				print "Skipping $tname\n";
			}
			$i++;
		}
		$ftp->quit;
	}
}
