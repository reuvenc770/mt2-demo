#!/usr/bin/perl
#===============================================================================
# File   : upload_slot_schedule.cgi 
#
#--Change Control---------------------------------------------------------------
#===============================================================================

#-----------------------
# include Perl Modules
#-----------------------
use strict;
use CGI;
use util;
use Date::Manip;

$|=1 ;   # set OUTPUT_AUTOFLUSH to true

my $util = util->new;
my $query = CGI->new;
my $dbh;
my $rows;
my $sql;
my $sth;
my $cnt;
my $nl_id=14;
my $BRAND;
my $alt_light_table_bg = $util->get_alt_light_table_bg;
my $images = $util->get_images_url;
my $upload_dir_unix;


# ----- check for login -------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}
my $upload_file = $query->param('upload_file');
my $ttype= $query->param('utype');
my ($dbhq,$dbhu)=$util->get_dbh();

if ( $upload_file ne "" ) 
{
	&process_file() ;
}
else
{
	print "Location: unique_schedule.cgi\n\n";
}
exit(0) ;


sub process_file 
{
	my ($file_name, $file_handle, $file_problem, $file_in, $line, @rest_of_line);
	my ($sdate,$slot_id,$ip_group,$client_group_id,$profile_id,$usa);
	my ($usa_id,$adv_id,$advertiser,$vendor_supp_list_id,$md5_suppression,$daycnt,$mlupd,$usaType,$cat_id,$countryCode);
	my $sid;
	my $t1;
	my @SUBJ;
	my @FROM;
	my @CREATIVE;
	my $uid;
	my $crid;
	my $fid;
	my $stime;
	my $etime;
	my $cgroup_id;
	my $group_id;
	my $useRdns;
	my $headerid=1;
	my $header_id=0;
	my $footer_id=0;
   	my ($client_group_id,$group_id,$profile_id,$template_id,$utype,$log_camp,$randomize,$mta_id,$surl,$zip,$mail_from,$use_master,$return_path,$prepull,$ConvertSubject,$ConvertFrom,$jlogProfileID);
	my $chour;
	my $domainid;
	my $CLIENTGROUP;
	my $tprofile_id;

	print "Content-type: text/html\n\n";
	print<<"end_of_html";
<html><head><title>Unique Slot Upload Results</title></head>
<body>
<center>
<table>
end_of_html
	# get upload subdir
	$sql = "select parmval from sysparm where parmkey = 'UPLOAD_DIR_UNIX'";
	my $sth1 = $dbhq->prepare($sql) ;
	$sth1->execute();
	($upload_dir_unix) = $sth1->fetchrow_array();
	$sth1->finish();

	# deal with filename passed to this script
	if ( $upload_file =~ /([^\/\\]+)$/ ) 
	{
		$file_name = $1;                # set file_name to $1 var - (file-name no path)
		$file_name =~ s/^\.+//;         # say what...
		$file_name =~ s/\s/_/g;         # replace WhiteSpace with UnderScore global
		$file_handle = $upload_file ;
	}
	else 
	{
		$file_problem = $query->param('upfile');
		&error("Bad File Name: $file_problem, File name can't have a slash in it!\n Rename it and try again!" ) ;
		exit(0);
	}

	#---- Open file and save File to Unix box ---------------------------

	$file_in = "${upload_dir_unix}slot.${user_id}" ;
	open(SAVED,">$file_in") || &logerror("Error - could NOT open Output SAVED file: $file_in");
	$file_handle = $upload_file ;
	print SAVED <$file_handle> ;
	close SAVED;

    my ($sec, $min, $hr, $day, $month, $year, $wkdy, $yrdy, $isDST)=localtime();
    $month+=1; $year+=1900;
	open(LOG,">>/tmp/upload_slot_$month$day$year.log");
	print LOG "$hr:$sec - $user_id\n";
	open(SAVED,"<$file_in") || &logerror("Error - could NOT open Input SAVED file: $file_in");
	while (<SAVED>) 
	{
		chomp;                       # remove Carriage Return (if exists)
		$line = $_;
		$line =~ s///g ;      # remove ^M from Email Addr (if exists)
		$line =~ s/\t/|/g ;
		$line =~ s/,/|/g ;
		print LOG "$hr:$sec - $user_id - <$line>\n";
		($sdate,$slot_id,$ip_group,$client_group_id,$profile_id,$usa,@rest_of_line) = split('\|', $line) ;
		my $tdate=UnixDate($sdate,"%Y-%m-%d");
		if ($tdate eq "")
		{
			print "<tr><td><font color=red>Slot $slot_id not scheduled because invalid date: $sdate</font></td></tr>\n";
				next;
		}
		else
		{
			$sdate=$tdate;
		}
    	$sql="select client_group_id,ip_group_id,schedule_time,profile_id,template_id,slot_type,hour_offset,log_campaign,randomize_records,mta_id,source_url,zip,mail_from,use_master,return_path,prepull,ConvertSubject,ConvertFrom,jlogProfileID,mailing_domain,useRdns,end_time from UniqueSlot where slot_id=? and status='A'";
    	$sth=$dbhu->prepare($sql);
    	$sth->execute($slot_id);
    	if (($client_group_id,$group_id,$stime,$profile_id,$template_id,$utype,$chour,$log_camp,$randomize,$mta_id,$surl,$zip,$mail_from,$use_master,$return_path,$prepull,$ConvertSubject,$ConvertFrom,$jlogProfileID,$domainid,$useRdns,$etime)=$sth->fetchrow_array())
		{
    		$sth->finish();
		}
		else
		{
    		$sth->finish();
			print "<tr><td><font color=red>Slot $slot_id - $sdate not added because couldn't find Active Unique Slot</font></td></tr>\n";
			next;
		}
        my $i=0;
        while ($i <= $#SUBJ)
        {
            $SUBJ[$i]='';
            $i++;
        }
        $i=0;
        while ($i <= $#FROM)
        {
            $FROM[$i]='';
            $i++;
        }
        $i=0;
        while ($i <= $#CREATIVE)
        {
            $CREATIVE[$i]='';
            $i++;
		}
		if ($usa ne "")
		{
			$sql="select usa.usa_id,usa.advertiser_id,ai.advertiser_name,ai.vendor_supp_list_id,md5_suppression,datediff(curdate(),md5_last_updated),md5_last_updated,usa.usaType,ai.category_id,countryCode from UniqueScheduleAdvertiser usa, advertiser_info ai,Country c where usa.advertiser_id=ai.advertiser_id and ai.status!='I' and usa.name=? and ai.countryID=c.countryID order by usa.usa_id asc limit 1"; 
			$sth=$dbhu->prepare($sql);
			$sth->execute($usa);
			if (($usa_id,$adv_id,$advertiser,$vendor_supp_list_id,$md5_suppression,$daycnt,$mlupd,$usaType,$cat_id,$countryCode)=$sth->fetchrow_array())
			{
				$sth->finish();
				my $i;
				$i=0;
				$sql="select uas.subject_id from UniqueAdvertiserSubject uas,advertiser_subject as1 where usa_id=? and uas.subject_id=as1.subject_id and as1.advertiser_id=? and as1.status='A' order by rowID";
				$sth=$dbhu->prepare($sql);
				$sth->execute($usa_id,$adv_id);
				while (($t1)=$sth->fetchrow_array())
				{
					$SUBJ[$i]=$t1;
					$i++;
				}
				$sth->finish();
				$i=0;
				$sql="select uaf.from_id from UniqueAdvertiserFrom uaf,advertiser_from af where usa_id=? and uaf.from_id=af.from_id and af.advertiser_id=? and af.status='A' order by rowID";
				$sth=$dbhu->prepare($sql);
				$sth->execute($usa_id,$adv_id);
				while (($t1)=$sth->fetchrow_array())
				{
					$FROM[$i]=$t1;
					$i++;
				}
				$sth->finish();
				$i=0;
				$sql="select uac.creative_id from UniqueAdvertiserCreative uac,creative c where usa_id=? and uac.creative_id=c.creative_id and c.advertiser_id=? and c.status='A' order by rowID";
				$sth=$dbhu->prepare($sql);
				$sth->execute($usa_id,$adv_id);
				while (($t1)=$sth->fetchrow_array())
				{
					$CREATIVE[$i]=$t1;
					$i++;
				}
				$sth->finish();
			}
			else
			{
				$sth->finish();
				print "<tr><td><font color=red>Slot $slot_id - $sdate not added because couldn't find Active Unique Schedule Advertiser $usa</font></td></tr>\n";
				next;
			}
		}
		else
		{
			print "<tr><td><font color=red>Slot $slot_id - $sdate not scheduled because Unique Schedule Advertiser is blank</font></td></tr>\n";
			next;
		}

		$sql="select client_group_id from ClientGroup where client_group_id=? and status='A'";
		$sth=$dbhu->prepare($sql);
		$sth->execute($client_group_id);
		if (($cgroup_id)=$sth->fetchrow_array())
		{
			$sth->finish();
			my $tcnt;
			$sql="select count(*) from ClientGroupClients cgc, user where cgc.client_group_id=? and cgc.client_id=user.user_id and user.status='A'";
			$sth=$dbhu->prepare($sql);
			$sth->execute($cgroup_id);
			($tcnt)=$sth->fetchrow_array();
			$sth->finish();
			if ($tcnt > 0)
			{
				$CLIENTGROUP->{$client_group_id}=$cgroup_id;
			}
			else
			{
				print "<tr><td><font color=red>Slot $slot_id - $sdate not scheduled because couldn't find any active clients for Client Group $client_group_id</font></td></tr>\n";
				next;
			}
		}
		else
		{
			$sth->finish();
			print "<tr><td><font color=red>Slot $slot_id - $sdate not scheduled because couldn't find Active Client Group $client_group_id</font></td></tr>\n";
			next;
		}
		$sql="select profile_id from UniqueProfile where profile_id=? and status='A'";
		$sth=$dbhu->prepare($sql);
		$sth->execute($profile_id);
		if (($tprofile_id)=$sth->fetchrow_array())
		{
			$sth->finish();
		}
		else
		{
			$sth->finish();
			print "<tr><td><font color=red>Slot $slot_id - $sdate not scheduled because couldn't find Active Profile $profile_id</font></td></tr>\n";
			next;
		}
		my $mta_name;
		$sql="select name from mta where mta_id=?";
		$sth=$dbhu->prepare($sql);
		$sth->execute($mta_id);
		if (($mta_name)=$sth->fetchrow_array())
		{
			$sth->finish();
		}
		else
		{
			$sth->finish();
			print "<tr><td><font color=red>Slot $slot_id - $sdate not scheduled because couldn't find MTA Setting $mta_id</font></td></tr>\n";
			next;
		}
		my $exflag;
		$sql="select substr(exclude_days,dayofweek(date_add('$sdate',interval 6 day)),1) from advertiser_info where advertiser_id=?";;
		$sth=$dbhu->prepare($sql);
		$sth->execute($adv_id);
		($exflag)=$sth->fetchrow_array();
		$sth->finish();
		if ($exflag eq "Y")
		{
			print "<tr><td><font color=red>Slot $slot_id - $sdate not scheduled because Advertiser $advertiser cannot be scheduled on specified day.</font></td></tr>\n";
			next;
		}
		# Check to make sure suppression specified
		if ($md5_suppression eq "Y")
		{
			if (($daycnt > 10) or ($mlupd eq ""))
			{
				if ($countryCode eq "US")
				{
					print "<tr><td><font color=red>Slot $slot_id - $sdate not added because Suppression List has not been updated in over 10 days for Advertiser $advertiser</font></td></tr>\n";
					next;
				}
			}
		}
		else
		{
		if ($vendor_supp_list_id <= 1)
		{
			if ($countryCode eq "US")
			{
				print "<tr><td><font color=red>Slot $slot_id - $sdate not added because no Suppression List specified for Advertiser $advertiser, country=US</font></td></tr>\n";
				next;
			}
		}
		else
		{
			if ($countryCode eq "US")
			{
				my $lupd;
				my $ccnt;
				$sql="select datediff(curdate(),last_updated),last_updated from vendor_supp_list_info where list_id=?"; 
				$sth=$dbhu->prepare($sql);
				$sth->execute($vendor_supp_list_id);
				($ccnt,$lupd)=$sth->fetchrow_array();
				$sth->finish();
				if (($ccnt > 10) or ($lupd eq ""))
				{
					print "<tr><td><font color=red>Slot $slot_id - $sdate not added because Suppression List has not been updated in over 10 days for Advertiser $advertiser</font></td></tr>\n";
					next;
				}
			}
		}
		}
		($crid,$t1)=split('-',$CREATIVE[0]);
		$crid=~s/ //g;
		($sid,$t1)=split('-',$SUBJ[0]);
		$sid=~s/ //g;
		($fid,$t1)=split('-',$FROM[0]);
		$fid=~s/ //g;
		if (($crid eq "") or ($sid eq "") or ($fid eq ""))
		{
			print "<tr><td><font color=red>Slot $slot_id - $sdate not added because no Creative, Subject, and/or From</font></td></tr>\n";
			next;
		}

		if ($ttype eq "test")
		{
		}
		else
		{
    		$sql="insert into unique_campaign(campaign_type,status,campaign_id,email_addr,nl_id,mailing_domain,mailing_ip,campaign_name,advertiser_id,creative_id,subject_id,from_id,mailing_template,include_wiki,send_date,mta_id,include_name,random_subdomain,profile_id,templateID,group_id,client_group_id,send_time,header_id,footer_id,dup_client_group_id,dupes_flag,slot_type,hour_offset,log_campaign,randomize_records,source_url,zip,mail_from,use_master,useRdns,return_path,prepull,ConvertSubject,ConvertFrom,jlogProfileID,stop_time) values('DEPLOYED','START',0,'',$nl_id,'$domainid','','$advertiser',$adv_id,$crid,$sid,$fid,$template_id,'N','$sdate',$mta_id,'N','N',$profile_id,$headerid,$group_id,$client_group_id,'$stime',$header_id,$footer_id,0,'N/A','$utype',$chour,'$log_camp','$randomize','$surl','$zip','$mail_from','$use_master','$useRdns','$return_path','$prepull','$ConvertSubject','$ConvertFrom',$jlogProfileID,'$etime')";
			print LOG "$hr:$sec - $user_id - <$sql>\n";
			$rows=$dbhu->do($sql);
			$sql="select LAST_INSERT_ID()"; 
			$sth=$dbhu->prepare($sql);
			$sth->execute();
			($uid)=$sth->fetchrow_array();
			$sth->finish();
			if ($uid eq "")
			{
				print "<tr><td><font color=red>Slot $slot_id - $sdate not added because error in line, maybe special character in a field.</font></td></tr>\n";
				next;
			}
        	$sql="insert into UniqueSchedule(slot_id,unq_id) values($slot_id,$uid)";
        	$rows=$dbhu->do($sql);

			my $i=0;
			while ($i <= $#CREATIVE)
			{
				if ($CREATIVE[$i] ne "")
				{
					($crid,$t1)=split('-',$CREATIVE[$i]);
					$crid=~s/ //g;
					my $rowID=0;
					if ($usaType eq "Combination")
					{
						$rowID=$i+1;
					}
					$sql="insert into UniqueCreative(unq_id,creative_id,rowID) values($uid,$crid,$rowID)";
					$rows=$dbhu->do($sql);
					print LOG "$hr:$sec - $user_id - <$sql>\n";
				}
				$i++;
			}
			$i=0;
			while ($i <= $#SUBJ)
			{
				if ($SUBJ[$i] ne "")
				{
					($sid,$t1)=split('-',$SUBJ[$i]);
					$sid=~s/ //g;
					my $rowID=0;
					if ($usaType eq "Combination")
					{
						$rowID=$i+1;
					}
					$sql="insert into UniqueSubject(unq_id,subject_id,rowID) values($uid,$sid,$rowID)";
					$rows=$dbhu->do($sql);
					print LOG "$hr:$sec - $user_id - <$sql>\n";
				}
				$i++;
			}
			$i=0;
			while ($i <= $#FROM)
			{
				if ($FROM[$i] ne "")
				{
					($fid,$t1)=split('-',$FROM[$i]);
					$fid=~s/ //g;
					my $rowID=0;
					if ($usaType eq "Combination")
					{
						$rowID=$i+1;
					}
					$sql="insert into UniqueFrom(unq_id,from_id,rowID) values($uid,$fid,$rowID)";
					$rows=$dbhu->do($sql);
					print LOG "$hr:$sec - $user_id - <$sql>\n";
				}
				$i++;
			}
		    $sql="insert ignore into UniqueDomain(unq_id,mailing_domain) select $uid,mailing_domain from UniqueSlotDomain where slot_id=$slot_id";
    		$rows=$dbhu->do($sql);	
		    $sql="insert ignore into UniqueContentDomain(unq_id,domain_name) select $uid,domain_name from UniqueSlotContentDomain where slot_id=$slot_id";
    		$rows=$dbhu->do($sql);	
			add_campaigns($uid,$advertiser,$profile_id,$sdate,$stime,$cgroup_id,$nl_id,$adv_id,"Off",$group_id);
		}
		print "<tr><td>Slot $slot_id - $sdate $usa added. </td></tr>\n";
	} 
	close SAVED;
	close LOG;
	unlink($file_in) || &logerror("Error - could NOT Remove file: $file_in");  # del file_in
	print<<"end_of_html";
</table>
<br><a href="unique_schedule.cgi">Back to Unique Schedule</a>
</center>
</body>
</html>
end_of_html
} # end of sub

sub add_campaigns
{
	my ($tid,$cname,$profile_id,$sdate,$stime,$cgroupid,$nl_id,$adv_id,$log_camp,$ipgroup_id)=@_;
	my $sql;
	my $profile_name;
	my $client_id;
	my $brand_id;
	my $camp_id;
	my $cdate;
	my $added_camp;
	my $tracking_id;
	my $sth;

	my $cnt;
	my $priority;

	$sql="select count(*) from IpGroup ip where ip.status='Active' and ip.group_id=? and (ip.goodmail_enabled='Y' or ip.group_name like 'discover%' or ip.group_name like 'credithelpadvisor%')";
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

	$sql="select client_id from ClientGroupClients cgc,user u where client_group_id=? and cgc.client_id=u.user_id and u.status='A'";
	$STHQ=$dbhq->prepare($sql);
	$STHQ->execute($cgroupid);
	while (($client_id) = $STHQ->fetchrow_array())
	{
		if ($BRAND->{$client_id})
		{
			$brand_id=$BRAND->{$client_id};
		}
		else
		{
		$sql="select brand_id from client_brand_info where client_id=? and nl_id=? and status='A' and brand_type='Newsletter'";
		my $STHQ1=$dbhq->prepare($sql);
		$STHQ1->execute($client_id,$nl_id);
		if (($brand_id) = $STHQ1->fetchrow_array())
		{
			$BRAND->{$client_id}=$brand_id;
		}
		else
		{
			$brand_id="";
		}
		$STHQ1->finish();
		}

		if ($brand_id ne "")
		{	
			my $timestr=$sdate." ".$stime;
			$sql = "insert into campaign(user_id,campaign_name,status,created_datetime,scheduled_datetime,advertiser_id,profile_id,brand_id,scheduled_date,scheduled_time,campaign_type,id) values($client_id,'$cname','C',now(),'$timestr',$adv_id,$profile_id,$brand_id,'$sdate','$stime','NEWSLETTER','$tid')";
			$rows=$dbhu->do($sql);
			$sql="select LAST_INSERT_ID()"; 
#			$sql = "select max(campaign_id) from campaign where campaign_name='$cname' and scheduled_date='$sdate' and id='$tid' and advertiser_id=$adv_id and profile_id=$profile_id and brand_id=$brand_id";
			$sth = $dbhq->prepare($sql);
			$sth->execute();
			($camp_id) = $sth->fetchrow_array();
			$sth->finish();
#		   	$sql="insert into campaign_log(campaign_id,date_sent,user_id) values($camp_id,curdate(),$client_id)";
#		   	$rows=$dbhu->do($sql);
			if (($sdate eq $cdate) and ($added_camp == 0))
			{
#				$sql="insert into current_campaigns(campaign_id,scheduled_date,scheduled_time,campaign_type) values($camp_id,curdate(),'$stime','DEPLOYED')";
#				$rows=$dbhu->do($sql);
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
	}
	$STHQ->finish();
}
