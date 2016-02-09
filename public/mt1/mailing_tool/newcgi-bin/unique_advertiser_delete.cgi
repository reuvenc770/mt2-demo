#!/usr/bin/perl
# *****************************************************************************************
# unique_advertiser_delete .cgi
#
# this page deletes a UniqueScheduleAdvertiser
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

my $usa_id= $query->param('usa_id');
$sql = "delete from UniqueScheduleAdvertiser where usa_id=$usa_id";
$rows = $dbhu->do($sql);
if ($dbhu->err() != 0)
{
	$errmsg = $dbhu->errstr();
	util::logerror("Deleting Unique Schedule Advertiser record $sql: $errmsg");
	exit(0);
}
$sql = "delete from UniqueAdvertiserFrom where usa_id=$usa_id";
$rows = $dbhu->do($sql);
$sql = "delete from UniqueAdvertiserSubject where usa_id=$usa_id";
$rows = $dbhu->do($sql);
$sql = "delete from UniqueAdvertiserCreative where usa_id=$usa_id";
$rows = $dbhu->do($sql);
print "Location: unique_advertiser_main.cgi\n\n";
$pms->clean_up();
exit(0);
