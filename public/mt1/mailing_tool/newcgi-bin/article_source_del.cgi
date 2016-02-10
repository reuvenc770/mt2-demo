#!/usr/bin/perl

# *****************************************************************************************
# article_source_del.cgi
#
# this page updates information in the article_source table
#
# History
# Jim Sobeck, 05/09/08, Creation
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
my $sid= $query->param('sid');
$sql="update article set source_id=0 where source_id=$sid";
$sth = $dbhu->do($sql);
$sql="delete from article_source where source_id=$sid";
$sth = $dbhu->do($sql);
#
# Display the confirmation page
#
print "Location: /cgi-bin/article_source_list.cgi\n\n";
$util->clean_up();
exit(0);
