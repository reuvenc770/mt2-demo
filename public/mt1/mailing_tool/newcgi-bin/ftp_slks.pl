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
$sql="select mailer_ftp,ftp_username,ftp_password,record_path from third_party_defaults where mailer_name='SLKS'";
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
			if (($tstr ne "tz_") && ($tsr ne "rz_"))
			{
				$tname =~ s/_$cdate.txt//;
				my $rest_str;
				($tname,$rest_str)=split('_',$tname);
			$sql="select replace(company,' ','') from user where user_id in (18,16,22,8,11,5,9,10,29,27,6,32,33,39,2,23) and replace(company,' ','')='$tname'";
			$sth = $dbhq->prepare($sql);
			$sth->execute();
			if (($tname) = $sth->fetchrow_array())
			{
                if ($tname ne "NetworkMetropolis")
                {
                    $ftp->put("/data4/data/new/$dots[$i]") or print "put failed", $ftp->message;
                    print "Sent $dots[$i]\n";
                }
                else
                {
                    $_=$dots[$i];
                    if (/_AOL_/)
                    {
                        $ftp->put("/data4/data/new/$dots[$i]") or print "put failed ", $ftp->message;
                        print "Sent $dots[$i]\n";
                    }
                }
			}
			else
			{
##				print "Removing $dots[$i]\n";
##				unlink("/data4/data/new/$dots[$i]");
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
