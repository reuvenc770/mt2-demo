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
my $sth;
my $em;
my $ftp;
my $cdate;
my $minsid;
my $maxsid;

my $ftpuser="mailingtool";
my $ftppass="1nt3l@";
($dbhq,$dbhu)=$util->get_dbh();
#
$sql="select date_sub(curdate(),interval 1 day)"; 
$sth=$dbhu->prepare($sql);
$sth->execute();
($cdate)=$sth->fetchrow_array();
$sth->finish();
#
$sql="select parmval from sysparm where parmkey='EOS_LAST_SID'";
$sth=$dbhu->prepare($sql);
$sth->execute();
($minsid)=$sth->fetchrow_array();
$sth->finish();
$minsid++;
#
my $dbhs = DBI->connect("DBI:mysql:supp:suppress.routename.com","db_user","sp1r3V");
$sql="select max(sid) from suppress_list_orange"; 
$sth=$dbhs->prepare($sql);
$sth->execute();
($maxsid)=$sth->fetchrow_array();
$sth->finish();

#
my $filename="/tmp/XLUnsub_eos_".$cdate.".txt";
open(LOG,">$filename");
$sql="select distinct email_addr from unsub_log where unsub_date = date_sub(curdate(),interval 1 day)"; 
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($em)=$sth->fetchrow_array())
{
	print LOG "$em,Suppression because of ESP - AllInclusive\n";
}
$sth->finish();
#
# Get suppression records
#
$sql="select email_addr,suppressionReasonDetails from suppress_list_orange slo join SuppressionReason sr on slo.suppressionReasonID=sr.suppressionReasonID where sid between $minsid and $maxsid";
$sth=$dbhs->prepare($sql);
print "$sql\n";
$sth->execute();
my $sreason;
while (($em,$sreason)=$sth->fetchrow_array())
{
	print LOG "$em,$sreason\n";
}
$sth->finish();
close(LOG);

$sql="update sysparm set parmval='$maxsid' where parmkey='EOS_LAST_SID'";
my $rows=$dbhu->do($sql);

my $host = "54.186.245.168";
$ftp = Net::FTP->new("$host", Timeout => 20, Debug => 0, Passive => 0) or print "Cannot connect to $host: $@\n";
if ($ftp)
{
    $ftp->login($ftpuser,$ftppass) or print "Cannot login ", $ftp->message;
    $ftp->ascii();
	$ftp->cwd("unsubs");
	$ftp->cwd("incoming");
    $ftp->put($filename) or print "put failed ", $ftp->message;
    print "Sent $filename\n";
    $ftp->quit;
}

$util->clean_up();
exit(0) ;
