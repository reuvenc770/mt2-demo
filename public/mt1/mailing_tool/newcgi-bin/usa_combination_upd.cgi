#!/usr/bin/perl
#-----  include Perl Modules ---------
use strict;
use CGI;
use util;

#------  get some objects to use later ---------
my $util = util->new;
my $query = CGI->new;
my $sth;
my $sql;
my $rowCnt;
my $rows;
my $cid;
my $sid;
my $fid;

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}
my ($dbhq,$dbhu)=$util->get_dbh();
my $usaid=$query->param('usaid');

$sql="select rowCnt from UniqueScheduleAdvertiser where usa_id=?";
$sth=$dbhu->prepare($sql);
$sth->execute($usaid);
($rowCnt)=$sth->fetchrow_array();
$sth->finish();

my $i=1;
while ($i <= $rowCnt)
{
	$sql="delete from UniqueAdvertiserCreative where usa_id=$usaid and rowID=$i";
	$rows=$dbhu->do($sql);
	$sql="delete from UniqueAdvertiserSubject where usa_id=$usaid and rowID=$i";
	$rows=$dbhu->do($sql);
	$sql="delete from UniqueAdvertiserFrom where usa_id=$usaid and rowID=$i";
	$rows=$dbhu->do($sql);
	$cid=$query->param("cid$i");
	$sid=$query->param("sid$i");
	$fid=$query->param("fid$i");
	$sql="insert into UniqueAdvertiserCreative(usa_id,rowID,creative_id) values($usaid,$i,$cid)";
	$rows=$dbhu->do($sql);
	$sql="insert into UniqueAdvertiserSubject(usa_id,rowID,subject_id) values($usaid,$i,$sid)";
	$rows=$dbhu->do($sql);
	$sql="insert into UniqueAdvertiserFrom(usa_id,rowID,from_id) values($usaid,$i,$fid)";
	$rows=$dbhu->do($sql);
	if ($i == 1)
	{
		$sql="update UniqueScheduleAdvertiser set creative_id=$cid,subject_id=$sid,from_id=$fid,lastUpdated=curdate() where usa_id=$usaid";
		$rows=$dbhu->do($sql);
	}
	$i++;
}
print "Location: unique_advertiser_main.cgi\n\n";

