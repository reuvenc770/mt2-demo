#!/usr/bin/perl

# *****************************************************************************************
# del_article.cgi
#
# this page updates information in the advertiser_article table
#
# History
# Jim Sobeck, 11/01/06, Creation
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
my $nl_id = $query->param('nl_id');
my $sid = $query->param('sid');
#
# Delete record from advertiser_article
#
$sql = "delete from nl_article where article_id=$sid and nl_id=$nl_id";
$sth = $dbhu->do($sql);
if ($dbhu->err() != 0)
{
	my $errmsg = $dbhu->errstr();
    $pmesg = "Error - Deleting nl_article record: $sql - $errmsg";
}
else
{
        $pmesg = "Successful Delete of Article Info for Newsletter!" ;
}
#
# Display the confirmation page
#
print "Location: /cgi-bin/newsletter_disp.cgi?pmode=U&nl_id=$nl_id&pmesg=$pmesg\n\n";
$util->clean_up();
exit(0);
