#!/usr/bin/perl

# *****************************************************************************************
# move_domain.cgi
#
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $rows;
my $sql;
my $dbh;
my $sid;
my $errmsg;
my $images = $util->get_images_url;
my $camp_id;

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
#
my $moveclass= $query->param('moveclass');
my $cdomain = $query->param('cdomain');
my @option1= $query->param('option1');
if ($cdomain ne "")
{
	$sql = "update email_domains set domain_class=$moveclass where domain_name='$cdomain'";
	my $rows= $dbhu->do($sql);
}
#
foreach my $did (@option1)
{
	$sql = "update email_domains set domain_class=$moveclass where domain_id=$did";
	my $rows= $dbhu->do($sql);
}
print "Location: /cgi-bin/emailclasses.cgi\n\n";
