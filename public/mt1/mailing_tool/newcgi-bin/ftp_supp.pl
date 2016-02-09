#!/usr/bin/perl
    use Net::FTP;
use lib "/var/www/html/newcgi-bin";
use util;

my $util = util->new;
my $dbh;
my $sql;
my $sth;
my $some_dir="/var/www/html/new_supplist";
my $server;
my $ip;
my $username;
my $to_dir;

#
$ip="ftp.aspiremail.com";

opendir(DIR, $some_dir);
@dots = grep { /\.txt/ || /\.csv/ || /\.TXT/ } readdir(DIR);
closedir DIR;
print "Files - $#dots\n";

if ($#dots >= 0)
{    
     $ftp = Net::FTP->new("$ip", Timeout => 20, Debug => 0, Passive => 0)
      or print "Cannot connect to $server: $@\n";
	if ($ftp)
	{
    $ftp->login('supp1','vvarlgyg')
      or print "Cannot login ", $ftp->message;
	$ftp->ascii();
	$i=0;
	while ($i <= $#dots)
	{
    	$ftp->put("/var/www/html/new_supplist/$dots[$i]") or print "put failed ", $ftp->message;
		print "Sent $dots[$i]\n";
    	unlink("/var/www/html/new_supplist/$dots[$i]");
		$i++;
	}
	$ftp->quit;
	}
}
my $some_dir="/var/www/html/supplist";
opendir(DIR, $some_dir);
@dots = grep { /\.txt/ || /\.csv/ || /\.TXT/ } readdir(DIR);
closedir DIR;
print "Files - $#dots\n";

if ($#dots >= 0)
{    
     $ftp = Net::FTP->new("$ip", Timeout => 20, Debug => 0, Passive => 0)
      or print "Cannot connect to $server: $@\n";
	if ($ftp)
	{
    $ftp->login('supp2','yhcpmfhm')
      or print "Cannot login ", $ftp->message;
	$ftp->ascii();
	$i=0;
	while ($i <= $#dots)
	{
    	$ftp->put("/var/www/html/supplist/$dots[$i]") or print "put failed ", $ftp->message;
		print "Sent $dots[$i]\n";
    	unlink("/var/www/html/supplist/$dots[$i]");
		$i++;
	}
	$ftp->quit;
	}
}
