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
["CreditHelpSource","crelpso","D\%Kj7p7"],
["AutoLoanAccess","aloac","Kq59y\!Y"],
["AccessCreditHelp","acrehe","DjP\&4c9"],
["FinancialMatchingSvc","fimase","r\$x9GB3"],
["CreditCardMatching","ccmats","\*CK4d3p"]
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
$sql="select date_format(date_sub(curdate(),interval 1 day),'%m.%d.%Y')";
$sth = $dbhq->prepare($sql);
$sth->execute();
($cdate)=$sth->fetchrow_array();
$sth->finish();
}
print "Cdate $cdate\n";
open (MAIL,"| /usr/sbin/sendmail -t");
my $from_addr = "Qinteractive Files Sent <info\@spirevision.com>";
print MAIL "From: $from_addr\n";
print MAIL "To: ariotto\@spirevision.com, jsobeck\@spirevision.com\n";
print MAIL "Subject: Qinteractive Files Sent\n";
my $date_str = $util->date(6,6);
print MAIL "Date: $date_str\n";
print MAIL "X-Priority: 1\n";
print MAIL "X-MSMail-Priority: High\n";

my $some_dir="/var/www/util/data/new";
my $i=0;
while ($i <= $#FINFO)
{
	my $tfile=$some_dir."/qint_".$FINFO[$i][0]."_".$cdate.".txt";
	print "Looking for file: $tfile\n";
	if (-e $tfile)
	{
my $data = qq|$record->{'email_addr'}, $record->{'first_name'}, $record->{'last_name'}, $record->{'city'}, $record->{'state'}, $record->{'source_url'}, $record->{'capture_date'}, $record->{'capture_date'},$record->{'member_source'}\n|;
	my $tfile1="/tmp/".$FINFO[$i][0].".txt";
	open(TMP,">$tfile1");
	print TMP "EM,FN,LN,C,S,PS,RD,AD,IP\n";
	open(IN,"<$tfile");
	while (<IN>)
	{
		my $line=$_;
		chop($line);
		$line=~s///g;
		print TMP "$line\n";
	}
	close(IN);
	close(TMP);
	$outfile="/users/emndata/".$FINFO[$i][1]."/Optin/NewRegistrants/qint_".$FINFO[$i][0]."_".$cdate.".txt";
#	$outfile="Optin/NewRegistrants/qint_".$FINFO[$i][0]."_".$cdate.".txt";

my %args = (
    user => $FINFO[$i][1],
    password => $FINFO[$i][2] 
);

	print "Connecting for <$FINFO[$i][1]> <$FINFO[$i][2]>\n";
my $sftp = Net::SFTP->new($server, %args);
	print "<$sftp>\n";
	print "Putting file: $tfile1 <$outfile>\n";
	print MAIL "Sent File: $tfile\n";
	$sftp->put($tfile1,$outfile);
	print $sftp->status()."\n";
	print "Finishing file: $tfile\n";
	}
	$i++;
}
close(MAIL);
