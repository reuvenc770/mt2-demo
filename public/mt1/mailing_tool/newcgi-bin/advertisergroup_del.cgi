#!/usr/bin/perl
# *****************************************************************************************
# advertisergroup_del.cgi
#
# this page deletes a AdvertiserCategoryGroup 
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
my $sql;
my $dbh;
my $rows;
my $errmsg;
my $userid;
my $dname;

# connect to the pms database
my ($dbhq,$dbhu)=$pms->get_dbh();

# check for login

my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $pms->clean_up();
    exit(0);
}

# get fields from the form

my $gid= $query->param('gid');

$sql = "delete from AdvertiserCategoryGroup where advertiser_group_id=$gid";
$rows = $dbhu->do($sql);
$sql = "delete from AdvertiserCategoryGroupCat where advertiser_group_id=$gid";
$rows = $dbhu->do($sql);
if ($dbhu->err() != 0)
{
	$errmsg = $dbhu->errstr();
	util::logerror("Deleting AdvertiserCategoryGroup record $sql: $errmsg");
	exit(0);
}
print "Location: advertisergroup_list.cgi\n\n";
$pms->clean_up();
exit(0);
