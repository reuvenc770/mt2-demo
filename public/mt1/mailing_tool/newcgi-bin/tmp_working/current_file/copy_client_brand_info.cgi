#!/usr/bin/perl

# *****************************************************************************************
# copy_client_brand_info.cgi
#
# this page displays middle frame of the category brand association screen 
#
# History
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $pms = util->new;
my $query = CGI->new;
my $sth;
my $sth1;
my $sth2;
my $sql;
my $dbh;
my $errmsg;
my $user_id;
my $link_id;
my $refurl;
my $bgcolor;
my $reccnt;
my $images = $pms->get_images_url;
my $alt_light_table_bg = $pms->get_alt_light_table_bg;
my $light_table_bg = $pms->get_light_table_bg;
my $table_text_color = $pms->get_table_text_color;
my $status_name;
my $status;
my $cat_id;
my $category_name;
my $domain_name;
my $to_bid = $query->param('to_bid');
my $from_bid = $query->param('brand_id');
my $to_client = $query->param('to_client');
my $from_clientid = $query->param('from_clientid');
if (($to_bid eq "") or ($from_bid eq ""))
{
	print "Content-type: text/html\n\n";
	print<<"end_of_html";
<html><head><title>Error</title></head>
<body>
<center><h3>You must select both a brand to copy TO and a brand to copy From</h3></h3></center>
</body></html>
end_of_html
	exit(0);
}
my $sid;
my $sname;
my $bname;
my $bname1;

# connect to the pms database

$pms->db_connect();
$dbh = $pms->get_dbh;

# check for login

$user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $pms->clean_up();
    exit(0);
}

$sql="delete from category_brand_info where brand_id=$to_bid";
my $rows=$dbh->do($sql);
$sql = "insert into category_brand_info(brand_id,subdomain_id) select $to_bid,subdomain_id from category_brand_info where brand_id=$from_bid";
$rows=$dbh->do($sql);

$sql="select brand_name from client_brand_info where brand_id=$to_bid";
$sth = $dbh->prepare($sql) ;
$sth->execute();
($bname) = $sth->fetchrow_array();
$sth->finish();
$sql="select brand_name from client_brand_info where brand_id=$from_bid";
$sth = $dbh->prepare($sql) ;
$sth->execute();
($bname1) = $sth->fetchrow_array();
$sth->finish();
#
# print out the html page
print "Content-type: text/plain\n\n";
print <<"end_of_html";
<html>

<head>
<title>New Page 2</title>
</head>
<body>
<br>
<br>
<br>
<center><h3>Category Info successfully copied from $bname1 to $bname.<h3><br>
<a href="/cgi-bin/disp_client_brand_info.cgi?clientid=$to_client&brand_id=$to_bid&action=Go">Click here</a> to see Category Selections
</center>
</body>
</html>
end_of_html

