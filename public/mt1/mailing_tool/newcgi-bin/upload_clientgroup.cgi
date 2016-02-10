#!/usr/bin/perl
#===============================================================================
# File   : upload_clientgroup.cgi 
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
my $cnt;
my $alt_light_table_bg = $util->get_alt_light_table_bg;
my $images = $util->get_images_url;

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
my $BusinessUnit;

$sql = "select BusinessUnit from UserAccounts where user_id=?"; 
my $sth1 = $dbhq->prepare($sql) ;
$sth1->execute($user_id);
($BusinessUnit) = $sth1->fetchrow_array();
$sth1->finish();

#----- Pass control to PROCESS_FILE  or  PROCESS_LIST  -------
if ( $upload_file ne "" ) 
{
	&process_file() ;
}
else
{
	print "Location: clientgroup_list.cgi\n\n";
}
exit(0) ;


sub process_file 
{
	my ($file_name, $file_handle, $file_problem, $file_in, $line, @rest_of_line);
	my $sth;
	my $sth1;
	my $upload_dir_unix;
	my ($gname,$client_id,$isp,$ostart,$oend,$cstart,$cend,$dstart,$dend,@rest_of_line);
	my ($convertstart,$convertend);
	my ($ostart1,$oend1,$cstart1,$cend1,$dstart1,$dend1);
	my ($convertstart1,$convertend1);
	my ($ostart2,$oend2,$cstart2,$cend2,$dstart2,$dend2);
	my ($convertstart2,$convertend2);
	my @ISPS;
	my $group_id;
	my $cnt;
	my $old_gname;
	my $profile_id;
	my $profile_name;

	print "Content-type: text/html\n\n";
	print<<"end_of_html";
<html><head><title>Client Group Upload Results</title></head>
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

	$file_in = "${upload_dir_unix}cgupload.${user_id}" ;
	open(SAVED,">$file_in") || &logerror("Error - could NOT open Output SAVED file: $file_in");
	$file_handle = $upload_file ;
	print SAVED <$file_handle> ;
	close SAVED;

    my ($sec, $min, $hr, $day, $month, $year, $wkdy, $yrdy, $isDST)=localtime();
    $month+=1; $year+=1900;
	open(LOG,">>/tmp/upload_clientgroup_$month$day$year.log");
	print LOG "$hr:$sec - $user_id\n";
	open(SAVED,"<$file_in") || &logerror("Error - could NOT open Input SAVED file: $file_in");
	$old_gname="";
	while (<SAVED>) 
	{
		chomp;                       # remove Carriage Return (if exists)
		$line = $_;
		$line =~ s///g ;      # remove ^M from Email Addr (if exists)
		$line =~ s/\t/|/g ;
		my $tline=$line;
		$line =~ s/,/|/g ;
		print LOG "$hr:$sec - $user_id - <$line>\n";
		($gname,$client_id,$isp,$ostart,$oend,$cstart,$cend,$convertstart,$convertend,$dstart,$dend,$ostart1,$oend1,$cstart1,$cend1,$convertstart1,$convertend1,$dstart1,$dend1,$ostart2,$oend2,$cstart2,$cend2,$convertstart2,$convertend2,$dstart2,$dend2,@rest_of_line) = split('\|', $line) ;
		$ostart1=$ostart1||0;
		$ostart2=$ostart2||0;
		$cstart1=$cstart1||0;
		$cstart2=$cstart2||0;
		$dstart1=$dstart1||0;
		$dstart2=$dstart2||0;
		$convertstart1=$convertstart1||0;
		$convertstart2=$convertstart2||0;
		$oend1=$oend1||0;
		$oend2=$oend2||0;
		$cend1=$cend1||0;
		$cend2=$cend2||0;
		$dend1=$dend1||0;
		$dend2=$dend2||0;
		$convertend1=$convertend1||0;
		$convertend2=$convertend2||0;
		if ($gname eq "Name")
		{
			next;
		}
		if (($oend < $ostart) or ($cend < $cstart) or ($dend < $dstart) or ($convertend < $convertstart))
		{
			print "<tr><td><font color=red>Start Range cannot be before End range  - $tline</font></td></tr>\n";
			next;
		}
		my @ISPS=split(' ',$isp);
		my ($class_str,$ccnt,$iret)=getClasses(@ISPS);
		if ($iret == 0)
		{
			next;
		}


		if ($gname ne $old_gname)
		{
			$sql="select client_group_id from ClientGroup where group_name=? and status='A'";
			$sth=$dbhu->prepare($sql);
			$sth->execute($gname);
			if (($group_id)=$sth->fetchrow_array())
			{
				$group_id=0;
				$old_gname=$gname;
				$sth->finish();
				print "<tr><td><font color=red>Group Name $gname already exists - All Records Ignored</font></td></tr>\n";
				next;
			}
			$sth->finish();
			$old_gname=$gname;
			$gname=~s/'/''/g;
			$sql="insert into ClientGroup(group_name,status,BusinessUnit) values('$gname','A','$BusinessUnit')";
			$rows=$dbhu->do($sql);
			$sql="select max(client_group_id) from ClientGroup where group_name=?";
			$sth=$dbhu->prepare($sql);
			$sth->execute($gname);
			($group_id)=$sth->fetchrow_array();
			$sth->finish();
		}
		if ($group_id == 0)
		{
			next;
		}
		$sql="select count(*) from user where user_id=? and status='A'";
		$sth=$dbhu->prepare($sql);
		$sth->execute($client_id);
		($cnt)=$sth->fetchrow_array();
		$sth->finish();
		if ($cnt == 0)
		{
			print "<tr><td><font color=redClient $client_id not added - Record does not exist or is not active</font></td></tr>\n";
			next;
		}
		$sql="select count(*) from ClientGroupClients where group_id=? and client_id=?";
		$sth=$dbhu->prepare($sql);
		$sth->execute($group_id,$client_id);
		($cnt)=$sth->fetchrow_array();
		$sth->finish();
		if ($cnt > 0)
		{
			print "<tr><td><font color=redClient $client_id not added - Already exists for Client group</font></td></tr>\n";
			next;
		}
		($profile_id,$profile_name)=getProfile($class_str,$ccnt,$gname,$client_id,$ostart,$oend,$cstart,$cend,$dstart,$dend,$ostart1,$oend1,$cstart1,$cend1,$dstart1,$dend,$ostart2,$oend2,$cstart2,$cend2,$dstart2,$dend2,$convertstart,$convertend,$convertstart1,$convertend1,$convertstart2,$convertend2);
		$sql="insert into ClientGroupClients(client_group_id,client_id,profile_id) values($group_id,$client_id,$profile_id)";
		$rows=$dbhu->do($sql);

		print "<tr><td>Client Group:$gname Client: $client_id Profile: $profile_name($profile_id) added </td></tr>\n";
	} 
	close SAVED;
	close LOG;
	unlink($file_in) || &logerror("Error - could NOT Remove file: $file_in");  # del file_in
	print<<"end_of_html";
</table>
<br><a href="clientgroup_list.cgi">Back to Client Group List</a>
</center>
</body>
</html>
end_of_html
} # end of sub

