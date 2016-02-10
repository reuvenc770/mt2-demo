#!/usr/bin/perl

# ******************************************************************************
# uniqueprofile_check_save.cgi 
#
# this page updates information in the UniqueCheck table
#
# History
# ******************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $sql;
my $dbh;
my $sid;
my $i;
my $class;
my $errmsg;
my $rows;
my $images = $util->get_images_url;
my $pmesg="";
my $cnt;
my $temp_cnt;
my $pid;
my $ctype="Old";
my $fields="";
my $aid=0;
my $recordsFile=0;

$cnt=0;
# connect to the util database
my ($dbhq,$dbhu)=$util->get_dbh();

# check for login
my $user_id = util::check_security();
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
	$util->clean_up();
    exit(0);
}
#
#
my $cgroup_id= $query->param('cgroupid');
if ($cgroup_id eq "")
{
	$cgroup_id=0;
}
my $randomize_flag= $query->param('randomize_flag');
if ($randomize_flag eq "")
{
	$randomize_flag="Y";
}
my $dupCnt= $query->param('dupCnt');
if ($dupCnt eq "")
{
	$dupCnt=0;
}
my $clientid= $query->param('clientid');
if ($clientid eq "")
{
	$clientid=0;
}
my $export= $query->param('export');
if ($export eq "")
{
	$export=0;
}
my $mcid= $query->param('mcid');
$mcid=~s/ //g;
if ($export > 0)
{
	my $username;
	my $exportData;
	my $ctime;
	$sql = "select username, exportData,now() from UserAccounts where user_id = ?";
	$sth = $dbhq->prepare($sql) ;
	$sth->execute($user_id);
	($username, $exportData,$ctime) = $sth->fetchrow_array();
	$sth->finish();
	if ($exportData eq "N")
	{
		open(LOG2,">>/tmp/export.log");
		print LOG2 "$ctime - $username\n";
		close(LOG2);
		print "Content-type: text/html\n\n";
		print<<"end_of_html";
<html><head><title>Export Error</title></head>
<body>
<center><h3>You do not have permission to Export Data.  This attempt has been logged.</h3><br>
<a href="/cgi-bin/mainmenu.cgi"><img src="/images/home_blkline.gif" border=0></a>
</center>
</body>
</html>
end_of_html
		exit();
	}
	$ctype="Export";
	if ($export == 2)
	{
		$ctype="Export Suppression";
	}
	$recordsFile= $query->param('recordsFile');
	if ($recordsFile eq "")
	{
		$recordsFile=0;
	}
	my @F= $query->param('fields');
	foreach my $fld (@F)
	{
		$fields.=$fld.",";
	}
	chop($fields);
	$aid= $query->param('aid');
}
my $ostart= $query->param('ostart');
my $oend= $query->param('oend');
my $cstart= $query->param('cstart');
my $cend= $query->param('cend');
my $dstart= $query->param('dstart');
my $dend= $query->param('dend');
my $ostart1= $query->param('ostart1');
my $oend1= $query->param('oend1');
my $cstart1= $query->param('cstart1');
my $cend1= $query->param('cend1');
my $dstart1= $query->param('dstart1');
my $dend1= $query->param('dend1');
my $ostart2= $query->param('ostart2');
my $oend2= $query->param('oend2');
my $cstart2= $query->param('cstart2');
my $cend2= $query->param('cend2');
my $dstart2= $query->param('dstart2');
my $dend2= $query->param('dend2');

my $convert_start= $query->param('convert_start');
my $convert_end= $query->param('convert_end');
my $convert_start1= $query->param('convert_start1');
my $convert_end1= $query->param('convert_end1');
my $convert_start2= $query->param('convert_start2');
my $convert_end2= $query->param('convert_end2');

my $rstart= $query->param('rstart');
my $rend= $query->param('rend');
my $ostart_date= $query->param('ostart_date');
my $oend_date= $query->param('oend_date');
my $cstart_date= $query->param('cstart_date');
my $cend_date= $query->param('cend_date');
my $dstart_date= $query->param('dstart_date');
my $dend_date= $query->param('dend_date');
my $convert_start_date= $query->param('convert_start_date');
my $convert_end_date= $query->param('convert_end_date');
my $dfactor = $query->param('dfactor');
my $send_international= $query->param('send_international');
my $source_url= $query->param('source_url');
if ($source_url eq "")
{
	$source_url="ALL";
}
if ($send_international eq "")
{
	$send_international="Y";
}
my $send_confirmed= $query->param('send_confirmed');
if ($send_confirmed eq "")
{
	$send_confirmed="Y";
}
my $surl= $query->param('surl');
my $seeds= $query->param('seeds');
my $zips= $query->param('zips');
my @genderarr= $query->param('gender');
my $gender="";
foreach my $g (@genderarr)
{
	if ($g ne "")
	{
		$gender.=$g."|";
	}
}
my $min_age= $query->param('min_age');
if ($min_age eq "")
{
	$min_age=0;
}
my $max_age= $query->param('max_age');
if ($max_age eq "")
{
	$max_age=0;
}
my $DeliveryDays= $query->param('DeliveryDays');
if ($DeliveryDays eq "")
{
	$DeliveryDays=0;
}
$sql="insert into UniqueCheck(check_date,opener_start,opener_end,clicker_start,clicker_end,deliverable_start,deliverable_end,deliverable_factor,send_international,opener_start_date,opener_end_date,clicker_start_date,clicker_end_date,deliverable_start_date,deliverable_end_date,opener_start1,opener_end1,clicker_start1,clicker_end1,deliverable_start1,deliverable_end1,opener_start2,opener_end2,clicker_start2,clicker_end2,deliverable_start2,deliverable_end2,send_confirmed,convert_start,convert_end,convert_start_date,convert_end_date,convert_start1,convert_end1,convert_start2,convert_end2,source_url,client_group_id,gender,min_age,max_age,DeliveryDays,client_id,type,volume_desired,fieldsToExport,advertiser_id,randomize_flag,user_id,dupCnt) values(curdate(),$ostart,$oend,$cstart,$cend,$dstart,$dend,$dfactor,'$send_international','$ostart_date','$oend_date','$cstart_date','$cend_date','$dstart_date','$dend_date',$ostart1,$oend1,$cstart1,$cend1,$dstart1,$dend1,$ostart2,$oend2,$cstart2,$cend2,$dstart2,$dend2,'$send_confirmed',$convert_start,$convert_end,'$convert_start_date','$convert_end_date',$convert_start1,$convert_end1,$convert_start2,$convert_end2,'$source_url',$cgroup_id,'$gender',$min_age,$max_age,$DeliveryDays,$clientid,'$ctype',$recordsFile,'$fields',$aid,'$randomize_flag',$user_id,$dupCnt)";
$rows=$dbhu->do($sql);
$sql="select max(check_id) from UniqueCheck"; 
$sth=$dbhu->prepare($sql);
$sth->execute();
($pid)=$sth->fetchrow_array();
$sth->finish();

