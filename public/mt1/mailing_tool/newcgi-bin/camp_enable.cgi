#!/usr/bin/perl
# *****************************************************************************************
# camp_enable.cgi
#
# this page approves a campaign
#
# History
# Jim Sobeck, 02/01/2005, Creation 
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $count;
my $sth;
my $sql;
my $dbh;
my $template_name;
my $campaign_id = $query->param('campaign_id');
my $campaign_name;
my $images = $util->get_images_url;

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

# get campaign name

$sql = "update campaign set disable_flag='N' where campaign_id=$campaign_id and status in ('C','S')";
$sth = $dbhq->prepare($sql);
$sth->execute();
($campaign_name) = $sth->fetchrow_array();
$sth->finish();

print "Location: /cgi-bin/mainmenu.cgi\n\n";
$util->clean_up();
exit(0);
