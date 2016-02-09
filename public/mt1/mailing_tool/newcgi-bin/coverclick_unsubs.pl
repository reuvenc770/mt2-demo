#!/usr/bin/perl -w

#use strict;
use Net::FTP;
use lib "/var/www/html/newcgi-bin";
use util;

my $util = util->new;
my $sql;
my $sth;
my $dbhq;
my $dbhu;
my $cdate;
my $em;
($dbhq,$dbhu)=$util->get_dbh();

$sql="select curdate()";
$sth=$dbhu->prepare($sql);
$sth->execute();
($cdate)=$sth->fetchrow_array();
$sth->finish();

$sql="select email_addr from unsub_log where client_id in (68,45,103,91,232,66,65,67,188,187) and unsub_date >= date_sub(curdate(),interval 7 day) and unsub_date < curdate()";
$sth=$dbhu->prepare($sql);
$sth->execute();
my $filename="/tmp/unsubs_coverclicks".$cdate.".csv";
my $outfile="unsubs_coverclicks".$cdate.".csv";
open(LOG,">$filename");
while (($em)=$sth->fetchrow_array())
{
	print LOG "$em\n";
}
$sth->finish();
close(LOG);
my $host = "ftpserver.affiliatenetwork.com";
$ftp = Net::FTP->new("$host", Timeout => 20, Debug => 0, Passive => 0) or print "Cannot connect to $host: $@\n";
if ($ftp)
{
	$ftp->login('spirevision','Je8uw3fR') or print "Cannot login ", $ftp->message;
    $ftp->ascii();
    $ftp->put($filename,$outfile) or print "put failed ", $ftp->message;
    print "Sent $filename\n";
    $ftp->quit;
}
