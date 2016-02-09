#!/usr/bin/perl
# *****************************************************************************************
# ipgroup_del.cgi
#
# this page deletes a IpGroup 
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

$sql = "update IpGroup set status='Deleted' where group_id=$gid and group_id != 0";
$rows = $dbhu->do($sql);
if ($dbhu->err() != 0)
{
	$errmsg = $dbhu->errstr();
	util::logerror("Deleting IpGroup record $sql: $errmsg");
	exit(0);
}
$sql="update UniqueSlot set status='D' where ip_group_id=$gid and status='A'";
$rows = $dbhu->do($sql);
$sql="select unq_id from unique_campaign where group_id=$gid and send_date >= curdate() and status='START'";
my $sth2=$dbhu->prepare($sql);
$sth2->execute();
my $uid;
while (($uid)=$sth2->fetchrow_array())
{
	$sql="delete from UniqueSchedule where unq_id=$uid"; 
	$rows = $dbhu->do($sql);
}
$sth2->finish();
$sql="delete from unique_campaign where group_id=$gid and send_date >= curdate() and status='START'";
$rows = $dbhu->do($sql);
$sql="update unique_campaign set status='CANCELLED',cancel_reason='IP Group Deleted' where group_id=$gid and send_date >= date_sub(curdate(),interval 2 day) and send_date <= curdate()  and status in ('START','PENDING','PAUSED','PRE-PULLING','SLEEPING','INJECTING')";
$rows = $dbhu->do($sql);

print "Location: ipgroup_list.cgi\n\n";
$pms->clean_up();
exit(0);
