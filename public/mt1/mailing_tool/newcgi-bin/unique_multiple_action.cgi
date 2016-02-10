#!/usr/bin/perl
#===============================================================================
# Name   : unique_multiple_action.cgi - Performs functions on unique campaigns 
#
#--Change Control---------------------------------------------------------------
# 04/05/10  Jim Sobeck  Creation
#===============================================================================

#-----  include Perl Modules ---------
use strict;
use CGI;
use util;

#------  get some objects to use later ---------
my $util = util->new;
my $query = CGI->new;
my $sql;
my $sth;
my $dbh;
my $rows;
my ($uid,$cname,$sdate,$cstatus);
my $tuid;
my $adv_id;
my $nl_id;
my ($profile_id,$stime,$cdate,$cgroupid,$group_id);

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}
#------  connect to the util database -----------
my ($dbhq,$dbhu)=$util->get_dbh();
my @caction=$query->param('caction');
my $f=$query->param('submit');
my $gsm=$query->param('gsm');
my $sord=$query->param('sord');
if ($f eq "Resume")
{
	foreach $uid (@caction)
	{
		my $dbid;
    	$sql="select dbID from campaign where id=? limit 1";
    	$sth=$dbhu->prepare($sql);
    	$sth->execute($uid);
    	($dbid)=$sth->fetchrow_array();
    	$sth->finish();
		logAction($user_id,$uid,'Resumed');

    	if ($dbid eq "")
    	{
			$sql="update unique_campaign set status='START' where unq_id=$uid and status='PAUSED' and server_id > 0";
		}
		else
		{
			$sql="update unique_campaign set status='PENDING' where unq_id=$uid and status='PAUSED' and server_id > 0";
		}
		$rows=$dbhu->do($sql);
		$sql="update unique_campaign set status='START' where unq_id=$uid and status='PAUSED' and server_id = 0";
		$rows=$dbhu->do($sql);
	}
}
elsif ($f eq "Restart")
{
	foreach $uid (@caction)
	{
    	$sql="update unique_campaign set status = 'START' where unq_id=$uid and status='CANCELLED'";
    	$dbhu->do($sql);
		logAction($user_id,$uid,'Restarted');

    	#$sql="update campaign set dbID = '',deleted_date=null where id=$uid";
    	#$dbhu->do($sql);
	}
}
elsif ($f eq "Replace USA")
{
	my $uidstr="";
	foreach $uid (@caction)
	{
		logAction($user_id,$uid,'Replaced USA');
		$uidstr=$uidstr.$uid."|";
	}
	print "Location: /cgi-bin/unique_replace_usa.cgi?gsm=$gsm&sord=$sord&uidstr=$uidstr\n\n";
	exit();
}
elsif ($f eq "Replace Domains")
{
	my $uidstr="";
	foreach $uid (@caction)
	{
		logAction($user_id,$uid,'Replaced Domain');
		$uidstr=$uidstr.$uid."|";
	}
	print "Location: /cgi-bin/unique_replace_domain.cgi?gsm=$gsm&sord=$sord&uidstr=$uidstr\n\n";
	exit();
}
elsif ($f eq "Pause")
{
	foreach $uid (@caction)
	{
		logAction($user_id,$uid,'Paused');
		$sql="update unique_campaign set status='PAUSED',pause_flag=1 where unq_id=$uid and status in ('PENDING','START','PRE-PULLING','INJECTING')";
		$rows=$dbhu->do($sql);
		$sql="update UniqueHotmailChunk set pause_flag=1 where unq_id=$uid";
		$rows=$dbhu->do($sql);
	}
}
elsif ($f eq "Cancel")
{
	foreach $uid (@caction)
	{
		my $sdate;
		$sql="select send_date from unique_campaign where unq_id=$uid and status in ('PENDING','START','PAUSED','PRE-PULLING','INJECTING','SLEEPING')";
		my $sth1=$dbhu->prepare($sql);
		$sth1->execute();
		if (($sdate)=$sth1->fetchrow_array())
		{
			logAction($user_id,$uid,'Cancelled');
			$sql="update unique_campaign set status='CANCELLED',pid=0,end_time=now(),cancel_reason='Manually Cancelled' where unq_id=$uid and status in ('PENDING','START','PAUSED','PRE-PULLING','INJECTING','SLEEPING')";
			$rows=$dbhu->do($sql);
			$sql="select campaign_id from campaign where id=? and scheduled_date=?";
			$sth=$dbhu->prepare($sql);
			$sth->execute($uid,$sdate);
			my $cid;
			while (($cid)=$sth->fetchrow_array())
			{
				$sql="delete from current_campaigns where campaign_id=$cid"; 
				$rows=$dbhu->do($sql);
				$sql="update campaign set deleted_date=now(),sent_datetime=now() where campaign_id=$cid"; 
				$rows=$dbhu->do($sql);
			}
			$sth->finish();
		}
		$sth1->finish();
	}
}
elsif ($f eq "Redeploy")
{
	foreach $tuid (@caction)
	{
		$sql="insert into unique_campaign(campaign_name,campaign_type,status,send_date,campaign_id,email_addr,nl_id,mta_id,mailing_domain,mailing_ip,advertiser_id,creative_id,subject_id,from_id,mailing_template,include_wiki,include_name,random_subdomain,profile_id,templateID,send_time,group_id,client_group_id,header_id,footer_id,dup_client_group_id,dupes_flag,server_id,slot_type,hour_offset,log_campaign,use_master,pasted_domains,randomize_records,source_url,article_id,zip,mail_from,useRdns,newMailing,return_path,deployFileName,deployPoolID,deployPoolChunkID,injectorID,jlogProfileID,ConvertSubject,ConvertFrom) select campaign_name,'TEST','START',curdate(),campaign_id,email_addr,nl_id,mta_id,mailing_domain,mailing_ip,advertiser_id,creative_id,subject_id,from_id,mailing_template,include_wiki,include_name,random_subdomain,profile_id,templateID,send_time,group_id,client_group_id,header_id,footer_id,dup_client_group_id,dupes_flag,0,slot_type,hour_offset,log_campaign,use_master,pasted_domains,randomize_records,source_url,article_id,zip,mail_from,useRdns,newMailing,return_path,deployFileName,deployPoolID,deployPoolChunkID,injectorID,jlogProfileID,ConvertSubject,ConvertFrom from unique_campaign where unq_id=$tuid";
		$rows=$dbhu->do($sql);
		logAction($user_id,$tuid,'Redeployed');
		my $old_unq_id=$tuid;
		$sql="select max(unq_id) from unique_campaign where campaign_name = (select campaign_name from unique_campaign where unq_id=$tuid)";
		$sth=$dbhu->prepare($sql);
		$sth->execute();
		($uid)=$sth->fetchrow_array();
		$sth->finish();
		$sql="select nl_id,advertiser_id,profile_id,campaign_name,send_date,send_time,curdate(),client_group_id,group_id from unique_campaign where unq_id=? and campaign_type='TEST'";
		$sth=$dbhu->prepare($sql);
		$sth->execute($uid);
		if (($nl_id,$adv_id,$profile_id,$cname,$sdate,$stime,$cdate,$cgroupid,$group_id)=$sth->fetchrow_array())
		{
			$sth->finish();
			add_campaigns($uid,$profile_id,$sdate,$stime,$cdate,$cgroupid,$group_id);
			$sql="update unique_campaign set campaign_type='DEPLOYED',status='START' where unq_id=$uid";
			$rows=$dbhu->do($sql);
		}
		else
		{
			$sth->finish();
		}
		$sql="insert into UniqueCreative select $uid,creative_id,rowID from UniqueCreative where unq_id=$old_unq_id";
		$rows=$dbhu->do($sql);
		$sql="insert into UniqueSubject select $uid,subject_id,rowID from UniqueSubject where unq_id=$old_unq_id";
		$rows=$dbhu->do($sql);
		$sql="insert into UniqueFrom select $uid,from_id,rowID from UniqueFrom where unq_id=$old_unq_id";
		$rows=$dbhu->do($sql);
		$sql="insert into UniqueDomain select $uid,mailing_domain from UniqueDomain where unq_id=$old_unq_id";
		$rows=$dbhu->do($sql);
		$sql="insert into UniqueContentDomain select $uid,domain_name from UniqueContentDomain where unq_id=$old_unq_id";
		$rows=$dbhu->do($sql);
	}
}
print "Location: /cgi-bin/unique_deploy_list.cgi?gsm=$gsm&sord=$sord\n\n";


