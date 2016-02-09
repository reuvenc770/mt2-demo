#!/usr/bin/perl

# ******************************************************************************
# uniqueprofile_check_add.cgi 
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
my $cnt;
my $pid;
my ($ostart,$oend,$cstart,$cend,$dstart,$dend,$dfactor,$send_international,$ostart_date,$oend_date,$cstart_date,$cend_date,$dstart_date,$dend_date,$dfactor);
my $send_confirmed;

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
my $cid= $query->param('cid');
my $profile_name = $query->param('pname');
$sql="select opener_start1,opener_end1,clicker_start1,clicker_end1,deliverable_start1,deliverable_end1,deliverable_factor,send_international,opener_start_date,opener_end_date,clicker_start_date,clicker_end_date,deliverable_start_date,deliverable_end_date,send_confirmed from UniqueCheck where check_id=$cid";
$sth=$dbhu->prepare($sql);
$sth->execute();
($ostart,$oend,$cstart,$cend,$dstart,$dend,$dfactor,$send_international,$ostart_date,$oend_date,$cstart_date,$cend_date,$dstart_date,$dend_date,$send_confirmed)=$sth->fetchrow_array();
$sth->finish();
my $complaint_control='Disable';
my $cc_aol=0;
my $cc_yahoo=0;
my $cc_hotmail=0;
my $cc_other=0;
my $ramp_up_freq=0;
my $subtract_days=0;
my $add_days=0;
my $ramp_up_freq=0;

my $max_end_date=180;
if ($send_international eq "")
{
	$send_international="N";
}
if ($send_confirmed eq "")
{
	$send_confirmed="Y";
}
$sql="insert into UniqueProfile(profile_name,opener_start,opener_end,clicker_start,clicker_end,deliverable_start,deliverable_end,deliverable_factor,complaint_control,cc_aol_send,cc_yahoo_send,cc_hotmail_send,cc_other_send,send_international,opener_start_date,opener_end_date,clicker_start_date,clicker_end_date,deliverable_start_date,deliverable_end_date,ramp_up_freq,subtract_days,add_days,max_end_date,send_confirmed) values('$profile_name',$ostart,$oend,$cstart,$cend,$dstart,$dend,$dfactor,'$complaint_control',$cc_aol,$cc_yahoo,$cc_hotmail,$cc_other,'$send_international','$ostart_date','$oend_date','$cstart_date','$cend_date','$dstart_date','$dend_date',$ramp_up_freq,$subtract_days,$add_days,$max_end_date,'$send_confirmed')";
$rows=$dbhu->do($sql);
$sql="select max(profile_id) from UniqueProfile where profile_name='$profile_name'";
$sth=$dbhu->prepare($sql);
$sth->execute();
($pid)=$sth->fetchrow_array();
$sth->finish();

$sql="select distinct class_id from UniqueCheckIsp where check_id=$cid";
$sth=$dbhu->prepare($sql);
$sth->execute();
my $class_id;
while (($class_id)=$sth->fetchrow_array())
{
	$sql="insert into UniqueProfileIsp(profile_id,class_id) values($pid,$class_id)";
	$rows=$dbhu->do($sql);
}
print "Location: /cgi-bin/uniqueprofile_edit.cgi?pid=$pid\n\n";
