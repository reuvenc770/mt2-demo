#!/usr/bin/perl
# *****************************************************************************************
# preprocess_send_mail.pl
#
# Batch program that runs from cron to send the emails
# schedule the email
#
# History
# Jim Sobeck,   08/07/01,   Created
# *****************************************************************************************

# send_email.pl 

use strict;
use lib "/var/www/pms/src";
use pms;
use pms_mail;

my $pms = pms->new;
my $sth;
my $sth1;
my $sth2;
my $dbh;
my $sql;
my $rows;
my $cdate = localtime();
my $program = "send_email.pl";
my $errmsg;
my $email_mgr_addr;
my $bin_dir_http;
my $records_per_file = 20000;
my $max_files = 5000;
my $cnt;

# connect to the pms database

$pms->db_connect();
$dbh = $pms->get_dbh;

send_powermail();

$pms->clean_up();
exit(0);

# ***********************************************************************
# sub send_powermail
# ***********************************************************************

sub send_powermail
{
	my $email_user_id;
	my $cemail;
	my $cnt;
	my $email_type;

	$sql = "select email_addr,count(*) from email_user where status='A' group by email_addr having count(*) > 1";  
	$sth = $dbh->prepare($sql);
	$sth->execute();
	while (($cemail,$cnt) = $sth->fetchrow_array())
	{
		$sql = "update email_user set status='D' where email_addr='$cemail' limit 1";
		$sth1 = $dbh->do($sql);
		print "Updating E-mail address $cemail\n";
	}
	$sth->finish();
}