sub add_campaigns
{
	my ($tid,$profile_id,$sdate,$stime,$cdate,$cgroupid,$ipgroup_id)=@_;
	my $sql;
	my $profile_name;
	my $client_id;
	my $brand_id;
	my $camp_id;
	my $third_id;
	my $added_camp;
	my $cnt;
	my $priority;
	my $tracking_id;

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

	$sql="select client_id from ClientGroupClients where client_group_id=?";
	my $STHQ=$dbhq->prepare($sql);
	$STHQ->execute($cgroupid);
	while (($client_id) = $STHQ->fetchrow_array())
	{
		$sql="select brand_id,third_party_id from client_brand_info where client_id=? and nl_id=? and status='A' and brand_type='Newsletter'";
		my $STHQ1=$dbhq->prepare($sql);
		$STHQ1->execute($client_id,$nl_id);
		if (($brand_id,$third_id) = $STHQ1->fetchrow_array())
		{	
			my $timestr=$sdate." ".$stime;
			$sql = "insert into campaign(user_id,campaign_name,status,created_datetime,scheduled_datetime,advertiser_id,profile_id,brand_id,scheduled_date,scheduled_time,campaign_type,id) values($client_id,'$cname','C',now(),'$timestr',$adv_id,$profile_id,$brand_id,'$sdate','$stime','NEWSLETTER','$tid')";
			$rows=$dbhu->do($sql);
			$sql = "select max(campaign_id) from campaign where campaign_name='$cname' and scheduled_date='$sdate' and id='$tid' and advertiser_id=$adv_id and profile_id=$profile_id and brand_id=$brand_id";
			$sth = $dbhq->prepare($sql);
			$sth->execute();
			($camp_id) = $sth->fetchrow_array();
			$sth->finish();
#		   	$sql="insert into campaign_log(campaign_id,date_sent,user_id) values($camp_id,curdate(),$client_id)";
#		   	$rows=$dbhu->do($sql);
			if (($sdate eq $cdate) and ($added_camp == 0))
			{
				$sql="insert into current_campaigns(campaign_id,scheduled_date,scheduled_time,campaign_type) values($camp_id,curdate(),'$stime','DEPLOYED')";
				$rows=$dbhu->do($sql);
				$added_camp=1;
				$sql="select tracking_id from advertiser_tracking where advertiser_id=? and client_id=? and daily_deal='N'"; 
				my $sth1=$dbhu->prepare($sql);
				$sth1->execute($adv_id,$client_id);
				if (($tracking_id)=$sth1->fetchrow_array())
				{
				}
				else
				{
					$util->genLinks($dbhu,$adv_id,0);
				}
				$sth1->finish();
			}
		}
		$STHQ1->finish();
	}
	$STHQ->finish();
}

sub logAction
{
	my ($user_id,$unq_id,$action)=@_;
	my $sql="insert into UniqueCampaignLog(unq_id,user_id,logDate,action) values($unq_id,$user_id,now(),'$action')";
	my $rows=$dbhu->do($sql);
}
