#!/usr/bin/perl

# *****************************************************************************************
# ins_url.cgi
#
# this page adds records to brand_url_info 
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
my $url_list = $query->param('curl');
$url_list =~ s/[\n\r\f\t]/\|/g ;
$url_list =~ s/\|{2,999}/\|/g ;
@url_array = split '\|', $url_list;
foreach $curl (@url_array)
{
$curl =~ s/'/''/g;
$curl =~ s/\x96/-/g;
#
$sql = "insert into brand_url_info(brand_id,url_type,url) values($bid,'$type','$curl')"; 
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
<title>Add Brand URLs</title>
</head>
<body>
<p><b>URLs successfully added.  Close this window and refresh the main window to see the new URLs.</b></br>
</body>
</html>
end_of_html
$util->clean_up();
exit(0);
