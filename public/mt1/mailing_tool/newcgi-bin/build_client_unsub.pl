#!/usr/bin/perl
# *****************************************************************************************
# build_client_unsub.pl
#
# Batch program that runs from cron to build daily unsub log 
#
# History
# Jim Sobeck,   01/07/04,   Created
# *****************************************************************************************

use strict;
use lib "/var/www/html/newcgi-bin";
use util;
use util_mail;

my $util = util->new;
my $sth;
my $sth1;
my $sth2;
my $sth3;
my $dbh;
my $sql;
my $rows;
my $cdate = localtime();
my $program = "build_unsub_daily.pl";
my $errmsg;
my $cname;
my $min_cnt;
my $max_cnt;
my $transfer_method;
my $total_reccnt;
my $file_date;
my $delimit_char;
my $aol_flag;
my $cnt;
my $filename;
my $client_id;
my $sname;

# connect to the util database

$| = 1;
$ENV{PATH} = '/bin:/usr/bin';
$ENV{BASH_ENV} = '';

$client_id = $ARGV[0];
$sname = $ARGV[1];
my $dbhq;
my $dbhu;
($dbhq,$dbhu)=$util->get_dbh();

$sql = "select DATE_FORMAT(date_sub(curdate(),INTERVAL 1 DAY),'%m%d%Y')";
$sth = $dbhq->prepare($sql);
$sth->execute();
($file_date) = $sth->fetchrow_array();
$sth->finish();

$filename = "/var/www/util/logs/" . $sname . "_" . $file_date . ".txt";
open (LOG, "> $filename");
send_adv();
close LOG;
print "Done Processing\n";
$util->clean_up();
exit(0);

# ***********************************************************************
# sub send_adv
# ***********************************************************************

sub send_adv
{
	my $sth2;
	my $aid;
	my $aname;
	my $email_addr;
	my $iemail;

	$sql="select email_addr from unsub_log where unsub_date >= date_sub(curdate(),interval 1 day) and unsub_date < curdate() and email_addr <> '' and client_id=$client_id"; 
	$sth2=$dbhq->prepare($sql);
	$sth2->execute();
	while (($email_addr) = $sth2->fetchrow_array())
	{
		$email_addr =~ s///g;
		print LOG "$email_addr\n";
	}
	$sth2->finish();
}
