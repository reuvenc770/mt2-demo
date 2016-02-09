#!/usr/bin/perl
# *****************************************************************************************
# clientgroup_del_mult.cgi
#
# this page deletes a ClientGroup 
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

my @gid= $query->param('gid');
my $cid= $query->param('cid');

foreach my $g (@gid)
{
$sql = "delete from ClientGroupClients where client_group_id=$g and client_id=$cid";
$rows = $dbhu->do($sql);
if ($dbhu->err() != 0)
{
	$errmsg = $dbhu->errstr();
	util::logerror("Deleting ClientGroup record $sql: $errmsg");
	exit(0);
}
}
print "Location: clientgroup_list.cgi\n\n";
$pms->clean_up();
exit(0);
