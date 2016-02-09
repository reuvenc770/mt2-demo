#!/usr/bin/perl

# ******************************************************************************
# uniqueprofile_save.cgi 
#
# this page updates information in the UniqueProfile table
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
my @url_array;
my $cnt;
my $curl;
my $temp_cnt;
my $btype;
my $nl_id;
my $BusinessUnit;

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
$sql="select BusinessUnit from UserAccounts where user_id=?"; 
$sth=$dbhu->prepare($sql);
$sth->execute($user_id);
($BusinessUnit)=$sth->fetchrow_array();
$sth->finish();
#
# Remove old url information
#
my $pid= $query->param('pid');
my $uid= $query->param('uid');
if ($uid eq "")
{
	$uid=0;
}
my $profile_name = $query->param('profile_name');
my $ostart= $query->param('ostart');
$ostart=$ostart || 0;
my $oend= $query->param('oend');
$oend=$oend || 0;
my $cstart= $query->param('cstart');
$cstart=$cstart || 0;
my $cend= $query->param('cend');
$cend=$cend || 0;
my $dstart= $query->param('dstart');
$dstart=$dstart || 0;
my $dend= $query->param('dend');
$dend=$dend || 0;
my $ostart1= $query->param('ostart1');
$ostart1=$ostart1|| 0;
my $oend1= $query->param('oend1');
$oend1=$oend1 || 0;
my $cstart1= $query->param('cstart1');
$cstart1=$cstart1 || 0;
my $cend1= $query->param('cend1');
$cend1=$cend1 || 0;
my $dstart1= $query->param('dstart1');
$dstart1=$dstart1 || 0;
my $dend1= $query->param('dend1');
$dend1 =$dend1 || 0;
my $ostart2= $query->param('ostart2');
$ostart2=$ostart2 || 0;
my $oend2= $query->param('oend2');
$oend2=$oend2 || 0;
my $cstart2= $query->param('cstart2');
$cstart2 =$cstart2 || 0;
my $cend2= $query->param('cend2');
$cend2=$cend2 || 0;
my $dstart2= $query->param('dstart2');
$dstart2 =$dstart2 || 0;
my $dend2= $query->param('dend2');
$dend2 =$dend2 || 0;
my $useLastCategory= $query->param('useLastCategory');
if ($useLastCategory eq "")
{
	$useLastCategory="N";
}

my $convert_start= $query->param('convert_start');
$convert_start = $convert_start || 0;
my $convert_end= $query->param('convert_end');
$convert_end = $convert_end || 0;
my $convert_start1= $query->param('convert_start1');
$convert_start1 = $convert_start1 || 0;
my $convert_end1= $query->param('convert_end1');
$convert_end1 = $convert_end1 || 0;
my $convert_start2= $query->param('convert_start2');
$convert_start2 = $convert_start2 || 0;
my $convert_end2= $query->param('convert_end2');
$convert_end2 = $convert_end2 || 0;

