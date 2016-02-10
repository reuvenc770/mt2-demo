#!/usr/bin/perl

# *****************************************************************************************
# dd_delete.cgi
#
# this page deletes a Daily Deal setting 
#
# History
# Jim Sobeck, 02/25/09, Creation
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
my $dd_id = $query->param('dd_id');
my $ctype = $query->param('ctype');
my $new_id;
my $sql;
#------ connect to the util database ------------------
my ($dbhq,$dbhu)=$util->get_dbh();
$sql="delete from DailyDealSettingDetail where dd_id=$dd_id";
my $rows=$dbhu->do($sql);
$sql="delete from DailyDealSettingCustom where dd_id=$dd_id";
my $rows=$dbhu->do($sql);
$sql="delete from DailyDealSetting where dd_id=$dd_id";
$rows=$dbhu->do($sql);
$sql="delete from DailyDealSettingDetailIpGroup where dd_id=$dd_id";
$rows=$dbhu->do($sql);
if ($ctype eq "Trigger")
{
	$sql="update user set dd_id=0 where dd_id=$dd_id";
	$rows=$dbhu->do($sql);
}

print "Location: dd_list.cgi?ctype=$ctype\n\n";
