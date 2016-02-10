#!/usr/bin/perl

# *****************************************************************************************
# add_blurb.cgi
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
my $aid = $query->param('aid');
#
# Get the information about the user from the form 
#
my $from_list = $query->param('cblurb');
#$from_list =~ s/[\n\r\f\t]/\|/g ;
#$from_list =~ s/\|{2,999}/\|/g ;
#@from_array = split '\|', $from_list;
# Insert record into article_blurb
#
$sql = "insert into article_blurb(article_id,blurb) values($aid,'$from_list')";
$sth = $dbhu->do($sql);
if ($dbhu->err() != 0)
{
	my $errmsg = $dbhu->errstr();
    util::logerror("Updating article blurb info record for $user_id: $errmsg");
}
#
# Display the confirmation page
#
print "Location: /cgi-bin/article.cgi?cid=$aid\n\n";
$util->clean_up();
exit(0);
