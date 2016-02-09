#!/usr/bin/perl
# *****************************************************************************************
# mta_edit_ramp_up.cgi
#
# this page edits a record to mta_ramp_up table 
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
my $mta_id;
my $class_id;
my $id= $query->param('id');
my $umaxrecs= $query->param('umaxrecs');

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

$sql="select mta_id,class_id from mta_ramp_up where id=?";
$sth=$dbhq->prepare($sql);
$sth->execute($id);
($mta_id,$class_id)=$sth->fetchrow_array();
$sth->finish();

$sql = "update mta_ramp_up set max_records_per_ip=$umaxrecs where id=$id";
$rows = $dbhu->do($sql);
if ($dbhu->err() != 0)
{
	$errmsg = $dbhu->errstr();
	util::logerror("Updating mta_ramp_up record $sql: $errmsg");
	exit(0);
}
#
print "Location: mta_ramp_up.cgi?mta_id=$mta_id&class_id=$class_id\n\n";
$pms->clean_up();
exit(0);
