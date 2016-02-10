#!/usr/bin/perl
#===============================================================================
# Purpose: Edit category brand subdomain 
# Name   : add_brandsubdomain.cgi 
#
#--Change Control---------------------------------------------------------------
# 06/07/05  Jim Sobeck  Creation
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
my $cid = $query->param('cid');

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}
#------  connect to the util database -----------
###$util->db_connect();

my ($dbhq,$dbhu)=$util->get_dbh();
###$dbh = $util->get_dbh;
#
$sql = "select category_name from category_info where category_id=$cid"; 
$sth = $dbhq->prepare($sql);
$sth->execute();
($category_name) = $sth->fetchrow_array();
$sth->finish();

#--------------------------------
# get CGI Form fields
#--------------------------------
        print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Category Brand Subdomains</title>
</head>
<body>
<p><b>Current $category_name Synonyms: </b></br>
end_of_html
$sql = "select subdomain_name from brandsubdomain_info where category_id=$cid"; 
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($brand_str) = $sth->fetchrow_array())
{
	print "&nbsp;&nbsp;&nbsp;$brand_str<br>\n";
}
$sth->finish();
print<<end_of_html
<p><b>Brand Subdomain: (Hit ENTER after each one) </b><br>
<form action="/cgi-bin/ins_brandsubdomain.cgi" method="post">
<input type=hidden name=cid value="$cid">
<textarea name="cbrand" rows="7" cols="82"></textarea></p>
<p>
											
											<input type=image height="22" src="/images/save_rev.gif" width="81" border="0">&nbsp;&nbsp;
<b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
</form>
</body>
</html>
end_of_html
