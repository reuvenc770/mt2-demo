#!/usr/bin/perl
#===============================================================================
# File   : upload_ipgroup.cgi 
#
#--Change Control---------------------------------------------------------------
#===============================================================================

#-----------------------
# include Perl Modules
#-----------------------
use strict;
use CGI;
use util;

$|=1 ;   # set OUTPUT_AUTOFLUSH to true

my $util = util->new;
my $query = CGI->new;
my $dbh;
my $rows;
my $cnt;
my $sql;
my $alt_light_table_bg = $util->get_alt_light_table_bg;
my $images = $util->get_images_url;
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
my ($dbhq,$dbhu)=$util->get_dbh();

#----- Pass control to PROCESS_FILE  or  PROCESS_LIST  -------
if ( $upload_file ne "" ) 
{
	&process_file() ;
}
print "Location: unique_slot.cgi\n\n";
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
	my @GMDOM;
	my @CGMDOM;
	my $slot_id;
	my $surl;
	my $mail_from;
	my $use_master;
	my $useRdns;
	my $return_path;
	my $prepull;
	my $ConvertSubject;
	my $ConvertFrom;
	my $jlogProfileID;
	my $jlogProfileName;
	my $upload_dir_unix;
	my ($ctime,$cgroup,$ipgroup, $profile,$domain,$mta_setting,$template,$ctype,$randomize,$logging,@rest_of_line);
	my $etime;
	my $cdomain;

	# get upload subdir
	$sql = "select parmval from sysparm where parmkey = 'UPLOAD_DIR_UNIX'";
	$sth1 = $dbhq->prepare($sql) ;
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

	$file_in = "${upload_dir_unix}unqslot.${user_id}" ;
	open(SAVED,">$file_in") || &logerror("Error - could NOT open Output SAVED file: $file_in");
	$file_handle = $upload_file ;
	print SAVED <$file_handle> ;
	close SAVED;

    my ($sec, $min, $hr, $day, $month, $year, $wkdy, $yrdy, $isDST)=localtime();
    $month+=1; $year+=1900;
    open(LOG,">>/tmp/upload_unqslot_$month$day$year.log");
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
    	print LOG "$hr:$sec - $user_id <$line>\n";
		($slot_id,$ctime,$etime,$cgroup,$ipgroup, $profile,$domain,$mta_setting,$template,$ctype,$randomize,$logging,$surl,$mail_from,$use_master,$useRdns,$return_path,$prepull,$ConvertSubject,$ConvertFrom,$jlogProfileName,$cdomain,@rest_of_line) = split('\|', $line) ;
		if ($use_master ne "Y")
		{
			$use_master="N";
		}
		if ($prepull ne "Y")
		{
			$prepull="N";
		}
		if (($jlogProfileName ne "") and ($jlogProfileID ne "0"))
		{
			$sql="select profileID from EmailEventHandlerProfile where profileName=?"; 
			$sth=$dbhu->prepare($sql);
			$sth->execute($jlogProfileName);
			if (($jlogProfileID)=$sth->fetchrow_array())
			{
				$sth->finish();
			}
			else
			{
				$sth->finish();
    			print LOG "$hr:$sec - Skipped jlog profile $jlogProfileName not found\n";
				next;
			}
		}
		else
		{
			$jlogProfileID=0;
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
		if ($useRdns ne "Y")
		{
			$useRdns="N";
		}
		if ($randomize eq "")
		{
			$randomize="Y";
		}
		$ipgroup=~s/'/''/g;
		$cgroup=~s/'/''/g;
		$profile=~s/'/''/g;
		$mta_setting=~s/'/''/g;
		if ($surl eq "")
		{
			$surl="ALL";
		}
		@MDOM=split(' ',$domain);
		my $i=0;
		my $j=0;
		while ($i <= $#MDOM)
		{
			if ($MDOM[$i] ne "")
			{
#				$sql="select count(*) from DomainExclusion where domain=?";
#				$sth=$dbhu->prepare($sql);
#				$sth->execute($MDOM[$i]);
#				($cnt)=$sth->fetchrow_array();
#				$sth->finish();
				$cnt=0;
				if ($cnt == 0)
				{
					$GMDOM[$j]=$MDOM[$i];
					$j++;
				}
			}
			$i++;
		}
		@CMDOM=split(' ',$cdomain);
		my $i=0;
		my $j=0;
		while ($i <= $#CMDOM)
		{
			if ($CMDOM[$i] ne "")
			{
				$CGMDOM[$j]=$CMDOM[$i];
				$j++;
			}
			$i++;
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
    		print LOG "$hr:$sec - Skipped Ip Group $ipgroup not found\n";
			next;
		}
		$sql="select client_group_id from ClientGroup where group_name=? and status='A'";
		$sth=$dbhu->prepare($sql);
		$sth->execute($cgroup);
		if (($cgroup_id)=$sth->fetchrow_array())
		{
			$sth->finish();
		}
		else
		{
			$sth->finish();
    		print LOG "$hr:$sec - Skipped Client Group $cgroup not found\n";
			next;
		}
		$sql="select profile_id from UniqueProfile where profile_name=? and status='A'";
		$sth=$dbhu->prepare($sql);
		$sth->execute($profile);
		if (($profile_id)=$sth->fetchrow_array())
		{
			$sth->finish();
		}
		else
		{
			$sth->finish();
    		print LOG "$hr:$sec - Skipped Profile $profile not found\n";
			next;
		}
		$sql="select mta_id from mta where name=?";
		$sth=$dbhu->prepare($sql);
		$sth->execute($mta_setting);
		if (($mta_id)=$sth->fetchrow_array())
		{
			$sth->finish();
		}
		else
		{
			$sth->finish();
    		print LOG "$hr:$sec - Skipped MTA $mta_setting not found\n";
			next;
		}
		$sql="select template_id from brand_template where template_name=?";
		$sth=$dbhu->prepare($sql);
		$sth->execute($template);
		if (($template_id)=$sth->fetchrow_array())
		{
			$sth->finish();
		}
		else
		{
			$sth->finish();
    		print LOG "$hr:$sec - Skipped Template $template not found\n";
			next;
		}
		if ($slot_id > 0)
		{
			$sql="update UniqueSlot set client_group_id=$cgroup_id,ip_group_id=$group_id,schedule_time='$ctime',profile_id=$profile_id,mailing_domain='$GMDOM[0]',template_id=$template_id,slot_type='$ctype',randomize_records='$randomize',log_campaign='$logging',mta_id=$mta_id,source_url='$surl',mail_from='$mail_from',use_master='$use_master',useRdns='$useRdns',return_path='$return_path',prepull='$prepull',ConvertSubject='$ConvertSubject',ConvertFrom='$ConvertFrom',jlogProfileID=$jlogProfileID,end_time='$etime' where slot_id=$slot_id";
			$rows=$dbhu->do($sql);
    		print LOG "$hr:$sec - $user_id <$sql>\n";
			$sql="delete from UniqueSlotDomain where slot_id=$slot_id";
			$rows=$dbhu->do($sql);
			$sql="delete from UniqueSlotContentDomain where slot_id=$slot_id";
			$rows=$dbhu->do($sql);
		}
		else
		{
			$sql="insert into UniqueSlot(client_group_id,ip_group_id,schedule_time,status,profile_id,mailing_domain,template_id,slot_type,log_campaign,randomize_records,mta_id,source_url,mail_from,use_master,useRdns,return_path,prepull,ConvertSubject,ConvertFrom,jlogProfileID,end_time) values($cgroup_id,$group_id,'$ctime','A',$profile_id,'$GMDOM[0]',$template_id,'$ctype','$logging','$randomize',$mta_id,'$surl','$mail_from','$use_master','$useRdns','$return_path','$prepull','$ConvertSubject','$ConvertFrom',$jlogProfileID,'$etime')";
			$rows=$dbhu->do($sql);
    		print LOG "$hr:$sec - $user_id <$sql>\n";
			$sql="select max(slot_id) from UniqueSlot where client_group_id=$cgroup_id and ip_group_id=$group_id and slot_type='$ctype'";
			my $sth=$dbhu->prepare($sql);
			$sth->execute();
			($slot_id)=$sth->fetchrow_array();
			$sth->finish();
		}

		my $i=0;
		while ($i <= $#GMDOM)
		{
			if ($GMDOM[$i] ne "")
			{
				$sql="insert into UniqueSlotDomain(slot_id,mailing_domain) values($slot_id,'$GMDOM[$i]')";
				$rows=$dbhu->do($sql);
			}
			$i++;
		}
		my $i=0;
		while ($i <= $#CGMDOM)
		{
			if ($CGMDOM[$i] ne "")
			{
				$sql="insert into UniqueSlotContentDomain(slot_id,domain_name) values($slot_id,'$CGMDOM[$i]')";
				$rows=$dbhu->do($sql);
			}
			$i++;
		}
	} 
	close SAVED;
	close LOG;
	unlink($file_in) || &logerror("Error - could NOT Remove file: $file_in");  # del file_in
#
# update any slots already scheduled
#
my $uid;
my $sdate;
$sql="select us.unq_id,uc.send_date from UniqueSchedule us, unique_campaign uc where us.slot_id=$slot_id and us.unq_id=uc.unq_id and uc.send_date >= curdate() and uc.server_id=0 and uc.status='START'";
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($uid,$sdate)=$sth->fetchrow_array())
{
	$sql="update unique_campaign set group_id=$group_id,client_group_id=$cgroup_id,profile_id=$profile_id,mailing_template=$template_id,slot_type='$ctype',hour_offset=0,log_campaign='$logging',mailing_domain='$GMDOM[0]',randomize_records='$randomize',mta_id=$mta_id,source_url='$surl',mail_from='$mail_from',use_master='$use_master',useRdns='$useRdns',return_path='$return_path',prepull='$prepull',ConvertSubject='$ConvertSubject',ConvertFrom='$ConvertFrom',jlogProfileID=$jlogProfileID where unq_id=$uid";
	$rows = $dbhu->do($sql);
	$sql="delete from UniqueDomain where unq_id=$uid";
	$rows = $dbhu->do($sql);
	$sql="insert ignore into UniqueDomain select $uid,mailing_domain from UniqueSlotDomain where slot_id=$slot_id";
	$rows = $dbhu->do($sql);
	$sql="delete from UniqueContentDomain where unq_id=$uid";
	$rows = $dbhu->do($sql);
	$sql="insert ignore into UniqueContentDomain select $uid,domain_name from UniqueSlotContentDomain where slot_id=$slot_id";
	$rows = $dbhu->do($sql);

	$sql="select campaign_id from campaign where id='$uid' and scheduled_date='$sdate' and deleted_date is null";
	my $campid;
	my $cdate=$sdate." ".$ctime;

	my $sth1=$dbhu->prepare($sql);
	$sth1->execute();
	while (($campid)=$sth1->fetchrow_array())
	{
		$sql="update campaign set scheduled_time='$ctime',scheduled_datetime='$cdate' where campaign_id=$campid";
		$rows = $dbhu->do($sql);
	}
	$sth1->finish();
}
$sth->finish();

} # end of sub
