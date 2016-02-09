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
$server="dosmonos.com";
$username="dosmonos_spvision";
$password="dmsvision";
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
open (MAIL,"| /usr/sbin/sendmail -t");
my $from_addr = "DosMonos Files Sent <info\@spirevision.com>";
print MAIL "From: $from_addr\n";
print MAIL "To: ariotto\@spirevision.com, jsobeck\@spirevision.com\n";
print MAIL "Subject: DosMonos Files Sent\n";
my $date_str = $util->date(6,6);
print MAIL "Date: $date_str\n";
print MAIL "X-Priority: 1\n";
print MAIL "X-MSMail-Priority: High\n";

my $some_dir="/var/www/util/data/new";
opendir(DIR, $some_dir);
@dots = grep { /^qint_/ && /\_$cdate\.txt/ } readdir(DIR);
closedir DIR;
print "$cdate - $#dots\n";
if ($#dots >= 0)
{
     $ftp = Net::FTP->new("$server", Timeout => 60, Debug => 0, Passive => 0)
      or print "Cannot connect to $server: $@\n";
	if ($ftp)
	{
    	$ftp->login($username,$password) or print "Cannot login ", $ftp->message;
		$ftp->cwd("db_names/acct3-SpireVision");
		$ftp->ascii();
		$i=0;
		while ($i <= $#dots)
		{
			my $tname = $dots[$i]; 
			print "$tname\n";
			$tname =~ s/_$cdate.txt//;
			$tname =~ s/qint_//;
			$sql="select replace(first_name,' ','') from user where user_id in (45,65,66,67,68,81,82,91,94,95,98,103,140,187,188,232,233,259) and replace(first_name,' ','')='$tname'";
			$sth = $dbhq->prepare($sql);
			$sth->execute();
			if (($tname) = $sth->fetchrow_array())
			{
                my $tfile=$dots[$i];
		$tfile=~s/qint_//g;
               	$ftp->put("/var/www/util/data/new/$dots[$i]","$tfile") or print "put failed ", $ftp->message;
                   print "Sent $dots[$i]\n";
			print MAIL "Sent $tfile\n";
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
close(MAIL);
