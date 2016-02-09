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

	# Get the mail information for the campaign being used
	$sql = "select user_id,tab from user where status='A' and user_id = 92 order by user_id"; 
	$sth1 = $dbh->prepare($sql);
	$sth1->execute();
	while (($client_id,$tab) = $sth1->fetchrow_array())
	{
		$sql = "select list_id,list_name from list where status='A' and user_id=$client_id order by list_id"; 
		$sth2 = $dbh->prepare($sql);
		$sth2->execute();
		print "Sql <$sql>\n";
		while (($list_id,$list_name) = $sth2->fetchrow_array())
		{
			print "Processing $list_id - $list_name\n";
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

				$sql = "select email_user_id from daily_open_log where list_id = $list_id and date_opened = '$cday'"; 
				$sth = $dbh2->prepare($sql);
				$sth->execute();
				while (($email_user_id) = $sth->fetchrow_array())
				{
					$sql = "insert ignore into OpenClicks(email_user_id) values($email_user_id)";
					$rows = $dbh->do($sql);
					$sql = "update OpenClicks set last_open='$cday' where email_user_id=$email_user_id";
					$rows = $dbh->do($sql);
#					if ($list_id != $open_list_id)
#					{
#						$move_cnt++;
#					}
				}
				$sth->finish();
				$sql = "delete from daily_open_log where date_opened ='$cday' and list_id=$list_id";			
				$rows = $dbh2->do($sql);
				$i--;
			}
#			if ($move_cnt > 0)
#			{
#				$sql="insert into move_open_log(client_id,date_processed,list_id,reccnt) values($client_id,curdate(),$list_id,$move_cnt)";
#				$rows = $dbh->do($sql);
#			}
		}
		$sth2->finish();
	}
	$sth1->finish();
}
