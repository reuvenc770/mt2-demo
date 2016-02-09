#!/usr/bin/perl

use Net::SFTP;
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
my @FINFO = (
["CreditHelpSource","crelpso","D\%Kj7p7","45"],
["AutoLoanAccess","aloac","Kq59y\!Y","68"],
["AccessCreditHelp","acrehe","DjP\&4c9","91"],
["FinancialMatchingSvc","fimase","r\$x9GB3","187"],
["CreditCardMatching","ccmats","\*CK4d3p","188"],
["YourCreditCardExpert","yocrex","pM2\!3Jr",341],
["CreditHelpExpert","crexpe","N\$Ac4j5",103],
["TheApprovalSource","tappso","HuT9\#a9",340]
);

my $dbhq;
my $dbhu;
my $cdate;
($dbhq,$dbhu)=$util->get_dbh();
$server="sftp.qinteractive.com";
if ($ARGV[0] ne "")
{
	$cdate=$ARGV[0];
}
else
{
$sql="select date_sub(curdate(),interval 1 day)";
$sth = $dbhq->prepare($sql);
$sth->execute();
($cdate)=$sth->fetchrow_array();
$sth->finish();
}
print "Cdate $cdate\n";
my $i=0;
my $cnt=0;
while ($i <= $#FINFO)
{
	$cnt=0;
	my $tfile="/tmp/unsub_".$FINFO[$i][0]."_".$cdate.".txt";
	open(TMP,">$tfile");
	print TMP "EM\n";
	$sql="select email_addr from unsub_log where client_id=$FINFO[$i][3] and unsub_date >= '$cdate' and unsub_date < date_add('$cdate',interval 1 day)";
	$sth=$dbhu->prepare($sql);
	$sth->execute();
	while (($em)=$sth->fetchrow_array())
	{
        	print TMP "\"$em\"\n";
			$cnt++;
	}
	$sth->finish();
	close(TMP);
	if ($cnt > 0)
	{
	$outfile="/users/emndata/".$FINFO[$i][1]."/OptOut/PartnerOptOut/unsub_".$FINFO[$i][0]."_".$cdate.".txt";

my %args = (
    user => $FINFO[$i][1],
    password => $FINFO[$i][2] 
);

	print "Connecting for <$FINFO[$i][1]> <$FINFO[$i][2]>\n";
my $sftp = Net::SFTP->new($server, %args);
	print "<$sftp>\n";
	print "Putting file: $tfile <$outfile>\n";
	print MAIL "Sent File: $tfile\n";
	$sftp->put($tfile,$outfile);
	print $sftp->status()."\n";
	print "Finishing file: $tfile\n";
	}
	$i++;
}
close(MAIL);
