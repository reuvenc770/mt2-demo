#!/usr/bin/perl
#===============================================================================
# Purpose: To remove expired creatives from the rotation 
# Name   : expired_creatives.pl
#
#--Change Control---------------------------------------------------------------
# 02/03/06  Jim Sobeck  Creation
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
my $cname;
my $aname;
my $mflag;

$pmesg="";
srand();
my $rid=rand();
my $cstatus;
my $flag;
my $mailopen=0;

#------ connect to the util database ------------------
my $dbhq;
my $dbhu;
($dbhq,$dbhu)=$util->get_dbh();
$sql="select creative_id,creative.advertiser_id,mediactivate_flag,creative_name,advertiser_name from creative,advertiser_info where creative.inactive_date is not null and creative.inactive_date != '0000-00-00' and creative.inactive_date <= curdate() and creative.advertiser_id=advertiser_info.advertiser_id and creative.status='A'";
my $sthq = $dbhq->prepare($sql);
$sthq->execute();
while (($cid,$puserid,$mflag,$cname,$aname) = $sthq->fetchrow_array())
{
	&delete_creative($cid,$mflag);
	print "Advertiser $puserid - Creative $cid - $pmesg\n";
	if (!$mailopen)
	{
		$mailopen=1;
    	open (MAIL2,"| /usr/sbin/sendmail -t");
        my $from_addr = "Creatives Set Inactive <info\@zetainteractive.com>";
        print MAIL2 "From: $from_addr\n";
        print MAIL2 "To: IndiaSetupTeam\@zetainteractive.com,dpezas\@zetainteractive.com,jtom\@zetainteractive.com\n";
        print MAIL2 "Subject: Creatives set Inactive\n";
        my $date_str = $util->date(6,6);
        print MAIL2 "Date: $date_str\n";
        print MAIL2 "X-Priority: 1\n";
        print MAIL2 "X-MSMail-Priority: High\n";
	}
    print MAIL2 "$puserid, $aname - $cname\n";
}
$sthq->finish();
if ($mailopen)
{
	close MAIL;
}
exit(0);

sub delete_creative
{
	my ($cid,$mflag) = @_;
}
	my $rows;
	my $i;
	# add user to database

	$sql = "update creative set status='I' where creative_id=$cid";
	$sth = $dbhu->do($sql);
	if ($dbhu->err() != 0)
	{
	    $pmesg = "Error - Deleting creative record: $sql - $errmsg";
	}
	else
	{
	    $pmesg = "Successful Delete of Creative Info!" ;
	}

	if ($mflag eq "Y")
	{
    	open (MAIL,"| /usr/sbin/sendmail -t");
        my $from_addr = "Creative Deleted <info\@zetainteractive.com>";
        print MAIL "From: $from_addr\n";
        print MAIL "To: alerts\@zetainteractive.com\n";
        print MAIL "Subject: $aname - $cname has been deleted\n";
        my $date_str = $util->date(6,6);
        print MAIL "Date: $date_str\n";
        print MAIL "X-Priority: 1\n";
        print MAIL "X-MSMail-Priority: High\n";
        print MAIL "$aname - $cname has been deleted\n";
        close MAIL;
	}
	$pmode = "U" ;

	$i=1;
	while ($i <=15)
	{
		$sql = "update advertiser_setup set creative${i}_id=0 where creative${i}_id=$cid and advertiser_id=$puserid";
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
	$sql="select class_id from email_class order by class_id";
	$sth9 = $dbhq->prepare($sql);
	$sth9->execute();
	while (($class_id) = $sth9->fetchrow_array())
	{
		$sql = "select creative1_id,creative2_id,creative3_id,creative4_id,creative5_id,creative6_id,creative7_id,creative8_id,creative9_id,creative10_id,creative11_id,creative12_id,creative13_id,creative14_id,creative15_id from advertiser_setup where advertiser_id=$puserid and class_id=$class_id";
		$sth = $dbhq->prepare($sql);
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
			$sth = $dbhu->do($sql);
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
