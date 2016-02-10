#!/usr/bin/perl

# ******************************************************************************
# uniqueprofile_checkv3_save.cgi 
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
my $ostart=0; 
my $oend=0; 
my $cstart=0; 
my $cend=0; 
my $dstart=0;
my $dend=0;
my $volume_desired= $query->param('volume_desired');
my $mcid= $query->param('mcid');
$mcid=~s/ //g;

my $convert_start=0;
my $convert_end=0;

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
my $zips= $query->param('zips');
my $gender= $query->param('gender');
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
$sql="insert into UniqueCheck(check_date,opener_start,opener_end,clicker_start,clicker_end,deliverable_start,deliverable_end,deliverable_factor,send_international,opener_start_date,opener_end_date,clicker_start_date,clicker_end_date,deliverable_start_date,deliverable_end_date,send_confirmed,convert_start,convert_end,convert_start_date,convert_end_date,source_url,volume_desired,type,client_group_id,gender,min_age,max_age,user_id) values(curdate(),$ostart,$oend,$cstart,$cend,$dstart,$dend,$dfactor,'$send_international','$ostart_date','$oend_date','$cstart_date','$cend_date','$dstart_date','$dend_date','$send_confirmed',$convert_start,$convert_end,'$convert_start_date','$convert_end_date','$source_url',$volume_desired,'v3',$cgroup_id,'$gender',$min_age,$max_age,$user_id)";
$rows=$dbhu->do($sql);
$sql="select max(check_id) from UniqueCheck"; 
$sth=$dbhu->prepare($sql);
$sth->execute();
($pid)=$sth->fetchrow_array();
$sth->finish();
my @isps= $query->param('isps');
foreach my $isp (@isps)
{
	$sql="insert into UniqueCheckIsp(check_id,class_id) values($pid,$isp)";
	$rows=$dbhu->do($sql);
}
if ($cgroup_id == 0)
{
	my @clients= $query->param('sel2');
	foreach my $client (@clients)
	{
		$sql="insert into UniqueCheckClient(check_id,client_id) values($pid,$client)";
		$rows=$dbhu->do($sql);
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
            $rows = $dbhu->do($sql);
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
print "Location: /cgi-bin/uniqueprofile_checkmainv2.cgi?cid=$pid\n\n";
