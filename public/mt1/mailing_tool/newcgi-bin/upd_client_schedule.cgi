#!/usr/bin/perl

# *****************************************************************************************
# upd_client_schedule.cgi
#
# History
# Jim Sobeck, 06/29/05, creation
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $sth1;
my $sth2;
my $sql;
my $dbh;
my $company;
my $shour;
my $smin;
my $images = $util->get_images_url;
my ($slot_id,$stime,$profile_id,$brand_id,$cstatus); 
my $mta_id;
my $performance;
my $log_camp;
my $source_url;
my $vsgID;
my $temp_profile_id;
my $third_id;
my $temp_tid;
my $mailer_name;
my $profile_name;
my $max_id;
my $max_emails;
my $aol_flag;
my $hotmail_flag;
my $yahoo_flag;
my $other_flag;
my $rows;
my $special_flag;
my @type_arr= (
    ["3rd Party","3"],
    ["Daily","D"] );
my $pid;
my $pname;
my $host_cnt;

# connect to the util database
my $dbhq;
my $dbhu;
($dbhq,$dbhu)=$util->get_dbh();

$special_flag=0;
my $cid = $query->param('client_id');
if ($cid eq "0")
{
	print "Location: /blank.html\n\n";
	exit(0);
}
my $daily_cnt= $query->param('daily_cnt');
my $camp_cnt= $query->param('camp_cnt');
my $aol_cnt= $query->param('aol_cnt');
my $rotate_cnt= $query->param('rotating_cnt');
my $third_cnt= $query->param('third_cnt');
if ($third_cnt eq "")
{
	$special_flag=1;
}
my $chunk_cnt= $query->param('chunk_cnt') || 0;
my $reccnt;
$sql = "select count(*) from network_schedule where client_id=$cid";
$sth = $dbhq->prepare($sql) ;
$sth->execute();
($reccnt) = $sth->fetchrow_array();
$sth->finish();
if (($third_cnt == 0) and ($special_flag == 0))
{
	print "Content-type:text/html\n\n";
print<<"end_of_html";
<html><head></head>
<body>
<center><h4>Schedule not updated because 3rd Party count set to zero</h4></center>
</body>
</html>
end_of_html
exit();
}
if ($special_flag == 0)
{
	if ($reccnt > 0)
	{
		$sql = "update network_schedule set campaign_cnt=$camp_cnt,aol_cnt=$aol_cnt,daily_cnt=$daily_cnt,rotating_cnt=$rotate_cnt,3rdparty_cnt=$third_cnt,chunk_cnt=$chunk_cnt where client_id=$cid";
	}
	else
	{
		$sql="insert into network_schedule(client_id,campaign_cnt,aol_cnt,daily_cnt,rotating_cnt,3rdparty_cnt,chunk_cnt) values($cid,$camp_cnt,$aol_cnt,$daily_cnt,$rotate_cnt,$third_cnt,$chunk_cnt)";
	}
	$rows=$dbhu->do($sql);
}
else
{
	$sql="select campaign_cnt,aol_cnt,daily_cnt,rotating_cnt,3rdparty_cnt,chunk_cnt from network_schedule where client_id=$cid";
	$sth=$dbhu->prepare($sql);
	$sth->execute();
	($camp_cnt,$aol_cnt,$daily_cnt,$rotate_cnt,$third_cnt,$chunk_cnt)=$sth->fetchrow_array();
	$sth->finish();
}

