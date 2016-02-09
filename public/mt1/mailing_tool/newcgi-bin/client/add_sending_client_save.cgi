#!/usr/bin/perl
# ******************************************************************************
# add_sending_client_save.cgi
#
# this page saves information to the client_data_source table 
#
# History
# ******************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $sth1;
my $sql;
my $rows;
my $images = $util->get_images_url;
my $company;
my $network_id;
my $client_id=$query->param('client_id');

# connect to the util database
my ($dbhq,$dbhu)=$util->get_dbh();

# check for login
my $user_id = util::check_security();
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
	$util->clean_up();
    exit(0);
}
$sql="delete from client_data_source where client_id=$client_id";
$rows=$dbhu->do($sql);
#
print "Content-type: text/html\n\n";
print<<"end_of_html";
<html>
<head><title>Success Page</title></head>
<body>
<center>
end_of_html
my @networks = $query->param('network');
foreach my $network (@networks) 
{
	$sql="insert into client_data_source(client_id,sending_client_id) values($client_id,$network)";
	$rows= $dbhu->do($sql);
}
$sql = "select first_name from user where user_id=$client_id"; 
$sth1 = $dbhq->prepare($sql);
$sth1->execute();
($company) = $sth1->fetchrow_array();
$sth1->finish();
print "Successfully updated $company<br>\n";
print "<br><a href=\"/cgi-bin/mainmenu.cgi\"><img src=\"/images/home.gif\" border=0></a>\n";
print "</body></html>\n";
$util->clean_up();
exit(0);
