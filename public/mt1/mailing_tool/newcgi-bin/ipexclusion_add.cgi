#!/usr/bin/perl
# *****************************************************************************************
# ipexclusion_add.cgi
#
# this page adds a new IpExclusion
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

my $ipname= $query->param('ipname');
if ($ipname eq "")
{
	util::logerror("Exclusion Name cannot be blank");
    $pms->clean_up();
    exit(0);
}
my $cnt;
$sql="select count(*) from IpExclusion where IpExclusion_name=?";
$sth=$dbhu->prepare($sql);
$sth->execute($ipname);
($cnt)=$sth->fetchrow_array();
$sth->finish();
if ($cnt > 0)
{
	util::logerror("Name $ipname already exists");
    $pms->clean_up();
    exit(0);
}

$ipname = $dbhq->quote($ipname);
$sql = "insert into IpExclusion(IpExclusion_name) values ($ipname)";
$rows = $dbhu->do($sql);
if ($dbhu->err() != 0)
{
	$errmsg = $dbhu->errstr();
	util::logerror("Inserting IpExclusion record $sql: $errmsg");
	exit(0);
}
#
# Get group_id just added
#
my $cid;
$sql = "select max(IpExclusionID) from IpExclusion where group_name=$ipname";
$sth = $dbhq->prepare($sql);
$sth->execute();
($cid) = $sth->fetchrow_array();
$sth->finish();
print "Location: ipexclusion_edit.cgi?gid=$cid\n\n";
$pms->clean_up();
exit(0);
