#!/usr/bin/perl

# ******************************************************************************
# uniqueprofile_calc_save.cgi 
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
my $MAX_CLIENT=15;

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
# Remove old url information
#
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

my $rstart= $query->param('rstart');
my $rend= $query->param('rend');
my $ostart_date= $query->param('ostart_date');
my $oend_date= $query->param('oend_date');
my $cstart_date= $query->param('cstart_date');
my $cend_date= $query->param('cend_date');
my $dstart_date= $query->param('dstart_date');
my $dend_date= $query->param('dend_date');
my $send_international= $query->param('send_international');
if ($send_international eq "")
{
	$send_international="Y";
}
$sql="insert into UniqueCheck(check_date,opener_start,opener_end,clicker_start,clicker_end,deliverable_start,deliverable_end,send_international,opener_start_date,opener_end_date,clicker_start_date,clicker_end_date,deliverable_start_date,deliverable_end_date,type,user_id) values(curdate(),$ostart,$oend,$cstart,$cend,$dstart,$dend,'$send_international','$ostart_date','$oend_date','$cstart_date','$cend_date','$dstart_date','$dend_date','New',$user_id)";
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
my $i=1;
while ($i <= $MAX_CLIENT)
{
	my $tstr="client".$i;
	my $client= $query->param($tstr);
	if ($client ne "")
	{
		$tstr="opencount".$i;
		my $opencount= $query->param($tstr);
		$tstr="clickcount".$i;
		my $clickcount= $query->param($tstr);
		$tstr="count".$i;
		my $count= $query->param($tstr);
		$tstr="order".$i;
		my $order_type= $query->param($tstr);
		$sql="insert into UniqueCheckClient(check_id,client_id,record_cnt,record_order,open_record_cnt,click_record_cnt) values($pid,$client,$count,'$order_type',$opencount,$clickcount)";
		$rows=$dbhu->do($sql);
	}
	$i++;
}
print "Location: /cgi-bin/uniqueprofile_calcmain.cgi?cid=$pid\n\n";
