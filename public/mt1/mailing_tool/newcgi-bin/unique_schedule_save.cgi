#!/usr/bin/perl
#===============================================================================
# Purpose: Bottom frame of unique_main schedule page 
# Name   : unique_schedule_save.cgi 
#
#--Change Control---------------------------------------------------------------
# 03/20/09  Jim Sobeck  Creation
#===============================================================================

#-----  include Perl Modules ---------
use strict;
use CGI;
use util;

#------  get some objects to use later ---------
my $util = util->new;
my $query = CGI->new;
my $name;
my $sql;
my $sth;
my $sth1;
my $sth1a;
my $send_date;
my $errmsg;
my $tracking_id;
my $log_camp;
my $randomize_records;
my $dir2;
my $utype;
my $chour;
my $rows;
my $cday;
my $camp_id;
my $dbh;
my $phone;
my $email;
my $id;
my $aim;
my $website;
my $username;
my $password;
my $tables;
my $usa_id = $query->param('usa_id');
my $advertiser_name;
my $cname;
my $slot_id;
my $tday;
my $client_id;
my $priority;
my $profile_id;
my $brand_id;
my $third_id;
my $creative1;
my $subject1;
my $from1;
my $send_email;
my $from_addr;
my $suppid;
my $supp_name;
my $last_updated;
my $filedate;
my $daycnt;
my $deploy_name;
my $mailer_name;
my $catid;
my $exclude_days;
my $exflag;
my $current_day;
my $current_hour;

my $template_id=1;
my $include_wiki="N";
my $include_name="N";
my $random_subdomain="N";
my $dup_client_group_id=0;
my $header_id=0;
my $headerid=1;
my $footer_id=0;
my $nl_id=14;
my $dupes_flag="N/A";
my $mta_id=11;
my $source_url="ALL";
my $mail_from="";
my $return_path="";
my $prepull="N";
my $ConvertSubject="None";
my $ConvertFrom="None";
my $use_master="N";
my $useRdns="N";
my $zip="ALL";
my $wiki="N";
my $unq_id;
my $ustatus;
my ($client_group_id,$group_id,$shour,$sdate1,$stime,$profile_id,$domainid);
my $stoptime;
my $creative;
my $csubject;
my $cfrom;
my $uid;
my $aid;
my $send_date;
my $server_id;
my $usaType;
my $jlogProfileID=0;

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}
#------  connect to the util database -----------
my ($dbhq,$dbhu)=$util->get_dbh();
#
$sql="select advertiser_id,creative_id,subject_id,from_id,usaType from UniqueScheduleAdvertiser where usa_id=?";
$sth = $dbhq->prepare($sql);
$sth->execute($usa_id);
($aid,$creative,$csubject,$cfrom,$usaType) = $sth->fetchrow_array();
$sth->finish();

$sql="select advertiser_name from advertiser_info where advertiser_id=?"; 
$sth = $dbhq->prepare($sql);
$sth->execute($aid);
($cname) = $sth->fetchrow_array();
$sth->finish();


