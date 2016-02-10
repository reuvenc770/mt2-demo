#!/usr/bin/perl
#===============================================================================
# File   : upload_ddschedule.cgi 
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
my $sql;
my $ddname;
my $dd_id;
my $alt_light_table_bg = $util->get_alt_light_table_bg;
my $images = $util->get_images_url;


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
print "Location: /daily_schedule.html\n\n";
exit(0) ;


sub process_file 
{
	my ($file_name, $file_handle, $file_problem, $file_in, $line, @rest_of_line);
	my $sql;
	my $sth;
	my $sth1;
	my $upload_dir_unix;
	my ($client_id,$slot_id,$cday,$aid);
	my $client_name;
	my $usa_id;
	my $uname;
	my $camp_id;
	my $old_aid;
	my $creative;
	my $subject;
	my $from;
	my $old_creative;
	my $old_subject;
	my $old_from;
	my $bid=4581;
	my @restline;
	my $sourceURL;

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

	$file_in = "${upload_dir_unix}ddsched.${user_id}" ;
	open(SAVED,">$file_in") || &logerror("Error - could NOT open Output SAVED file: $file_in");
	$file_handle = $upload_file ;
	print SAVED <$file_handle> ;
	close SAVED;

	open(SAVED,"<$file_in") || &logerror("Error - could NOT open Input SAVED file: $file_in");
	while (<SAVED>) 
	{
		chomp;                       # remove Carriage Return (if exists)
		$line = $_;
		$line =~ s///g ;      # remove ^M from Email Addr (if exists)
		$line =~ s/\t/|/g ;
		$line =~ s/,/|/g ;
		($client_name,$client_id,$slot_id,$sourceURL,$cday,$uname,$ddname,@restline) = split('\|', $line) ;
		if (($client_id eq "user_id") or ($client_id eq "client_id"))
		{
			next;
		}
		if (($client_id eq "") or ($client_id == 0))
		{
			$sql="select user_id from user where first_name=? and status='A'";
			$sth=$dbhu->prepare($sql);
			$sth->execute($client_name);
			if (($client_id)=$sth->fetchrow_array())
			{
				$sth->finish();
			}
			else
			{
				$sth->finish();
				next;
			}
		}
			
		$sql="select usa.usa_id,usa.advertiser_id,usa.creative_id,usa.subject_id,usa.from_id from UniqueScheduleAdvertiser usa,advertiser_info ai where usa.advertiser_id=ai.advertiser_id and ai.status != 'I' and name=?";
		$sth=$dbhu->prepare($sql);
		$sth->execute($uname);
		if (($usa_id,$aid,$creative,$subject,$from)=$sth->fetchrow_array())
		{
		}
		else
		{
			next;
		}
		$sth->finish();
		$sql="select dd_id from DailyDealSetting where name=? and settingType='Daily'"; 
		$sth=$dbhu->prepare($sql);
		$sth->execute($ddname);
		if (($dd_id)=$sth->fetchrow_array())
		{
		}
		else
		{
			next;
		}
		$sth->finish();

		$sql="select advertiser_id,c.creative1_id,c.subject1,c.from1,csi.campaign_id from camp_schedule_info csi, campaign c,daily_deals dd where csi.campaign_id=c.campaign_id and csi.slot_id=? and csi.client_id=? and csi.slot_type='D' and dd.client_id=csi.client_id and dd.cday=? and dd.campaign_id=c.campaign_id and csi.status='A'";
		$sth=$dbhu->prepare($sql);
		$sth->execute($slot_id,$client_id,$cday);
		($old_aid,$old_creative,$old_subject,$old_from,$camp_id)=$sth->fetchrow_array();
		$sth->finish();
		if (($old_aid == $aid) and ($old_creative == $creative) and ($old_subject == $subject) and ($old_from == $from))
		{
			next;
		}
		$sql="delete from daily_deals where campaign_id=$camp_id";
		$rows=$dbhu->do($sql);
        $sql="update campaign set deleted_date=curdate() where campaign_id=$camp_id"; 
        $rows=$dbhu->do($sql);
        $sql="update camp_schedule_info set status='D' where campaign_id=$camp_id and slot_type='D'"; 
        $rows=$dbhu->do($sql);
		add_camp($client_id,$aid,$bid,$cday,$slot_id,$creative,$subject,$from,$usa_id,$sourceURL,$dd_id);
	} 
	close SAVED;
	unlink($file_in) || &logerror("Error - could NOT Remove file: $file_in");  # del file_in

} # end of sub

sub add_camp
{
	my ($cid,$adv_id,$bid,$cday,$slot_id,$creative,$subject,$from,$usa_id,$sourceURL,$dd_id)=@_;
	my $cname;
	my $deploy_name;
	my $rows;
	my $camp_id;
	my $sth;
	my $mta_id;

	if ($adv_id eq "")
	{
		return;
	}
	$sql="select advertiser_name from advertiser_info where advertiser_id=?";
	$sth=$dbhu->prepare($sql);
	$sth->execute($adv_id);
	($cname)=$sth->fetchrow_array();
	$sth->finish();
	$deploy_name=$cname;
	$deploy_name=~s/'/''/g;
	$sql = "insert into campaign(user_id,campaign_name,status,created_datetime,scheduled_datetime,sent_datetime,advertiser_id,profile_id,brand_id,scheduled_date,scheduled_time,campaign_type,creative1_id,subject1,from1) values($cid,'$deploy_name','W',now(),curdate(),curtime(),$adv_id,0,$bid,curdate(),curtime(),'DAILY',$creative,$subject,$from)";
	$rows=$dbhu->do($sql);
	$sql = "select max(campaign_id) from campaign where campaign_name='$deploy_name' and scheduled_date=curdate() and advertiser_id=$adv_id and profile_id=0 and campaign_type='DAILY' and brand_id=$bid";
	$sth = $dbhu->prepare($sql);
	$sth->execute();
	($camp_id) = $sth->fetchrow_array();
	$sth->finish();
	$sql="insert into daily_deals(campaign_id,client_id,cday) values($camp_id,$cid,$cday)";
	$rows=$dbhu->do($sql);
	$sql="insert into camp_schedule_info(client_id,slot_id,slot_type,schedule_date,campaign_id,nl_id,usa_id) values($cid,$slot_id,'D',curdate(),$camp_id,$cday,$usa_id)";
	$rows=$dbhu->do($sql);

	$sql="update schedule_info set source_url='$sourceURL',mta_id=$dd_id where client_id=$cid and slot_id=$slot_id and slot_type='D'";
	$rows=$dbhu->do($sql);
}
