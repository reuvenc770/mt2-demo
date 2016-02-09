#!/usr/bin/perl
#===============================================================================
# Purpose: Update creative info - (eg table 'creative' data).
# Name   : delete_creative.cgi 
#
#--Change Control---------------------------------------------------------------
# 01/12/05  Jim Sobeck  Creation
# 01/05/06	Jim Sobeck	Added logic for rotation by ISP
#===============================================================================

# include Perl Modules
use strict;
use CGI;
use util;

# get some objects to use later
my $util = util->new;
my $query = CGI->new;
my ($sth, $sql, $dbh, $errmsg ) ;
my ($pmesg, $old_email_addr) ;
my $images = $util->get_images_url;
my $creative_name ;
my $original_flag ;
my $trigger_flag ;
my $approved_flag ;
my $creative_date;
my $inactive_date ;
my $unsub_image ;
my $default_subject ;
my $default_from ;
my $image_directory ;
my $thumbnail ;
my $html_code ;
my $puserid;
my $pmode;
my $cid;

$pmesg="";
srand();
my $rid=rand();
my $cstatus;

#----------- check for login ------------------
my $user_id = util::check_security();
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}


    #---------------------------------------------------
    # Get the information about the user from the form
    #---------------------------------------------------
    $cid = $query->param('cid');
    $puserid = $query->param('aid');

#------ connect to the util database ------------------
$util->db_connect();
$dbh = 0;
while (!$dbh)
{
$dbh = $util->get_dbh;
}
$dbh->{mysql_auto_reconnect}=1;
&delete_creative();
# go to next screen

$util->clean_up();
print "Cache-Control: no-cache\n";
print "Pragma: no-cache\n";
print "Expires: 0\n";
print "Location: /cgi-bin/advertiser_disp2.cgi?pmode=$pmode&puserid=$puserid&pmesg=$pmesg&rid=$rid\n\n";
exit(0);

sub delete_creative
{
	my $rows;
	my $i;
	# add user to database

	$sql = "update creative set status='D' where creative_id=$cid";
	$sth = $dbh->do($sql);
	if ($dbh->err() != 0)
	{
	    $pmesg = "Error - Deleting creative record: $sql - $errmsg";
	}
	else
	{
	    $pmesg = "Successful Delete of Creative Info!" ;
	}

	$pmode = "U" ;

	$i=1;
	while ($i <=15)
	{
		$sql = "update advertiser_setup set creative${i}_id=0 where creative${i}_id=$cid and advertiser_id=$puserid";
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
	$sql="select class_id from email_class order by class_id";
	$sth9 = $dbh->prepare($sql);
	$sth9->execute();
	while (($class_id) = $sth9->fetchrow_array())
	{
		$sql = "select creative1_id,creative2_id,creative3_id,creative4_id,creative5_id,creative6_id,creative7_id,creative8_id,creative9_id,creative10_id,creative11_id,creative12_id,creative13_id,creative14_id,creative15_id from advertiser_setup where advertiser_id=$puserid and class_id=$class_id";
		$sth = $dbh->prepare($sql);
		$sth->execute();
		(@cids) = $sth->fetchrow_array();
		$sth->finish();
		$i=0;
		my $j=1;
		while ($j <=15)
		{
			$tcids[$j] = 0;
			$j++;
		}
		$j=1;
		while ($i < 15)
		{
			if ($cids[$i] != 0)
			{
				$tcids[$j] = $cids[$i];
				$j++;
			}
			$i++;
		}
		$i=1;
		while ($i <=15)
		{
			$sql = "update advertiser_setup set creative${i}_id=$tcids[$i] where advertiser_id=$puserid and class_id=$class_id";
			$sth = $dbh->do($sql);
			$i++;
		}
		if ($tcids[1] == 0)
		{
			$pmesg="No creatives setup for advertiser rotation now!";
		}
	}
	$sth9->finish();
}
# end sub - delete_creative
