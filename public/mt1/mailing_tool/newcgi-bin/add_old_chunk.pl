#!/usr/bin/perl
# *****************************************************************************************
# add_chunk_data.pl
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
my $client_id=$ARGV[0];
my $start_date='2006-04-04';
#my $start_date='2005-11-01';
my $domain_id;
my ($email_addr,$gender,$sdate,$stime,$first_name,$last_name,$birth_date,$address,$address2,$city,$state,$zip,$country,$phone,$date_captured,$member_source,$source_url);
my $eid;


# connect to the util database

$| = 1;

$util->db_connect();
$dbh = $util->get_dbh;
my $dbh2 = DBI->connect("DBI:mysql:new_mail:update2.routename.com","db_user","sp1r3V");
$sql="select domain_id from email_domains where chunked=1";
$sth1 = $dbh->prepare($sql);
$sth1->execute();
$domain_str="";
while (($domain_id)=$sth1->fetchrow_array())
{
	$domain_str=$domain_str.$domain_id.",";
}
$sth1->finish();
$_=$domain_str;
chop;
$domain_str=$_;
#
$sql="select list_id from list where status='A' and list_type='GENERAL' and user_id=$client_id order by list_id";
$sth1 = $dbh->prepare($sql);
$sth1->execute();
while (($list_id)=$sth1->fetchrow_array())
{
	print "Processing list $list_id\n";
	$sql="select email_addr,gender,subscribe_date,subscribe_time, first_name, last_name, dob, address, address2, city, state, zip, country, phone, capture_date, member_source,source_url,domain_id from email_list where list_id=? and domain_id in ($domain_str) and status='A' and subscribe_date < ?";
unless ($dbh && $dbh->ping) {
print "connecting\n";
$util->db_connect();
$dbh = $util->get_dbh;
   }
	$sth=$dbh->prepare($sql);
	$sth->execute($list_id,$start_date);
	while (($email_addr,$gender,$sdate,$stime,$first_name,$last_name,$birth_date,$address,$address2,$city,$state,$zip,$country,$phone,$date_captured,$member_source,$source_url,$domain_id) = $sth->fetchrow_array())
	{
            $sql="select email_user_id from master_email_chunk_list where email_addr=?";
            my $sth1a = $dbh2->prepare($sql);
            $sth1a->execute($email_addr);
            if (($eid) = $sth1a->fetchrow_array())
            {
                $sth1a->finish();
            }
            else
            {
                $sth1a->finish();
                $sql="insert into master_email_chunk_list(email_addr) values('$email_addr')";
                $rows = $dbh2->do($sql);
                $sql="select LAST_INSERT_ID()";
                $sth1a = $dbh2->prepare($sql);
                $sth1a->execute();
                ($eid) = $sth1a->fetchrow_array();
                $sth1a->finish();
            }
            # Store in the client table
            #
            my $tab="email_chunk_list_".$client_id;
			print "Adding $email_addr\n";
            my $sql_ins = qq{insert ignore into $tab(email_user_id,list_id, status,gender,subscribe_date,subscribe_time, first_name, last_name, dob, address, address2, city, state, zip, country, phone, capture_date, member_source,source_url,domain_id) values ($eid,0,'A','$gender', '$sdate', '$stime','$first_name', '$last_name','$birth_date', '$address', '$address2','$city', '$state', '$zip','$country', '$phone','$date_captured','$member_source','$source_url',$domain_id) };
            $rows = $dbh2->do($sql_ins);
	}
}
$sth1->finish();
