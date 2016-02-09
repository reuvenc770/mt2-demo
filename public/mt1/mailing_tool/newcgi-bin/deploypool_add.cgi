#!/usr/bin/perl
# *****************************************************************************************
# deploypool_add.cgi
#
# this page adds a new DeployPool 
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

my $pname= $query->param('pname');
if ($pname eq "")
{
	util::logerror("Pool Name cannot be blank");
    $pms->clean_up();
    exit(0);
}
my $gid= $query->param('gid');
my $profileid= $query->param('profileid');
my $chunksize= $query->param('chunksize');

$pname = $dbhq->quote($pname);


$sql = "insert into DeployPool(deployPoolName,client_group_id,profile_id,recsPerChunk) values($pname,$gid,$profileid,$chunksize)";
$rows = $dbhu->do($sql);
if ($dbhu->err() != 0)
{
	$errmsg = $dbhu->errstr();
	util::logerror("Inserting DeployPool record $sql: $errmsg");
	exit(0);
}
print "Location: deploypool_list.cgi\n\n";
$pms->clean_up();
exit(0);
