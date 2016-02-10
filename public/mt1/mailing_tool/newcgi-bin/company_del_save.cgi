#!/usr/bin/perl

# *****************************************************************************************
# company_del_save.cgi
#
# Delete a company information 
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
my $rows;
my $errmsg;
my $user_id;
my $company_id = $query->param('company_id');

# connect to the util database
my ($dbhq,$dbhu)=$util->get_dbh();

# check for login

$user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}

# update this campaign's record

$sql = "update company_info set status='D' where company_id = $company_id";
$rows = $dbhu->do($sql);
if ($dbhu->err() != 0)
{
	$errmsg = $dbhu->errstr();
	util::logerror("Updating company_info record for $company_id: $errmsg");
	exit(0);
}

print "Location: company_list.cgi\n\n";

$util->clean_up();
exit(0);
