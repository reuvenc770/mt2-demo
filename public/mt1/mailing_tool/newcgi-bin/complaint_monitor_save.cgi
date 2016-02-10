#!/usr/bin/perl
#===============================================================================
# Name   : complaint_monitor_save.cgi 
#
#--Change Control---------------------------------------------------------------
# 03/20/09  Jim Sobeck  Creation
#===============================================================================

#-----  include Perl Modules ---------
use strict;
use CGI;
use util;

#------  get some objects to use later ---------
my $util = util->new;
my $query = CGI->new;
my $name;
my $sql;
my $sth;
my $rows;
my $cday= $query->param('cday');
my $client_group_id = $query->param('client_group_id');
my $profile_id = $query->param('pid');
my $usa_id = $query->param('usa_id');
my $template_id = $query->param('template_id');
my $ctime= $query->param('ctime');
my $num_drops= $query->param('drops');

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}
#------  connect to the util database -----------
my ($dbhq,$dbhu)=$util->get_dbh();
#
$sql="delete from ComplaintSetup where cday=$cday";
$rows=$dbhu->do($sql);

$sql="insert into ComplaintSetup(cday,client_group_id,profile_id,usa_id,template_id,send_time,num_drops) values($cday,$client_group_id,$profile_id,$usa_id,$template_id,'$ctime',$num_drops)";
$rows=$dbhu->do($sql);

print "Location: /cgi-bin/complaint_monitor.cgi\n\n";
