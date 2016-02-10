#!/usr/bin/perl
# *****************************************************************************************
# add_codes.pl
#
# History
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
my $from_addr;
my $sql;
my $rows;
my $add_freq;
my $cdate = localtime();
my $amount_to_add;
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
my $eid;
my $email_addr;


# connect to the util database

$| = 1;

$util->db_connect();
$dbh = $util->get_dbh;
my $dbh2 = DBI->connect("DBI:mysql:new_mail:","db_user","sp1r3V");

$sql = "select distinct email_user_id from sm_log where sm_type='$ARGV[0]' and log_date >= '2006-08-01' and log_date < '2006-08-03'";
$sth1 = $dbh->prepare($sql);
$sth1->execute();
while (($eid) = $sth1->fetchrow_array())
{
	$sql="select email_addr from email_list,list where email_user_id=? and email_list.status=? and email_list.list_id=list.list_id and user_id in (8,4,3,26,17,21,7) and unsubscribe_date >= '2006-08-01 and unsubscribe_date <= '2006-08-03'";
	$sth=$dbh->prepare($sql);
	$sth->execute($eid,$ARGV[1]);
	if (($email_addr) = $sth->fetchrow_array())
	{
		print "updating $email_addr\n";
		$sql="update email_list set status='A',unsubscribe_date=null,unsubscribe_time=null where email_user_id=$eid";
		my $rows=$dbh->do($sql); 
	}
	$sth->finish();
}
$sth1->finish();
exit(0);