my $startdate= $query->param('sdate');
my $nid= $query->param('nid');
my $mtaid= $query->param('mtaid');
my @chkboxs= $query->param('chkbox');
my $submit= $query->param('submit');
foreach my $chkbox (@chkboxs) 
{
   ($slot_id,$cday) = split('_',$chkbox);
	my $sth1a;
    $sql="select client_group_id,ip_group_id,hour(schedule_time),date_add(curdate(),interval $cday day),schedule_time,end_time,profile_id,mailing_domain,template_id,slot_type,hour_offset,log_campaign,randomize_records,mta_id,source_url,zip,mail_from,use_master,useRdns,return_path,prepull,ConvertSubject,ConvertFrom,jlogProfileID from UniqueSlot where slot_id=?";
    $sth=$dbhu->prepare($sql);
    $sth->execute($slot_id);
    ($client_group_id,$group_id,$shour,$sdate1,$stime,$stoptime,$profile_id,$domainid,$template_id,$utype,$chour,$log_camp,$randomize_records,$mta_id,$source_url,$zip,$mail_from,$use_master,$useRdns,$return_path,$prepull,$ConvertSubject,$ConvertFrom,$jlogProfileID)=$sth->fetchrow_array();
    $sth->finish();
	#
	# Check to see if advertiser excluded
	#
	my $exflag;
	$sql="select substr(exclude_days,dayofweek(date_add('$sdate1',interval 6 day)),1) from advertiser_info where advertiser_id=?";;
	$sth=$dbhu->prepare($sql);
	$sth->execute($aid);
	($exflag)=$sth->fetchrow_array();
	$sth->finish();
	if ($exflag eq "Y")
	{
		next;
	}
	#
	# check to see if campaign already scheduled
	#
	$sql="select unique_campaign.unq_id,unique_campaign.status,unique_campaign.send_date,unique_campaign.server_id from UniqueSchedule,unique_campaign where UniqueSchedule.slot_id=$slot_id and UniqueSchedule.unq_id=unique_campaign.unq_id and unique_campaign.send_date=date_add(curdate(),interval $cday day)";
    $sth1a = $dbhq->prepare($sql);
    $sth1a->execute();
	if (($unq_id,$ustatus,$send_date,$server_id)=$sth1a->fetchrow_array())
	{
		$cname=~s/'/''/g;
		$sql="update unique_campaign set campaign_name='$cname',advertiser_id=$aid,log_campaign='$log_camp',randomize_records='$randomize_records',ConvertSubject='$ConvertSubject',ConvertFrom='$ConvertFrom',jlogProfileID=$jlogProfileID where campaign_id=$camp_id";
		$rows=$dbhu->do($sql);
		next;
	}
	$sth1a->finish();
    $sql="insert into unique_campaign(campaign_type,status,campaign_id,email_addr,nl_id,mailing_domain,mailing_ip,campaign_name,advertiser_id,creative_id,subject_id,from_id,mailing_template,include_wiki,send_date,mta_id,include_name,random_subdomain,profile_id,templateID,group_id,client_group_id,send_time,stop_time,header_id,footer_id,dup_client_group_id,dupes_flag,slot_type,hour_offset,log_campaign,randomize_records,source_url,zip,mail_from,use_master,useRdns,return_path,prepull,ConvertSubject,ConvertFrom,jlogProfileID) values('DEPLOYED','START',0,'',$nl_id,'$domainid','','$cname',$aid,$creative,$csubject,$cfrom,$template_id,'$wiki','$sdate1',$mta_id,'$include_name','$random_subdomain',$profile_id,$headerid,$group_id,$client_group_id,'$stime','$stoptime',$header_id,$footer_id,$dup_client_group_id,'$dupes_flag','$utype',$chour,'$log_camp','$randomize_records','$source_url','$zip','$mail_from','$use_master','$useRdns','$return_path','$prepull','$ConvertSubject','$ConvertFrom',$jlogProfileID)";
	$rows=$dbhu->do($sql);
	$sql="select LAST_INSERT_ID()";
	$sth=$dbhu->prepare($sql);
	$sth->execute();
	($uid)=$sth->fetchrow_array();
	$sth->finish();

	$sql="insert into UniqueSchedule(slot_id,unq_id) values($slot_id,$uid)";
	$rows=$dbhu->do($sql);

	add_campaigns($uid,$profile_id,$sdate1,$stime,$client_group_id,$log_camp,$group_id);
	$sql="insert into UniqueCreative(unq_id,creative_id,rowID) select $uid,creative_id,rowID from UniqueAdvertiserCreative where usa_id=$usa_id";
	$rows=$dbhu->do($sql);
	$sql="insert into UniqueSubject(unq_id,subject_id,rowID) select $uid,subject_id,rowID from UniqueAdvertiserSubject where usa_id=$usa_id";
	$rows=$dbhu->do($sql);
	$sql="insert into UniqueFrom(unq_id,from_id,rowID) select $uid,from_id,rowID from UniqueAdvertiserFrom where usa_id=$usa_id";
	$rows=$dbhu->do($sql);
	$sql="insert ignore into UniqueDomain(unq_id,mailing_domain) select $uid,mailing_domain from UniqueSlotDomain where slot_id=$slot_id"; 
	$rows=$dbhu->do($sql);
	$sql="insert ignore into UniqueContentDomain(unq_id,domain_name) select $uid,domain_name from UniqueSlotContentDomain where slot_id=$slot_id"; 
	$rows=$dbhu->do($sql);
}
my @dchkboxs= $query->param('dchkbox');
foreach my $dchkbox (@dchkboxs) 
{
   ($slot_id,$cday) = split('_',$dchkbox);
	#
	# check to see if campaign already scheduled
	#
	$sql="select unique_campaign.unq_id,unique_campaign.status,send_date from UniqueSchedule,unique_campaign where UniqueSchedule.slot_id=$slot_id and UniqueSchedule.unq_id=unique_campaign.unq_id and unique_campaign.send_date=date_add(curdate(),interval $cday day)";
    $sth1a = $dbhq->prepare($sql);
    $sth1a->execute();
	if (($unq_id,$ustatus,$send_date)=$sth1a->fetchrow_array())
	{
		my $camp_id;
		$sql="select campaign_id from campaign where scheduled_date=? and id=?";
		$sth=$dbhu->prepare($sql);
		$sth->execute($send_date,$unq_id);
		while (($camp_id)=$sth->fetchrow_array())
		{
			$sql = "update campaign set deleted_date = now() where campaign_id=$camp_id"; 
			$rows = $dbhu->do($sql);
			$sql="delete from current_campaigns where campaign_id=$camp_id"; 
			$rows = $dbhu->do($sql);
		}
		$sth->finish();
		$sql="delete from UniqueSchedule where unq_id=$unq_id";
		$rows = $dbhu->do($sql);
		$sql="delete from unique_campaign where unq_id=$unq_id";
		$rows = $dbhu->do($sql);
		$sql="delete from UniqueDomain where unq_id=$unq_id";
		$rows = $dbhu->do($sql);
		$sql="delete from UniqueContentDomain where unq_id=$unq_id";
		$rows = $dbhu->do($sql);
		$sql="delete from UniqueCreativewhere unq_id=$unq_id";
		$rows = $dbhu->do($sql);
		$sql="delete from UniqueFrom where unq_id=$unq_id";
		$rows = $dbhu->do($sql);
		$sql="delete from UniqueSubject where unq_id=$unq_id";
		$rows = $dbhu->do($sql);
	}
}
print "Location: unique_schedule.cgi?sdate=$startdate&nid=$nid&mtaid=$mtaid\n\n";


