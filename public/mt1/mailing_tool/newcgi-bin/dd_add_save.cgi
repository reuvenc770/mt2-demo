#!/usr/bin/perl

# *****************************************************************************************
# dd_add_save.cgi
#
# this page adds a new custom daily deal setting 
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
my $dd_id = 1; 
my $ctype = $query->param('ctype');
my $dd_name = $query->param('dd_name');
my $cid = $query->param('cid');
my $new_id;
$dd_name=~s/'/''/g;
my $sql;
my $uid;

#------ connect to the util database ------------------
my ($dbhq,$dbhu)=$util->get_dbh();

$sql="insert into DailyDealSetting(name,settingType,customClientID) values('$dd_name','$ctype',$cid)";
my $rows=$dbhu->do($sql);

$sql="select max(dd_id) from DailyDealSetting where name=?";
$sth=$dbhu->prepare($sql);
$sth->execute($dd_name);
($new_id)=$sth->fetchrow_array();
$sth->finish();
#
$sql="insert into DailyDealSettingDetail(dd_id,class_id,group_id,domain,template_id,header_id,footer_id,seedlist,wikiID,mailingHeaderID,article_id,mail_from,hotmailDomain,ramp_up_freq,ramp_up_email_cnt,last_updated,send_cnt,cap_volume,dateCntUpdated,return_path) select $new_id,class_id,group_id,domain,template_id,header_id,footer_id,seedlist,wikiID,mailingHeaderID,article_id,mail_from,hotmailDomain,ramp_up_freq,ramp_up_email_cnt,curdate(),0,cap_volume,dateCntUpdated,return_path from DailyDealSettingDetail where dd_id=$dd_id";
$rows=$dbhu->do($sql);
$sql="insert into DailyDealSettingContentDomain select $new_id,class_id,domain_name from DailyDealSettingContentDomain where dd_id=$dd_id";
$rows=$dbhu->do($sql);
$sql="insert into DailyDealSettingCustom select $new_id,class_id,clientRecordKeyID,clientRecordValueID from DailyDealSettingCustom where dd_id=$dd_id";
$rows=$dbhu->do($sql);
$sql="insert into DailyDealSettingDetailIpGroup select $new_id,class_id,weekDay,group_id from DailyDealSettingDetailIpGroup where dd_id=$dd_id";
$rows=$dbhu->do($sql);

print "Location: dd_list.cgi?ctype=$ctype\n\n";