#
##  query all vsgID combinations for later use ##
### put a check on mail-able IP w/ mail=1 on ip_config table 7/19 ###
#my $hrVSG={};
#my $qSel="SELECT sic.ip, bi.ip FROM server_config sc, server_ip_config sic, client_brand_info c,brand_ip bi WHERE c.brand_id=bi.brandID AND bi.ip=sic.ip and c.client_id=? and sc.id=sic.id AND sc.inService=1 AND sc.type='strmail' AND sic.mail=1 ORDER BY sic.ip ASC, bi.ip ASC";
#$sth = $dbhq->prepare($qSel);
#$sth->execute($cid);
#while (my ($list_vsgID, $ip)=$sth->fetchrow_array) {
#	$hrVSG->{$list_vsgID}{$ip}=1;
#}
#$sth->finish;
##
$sql = "select company from user where user_id=$cid";
$sth = $dbhq->prepare($sql) ;
$sth->execute();
($company) = $sth->fetchrow_array();
$sth->finish();
#
$sql = "select max(slot_id) from schedule_info where client_id=$cid and slot_type='C'";
$sth = $dbhq->prepare($sql) ;
$sth->execute();
($max_id) = $sth->fetchrow_array();
$sth->finish();
if ($max_id eq "")
{
	$max_id=0;
}
$sql = "delete from schedule_info where client_id=$cid and slot_type='C' and slot_id > $camp_cnt";
$rows=$dbhu->do($sql);
$max_id++;
while ($max_id <= $camp_cnt)
{
	$sql = "insert into schedule_info(client_id,slot_id,slot_type) values($cid,$max_id,'C')";
	$rows=$dbhu->do($sql);
	$max_id++;
}
#
$sql = "select max(slot_id) from schedule_info where client_id=$cid and slot_type='3'";
$sth = $dbhq->prepare($sql) ;
$sth->execute();
($max_id) = $sth->fetchrow_array();
$sth->finish();
$sql = "delete from schedule_info where client_id=$cid and slot_type='3' and slot_id > $third_cnt";
$rows=$dbhu->do($sql);
$max_id++;
while ($max_id <= $third_cnt)
{
	$sql = "insert into schedule_info(client_id,slot_id,slot_type) values($cid,$max_id,'3')";
	$rows=$dbhu->do($sql);
	$max_id++;
}
$sql = "select max(slot_id) from schedule_info where client_id=$cid and slot_type='A'";
$sth = $dbhq->prepare($sql) ;
$sth->execute();
($max_id) = $sth->fetchrow_array();
$sth->finish();
if ($max_id eq "")
{
	$max_id=0;
}
$sql = "delete from schedule_info where client_id=$cid and slot_type='A' and slot_id > $aol_cnt";
$rows=$dbhu->do($sql);
$max_id++;
while ($max_id <= $aol_cnt)
{
	$sql = "insert into schedule_info(client_id,slot_id,slot_type) values($cid,$max_id,'A')";
	$rows=$dbhu->do($sql);
	$max_id++;
}
$sql = "select max(slot_id) from schedule_info where client_id=$cid and slot_type='W'";
$sth = $dbhq->prepare($sql) ;
$sth->execute();
($max_id) = $sth->fetchrow_array();
$sth->finish();
if ($max_id eq "")
{
	$max_id=0;
}
$sql = "delete from schedule_info where client_id=$cid and slot_type='W' and slot_id > $chunk_cnt";
$rows=$dbhu->do($sql);
$max_id++;
while ($max_id <= $chunk_cnt)
{
	$sql = "insert into schedule_info(client_id,slot_id,slot_type) values($cid,$max_id,'W')";
	$rows=$dbhu->do($sql);
	$max_id++;
}
$sql = "select max(slot_id) from schedule_info where client_id=$cid and slot_type='D'";
$sth = $dbhq->prepare($sql) ;
$sth->execute();
($max_id) = $sth->fetchrow_array();
$sth->finish();
if ($max_id eq "")
{
	$max_id=0;
}
$sql = "delete from schedule_info where client_id=$cid and slot_type='D' and slot_id > $daily_cnt";
$rows=$dbhu->do($sql);
#
# get daily deals
#
$sql="select campaign_id from camp_schedule_info where slot_type='D' and client_id=$cid and slot_id > $daily_cnt and status='A'";
$sth=$dbhq->prepare($sql);
$sth->execute();
my $tcamp;
while (($tcamp)=$sth->fetchrow_array())
{
	$sql="update campaign set deleted_date=curdate() where campaign_id=$tcamp";
	$rows=$dbhu->do($sql);
	$sql="delete from daily_deals where client_id=$cid and campaign_id=$tcamp";
	$rows=$dbhu->do($sql);
	$sql="update camp_schedule_info set status='D' where client_id=$cid and campaign_id=$tcamp";
	$rows=$dbhu->do($sql);
}
$sth->finish();
$max_id++;
while ($max_id <= $daily_cnt)
{
	$sql = "insert into schedule_info(client_id,slot_id,slot_type) values($cid,$max_id,'D')";
	$rows=$dbhu->do($sql);
	$max_id++;
}
$sql = "select max(slot_id) from schedule_info where client_id=$cid and slot_type='R'";
$sth = $dbhq->prepare($sql) ;
$sth->execute();
($max_id) = $sth->fetchrow_array();
$sth->finish();
if ($max_id eq "")
{
	$max_id=0;
}
$sql = "delete from schedule_info where client_id=$cid and slot_type='R' and slot_id > $rotate_cnt";
$rows=$dbhu->do($sql);
$max_id++;
while ($max_id <= $rotate_cnt)
{
	$sql = "insert into schedule_info(client_id,slot_id,slot_type) values($cid,$max_id,'R')";
	$rows=$dbhu->do($sql);
	$max_id++;
}
#
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>
<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Schedule Details</title>
</head>
<script language="JavaScript">
function addbrand(sid,value,text)
{
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    var selObj = document.getElementById('bid_3_'+sid);
    selObj.add(newOpt);
}
function update_brand(slot_id)
{
    var selObj = document.getElementById('pid_3_'+slot_id);
    var brandObj = document.getElementById('bid_3_'+slot_id);
    var selIndex = selObj.selectedIndex;
    var selLength = brandObj.length;
    while (selLength>0)
    {
        brandObj.remove(selLength-1);
        selLength--;
    }
    brandObj.length=0;
    parent.frames[2].location="/newcgi-bin/sch_update_brand.cgi?pid="+selObj.options[selIndex].value+"&sid="+slot_id+"&cid=$cid";
}
</script>
<body>
<form form=campform method=post action="/cgi-bin/sav_client_schedule.cgi">
<input type=hidden name=cid value=$cid>
<input type=hidden name=special_flag value=$special_flag>
<!-- <table border="0" width="35%" id="table7">
	<tr>
		<td><b><font face="Verdana">$company: </font></b></td>
		<td>
		&nbsp;</td>
	</tr>
