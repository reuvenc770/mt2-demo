#!/usr/bin/perl
use strict;

use Net::FTP;
use DBI;
use lib "/var/www/html/newcgi-bin";
use util;

$| = 1;    # don't buffer output for debugging log

my $em;
my $cdate;
my $util=util->new();
# connect to the util database
my $dbhq;
my $dbhu;
my $dbhs;
($dbhq,$dbhu)=$util->get_dbh();
$dbhs = DBI->connect("DBI:mysql:supp:suppmasterdb.routename.com","db_user","sp1r3V");

my $sql="select date_format(date_sub(curdate(),interval 1 day),'%d_%m_%Y')";
my $sth=$dbhu->prepare($sql);
$sth->execute();
($cdate)=$sth->fetchrow_array();
$sth->finish();

my $tfile=$cdate."_REL.txt";
open(OUT,">$tfile");

$sql="select email_addr from email_list_REL where subscribe_date=date_sub(curdate(),interval 1 day)";
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($em)=$sth->fetchrow_array())
{
	my $cnt;
	$sql="select count(*) from suppress_list_orange where email_addr=?";
	my $sth1=$dbhs->prepare($sql);
	$sth1->execute($em);
	($cnt)=$sth1->fetchrow_array();
	$sth1->finish();
	if ($cnt == 0)
	{
		print OUT "$em\n";
	}
}
$sth->finish();
close(OUT);

my $host = "lf.qnoon.com";
my $ftp = Net::FTP->new("$host", Timeout => 20, Debug => 0, Passive => 1) or print "Cannot connect to $host: $@\n";
if ($ftp)
{
    $ftp->login('ken_levarek','Veltkm7') or print "Cannot login ", $ftp->message;
	$ftp->ascii();
	$ftp->cwd("REL_DailyFiles");
	$ftp->put($tfile) or die "Get Failed ",$ftp->message;
    $ftp->quit;
	unlink($tfile);
}
