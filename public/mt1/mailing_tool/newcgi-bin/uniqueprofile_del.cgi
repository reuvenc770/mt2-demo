#!/usr/bin/perl

# ******************************************************************************
# uniqueprofile_delete.cgi 
#
# this page removes informaiton the UniqueProfile table
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
my $sql;
my $dbh;
my $sid;
my $i;
my $class;
my $errmsg;
my $rows;
my $images = $util->get_images_url;
my $pmesg="";
my @url_array;
my $cnt;
my $curl;
my $temp_cnt;
my $btype;
my $nl_id;

$cnt=0;
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
# Remove old url information
#
my $pid= $query->param('pid');
$sql="update UniqueProfile set status='D' where profile_id=$pid"; 
$rows=$dbhu->do($sql);
print "Location: /cgi-bin/uniqueprofile_list.cgi\n\n";
