#!/usr/bin/perl
# *****************************************************************************************
# move_daily.pl
#
# Batch program that runs from cron update the daily_records1 table nightly 
#
# History
# Jim Sobeck,   12/14/05,   Created
# *****************************************************************************************

use strict;
use lib "/var/www/html/newcgi-bin";
use util;

my $util = util->new;
my $STATE_USER = 0;
my $IP_USER = 0;
my $MALE_CID = 0;
my $sth;
my $sth1;
my $temp_str;
my $reccnt;
my $sth2;
my $sth2a;
my $dbh;
my $dbh1;
my $sql;
my $rows;
my $cdate = localtime();
my $program = "daily_emails.pl";
my $errmsg;
my $records_per_file;
my $max_files = 40000;
my $REDIR_ROTATION=250000;
my $IMG_ROTATION=500000;
my $cnt;
my $total_cnt;
my $list_str;
my $campaign_str;
my $temp_id;
my $list_cnt;
my $last_email_user_id;
my $max_emails;
my $filecnt;
my $clast60;
my $aolflag;
my $hotmailflag;
my $yahooflag;
my $otherflag;
my $openflag;
my $open_catid;
my $suppid;
my $catid;
my $content_id;
my $subdomain_name;
my $redir_domain;
my $redir_cnt;
my $redir_ind;
my $chged_redir;
my $added_seeds;
my $img_domain;
my $img_cnt;
my $img_ind;
my $max_ind;
my $redir_max_ind;
my $sname;
my $trand;
my $domain_suppid;
my $addrec;
my $begin;
my $end;
my $bend;
my $sth3;
my $sth4;
my $client_id;
    my $subject;
    my $from_addr;
    my $list_id;
    my $email_user_id;
    my $cemail;
    my $state;
    my $fname;
    my $lname;
    my $city;
    my $zip;
    my $daycnt;
    my $last_open_date;
    my $link_id;
    my $global_link_id;
    my $name_str;
    my $subject_name_str;
    my $fullname;
    my $loc;
    my $email_type;
    my $the_email;
    my $filename;
    my $dname;
    my $footer_dname;
    my $days;
my $max_client_emails = 100000;
$chged_redir = 0;
#
#  Set up array for servers
#
my $sarr_cnt;
my $sarr1_cnt;
my $cnt2;
my $ycnt2;
my $last_aol;
my @sarry;
my @sarry1;
my $cnt2a;
my $yarry_cnt;

# connect to the util database

$| = 1;
$util->db_connect();
$dbh = $util->get_dbh;
$dbh1=DBI->connect('DBI:mysql:new_mail:update2.routename.com', 'db_user', 'sp1r3V') or die "can't connect to db: $!";

send_powermail();

$util->clean_up();
exit(0);

# ***********************************************************************
# sub send_powermail
# ***********************************************************************

sub send_powermail
{
	my $campaign_id;
	my $max_client;
	my $cday;
	my $frec;
	my $lrec;


    $sql = "select max(user_id) from user where status='A'"; 
    $sth2 = $dbh->prepare($sql);
    $sth2->execute();
	($max_client) = $sth2->fetchrow_array();
	$sth2->finish();

	$client_id=1;
	while ($client_id <= $max_client)
	{
		print "Processing client $client_id\n";
        $sql = "select send_day,first_rec,last_rec from daily_info where client_id=$client_id and cdate=curdate() and rec_type='REG'"; 
        $sth2 = $dbh->prepare($sql);
        $sth2->execute();
        while (($cday,$frec,$lrec) = $sth2->fetchrow_array())
        {
			print "Processing client $client_id - $cday - $frec\n";
#			if ($cday < 1)
#			{
#				$sql="update daily_records1 set send_day=send_day+1 where client_id=$client_id and rec_id between $frec and $lrec and send_day=$cday";
#			}
#			else
#			{
				$sql="delete from daily_records1 where client_id=$client_id and rec_id between $frec and $lrec and send_day=$cday";
#			}				
			$rows=$dbh1->do($sql);
		}
		$sth2->finish();
#
        $sql = "select send_day,first_rec,last_rec from daily_info where client_id=$client_id and cdate=curdate() and rec_type='IB3'"; 
        $sth2 = $dbh->prepare($sql);
        $sth2->execute();
        while (($cday,$frec,$lrec) = $sth2->fetchrow_array())
        {
#			if ($cday < 1)
#			{
#				$sql="update daily_records_ib3 set send_day=send_day+1 where client_id=$client_id and rec_id between $frec and $lrec and send_day=$cday";
#			}
#			else
#			{
				$sql="delete from daily_records_ib3 where client_id=$client_id and rec_id between $frec and $lrec and send_day=$cday";
#			}				
			$rows=$dbh1->do($sql);
		}
		$sth2->finish();
		$client_id++;
	}
	$sql="delete from daily_records1 where date_added <= date_sub(curdate(),interval 2 day)";
	$rows=$dbh1->do($sql);
}
