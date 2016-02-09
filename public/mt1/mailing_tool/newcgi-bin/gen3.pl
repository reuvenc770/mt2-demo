#!/usr/bin/perl
# *****************************************************************************************
# gen3.pl
#
# History
# *****************************************************************************************

use strict;
use lib "/var/www/html/newcgi-bin";
use Net::FTP;
use util;
use thirdparty;

my $util = util->new;
my $notfound;
my $i;
my $sth;
my $sth1;
my $sth2;
my $dbh;
my $sql;
my $rows;
my $cdate = localtime();
my $program = "gen3.pl";
my $errmsg;
my $email_mgr_addr;
my $bin_dir_http;
my $cnt;

$| = 1;
my $dbhq;
my $dbhu;
($dbhq,$dbhu)=$util->get_dbh();
my $third_id=$ARGV[0];
my $camp_id;
my $brand_id;
my $adv_id;
my $client_id;

$sql = "select third_party_id,campaign_id,brand_id,advertiser_id,client_id from 3rdparty_campaign where id=$third_id"; 
$sth = $dbhu->prepare($sql);
$sth->execute();
($third_id,$camp_id,$brand_id,$adv_id,$client_id) = $sth->fetchrow_array();
$sth->finish();
print "Getting $camp_id - $client_id - $dbhu\n";
thirdparty::deploy_it($dbhu,$third_id,$camp_id,$brand_id,$adv_id,$client_id);
