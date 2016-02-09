#!/usr/bin/perl

# *****************************************************************************************
# default_from.cgi
#
# this page updates information in the advertiser_from table
#
# History
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
my $pmesg;
my $sid;
my $errmsg;
my $images = $util->get_images_url;
my $csubject;
my @subject_array;

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
my $aid = $query->param('aid');
my $sid = $query->param('sid');
#
#
$sql = "update creative set default_from=$sid where advertiser_id=$aid";
$sth = $dbhu->do($sql);
if ($dbhu->err() != 0)
{
	my $errmsg = $dbhu->errstr();
    $pmesg = "Error - Updating default from for creatives: $sql - $errmsg";
}
else
{
	$pmesg = "Successful Update of Default From" ;
}
#
print "Location: /cgi-bin/advertiser_disp2.cgi?puserid=$aid&pmesg=$pmesg\n\n";
$util->clean_up();
exit(0);
