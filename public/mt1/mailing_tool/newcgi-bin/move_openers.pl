#!/usr/bin/perl
# *****************************************************************************************
# move_openers.pl
#
# History
# Jim Sobeck,   04/09/03,   Created
# *****************************************************************************************

use strict;
use lib "/var/www/html/newcgi-bin";
use util;
use Data::Dumper;
#use Lib::Database::Perl::Interface::Email;

my $util = util->new;
my $sth;
my $sth1;
my $sth2;
my $dbh;
my $sql;
my $wait_days = 3;
my $rows;
my $cdate = localtime();
my $errmsg;
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
my $program;
#
#  Set up array for servers
#
my $cnt2;

# connect to the util database

$| = 1;
$util->db_connect();
$dbh = $util->get_dbh;
my $dbh2=DBI->connect('DBI:mysql:new_mail:suppress.routename.com', 'db_user', 'sp1r3V') or die "can't connect to db: $!";

# Send any mail that needs to be sent
mail_send();

$util->clean_up();
exit(0);

# ***********************************************************************
# This routine is used for sending all email for a single campaign
# ***********************************************************************
sub mail_send
{
	my $subject;
	my $from_addr;
	my $list_id;
	my $list_name;
	my $email_user_id;
	my $cemail;
	my $email_type;
	my $the_email;
	my $filename;
	my $filecnt;
	my $curcnt;
	my $client_id;
	my $max_clients;
	my $open_list_id;
	my $click_list_id;
	my $confirmed_list_id;
	my $unconfirmed_list_id;
	my $move_cnt;
	my $tab;
	my $cday;
	my $params;
	my $OPEN;
	my $CLICK;
	my $CONFIRM;
	my $UNCONFIRM;
	my $MOVE;

	$sql="select user_id,list_id from list where list_name='Openers'";
	$sth2 = $dbh->prepare($sql);
	$sth2->execute();
	while (($client_id,$list_id) = $sth2->fetchrow_array())
	{
		$OPEN->{$client_id}=$list_id;
	}
	$sth2->finish();
	$sql="select user_id,list_id from list where list_name='Clickers'";
	$sth2 = $dbh->prepare($sql);
	$sth2->execute();
	while (($client_id,$list_id) = $sth2->fetchrow_array())
	{
		$CLICK->{$client_id}=$list_id;
	}
	$sth2->finish();
	$sql="select user_id,list_id from list where list_name='Confirmed'";
	$sth2 = $dbh->prepare($sql);
	$sth2->execute();
	while (($client_id,$list_id) = $sth2->fetchrow_array())
	{
		$CONFIRM->{$client_id}=$list_id;
	}
	$sth2->finish();
	$sql="select user_id,list_id from list where list_name='Unconfirmed'";
	$sth2 = $dbh->prepare($sql);
	$sth2->execute();
	while (($client_id,$list_id) = $sth2->fetchrow_array())
	{
		$UNCONFIRM->{$client_id}=$list_id;
	}
	$sth2->finish();

	# Get the mail information for the campaign being used
	$sql = "select user_id,tab from user where status='A' and user_id != 92 order by user_id"; 
	$sth1 = $dbh->prepare($sql);
	$sth1->execute();
	while (($client_id,$tab) = $sth1->fetchrow_array())
	{
		print "Processing Client $client_id\n";
		$open_list_id = $OPEN->{$client_id} || 0;
		$click_list_id = $CLICK->{$client_id} || 0;
		$confirmed_list_id = $CONFIRM->{$client_id} || 0;
		$unconfirmed_list_id = $UNCONFIRM->{$client_id} || 0;

		$sql = "select list_id,list_name from list where status='A' and user_id=$client_id and list_id not in ($click_list_id,$confirmed_list_id,$unconfirmed_list_id) order by list_id"; 
		$sth2 = $dbh->prepare($sql);
		$sth2->execute();
		print "<$sql>\n";
		my $lstr="";
		while (($list_id,$list_name) = $sth2->fetchrow_array())
		{
			$lstr=$lstr.$list_id.",";
		}
		$sth2->finish();
		chop($lstr);
		$move_cnt = 0;
		my $i=8;
		while ($i > 0)
		{
			$sql="select date_sub(curdate(),interval $i day)";
			$sth = $dbh2->prepare($sql);
			$sth->execute();
			($cday) = $sth->fetchrow_array();
			$sth->finish();
			print "Processing day <$cday>\n";	

			$sql = "select list_id,email_user_id from daily_open_log where list_id in ($lstr) and date_opened = '$cday'"; 
			$sth = $dbh2->prepare($sql);
			$sth->execute();
			while (($list_id,$email_user_id) = $sth->fetchrow_array())
			{
				print "moving $email_user_id to $open_list_id\n";	
				if ($email_user_id < 1111832975)
				{
					$sql = "update email_list set list_id=$open_list_id,subscribe_date=curdate(),subscribe_time=curtime() where email_user_id=$email_user_id and status='A'";
				}
				else
				{
					$sql = "update email_list_new set list_id=$open_list_id,subscribe_date=curdate(),subscribe_time=curtime() where email_user_id=$email_user_id and status='A'";
				}
				$rows = $dbh->do($sql);
										
				if ($list_id != $open_list_id)
				{
					$move_cnt++;
					$MOVE->{$client_id}{$list_id}++;
				}
				#
				# move all other records 
				#
					
				move_email_addr($email_user_id,$client_id,$cday,$OPEN);
			}
			$sth->finish();
			$sql = "delete from daily_open_log where date_opened ='$cday' and list_id in ($lstr)";
			$rows = $dbh2->do($sql);
			print "Deleting open records\n";
			$sql = "delete from daily_open_log where date_opened = '$cday' and list_id=$click_list_id";	
			$rows = $dbh2->do($sql);
			print "Done deleting click records\n";
			$sql = "delete from daily_open_log where date_opened = '$cday' and list_id=$confirmed_list_id";			
			$rows = $dbh2->do($sql);
			$sql = "delete from daily_open_log where date_opened = '$cday' and list_id=$unconfirmed_list_id";			
			$rows = $dbh2->do($sql);
			$i--;
		}
		if ($move_cnt > 0)
		{
			#update list to have get_data process list
			updateListStatus($open_list_id);

		}
	}
	$sth1->finish();
	foreach (keys %{$MOVE})
	{
    	$client_id=$_;
    	foreach (keys %{$MOVE->{$client_id}})
    	{
        	$list_id=$_;
			$sql="insert into move_open_log(client_id,date_processed,list_id,reccnt) values($client_id,curdate(),$list_id,$MOVE->{$client_id}{$list_id})";
			$rows = $dbh->do($sql);
			updateListStatus($list_id);
		}
	}
}

