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
my $name;
my $label;
my $status;
my $desc;

# connect to the util database
my ($dbhq,$dbhu)=$util->get_dbh();

my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}

# Get the template information
$sql = "select mailingTemplateTypeLabel,mailingTemplateTypeName,description from MailingTemplateType where mailingTemplateTypeID= $template_id";
$sth=$dbhu->prepare($sql);
$sth->execute();
($label,$name,$desc)=$sth->fetchrow_array();
$sth->finish();
$desc=~s/'/''/g;
$name=~s/'/''/g;
$label=~s/'/''/g;
$label="Copy of ".$label;
$sql="insert into mailingTemplateType(mailingTemplateTypeLabel,mailingTemplateTypeName,description) values('$label','$name','$desc')";
$rows = $dbhu->do($sql);
if ($dbhu->err() != 0)
{
	$errmsg = $dbhu->errstr();
	util::logerror("Updating mailingtemplateType record: $sql : $errmsg");
	exit(0);
}

# go back to the template list screen

print "Location: index.cgi\n\n";

# exit function

$util->clean_up();
exit(0);
