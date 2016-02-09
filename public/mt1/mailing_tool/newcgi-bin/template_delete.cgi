#!/usr/bin/perl
# *****************************************************************************************
# template_delete.cgi
#
# delete a template
#
# History
# Grady Nash, 10/02/01, Creation
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
my $template_id = $query->param('nl_id');

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

$util->getUserData({'userID' => $user_id});

my $userDataRestrictionWhereClause = '';

if($util->getUserData()->{'isExternalUser'} == 1)
{
	$userDataRestrictionWhereClause = qq|
        userID = $user_id AND
    |;
}


# update the template record - set status to Delete

$sql = "update brand_template set status = 'D' where $userDataRestrictionWhereClause template_id = $template_id";
$rows = $dbhu->do($sql);
if ($dbhu->err() != 0)
{
	$errmsg = $dbhu->errstr();
	util::logerror("Updating template record: $sql : $errmsg");
	exit(0);
}

# go back to the template list screen

print "Location: template_list.cgi\n\n";

# exit function

$util->clean_up();
exit(0);
