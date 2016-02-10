#!/usr/bin/perl
# *****************************************************************************************
# add_chunk_data.pl
#
# History
# Jim Sobeck, 04/07/06, Creation
# Jim Sobeck, 05/31/06, Added logic to use profile_chunk_add 
# Jim Sobeck, 09/11/06, Added logic to add clean AOL data
# *****************************************************************************************

use strict;
use lib "/var/www/html/newcgi-bin";
use util;

my $util = util->new;
my $sth;
my $list_id;
my $sth1;
my $sth1a;
my $sth2;
my $del_cnt;
my ($camp_id,$aol_comp,$bounce_cnt,$fullmbx_cnt,$uns_cnt,$notdelivered_cnt);
my $reccnt;
my $dbh;
my $clean_add_list;
my $from_addr;
my $sql;
my $rows;
my $add_freq;
my $cdate = localtime();
my $amount_to_add;
my $clean_add;
my $record_cnt;
my $max_amount;
my $aolflag;
my $domain_str;
my $did;
my $hotmailflag;
my $otherflag;
my $yahooflag;
my $client_id;
my $profile_id;
my $profile_name;
my $errmsg;

# connect to the util database

$| = 1;

$util->db_connect();
$dbh = $util->get_dbh;
my $dbh2 = DBI->connect("DBI:mysql:new_mail:update2.routename.com","db_user","sp1r3V");
## 08/31 - JES - Changed to remove status='S' so strongmail profiles are added
##
$sql = "select list_profile.profile_id,profile_name,client_id,amount_to_add,list_profile.aol_flag,list_profile.hotmail_flag,list_profile.other_flag,list_profile.yahoo_flag,add_freq,clean_add from list_profile,campaign where profile_type='CHUNK' and list_profile.profile_id=campaign.profile_id and scheduled_datetime >= curdate() and scheduled_datetime < date_add(curdate(),interval 1 day) and deleted_date is null and (date_added < curdate() or date_added is null) and add_freq='DAILY' union
select list_profile.profile_id,profile_name,client_id,amount_to_add,list_profile.aol_flag,list_profile.hotmail_flag,list_profile.other_flag,list_profile.yahoo_flag,add_freq,clean_add from list_profile,campaign where profile_type='CHUNK' and list_profile.profile_id=campaign.profile_id and scheduled_datetime >= curdate() and scheduled_datetime < date_add(curdate(),interval 1 day) and deleted_date is null and  (date_added <= date_sub(curdate(),interval 7 day) or date_added is null) and add_freq='WEEKLY'";
$sth1 = $dbh->prepare($sql);
$sth1->execute();
while (($profile_id,$profile_name,$client_id,$amount_to_add,$aolflag,$hotmailflag,$otherflag,$yahooflag,$add_freq,$clean_add) = $sth1->fetchrow_array())
{
	print "Clean_add for $profile_name is $clean_add\n";
	$amount_to_add=1;
#
#	If aolflag then check complaints
#
	$sql="select count(*) from profile_chunk_domain,email_domains where profile_id=? and profile_chunk_domain.domain_id=email_domains.domain_id and email_domains.domain_class=1";
	$sth=$dbh->prepare($sql);
	$sth->execute($profile_id);
	($reccnt) = $sth->fetchrow_array();
	$sth->finish();

	if ($reccnt > 0)
	{
		$sql = "select campaign_id,aol_complaints,bounce_cnt,fullmbx_cnt,unsubscribe_cnt,notdelivered_cnt from campaign where profile_id=$profile_id and sent_datetime = date_sub(curdate(),interval 1 day) and deleted_date is null";
		$sth = $dbh->prepare($sql);
		$sth->execute();
		while (($camp_id,$aol_comp,$bounce_cnt,$fullmbx_cnt,$uns_cnt,$notdelivered_cnt) = $sth->fetchrow_array())
		{
			if ($aol_comp > 0)
			{
				$sql="select sum(sent_cnt)-$bounce_cnt-$fullmbx_cnt-$notdelivered_cnt from campaign_log where campaign_id=$camp_id";
				$sth1a = $dbh->prepare($sql);
				$sth1a->execute();
				($del_cnt) = $sth1a->fetchrow_array();
				$sth1a->finish();
				my $del_percent;
				if ($del_cnt > 0)
				{
					$del_percent = ($aol_comp / $del_cnt)*100;
				}
				else
				{
					$del_percent = 0; 
				}
#			if (($del_percent > 1) && ($client_id != 6))
#			{
#				print "$del_cnt - $aol_comp - $profile_name\n";
#				$amount_to_add=0;
#			}
			}
			else
			{
				$sql="select sum(sent_cnt) from campaign_log where campaign_id=$camp_id";
				$sth1a = $dbh->prepare($sql);
				$sth1a->execute();
				($del_cnt) = $sth1a->fetchrow_array();
				$sth1a->finish();
				if ($del_cnt > 1000)
				{
					print "No AOL Complaints for $profile_id - $profile_name\n";
					$amount_to_add=0;
				}
			}
		}
		$sth->finish();
	}
	if ($amount_to_add > 0)
	{
#
		$clean_add_list=0;
		if ($clean_add > 0)
		{
			$sql="select floor(?/count(*)) from list_profile_list where profile_id=$profile_id";
			$sth = $dbh->prepare($sql);
			$sth->execute($clean_add);
			($clean_add_list) = $sth->fetchrow_array();
			$sth->finish();
		}
		$sql="select list_id from list_profile_list where profile_id=$profile_id";
		print "Sql <$sql>\n";
		$sth = $dbh->prepare($sql);
		$sth->execute();
		while (($list_id) = $sth->fetchrow_array())
		{
			$sql="select profile_chunk_domain.domain_id from profile_chunk_domain where profile_id=?";
			$sth1a=$dbh->prepare($sql);
			$sth1a->execute($profile_id);
			while (($did) = $sth1a->fetchrow_array())
			{
				$sql="select add_amount,max_amount from profile_chunk_add where domain_id=? and (profile_id=? or profile_id=0) order by profile_id desc";
				$sth2=$dbh->prepare($sql);
				$sth2->execute($did,$profile_id);
				($amount_to_add,$max_amount)=$sth2->fetchrow_array();
				$sth2->finish();
				if ($amount_to_add eq "")
				{
					$amount_to_add=0;
				}
				print "Adding $amount_to_add records to client $client_id for profile $profile_id - $list_id - $did\n";
				if ($amount_to_add > 0)
				{
					if ($max_amount == -1)
					{
						$sql="update email_chunk_list_${client_id} set list_id=$list_id,subscribe_date=curdate() where list_id=0 and status='A' and domain_id=$did order by subscribe_date limit $amount_to_add";
						print "Sql = <$sql>\n";
	unless ($dbh2 && $dbh2->ping) {
$dbh2 = DBI->connect("DBI:mysql:new_mail:update2.routename.com","db_user","sp1r3V");
}
						$rows = $dbh2->do($sql);
						$errmsg = $dbh2->errstr();
						print "Errstring <$errmsg>\n";
					}
					else	
					{
						$sql="select record_cnt from list_cnt where list_id=? and domain_id=?";
						$sth2=$dbh->prepare($sql);
						$sth2->execute($list_id,$did);
						($record_cnt)=$sth2->fetchrow_array();
						$sth2->finish();
						if ($record_cnt < $max_amount)
						{
							my $tmp_amt = $max_amount-$record_cnt;
							if ($tmp_amt < $amount_to_add)
							{
								$amount_to_add=$tmp_amt;
							}
							$sql="update email_chunk_list_${client_id} set list_id=$list_id,subscribe_date=curdate() where list_id=0 and status='A' and domain_id=$did order by subscribe_date limit $amount_to_add";
							print "Sql = <$sql>\n";
	unless ($dbh2 && $dbh2->ping) {
$dbh2 = DBI->connect("DBI:mysql:new_mail:update2.routename.com","db_user","sp1r3V");
}
							$rows = $dbh2->do($sql);
						$errmsg = $dbh2->errstr();
						print "Errstring <$errmsg>\n";
						}
					}
				}
			}
			$sth1a->finish();
			if ($clean_add_list > 0)
			{
				print "Adding $clean_add_list records to list $list_id\n";
				$sql="insert ignore into email_chunk_list_${client_id}(email_user_id,list_id,domain_id,subscribe_date,subscribe_time,unsubscribe_date,unsubscribe_time,status,first_name,last_name,address,address2,city,state,zip,country,dob,gender,phone,capture_date,member_source,source_url) select email_user_id,$list_id,domain_id,subscribe_date,subscribe_time,unsubscribe_date,unsubscribe_time,status,first_name,last_name,address,address2,city,state,zip,country,dob,gender,phone,capture_date,member_source,source_url from email_chunk_list_clean where client_id=$client_id and status='A' order by subscribe_date limit $clean_add_list";
	unless ($dbh2 && $dbh2->ping) {
$dbh2 = DBI->connect("DBI:mysql:new_mail:update2.routename.com","db_user","sp1r3V");
}
				$rows = $dbh2->do($sql);
						$errmsg = $dbh2->errstr();
						print "Errstring <$errmsg>\n";
				$sql="delete from email_chunk_list_clean where client_id=$client_id and status='A' order by subscribe_date limit $clean_add_list";
	unless ($dbh2 && $dbh2->ping) {
$dbh2 = DBI->connect("DBI:mysql:new_mail:update2.routename.com","db_user","sp1r3V");
}
				$rows = $dbh2->do($sql);
						$errmsg = $dbh2->errstr();
						print "Errstring <$errmsg>\n";
			}
		}
		$sth->finish();
		$sql="update list_profile set date_added=curdate(),clean_add=0 where profile_id=$profile_id";
	unless ($dbh && $dbh->ping) {
	$util->db_connect();
	$dbh = $util->get_dbh;
	}
		$rows = $dbh->do($sql);
	}
}
$sth1->finish();
$util->clean_up();
exit(0);