my $rstart= $query->param('rstart');
my $rend= $query->param('rend');
my $ramp_up_freq= $query->param('ramp_up_freq');
if ($ramp_up_freq eq "")
{
	$ramp_up_freq=0;
}
my $ramp_up_email_cnt= $query->param('ramp_up_email_cnt');
if ($ramp_up_email_cnt eq "")
{
	$ramp_up_email_cnt=0;
}
my $subtract_days= $query->param('subtract_days');
if ($subtract_days eq "")
{
	$subtract_days=0;
}
my $add_days= $query->param('add_days');
if ($add_days eq "")
{
	$add_days=0;
}
my $max_end_date = $query->param('max_end_date');
if ($max_end_date eq "")
{
	$max_end_date=180;
}
my $ostart_date= $query->param('ostart_date');
my $oend_date= $query->param('oend_date');
my $cstart_date= $query->param('cstart_date');
my $cend_date= $query->param('cend_date');
my $dstart_date= $query->param('dstart_date');
my $dend_date= $query->param('dend_date');
my $convert_start_date= $query->param('convert_start_date');
my $convert_end_date= $query->param('convert_end_date');
my $dfactor = $query->param('dfactor');
my $complaint_control= $query->param('complaint_control');
my $cc_aol= $query->param('cc_aol');
my $cc_yahoo= $query->param('cc_yahoo');
my $cc_hotmail= $query->param('cc_hotmail');
my $cc_other = $query->param('cc_other');
my $send_international= $query->param('send_international');
my $DivideRangeByIsps= $query->param('DivideRangeByIsps');
my $surl= $query->param('surl');
my $zips= $query->param('zips');
my $sids= $query->param('sids');
my $cakeids= $query->param('cakeids');
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
if ($send_international eq "")
{
	$send_international="N";
}
if ($DivideRangeByIsps eq "")
{
	$DivideRangeByIsps="N";
}
my @MULTI=$query->param('multitype');
my $multitype="";
foreach my $m (@MULTI)
{
	$multitype=$multitype.$m.",";
}
chop($multitype);
my $multi_start=$query->param('multi_start');
if ($multi_start eq "")
{
	$multi_start=0;
}
my $multi_end=$query->param('multi_end');
if ($multi_end eq "")
{
	$multi_end=0;
}
my $multi_cnt=$query->param('multi_cnt');
if ($multi_cnt eq "")
{
	$multi_cnt=0;
}
my $DeliveryDays=$query->param('DeliveryDays');
if ($DeliveryDays eq "")
{
	$DeliveryDays=0;
}
my $emailListCntOperator=$query->param('emailListCntOperator');
my $emailListCnt=$query->param('emailListCnt');
if ($emailListCnt eq "")
{
	$emailListCnt=0;
}
my $groupSuppListID=$query->param('groupSuppListID');
if ($groupSuppListID eq "")
{
	$groupSuppListID=0;
}
my @category=$query->param('category');
if ($pid == 0)
{
	my $rfld="";
	my $rval="";
	$rfld="start_record";
	if ($rstart ne '') 
	{
		$rval="$rstart";
	}
	else
	{
		$rval="null";
	}
	$rfld=$rfld.",end_record";
	if ($rend ne '') 
	{
		$rval=$rval.",$rend";
	}
	else
	{
		$rval=$rval.",null";
	}
	$sql="insert into UniqueProfile(profile_name,opener_start,opener_end,clicker_start,clicker_end,deliverable_start,deliverable_end,deliverable_factor,complaint_control,cc_aol_send,cc_yahoo_send,cc_hotmail_send,cc_other_send,send_international,opener_start_date,opener_end_date,clicker_start_date,clicker_end_date,deliverable_start_date,deliverable_end_date,ramp_up_freq,subtract_days,add_days,max_end_date,opener_start1,opener_end1,clicker_start1,clicker_end1,deliverable_start1,deliverable_end1,opener_start2,opener_end2,clicker_start2,clicker_end2,deliverable_start2,deliverable_end2,convert_start,convert_end,convert_start_date,convert_end_date,convert_start1,convert_end1,convert_start2,convert_end2,ramp_up_email_cnt,gender,min_age,max_age,multitype,multi_start,multi_end,multi_cnt,DeliveryDays,DivideRangeByIsps,ProfileForClient,emailListCntOperator,emailListCnt,groupSuppListID,useLastCategory,BusinessUnit,$rfld) values('$profile_name',$ostart,$oend,$cstart,$cend,$dstart,$dend,$dfactor,'$complaint_control',$cc_aol,$cc_yahoo,$cc_hotmail,$cc_other,'$send_international','$ostart_date','$oend_date','$cstart_date','$cend_date','$dstart_date','$dend_date',$ramp_up_freq,$subtract_days,$add_days,$max_end_date,$ostart1,$oend1,$cstart1,$cend1,$dstart1,$dend1,$ostart2,$oend2,$cstart2,$cend2,$dstart2,$dend2,$convert_start,$convert_end,'$convert_start_date','$convert_end_date',$convert_start1,$convert_end1,$convert_start2,$convert_end2,$ramp_up_email_cnt,'$gender',$min_age,$max_age,'$multitype',$multi_start,$multi_end,$multi_cnt,$DeliveryDays,'$DivideRangeByIsps',$uid,'$emailListCntOperator',$emailListCnt,$groupSuppListID,'$useLastCategory','$BusinessUnit',$rval)";
	$rows=$dbhu->do($sql);
	$sql="select max(profile_id) from UniqueProfile where profile_name='$profile_name'";
	$sth=$dbhu->prepare($sql);
	$sth->execute();
	($pid)=$sth->fetchrow_array();
	$sth->finish();
}
else
{
	my $rsql="";
	if ($rstart ne '') 
	{
		$rsql=",start_record=$rstart";
	}
	else
	{
		$rsql=",start_record=null";
	}
	if ($rend ne '') 
	{
		$rsql=$rsql.",end_record=$rend";
	}
	else
	{
		$rsql=$rsql.",end_record=null";
	}
	$sql="update UniqueProfile set profile_name='$profile_name',opener_start=$ostart,opener_end=$oend,clicker_start=$cstart,clicker_end=$cend,deliverable_start=$dstart,deliverable_end=$dend,opener_start1=$ostart1,opener_end1=$oend1,clicker_start1=$cstart1,clicker_end1=$cend1,deliverable_start1=$dstart1,deliverable_end1=$dend1,opener_start2=$ostart2,opener_end2=$oend2,clicker_start2=$cstart2,clicker_end2=$cend2,deliverable_start2=$dstart2,deliverable_end2=$dend2,deliverable_factor=$dfactor,complaint_control='$complaint_control',cc_aol_send=$cc_aol,cc_yahoo_send=$cc_yahoo,cc_hotmail_send=$cc_hotmail,cc_other_send=$cc_other,send_international='$send_international',opener_start_date='$ostart_date',opener_end_date='$oend_date',clicker_start_date='$cstart_date',clicker_end_date='$cend_date',deliverable_start_date='$dstart_date',deliverable_end_date='$dend_date',ramp_up_freq=$ramp_up_freq,ramp_up_email_cnt=$ramp_up_email_cnt,subtract_days=$subtract_days,max_end_date=$max_end_date,convert_start=$convert_start,convert_end=$convert_end,convert_start_date='$convert_start_date',convert_end_date='$convert_end_date',convert_start1=$convert_start1,convert_end1=$convert_end1,convert_start2=$convert_start2,convert_end2=$convert_end2,gender='$gender',min_age=$min_age,max_age=$max_age,add_days=$add_days,multitype='$multitype',multi_start=$multi_start,multi_end=$multi_end,multi_cnt=$multi_cnt,DeliveryDays=$DeliveryDays,DivideRangeByIsps='$DivideRangeByIsps',emailListCntOperator='$emailListCntOperator',emailListCnt=$emailListCnt,groupSuppListID=$groupSuppListID,useLastCategory='$useLastCategory',BusinessUnit='$BusinessUnit'".$rsql." where profile_id=$pid";
	$rows=$dbhu->do($sql);
	$sql="delete from UniqueProfileIsp where profile_id=$pid";
	$rows=$dbhu->do($sql);
	$sql="delete from UniqueProfileUrl where profile_id=$pid";
	$rows=$dbhu->do($sql);
	$sql="delete from UniqueProfileZip where profile_id=$pid";
	$rows=$dbhu->do($sql);
	$sql="delete from UniqueProfileCategory where profile_id=$pid";
	$rows=$dbhu->do($sql);
	$sql="delete from UniqueProfileSid where profile_id=$pid";
	$rows=$dbhu->do($sql);
	$sql="delete from UniqueProfileCustom where profile_id=$pid";
	$rows=$dbhu->do($sql);
	$sql="delete from UniqueProfileCountry where profile_id=$pid";
	$rows=$dbhu->do($sql);
	$sql="delete from UniqueProfileUA where profile_id=$pid";
	$rows=$dbhu->do($sql);
}
my @isps= $query->param('isps');
foreach my $isp (@isps)
{
	$sql="insert into UniqueProfileIsp(profile_id,class_id) values($pid,$isp)";
	$rows=$dbhu->do($sql);
}
my @country= $query->param('country');
foreach my $c (@country)
{
	$sql="insert into UniqueProfileCountry(profile_id,countryID) values($pid,$c)";
	$rows=$dbhu->do($sql);
}
my @ua= $query->param('ua');
foreach my $uaID (@ua)
{
	$sql="insert into UniqueProfileUA(profile_id,userAgentStringLabelID) values($pid,$uaID)";
	$rows=$dbhu->do($sql);
}
#if ($uid > 0)
#{
	my @cdata= $query->param('cdata');
	foreach my $keyval (@cdata)
	{
		my ($key,$val)=split('\|',$keyval);
		$sql="insert into UniqueProfileCustom(profile_id,clientRecordKeyID,clientRecordValueID) values($pid,$key,$val)";
		$rows=$dbhu->do($sql);
	}
