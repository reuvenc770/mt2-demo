#!/usr/bin/perl
#===============================================================================
# Name   : headline.cgi 
#
#--Change Control---------------------------------------------------------------
# 10/31/06  Jim Sobeck  Creation
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
my $password;
my $notes;
my $aid = $query->param('aid');
my $headline;

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}
#------  connect to the util database -----------
my ($dbhq,$dbhu)=$util->get_dbh();
#
$sql = "select headline from advertiser_headline where advertiser_id=$aid order by headline"; 
$sth = $dbhq->prepare($sql);

#--------------------------------
# get CGI Form fields
#--------------------------------
        print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Newsletter Headlines</title>
</head>
<body>
<p><b>Current Headlines: </b></br>
end_of_html
$sth->execute();
while (($headline) = $sth->fetchrow_array())
{
	print "&nbsp;&nbsp;&nbsp;$headline<br>\n";
}
$sth->finish();
print<<end_of_html
<p><b>Add Headlines: (Hit ENTER after each one) </b><br>
<form action="/cgi-bin/add_headline.cgi" method="post">
<input type=hidden name=aid value="$aid">
<textarea name="cheadline" rows="7" cols="82"></textarea></p>
<p>
											
											<input type=image height="22" src="/images/save_rev.gif" width="81" border="0">
</form>
</body>
</html>
end_of_html
