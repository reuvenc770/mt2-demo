#!/usr/bin/perl
#-----  include Perl Modules ---------
use strict;
use CGI;
use util;

#------  get some objects to use later ---------
my $util = util->new;
my $query = CGI->new;
my $sth;
my $sql;

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}
my ($dbhq,$dbhu)=$util->get_dbh();
my $usaid=$query->param('usaid');
$sql="update UniqueScheduleAdvertiser set rowCnt=rowCnt+1,lastUpdated=curdate() where usa_id=$usaid";
my $rows=$dbhu->do($sql);

print "Location: usa_combination.cgi?usaid=$usaid\n\n";

