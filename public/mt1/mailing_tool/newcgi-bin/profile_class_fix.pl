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
$sql="select profile_id,profile_name,aol_flag,yahoo_flag,other_flag,hotmail_flag from list_profile where status='A' and yahoo_flag='M'"; 
$sth=$dbhq->prepare($sql);
$sth->execute();
while (($profile_id,$profile_name,$aol_flag,$yahoo_flag,$other_flag,$hotmail_flag) = $sth->fetchrow_array())
{
my $prof_class=0;
$prof_class+=1 if $aol_flag eq 'Y';
$prof_class+=2 if $hotmail_flag eq 'Y';
$prof_class+=4 if $other_flag eq 'Y';
$prof_class+=8 if $yahoo_flag eq 'Y';
$prof_class+=8 if $yahoo_flag eq 'M';
	print "Setting profile class to $prof_class - $aol_flag,$hotmail_flag,$yahoo_flag,$other_flag\n";
	$sql="update list_profile set profile_class=$prof_class where profile_id=$profile_id";
	$rows=$dbhu->do($sql);
}
$sth->finish();
