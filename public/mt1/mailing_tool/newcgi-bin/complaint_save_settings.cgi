#!/usr/bin/perl

# ******************************************************************************
# complaint_save_settings.cgi 
#
# this page updates information in the DeliverableAdd table
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
my $classid= $query->param('classid');
my $client_id= $query->param('clientid');
my $start= $query->param('start');
my $end= $query->param('end');
$sql="insert into DeliverableAdd(class_id,client_id,start,end) values($classid,$client_id,$start,$end)";
$rows=$dbhu->do($sql);
print "Location: /cgi-bin/complaint_setup_bot.cgi?classid=$classid\n\n";