if ($clientid > 0)
{
	my @cdata= $query->param('cdata');
	foreach my $keyval (@cdata)
	{
		my ($key,$val)=split('\|',$keyval);
		$sql="insert into UniqueCheckCustom(check_id,clientRecordKeyID,clientRecordValueID) values($pid,$key,$val)";
		$rows=$dbhu->do($sql);
	}
}
my @isps= $query->param('isps');
my @country= $query->param('country');
my @ua= $query->param('ua');
if ($cgroup_id == 0)
{
	my @clients= $query->param('sel2');
	foreach my $client (@clients)
	{
		$sql="insert into UniqueCheckClient(check_id,client_id) values($pid,$client)";
		$rows=$dbhu->do($sql);
		foreach my $isp (@isps)
		{
			$sql="insert into UniqueCheckIsp(check_id,class_id,client_id) values($pid,$isp,$client)";
			$rows=$dbhu->do($sql);
		}
	}
	my @m1;
	if ($mcid ne '')
	{
    	$mcid =~ s/[ \n\r\f\t]/\|/g ;
    	$mcid =~ s/\|{2,999}/\|/g ;
    	@m1= split '\|', $mcid;
	}
	my $i=0;
	my $cnt;
	while ($i <= $#m1)
	{
		$sql="select count(*) from user where user_id=? and status='A'";
		my $sth=$dbhu->prepare($sql);
		$sth->execute($m1[$i]);
		($cnt)=$sth->fetchrow_array();
		$sth->finish();
		if ($cnt > 0)
		{
			$sql="insert into UniqueCheckClient(check_id,client_id) values($pid,$m1[$i])";
			$rows=$dbhu->do($sql);
			foreach my $isp (@isps)
			{
				$sql="insert into UniqueCheckIsp(check_id,class_id,client_id) values($pid,$isp,$m1[$i])";
				$rows=$dbhu->do($sql);
			}
		}
		$i++;
	}
}
else
{
	$sql="select client_id from ClientGroupClients where client_group_id=?";
	$sth=$dbhu->prepare($sql);
	$sth->execute($cgroup_id);
	my $client;
	while (($client)=$sth->fetchrow_array())
	{
		$sql="insert into UniqueCheckClient(check_id,client_id) values($pid,$client)";
		$rows=$dbhu->do($sql);
		foreach my $isp (@isps)
		{
			$sql="insert into UniqueCheckIsp(check_id,class_id,client_id) values($pid,$isp,$client)";
			$rows=$dbhu->do($sql);
		}
	}
	$sth->finish();
}
if ($surl ne '')
{
	$surl =~ s/[ \n\r\f\t]/\|/g ;    
	$surl =~ s/\|{2,999}/\|/g ;           
	my @url= split '\|', $surl;
	foreach my $u (@url)
	{
		$sql="insert into UniqueCheckUrl(check_id,source_url) values($pid,'$u')";
		$rows=$dbhu->do($sql);
	}
}
foreach my $u (@country)
{
	$sql="insert into UniqueCheckCountry(check_id,countryID) values($pid,$u)";
	$rows=$dbhu->do($sql);
}
foreach my $u (@ua)
{
	$sql="insert into UniqueCheckUA(check_id,userAgentStringLabelID) values($pid,$u)";
	$rows=$dbhu->do($sql);
}
if ($zips ne '')
{
	$zips =~ s/[ \n\r\f\t]/\|/g ;    
	$zips =~ s/\|{2,999}/\|/g ;           
	my @zip = split '\|', $zips;
	foreach my $u (@zip)
	{
		$sql="insert into UniqueCheckZip(check_id,zip) values($pid,'$u')";
		$rows=$dbhu->do($sql);
	}
}
if ($seeds ne '')
{
	$seeds =~ s/[ \n\r\f\t]/\|/g ;    
	$seeds =~ s/\|{2,999}/\|/g ;           
	my @em= split '\|', $seeds;
	foreach my $e (@em)
	{
		$sql="insert into UniqueCheckSeed(check_id,email_addr) values($pid,'$e')";
		$rows=$dbhu->do($sql);
	}
}
print "Location: /cgi-bin/uniqueprofile_checkmain.cgi?cid=$pid\n\n";
