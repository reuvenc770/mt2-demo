#!/usr/bin/perl

# *****************************************************************************************
# delete_slot.cgi
#
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $rows;
my $sql;
my $dbh;
my $sid;
my $errmsg;
my $images = $util->get_images_url;
my $camp_id;

# connect to the util database
$util->db_connect();
$dbh = $util->get_dbh;

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
my $cid = $query->param('cid');
my $sid= $query->param('sid');
my $flag= $query->param('flag');
my $stype= $query->param('stype');
if ($stype eq "")
{
	$stype="C";
}
my ($dcnt,$ccnt,$acnt,$rcnt,$tcnt);
#
$sql = "update schedule_info set status='$flag' where client_id=$cid and slot_id=$sid and slot_type='$stype'";
$sth = $dbh->do($sql);
if ($flag eq "D")
{
	$sql="select campaign_id from camp_schedule_info where client_id=$cid and slot_id=$sid and schedule_date >= curdate() and slot_type='$stype'";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	while (($camp_id) = $sth->fetchrow_array())
	{
		$sql="update campaign set deleted_date=now() where campaign_id=$camp_id and status='S'";
		$rows = $dbh->do($sql);
		$sql="delete from camp_schedule_info where campaign_id=$camp_id";
		$rows = $dbh->do($sql);
	}
	$sth->finish();
}
$sql = "select campaign_cnt,aol_cnt,daily_cnt,rotating_cnt,3rdparty_cnt from network_schedule where client_id=$cid";
$sth = $dbh->prepare($sql);
$sth->execute();
($ccnt,$acnt,$dcnt,$rcnt,$tcnt) = $sth->fetchrow_array();
$sth->finish();
#
# Display the confirmation page
#
print "Location: /cgi-bin/upd_client_schedule.cgi?client_id=$cid&daily_cnt=$dcnt&camp_cnt=$ccnt&aol_cnt=$acnt&rotate_cnt=$rcnt&third_cnt=$tcnt\n\n";
