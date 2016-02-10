#!/usr/bin/perl

# *****************************************************************************************
# del_brand_article.cgi
#
# this page updates information in the brand_article table
#
# History
# Jim Sobeck, 08/10/07, Creation
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
my $bid = $query->param('bid');
my $sid = $query->param('sid');
#
# Delete record from brand_article
#
if ($sid == 0)
{
	$sql="delete from brand_article where brand_id=$bid";
}
else
{
	$sql = "delete from brand_article where article_id=$sid and brand_id=$bid";
}
$sth = $dbhu->do($sql);
if ($dbhu->err() != 0)
{
	my $errmsg = $dbhu->errstr();
    $pmesg = "Error - Deleting brand_article record: $sql - $errmsg";
}
else
{
        $pmesg = "Successful Delete of Article Info for Brand!" ;
}
#
# Display the confirmation page
#
print "Location: /cgi-bin/add_brand_article.cgi?bid=$bid&pmesg=$pmesg\n\n";
$util->clean_up();
exit(0);
