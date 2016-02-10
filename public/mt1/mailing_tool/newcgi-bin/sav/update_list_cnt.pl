#!/usr/bin/perl

# *****************************************************************************************
# update_list_cnt.pl 
#
# This program updates the member count for active lists 
#
# History
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use pms;

# get some objects to use later
my $EDEALSDIRECT_LIST = 48;
my $IMEDIA_LIST1 = 52;
my $IMEDIA_LIST2 = 53;
my $pms = pms->new;
my $errmsg;
my $sth;
my $sth2;
my $sql;
my $dbh;
my $list_id;
my $status;
my $schedule_date;
my $light_table_bg = $pms->get_light_table_bg;
my $images = $pms->get_images_url;
my $list_members = 1;
my $counter;

# connect to the pms database

$pms->db_connect();
$dbh = $pms->get_dbh;

$sql = "select list_id from list where status='A'"; 
$sth = $dbh->prepare($sql);
$sth->execute();
while (($list_id) = $sth->fetchrow_array())
{
	if ($list_id == $IMEDIA_LIST1)
	{
		$sql = "select count(*) from imedia_member where list_id = $list_id and status = 'A'";
	}
	elsif ($list_id == $IMEDIA_LIST2)
	{
		$sql = "select count(*) from imedia_member where list_id = $list_id and status = 'A'";
	}
	elsif ($list_id == $EDEALSDIRECT_LIST)
	{
		$sql = "select count(*) from edealsdirect_member where list_id = $list_id and status = 'A'";
	}
	else
	{
#		$sql = "select count(*) from list_member where list_id = $list_id and status = 'A'";
		$sql = "select SQL_BUFFER_RESULT count(*) from member_list where list_id = $list_id and status = 'A'";
	}
	$sth2 = $dbh->prepare($sql);
	$sth2->execute();
	($counter) = $sth2->fetchrow_array();
	$sth2->finish();

    $sql = "update list set member_cnt=$counter where list_id=$list_id";
    my $rows = $dbh->do($sql);
    if ($dbh->err() != 0)
    {
    	$errmsg = $dbh->errstr();
        print "Error updating list : $sql : $errmsg";
	}
	print "updating list $list_id count to $counter\n";
}
$sth->finish();

$pms->clean_up();
exit(0);
