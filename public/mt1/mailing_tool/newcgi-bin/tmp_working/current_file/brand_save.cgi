#!/usr/bin/perl
# *****************************************************************************************
# brand_save.cgi
#
# this page saves the copy brand changes 
#
# History
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $sql;
my $dbh;
my $rows;
my $errmsg;
my $oldaid = $query->param('oldaid');
my $aname = $query->param('brand_name');
my $client_id= $query->param('cid');
my $newaid;

# connect to the util database

$util->db_connect();
$dbh = $util->get_dbh;

# check for login

my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}

$sql = "insert into client_brand_info(client_id,brand_name,others_ns1,others_ns2,yahoo_ns1,yahoo_ns2,others_ip,yahoo_ip,mailing_addr1,mailing_addr2,whois_email,abuse_email,personal_email,others_host,yahoo_host,header_text,footer_text,footer_variation,status,footer_font_id,footer_color_id,footer_bg_color_id,cleanser_ns1,cleanser_ns2) select $client_id,'$aname',others_ns1,others_ns2,yahoo_ns1,yahoo_ns2,others_ip,yahoo_ip,mailing_addr1,mailing_addr2,whois_email,abuse_email,personal_email,others_host,yahoo_host,header_text,footer_text,footer_variation,status,footer_font_id,footer_color_id,footer_bg_color_id,cleanser_ns1,cleanser_ns2 from client_brand_info where brand_id=$oldaid"; 
$rows = $dbh->do($sql);
if ($dbh->err() != 0)
{
	$errmsg = $dbh->errstr();
	util::logerror("Updating client_brand_info $sql: $errmsg");
	exit(0);
}
$sql = "select max(brand_id) from client_brand_info where brand_name='$aname'";
$sth = $dbh->prepare($sql);
$sth->execute();
($newaid) = $sth->fetchrow_array();
$sth->finish();

$sql = "insert into brand_url_info(brand_id,url_type,url) select $newaid,url_type,url from brand_url_info where brand_id=$oldaid";
$rows=$dbh->do($sql);
$sql = "insert into brand_host(brand_id,server_name,server_type) select $newaid,server_name,server_type from brand_host where brand_id=$oldaid";
$rows=$dbh->do($sql);

print "Location: edit_client_brand.cgi?bid=$newaid&cid=$client_id&mode=U\n\n";

# exit function

$util->clean_up();
exit(0);
