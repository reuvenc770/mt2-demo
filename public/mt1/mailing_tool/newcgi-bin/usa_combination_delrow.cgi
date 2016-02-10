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
my $rowcnt;

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}
my ($dbhq,$dbhu)=$util->get_dbh();
my $usaid=$query->param('usaid');
$sql="select rowCnt from UniqueScheduleAdvertiser where usa_id=$usaid";
$sth=$dbhu->prepare($sql);
$sth->execute();
($rowcnt)=$sth->fetchrow_array();
$sth->finish();

$sql="delete from UniqueAdvertiserCreative where usa_id=$usaid and rowID=$rowcnt";
my $rows=$dbhu->do($sql);
$sql="delete from UniqueAdvertiserSubject where usa_id=$usaid and rowID=$rowcnt";
$rows=$dbhu->do($sql);
$sql="delete from UniqueAdvertiserFrom where usa_id=$usaid and rowID=$rowcnt";
$rows=$dbhu->do($sql);

$rowcnt--;
$sql="update UniqueScheduleAdvertiser set rowCnt=$rowcnt,lastUpdated=curdate() where usa_id=$usaid";
$rows=$dbhu->do($sql);

print "Location: usa_combination.cgi?usaid=$usaid\n\n";

