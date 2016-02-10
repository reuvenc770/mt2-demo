#!/usr/bin/perl
use strict;
use Sys::Hostname;
use lib "/var/www/html/newcgi-bin";
use util;

my $util = util->new;
my $sth;
my $sth1;
my $sth2;
my $sth2a;
my $dbh;
my $dbh1;
my $dbh2;
my $sql;
my $rows;
my $list_id;
my $client_id;
my @LIST;
my $domain_id;
my $cdate = localtime();
my $rows;

$| = 1;

print "Starting at $cdate\n";
$util->db_connect();
$dbh = $util->get_dbh;
my $dbh2 = DBI->connect("DBI:mysql:new_mail:update2.routename.com","db_user","sp1r3V");

$sql="select list_id,user_id from list order by list_id"; 
$sth=$dbh->prepare($sql);
$sth->execute();
while (($list_id,$client_id)=$sth->fetchrow_array())
{
	$LIST[$list_id]=$client_id;
}
$sth->finish();
my $bend;
my $email_addr;
my $begin=0;
my $end=185194464;
open(LOG,">nolist.txt");
while ($begin < $end)
{
	$bend=$begin + 99999;
	print "<$begin - $bend> \n";
	$sql="select email_addr,list_id from email_list where email_user_id between ? and ?";
unless ($dbh && $dbh->ping) {
print "connecting\n";
$util->db_connect();
$dbh = $util->get_dbh;
   }
	$sth=$dbh->prepare($sql);
	$sth->execute($begin,$bend);
	while (($email_addr,$list_id) = $sth->fetchrow_array())
	{
		if (exists($LIST[$list_id]))
		{
			$sql="insert ignore into client_email_list(client_id,email_addr) values($LIST[$list_id],'$email_addr')";
			$rows=$dbh2->do($sql);
		}
		else
		{
			print LOG "$email_addr\n"; 
		}
	}
	$sth->finish();
	$begin = $begin + 100000;
}
close LOG;
