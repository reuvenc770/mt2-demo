#!/usr/bin/perl

# ******************************************************************************
# sm2_history_copy_schedule_save.cgi
#
# this page saves information about the copied history schedule 
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
my $sth1;
my $sql;
my $rows;
my $dbh;
my $images = $util->get_images_url;
my $stime;
my $company;
my $network_id;
my $tparty;
my $STRONGMAIL_ID=10;
my $daycnt;
my $daycnt2;
my $daycnt1;
my $temp_date;
my $sdate=$query->param('sdate');
my $edate=$query->param('edate');
my $tdate=$query->param('tdate');
my $test_id;

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
# Check to make sure all dates entered
#
if (($sdate eq "") || ($edate eq "") || ($tdate eq ""))
{
	display_error("One or more dates are blank.  All dates must be entered");
	exit(0);
}
$sql = "select datediff('$edate','$sdate')";
$sth = $dbhq->prepare($sql);
$sth->execute();
($daycnt) = $sth->fetchrow_array();
$sth->finish();
if ($daycnt < 0)
{
	display_error("End date must be later than Start date.");
	exit(0);
}
$sql = "select datediff('$tdate',curdate())";
$sth = $dbhq->prepare($sql);
$sth->execute();
($daycnt) = $sth->fetchrow_array();
$sth->finish();
if ($daycnt < 0)
{
	display_error("To date must be later than current date.");
	exit(0);
}
#
$sql="select '$tdate' between '$sdate' and '$edate'";
$sth = $dbhq->prepare($sql);
$sth->execute();
($daycnt) = $sth->fetchrow_array();
$sth->finish();
if ($daycnt == 1)
{
	display_error("To date cannot be between start and end date.");
	exit(0);
}
$sql = "select datediff('$edate','$sdate')";
$sth = $dbhq->prepare($sql);
$sth->execute();
($daycnt) = $sth->fetchrow_array();
$sth->finish();
$sql = "select datediff('$tdate','$sdate')";
$sth = $dbhq->prepare($sql);
$sth->execute();
($daycnt1) = $sth->fetchrow_array();
$sth->finish();
#
#	Delete all records for date range 
#
	$sql="select test_id,schedule_date from test_schedule where schedule_date >= '$tdate' and schedule_date <= date_add('$tdate',interval $daycnt day)";
	$sth = $dbhq->prepare($sql);
	$sth->execute();
	while (($test_id,$temp_date) = $sth->fetchrow_array())
	{
		$sql = "delete from test_schedule where test_id=$test_id and schedule_date='$temp_date'";
		$rows = $dbhu->do($sql);
	}
	$sth->finish();
#
	$sql="select test_id,schedule_date from test_schedule where schedule_date >= '$sdate' and schedule_date <= '$edate'"; 
	$sth = $dbhq->prepare($sql);
	$sth->execute();
	while (($test_id,$temp_date) = $sth->fetchrow_array())
	{
			$sql="insert into test_schedule(test_id,schedule_date,status) values($test_id,date_add('$temp_date',interval $daycnt1 day),'START')";
			$rows = $dbhu->do($sql);
#
	}
	$sth->finish();
	print "Location: /cgi-bin/sm2_history_schedule.cgi\n\n";
$util->clean_up();
exit(0);

sub display_error
{
	my ($mesg) = @_ ;
print "Content-type: text/html\n\n";
print<<"end_of_html";
<html>
<head><title>Error</title></head>
<body>
<center>
<h3>$mesg</h3>
</center>
</body>
</html>
end_of_html
}
