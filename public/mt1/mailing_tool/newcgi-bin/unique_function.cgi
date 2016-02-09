#!/usr/bin/perl
#===============================================================================
# Name   : unique_function.cgi - Performs functions on unique campaigns 
#
#--Change Control---------------------------------------------------------------
# 07/22/08  Jim Sobeck  Creation
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
my $adv_id;
my $nl_id;
my $dbid;

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}
#------  connect to the util database -----------
my ($dbhq,$dbhu)=$util->get_dbh();
my $uid=$query->param('uid');
my $f=$query->param('f');
if ($f eq "resume")
{
	$sql="select dbID from campaign where id=? limit 1";
	$sth=$dbhu->prepare($sql);
	$sth->execute($uid);
	($dbid)=$sth->fetchrow_array();
	$sth->finish();

	logAction($user_id,$uid,"Resumed");
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
	print "Location: /cgi-bin/unique_deploy_list.cgi\n\n";
}
elsif ($f eq "pause")
{
	logAction($user_id,$uid,"Paused");
	$sql="update unique_campaign set status='PAUSED',pause_flag=1 where unq_id=$uid and status in ('PENDING','START','PRE-PULLING','INJECTING')";
	$rows=$dbhu->do($sql);
	$sql="update UniqueHotmailChunk set pause_flag=1 where unq_id=$uid";
	$rows=$dbhu->do($sql);
	print "Location: /cgi-bin/unique_resume.cgi?uid=$uid\n\n";
}
elsif ($f eq "pauseall")
{
	$sql="select unq_id from unique_campaign uc, IpGroup ip where uc.slot_type in ('Hotmail','Chunking') and uc.send_date=curdate() and uc.status in ('PENDING','START','PRE-PULLING','INJECTING') and uc.group_id=ip.group_id and ip.goodmail_enabled='N' and ip.status='Active' and ip.group_name not like 'discover%' and ip.group_name not like 'credithelpadvisor%'";
	$sth=$dbhu->prepare($sql);
	$sth->execute();
	while (($uid)=$sth->fetchrow_array())
	{
		$sql="update UniqueHotmailChunk set pause_flag=1 where unq_id=$uid";
		$rows=$dbhu->do($sql);
		logAction($user_id,$uid,"Paused");
		$sql="update unique_campaign set status='PAUSED',pause_flag=1 where unq_id=$uid and status in ('PENDING','START','PRE-PULLING','INJECTING')";
		$rows=$dbhu->do($sql);
	}
	$sth->finish();
	print "Location: /cgi-bin/unique_resumeall.cgi\n\n";
}
elsif ($f eq "cancel")
{
	logAction($user_id,$uid,"Cancelled");
	$sql="update unique_campaign set status='CANCELLED',pid=0,end_time=now(),cancel_reason='Manually Cancelled' where unq_id=$uid and status in ('PENDING','START','PAUSED','PRE-PULLING','INJECTING','SLEEPING')";
	$rows=$dbhu->do($sql);
	$sql="select campaign_id from campaign where id=?";
	$sth=$dbhu->prepare($sql);
	$sth->execute($uid);
	my $cid;
	while (($cid)=$sth->fetchrow_array())
	{
		$sql="delete from current_campaigns where campaign_id=$cid"; 
		$rows=$dbhu->do($sql);
		$sql="update campaign set deleted_date=now(),sent_datetime=now() where campaign_id=$cid"; 
		$rows=$dbhu->do($sql);
	}
	$sth->finish();
	print "Location: /cgi-bin/unique_deploy_list.cgi\n\n";
}
elsif ($f eq "redeploy")
{
	$sql="insert into unique_campaign(campaign_name,campaign_type,status,send_date,campaign_id,email_addr,nl_id,mta_id,mailing_domain,mailing_ip,advertiser_id,creative_id,subject_id,from_id,mailing_template,include_wiki,include_name,random_subdomain,profile_id,templateID,send_time,group_id,client_group_id,header_id,footer_id,dup_client_group_id,dupes_flag,server_id,slot_type,hour_offset,log_campaign,use_master,pasted_domains,randomize_records,source_url,article_id,zip,mail_from,useRdns,newMailing,return_path,deployFileName,deployPoolID,deployPoolChunkID,injectorID,jlogProfileID,ConvertSubject,ConvertFrom) select campaign_name,'TEST','START',curdate(),campaign_id,email_addr,nl_id,mta_id,mailing_domain,mailing_ip,advertiser_id,creative_id,subject_id,from_id,mailing_template,include_wiki,include_name,random_subdomain,profile_id,templateID,send_time,group_id,client_group_id,header_id,footer_id,dup_client_group_id,dupes_flag,0,slot_type,hour_offset,log_campaign,use_master,pasted_domains,randomize_records,source_url,article_id,zip,mail_from,useRdns,newMailing,return_path,deployFileName,deployPoolID,deployPoolChunkID,injectorID,jlogProfileID,ConvertSubject,ConvertFrom from unique_campaign where unq_id=$uid";
	$rows=$dbhu->do($sql);
	logAction($user_id,$uid,"Redeployed");
	my $old_unq_id=$uid;
	$sql="select max(unq_id) from unique_campaign where campaign_name = (select campaign_name from unique_campaign where unq_id=$uid)";
	$sth=$dbhu->prepare($sql);
	$sth->execute();
	($uid)=$sth->fetchrow_array();
	$sth->finish();
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
	print "Location: /cgi-bin/unique_deploy.cgi?uid=$uid\n\n";
}

elsif ($f eq "restart")
{
	logAction($user_id,$uid,"Restarted");
	$sql="update unique_campaign set status = 'START' where unq_id=$uid";
	$dbhu->do($sql);

	$sql="update campaign set deleted_date=null where id=$uid";
	$dbhu->do($sql);
	
	print "Location: /cgi-bin/unique_deploy_list.cgi\n\n";
}
elsif ($f eq "redeliver")
{
	logAction($user_id,$uid,"Redeliver");
	$sql="update unique_campaign set status = 'START',reDeliver='Y' where unq_id=$uid";
	$dbhu->do($sql);

#	$sql="update campaign set dbID = '' where id=$uid";
#	$dbhu->do($sql);
	
	print "Location: /cgi-bin/unique_deploy_list.cgi\n\n";
}

sub logAction
{
    my ($user_id,$unq_id,$action)=@_;
    my $sql="insert into UniqueCampaignLog(unq_id,user_id,logDate,action) values($unq_id,$user_id,now(),'$action')";
    my $rows=$dbhu->do($sql);
}