sub add_campaigns
{
	my ($tid,$profile_id,$sdate,$stime,$cgroupid,$log_camp,$ipgroup_id)=@_;
	my $sql;
	my $profile_name;
	my $client_id;
	my $brand_id;
	my $camp_id;
	my $third_id;
	my $cdate;
	my $added_camp;

	my $cnt;
	my $priority;

	$sql="select count(*) from IpGroup ip where group_id=? and (ip.goodmail_enabled='Y' or ip.group_name like 'discover%' or ip.group_name like 'credithelpadvisor%')";
	my $sth1=$dbhu->prepare($sql);
	$sth1->execute($ipgroup_id);
	($cnt)=$sth1->fetchrow_array();
	$sth1->finish();
	if ($cnt > 0)
	{
		$priority=1;
	}
	else
	{
		$priority=5;
	}
	$added_camp=0;
	$sql="select curdate()"; 
	my $STHQ=$dbhq->prepare($sql);
	$STHQ->execute();
	($cdate) = $STHQ->fetchrow_array();
	$STHQ->finish();

	$sql="select client_id from ClientGroupClients where client_group_id=?";
	$STHQ=$dbhq->prepare($sql);
	$STHQ->execute($cgroupid);
	while (($client_id) = $STHQ->fetchrow_array())
	{
		$sql="select brand_id,third_party_id from client_brand_info where client_id=? and nl_id=? and status='A' and brand_type='Newsletter'";
		my $STHQ1=$dbhq->prepare($sql);
		$STHQ1->execute($client_id,$nl_id);
		if (($brand_id,$third_id) = $STHQ1->fetchrow_array())
		{	
			my $timestr=$sdate." ".$stime;
			$sql = "insert into campaign(user_id,campaign_name,status,created_datetime,scheduled_datetime,advertiser_id,profile_id,brand_id,scheduled_date,scheduled_time,campaign_type,id) values($client_id,'$cname','C',now(),'$timestr',$aid,$profile_id,$brand_id,'$sdate','$stime','NEWSLETTER','$tid')";
			$rows=$dbhu->do($sql);
			$sql = "select max(campaign_id) from campaign where campaign_name='$cname' and scheduled_date='$sdate' and id='$tid' and advertiser_id=$aid and profile_id=$profile_id and brand_id=$brand_id";
			$sth = $dbhq->prepare($sql);
			$sth->execute();
			($camp_id) = $sth->fetchrow_array();
			$sth->finish();

			if (($sdate eq $cdate) and ($added_camp == 0))
			{
				$sql="insert into current_campaigns(campaign_id,scheduled_date,scheduled_time,campaign_type) values($camp_id,curdate(),'$stime','DEPLOYED')";
				$rows=$dbhu->do($sql);
				$added_camp=1;
				$sql="select tracking_id from advertiser_tracking where advertiser_id=? and client_id=? and daily_deal='N'"; 
				my $sth1=$dbhu->prepare($sql);
				$sth1->execute($aid,$client_id);
				if (($tracking_id)=$sth1->fetchrow_array())
				{
				}
				else
				{
					$util->genLinks($dbhu,$aid,0);
				}
				$sth1->finish();
			}
		}
		$STHQ1->finish();
	}
	$STHQ->finish();
}
