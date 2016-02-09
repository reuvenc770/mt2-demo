#!/usr/bin/perl
#===============================================================================
# Purpose: To remove expired subjects and froms from the rotation 
# Name   : expired_sub_from.pl
#
#--Change Control---------------------------------------------------------------
# 01/22/08  Jim Sobeck  Creation
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
my $sid;
my $sname;
my $aname;

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
$sql="select subject_id,advertiser_subject.advertiser_id,advertiser_subject,advertiser_name from advertiser_subject,advertiser_info where advertiser_subject.inactive_date is not null and advertiser_subject.inactive_date != '0000-00-00' and advertiser_subject.advertiser_id=advertiser_info.advertiser_id and advertiser_subject.inactive_date <= curdate() and advertiser_subject.status='A'";
my $sthq = $dbhq->prepare($sql);
$sthq->execute();
while (($sid,$puserid,$sname,$aname) = $sthq->fetchrow_array())
{
	&delete_subject($sid);
	print "Advertiser $puserid - Subject $sid - $pmesg\n";
    if (!$mailopen)
    {
        $mailopen=1;
        open (MAIL2,"| /usr/sbin/sendmail -t");
        my $from_addr = "Subject/From Set Inactive <info\@zetainteractive.com>";
        print MAIL2 "From: $from_addr\n";
        print MAIL2 "To: IndiaSetupTeam\@zetainteractive.com,dpezas\@zetainteractive.com,jtom\@zetainteractive.com\n";
        print MAIL2 "Subject: Subject/From set Inactive\n";
        my $date_str = $util->date(6,6);
        print MAIL2 "Date: $date_str\n";
        print MAIL2 "X-Priority: 1\n";
        print MAIL2 "X-MSMail-Priority: High\n";
    }
    print MAIL2 "$puserid, $aname - Subject: $sname\n";
}
$sthq->finish();
$sql="select from_id,advertiser_from.advertiser_id,advertiser_from,advertiser_name from advertiser_from,advertiser_info where advertiser_from.inactive_date is not null and advertiser_from.inactive_date != '0000-00-00' and advertiser_from.advertiser_id=advertiser_info.advertiser_id and advertiser_from.inactive_date <= curdate() and advertiser_from.status='A'";
my $sthq = $dbhq->prepare($sql);
$sthq->execute();
while (($sid,$puserid,$sname,$aname) = $sthq->fetchrow_array())
{
	&delete_from($sid);
	print "Advertiser $puserid - From $sid - $pmesg\n";
    if (!$mailopen)
    {
        $mailopen=1;
        open (MAIL2,"| /usr/sbin/sendmail -t");
        my $from_addr = "Subject/From Set Inactive <info\@zetainteractive.com>";
        print MAIL2 "From: $from_addr\n";
        print MAIL2 "To: IndiaSetupTeam\@zetainteractive.com,dpezas\@zetainteractive.com,jtom\@zetainteractive.com\n";
        print MAIL2 "Subject: Subject/From set Inactive\n";
        my $date_str = $util->date(6,6);
        print MAIL2 "Date: $date_str\n";
        print MAIL2 "X-Priority: 1\n";
        print MAIL2 "X-MSMail-Priority: High\n";
    }
    print MAIL2 "$puserid, $aname - From: $sname\n";
}
$sthq->finish();
if ($mailopen)
{
    close MAIL;
}
exit(0);

