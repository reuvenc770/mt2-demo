#!/usr/bin/perl

# ******************************************************************************
# dd_send_seeds.cgi
#
# this page loads daily deal seeds
#
# History
# ******************************************************************************

# include Perl Modules

use strict;
use util;

my $util = util->new;
my $sql;
my $sth;
my $dd_id;
my $dname;

my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}
my $dbhq;
my $dbhu;
($dbhq,$dbhu)=$util->get_dbh();

print "Content-type: text/html\n\n";
print<<"end_of_html";
<html><head><title>Send Daily Deal Seeds</title></head>
<body>
<center>
<form method=post action="dd_send_seeds_save.cgi">
<table>
<tr><td>Daily Deal Setting:</td><td><select name=dd_id>
end_of_html
$sql="select dd_id,name from DailyDealSetting order by name";
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($dd_id,$dname)=$sth->fetchrow_array())
{
	print "<option value=$dd_id>$dname</option>\n";
}
$sth->finish();
print<<"end_of_html";
</td></tr>
<tr><td>Email Address:</td><td><input type=text name=seedlist size=50 maxlength=50></td></tr>
<tr><td colspan=2><input type=submit name=submit value="Submit"></td></tr>
</table>
</form>
<a href="mainmenu.cgi"><img src=/mail-images/home_blkline.gif border=0></a>
</body>
</html>
end_of_html
