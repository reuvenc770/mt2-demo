#!/usr/bin/perl
# ******************************************************************************
# template_copy.cgi
#
# copies a template
#
# History
# ******************************************************************************

# include Perl Modules
use lib('/var/www/html/newcgi-bin');
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
my $template_name;
my $templateCode;
my $status;
my $maxCharacters;

# connect to the util database
my ($dbhq,$dbhu)=$util->get_dbh();

my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}

my $userDataRestrictionWhereClause = '';

$util->getUserData({'userID' => $user_id});

if($util->getUserData()->{'isExternalUser'} == 1)
{
	$userDataRestrictionWhereClause = qq|
        userID = $user_id AND
    |;
}

# Get the template information
$sql = "select templateName,status,templateCode,maxCharacters from wikiTemplate where $userDataRestrictionWhereClause wikiID = $template_id";
$sth=$dbhu->prepare($sql);
$sth->execute();
($template_name,$status,$templateCode,$maxCharacters)=$sth->fetchrow_array();
$sth->finish();
$template_name="Copy of ".$template_name;
$sql="insert into wikiTemplate(userID, templateName,dateAdded,status,templateCode,maxCharacters) values($user_id, '$template_name',now(),'A','$templateCode',$maxCharacters)";
$rows = $dbhu->do($sql);
if ($dbhu->err() != 0)
{
	$errmsg = $dbhu->errstr();
	util::logerror("Updating template record: $sql : $errmsg");
	exit(0);
}

# go back to the template list screen

print "Location: index.cgi\n\n";

# exit function

$util->clean_up();
exit(0);
