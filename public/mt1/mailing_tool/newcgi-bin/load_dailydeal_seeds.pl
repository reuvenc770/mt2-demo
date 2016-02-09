#!/usr/bin/perl
use strict;
use Sys::Hostname;
use lib "/var/www/html/newcgi-bin";
use util;

my $util = util->new;
my $sth;
my $sth1;
my $dbh;
my $dbh2;
my $sql;
my $rows;
my $client_id;
my $domain_id;
my $dd_id;
my $class_id;
my $seedlist;
my $old_dd;
my $camp_id;
my $CIDS;
my $LIST;
my $EIDS;
my $EID;
my $eid;

$| = 1;

$util->db_connect();
$dbh = $util->get_dbh;
my $dbh2 = DBI->connect("DBI:mysql:new_mail:update2.routename.com","db_user","sp1r3V");
$dd_id=$ARGV[0];
$seedlist=$ARGV[1];
if ($dd_id eq "")
{
	$sql="select dd_id,class_id,seedlist from DailyDealSettingDetail where seedlist != '' order by dd_id"; 
}
else
{
	$sql="select dd_id,class_id,$seedlist from DailyDealSettingDetail where dd_id=$dd_id"; 
}
$sth=$dbh->prepare($sql);
$sth->execute();
$old_dd=0;
while (($dd_id,$class_id,$seedlist)=$sth->fetchrow_array())
{
	if ($old_dd != $dd_id)
	{
		$CIDS={};
#		$sql="select si.client_id,csi.campaign_id from schedule_info si, camp_schedule_info csi where mta_id=? and status='A' and si.slot_type='D' and si.slot_id=csi.slot_id and si.slot_type=csi.slot_type and si.client_id=csi.client_id and csi.nl_id=1";
		$sql="select si.client_id from schedule_info si, camp_schedule_info csi where mta_id=? and si.status='A' and csi.status='A' and si.slot_type='D' and si.slot_id=csi.slot_id and si.slot_type=csi.slot_type and si.client_id=csi.client_id and csi.nl_id=1 order by rand() limit 1";
		$sth1=$dbh->prepare($sql);
		$sth1->execute($dd_id);
		while (($client_id)=$sth1->fetchrow_array())
		{
			$CIDS->{$client_id}=1;
		}
		$sth1->finish();
		$old_dd=$dd_id;
	}
	foreach (keys %{$CIDS})
	{
		$client_id=$_;
		$camp_id=$CIDS->{$client_id};
		my $list_id=get_list($client_id);
		$eid=get_email_user_id($seedlist,$list_id);
		print "<$client_id> <$camp_id> <$list_id> <$class_id> <$eid> <$seedlist>\n";
		$EID->{$eid}{$client_id}=$class_id;
	}
}
$sth->finish();
foreach (keys %{$EID})
{
	$eid=$_;
	foreach (keys %{$EID->{$eid}})
	{
		$client_id=$_;
		$class_id=$EID->{$eid}{$client_id};
		$sql="delete from realtime_daily where client_id=$client_id and email_user_id=$eid and domain_id=$class_id and cday > 1";
		$rows=$dbh2->do($sql);
		$sql="insert into realtime_daily(client_id,email_user_id,domain_id,cday,send_datetime) values($client_id,$eid,$class_id,1,now())";
		$rows=$dbh2->do($sql);
		print "<$eid> <$client_id> <$EID->{$eid}{$client_id}>\n";
	}
}

sub get_list
{
	my ($client_id)=@_;
	my $sql;
	my $sth;

	if ($LIST->{$client_id})
	{
		return $LIST->{$client_id};
	}
	$sql="select list_id from list where user_id=? and list_name='Newest Records'"; 
	$sth=$dbh->prepare($sql);
	$sth->execute($client_id);
	($LIST->{$client_id})=$sth->fetchrow_array();
	$sth->finish();
	return $LIST->{$client_id};
}

sub get_email_user_id
{
	my ($em,$list_id)=@_;
	my $sql;
	my $sth;
	my $eid;

	if ($EIDS->{$em}{$list_id})
	{
	}
	else
	{
		$eid="";
		while ($eid eq "")
		{
			$sql="select email_user_id from email_list where email_addr=? and list_id=?";
			$sth=$dbh->prepare($sql);
			$sth->execute($em,$list_id);
			if (($eid)=$sth->fetchrow_array())
			{
				$EIDS->{$em}{$list_id}=$eid;
			}
			else
			{
				$sql="insert into email_list(list_id,email_addr,status,subscribe_date,subscribe_time,capture_date,member_source,source_url) values($list_id,'$em','S',curdate(),curtime(),now(),'216.57.42.222','www.spirevision.com')";
				my $rows=$dbh->do($sql);
				print "Added $em to List $list_id\n";
			}
		}
	}
	return $EIDS->{$em}{$list_id};
}
