#!/usr/bin/perl
#===============================================================================
# File   : upload_uniqueprofile.cgi 
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

#----- Pass control to PROCESS_FILE  or  PROCESS_LIST  -------
if ( $upload_file ne "" ) 
{
	&process_file() ;
}
else
{
	print "Location: uniqueprofile_list.cgi\n\n";
}
exit(0) ;


sub process_file 
{
	my ($file_name, $file_handle, $file_problem, $file_in, $line, @rest_of_line);
	my $sth;
	my $sth1;
	my $upload_dir_unix;
	my $pname;
	my $isp;
	my $country;
	my $ua;
	my $old_pname;
	my ($ostart,$oend,$cstart,$cend,$dstart,$dend);
	my $pid;
	my ($ostart1,$oend1,$cstart1,$cend1,$dstart1,$dend1);
	my ($ostart2,$oend2,$cstart2,$cend2,$dstart2,$dend2);
	my @ISPS;
	my $rstart;
	my $rend;
	my $emailListCnt;
	my $emailListCntOperator;
	my $BusinessUnit;

	$sql = "select BusinessUnit from UserAccounts where user_id=?"; 
	$sth1 = $dbhq->prepare($sql) ;
	$sth1->execute($user_id);
	($BusinessUnit) = $sth1->fetchrow_array();
	$sth1->finish();

	print "Content-type: text/html\n\n";
	print<<"end_of_html";
<html><head><title>Unique Profile Upload Results</title></head>
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
	open(LOG,">>/tmp/upload_uniquegroup_$month$day$year.log");
	print LOG "$hr:$sec - $user_id\n";
	open(SAVED,"<$file_in") || &logerror("Error - could NOT open Input SAVED file: $file_in");
	$old_pname="";
	while (<SAVED>) 
	{
		chomp;                       # remove Carriage Return (if exists)
		$line = $_;
		$line =~ s///g ;      # remove ^M from Email Addr (if exists)
		$line =~ s/\t/|/g ;
		my $tline=$line;
		$line =~ s/,/|/g ;
		print LOG "$hr:$sec - $user_id - <$line>\n";
		($pname,$isp,$ostart,$oend,$cstart,$cend,$dstart,$dend,$rstart,$rend,$emailListCntOperator,$emailListCnt,$country,$ua,@rest_of_line) = split('\|', $line) ;
		$rend=~ s/\n//g;
		$rend=~ s///g;
		$rstart=$rstart||0;
		$rend=$rend||0;
		if ($emailListCntOperator eq "")
		{
			$emailListCnt=0;
		}
		$ostart1=$ostart1||0;
		$ostart2=$ostart2||0;
		$cstart1=$cstart1||0;
		$cstart2=$cstart2||0;
		$dstart1=$dstart1||0;
		$dstart2=$dstart2||0;
		$oend1=$oend1||0;
		$oend2=$oend2||0;
		$cend1=$cend1||0;
		$cend2=$cend2||0;
		$dend1=$dend1||0;
		$dend2=$dend2||0;
		if ($pname eq "profilename")
		{
			next;
		}
		if (($oend < $ostart) or ($cend < $cstart) or ($dend < $dstart))
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
		my @COUNTRYS=split(' ',$country);
		my ($country_str,$ccnt,$iret)=getCountrys(@COUNTRYS);
		if ($iret == 0)
		{
			next;
		}
		my @UAS=split(' ',$ua);
		my ($ua_str,$ccnt,$iret)=getUAs(@UAS);
		if ($iret == 0)
		{
			next;
		}


		if ($pname ne $old_pname)
		{
			$sql="select profile_id from UniqueProfile where profile_name=? and status='A'";
			$sth=$dbhu->prepare($sql);
			$sth->execute($pname);
			if (($pid)=$sth->fetchrow_array())
			{
				$pid=0;
				$old_pname=$pname;
				$sth->finish();
				print "<tr><td><font color=red>Profile Name $pname already exists - All Records Ignored</font></td></tr>\n";
				next;
			}
			$sth->finish();
			$old_pname=$pname;
			$pname=~s/'/''/g;
			$sql="insert into UniqueProfile(profile_name,opener_start,opener_end,clicker_start,clicker_end,deliverable_start,deliverable_end,deliverable_factor,complaint_control,cc_aol_send,cc_yahoo_send,cc_hotmail_send,cc_other_send,send_international,opener_start_date,opener_end_date,clicker_start_date,clicker_end_date,deliverable_start_date,deliverable_end_date,ramp_up_freq,subtract_days,add_days,max_end_date,send_confirmed,status,start_record,end_record,emailListCntOperator,emailListCnt,BusinessUnit) values('$pname',$ostart,$oend,$cstart,$cend,$dstart,$dend,0,'Disable',0,0,0,0,'N','0000-00-00','0000-00-00','0000-00-00','0000-00-00','0000-00-00','0000-00-00',0,0,0,0,'N','A',$rstart,$rend,'$emailListCntOperator',$emailListCnt,'$BusinessUnit')";
			$rows=$dbhu->do($sql);
			$sql="select max(profile_id) from UniqueProfile where profile_name='$pname'";
			$sth=$dbhu->prepare($sql);
			$sth->execute();
			($pid)=$sth->fetchrow_array();
			$sth->finish();
		}
		if ($pid == 0)
		{
			next;
		}
		my @C=split(',',$class_str);
		foreach my $cid (@C)
		{
			$sql="insert into UniqueProfileIsp(profile_id,class_id) values($pid,$cid)";
			$rows=$dbhu->do($sql);
		}
		print "<tr><td>Profile :$pname($pid) added </td></tr>\n";
	} 
	close SAVED;
	close LOG;
	unlink($file_in) || &logerror("Error - could NOT Remove file: $file_in");  # del file_in
	print<<"end_of_html";
</table>
<br><a href="uniqueprofile_list.cgi">Back to Unique Profile List</a>
</center>
</body>
</html>
end_of_html
} # end of sub

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
sub getCountrys
{
	my (@COUNTRYS)=@_;
	my $sql;
	my $cstr;
	my $class_id;
	my $cnt;
	my $sth;
	my $iret;

	$iret=1;
	$cstr="";
	$cnt=0;
	foreach my $c (@COUNTRYS)
	{
		if ($c eq "ALL")
		{
			$cnt=0;
			$sql="select countryID from Country where visible=1";
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
			$sql="select countryID from Country where countryCode=? and visible=1";
			$sth=$dbhu->prepare($sql);
			$sth->execute($c);
			if (($class_id)=$sth->fetchrow_array())
			{
				$cnt++;
				$cstr=$cstr.$class_id.",";
			}
			else
			{
				$iret=0;
				print "<tr><td><font color=red>Invalid Country specified - <b>$c</b> - Record ignored</font></td></tr>\n";
			}
			$sth->finish();
		}
	}
	chop($cstr);
	return($cstr,$cnt,$iret);
}
sub getUAs
{
	my (@UAS)=@_;
	my $sql;
	my $cstr;
	my $class_id;
	my $cnt;
	my $sth;
	my $iret;

	$iret=1;
	$cstr="";
	$cnt=0;
	foreach my $c (@UAS)
	{
		if ($c eq "ALL")
		{
			$cnt=0;
			$sql="select userAgentStringLabelID from UserAgentStringsLabel";
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
			$sql="select userAgentStringLabelID from UserAgentStringsLabel where userAgentStringLabel=?";
			$sth=$dbhu->prepare($sql);
			$sth->execute($c);
			if (($class_id)=$sth->fetchrow_array())
			{
				$cnt++;
				$cstr=$cstr.$class_id.",";
			}
			else
			{
				$iret=0;
				print "<tr><td><font color=red>Invalid Device specified - <b>$c</b> - Record ignored</font></td></tr>\n";
			}
			$sth->finish();
		}
	}
	chop($cstr);
	return($cstr,$cnt,$iret);
}
