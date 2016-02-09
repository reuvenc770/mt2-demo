#!/usr/bin/perl
#===============================================================================
# File   : upload_unique_save.cgi 
#
#--Change Control---------------------------------------------------------------
#===============================================================================

#-----------------------
# include Perl Modules
#-----------------------
use strict;
use CGI;
use Lib::Database::Perl::Interface::Server;
use Net::FTP;
use util;
use Date::Manip;

$|=1 ;   # set OUTPUT_AUTOFLUSH to true

my $util = util->new;
my $query = CGI->new;
my $dbh;
my $rows;
my $sql;
my $cnt;
my $md5_suppression;
my $daycnt;
my $NLID;
my $CLIENTGROUP;
my $BRAND;
my $PROF;
my $MTA;
my $TEMPLATE;
my $INJ;
my $mlupd;
my $usaType;
my $alt_light_table_bg = $util->get_alt_light_table_bg;
my $images = $util->get_images_url;
my $ConvertSubject;
my $ConvertFrom;
my $adrevolutionDeploy;
my @ENC=("None","ISO","UTF8-Q","UTF8-B","UTF-7","ASCII","UTF-16","UTF-8","UTF-8 HOT","ISO B","UTF-8Q HOT","UTF-7 HOT","ASCII HOT","UTF-16 HOT","ISO B HOT","ISO Q HOT");
my @ENC1=("","ISO","UTF8","UTF8-B","UTF-7","ASCII","UTF-16","UTF8","UTF-8 HOT","ISO B","UTF-8Q HOT","UTF-7 HOT","ASCII HOT","UTF-16 HOT","ISO B HOT","ISO Q HOT");


# ------- Get fields from html Form post -----------------


# ----- check for login -------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}
my $upload_file = $query->param('upload_file');
my $utype = $query->param('utype');
my ($dbhq,$dbhu)=$util->get_dbh();
my $serverInterface     = Lib::Database::Perl::Interface::Server->new(('write' => 1));
my $errors;
my $results;
my $params;
($errors, $results) = $serverInterface->getDedicatedInjectors($params);
my $cnt=$#{$results};
my $i=0;
while ($i <= $cnt)
{
    my $server_id=$results->[$i]->{'serverID'};
    my $sname=$results->[$i]->{'hostname'};
    $sname=~s/.pcposts.com//;
    $sname=~s/.i.routename.com//;
    $sname=~s/.routename.com//;
	$INJ->{$sname}=$server_id;
	$i++;
}

#----- Pass control to PROCESS_FILE  or  PROCESS_LIST  -------
if ( $upload_file ne "" ) 
{
	&process_file() ;
}
else
{
	print "Location: unique_list.cgi\n\n";
}
exit(0) ;


