#!/usr/bin/perl

# *****************************************************************************************
# template_preview.cgi
#
# this page presents the user with a preview of the template
#
# History
# Grady Nash, 9/04/01, Creation
# *****************************************************************************************

# include Perl Modules

use strict;
use util_mail;
use util;

my $util = util->new;
my $query = CGI->new;
my $dbh;
my $sth;
my $sql;
my $template_id = $query->param('template_id');
my $format = "H";
my $images = $util->get_images_url;

# connect to the util database

$util->db_connect();
$dbh = $util->get_dbh;

# check for login

my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}

# Get the email template and substitute all the field data

my $the_email = &util_mail::repl_template($dbh,$template_id,$user_id,$format);

# print it out for the user to see it

print "Content-type: text/html\n\n";
print "$the_email\n";

$util->clean_up();
exit(0);

