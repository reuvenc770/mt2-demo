#!/usr/bin/perl
#===============================================================================
#
#--Change Control---------------------------------------------------------------
#===============================================================================

#-----------------------
# include Perl Modules
#-----------------------
use strict;
use Net::FTP;
use lib "/var/www/html/newcgi-bin";
use util;

$|=1 ;   # set OUTPUT_AUTOFLUSH to true

my $util = util->new;
my $dbhu;
my $dbhq;
my $rows;
my $sql;
my $total_cnt;
my $cdate;
my $total_unscnt;
my $total_alreadycnt;
my $sth;
my $em;
my $ftp;
my $dayint;
my $DOM;
my $dname;
my $t1;

my $clientstr=$ARGV[0];
my $ftpuser=$ARGV[1];
my $ftppass=$ARGV[2];
my $fileversion=$ARGV[3];
my $isp=$ARGV[4];
($dbhq,$dbhu)=$util->get_dbh();
$sql="select date_format(curdate(),'%m_%d_%Y')";
$sth=$dbhu->prepare($sql);
$sth->execute();
($cdate)=$sth->fetchrow_array();
$sth->finish();
#
if ($isp ne "")
{
	$sql="select domain_name from email_domains ed,email_class ec where ed.domain_class=ec.class_id and ec.class_name=?";
	$sth=$dbhu->prepare($sql);
	$sth->execute($isp);
	while (($dname)=$sth->fetchrow_array())
	{
		$DOM->{$dname}=1;
	}
	$sth->finish();
}

my $filename="/tmp/XLUnsub_".$fileversion."_".$cdate.".txt";
open(LOG,">$filename");
$sql="select email_addr from unsub_log where client_id in (?) and unsub_date >= date_sub(curdate(),interval 7 day) and unsub_date < curdate()";
$sth=$dbhu->prepare($sql);
$sth->execute($clientstr);
while (($em)=$sth->fetchrow_array())
{
	if ($isp eq "")
	{
		print LOG "$em\n";
	}
	else
	{
		($t1,$dname)=split('@',$em);
		if ($DOM->{$dname})
		{
			print LOG "$em\n";
		}
	}
}
$sth->finish();
close(LOG);
my $host = "ftp.aspiremail.com";
$ftp = Net::FTP->new("$host", Timeout => 20, Debug => 0, Passive => 0) or print "Cannot connect to $host: $@\n";
if ($ftp)
{
    $ftp->login($ftpuser,$ftppass) or print "Cannot login ", $ftp->message;
    $ftp->ascii();
    $ftp->put($filename) or print "put failed ", $ftp->message;
    print "Sent $filename\n";
    $ftp->quit;
}

$util->clean_up();
exit(0) ;
