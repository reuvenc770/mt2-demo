#!/usr/bin/perl

# *****************************************************************************************
# add_article_subject.cgi
#
# this page updates information in the article_subject table
#
# History
# Jim Sobeck, 12/20/06, Creation
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
my $aid = $query->param('aid');
#
# Get the information about the user from the form 
#
my $from_list = $query->param('csubject');
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
# Insert record into advertiser_subject
#
$sql = "insert into article_subject(article_id,subject) values($aid,'$cfrom')";
$sth = $dbhu->do($sql);
if ($dbhu->err() != 0)
{
	my $errmsg = $dbhu->errstr();
    util::logerror("Updating article subject info record for $user_id: $errmsg");
}
}
#
# Display the confirmation page
#
print "Location: /cgi-bin/article.cgi?cid=$aid\n\n";
$util->clean_up();
exit(0);