</table> -->
<table border="0" width="35%" id="table25">
	<tr>
		<td><b><font face="Verdana"><a href="/cgi-bin/listprofile_list.cgi?client_id=$cid" target="_blank">List Profiles</a></font></b></td>
		<td>
		&nbsp;</td>
	</tr>
</table>
<table border="0" width="35%" id="table26">
	<tr>
		<td>&nbsp;</td>
		<td>
		&nbsp;</td>
	</tr>
</table>
end_of_html
my $i = 0;
while ($i <= $#type_arr)
{
print<<"end_of_html";
<table border="0" width="35%" id="table4">
	<tr>
		<td><b><font face="Verdana">&nbsp;$type_arr[$i][0]:</font></b></td>
		<td width="205">
		<p align="right"><b><font face="Verdana">&nbsp;</font></b></td>
	</tr>
</table>
<table border="1" width="54%" id="table1">
end_of_html
if ($type_arr[$i][1] eq "3")
{
print<<"end_of_html";
	<tr><td width="9"><b><font face="Verdana" size="1">#</font></b></td>
		<td width="200"><b><font face="Verdana" size="1">Time:</font></b></td>
		<td width="239"><b><font face="Verdana" size="1">3rd Party/Profile:</font></b></td>
		<td width="180"><b><font face="Verdana" size="1">Brand</font></b></td>
		<td width="180"><b><font face="Verdana" size="1">MTA Setting</font></b></td>
		<td width="180"><b><font face="Verdana" size="1">Performance</font></b></td>
		<td width="180"><b><font face="Verdana" size="1">Logging</font></b></td>
<!--		<td width="180"><b><font face="Verdana" size="1">VSG</font></b></td>-->
		<td width="180"><b><font face="Verdana" size="1"></font></b></td>
	</tr>
end_of_html
}
else
{
print<<"end_of_html";
	<tr><td width="9"><b><font face="Verdana" size="1">#</font></b></td>
		<td width="239"><b><font face="Verdana" size="1">Daily Deal Setting</font></b></td>
		<td width="180"><b><font face="Verdana" size="1">Brand</font></b></td>
		<td width="88"><b><font face="Verdana" size="1">Performance</font></b></td>
		<td width="88"><b><font face="Verdana" size="1">Logging</font></b></td>
		<td width="239"><b><font face="Verdana" size="1">Source URL</font></b></td>
	</tr>
end_of_html
}
$sql = "select slot_id,hour(schedule_time),minute(schedule_time),profile_id,brand_id,vsgID, status,third_party_id,mta_id,performance,log_campaign,source_url from schedule_info where client_id=$cid and slot_type='$type_arr[$i][1]' order by slot_id";
$sth = $dbhq->prepare($sql) ;
$sth->execute();
while (($slot_id,$stime,$smin,$profile_id,$brand_id,$vsgID, $cstatus,$third_id,$mta_id,$performance,$log_camp,$source_url) = $sth->fetchrow_array())
{
print<<"end_of_html";
	<tr>
		<td width="9" align="center"><b><font face="Verdana" size="1">$slot_id</font></b></td>
end_of_html
if ($type_arr[$i][1] eq "3")
{
print<<"end_of_html";
		<td width="200">
									<select name="tid_${type_arr[$i][1]}_${slot_id}">
end_of_html
	my $j=1;
	if ($stime > 12)
	{
		$shour = $stime - 12;
	}
	else
	{
		$shour = $stime;
	}
	if ($stime == 0)
	{
		$shour=12;
	}
	while ($j <= 12)
	{
		if ($j == $shour)
		{
			print "<option selected value=$j>$j</option>\n";
		}
		else
		{
			print "<option value=$j>$j</option>\n";
		}
		$j++;
	}
print<<"end_of_html";
	</select>
   <select name="tmin_${type_arr[$i][1]}_${slot_id}">
end_of_html
	my $j=0;
	while ($j <= 59)
	{
		if ($j == $smin)
		{
			print "<option selected value=$j>$j</option>\n";
		}
		else
		{
			print "<option value=$j>$j</option>\n";
		}
		$j++;
	}
	print "</select><select name=am_pm_${type_arr[$i][1]}_${slot_id}>\n";
	if ($stime >= 13)
	{
		print "<option value=\"AM\">AM</option>\n";
		print "<option value=\"PM\" selected>PM</option>\n";
	}
	else
	{
		print "<option value=\"AM\" selected>AM</option>\n";
		print "<option value=\"PM\">PM</option>\n";
	}
print<<"end_of_html";
									</select></td>
end_of_html
}
print<<"end_of_html";
		<td>
end_of_html
if ($type_arr[$i][1] ne "3")
{
print<<"end_of_html";
									<select name="pid_${type_arr[$i][1]}_${slot_id}" size="1">
end_of_html
}
else
{
print<<"end_of_html";
									<select name="pid_${type_arr[$i][1]}_${slot_id}" size="1" onChange="update_brand($slot_id);">
end_of_html
}
if ($type_arr[$i][1] eq "D")
{
	$sql="select dd_id,name from DailyDealSetting where settingType='Daily' order by name"; 
	$sth1 = $dbhq->prepare($sql) ;
	$sth1->execute();
	my $reccnt_str="";
	while (($pid,$pname) = $sth1->fetchrow_array())
	{
		if ($pid == $mta_id)
		{
			print "<option value=$pid selected>$pname</option>\n";
		}
		else
		{
			print "<option value=$pid>$pname</option>\n";
		}
	}
	$sth1->finish();
}
else
{
	$sql="select distinct third_party_defaults.third_party_id,mailer_name,profile_name,profile_id from third_party_defaults,list_profile where third_party_defaults.third_party_id=list_profile.third_party_id and client_id=$cid and list_profile.status='A' and profile_type='3RDPARTY' order by mailer_name,profile_name";
	$sth2 = $dbhq->prepare($sql) ;
	$sth2->execute();
	while (($temp_tid,$mailer_name,$profile_name,$temp_profile_id) = $sth2->fetchrow_array())
	{
		if (($temp_tid == $third_id) && ($temp_profile_id == $profile_id))
		{
			print "<option selected value=$temp_tid|$temp_profile_id>$mailer_name - $profile_name</option>\n";
		}
		else
		{
			print "<option value=$temp_tid|$temp_profile_id>$mailer_name - $profile_name</option>\n";
		}		
	}
	$sth2->finish();
}
print<<"end_of_html";
									</select></td>
		<td>
									<select name="bid_${type_arr[$i][1]}_${slot_id}" size="1">
end_of_html
my $other_cnt;
my $yahoo_cnt;
if ($type_arr[$i][1] eq "D")
{
		$sql="select brand_id,brand_name from client_brand_info where client_id in (1,$cid) and status='A' and Purpose='Daily' order by brand_name";
}
else
{
	$sql="select brand_id,brand_name from client_brand_info where client_id=$cid and status='A' and third_party_id=$third_id order by brand_name";
}
$sth1 = $dbhq->prepare($sql) ;
$sth1->execute();
while (($pid,$pname) = $sth1->fetchrow_array())
{
#	$sql="select count(distinct server_name) from brand_host where brand_id=$pid and server_type in ('O','Y')";
#	$sth2 = $dbhq->prepare($sql) ;
#	$sth2->execute();
#	($host_cnt) = $sth2->fetchrow_array();
#	$sth2->finish();
#	$sql="select count(distinct server_name) from brand_host where brand_id=$pid and server_type='O' and server_name not in (select server_name from brand_host where brand_id=$pid and server_type ='Y')";
#	$sth2 = $dbhq->prepare($sql) ;
#	$sth2->execute();
#	($other_cnt) = $sth2->fetchrow_array();
#	$sth2->finish();
#	$sql="select count(distinct server_name) from brand_host where brand_id=$pid and server_type='Y' and server_name not in (select server_name from brand_host where brand_id=$pid and server_type ='O')";
#	$sth2 = $dbhq->prepare($sql) ;
#	$sth2->execute();
#	($yahoo_cnt) = $sth2->fetchrow_array();
#	$sth2->finish();
#	my $tcnt = $other_cnt + $yahoo_cnt;
#	if ($tcnt < $host_cnt)
#	{
#		$other_cnt = $other_cnt + ($host_cnt-$tcnt)/2.0;
#		$yahoo_cnt = $yahoo_cnt + ($host_cnt-$tcnt)/2.0;
#	}
	if ($pid == $brand_id)
	{
		if ($type_arr[$i][1] ne "3")
		{
			print "<option value=$pid selected>$pname:&nbsp;&nbsp;O - $other_cnt&nbsp;&nbsp;&nbsp;&nbsp;Y - $yahoo_cnt</option>\n";
		}
		else
		{		
			print "<option value=$pid selected>$pname</option>\n";
		}
	}
	else
	{
		if ($type_arr[$i][1] ne "3")
		{
			print "<option value=$pid>$pname:&nbsp;&nbsp;O - $other_cnt&nbsp;&nbsp;&nbsp;&nbsp;Y - $yahoo_cnt</option>\n";
		}
		else
		{		
			print "<option value=$pid >$pname</option>\n";
		}
	}
}
$sth1->finish();
print<<"end_of_html";
									</select></td>
end_of_html

if ($type_arr[$i][1] eq "3") {
	print qq^	<td>
									<select name="mta_${type_arr[$i][1]}_${slot_id}" size="1">^;
#										<option value=''> -- </option>\n^;
#	foreach my $list_vsgID (sort keys %$hrVSG) {
#		my $selected=($vsgID eq $list_vsgID) ? 'SELECTED' : '';
#		print qq^<option value='$list_vsgID' $selected>$list_vsgID [all IPs]</option>\n^;
#		foreach my $ip (sort keys %{$hrVSG->{$list_vsgID}}) {
#			my $selected=($vsgID eq "$list_vsgID-$ip") ? 'SELECTED' : '';
#			print qq^<option value='$list_vsgID-$ip' $selected>$list_vsgID-$ip</option>\n^;
#		}
#	}
#	$sth1->finish;
$sql="select mta_id,name from mta order by name";
$sth1 = $dbhq->prepare($sql) ;
$sth1->execute();
while (($pid,$pname) = $sth1->fetchrow_array())
{
	if ($pid == $mta_id)
	{
		print "<option selected value=$pid>$pname</option>\n";
	}
	else
	{
		print "<option value=$pid>$pname</option>\n";
	}
}
$sth1->finish();
	print qq^		</select>
		</td>\n^;
}

my @PCOLORS=("#FFFFFF","#FF99CC","#FFCC99","#FFFF66","#99FFCC","#CCCCCC","#CC0000");
print "<td><select name=\"perf_${type_arr[$i][1]}_${slot_id}\" size=1>\n";
my $i1=0;
while ($i1 <= $#PCOLORS)
{
	if ($i1 == 0)
	{
		if ($performance == $i1)
		{
			print "<option selected value=0 style=\"background: $PCOLORS[$i1]\"></font></option>\n";
		}
		else
		{
			print "<option value=0 style=\"background: $PCOLORS[$i1]\"></option>\n";
		}
	}
	else
	{
		if ($performance == $i1)
		{
			print "<option selected value=$i1 style=\"background: $PCOLORS[$i1]\"><b>$i1</b></option>\n";
		}
		else
		{
			print "<option value=$i1 style=\"background: $PCOLORS[$i1]\"><b>$i1</b></option>\n";
		}
	}
	$i1++;
}
print "</select></td>";

my @LOG=("Off","On");
print "<td><select name=\"log_${type_arr[$i][1]}_${slot_id}\" size=1>\n";
my $i1=0;
while ($i1 <= $#LOG)
{
	if ($log_camp eq $LOG[$i1])
	{
		print "<option selected value=$LOG[$i1]></font>$LOG[$i1]</option>\n";
	}
	else
	{
		print "<option value=$LOG[$i1]></font>$LOG[$i1]</option>\n";
	}
	$i1++;
}
print "</select></td>";
if ($type_arr[$i][1] eq "D") {
print "<td><input type=text size=30 maxlength=255 name=\"url_${type_arr[$i][1]}_${slot_id}\" value=\"$source_url\"></td>\n";
}
if ($cstatus eq "A")
{
	print "<input type=hidden name=stat_${type_arr[$i][1]}_${slot_id} value=\"A\">\n";
	print "<td align=center><a href=\"/cgi-bin/delete_slot.cgi?cid=$cid&sid=$slot_id&flag=D&stype=${type_arr[$i][1]}\">Delete</a></td>\n";
}
else
{
	print "<input type=hidden name=stat_${type_arr[$i][1]}_${slot_id} value=\"D\">\n";
	print "<td align=center><a href=\"/cgi-bin/delete_slot.cgi?cid=$cid&sid=$slot_id&flag=A&stype=${type_arr[$i][1]}\">UnDelete</a></td>\n";
}
print<<"end_of_html";
	</tr>
end_of_html
}
	print "</table>";
$sth->finish();
$i++;
}
print<<"end_of_html";
<p align="left">
						<a href="/blank.html">
						<img src="/images/cancel.gif" border="0"></a><img height="1" src="/images/spacer.gif" width="40" border="0">
						<input type="image" src="/images/save.gif" border="0" name="I1"></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
</p>
</form>
</body>
</html>
end_of_html
#
$util->clean_up();
exit(0);

sub commify
{
    my $text = reverse $_[0];
    $text =~ s/(\d\d\d)(?=\d)(?!\d*\.)/$1,/g;
    return scalar reverse $text;
}
