#!/usr/bin/perl

# *****************************************************************************************
# ins_host.cgi
#
# this page adds records to brand_host
#
# History
# Jim Sobeck, 06/15/05, Creation
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
my $sid;
my $errmsg;
my $images = $util->get_images_url;
my $curl;
my $ip_addr;
my @url_array;

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
#
# Remove old subject information
#
my $bid = $query->param('bid');
my $type= $query->param('type');
#
if (($type ne "A") and ($type ne "T"))
{
my $url_list = $query->param('curl');
$url_list =~ s/[\n\r\f\t]/\|/g ;
$url_list =~ s/\|{2,999}/\|/g ;
@url_array = split '\|', $url_list;
foreach $curl (@url_array)
{
$curl =~ s/'/''/g;
$curl =~ s/\x96/-/g;
#
$sql = "insert into brand_host(brand_id,server_type,server_name) values($bid,'$type','$curl')"; 
$sth = $dbh->do($sql);
}
}
else
{
	my $ip_addr = $query->param('ip_addr');
	my $curl= $query->param('curl');
	$sql = "insert into brand_host(brand_id,server_type,server_name,ip_addr) values($bid,'$type','$curl','$ip_addr')"; 
	$sth = $dbh->do($sql);
}
#
# Display the confirmation page
#
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Add Brand Hosts</title>
</head>
<body>
<p><b>Hosts successfully added.  Close this window and refresh the main window to see the new Hosts.</b></br>
</body>
</html>
end_of_html
$util->clean_up();
exit(0);
