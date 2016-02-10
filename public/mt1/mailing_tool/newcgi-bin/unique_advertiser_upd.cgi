#!/usr/bin/perl
# *****************************************************************************************
# unique_advertiser_upd.cgi
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

my $usa_id= $query->param('usa_id');
my @cid= $query->param('creative');
if ($#cid < 0)
{
	$cid[0]=0;
}
my @sid= $query->param('csubject');
if ($#sid < 0)
{
	$sid[0]=0;
}
my @fid= $query->param('cfrom');
if ($#fid < 0)
{
	$fid[0]=0;
}
#$sql="delete from UniqueScheduleAdvertiser where advertiser_id=$aid";
#$rows = $dbhu->do($sql);

$sql = "update UniqueScheduleAdvertiser set creative_id=$cid[0],subject_id=$sid[0],from_id=$fid[0],lastUpdated=curdate() where usa_id=$usa_id";
$rows = $dbhu->do($sql);
if ($dbhu->err() != 0)
{
	$errmsg = $dbhu->errstr();
	util::logerror("Inserting Unique Schedule Advertiser record $sql: $errmsg");
	exit(0);
}
$sql="delete from UniqueAdvertiserCreative where usa_id=$usa_id";
$rows=$dbhu->do($sql);
$sql="delete from UniqueAdvertiserSubject where usa_id=$usa_id";
$rows=$dbhu->do($sql);
$sql="delete from UniqueAdvertiserFrom where usa_id=$usa_id";
$rows=$dbhu->do($sql);
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
