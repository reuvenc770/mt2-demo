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
my $name=$query->param('name');
$name=~s/'/''/g;
my $aid=$query->param('aid');
$sql="insert into UniqueScheduleAdvertiser(advertiser_id,name,usaType,rowCnt,lastUpdated) values($aid,'$name','Combination',1,curdate())";
my $rows=$dbhu->do($sql);

my $usaid;
$sql="select LAST_INSERT_ID()";
$sth=$dbhu->prepare($sql);
$sth->execute();
($usaid)=$sth->fetchrow_array();
$sth->finish();
print "Location: usa_combination.cgi?usaid=$usaid\n\n";

