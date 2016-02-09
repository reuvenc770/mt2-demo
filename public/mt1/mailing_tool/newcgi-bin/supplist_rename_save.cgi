#!/usr/bin/perl
# *****************************************************************************************
# supplist_rename_save.cgi
#
# this page changes the name of the suppression list 
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
my $sth1;
my $sql;
my $dbh;
my $rows;
my $errmsg;
my $btn= $query->param('btn');
my $vid= $query->param('vid');
my $sortby= $query->param('sortby');
my $f= $query->param('f');


# check for login

my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}
if ($btn eq "cancel")
{
	print "Location: /newcgi-bin/supplist_mgr.cgi?f=$f&sortby=$sortby\n\n";
	exit(0);
}
# connect to the util database
my ($dbhq,$dbhu)=$util->get_dbh();
my $list_name= $query->param('list_name');
$sql="update vendor_supp_list_info set list_name='$list_name' where list_id=$vid";
my $rows=$dbhu->do($sql);
	print "Location: /newcgi-bin/supplist_mgr.cgi?f=$f&sortby=$sortby\n\n";

