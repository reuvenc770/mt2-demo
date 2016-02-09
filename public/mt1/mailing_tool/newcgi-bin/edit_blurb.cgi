#!/usr/bin/perl
#===============================================================================
# Name   : edit_blurb.cgi 
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
my $sid = $query->param('sid');
my $from_str;
my $from;

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
$sql = "select blurb from article_blurb where article_id=$aid and blurb_id=$sid"; 
$sth = $dbhq->prepare($sql);
$sth->execute();
($from) = $sth->fetchrow_array();
$sth->finish();

#--------------------------------
# get CGI Form fields
#--------------------------------
        print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Edit Article Blurb</title>
</head>
<body>
<form action="/cgi-bin/upd_blurb.cgi" method="post">
<p><b>Blurb: </b><br><textarea name="cblurb" rows="7" cols="82">$from</textarea></p>
<input type=hidden name=aid value="$aid">
<input type=hidden name=sid value="$sid">
<p>
<input type=image height="22" src="/images/save_rev.gif" width="81" border="0">
</p>
</form>
</body>
</html>
end_of_html