sub getProfile
{
	my ($class_str,$ccnt,$gname,$client_id,$ostart,$oend,$cstart,$cend,$dstart,$dend,$ostart1,$oend1,$cstart1,$cend1,$dstart1,$dend1,$ostart2,$oend2,$cstart2,$cend2,$dstart2,$dend2,$convertstart,$convertend,$convertstart1,$convertend1,$convertstart2,$convertend2)=@_;
	my $profile_id;
	my $pname;
	my $profile_name;
	my $got_pid;
	my $tcnt;
	my $rows;

	$got_pid=0;
	$sql="select profile_id,profile_name from UniqueProfile where status='A' and opener_start=$ostart and opener_end=$oend and clicker_start=$cstart and clicker_end=$cend and deliverable_start=$dstart and deliverable_end=$dend and start_record is null and end_record is null and ramp_up_freq=0 and opener_start1=$ostart1 and opener_end1=$oend1 and clicker_start1=$cstart1 and clicker_end1=$cend1 and deliverable_start1=$dstart1 and deliverable_end1=$dend1 and opener_start2=$ostart2 and opener_end2=$oend2 and clicker_start2=$cstart2 and clicker_end2=$cend2 and deliverable_start2=$dstart2 and send_confirmed='N' and ramp_up_email_cnt=0 and deliverable_end2=$dend2 and convert_start=$convertstart and convert_end=$convertend and convert_start1=$convertstart1 and convert_end1=$convertend1 and convert_start2=$convertstart2 and convert_end2=$convertend2";
	my $sth=$dbhu->prepare($sql);
	$sth->execute();
	while (($profile_id,$pname)=$sth->fetchrow_array())
	{
		if ($got_pid > 0)
		{
			next;
		}
		$sql="select count(*) from UniqueProfileIsp where profile_id=$profile_id"; 
		my $sth1=$dbhu->prepare($sql);
		$sth1->execute();
		($tcnt)=$sth1->fetchrow_array();
		$sth1->finish();
		if ($ccnt != $tcnt)
		{
			next;
		}

		$sql="select count(*) from UniqueProfileIsp where profile_id=$profile_id and class_id in ($class_str)";
		my $sth1=$dbhu->prepare($sql);
		$sth1->execute();
		($tcnt)=$sth1->fetchrow_array();
		$sth1->finish();
		if ($ccnt == $tcnt)
		{
			$got_pid=$profile_id;
			$profile_name=$pname;
		}
	}
	$sth->finish();
	if ($got_pid == 0)
	{
		$profile_name=$gname."-".$client_id;
		$sql="insert into UniqueProfile(profile_name,opener_start,opener_end,clicker_start,clicker_end,deliverable_start,deliverable_end,status,last_updated,send_confirmed,opener_start1,opener_end1,clicker_start1,clicker_end1,deliverable_start1,deliverable_end1,opener_start2,opener_end2,clicker_start2,clicker_end2,deliverable_start2,deliverable_end2,convert_start,convert_end,convert_start1,convert_end1,convert_start2,convert_end2) values('$profile_name',$ostart,$oend,$cstart,$cend,$dstart,$dend,'A',now(),'N',$ostart1,$oend1,$cstart1,$cend1,$dstart1,$dend1,$ostart2,$oend2,$cstart2,$cend2,$dstart2,$dend2,$convertstart,$convertend,$convertstart1,$convertend1,$convertstart2,$convertend2)";
		$rows=$dbhu->do($sql);
		$sql="select max(profile_id) from UniqueProfile where profile_name=?";
		my $sth1=$dbhu->prepare($sql);
		$sth1->execute($profile_name);
		($got_pid)=$sth1->fetchrow_array();
		$sth1->finish();

		my @C=split(',',$class_str);
		foreach my $cid (@C)
		{
			$sql="insert into UniqueProfileIsp(profile_id,class_id) values($got_pid,$cid)";
			$rows=$dbhu->do($sql);
		}
	}
	return($got_pid,$profile_name);	
}

sub getClasses
{
	my (@ISPS)=@_;
	my $sql;
	my $cstr;
	my $class_id;
	my $cnt;
	my $sth;
	my $iret;

	$iret=1;
	$cstr="";
	$cnt=0;
	foreach my $isp (@ISPS)
	{
		if ($isp eq "ALL")
		{
			$cnt=0;
			$sql="select class_id from email_class where status='Active'";
			$sth=$dbhu->prepare($sql);
			$sth->execute();
			while (($class_id)=$sth->fetchrow_array())
			{
				$cnt++;
				$cstr=$cstr.$class_id.",";
			}
			$sth->finish();
		}
		else
		{
			$sql="select class_id from email_class where class_name=? and status='Active'";
			$sth=$dbhu->prepare($sql);
			$sth->execute($isp);
			if (($class_id)=$sth->fetchrow_array())
			{
				$cnt++;
				$cstr=$cstr.$class_id.",";
			}
			else
			{
				$iret=0;
				print "<tr><td><font color=red>Invalid ISP specified - <b>$isp</b> - Record ignored</font></td></tr>\n";
			}
			$sth->finish();
		}
	}
	chop($cstr);
	return($cstr,$cnt,$iret);
}
