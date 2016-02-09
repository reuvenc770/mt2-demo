#!/usr/bin/perl
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
my $sth1;
my $new_pid;
($dbhq,$dbhu)=$util->get_dbh();
my ($profile_id,$profile_name,$client_id,$day_flag,$aol_flag,$yahoo_flag,$other_flag,$hotmail_flag,$status,$max_emails,$last_email_user_id,$loop_flag,$list_to_add_from,$amount_to_add,$unique_id,$percent_sub,$profile_type,$profile_class);
# 
$| = 1;
$sql="select profile_id,profile_name,client_id,day_flag,aol_flag,yahoo_flag,other_flag,hotmail_flag,status,max_emails,last_email_user_id,loop_flag,list_to_add_from,amount_to_add,unique_id,percent_sub,profile_type,profile_class from list_profile where third_party_id=1";
$sth=$dbhq->prepare($sql);
$sth->execute();
while (($profile_id,$profile_name,$client_id,$day_flag,$aol_flag,$yahoo_flag,$other_flag,$hotmail_flag,$status,$max_emails,$last_email_user_id,$loop_flag,$list_to_add_from,$amount_to_add,$unique_id,$percent_sub,$profile_type,$profile_class) = $sth->fetchrow_array())
{
	$sql="insert into list_profile(profile_name,client_id,day_flag,aol_flag,yahoo_flag,other_flag,hotmail_flag,status,max_emails,last_email_user_id,loop_flag,list_to_add_from,amount_to_add,unique_id,percent_sub,profile_type,profile_class,third_party_id) values('$profile_name',$client_id,'$day_flag','$aol_flag','$yahoo_flag','$other_flag','$hotmail_flag','$status',$max_emails,$last_email_user_id,'$loop_flag',0,0,'$unique_id','$percent_sub','$profile_type',$profile_class,11)";
	$rows=$dbhu->do($sql);
	$sql="select max(profile_id) from list_profile where third_party_id=11 and profile_name='$profile_name'";
	$sth1=$dbhu->prepare($sql);
	$sth1->execute();
	($new_pid)=$sth1->fetchrow_array();
	$sth1->finish();
	print "Added $profile_name - $new_pid\n";
	$sql="insert into list_profile_list(profile_id,list_id) select $new_pid,list_id from list_profile_list where profile_id=$profile_id";
	$rows=$dbhu->do($sql);
}
$sth->finish();
