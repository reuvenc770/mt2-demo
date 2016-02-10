#!/usr/bin/perl
# *****************************************************************************************
# clientgroup_add.cgi
#
# this page adds a new ClientGroup 
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

my $userDataRestrictionWhereClause = '';

$pms->getUserData({'userID' => $user_id});

if($pms->getUserData()->{'isExternalUser'} == 1)
{
	$userDataRestrictionWhereClause = qq|
        userID = $user_id AND
    |;
}

# get fields from the form

my $gname= $query->param('gname');
my $excludeFromSuper = $query->param('excludeFromSuper');
if ($excludeFromSuper eq "")
{
	$excludeFromSuper="N";
}
if ($gname eq "")
{
	util::logerror("Group Name cannot be blank");
    $pms->clean_up();
    exit(0);
}

$gname = $dbhq->quote($gname);
my $BusinessUnit;

$sql = "select BusinessUnit from UserAccounts where user_id=?"; 
my $sth1 = $dbhq->prepare($sql) ;
$sth1->execute($user_id);
($BusinessUnit) = $sth1->fetchrow_array();
$sth1->finish();


$sql = "insert into ClientGroup(userID, group_name,status,excludeFromSuper,BusinessUnit) values ($user_id, $gname,'A','$excludeFromSuper','$BusinessUnit')";
$rows = $dbhu->do($sql);
if ($dbhu->err() != 0)
{
	$errmsg = $dbhu->errstr();
	util::logerror("Inserting ClientGroup record $sql: $errmsg");
	exit(0);
}
#
# Get group_id just added
#
my $cid;
$sql = "select max(client_group_id) from ClientGroup where $userDataRestrictionWhereClause group_name=$gname and status='A'";
$sth = $dbhq->prepare($sql);
$sth->execute();
($cid) = $sth->fetchrow_array();
$sth->finish();
print "Location: clientgroup_edit.cgi?gid=$cid\n\n";
$pms->clean_up();
exit(0);