sub delete_subject
{
	my ($sid) = @_;
	my $rows;
	my $i;
	# add user to database

	$sql = "update advertiser_subject set status='I' where subject_id=$sid";
	$sth = $dbhu->do($sql);
	if ($dbhu->err() != 0)
	{
	    $pmesg = "Error - Deleting subject record: $sql - $errmsg";
	}
	else
	{
	    $pmesg = "Successful Delete of Subject Info!" ;
	}
	$sql="delete from UniqueAdvertiserSubject where subject_id=$sid";
	$sth = $dbhu->do($sql);


	$sql="select uc.unq_id from unique_campaign uc, UniqueSubject where uc.unq_id=UniqueSubject.unq_id and UniqueSubject.subject_id=$sid and uc.send_date >= curdate() and uc.status in ('START','PRE-PULLING')";
	$sth = $dbhq->prepare($sql);
	$sth->execute();
	my $uid;
	while (($uid)=$sth->fetchrow_array())
	{
		$sql="delete from UniqueSubject where unq_id=$uid and subject_id=$sid";
		my $rows= $dbhu->do($sql);
	}
	$sth->finish();

	$i=1;
	while ($i <=30)
	{
		$sql = "update advertiser_setup set subject${i}=0 where subject${i}=$sid and advertiser_id=$puserid";
		$sth = $dbhu->do($sql);
		print "<$sql>\n";		
		$i++;
	}
	#
	# Move up subject stuff
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
		$sql = "select subject1,subject2,subject3,subject4,subject5,subject6,subject7,subject8,subject9,subject10,subject11,subject12,subject13,subject14,subject15,subject16,subject17,subject18,subject19,subject20,subject21,subject22,subject23,subject24,subject25,subject26,subject27,subject28,subject29,subject30 from advertiser_setup where advertiser_id=$puserid and class_id=$class_id";
		$sth = $dbhq->prepare($sql);
		$sth->execute();
		(@cids) = $sth->fetchrow_array();
		$sth->finish();
		$i=0;
		my $j=1;
		while ($j <=30)
		{
			$tcids[$j] = 0;
			$j++;
		}
		$j=1;
		while ($i < 30)
		{
			print "<$cids[$i] $i>\n";		
			if ($cids[$i] != 0)
			{
				$tcids[$j] = $cids[$i];
				print "<Setting tcids $tcids[$j] $j>\n";		
				$j++;
			}
			$i++;
		}
		$i=1;
		while ($i <=30)
		{
			$sql = "update advertiser_setup set subject${i}=$tcids[$i] where advertiser_id=$puserid and class_id=$class_id";
			$sth = $dbhu->do($sql);
			$i++;
		}
		if ($tcids[1] == 0)
		{
			$pmesg="No subjects setup for advertiser rotation now!";
		}
	}
	$sth9->finish();
}
# end sub - delete_subject

sub delete_from
{
	my ($sid) = @_;
	my $rows;
	my $i;
	# add user to database

	$sql = "update advertiser_from set status='I' where from_id=$sid";
	$sth = $dbhu->do($sql);
	if ($dbhu->err() != 0)
	{
	    $pmesg = "Error - Deleting from record: $sql - $errmsg";
	}
	else
	{
	    $pmesg = "Successful Delete of Subject Info!" ;
	}
	$sql="delete from UniqueAdvertiserFrom where from_id=$sid";
	$sth = $dbhu->do($sql);


	$sql="select uc.unq_id from unique_campaign uc, UniqueFrom where uc.unq_id=UniqueFrom.unq_id and UniqueFrom.from_id=$sid and uc.send_date >= curdate() and uc.status in ('START','PRE-PULLING')";
	$sth = $dbhq->prepare($sql);
	$sth->execute();
	my $uid;
	while (($uid)=$sth->fetchrow_array())
	{
		$sql="delete from UniqueFrom where unq_id=$uid and from_id=$sid";
		my $rows= $dbhu->do($sql);
	}
	$sth->finish();

	$i=1;
	while ($i <=20)
	{
		$sql = "update advertiser_setup set from${i}=0 where from${i}=$sid and advertiser_id=$puserid";
		$sth = $dbhu->do($sql);
		$i++;
	}
	#
	# Move up from stuff
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
		$sql = "select from1,from2,from3,from4,from5,from6,from7,from8,from9,from10,from11,from12,from13,from14,from15,from16,from17,from18,from19,from20 from advertiser_setup where advertiser_id=$puserid and class_id=$class_id";
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
			$sql = "update advertiser_setup set from${i}=$tcids[$i] where advertiser_id=$puserid and class_id=$class_id";
			$sth = $dbhu->do($sql);
			$i++;
		}
		if ($tcids[1] == 0)
		{
			$pmesg="No froms setup for advertiser rotation now!";
		}
	}
	$sth9->finish();
}
# end sub - delete_from
