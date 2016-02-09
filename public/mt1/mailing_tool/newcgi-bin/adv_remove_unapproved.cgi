#!/usr/bin/perl

# *****************************************************************************************
# adv_remove_unapproved.cgi
#
# this page removes any unapproved creatives, subjects, or froms 
#
# History
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

my ($dbhq,$dbhu)=$util->get_dbh();

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
#
# Delete record from advertiser_subject
#
$sql = "update advertiser_subject set status='D' where advertiser_id=$aid and approved_flag='N' and internal_approved_flag='N'";
$sth = $dbhu->do($sql);
if ($dbhu->err() != 0)
{
	my $errmsg = $dbhu->errstr();
    $pmesg = "Error - Deleting advertiser subject record: $sql - $errmsg";
}
$sql="select subject_id from advertiser_subject where advertiser_id=$aid and status='D'";
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($sid)=$sth->fetchrow_array())
{
	$sql="delete from UniqueAdvertiserSubject where subject_id=$sid";
	my $rows= $dbhu->do($sql);

	$sql="select uc.unq_id from unique_campaign uc, UniqueSubject where uc.unq_id=UniqueSubject.unq_id and UniqueSubject.subject_id=$sid and uc.send_date >= curdate() and uc.status in ('START','PRE-PULLING')";
	my $sth1 = $dbhu->prepare($sql);
	$sth1->execute();
	my $uid;
	while (($uid)=$sth1->fetchrow_array())
	{
		$sql="delete from UniqueSubject where unq_id=$uid and subject_id=$sid";
		my $rows= $dbhu->do($sql);
	}
	$sth1->finish();
}
$sth->finish();
$sql = "update advertiser_from set status='D' where advertiser_id=$aid and approved_flag='N' and internal_approved_flag='N'";
$sth = $dbhu->do($sql);
if ($dbhu->err() != 0)
{
	my $errmsg = $dbhu->errstr();
    $pmesg = "Error - Deleting advertiser from record: $sql - $errmsg";
}
$sql="select from_id from advertiser_from where advertiser_id=$aid and status='D'";
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($sid)=$sth->fetchrow_array())
{
	$sql="delete from UniqueAdvertiserFrom where from_id=$sid";
	my $rows= $dbhu->do($sql);

	$sql="select uc.unq_id from unique_campaign uc, UniqueFrom where uc.unq_id=UniqueFrom.unq_id and UniqueFrom.from_id=$sid and uc.send_date >= curdate() and uc.status in ('START','PRE-PULLING')";
	my $sth1 = $dbhu->prepare($sql);
	$sth1->execute();
	my $uid;
	while (($uid)=$sth1->fetchrow_array())
	{
		$sql="delete from UniqueFrom where unq_id=$uid and from_id=$sid";
		my $rows= $dbhu->do($sql);
	}
	$sth1->finish();
}
$sth->finish();
$sql = "update creative set status='D',deleted_by=$user_id where advertiser_id=$aid and approved_flag='N' and internal_approved_flag='N'";
$sth = $dbhu->do($sql);
if ($dbhu->err() != 0)
{
	my $errmsg = $dbhu->errstr();
    $pmesg = "Error - Deleting creative record: $sql - $errmsg";
}
if ($pmesg eq "")
{
	$pmesg = "Successful Removal of all unapproved creatives, subjects, and froms" ;
}
#
# Display the confirmation page
#
print "Location: /cgi-bin/advertiser_disp2.cgi?puserid=$aid&pmesg=$pmesg\n\n";
$util->clean_up();
exit(0);
