#!/usr/bin/perl
# *****************************************************************************************
# ipgroup_add.cgi
#
# this page adds a new IpGroup 
#
# History
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $pms = util->new;
my $util = $pms;
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

my $userDataRestrictionWhereClause = '';

$util->getUserData({'userID' => $user_id});

if($util->getUserData()->{'isExternalUser'} == 1)
{
	$userDataRestrictionWhereClause = qq|
        userID = $user_id AND
    |;
}


# get fields from the form

my $ipname= $query->param('ipname');
my $colo=$query->param('colo');
my $chunk=$query->param('chunk');
if ($chunk eq "")
{
	$chunk=0;
}
if ($ipname eq "")
{
	util::logerror("Group Name cannot be blank");
    $pms->clean_up();
    exit(0);
}
my $othrottle= $query->param('othrottle');
if ($othrottle eq "")
{
	util::logerror("Outbound Throttle cannot be blank");
    $pms->clean_up();
    exit(0);
}
my $goodmail_enabled= $query->param('goodmail_enabled');
my $domainkeys_enabled= $query->param('domainkeys_enabled');

my $cnt;
$sql="select count(*) from IpGroup where $userDataRestrictionWhereClause group_name=? and status='A'";
$sth=$dbhu->prepare($sql);
$sth->execute($ipname);
($cnt)=$sth->fetchrow_array();
$sth->finish();
if ($cnt > 0)
{
	util::logerror("Group Name $ipname already exists");
    $pms->clean_up();
    exit(0);
}

$ipname = $dbhq->quote($ipname);
$sql = "insert into IpGroup(userID, group_name,outbound_throttle,goodmail_enabled,colo,chunk,domainkeys_enabled) values ($user_id, $ipname,$othrottle,'$goodmail_enabled','$colo',$chunk,'$domainkeys_enabled')";
$rows = $dbhu->do($sql);
if ($dbhu->err() != 0)
{
	$errmsg = $dbhu->errstr();
	util::logerror("Inserting IpGroup record $sql: $errmsg");
	exit(0);
}
#
# Get group_id just added
#
my $cid;
$sql = "select max(group_id) from IpGroup where $userDataRestrictionWhereClause group_name=$ipname";
$sth = $dbhq->prepare($sql);
$sth->execute();
($cid) = $sth->fetchrow_array();
$sth->finish();
print "Location: ipgroup_edit.cgi?gid=$cid\n\n";
$pms->clean_up();
exit(0);
