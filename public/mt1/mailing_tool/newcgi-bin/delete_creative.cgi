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
my ($dbhq,$dbhu)=$util->get_dbh();
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
	my $cname;
	my $aname;
	my $mflag;
	my $trigger_flag;
	my $reccnt;

	# add user to database
	$reccnt=0;
	$sql="select creative_name,advertiser_name,mediactivate_flag,trigger_flag from creative,advertiser_info where creative_id=$cid and creative.advertiser_id=advertiser_info.advertiser_id";
	$sth = $dbhq->prepare($sql);
	$sth->execute();
	($cname,$aname,$mflag,$trigger_flag) = $sth->fetchrow_array();
	$sth->finish();
	if ($trigger_flag eq "Y")
	{
		# Check to see if part of category trigger
		$sql="select count(*) from category_trigger where trigger1=$cid or trigger2=$cid or alt_trigger=$cid";
		$sth = $dbhq->prepare($sql);
		$sth->execute();
		($reccnt) = $sth->fetchrow_array();
		$sth->finish();
	}

	if ($reccnt == 0)
	{
		$sql = "update creative set deleted_by=$user_id,status='D' where creative_id=$cid";
		$sth = $dbhu->do($sql);
		if ($dbhu->err() != 0)
		{
	    	$pmesg = "Error - Deleting creative record: $sql - $errmsg";
		}
		else
		{
	    	$pmesg = "Successful Delete of Creative Info!" ;
		}
		$sql = "delete from UniqueAdvertiserCreative where creative_id=$cid";
		$sth = $dbhu->do($sql);

		my $usaid;
		$sql="select usa_id from UniqueScheduleAdvertiser where creative_id=$cid";
		$sth = $dbhq->prepare($sql);
		$sth->execute();
		while (($usaid) = $sth->fetchrow_array())
		{
			my $tcid;
			$sql="select creative_id from UniqueAdvertiserCreative where usa_id=? limit 1";
			my $sthu=$dbhu->prepare($sql);
			$sthu->execute($usaid);
			($tcid)=$sthu->fetchrow_array();
			$sthu->finish();
			if ($tcid eq "")
			{
				$tcid=0;
			}
			$sql="update UniqueScheduleAdvertiser set creative_id=$tcid,lastUpdated=curdate() where usa_id=$usaid";
			$rows= $dbhu->do($sql);

		}
		$sth->finish();

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
		$sql="select class_id from email_class where status='Active' order by class_id";
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
	else
	{
		$pmesg="Creative not deleted because it is a trigger for one or more categories";
	}
}
# end sub - delete_creative
