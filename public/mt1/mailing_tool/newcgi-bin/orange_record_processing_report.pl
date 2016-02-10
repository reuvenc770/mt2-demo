#!/usr/bin/perl
#-----  include Perl Modules ---------
use strict;
use MIME::Lite;
use lib "/var/www/html/newcgi-bin";
use util;

#------  get some objects to use later ---------
my $util = util->new;
my $sql;
my $sth;
my $sth1;
my $dbh;
my $rows;
my ($cid,$username,$totalrecords,$validrecords,$invalidrecords,$duplicates,$suppressedrecords,$uniquerecords,$recorddeliverables,$recordpreviousopeners,$recordpreviousclickers);

#------  connect to the util database -----------
my $dbhq;
my $dbhu;
($dbhq,$dbhu)=$util->get_dbh();
my $notify_email_addr="espken\@zetainteractive.com,espdata\@zetainteractive.com";
my $mail_mgr_addr="info\@zetainteractive.com";
my $tfile="/tmp/orange_record_processing_".$$.".csv";
open(MAIL,">$tfile");
print MAIL "Client ID,Client,totalrecords,validrecords,invalidrecords,duplicates,suppressedrecords,supp rate,uniquerecords,unq rate,record deliverables,record previous openers,record previous clickers,AOL valid cnt,Yahoo valid cnt,Hotmail valid cnt,Gmail valid cnt,Others valid cnt\n";

$sql="select user_id,company,sum(totalRecords),sum(validRecords),sum(invalidRecords),sum(duplicates),sum(suppressedRecords),sum(uniqueRecords),sum(recordDeliverables),sum(recordPreviousOpeners),sum(recordPreviousClickers) from ClientRecordTotalsByIsp crt join user u on u.user_id=crt.clientID where processedDate=date_sub(curdate(),interval 1 day) and OrangeClient='Y' group by user_id,company"; 
$sth = $dbhu->prepare($sql);
$sth->execute();
while (($cid,$username,$totalrecords,$validrecords,$invalidrecords,$duplicates,$suppressedrecords,$uniquerecords,$recorddeliverables,$recordpreviousopeners,$recordpreviousclickers) = $sth->fetchrow_array())
{
	my $supprate=($suppressedrecords/$totalrecords)*100;
	my $unqrate=($uniquerecords/$totalrecords)*100;
	print MAIL "$cid,$username,$totalrecords,$validrecords,$invalidrecords,$duplicates,$suppressedrecords,";
	printf MAIL "%4.2f \%,",$supprate;
	print MAIL "$uniquerecords,";
	printf MAIL "%4.2f \%,",$unqrate;
	print MAIL "$recorddeliverables,$recordpreviousopeners,$recordpreviousclickers,";
	$sql="select classID,sum(validRecords) from ClientRecordTotalsByIsp where clientID=? and processedDate=date_sub(curdate(),interval 1 day) and classID in (1,2,3,4,17) group by 1";
	$sth1=$dbhu->prepare($sql);
	$sth1->execute($cid);
	my $classID;
	my $vrecords;
	my $C={};
	while (($classID,$vrecords)=$sth1->fetchrow_array())
	{
		$C->{$classID}=$vrecords;
	}
	$sth1->finish();
	$C->{1}=$C->{1} || 0;
	$C->{2}=$C->{2} || 0;
	$C->{3}=$C->{3} || 0;
	$C->{4}=$C->{4} || 0;
	$C->{17}=$C->{17} || 0;
	print MAIL "$C->{1},$C->{3},$C->{2},$C->{17},$C->{4}\n";
}
$sth->finish();

print MAIL "\n\nLast 15 days\n";
print MAIL "-----------\n";
print MAIL "Client ID,Client,totalrecords,validrecords,invalidrecords,duplicates,suppressedrecords,supp rate,uniquerecords,unq rate,record deliverables,record previous openers,record previous clickers,AOL valid cnt,Yahoo valid cnt,Hotmail valid cnt,Gmail valid cnt,Others valid cnt\n";

