#!/usr/bin/perl

# *****************************************************************************************
# del_subject.cgi
#
# this page updates information in the advertiser_subject table
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
my $dbh;
my $pmesg;
my $sid;
my $errmsg;
my $images = $util->get_images_url;
my $csubject;
my @subject_array;

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
# Remove old subject information
#
my $aid = $query->param('aid');
my $sid = $query->param('sid');
#
# Delete record from advertiser_subject
#
$sql = "update advertiser_subject set status='D' where subject_id=$sid and advertiser_id=$aid";
$sth = $dbhu->do($sql);
if ($dbhu->err() != 0)
{
	my $errmsg = $dbhu->errstr();
    $pmesg = "Error - Deleting advertiser subject record: $sql - $errmsg";
}
else
{
	$pmesg = "Successful Delete of Advertiser Subject" ;
}
$sql = "delete from UniqueAdvertiserSubject where subject_id=$sid";
$sth = $dbhu->do($sql);

my $usaid;
$sql="select usa_id from UniqueScheduleAdvertiser where subject_id=$sid";
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($usaid) = $sth->fetchrow_array())
{
	my $tcid;
	$sql="select subject_id from UniqueAdvertiserSubject where usa_id=? limit 1";
	my $sthu=$dbhu->prepare($sql);
	$sthu->execute($usaid);
	($tcid)=$sthu->fetchrow_array();
	$sthu->finish();
	if ($tcid eq "")
	{
		$tcid=0;
	}
	$sql="update UniqueScheduleAdvertiser set subject_id=$tcid,lastUpdated=curdate() where usa_id=$usaid";
	my $rows= $dbhu->do($sql);
}
$sth->finish();
my $i=1;
	while ($i <=30)
	{
		$sql = "update advertiser_setup set subject${i}=0 where subject${i}=$sid and advertiser_id=$aid";
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
	$sql="select class_id from email_class where status='Active' order by class_id"; 
	$sth9 = $dbhq->prepare($sql);
	$sth9->execute();
	while (($class_id) = $sth9->fetchrow_array())
	{
		$sql = "select subject1,subject2,subject3,subject4,subject5,subject6,subject7,subject8,subject9,subject10,subject11,subject12,subject13,subject14,subject15,subject16,subject17,subject18,subject19,subject20,subject21,subject22,subject23,subject24,subject25,subject26,subject27,subject28,subject29,subject30 from advertiser_setup where advertiser_id=$aid and class_id=$class_id";
		$sth = $dbhq->prepare($sql);
		$sth->execute();
		(@cids) = $sth->fetchrow_array();
		$sth->finish();
		$i=0;
		my $j=1;
		while ($j <= $#cids)
		{
			$tcids[$j] = 0;
			$j++;
		}
		$j=1;
		while ($i < $#cids)
		{
			if ($cids[$i] != 0)
			{
				$tcids[$j] = $cids[$i];
				$j++;
			}
			$i++;
		}
		$i=1;
		while ($i <= $#cids)
		{
			$sql = "update advertiser_setup set subject${i}=$tcids[$i] where advertiser_id=$aid and class_id=$class_id";
			$sth = $dbhu->do($sql);
			$i++;
		}
		if ($tcids[1] == 0)
		{
			$pmesg="No subjects setup for advertiser rotation now!";
		}
	}
	$sth9->finish();
#
# Display the confirmation page
#
print "Location: /cgi-bin/advertiser_disp2.cgi?puserid=$aid&pmesg=$pmesg\n\n";
$util->clean_up();
exit(0);
