#!/usr/bin/perl
# *****************************************************************************************
# move_chunk_list.cgi
#
# this page is to move a chunk list
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
my $user_id;
my $list_name;
my $ip_addr;
my $server_id;
my $server;
my ($status, $optin_flag, $list_name, $double_mail_template, $thankyou_mail_template);
my $images = $util->get_images_url;

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

$sql = "select list_name, status, ip_addr,server_id,user_id,server from list,server_config where list_id = $list_id and list.server_id=server_config.id";
	$sth = $dbhq->prepare($sql);
	$sth->execute();
	($list_name, $status, $ip_addr,$server_id,$user_id,$server ) = $sth->fetchrow_array();
$sth->finish();
# print out the html page
print "Content-type:text/html\n\n";
print<<"end_of_html";
<html><head><title>Move Chunk List</title></head>
<body>
<center>
<form method=post action="/cgi-bin/move_chunk_list_save.cgi">
<input type=hidden name=list_id value=$list_id>
<input type=hidden name=uid value=$user_id>
<table>
<tr><td>Move List <b>$list_name - $server - $ip_addr</b></td></tr>
<tr><td>To: <select name=new_list_id>
end_of_html
$sql="select list_id,list_name, status, ip_addr,server_id,user_id,server from list,server_config where user_id=$user_id and list.server_id=server_config.id and list_id != $list_id and status='A' order by list_name";
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($list_id,$list_name, $status, $ip_addr,$server_id,$user_id,$server ) = $sth->fetchrow_array())
{
	print "<option value=$list_id>$list_name - $server - $ip_addr</option>\n";
}
$sth->finish();
print<<"end_of_html";
</select>
</td></tr>
<tr><td>Amount to Move(if blank then moves all)&nbsp;<input type=text name="move_amt" size=10 maxlength=10></td></tr>
</table>
<input type=submit name="Move" value="Move List">
</form>
</body>
</html>
end_of_html
$util->clean_up();
exit(0);
