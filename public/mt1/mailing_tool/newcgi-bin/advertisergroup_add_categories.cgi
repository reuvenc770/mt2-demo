#!/usr/bin/perl
# *****************************************************************************************
# advertisergroup_add_categories.cgi
#
# this page adds categories to a Advertiser Group 
#
# History
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $pms = util->new;
my $query = CGI->new;
my $sth;
my $sql;
my $dbh;
my $rows;
my $errmsg;
my $userid;
my $dname;

# connect to the pms database
my ($dbhq,$dbhu)=$pms->get_dbh();

# check for login

my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $pms->clean_up();
    exit(0);
}

# get fields from the form

my $gid= $query->param('gid');
my @clients= $query->param('sel2');

$sql = "delete from AdvertiserCategoryGroupCat where advertiser_group_id=$gid";
$rows = $dbhu->do($sql);
my $i=0;
while ($i <= $#clients)
{
	$sql = "insert into AdvertiserCategoryGroupCat(advertiser_group_id,category_id) values ($gid,'$clients[$i]')";
	$rows = $dbhu->do($sql);
	$i++;
}
print "Location: advertisergroup_list.cgi\n\n";
$pms->clean_up();
exit(0);
