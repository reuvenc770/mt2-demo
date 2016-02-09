#!/usr/bin/perl
# *****************************************************************************************
# clientgroup_copy.cgi
#
# this page allows you to copy a client group 
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
my $gname;
my $excludeFromSuper;

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
my $BusinessUnit;
my $gid= $query->param('gid');
$sql="select group_name,excludeFromSuper,BusinessUnit from ClientGroup where $userDataRestrictionWhereClause client_group_id=?";
$sth=$dbhu->prepare($sql);
$sth->execute($gid);
($gname,$excludeFromSuper,$BusinessUnit)=$sth->fetchrow_array();
$sth->finish();
$gname="Copy-".$gname;

$sql="insert into ClientGroup(userID, group_name,status,excludeFromSuper,BusinessUnit) values($user_id, '$gname','A','$excludeFromSuper','$BusinessUnit')";
$rows=$dbhu->do($sql);

my $newgid;
$sql="select last_insert_id()";
$sth=$dbhu->prepare($sql);
$sth->execute();
($newgid)=$sth->fetchrow_array();
$sth->finish();

$sql="insert into ClientGroupClients(client_group_id,client_id) select $newgid,client_id from ClientGroupClients where client_group_id=$gid";
$rows=$dbhu->do($sql);

print "Location: clientgroup_edit.cgi?gid=$newgid\n\n";
