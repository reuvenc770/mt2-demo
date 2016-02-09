#!/usr/bin/perl
# *****************************************************************************************
# listprofile_del.cgi
#
# Removes a list profile 
#
# History
# Jim Sobeck, 5/31/05, Creation
# *****************************************************************************************

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
my $errmsg;
my $user_id;
my $list_name;
my $profile_type;
my $reccnt;
my %checkit = ( 'Y' => 'CHECKED', 'N' => '' );
my $images = $util->get_images_url;

# connect to the util database
my ($dbhq,$dbhu)=$util->get_dbh();

# check for login

$user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}
my $pid = $query->param('pid');
$sql="select profile_type from list_profile where profile_id=$pid";
$sth=$dbhq->prepare($sql);
$sth->execute();
($profile_type)=$sth->fetchrow_array();
$sth->finish();
if ($profile_type eq '3RDPARTY')
{
	$profile_type="3";
}
elsif ($profile_type eq 'CHUNK')
{
	$profile_type="C";
}
else
{
	$profile_type="";
}
$sql="select count(*) from schedule_info where profile_id=$pid and status='A'";
$sth=$dbhq->prepare($sql);
$sth->execute();
($reccnt)=$sth->fetchrow_array();
$sth->finish();
if ($reccnt > 0)
{
print "Location: /cgi-bin/listprofile_list.cgi?tflag=$profile_type&mesg=Profile not deleted because it is currently part of a client schedule.  It must be removed from the client schedule first.\n\n";
}
else
{
$sql = "update list_profile set status='D' where profile_id=$pid";
my $rows=$dbhu->do($sql);
print "Location: /cgi-bin/listprofile_list.cgi?tflag=$profile_type\n\n";
}
