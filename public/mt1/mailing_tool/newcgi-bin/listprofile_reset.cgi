#!/usr/bin/perl
# *****************************************************************************************
# listprofile_reset.cgi
#
# Removes a list profile 
#
# History
# Jim Sobeck, 5/31/05, Creation
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
my $errmsg;
my $user_id;
my $list_name;
my %checkit = ( 'Y' => 'CHECKED', 'N' => '' );
my $images = $util->get_images_url;

# connect to the util database

###$util->db_connect();

my ($dbhq,$dbhu)=$util->get_dbh();
###$dbh = $util->get_dbh;

# check for login

$user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}
my $pid = $query->param('pid');
$sql = "update list_profile set last_email_user_id=0 where profile_id=$pid";
my $rows=$dbhu->do($sql);
print "Location: /cgi-bin/listprofile_list.cgi\n\n";
