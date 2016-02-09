#!/usr/bin/perl
# *****************************************************************************************
# move_chunk_list_save.cgi
#
# this page is to save a moved chunk list
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
my $errmsg;
my $list_id = $query->param('list_id');
my $new_list_id = $query->param('new_list_id');
my $uid = $query->param('uid');
my $move_amt = $query->param('move_amt');
my $user_id;
my $list_name;
my $ip_addr;
my $server_id;
my $server;
my ($status, $optin_flag, $list_name, $double_mail_template, $thankyou_mail_template);
my $images = $util->get_images_url;

# connect to the util database
my ($dbhq,$dbhu)=$util->get_dbh();
my $dbhu2 = DBI->connect("DBI:mysql:new_mail:update2.routename.com","db_user","sp1r3V");

# check for login
$user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}

$sql = "update email_chunk_list_$uid set list_id=$new_list_id where list_id=$list_id";
if ($move_amt ne "")
{
	$sql=$sql . " limit $move_amt";
}
my $rows=$dbhu2->do($sql);
# print out the html page
print "Content-type:text/html\n\n";
print<<"end_of_html";
<html><head><title>Chunk List Moved</title></head>
<body>
<center>
<h2>Chunk List has successfully been moved.</h2>
</center>
</body>
</html>
end_of_html
$util->clean_up();
exit(0);
