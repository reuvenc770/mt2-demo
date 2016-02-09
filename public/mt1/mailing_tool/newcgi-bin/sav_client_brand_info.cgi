#!/usr/bin/perl

# *****************************************************************************************
# sav_client_brand_info.cgi
#
# this page saves the client brand category selection
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
my $sid;
my $dbh;
my $list_id;
my $iopt;
my $rows;
my $errmsg;
my $campaign_id;
my $id;
my $campaign_name;
my $k;
my $cname;
my $status;
my $aid;
my %checked = ( 'on' => 'Y', '' => 'N' );
my $list_cnt;

# connect to the util database

###$util->db_connect();

my ($dbhq,$dbhu)=$util->get_dbh();
###$dbh = $util->get_dbh;

# check for login

my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}

my $client_id = $query->param('clientid');
my $brand_id = $query->param('brand_id');

if ($brand_id > 0)
{
# Update the information in the tables
$sql = "delete from category_brand_info where brand_id=$brand_id";
$rows = $dbhu->do($sql);

$sql = "select subdomain_id from brandsubdomain_info"; 
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($sid) = $sth->fetchrow_array())
{
   	$iopt = $query->param("brandid_$sid");
   	if ($iopt)
   	{
		$sql = "insert into category_brand_info(subdomain_id,brand_id) values($sid,$brand_id)"; 
		$rows = $dbhu->do($sql);
	}
}
$sth->finish();
}
print "Location: disp_client_brand_info.cgi?clientid=$client_id&brand_id=$brand_id\n\n";
$util->clean_up();
exit(0);
