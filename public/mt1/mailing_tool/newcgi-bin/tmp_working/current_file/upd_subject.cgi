#!/usr/bin/perl

# *****************************************************************************************
# upd_contact.cgi
#
# this page updates information in the advertiser_subject table
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
my $csubject;
my @subject_array;

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
# Remove old subject information
#
my $aid = $query->param('aid');
my $sid = $query->param('sid');
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
my $csubject = $query->param('csubject');
$csubject =~ s/'/''/g;
$csubject =~ s/\x96/-/g;
    $csubject =~ s/\//g;
    $csubject =~ s/\xc2//g;
    $csubject =~ s/\xa0//g;
    $csubject =~ s/\xb7//g;
    $csubject =~ s/\x85//g;
    $csubject =~ s/\x95//g;
    $csubject =~ s/\xae//g;
    $csubject =~ s/\x99//g;
    $csubject =~ s/\xa9//g;
    $csubject =~ s/\x92//g;
    $csubject =~ s/\x93//g;
    $csubject =~ s/\x94//g;
    $csubject =~ s/\x95//g;
    $csubject =~ s/\x96//g;
    $csubject =~ s/\x97//g;
    $csubject =~ s/\x82//g;
    $csubject =~ s/\x85//g;
#
# Insert record into advertiser_subject
#
if ($aflag eq "Y")
{
	$sql = "update advertiser_subject set approved_flag='$aflag',approved_by='SpireVision',date_approved=curdate() where subject_id=$sid and advertiser_id=$aid and approved_by is null";
	$sth = $dbh->do($sql);
}
$sql = "update advertiser_subject set advertiser_subject='$csubject',approved_flag='$aflag',original_flag='$oflag' where subject_id=$sid and advertiser_id=$aid";
$sth = $dbh->do($sql);
if ($dbh->err() != 0)
{
	my $errmsg = $dbh->errstr();
    util::logerror("Updating advertiser subject info record for $user_id: $errmsg");
}
#
# Display the confirmation page
#
print "Location: /cgi-bin/advertiser_disp2.cgi?puserid=$aid\n\n";
$util->clean_up();
exit(0);
