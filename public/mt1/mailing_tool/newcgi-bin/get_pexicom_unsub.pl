#!/usr/bin/perl
# *****************************************************************************************
# get_pexicom_unsub.pl
#
# Batch program that runs from cron to get pexicom unsub files 
#
# History
# Jim Sobeck,   02/27/06,   Created
# Jim SObeck,	04/28/06, 	Added logic to also transfer BlueRockDove Files
# *****************************************************************************************

use strict;
use Net::FTP;
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
my $program = "get_pexicom_unsub.pl";
my $errmsg;
my $cname;
my $file_date;
my $id;
my $mailer_name;
my $ftp_ip;
my $ftp_username;
my $ftp_password;
my $list_path;
my $unsub_path;
my $client_id;
my $email_addr;
my $filename;
my ($id,$mailer_name,$ftp_ip,$ftp_username,$ftp_password,$list_path);
my $got_rec;

# connect to the util database
my $dbhq;
my $dbhu;
($dbhq,$dbhu)=$util->get_dbh();
$| = 1;
#
$sql = "select DATE_FORMAT(date_sub(curdate(),INTERVAL 1 DAY),'%Y-%c-%e')";
$sth = $dbhq->prepare($sql);
$sth->execute();
($file_date) = $sth->fetchrow_array();
$sth->finish();

$sql="select third_party_id,mailer_name,mailer_ftp,ftp_username,ftp_password,list_suppression_path,suppression_path from third_party_defaults where mailer_name='Pexicom'"; 
$sth = $dbhq->prepare($sql);
$sth->execute();
if (($id,$mailer_name,$ftp_ip,$ftp_username,$ftp_password,$list_path,$unsub_path) = $sth->fetchrow_array())
{
#	get_ftp_file($ftp_ip,$ftp_username,$ftp_password,"/events","/data3/unsubs/pexicom",$file_date);
#	put_unsubs($ftp_ip,$ftp_username,$ftp_password,$unsub_path,"/data3/unsubs/lm");
}
$sth->finish();
#
# 	Blue Rock Dove push
#
$sql="select third_party_id,mailer_name,mailer_ftp,ftp_username,ftp_password,list_suppression_path,suppression_path from third_party_defaults where mailer_name='Blue Rock Dove'"; 
$sth = $dbhq->prepare($sql);
$sth->execute();
if (($id,$mailer_name,$ftp_ip,$ftp_username,$ftp_password,$list_path,$unsub_path) = $sth->fetchrow_array())
{
	put_unsubs($ftp_ip,$ftp_username,$ftp_password,"/suppression","/data3/unsubs/lm");
}
$sth->finish();
exit(0);

sub get_ftp_file
{
    my ($ftp_ip,$ftp_username,$ftp_password,$remote_path,$mydir,$file_date) = @_
;
    my $filename;
    my $to_dir;
	my $mesg;
	my $temp_filename;
	my $rest_str;
	my $unique_id;
	my $profile_id;

	chdir($mydir);
    my $ftp = Net::FTP->new("$ftp_ip", Timeout => 120, Debug => 0, Passive => 0)
 or $mesg="Cannot connect to $ftp_ip: $@";
    if ($ftp)
    {
        $ftp->login($ftp_username,$ftp_password) or $mesg="Cannot login $ftp_ip"
;
        $ftp->cwd($remote_path);
		$ftp->ascii();
        my @files = $ftp->dir;
		foreach(@files) 
		{
			$_=substr($_,55);
			if (( /${file_date}/ ) && ( /^optout/))
			{
				($temp_filename,$rest_str) = split('\.',$_,2);
				($rest_str,$unique_id) = split("_",$temp_filename,2);
				print "Unique Id <$unique_id>\n";
				$sql="select profile_id,client_id from list_profile where unique_id=$unique_id"; 
				$sth1 = $dbhq->prepare($sql);
				$sth1->execute();
				if (($profile_id,$client_id) = $sth1->fetchrow_array())
				{
					$ftp->get($_);
					print "Getting file for $profile_id\n";
					process_unsubs($profile_id,$client_id,$_,$mydir);
				}
				else
				{
					print "No profile defined for Id $unique_id\n";
				}
				$sth1->finish();
			}
		}
    }
    $ftp->quit;
}

sub process_unsubs
{
my ($profile_id,$client_id,$filename,$mydir) = @_;
my $cdate;
my $email_addr;
my $rest_str;
my $list_str;
my $list_id;
my $sql;
my $sth;
my $rows;
my $temp_filename;
my $eid;

$sql="select list_id from list_profile_list where profile_id=$profile_id";
$sth = $dbhq->prepare($sql);
$sth->execute();
$list_str="";
while (($list_id) = $sth->fetchrow_array())
{
	$list_str = $list_str . $list_id . ",";
}
$sth->finish();
$_ = $list_str;
chop;
$list_str = $_;

$temp_filename=$mydir . "/" . $filename;
open(LOG,"<$temp_filename");
while (<LOG>)
{
	($cdate,$email_addr,$rest_str) = split(",",$_,3);
	$sql="select email_user_id from email_list where email_addr='$email_addr' and status in ('A','P') and list_id in ($list_str)";
	$sth = $dbhq->prepare($sql);
	$sth->execute();
	if (($eid) = $sth->fetchrow_array())
	{
		print "Removing <$email_addr>\n";
		$sql="update email_list set status='U',unsubscribe_date=curdate(),unsubscribe_time=curtime() where email_addr='$email_addr' and status in ('A','P') and list_id in ($list_str)";
		$rows=$dbhu->do($sql);
		$sql="insert into unsub_log(email_addr,unsub_date,client_id) values('$email_addr',now(),$client_id)";
		$rows=$dbhu->do($sql);
	}
	$sth->finish();
}
close(LOG);
unlink($temp_filename);
}

sub put_unsubs 
{
    my ($ftp_ip,$ftp_username,$ftp_password,$remote_path,$mydir) = @_
;
    my $filename;
    my $to_dir;
	my $mesg;
	my $temp_filename;
	my $rest_str;
	my $unique_id;
	my $profile_id;

	chdir($mydir);
    my $ftp = Net::FTP->new("$ftp_ip", Timeout => 120, Debug => 0, Passive => 0)
 or $mesg="Cannot connect to $ftp_ip: $@";
    if ($ftp)
    {
        $ftp->login($ftp_username,$ftp_password) or $mesg="Cannot login $ftp_ip"
;
        $ftp->cwd($remote_path);
		$ftp->ascii();
		$sql="select company from user,list_profile where unique_id > 0 and list_profile.client_id=user.user_id and list_profile.status='A'";
		$sth1 = $dbhq->prepare($sql);
		$sth1->execute();
		while (($cname) = $sth1->fetchrow_array())
		{
			$cname =~ s/ //g;
			$cname = $cname . ".txt";
			my $temp_cname = $cname . "UL.txt";
			print "Putting file for $cname\n";
			$ftp->put($cname,$temp_cname);
			$ftp->rename($temp_cname,$cname);
		}
		$sth1->finish();
		#
		# Push the GlobalSuppression.txt file to
		#
		$cname="GlobalSuppression.txt";
		my $temp_cname="GlobalSuppressionUL.txt";
		$ftp->put($cname,$temp_cname);
		$ftp->rename($temp_cname,$cname);
    }
    $ftp->quit;
}
