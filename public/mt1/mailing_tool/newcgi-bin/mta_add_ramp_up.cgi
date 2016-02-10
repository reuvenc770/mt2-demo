#!/usr/bin/perl
# *****************************************************************************************
# mta_add_ramp_up.cgi
#
# this page adds a record to mta_ramp_up table 
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
my $mta_id= $query->param('mta_id');
my $class_id= $query->param('class_id');
my $maxrecs= $query->param('maxrecs');

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

$sql = "insert into mta_ramp_up(mta_id,class_id,max_records_per_ip) values ($mta_id,$class_id,$maxrecs)";
$rows = $dbhu->do($sql);
if ($dbhu->err() != 0)
{
	$errmsg = $dbhu->errstr();
	util::logerror("Inserting mta_ramp_up record $sql: $errmsg");
	exit(0);
}
#
print "Location: mta_ramp_up.cgi?mta_id=$mta_id&class_id=$class_id\n\n";
$pms->clean_up();
exit(0);