#}
if ($surl ne '')
{
	$surl =~ s/[ \n\r\f\t]/\|/g ;    
	$surl =~ s/\|{2,999}/\|/g ;           
	my @url= split '\|', $surl;
	foreach my $u (@url)
	{
		$sql="insert into UniqueProfileUrl(profile_id,source_url) values($pid,'$u')";
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
		$sql="insert into UniqueProfileZip(profile_id,zip) values($pid,'$u')";
		$rows=$dbhu->do($sql);
	}
}
my $gotcat=0;
foreach my $c (@category)
{
	$gotcat++;
	$sql="insert into UniqueProfileCategory(profile_id,category_id) values($pid,$c)";
	$rows=$dbhu->do($sql);
}
if (($sids ne '') and ($gotcat == 0))
{
	$sids =~ s/[ \n\r\f\t]/\|/g ;    
	$sids =~ s/\|{2,999}/\|/g ;           
	my @sid = split '\|', $sids;
	foreach my $u (@sid)
	{
		$sql="insert into UniqueProfileSid(profile_id,sid) values($pid,$u)";
		$rows=$dbhu->do($sql);
	}
}
if (($cakeids ne '') and ($gotcat == 0))
{
	$cakeids =~ s/[ \n\r\f\t]/\|/g ;    
	$cakeids =~ s/\|{2,999}/\|/g ;           
	my @cakeid = split '\|', $cakeids;
	foreach my $u (@cakeid)
	{
		$sql="insert into UniqueProfileCakeCreativeID(profile_id,cake_creativeID) values($pid,$u)";
		$rows=$dbhu->do($sql);
	}
}
print "Location: /cgi-bin/uniqueprofile_list.cgi\n\n";
