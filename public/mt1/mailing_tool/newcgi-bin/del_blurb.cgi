#!/usr/bin/perl

# *****************************************************************************************
# del_blurb.cgi
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
my $reccnt;
my $i;
my $dbh;
my $sid;
my $errmsg;
my $images = $util->get_images_url;
my $cfrom;
my @from_array;
my $pmesg;

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
# Delete record from article_blurb
#
$sql = "delete from article_blurb where blurb_id=$sid and article_id=$aid";
$sth = $dbhu->do($sql);
if ($dbhu->err() != 0)
{
	my $errmsg = $dbhu->errstr();
    $pmesg = "Error - Deleting article_blurb record: $sql - $errmsg";
}
else
{
        $pmesg = "Successful Delete of Blurb Info!" ;
}
#
# Display the confirmation page
#
print "Location: /cgi-bin/article.cgi?cid=$aid&pmesg=$pmesg\n\n";
$util->clean_up();
exit(0);
