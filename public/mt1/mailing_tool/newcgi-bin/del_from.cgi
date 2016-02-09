#!/usr/bin/perl

# *****************************************************************************************
# del_from.cgi
#
# this page updates information in the advertiser_from table
#
# History
# Jim Sobeck, 12/16/04, Creation
# Jim Sobeck, 02/02/05, Modifed to handle unique id
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $sql;
my $reccnt;
my $i;
my $dbh;
my $sid;
my $errmsg;
my $images = $util->get_images_url;
my $cfrom;
my @from_array;
my $pmesg;

# connect to the util database
###$util->db_connect();

my ($dbhq,$dbhu)=$util->get_dbh();
###$dbh = $util->get_dbh;

# check for login
my $user_id = util::check_security();
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
	$util->clean_up();
    exit(0);
}
#
# Remove old from information
#
my $aid = $query->param('aid');
my $sid = $query->param('sid');
#
# Check to see if default from for trigger creative
#
$sql="select count(*) from creative where default_from=$sid and advertiser_id=$aid and trigger_flag='Y'";
$sth=$dbhq->prepare($sql);
$sth->execute();
($reccnt)=$sth->fetchrow_array();
$sth->finish();
if ($reccnt > 0)
{
	$pmesg="Cannot delete from - it is the default from for a Trigger Creative";
	print "Location: /cgi-bin/advertiser_disp2.cgi?puserid=$aid&pmesg=$pmesg\n\n";
	exit(0);
}
#
# Delete record from advertiser_from
#
$sql = "delete from advertiser_from where from_id=$sid and advertiser_id=$aid";
$sth = $dbhu->do($sql);
if ($dbhu->err() != 0)
{
	my $errmsg = $dbhu->errstr();
    $pmesg = "Error - Deleting advertiser_from record: $sql - $errmsg";
}
else
{
        $pmesg = "Successful Delete of From Info!" ;
}
$sql = "delete from UniqueAdvertiserFrom where from_id=$sid";
$sth = $dbhu->do($sql);

my $usaid;
$sql="select usa_id from UniqueScheduleAdvertiser where from_id=$sid";
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($usaid) = $sth->fetchrow_array())
{
	my $tcid;
	$sql="select from_id from UniqueAdvertiserFrom where usa_id=? limit 1";
	my $sthu=$dbhu->prepare($sql);
	$sthu->execute($usaid);
	($tcid)=$sthu->fetchrow_array();
	$sthu->finish();
	if ($tcid eq "")
	{
		$tcid=0;
	}
	$sql="update UniqueScheduleAdvertiser set from_id=$tcid,lastUpdated=curdate() where usa_id=$usaid";
	my $rows= $dbhu->do($sql);
}
$sth->finish();
	$i=1;
	while ($i <=20)
	{
		$sql = "update advertiser_setup set from${i}=0 where from${i}=$sid and advertiser_id=$aid";
		$sth = $dbhu->do($sql);
		$i++;
	}
	#
	# Move up creative stuff
	#
	my @cids;
	my @tcids;
	my $class_id;
	my $sth9;
	$sql = "select class_id from email_class where status='Active' order by class_id";
	$sth9 = $dbhq->prepare($sql);
	$sth9->execute();
	while (($class_id) = $sth9->fetchrow_array())
	{
		$sql = "select from1,from2,from3,from4,from5,from6,from7,from8,from9,from10,from11,from12,from13,from14,from15,from16,from17,from18,from19,from20 from advertiser_setup where advertiser_id=$aid and class_id=$class_id";
		$sth = $dbhq->prepare($sql);
		$sth->execute();
		(@cids) = $sth->fetchrow_array();
		$sth->finish();
		$i=0;
		my $j=1;
		while ($j <=20)
		{
			$tcids[$j] = 0;
			$j++;
		}
		$j=1;
		while ($i < 20)
		{
			if ($cids[$i] != 0)
			{
				$tcids[$j] = $cids[$i];
				$j++;
			}
			$i++;
		}
		$i=1;
		while ($i <=20)
		{
			$sql = "update advertiser_setup set from${i}=$tcids[$i] where advertiser_id=$aid and class_id=$class_id";
			$sth = $dbhu->do($sql);
			$i++;
		}
		if ($tcids[1] == 0)
		{
			$pmesg="No froms setup for advertiser rotation now!";
		}
	}
	$sth9->finish();
#
# Display the confirmation page
#
print "Location: /cgi-bin/advertiser_disp2.cgi?puserid=$aid&pmesg=$pmesg\n\n";
$util->clean_up();
exit(0);
