#!/usr/bin/perl
# *****************************************************************************************
# unique_advertiser_add.cgi
#
# this page adds a new UniqueScheduleAdvertiser
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
my $usa_id;

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

my $aid= $query->param('aid');
my $uname= $query->param('uname');
$uname=~s/'/''/g;
my @cid= $query->param('creative');
my @sid= $query->param('csubject');
my @fid= $query->param('cfrom');
$sql = "insert into UniqueScheduleAdvertiser(name,advertiser_id,creative_id,subject_id,from_id,lastUpdated) values ('$uname',$aid,$cid[0],$sid[0],$fid[0],curdate())";
$rows = $dbhu->do($sql);
if ($dbhu->err() != 0)
{
	$errmsg = $dbhu->errstr();
	util::logerror("Inserting Unique Schedule Advertiser record $sql: $errmsg");
	exit(0);
}
$sql="select max(usa_id) from UniqueScheduleAdvertiser where name=? and advertiser_id=?";
$sth=$dbhu->prepare($sql);
$sth->execute($uname,$aid);
($usa_id)=$sth->fetchrow_array();
$sth->finish();
#
my $i=0;
while ($i <= $#cid)
{
	$sql="insert into UniqueAdvertiserCreative(usa_id,creative_id) values($usa_id,$cid[$i])";
	$rows=$dbhu->do($sql);
	$i++;
}
$i=0;
while ($i <= $#fid)
{
	$sql="insert into UniqueAdvertiserFrom(usa_id,from_id) values($usa_id,$fid[$i])";
	$rows=$dbhu->do($sql);
	$i++;
}
$i=0;
while ($i <= $#sid)
{
	$sql="insert into UniqueAdvertiserSubject(usa_id,subject_id) values($usa_id,$sid[$i])";
	$rows=$dbhu->do($sql);
	$i++;
}
print "Location: unique_advertiser_main.cgi\n\n";
$pms->clean_up();
exit(0);
