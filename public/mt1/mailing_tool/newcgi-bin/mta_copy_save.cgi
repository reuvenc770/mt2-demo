#!/usr/bin/perl

# *****************************************************************************************
# mta_copy_save.cgi
#
# this page copies an mta setting 
#
# History
# Jim Sobeck, 03/28/08, Creation
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
my $mta_id = $query->param('mta_id');
my $mta_name = $query->param('mta_name');
my $new_id;
$mta_name=~s/'/''/g;
my $cid;
my $sql;
#------ connect to the util database ------------------
my ($dbhq,$dbhu)=$util->get_dbh();
$sql="insert into mta(name) values('$mta_name')";
my $rows=$dbhu->do($sql);

$sql="select max(mta_id) from mta where name=?";
$sth=$dbhu->prepare($sql);
$sth->execute($mta_name);
($new_id)=$sth->fetchrow_array();
$sth->finish();
#
$sql="insert into mta_detail select $new_id,class_id,inj_qty,pause_time,max_records_per_ip,ip_rotation_type,ip_rotation_times,domain_type,domain_times,fromline_type,fromline_times,subjectline_type,subjectline_times,creative_type,creative_times,seed_times,seedlist,seed_type,wikiID,ramp_up,encrypt_link,use_random_batch,variance,oneliner_type,oneliner_times,action_type,action_times,mailingHeaderID,userID,newMailing from mta_detail where mta_id=$mta_id";
$rows=$dbhu->do($sql);

$sql="insert into mta_ramp_up(mta_id,class_id,max_records_per_ip) select $new_id,class_id,max_records_per_ip from mta_ramp_up where mta_id=$mta_id";
$rows=$dbhu->do($sql);
#
$sql="insert into mta_templates select $new_id,class_id,template_id from mta_templates where mta_id=$mta_id";
$rows=$dbhu->do($sql);
$sql="insert into mta_footers select $new_id,class_id,footer_id from mta_footers where mta_id=$mta_id";
$rows=$dbhu->do($sql);
print "Location: mta_list.cgi\n\n";