sub updateListStatus {

	my ($listID) = @_;
	
	my $updateQuery = qq|
		
	UPDATE
		list
	SET 
		listUpdatedStatus = 1
	WHERE 
		list_id = $listID
	|;	
					
	$dbh->do($updateQuery);
		
	
}

sub display
{
    my ($message, $displayValue)    = @_;

    print "\n" . '*' x 30 ."\n\n";
    print "$message: " . Dumper($displayValue) . "\n";
}

sub move_email_addr
{
	my ($eid, $client_id, $cday, $OPEN) = @_;
	
	my $sth2a;
	my $teid;
	my $list_id;
	my $user_id;
	my $rows;
	my $table;
	
	my $sql =qq|
		select 
			email_user_id,el.list_id,l.user_id,"email_list"
		from 
			email_list el, list l 
		where 
		el.list_id=l.list_id 
		and el.status='A' 
		and email_addr = (select email_addr from email_list where email_user_id=?) 
		and el.email_user_id != ? 
		and l.list_name not in ('Clickers','Confirmed','Unconfirmed')
		union
		select 
			email_user_id,el.list_id,l.user_id,"email_list_new" 
		from 
			email_list_new el, list l 
		where 
		el.list_id=l.list_id 
		and el.status='A' 
		and email_addr = (select email_addr from email_list_new where email_user_id=?) 
		and el.email_user_id != ? 
		and l.list_name not in ('Clickers','Confirmed','Unconfirmed')
	|;

	$sth2a=$dbh->prepare($sql);
	$sth2a->execute($eid,$eid,$eid,$eid);
	while (($teid,$list_id,$user_id,$table)=$sth2a->fetchrow_array())
	{
		$sql = "update $table set list_id=$OPEN->{$user_id},subscribe_date=curdate(),subscribe_time=curtime() where email_user_id=$teid and status='A'";
		$rows=$dbh->do($sql);
										
		$sql = "delete from daily_open_log where date_opened='$cday' and list_id=$list_id and email_user_id=$teid";			
		$rows = $dbh2->do($sql);
	}
	$sth2a->finish();
}
