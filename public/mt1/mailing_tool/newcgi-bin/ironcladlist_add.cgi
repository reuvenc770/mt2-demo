#!/usr/bin/perl
# *****************************************************************************************
# ironcladlist_add.cgi
#
# this page adds a new IronCladList
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
my $listName= $query->param('listName');
my $listGroupID= $query->param('listGroupID');
my $firstip= $query->param('firstip');
my $lastip= $query->param('lastip');
my $domain= $query->param('domain');
my $xperip= $query->param('xperip');
my $ipc= $query->param('ipc');
my $listadkhours= $query->param('listadkhours');
my $adkServerID= $query->param('adkServerID');


$sql = "insert into IronCladList(listName,listGroupID,firstip,lastip,domain,xperip,ipc,listadkhours,adkServerID) values('$listName',$listGroupID,$firstip,$lastip,'$domain',$xperip,'$ipc','$listadkhours',$adkServerID)";
$rows = $dbhu->do($sql);
if ($dbhu->err() != 0)
{
	$errmsg = $dbhu->errstr();
	util::logerror("Inserting IronCladList record $sql: $errmsg");
	exit(0);
}

my $listID;
$sql="select LAST_INSERT_ID()";
my $sth=$dbhu->prepare($sql);
$sth->execute();
($listID)=$sth->fetchrow_array();
$sth->finish();
$sql="insert into IronCladGroupLists(IronCladGroupID,listID) values($gid,$listID)";
$rows = $dbhu->do($sql);

print "Location: ironcladgroup_edit.cgi?gid=$gid\n\n"; 
$pms->clean_up();
exit(0);
