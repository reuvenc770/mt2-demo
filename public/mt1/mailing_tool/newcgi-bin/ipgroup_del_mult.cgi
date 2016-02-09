#!/usr/bin/perl
# *****************************************************************************************
# ipgroup_del_mult.cgi
#
# this page deletes IP Groups 
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
my $userid;
my $dname;
my $gid;

# connect to the pms database
my ($dbhq,$dbhu)=$util->get_dbh();

# check for login

my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}

my $userDataRestrictionWhereClause = '';

$util->getUserData({'userID' => $user_id});
my $externalUser = $util->getUserData()->{'isExternalUser'};

if($util->getUserData()->{'isExternalUser'} == 1)
{
	$userDataRestrictionWhereClause = qq|
        userID = $user_id AND
    |;   
}


# get fields from the form

my $gname= $query->param('gname');
if ($gname eq "")
{
	exit;
}
print "Content-type: text/html\n\n";
print<<"end_of_html";
<html><head></head><body>
<center>
<h3>IP Groups Deleted</h3>
<table>
end_of_html
$sql="select group_id,group_name from IpGroup where $userDataRestrictionWhereClause group_name like '$gname%' and group_id != 0";
my $sth1a=$dbhu->prepare($sql);
$sth1a->execute();

# We should be "safe" in the loop because the only group IDs that would be used would be those that already made it past the restriction in the above query.
while (($gid,$gname)=$sth1a->fetchrow_array())
{
	print "<tr><td>$gid - $gname</td></tr>";
	$sql="update IpGroup set status='Deleted' where $userDataRestrictionWhereClause group_id=$gid";
	$rows=$dbhu->do($sql);
	$sql="update DailyDealSettingDetail set group_id=0 where group_id=$gid";
	$rows=$dbhu->do($sql);
	$sql="update UniqueSlot set status='D' where ip_group_id=$gid and status='A'";
	$rows = $dbhu->do($sql);

	$sql="select unq_id from unique_campaign where group_id=? and send_date >= curdate() and status='START'";
	my $sth2=$dbhu->prepare($sql);
	$sth2->execute($gid);
	my $uid;
	while (($uid)=$sth2->fetchrow_array())
	{
		$sql="delete from UniqueSchedule where unq_id=$uid";
		$rows = $dbhu->do($sql);
	}
	$sql="delete from unique_campaign where group_id=$gid and send_date >= curdate() and status='START'";
	$rows = $dbhu->do($sql);
	$sql="update unique_campaign set status='CANCELLED',cancel_reason='IP Group Deleted' where group_id=$gid and send_date >= date_sub(curdate(),interval 2 day) and send_date <= curdate()  and status in ('START','PENDING','PAUSED','PRE-PULLING','SLEEPING','INJECTING')";
	$rows = $dbhu->do($sql);
}
$sth1a->finish();
print "</table></body></html>";
