#!/usr/bin/perl
#===============================================================================
# Purpose: Control script for Brandsubdomain for categories 
# Name   : brandsubdomain.cgi 
#
#--Change Control---------------------------------------------------------------
# 06/08/05  Jim Sobeck  Creation
#===============================================================================

#-----  include Perl Modules ---------
use strict;
use CGI;
use util;

#------  get some objects to use later ---------
my $util = util->new;
my $query = CGI->new;
my $name;
my $sql;
my $sth;
my $dbh;
my $phone;
my $email;
my $company;
my $aim;
my $website;
my $username;
my $brand_str;
my $category_name;
my $bname;
my $cid = $query->param('cid');
my $btn1 = $query->param('btn1');

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}
if ($btn1 eq "Add")
{
	print "Location: add_brandsubdomain.cgi?cid=$cid\n\n";
	exit(0);
}
#------  connect to the util database -----------
###$util->db_connect();

my ($dbhq,$dbhu)=$util->get_dbh();
###$dbh = $util->get_dbh;
#
if ($btn1 eq "Delete")
{
	my $brandid = $query->param('brandid');
	$sql = "delete from brandsubdomain_info where subdomain_id=$brandid and category_id=$cid";
	my $rows=$dbhu->do($sql);
	print "Location: new_list_category.cgi\n\n";
	exit(0);
}
#
if ($btn1 eq "Edit")
{
	my $brandid = $query->param('brandid');
	$sql = "select subdomain_name from brandsubdomain_info where subdomain_id=$brandid and category_id=$cid";
    $sth = $dbhq->prepare($sql);
    $sth->execute();
    ($bname) = $sth->fetchrow_array();
	$sth->finish();
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Category Brand Subdomains</title>
</head>
<body>
<p>
<form action="/cgi-bin/upd_brandsubdomain.cgi" method="post">
<input type=hidden name=cid value="$cid">
<input type=hidden name=brandid value="$brandid">
Brand Subdomain: <input type=text name=subdomain value='$bname'>
<p>
<input type=image height="22" src="/images/save_rev.gif" width="81" border="0">&nbsp;&nbsp;<a href="new_list_category.cgi"><img src="/images/cancel.gif" border=0></a>
</form>
</body>
</html>
end_of_html
}
