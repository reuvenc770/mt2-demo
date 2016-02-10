#!/usr/bin/perl

# *****************************************************************************************
# camp_revenue_save.cgi
#
# this page saves the revenue info
#
# History
# Jim Sobeck	08/08/2001		Creation 
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
my $lists;
my @list_ids;
my $email_addr;
my $rows;
my $errmsg;
my $campaign_id;
my $old_campaign_id;
my $id;
my $campaign_name;
my $subject;
my $image_url;
my $title;
my $subtitle;
my $date_str;
my $greeting;
my $introduction;
my $k;
my $status;
my %checked = ( 'on' => 'Y', '' => 'N' );

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

$campaign_id = $query->param('campaign_id');
my $ctype = $query->param('ctype');
my $crevenue = $query->param('crevenue');
my $cinput = $query->param('cinput');
my $caction_cnt = $query->param('caction_cnt');

# Update the information in the tables
$sql = "update campaign set campaign_type='$ctype',revenue=$crevenue,action_cnt=$caction_cnt,input_revenue=$cinput where campaign_id=$campaign_id";
$rows = $dbhu->do($sql);

print "Location: mainmenu.cgi\n\n";
$util->clean_up();
exit(0);
