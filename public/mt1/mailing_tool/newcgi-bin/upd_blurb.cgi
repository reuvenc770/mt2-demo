#!/usr/bin/perl

# *****************************************************************************************
# upd_blurb.cgi
#
# this page updates information in the article_blurb table
#
# History
# Jim Sobeck, 10/31/06, Creation
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
# Remove old from information
#
my $aid = $query->param('aid');
my $sid = $query->param('sid');
#
# Get the information about the user from the form 
#
my $cfrom = $query->param('cblurb');
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
# Insert record into article_blurb
#
$sql = "update article_blurb set blurb='$cfrom' where blurb_id=$sid and article_id=$aid";
$sth = $dbhu->do($sql);
if ($dbhu->err() != 0)
{
	my $errmsg = $dbhu->errstr();
    util::logerror("Updating article blurb info record for $user_id: $errmsg");
}
#
print "Location: /cgi-bin/article.cgi?cid=$aid\n\n";
$util->clean_up();
exit(0);
