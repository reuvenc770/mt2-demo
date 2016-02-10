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
$sql="select mailer_ftp,ftp_username,ftp_password,record_path from third_party_defaults where mailer_name='CBS'";
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
			my $tstr=substr($tname,0,3);
			if (($tstr ne "tz_") && ($tsr ne "rz_") && ($tsr ne "3rd"))
			{
				$tname =~ s/_$cdate.txt//;
				my $rest_str;
				($tname,$rest_str)=split('_',$tname);
				$sql="select replace(company,' ','') from user where user_id in (2,3,5,6,9,10,11,16,19,22,23,29,33,51,75,135,76,55,46) and replace(company,' ','')='$tname'";
				$sth = $dbhq->prepare($sql);
				$sth->execute();
				my $ltname;
				if (($ltname) = $sth->fetchrow_array())
				{
   	            	$_=$dots[$i];
                	if (/_Yahoo_/)
                	{
                		$ftp->put("/data4/data/new/$dots[$i]") or print "put failed ", $ftp->message;
                    	print "Sent $dots[$i]\n";
                	}
				}
				$sth->finish();
				# Send AOL Files
				$tname =~ s/_$cdate.txt//;
				my $rest_str;
				($tname,$rest_str)=split('_',$tname);
				$sql="select replace(company,' ','') from user where user_id in (10,11,31,124,148,144) and replace(company,' ','')='$tname'";
				$sth = $dbhq->prepare($sql);
				$sth->execute();
				if (($tname) = $sth->fetchrow_array())
				{
   	            	$_=$dots[$i];
                	if (/_AOL_/)
                	{
                		$ftp->put("/data4/data/new/$dots[$i]") or print "put failed ", $ftp->message;
                    	print "Sent $dots[$i]\n";
                	}
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
