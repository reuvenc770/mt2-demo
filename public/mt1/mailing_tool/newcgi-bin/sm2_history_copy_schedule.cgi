#!/usr/bin/perl

# ******************************************************************************
# sm2_history_copy_schedule.cgi
#
# this page is to copy a history schedule
#
# History
# ******************************************************************************

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
my $images = $util->get_images_url;
my $company;
my $network_id;
my $sdate;
my $edate;
my $tdate;

my ($dbhq,$dbhu)=$util->get_dbh();
# check for login

my $user_id = util::check_security();
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
	$util->clean_up();
    exit(0);
}

$sql = "select date_sub(curdate(),interval dayofweek(curdate())-1 day)";
$sth = $dbhq->prepare($sql);
$sth->execute();
($sdate) = $sth->fetchrow_array();
$sth->finish();
$sql = "select date_add('$sdate',interval 6 day),date_add('$sdate',interval 7 day)";
$sth = $dbhq->prepare($sql);
$sth->execute();
($edate,$tdate) = $sth->fetchrow_array();
$sth->finish();

# print html page out
print "Content-type: text/html\n\n";
print << "end_of_html";
<html>
<head><title>History Schedule Copy</title></head>
<center>
<body>
<h3>Copy A History Schedule</h3>
<br>
<form method=post name=adform action="/cgi-bin/sm2_history_copy_schedule_save.cgi">
<table width=50%>
<tr><td><b>Start Date</b></td><td><input type=text name=sdate size=10 maxlength=10 value="$sdate"><td><b>End Date</b></td><td><input type=text name=edate size=10 maxlength=10 value="$edate"></td></tr>
<tr><td><b>To Date</b></td><td><input type=text name=tdate size=10 maxlength=10 value="$tdate"></td></tr>
<tr><td>&nbsp;</td></tr>
</table>
<a href="/cgi-bin/sm2_history_schedule.cgi" target=_top><img src="/images/cancel.gif" border="0"></a><img height="1" src="/images/spacer.gif" width="40" border="0"><input type="image" src="/images/save.gif" border="0" name="I1">
</form>
</body>
</html>
end_of_html
$util->clean_up();
exit(0);
