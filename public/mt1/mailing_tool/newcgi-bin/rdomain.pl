#!/usr/bin/perl
# *****************************************************************************************
# update_list_cnt.pl
#
# History
# Jim Sobeck,   04/09/03,   Created
# *****************************************************************************************

use strict;
use lib "/var/www/html/newcgi-bin";
use util;
use util_mail;

my $util = util->new;
my $notfound;
my $i;
my $sth;
my $sth1;
my $user_id;
my $sth2;
my $dbh;
my $sql;
my $rows;
my $cdate = localtime();
my $program = "update_list_cnt.pl";
my $errmsg;
my $bin_dir_http;
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
my ($gender);
my ($first_name,  $middle_name,      $last_name);
my ($birth_date,  $address,          $address2);
my ($city,        $state,            $zip);
my ($country,     $marital_status,   $occupation);
my ($job_status,  $household_income, $education_level);
my ($date_captured, $member_source, $phone, $source_url);
my @BAD_WORDS = (
"thelocalgig.com",
"brightyellow.net"
);
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

# connect to the util database

$| = 1;
my $dbhq;
my $dbhu;
my $domain_id;
my $domain;
my $bid;

($dbhq,$dbhu)=$util->get_dbh();
open (FILE, "<$ARGV[0]") or die "Can't open file to read\n";
while (<FILE>) {
	chomp;
	$_=~s/\n|\r//;
	$domain=$_;
	my $bname;
	$sql="select brandID,brand_name from brand_available_domains bad, client_brand_info cb where domain='$domain' and bad.brandID=cb.brand_id";
	$sth=$dbhu->prepare($sql);
	$sth->execute();
	if (($bid,$bname)=$sth->fetchrow_array())
	{
		print "$bid - $bname - $domain\n";
		$sql="delete from brand_available_domains where domain='$domain'";
#		my $orws=$dbhu->do($sql); 
	}
	$sth->finish();
	$sql="select b.brand_id,brand_name from brand_url_info b,client_brand_info cb where url='$domain' and b.brand_id=cb.brand_id";
	$sth=$dbhu->prepare($sql);
	$sth->execute();
	if (($bid,$bname)=$sth->fetchrow_array())
	{
		print "$bid - $bname - $domain\n";
		$sql="delete from brand_url_info where url='$domain'";
#		my $orws=$dbhu->do($sql); 
	}
	$sth->finish();
} ## end while loop
close FILE;
$util->clean_up();
exit(0);
