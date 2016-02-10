#!/usr/bin/perl

# *****************************************************************************************
# clientgroup_profile_save.cgi
#
# this page updates the ClientGroupClients table 
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
my $errmsg;
my $user_id;
my $link_id;
my $refurl;
my $bgcolor;
my $reccnt;
my $images = $pms->get_images_url;
my $alt_light_table_bg = $pms->get_alt_light_table_bg;
my $light_table_bg = $pms->get_light_table_bg;
my $table_text_color = $pms->get_table_text_color;
my $status_name;
my $status;
my $cat_id;
my $category_name;
my $domain_name;
my $rows;
my $group_id = $query->param('group_id');

my ($dbhq,$dbhu)=$pms->get_dbh();
# check for login
$user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $pms->clean_up();
    exit(0);
}
# read info about the lists
my $cid;
my $tstr;
my $pid;
$sql = "select client_id from ClientGroupClients where client_group_id=$group_id";
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($cid) = $sth->fetchrow_array())
{
	$tstr="pid_".$cid;
	$pid=$query->param($tstr);
	if ($pid ne "")
	{
		$sql="update ClientGroupClients set profile_id=$pid where client_group_id=$group_id and client_id=$cid";
		$rows=$dbhu->do($sql);
	}
}
$sth->finish();
print "Location: clientgroup_list.cgi\n\n";
exit(0);
