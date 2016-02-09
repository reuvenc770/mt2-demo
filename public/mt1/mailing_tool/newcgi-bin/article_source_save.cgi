#!/usr/bin/perl

# *****************************************************************************************
# article_source_save.cgi
#
# this page updates information in the article_source table
#
# History
# Jim Sobeck, 05/09/08, Creation
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
my $source_name= $query->param('source_name');
my $source_url= $query->param('source_url');
my $image_url= $query->param('image_url');
$source_name=~s/'/''g/;
if ($sid > 0)
{
	$sql="update article_source set source_name='$source_name',source_url='$source_url',image_url='$image_url' where source_id=$sid";
}
else
{
	$sql="insert into article_source(source_name,source_url,image_url) values('$source_name','$source_url','$image_url')";
}
$sth = $dbhu->do($sql);
#
# Display the confirmation page
#
print "Location: /cgi-bin/article_source_list.cgi\n\n";
$util->clean_up();
exit(0);
