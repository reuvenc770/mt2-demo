#!/usr/bin/perl

# *****************************************************************************************
# uniqueprofile_copy_save.cgi
#
# this page copies an Unique Profile
#
# History
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
my $dbh;
my $pid= $query->param('pid');
my $pname= $query->param('pname');
my $new_id;
$pname=~s/'/''/g;
my $cid;
my $sql;
#------ connect to the util database ------------------
my ($dbhq,$dbhu)=$util->get_dbh();
$sql="insert into UniqueProfile(profile_name,opener_start,opener_end,clicker_start,clicker_end,deliverable_start,deliverable_end,deliverable_factor,complaint_control,cc_aol_send,cc_yahoo_send,cc_hotmail_send,cc_other_send,send_international,opener_start_date,opener_end_date,clicker_start_date,clicker_end_date,deliverable_start_date,deliverable_end_date,start_record,end_record,ramp_up_freq,subtract_days,add_days,max_end_date,opener_start1,opener_end1,clicker_start1,clicker_end1,deliverable_start1,deliverable_end1,opener_start2,opener_end2,clicker_start2,clicker_end2,deliverable_start2,deliverable_end2,send_confirmed,convert_start,convert_end,convert_start_date,convert_end_date,convert_start1,convert_end1,convert_start2,convert_end2,ramp_up_email_cnt,min_age,max_age,gender,status,DivideRangeByIsps,ProfileForClient,emailListCntOperator,emailListCnt,BusinessUnit) select '$pname',opener_start,opener_end,clicker_start,clicker_end,deliverable_start,deliverable_end,deliverable_factor,complaint_control,cc_aol_send,cc_yahoo_send,cc_hotmail_send,cc_other_send,send_international,opener_start_date,opener_end_date,clicker_start_date,clicker_end_date,deliverable_start_date,deliverable_end_date,start_record,end_record,ramp_up_freq,subtract_days,add_days,max_end_date,opener_start1,opener_end1,clicker_start1,clicker_end1,deliverable_start1,deliverable_end1,opener_start2,opener_end2,clicker_start2,clicker_end2,deliverable_start2,deliverable_end2,send_confirmed,convert_start,convert_end,convert_start_date,convert_end_date,convert_start1,convert_end1,convert_start2,convert_end2,ramp_up_email_cnt,min_age,max_age,gender,'A',DivideRangeByIsps,ProfileForClient,emailListCntOperator,emailListCnt,BusinessUnit from UniqueProfile where profile_id=$pid"; 
my $rows=$dbhu->do($sql);

$sql="select max(profile_id) from UniqueProfile where profile_name=? and status='A'";
$sth=$dbhu->prepare($sql);
$sth->execute($pname);
($new_id)=$sth->fetchrow_array();
$sth->finish();
#
$sql="insert into UniqueProfileIsp(profile_id,class_id) select $new_id,class_id from UniqueProfileIsp where profile_id=$pid"; 
$rows=$dbhu->do($sql);
$sql="insert into UniqueProfileUA(profile_id,userAgentStringLabelID) select $new_id,userAgentStringLabelID from UniqueProfileUA where profile_id=$pid"; 
$rows=$dbhu->do($sql);
$sql="insert into UniqueProfileCountry(profile_id,countryID) select $new_id,countryID from UniqueProfileCountry where profile_id=$pid"; 
$rows=$dbhu->do($sql);
$sql="insert into UniqueProfileUrl(profile_id,source_url) select $new_id,source_url from UniqueProfileUrl where profile_id=$pid"; 
$rows=$dbhu->do($sql);
$sql="insert into UniqueProfileZip(profile_id,zip) select $new_id,zip from UniqueProfileZip where profile_id=$pid"; 
$rows=$dbhu->do($sql);
$sql="insert into UniqueProfileCustom(profile_id,clientRecordKeyID,clientRecordValueID) select $new_id,clientRecordKeyID,clientRecordValueIDfrom UniqueProfileCustom where profile_id=$pid"; 
$rows=$dbhu->do($sql);

print "Location: uniqueprofile_edit.cgi?pid=$new_id\n\n";
