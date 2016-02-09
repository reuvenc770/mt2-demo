#!/usr/bin/perl

# *****************************************************************************************
# add_from.cgi
#
# this page updates information in the advertiser_from table
#
# History
# Jim Sobeck, 12/16/04, Creation
# Jim Sobeck, 02/02/05, Modifed to handle unique id
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
my $sid;
my $errmsg;
my $images = $util->get_images_url;
my $cfrom;
my @from_array;

# connect to the util database
$util->db_connect();
$dbh = $util->get_dbh;

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
my $aflag = $query->param('aflag');
if ($aflag eq "")
{
	$aflag = "N";
}
my $oflag = $query->param('oflag');
if ($oflag eq "")
{
	$oflag = "N";
}
#
# Get the information about the user from the form 
#
my $from_list = $query->param('cfrom');
$from_list =~ s/[\n\r\f\t]/\|/g ;
$from_list =~ s/\|{2,999}/\|/g ;
@from_array = split '\|', $from_list;
foreach $cfrom (@from_array)
{
    $cfrom =~ s/\//g;
    $cfrom =~ s/\xc2//g;
    $cfrom =~ s/\xa0//g;
    $cfrom =~ s/\xb7//g;
    $cfrom =~ s/\x85//g;
    $cfrom =~ s/\x95//g;
    $cfrom =~ s/\xae//g;
    $cfrom =~ s/\x99//g;
    $cfrom =~ s/\xa9//g;
    $cfrom =~ s/\x92//g;
    $cfrom =~ s/\x93//g;
    $cfrom =~ s/\x94//g;
    $cfrom =~ s/\x95//g;
    $cfrom =~ s/\x96//g;
    $cfrom =~ s/\x97//g;
    $cfrom =~ s/\x82//g;
    $cfrom =~ s/\x85//g;
#
# Insert record into advertiser_from
#
if ($aflag eq "Y")
{
$sql = "insert into advertiser_from(advertiser_id,advertiser_from,approved_flag,original_flag,status,date_approved,approved_by) values($aid,'$cfrom','$aflag','$oflag','A',now(),'SpireVision')";
}
else
{
$sql = "insert into advertiser_from(advertiser_id,advertiser_from,approved_flag,original_flag,status) values($aid,'$cfrom','$aflag','$oflag','A')";
}
$sth = $dbh->do($sql);
if ($dbh->err() != 0)
{
	my $errmsg = $dbh->errstr();
    util::logerror("Updating advertiser from info record for $user_id: $errmsg");
}
}
#
# Display the confirmation page
#
print "Location: /cgi-bin/advertiser_disp2.cgi?puserid=$aid\n\n";
$util->clean_up();
exit(0);