sub process_file 
{
	my ($file_name, $file_handle, $file_problem, $file_in, $line, @rest_of_line);
	my $gname;
	my $ip;
	my $chunkval;
	my $old_gname;
	my $group_id;
	my $cgroup_id;
	my $profile_id;
	my $mta_id;
	my $template_id;
	my $colo;
	my $sql;
	my $sth;
	my $sth1;
	my @MDOM;
	my @CMDOM;
	my $slot_id;
	my $upload_dir_unix;
	my $nl_id;
	my $dup_client_group_id;
	my $adv_id;
	my $aname;
	my $cat_id;
	my $vendor_supp_list_id;
	my $uid;
	my $countryCode;
	my ($deploy_name,$sdate,$ctime,$nl,$domain,$ipgroup,$cgroup,$ipgroup,$profile,$ctype,$randomize,$dup_client_group,$mailing_type,$mta_setting,$source_url,$advertiser,$creative,$from,$subject,$include_name,$use_master,$template,$article,$header,$footer,$include_wiki,$insert_random_subdomain,@rest_of_line);
	my $attributed_client;
	my $CutMail;
	my $attributed_client_id;
	my $etime;
	my $cdomain;
	my $deployFileName;
	my $injectorName;
	my $injectorID;
	my $deployPoolName;
	my $jlogProfileName;
	my $jlogProfileID;
	my $deployPoolID;
	my $deployPoolChunkID;
	my $zip;
	my $prepull;
	my $mailfrom;
	my $useRdns;
	my $usaName;
	my $randomType;
	my $trace_header_id;
	my $newMailing;
	my $return_path;
	my $usa_id;
	my $crid;
	my $sid;
	my $fid;
	my $t1;
	my @SUBJ;
	my @FROM;
	my @CREATIVE;
	my @TSUBJ;
	my @TFROM;
	my @TCREATIVE;
	my $USERS;

	print "Content-type: text/html\n\n";
	print<<"end_of_html";
<html><head><title>Unique Upload Results $utype</title></head>
<body>
<center>
<table>
end_of_html
	# get upload subdir
	$sql = "select parmval from sysparm where parmkey = 'UPLOAD_DIR_UNIX'";
	$sth1 = $dbhq->prepare($sql) ;
	$sth1->execute();
	($upload_dir_unix) = $sth1->fetchrow_array();
	$sth1->finish();

	$sql="select user_id,first_name from user";
	$sth1 = $dbhq->prepare($sql) ;
	$sth1->execute();
	my $tuid;
	my $fname;
	while (($tuid,$fname) = $sth1->fetchrow_array())
	{
		$USERS->{$tuid}=$fname;
	}
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

	$file_in = "${upload_dir_unix}unique.${user_id}" ;
	open(SAVED,">$file_in") || &logerror("Error - could NOT open Output SAVED file: $file_in");
	$file_handle = $upload_file ;
	print SAVED <$file_handle> ;
	close SAVED;

    my ($sec, $min, $hr, $day, $month, $year, $wkdy, $yrdy, $isDST)=localtime();
    $month+=1; $year+=1900;
	open(LOG,">>/tmp/upload_unique_$month$day$year.log");
	print LOG "$hr:$sec - $user_id\n";
	open(SAVED,"<$file_in") || &logerror("Error - could NOT open Input SAVED file: $file_in");
	$old_gname="";
	while (<SAVED>) 
	{
		chomp;                       # remove Carriage Return (if exists)
		$line = $_;
		$line =~ s///g ;      # remove ^M from Email Addr (if exists)
		$line =~ s/\t/|/g ;
		$line =~ s/,/|/g ;
		print LOG "$hr:$sec - $user_id - <$line>\n";
		($deploy_name,$sdate,$ctime,$etime,$nl,$domain,$ipgroup,$cgroup,$profile,$ctype,$randomize,$dup_client_group,$mailing_type,$mta_setting,$source_url,$advertiser,$creative,$from,$subject,$include_name,$use_master,$template,$article,$header,$footer,$include_wiki,$insert_random_subdomain,$zip,$prepull,$mailfrom,$useRdns,$usaName,$trace_header_id,$newMailing,$return_path,$deployPoolName,$deployPoolChunkID,$jlogProfileName,$deployFileName,$injectorName,$ConvertSubject,$ConvertFrom,$adrevolutionDeploy,$cdomain,$attributed_client,$CutMail,@rest_of_line) = split('\|', $line) ;
		$aname=$advertiser;
		$randomType='';
		if ($adrevolutionDeploy eq "Y")
		{
			$adrevolutionDeploy=14358;
		}
		elsif ($adrevolutionDeploy eq "N")
		{
			$adrevolutionDeploy=0;
		}
		if ($deploy_name eq "Name")
		{
			next;
		}
		if ($source_url eq "")
		{
			$source_url="ALL";
		}
		my $tdate=UnixDate($sdate,"%Y-%m-%d");
		if ($tdate eq "")
		{
			print "<tr><td><font color=red>Deploy $deploy_name not added because invalid date: $sdate</font></td></tr>\n";
				next;
		}
		else
		{
			$sdate=$tdate;
		}
		if ($deployPoolChunkID eq "")
		{
			$deployPoolChunkID=0;
		}
		if ($deployPoolName eq "")
		{
			$deployPoolID=0;
			$deployPoolChunkID=0;
		}
		else
		{
			my $maxPoolID;
			$sql="select deployPoolID,totalChunks from DeployPool where deployPoolName=? and status='Active'";
			my $sthq=$dbhu->prepare($sql);
			$sthq->execute($deployPoolName);
			if (($deployPoolID,$maxPoolID)=$sthq->fetchrow_array())
			{
			}
			else
			{
				print "<tr><td><font color=red>Deploy $deploy_name not added because couldn't find Deploy Pool Name $deployPoolName</font></td></tr>\n";
				next;
			}
			if (($deployPoolChunkID > $maxPoolID) or ($deployPoolChunkID == 0))
			{
				print "<tr><td><font color=red>Deploy $deploy_name not added because specified Pool Chunk ID is zero or greater than max Chunk ID $maxPoolID for Deploy Pool Name $deployPoolName</font></td></tr>\n";
				next;
			}
		}
		if ($deployFileName ne "")
		{
			my $gotfile=0;
			my $host = "ftp.aspiremail.com";
			my $ftp = Net::FTP->new("$host", Timeout => 20, Debug => 0, Passive => 0) or print "Cannot connect to $host: $@\n";
			if ($ftp)
			{
    			$ftp->login('deploysByEID','24vision') or print "Cannot login ", $ftp->message;
				foreach my $file($ftp->ls)
				{
					if ($file eq $deployFileName)
					{
						$gotfile=1;
					}
				}
    			$ftp->quit;
			}
			if (!$gotfile)
			{
				print "<tr><td><font color=red>Deploy $deploy_name not added because couldnt find EID file $deployFileName</font></td></tr>\n";
				next;
			}
		}
		my $i=0;
		my $gotIt=0;
		while ($i <= $#ENC)
		{
			if ($ENC[$i] eq $ConvertFrom)
			{
				$ConvertFrom=$ENC1[$i];
				$gotIt=1;
			}
			$i++;
		}
		if (!$gotIt)
		{
			$ConvertFrom="None";
		}
		$i=0;
		$gotIt=0;
		while ($i <= $#ENC)
		{
			if ($ENC[$i] eq $ConvertSubject)
			{
				$ConvertSubject=$ENC1[$i];
				$gotIt=1;
			}
			$i++;
		}
		if (!$gotIt)
		{
			$ConvertSubject="None";
		}
		if (($injectorName eq "") or ($injectorName eq "0"))
		{
			$injectorID=0;
		}	
		elsif ($INJ->{$injectorName})
		{
			$injectorID=$INJ->{$injectorName};
		}
		else
		{
			print "<tr><td><font color=red>Deploy $deploy_name not added because injector $injectorName is not a dedicated injector</font></td></tr>\n";
			next;
		}
		if (($jlogProfileName eq "") or ($jlogProfileName eq "0"))
		{
			$jlogProfileID=0;
		}
		else
		{
			$sql="select profileID from EmailEventHandlerProfile where profileName=?";
			my $sthq=$dbhu->prepare($sql);
			$sthq->execute($jlogProfileName);
			if (($jlogProfileID)=$sthq->fetchrow_array())
			{
			}
			else
			{
				print "<tr><td><font color=red>Deploy $deploy_name not added because couldn't find Jlog Profile $jlogProfileName</font></td></tr>\n";
				next;
			}
		}
		if ($adrevolutionDeploy != 0) 
		{
			if ($adrevolutionDeploy >= 14358 and $adrevolutionDeploy <= 14362) 
			{
			}
			elsif ($adrevolutionDeploy >= 16159 and $adrevolutionDeploy <= 16188) 
			{
			}
			elsif ($adrevolutionDeploy == 16194)
			{
			}
			else
			{	
				print "<tr><td><font color=red>Deploy $deploy_name not added because invalid adknowledge listID: $adrevolutionDeploy</font></td></tr>\n";
				next;
			}
		}
		if ($trace_header_id eq "")
		{
			$trace_header_id=0;
		}
		if ($newMailing eq "")
		{
			$newMailing="N";
		}
		if ($useRdns eq "")
		{
			$useRdns="N";
		}
		if ($randomize eq "")
		{
			$randomize="Y";
		}
		if ($zip eq "")
		{
			$zip="ALL";
		}
		if ($prepull eq "")
		{
			$prepull="N";
		}
		$deploy_name=~s/'/''/g;
		$ipgroup=~s/'/''/g;
		$cgroup=~s/'/''/g;
		$profile=~s/'/''/g;
		$mta_setting=~s/'/''/g;
		$nl=~s/'/''/g;
		my @TMDOM=split(' ',$domain);
		#
		# check to see if domain is active
		#
		my $dcnt=0;
		my $tcnt;
		@MDOM=();
		foreach my $d (@TMDOM)
		{
			$sql="select count(*) from Domain,DomainTypeJoin where Domain.domainName=? and Domain.active=1 and Domain.domainID=DomainTypeJoin.domainID and DomainTypeJoin.domainTypeID=1";
			my $sthq=$dbhu->prepare($sql);
			$sthq->execute($d);
			($tcnt)=$sthq->fetchrow_array();
			$sthq->finish();
			if ($tcnt > 0)
			{
				$MDOM[$dcnt]=$d;
				$dcnt++;
			}
		}
		if ($dcnt == 0)
		{
			if ($useRdns eq "Y")
			{
				$MDOM[0]='';
			}
			elsif ($ctype eq "Hotmail Domain")
			{
				$MDOM[0]='';
			}
			else
			{
				print "<tr><td><font color=red>Deploy $deploy_name not added because couldn't find any active Domains</font></td></tr>\n";
				next;
			}
		}
		if (($CutMail ne "Y") and ($CutMail ne "N"))
		{
			$CutMail="N";
		}
		$attributed_client_id=0;
		if ($attributed_client ne "")
		{
			$sql="select user_id from user where status='A' and username=?";
			my $sthq=$dbhu->prepare($sql);
			$sthq->execute($attributed_client);
			if (($attributed_client_id)=$sthq->fetchrow_array())
			{
				$sthq->finish();
			}
			else
			{
				$sthq->finish();
				print "<tr><td><font color=red>Deploy $deploy_name not added because couldn't find attributed client $attributed_client</font></td></tr>\n";
				next;
			}
		}
		my @TMDOM=split(' ',$cdomain);
		#
		# check to see if domain is active
		#
		my $dcnt=0;
		my $tcnt;
		@CMDOM=();
		foreach my $d (@TMDOM)
		{
			$sql="select count(*) from Domain,DomainTypeJoin where Domain.domainName=? and Domain.active=1 and Domain.domainID=DomainTypeJoin.domainID and DomainTypeJoin.domainTypeID=1";
			my $sthq=$dbhu->prepare($sql);
			$sthq->execute($d);
			($tcnt)=$sthq->fetchrow_array();
			$sthq->finish();
			if ($tcnt > 0)
			{
				$CMDOM[$dcnt]=$d;
				$dcnt++;
			}
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
		if ($usaName ne "")
		{
			if (($usaName eq "RAND_RAND") or ($usaName eq "RAND_MAX") or ($usaName eq "RAND_MAX_15") or ($usaName eq "RAND_MAX_30"))
			{
				$adv_id=0;
				$aname=$usaName;
				$SUBJ[0]=0;
				$FROM[0]=0;
				$CREATIVE[0]=0;
				$md5_suppression="N";
				$vendor_supp_list_id=0;
				$countryCode="UK";
				$randomType=$usaName;
				$cat_id=0;
			}
			else
			{
				$sql="select usa.usa_id,usa.advertiser_id,ai.advertiser_name,ai.vendor_supp_list_id,md5_suppression,datediff(curdate(),md5_last_updated),md5_last_updated,usa.usaType,ai.category_id,countryCode from UniqueScheduleAdvertiser usa, advertiser_info ai,Country c where usa.advertiser_id=ai.advertiser_id and ai.status!='I' and usa.name=? and ai.countryID=c.countryID order by usa.usa_id asc limit 1"; 
				$sth=$dbhu->prepare($sql);
				$sth->execute($usaName);
				if (($usa_id,$adv_id,$aname,$vendor_supp_list_id,$md5_suppression,$daycnt,$mlupd,$usaType,$cat_id,$countryCode)=$sth->fetchrow_array())
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
					print "<tr><td><font color=red>Deploy $deploy_name not added because couldn't find Unique Schedule Advertiser $usaName</font></td></tr>\n";
					next;
				}
			}
		}
		else
		{
			@TSUBJ=split(' ',$subject);
			@TFROM=split(' ',$from);
			@TCREATIVE=split(' ',$creative);
		}

		if ($NLID->{$nl})
		{
			$nl_id=$NLID->{$nl};
		}
		else
		{
			$sql="select nl_id from newsletter where nl_name=?";
			$sth=$dbhu->prepare($sql);
			$sth->execute($nl);
			if (($nl_id)=$sth->fetchrow_array())
			{
				$sth->finish();
				$NLID->{$nl}=$nl_id;
			}
			else
			{
				$sth->finish();
				print "<tr><td><font color=red>Deploy $deploy_name not added because couldn't find newsletter $nl</font></td></tr>\n";
				next;
			}
		}
		$sql="select group_id from IpGroup where status='Active' and group_name=?";
		$sth=$dbhu->prepare($sql);
		$sth->execute($ipgroup);
		if (($group_id)=$sth->fetchrow_array())
		{
			$sth->finish();
		}
		else
		{
			$sth->finish();
			print "<tr><td><font color=red>Deploy $deploy_name not added because couldn't find Ip Group $ipgroup</font></td></tr>\n";
			next;
		}
		if ($CLIENTGROUP->{$cgroup})
		{
			$cgroup_id=$CLIENTGROUP->{$cgroup};
		}
		else
		{
			$sql="select client_group_id from ClientGroup where group_name=? and status='A'";
			$sth=$dbhu->prepare($sql);
			$sth->execute($cgroup);
			if (($cgroup_id)=$sth->fetchrow_array())
			{
				$sth->finish();
				$sql="select count(*) from ClientGroupClients cgc, user where cgc.client_group_id=? and cgc.client_id=user.user_id and user.status='A'";
				$sth=$dbhu->prepare($sql);
				$sth->execute($cgroup_id);
				($tcnt)=$sth->fetchrow_array();
				$sth->finish();
				if ($tcnt > 0)
				{
					$CLIENTGROUP->{$cgroup}=$cgroup_id;
				}
				else
				{
					print "<tr><td><font color=red>Deploy $deploy_name not added because couldn't find any active clients for Client Group $cgroup</font></td></tr>\n";
					next;
				}
			}
			else
			{
				$sth->finish();
				print "<tr><td><font color=red>Deploy $deploy_name not added because couldn't find Client Group $cgroup</font></td></tr>\n";
				next;
			}
		}
		if ($PROF->{$profile})
		{
			$profile_id=$PROF->{$profile};
		}
		else
		{
			$sql="select profile_id from UniqueProfile where profile_name=? and status='A'";
			$sth=$dbhu->prepare($sql);
			$sth->execute($profile);
			if (($profile_id)=$sth->fetchrow_array())
			{
				$sth->finish();
				$PROF->{$profile}=$profile_id;
			}
			else
			{
				$sth->finish();
				print "<tr><td><font color=red>Deploy $deploy_name not added because couldn't find Profile $profile</font></td></tr>\n";
				next;
			}
		}
		if ($mta_setting eq "")
		{
			print "<tr><td><font color=red>Deploy $deploy_name not added because no MTA Setting specified </font></td></tr>\n";
			next;
		}
		if ($MTA->{$mta_setting})
		{
			$mta_id=$MTA->{$mta_setting};
		}
		else
		{
			$sql="select mta_id from mta where name=?";
			$sth=$dbhu->prepare($sql);
			$sth->execute($mta_setting);
			if (($mta_id)=$sth->fetchrow_array())
			{
				$sth->finish();
				$MTA->{$mta_setting}=$mta_id;
			}
			else
			{
				$sth->finish();
				print "<tr><td><font color=red>Deploy $deploy_name not added because couldn't find MTA Setting $mta_setting</font></td></tr>\n";
				next;
			}
		}
		if ($TEMPLATE->{$template})
		{
			$template_id=$TEMPLATE->{$template};
		}
		else
		{
			$sql="select template_id from brand_template where template_name=?";
			$sth=$dbhu->prepare($sql);
			$sth->execute($template);
			if (($template_id)=$sth->fetchrow_array())
			{
				$sth->finish();
				$TEMPLATE->{$template}=$template_id;
			}
			else
			{
				$sth->finish();
				print "<tr><td><font color=red>Deploy $deploy_name not added because couldn't find Template $template</font></td></tr>\n";
				next;
			}
		}

		if ($usaName eq "")
		{
			$advertiser=~s/'/''/g;
			$sql="select advertiser_id,vendor_supp_list_id,md5_suppression,datediff(curdate(),md5_last_updated),md5_last_updated,countryCode,ai.category_id from advertiser_info ai,Country c where advertiser_name=? and status='A' and test_flag='N' and ai.countryID=c.countryID";
			$sth=$dbhu->prepare($sql);
			$sth->execute($advertiser);
			if (($adv_id,$vendor_supp_list_id,$md5_suppression,$daycnt,$mlupd,$countryCode,$cat_id)=$sth->fetchrow_array())
			{
				$sth->finish();
			}
			else
			{
				$sth->finish();
				print "<tr><td><font color=red>Deploy $deploy_name not added because couldn't find Advertiser $advertiser</font></td></tr>\n";
				next;
			}
			#
			# check assets
			#
			my $i=0;
			foreach my $c (@TCREATIVE)
			{
				my $tcnt;
				$sql="select count(*) from creative where advertiser_id=? and status='A' and creative_id=?";
				$sth=$dbhu->prepare($sql);
				$sth->execute($adv_id,$c);
				($tcnt)=$sth->fetchrow_array();
				$sth->finish();
				if ($tcnt > 0)
				{
					$CREATIVE[$i]=$c;
					$i++;
				}
				else
				{
					print "<tr><td><font color=red>Creative $c not added because for wrong advertiser or not active</font></td></tr>\n";
				}
			}
			$i=0;
			foreach my $c (@TSUBJ)
			{
				my $tcnt;
				$sql="select count(*) from advertiser_subject where advertiser_id=? and status='A' and subject_id=?";
				$sth=$dbhu->prepare($sql);
				$sth->execute($adv_id,$c);
				($tcnt)=$sth->fetchrow_array();
				$sth->finish();
				if ($tcnt > 0)
				{
					$SUBJ[$i]=$c;
					$i++;
				}
				else
				{
					print "<tr><td><font color=red>Subject $c not added because for wrong advertiser or not active</font></td></tr>\n";
				}
			}
			$i=0;
			foreach my $c (@TFROM)
			{
				my $tcnt;
				$sql="select count(*) from advertiser_from where advertiser_id=? and status='A' and from_id=?";
				$sth=$dbhu->prepare($sql);
				$sth->execute($adv_id,$c);
				($tcnt)=$sth->fetchrow_array();
				$sth->finish();
				if ($tcnt > 0)
				{
					$FROM[$i]=$c;
					$i++;
				}
				else
				{
					print "<tr><td><font color=red>From $c not added because for wrong advertiser or not active</font></td></tr>\n";
				}
			}
		} 
		#$sql="select cgc.client_id from client_category_exclusion,ClientGroupClients cgc where client_category_exclusion.client_id=cgc.client_id and client_category_exclusion.category_id=? and cgc.client_group_id=? union select cgc.client_id from client_advertiser_exclusion,ClientGroupClients cgc where client_advertiser_exclusion.client_id=cgc.client_id and client_advertiser_exclusion.advertiser_id=? and cgc.client_group_id=?";
		$sql="select cgc.client_id from client_category_exclusion,ClientGroupClients cgc where client_category_exclusion.client_id=cgc.client_id and client_category_exclusion.category_id=? and cgc.client_group_id=?"; 
		$sth=$dbhu->prepare($sql);
		$sth->execute($cat_id,$cgroup_id);
		my $tcid;
		while (($tcid)=$sth->fetchrow_array())
		{
			print "<tr><td><font color=red>Deploy $deploy_name - Client $USERS->{$tcid} ($tcid) will not be used because excluded by Category or Advertiser</font></td></tr>\n";
		}
		$sth->finish();

		my $exflag;
		$sql="select substr(exclude_days,dayofweek(date_add('$sdate',interval 6 day)),1) from advertiser_info where advertiser_id=?";;
		$sth=$dbhu->prepare($sql);
		$sth->execute($adv_id);
		($exflag)=$sth->fetchrow_array();
		$sth->finish();
		if ($exflag eq "Y")
		{
			print "<tr><td><font color=red>Deploy $deploy_name not added because Advertiser $aname cannot be scheduled on specified day.</font></td></tr>\n";
			next;
		}
		# Check to make sure suppression specified
		if ($md5_suppression eq "Y")
		{
			if (($daycnt > 10) or ($mlupd eq ""))
			{
				if ($countryCode eq "US")
				{
					print "<tr><td><font color=red>Deploy $deploy_name not added because Suppression List has not been updated in over 10 days for Advertiser $aname</font></td></tr>\n";
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
				print "<tr><td><font color=red>Deploy $deploy_name not added because no Suppression List specified for Advertiser $aname, country=US</font></td></tr>\n";
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
					print "<tr><td><font color=red>Deploy $deploy_name not added because Suppression List has not been updated in over 10 days for Advertiser $aname</font></td></tr>\n";
					next;
				}
			}
		}
		}
		if ($article eq "")
		{
			$article=0;
		}
		else
		{
			$sql="select article_id from article where article_name=?";
			$sth=$dbhu->prepare($sql);
			$sth->execute($article);
			if (($article)=$sth->fetchrow_array())
			{
				$sth->finish();
			}
			else
			{
				$sth->finish();
				print "<tr><td><font color=red>Deploy $deploy_name not added because couldn't find Article</font></td></tr>\n";
				next;
			}
		}
		if ($header eq "")
		{
			$header=0;
		}
		else
		{
			$sql="select header_id from Headers where header_name=?";
			$sth=$dbhu->prepare($sql);
			$sth->execute($header);
			if (($header)=$sth->fetchrow_array())
			{
				$sth->finish();
			}
			else
			{
				$sth->finish();
				print "<tr><td><font color=red>Deploy $deploy_name not added because couldn't find Header</font></td></tr>\n";
				next;
			}
		}
		if ($footer eq "")
		{
			$footer=0;
		}
		else
		{
			$sql="select footer_id from Footers where footer_name=?";
			$sth=$dbhu->prepare($sql);
			$sth->execute($header);
			if (($footer)=$sth->fetchrow_array())
			{
				$sth->finish();
			}
			else
			{
				$sth->finish();
				print "<tr><td><font color=red>Deploy $deploy_name not added because couldn't find Footer</font></td></tr>\n";
				next;
			}
		}
		if ($dup_client_group eq "N/A")
		{
			$dup_client_group_id=0;
		}
		else
		{
			$sql="select client_group_id from ClientGroup where group_name=? and status='A'";
			$sth=$dbhu->prepare($sql);
			$sth->execute($dup_client_group);
			if (($dup_client_group_id)=$sth->fetchrow_array())
			{
				$sth->finish();
			}
			else
			{
				$sth->finish();
				print "<tr><td><font color=red>Deploy $deploy_name not added because couldn't find Duplicate Client Group $dup_client_group</font></td></tr>\n";
				next;
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
			print "<tr><td><font color=red>Deploy $deploy_name not added because no Creative, Subject, and/or From for Advertiser $aname</font></td></tr>\n";
			next;
		}

		if ($utype eq "test")
		{
		}
		else
		{
			$sql="insert into unique_campaign(campaign_name,campaign_type,status,send_date,nl_id,mta_id,mailing_domain,advertiser_id,creative_id,subject_id,from_id,mailing_template,include_wiki,include_name,random_subdomain,profile_id,templateID,group_id,client_group_id,send_time,header_id,footer_id,dup_client_group_id,dupes_flag,slot_type,use_master,randomize_records,source_url,article_id,zip,prepull,mail_from,useRdns,trace_header_id,newMailing,return_path,deployPoolID,deployPoolChunkID,jlogProfileID,deployFileName,injectorID,ConvertSubject,ConvertFrom,adknowledgeDeploy,stop_time,RandomType,CutMail) values('$deploy_name','DEPLOYED','START','$sdate',$nl_id,$mta_id,'$MDOM[0]',$adv_id,$crid,$sid,$fid,$template_id,'$include_wiki','$include_name','$insert_random_subdomain',$profile_id,1,$group_id,$cgroup_id,'$ctime',$header,$footer,$dup_client_group_id,'$mailing_type','$ctype','$use_master','$randomize','$source_url',$article,'$zip','$prepull','$mailfrom','$useRdns',$trace_header_id,'$newMailing','$return_path',$deployPoolID,$deployPoolChunkID,$jlogProfileID,'$deployFileName',$injectorID,'$ConvertSubject','$ConvertFrom',$adrevolutionDeploy,'$etime','$randomType','$CutMail')";
			print LOG "$hr:$sec - $user_id - <$sql>\n";
			$rows=$dbhu->do($sql);
			$sql="select LAST_INSERT_ID()"; 
			$sth=$dbhu->prepare($sql);
			$sth->execute();
			($uid)=$sth->fetchrow_array();
			$sth->finish();
			if ($uid eq "")
			{
				print "<tr><td><font color=red>Deploy $deploy_name not added because error in line, maybe special character in a field.</font></td></tr>\n";
				next;
			}

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
	
			$i=0;
			while ($i <= $#MDOM)
			{
				if ($MDOM[$i] ne "")
				{
					$sql="insert into UniqueDomain(unq_id,mailing_domain) values($uid,'$MDOM[$i]')";
					$rows=$dbhu->do($sql);
				}
				$i++;
			}
			$i=0;
			while ($i <= $#CMDOM)
			{
				if ($CMDOM[$i] ne "")
				{
					$sql="insert into UniqueContentDomain(unq_id,domain_name) values($uid,'$CMDOM[$i]')";
					$rows=$dbhu->do($sql);
				}
				$i++;
			}
			if ($attributed_client_id > 0)
			{
    			$sql="insert into UniqueAttributedClient(unq_id,client_id) values($uid,$attributed_client_id)";
    			$rows=$dbhu->do($sql);
			}
			add_campaigns($uid,$deploy_name,$profile_id,$sdate,$ctime,$cgroup_id,$nl_id,$adv_id,"Off",$group_id);
		}
		if ($deployPoolID > 0)
		{
			print "<tr><td>Deploy $deploy_name added. <b>POOL</b></td></tr>\n";
		}
		else
		{
			print "<tr><td>Deploy $deploy_name added. <b>Normal</b></td></tr>\n";
		}
	} 
	close SAVED;
	close LOG;
	unlink($file_in) || &logerror("Error - could NOT Remove file: $file_in");  # del file_in
	print<<"end_of_html";
</table>
<br><a href="unique_list.cgi">Back to Unique List</a>
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
