#!/usr/bin/perl
# *****************************************************************************************
# upload_images_delete.cgi
#
# deletes an image out of the user's upload directory
#
# History
# Grady Nash   10/02/2001		Creation 
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;
use URI::Escape;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $sql;
my $dbh;
my $username;
my $user_id;
my $full_file_name;
my $imagedir;
my $images = $util->get_images_url;
my $document_root;
my $file_name;

# connect to the util database

$util->db_connect();
$dbh = $util->get_dbh;

# check for login

$user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}

# get this user's username

$sql = "select username from user where user_id = $user_id";
$sth = $dbh->prepare($sql);
$sth->execute();
($username) = $sth->fetchrow_array();
$sth->finish();

$sql = "select parmval from sysparm where parmkey = 'DOCUMENT_ROOT'";
$sth = $dbh->prepare($sql);
$sth->execute();
($document_root) = $sth->fetchrow_array();
$sth->finish();

# build the pathname for this users image directory

$imagedir = "${document_root}$username";
$file_name = $query->param('file');
$file_name = uri_unescape($file_name);
$full_file_name = $imagedir . "/" . $file_name;

# delete the file

$! = 0;
unlink $full_file_name;
if ($!)
{
    util::logerror "Error deleting the image file: $!\n";
}
else
{
	# go back to the browse screen
	print "Location: upload_images_browse.cgi\n\n";
}

# exit function

$util->clean_up();
exit(0);
