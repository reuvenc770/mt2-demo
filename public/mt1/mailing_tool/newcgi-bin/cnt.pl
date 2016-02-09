#!/usr/bin/perl

use strict;
use lib "/var/www/html/newcgi-bin";
use util;
use util_mail;

my $util = util->new;
my $sth;
my $sth1;
my $sth2;
my $dbh;
my $dbh2;
my $try_again;
my $table;
my $sql;
my $rows;
my $cdate = localtime();
my $program = "update_list_cnt.pl";
my $errmsg;
my $cnt;
my $total_cnt;
my $aol_cnt;
my $list_aol_cnt;
my $list_hotmail_cnt;
my $list_msn_cnt;
my $list_cnt;
my $list_yahoo_cnt;
my $list_foreign_cnt;
my $last_email_user_id;
my $max_emails;
my $clast60;
my $aolflag;
my $openflag;
my $first_email_user_id;
my $addrec;
my $begin;
my $end;
my $list_str;
my $bend;
#
#  Set up array for servers
#
my $sarr_cnt = 6;
my $cnt2;
my @sarry = (
	["mail11","2"],
	["mail12","2"],
	["mail13","2"],
	["dbbox1","2"],
	["dbbox2","2"],
	["dbbox3","2"]
);
my @AOLDOMAIN;
my @HOTMAILDOMAIN;
my @YAHOODOMAIN;
my $list_id;
my $domain_id;
my $source_url;

# connect to the util database

$| = 1;

$util->db_connect();
$dbh = $util->get_dbh;

$sql="select domain_id from email_domains where domain_class=1";
$sth=$dbh->prepare($sql);
$sth->execute();
while (($domain_id)=$sth->fetchrow_array())
{
	$AOLDOMAIN[$domain_id]=1;
}
$sth->finish();
$sql="select domain_id from email_domains where domain_class=2";
$sth=$dbh->prepare($sql);
$sth->execute();
while (($domain_id)=$sth->fetchrow_array())
{
	$HOTMAILDOMAIN[$domain_id]=1;
}
$sth->finish();
$sql="select domain_id from email_domains where domain_class=3";
$sth=$dbh->prepare($sql);
$sth->execute();
while (($domain_id)=$sth->fetchrow_array())
{
	$YAHOODOMAIN[$domain_id]=1;
}
$sth->finish();

my $list_str="";
$sql="select list_id from list where user_id=22 and status='A' and list_type!='CHUNK'";
$sth2 = $dbh->prepare($sql);
$sth2->execute();
while (($list_id) = $sth2->fetchrow_array())
{
	$list_str=$list_str.$list_id.",";
}
$sth2->finish();
$_=$list_str;
chop;
$list_str=$_;

$begin=165962600;
$end=180106014;
my $did;
my $cdate;
while ($begin < $end)
{
	$bend=$begin+99999;
	$sql="select domain_id,date(capture_date),source_url from email_list where list_id in ($list_str) and email_user_id between ? and ? and capture_date >= '2006-09-01'";
	$sth2 = $dbh->prepare($sql);
	$sth2->execute($begin,$bend);
	print "<$sql> $begin $bend\n";
	while (($domain_id,$cdate,$source_url)=$sth2->fetchrow_array())
	{
                if (exists($AOLDOMAIN[$domain_id]))
                {
                     $domain_id=1;
                }
                elsif (exists($HOTMAILDOMAIN[$domain_id]))
                {
                     $domain_id=2;
                }
                elsif (exists($YAHOODOMAIN[$domain_id]))
                {
                     $domain_id=3;
                }
                else
                {
                     $domain_id=4;
                }
		$sql="insert into tmp_jim(cdate,domain_id,source_url) values('$cdate',$domain_id,'$source_url')";
		my $rows = $dbh->do($sql);
	}
	$sth2->finish();
	$begin=$begin+100000;
}
