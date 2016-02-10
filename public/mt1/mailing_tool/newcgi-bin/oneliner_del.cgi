#!/usr/bin/perl

# *****************************************************************************************
# oneliner_del.cgi
#
# this page updates information in the HeaderOneLiner table
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
$sql="delete from HeaderOneLiner where oneliner_id=$sid";
$sth = $dbhu->do($sql);
#
# Display the confirmation page
#
print "Location: /cgi-bin/oneliner_list.cgi\n\n";
$util->clean_up();
exit(0);
