#!/usr/bin/perl
# *****************************************************************************************
# calc_3rdparty_sent.pl
#
# Batch program that runs from cron to calculate 3rdparty sent counts 
#
# History
# Jim Sobeck,   01/25/06,   Created
# *****************************************************************************************

use strict;
use lib "/var/www/html/newcgi-bin";
use util;

my $util = util->new;
my $sth;
my $sth1;
my $sth2;
my $sth3;
my $dbh;
my $sql;
my $rows;
my $cdate = localtime();
my $program = "calc_3rdparty_sent.pl";
my $errmsg;
my $cname;
my $file_date;
my $id;
my $mailer_name;
my $ftp_ip;
my $ftp_username;
my $ftp_password;
my $list_path;
my $client_id;
my $email_addr;
my $filename;
my $got_rec;
my $aol_flag;
my $hotmail_flag;
my $yahoo_flag;
my $other_flag;
my $percent_sub;
my $profile_id;
my $campaign_id;
my ($aol_cnt, $yahoo_cnt, $hotmail_cnt,$other_cnt); 
my $member_cnt;
my $rows;

# connect to the util database
my $dbhq;
my $dbhu;
($dbhq,$dbhu)=$util->get_dbh();
$| = 1;
#
$sql="select campaign.campaign_id,campaign.profile_id,percent_sub,list_profile.aol_flag,list_profile.hotmail_flag,list_profile.yahoo_flag,list_profile.other_flag from campaign,3rdparty_campaign,list_profile,third_party_defaults where campaign.campaign_id=3rdparty_campaign.campaign_id and deleted_date is null and scheduled_date>=date_sub(curdate(),interval 2 day) and scheduled_date<=curdate() and campaign.profile_id=list_profile.profile_id and 3rdparty_campaign.third_party_id=third_party_defaults.third_party_id and third_party_defaults.send_data='Y'";
$sth1 = $dbhq->prepare($sql);
$sth1->execute();
while (($campaign_id,$profile_id,$percent_sub,$aol_flag,$hotmail_flag,$yahoo_flag,$other_flag) = $sth1->fetchrow_array())
{
        $sql = "select sum(aol_cnt),sum(yahoo_cnt),sum(hotmail_cnt)+sum(msn_cnt),sum(member_cnt)-sum(aol_cnt)-sum(yahoo_cnt)-sum(hotmail_cnt)-sum(msn_cnt) from list,list_profile_list where list.list_id = list_profile_list.list_id and profile_id=$profile_id and list.status ='A'";
        $sth = $dbhq->prepare($sql) ;
        $sth->execute();
        ($aol_cnt, $yahoo_cnt, $hotmail_cnt,$other_cnt) = $sth->fetchrow_array();
        $sth->finish();
		$member_cnt=0;
		if ($aol_flag eq "Y")
		{
			$member_cnt=$member_cnt + $aol_cnt;
		}
		if ($hotmail_flag eq "Y")
		{
			$member_cnt=$member_cnt + $hotmail_cnt;
		}
		if ($yahoo_flag ne "N")
		{
			$member_cnt=$member_cnt + $yahoo_cnt;
		}
		if ($other_flag eq "Y")
		{
			$member_cnt=$member_cnt + $other_cnt;
		}
		my $est_cnt = int(($member_cnt * $percent_sub)/100);
		print "Setting sent_count $member_cnt ($percent_sub) to $est_cnt for $campaign_id\n";
		$sql="update campaign_log set sent_cnt=$est_cnt where campaign_id=$campaign_id";
		$rows=$dbhu->do($sql);
}
$sth1->finish();
exit(0);
