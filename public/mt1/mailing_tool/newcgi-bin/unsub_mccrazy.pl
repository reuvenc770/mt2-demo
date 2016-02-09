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

($dbhq,$dbhu)=$util->get_dbh();

$sql="select date_format(date_sub(curdate(),interval 1 day),'%m_%d')";
$sth=$dbhu->prepare($sql);
$sth->execute();
($cdate)=$sth->fetchrow_array();
$sth->finish();
my $filename="XLUnsub_".$cdate.".txt";
open(LOG,">XLUnsub_$cdate.txt");
$sql="select email_addr from unsub_log where client_id =830 and unsub_date >= date_sub(curdate(),interval 7 day) and unsub_date < curdate()";
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($em)=$sth->fetchrow_array())
{
	print LOG "$em\n";
}
$sth->finish();
close(LOG);
my $host = "ftp.aspiremail.com";
$ftp = Net::FTP->new("$host", Timeout => 20, Debug => 0, Passive => 0) or print "Cannot connect to $host: $@\n";
if ($ftp)
{
    $ftp->login('mccrazyunsub','7KewD8mj') or print "Cannot login ", $ftp->message;
    $ftp->ascii();
    $ftp->put($filename) or print "put failed ", $ftp->message;
    print "Sent $filename\n";
    $ftp->quit;
}

exit(0) ;
