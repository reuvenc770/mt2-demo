#!/usr/bin/perl

# ******************************************************************************
# master_schedule_copy_save.cgi
#
# this page saves information about the copied schedule 
#
# History
# ******************************************************************************

# include Perl Modules

use strict;
use CGI;
use Net::FTP;
use util;
use thirdparty;

# get some objects to use later

my $util = util->new;
my $thirdparty = thirdparty->new;
my $query = CGI->new;
my $sth;
my $sth1;
my $sql;
my $rows;
my $images = $util->get_images_url;
my $company;
my $network_id;
my $daycnt;
my $exclude_from_brands_w_articles;
my $campaign_name;
my $disp_msg;
my $brand_id;
my $daycnt2;
my $daycnt1;
my $temp_date;
my $adv_id;
my $stime;
my $priority;

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
#
print "Content-type: text/html\n\n";
print<<"end_of_html";
<html>
<head><title>Copy Success Page</title></head>
<body>
<center>
end_of_html
$sql="delete from CopyScheduleClient";
my $rows=$dbhu->do($sql);
my @networks = $query->param('network');
foreach my $network (@networks) 
{
	$sql="insert into CopyScheduleClient(client_id) values($network)";
	$rows=$dbhu->do($sql);
}
print "<br>Clients to be copied updated.\n";
print "<br><a href=\"/cgi-bin/mainmenu.cgi\"><img src=\"/images/home.gif\" border=0></a>\n";
print "</body></html>\n";
$util->clean_up();
close(LOG);
exit(0);
