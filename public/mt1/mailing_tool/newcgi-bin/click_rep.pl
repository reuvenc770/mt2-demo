#!/usr/bin/perl
# *****************************************************************************************
# update_list_cnt.pl
#
# History
# Jim Sobeck,   04/09/03,   Created
# *****************************************************************************************

use strict;
use lib "/var/www/html/newcgi-bin";
use util;

my $util = util->new;
my $sth;
my $sth1;
my $sth2;
my $dbh;
my $sql;
my $rows;
my $reccnt;
my $cdate = localtime();
my $program = "update_list_cnt.pl";
my $errmsg;
my $bin_dir_http;
my $cnt;
my $total_cnt;
my $aol_cnt;
my $list_aol_cnt;
my $list_hotmail_cnt;
my $list_msn_cnt;
my $list_cnt;
my $list_yahoo_cnt;
my $list_foreign_cnt;
my $last_email_user_id;
my $max_emails;
my $clast60;
my $aolflag;
my $openflag;
my $first_email_user_id;
my $addrec;
my $begin;
my $end;
my $list_str;
my $bend;

# connect to the util database
my $dbhq;
my $dbhu;
($dbhq,$dbhu)=$util->get_dbh();

$| = 1;

	my $list_id;
	my $list_name;
	my $email_user_id;
	my $cemail;
	my $email_type;
	my $the_email;
	my $filename;
	my $filecnt;
	my $curcnt;
	my $eid;
	my @CATNAME;
	my @CID;
	my $catid;
	my $catname;
	my $cid;
	my $cid_str;
	my $i;

	$i=3;
	while ($i <= 65)
	{
		$CID[$i]="";
		$i++;
	}
	$sql="select category_id,category_name from category_info where category_id != 58 order by category_id";
	$sth = $dbhq->prepare($sql);
	$sth->execute();
	while (($catid,$catname)=$sth->fetchrow_array())
	{
		$sql="select campaign_id from advertiser_info,campaign where category_id=? and advertiser_info.advertiser_id=campaign.advertiser_id and campaign.status in ('C','W','T')";
		$sth1 = $dbhq->prepare($sql);
		$sth1->execute($catid);
		$cid_str="";
		while (($cid) = $sth1->fetchrow_array())
		{
			$cid_str = $cid_str . $cid . ","; 
		}
		$sth1->finish();
		$_=$cid_str;
		chop;
		$cid_str=$_;
		$CID[$catid]=$cid_str;
		$CATNAME[$catid]=$catname;
	}
	$sth->finish();
	
	

		$sql = "select list_id from list where list_name = 'Clickers' and user_id=$ARGV[0]";
		$sth = $dbhq->prepare($sql);
		$sth->execute();
		($list_id) = $sth->fetchrow_array();
		$sth->finish();
#
		$sql="select email_user_id from email_list where list_id=? and status='A'"; 
		$sth = $dbhq->prepare($sql);
		$sth->execute($list_id);
		while (($eid) = $sth->fetchrow_array())
		{
			print "$eid,";
			$i=3;
			while ($i <= $#CID)
			{
				if ($CID[$i] != "")
				{
					$sql="select count(*) from click_history where campaign_id in ($CID[$i]) and email_user_id=?";
					$sth1 = $dbhq->prepare($sql);
					$sth1->execute($eid);
					($reccnt)=$sth1->fetchrow_array();
					$sth1->finish();
					print "$reccnt,";
				}
				else
				{
					print "0,";
				}
				$i++;
			}
			print "\n";
		}
		$sth->finish();