$sql="select user_id,company,sum(totalRecords),sum(validRecords),sum(invalidRecords),sum(duplicates),sum(suppressedRecords),sum(uniqueRecords),sum(recordDeliverables),sum(recordPreviousOpeners),sum(recordPreviousClickers) from ClientRecordTotalsByIsp crt join user u on u.user_id=crt.clientID where processedDate between date_sub(curdate(),interval 16 day) and date_sub(curdate(),interval 1 day) and OrangeClient='Y' group by user_id,company"; 
$sth = $dbhu->prepare($sql);
$sth->execute();
while (($cid,$username,$totalrecords,$validrecords,$invalidrecords,$duplicates,$suppressedrecords,$uniquerecords,$recorddeliverables,$recordpreviousopeners,$recordpreviousclickers) = $sth->fetchrow_array())
{
	my $supprate=($suppressedrecords/$totalrecords)*100;
	my $unqrate=($uniquerecords/$totalrecords)*100;
	print MAIL "$cid,$username,$totalrecords,$validrecords,$invalidrecords,$duplicates,$suppressedrecords,";
	printf MAIL "%4.2f \%,",$supprate;
	print MAIL "$uniquerecords,";
	printf MAIL "%4.2f \%,",$unqrate;
	print MAIL "$recorddeliverables,$recordpreviousopeners,$recordpreviousclickers,";
	$sql="select classID,sum(validRecords) from ClientRecordTotalsByIsp where clientID=? and processedDate between date_sub(curdate(),interval 16 day) and date_sub(curdate(),interval 1 day) and classID in (1,2,3,4,17) group by 1";
	$sth1=$dbhu->prepare($sql);
	$sth1->execute($cid);
	my $classID;
	my $vrecords;
	my $C={};
	while (($classID,$vrecords)=$sth1->fetchrow_array())
	{
		$C->{$classID}=$vrecords;
	}
	$sth1->finish();
	$C->{1}=$C->{1} || 0;
	$C->{2}=$C->{2} || 0;
	$C->{3}=$C->{3} || 0;
	$C->{4}=$C->{4} || 0;
	$C->{17}=$C->{17} || 0;
	print MAIL "$C->{1},$C->{3},$C->{2},$C->{17},$C->{4}\n";
}
$sth->finish();
print MAIL "\n\nLast 30 days\n";
print MAIL "-----------\n";
print MAIL "Client ID,Client,totalrecords,validrecords,invalidrecords,duplicates,suppressedrecords,supp rate,uniquerecords,unq rate,record deliverables,record previous openers,record previous clickers,AOL valid cnt,Yahoo valid cnt,Hotmail valid cnt,Gmail valid cnt,Others valid cnt\n";

$sql="select user_id,company,sum(totalRecords),sum(validRecords),sum(invalidRecords),sum(duplicates),sum(suppressedRecords),sum(uniqueRecords),sum(recordDeliverables),sum(recordPreviousOpeners),sum(recordPreviousClickers) from ClientRecordTotalsByIsp crt join user u on u.user_id=crt.clientID where processedDate between date_sub(curdate(),interval 31 day) and date_sub(curdate(),interval 1 day) and OrangeClient='Y' group by user_id,company"; 
$sth = $dbhu->prepare($sql);
$sth->execute();
while (($cid,$username,$totalrecords,$validrecords,$invalidrecords,$duplicates,$suppressedrecords,$uniquerecords,$recorddeliverables,$recordpreviousopeners,$recordpreviousclickers) = $sth->fetchrow_array())
{
	my $supprate=($suppressedrecords/$totalrecords)*100;
	my $unqrate=($uniquerecords/$totalrecords)*100;
	print MAIL "$cid,$username,$totalrecords,$validrecords,$invalidrecords,$duplicates,$suppressedrecords,";
	printf MAIL "%4.2f \%,",$supprate;
	print MAIL "$uniquerecords,";
	printf MAIL "%4.2f \%,",$unqrate;
	print MAIL "$recorddeliverables,$recordpreviousopeners,$recordpreviousclickers,";
	$sql="select classID,sum(validRecords) from ClientRecordTotalsByIsp where clientID=? and processedDate between date_sub(curdate(),interval 31 day) and date_sub(curdate(),interval 1 day) and classID in (1,2,3,4,17) group by 1";
	$sth1=$dbhu->prepare($sql);
	$sth1->execute($cid);
	my $classID;
	my $vrecords;
	my $C={};
	while (($classID,$vrecords)=$sth1->fetchrow_array())
	{
		$C->{$classID}=$vrecords;
	}
	$sth1->finish();
	$C->{1}=$C->{1} || 0;
	$C->{2}=$C->{2} || 0;
	$C->{3}=$C->{3} || 0;
	$C->{4}=$C->{4} || 0;
	$C->{17}=$C->{17} || 0;
	print MAIL "$C->{1},$C->{3},$C->{2},$C->{17},$C->{4}\n";
}
$sth->finish();
close(MAIL);
my $subject="Orange Record Processing Report";
my $msg = MIME::Lite->new(
    From    => $mail_mgr_addr,
    To      => $notify_email_addr,
    Subject => $subject,
    Type    => 'multipart/mixed',
);

$msg->attach(
    Type     => 'text/csv',
    Path     => $tfile,
    Filename => 'orange_record_processing.csv'
);
$msg->send;
