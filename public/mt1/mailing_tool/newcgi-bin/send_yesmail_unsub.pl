#!/usr/bin/perl
#################################################################
####   send_yesmail_unsub.pl - Send unsubs to Expert Sender 
####
#################################################################

use strict;
use Net::FTP;
use util;

$|=1 ;   # set OUTPUT_AUTOFLUSH to true
my $util = util->new;
my $dbhq;
my $dbhu;
($dbhq,$dbhu)=$util->get_dbh();
my $util = util->new;
my $sql;
my $rows;
my $sth;
my $sdate;
my $edate;
my $server="express.yesmail.com";
my @DIVISION=("UK","MISC");
my @FTPUSER=("132/7","132/6");
my @FTPPW=("zej8cuxo","repo8ocu");
my $clientid;

if ($ARGV[0] ne "")
{
	$sdate=$ARGV[0];
}
else
{
	$sql="select date_sub(curdate(),interval 1 day)";
	$sth=$dbhu->prepare($sql);
	$sth->execute();
	($sdate)=$sth->fetchrow_array();
	$sth->finish();
}
$sql="select client_id from YesmailData";
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($clientid)=$sth->fetchrow_array())
{
	my $filename="/tmp/".$clientid."_unsubs_".$sdate.".txt";
	open(OUT,">$filename");
	print OUT "email_addr\n";
	$sql="select email_addr from unsub_log where client_id =$clientid and unsub_date=?";
	my $sth1=$dbhu->prepare($sql);
	$sth1->execute($sdate);
	my $em;
	while (($em)=$sth1->fetchrow_array())
	{
		print OUT "$em\n";
	}
	$sth1->finish();
	close(OUT);
	my $j=0;
	while ($j <= $#FTPUSER)
	{
    	my $ftp = Net::FTP->new("$server", Timeout => 20, Debug => 0, Passive => 0) or print "Cannot connect to $server: $@\n";
    	if ($ftp)
    	{
        	$ftp->login($FTPUSER[$j],$FTPPW[$j]) or print "Cannot login ", $ftp->message;
        	$ftp->ascii();
			$ftp->cwd("datauploads/address_kill");
			$ftp->put($filename) or print "put failed ", $ftp->message;
			$ftp->quit;
			print "Sent $filename\n";
		}
		$j++;
	}
}
$sth->finish();
