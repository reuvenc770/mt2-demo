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
my $i;
my $dbh;
my $sid;
my $errmsg;
my $images = $util->get_images_url;
my $cfrom;
my @from_array;
my $pmesg;

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
# Remove old from information
#
my $aid = $query->param('aid');
my $sid = $query->param('sid');
#
# Delete record from advertiser_from
#
$sql = "delete from advertiser_from where from_id=$sid and advertiser_id=$aid";
$sth = $dbh->do($sql);
if ($dbh->err() != 0)
{
	my $errmsg = $dbh->errstr();
    $pmesg = "Error - Deleting advertiser_from record: $sql - $errmsg";
}
else
{
        $pmesg = "Successful Delete of From Info!" ;
}
	$i=1;
	while ($i <=10)
	{
		$sql = "update advertiser_setup set from${i}=0 where from${i}=$sid and advertiser_id=$aid";
		$sth = $dbh->do($sql);
		$i++;
	}
	#
	# Move up creative stuff
	#
	my @cids;
	my @tcids;
	my $class_id;
	my $sth9;
	$sql = "select class_id from email_class order by class_id";
	$sth9 = $dbh->prepare($sql);
	$sth9->execute();
	while (($class_id) = $sth9->fetchrow_array())
	{
		$sql = "select from1,from2,from3,from4,from5,from6,from7,from8,from9,from10 from advertiser_setup where advertiser_id=$aid and class_id=$class_id";
		$sth = $dbh->prepare($sql);
		$sth->execute();
		(@cids) = $sth->fetchrow_array();
		$sth->finish();
		$i=0;
		my $j=1;
		while ($j <=10)
		{
			$tcids[$j] = 0;
			$j++;
		}
		$j=1;
		while ($i < 10)
		{
			if ($cids[$i] != 0)
			{
				$tcids[$j] = $cids[$i];
				$j++;
			}
			$i++;
		}
		$i=1;
		while ($i <=10)
		{
			$sql = "update advertiser_setup set from${i}=$tcids[$i] where advertiser_id=$aid and class_id=$class_id";
			$sth = $dbh->do($sql);
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
