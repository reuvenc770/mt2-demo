#!/usr/bin/perl

# *****************************************************************************************
# unique_schedule_camp_del.cgi
#
# Delete a campaign information 
#
# History
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
my $rows;
my $errmsg;
my $user_id;
my $send_date;
my $cstatus;
my  $server_id;
my $uid = $query->param('uid');
my $sdate = $query->param('sdate');
my $nid= $query->param('nid');
my $mtaid= $query->param('mtaid');

my ($dbhq,$dbhu)=$util->get_dbh();

# check for login

$user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}

# update this campaign's record
$sql="select send_date,status,server_id from unique_campaign where unq_id=?";
$sth=$dbhu->prepare($sql);
$sth->execute($uid);
($send_date,$cstatus,$server_id)=$sth->fetchrow_array();
$sth->finish();
if (($cstatus ne "START") or ($server_id > 0))
{
	print "Location: unique_schedule.cgi\n\n";
}

$sql = "update campaign set deleted_date = now() where scheduled_date='$send_date' and id='$uid'";
$rows = $dbhu->do($sql);
if ($dbhu->err() != 0)
{
	$errmsg = $dbhu->errstr();
	util::logerror("Updating campaign record for $uid: $errmsg");
	exit(0);
}
$sql="delete from UniqueSchedule where unq_id=$uid";
$rows = $dbhu->do($sql);
$sql="delete from unique_campaign where unq_id=$uid";
$rows = $dbhu->do($sql);

print "Location: unique_schedule.cgi?sdate=$sdate&nid=$nid&mtaid=$mtaid\n\n";

$util->clean_up();
exit(0);
