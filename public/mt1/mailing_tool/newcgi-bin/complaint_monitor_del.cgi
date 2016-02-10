#!/usr/bin/perl
#===============================================================================
# Purpose: Display of the ComplaintSetup Table 
# Name   : complaint_monitor_del.cgi 
#
#--Change Control---------------------------------------------------------------
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
my $sth1;
my $dbh;
my $cday=$query->param('cday');

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}
my ($dbhq,$dbhu)=$util->get_dbh();
#
#
$sql = "delete from ComplaintSetup where cday=$cday"; 
$sth = $dbhu->do($sql);
print "Location: /cgi-bin/complaint_monitor.cgi\n\n";
