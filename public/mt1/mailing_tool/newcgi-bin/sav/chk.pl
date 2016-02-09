#!/usr/bin/perl

# include Perl Modules

use strict;
use CGI;
use pms;

# get some objects to use later

my $pms = pms->new;
my $errmsg;
my $sth;
my $sth2;
my $sth3;
my $sql;
my $dbh;
my $list_id;
my $status;
my $schedule_date;
my $light_table_bg = $pms->get_light_table_bg;
my $images = $pms->get_images_url;
my $list_members = 1;
my $counter;
my $email_id;
my $status;

# connect to the pms database

$pms->db_connect();
$dbh = $pms->get_dbh;

$sql = "select list_id from list where status='A'"; 
$sth = $dbh->prepare($sql);
$sth->execute();
while (($list_id) = $sth->fetchrow_array())
{
	$sql = "select email_user_id from list_member where list_id = $list_id and status = 'A' order by email_user_id";
	$sth2 = $dbh->prepare($sql);
	$sth2->execute();
	while (($email_id) = $sth2->fetchrow_array())
	{
		$sql = "select count(*) from email_user where email_user_id=$email_id and status='A'";
		$sth3 = $dbh->prepare($sql);
		$sth3->execute();
		if (($counter) = $sth3->fetchrow_array())
		{
			$counter = 0;
		}
		else
		{
			print "$list_id|$email_id\n";
		}
		$sth3->finish();
	}
	$sth2->finish();
}
$sth->finish();

$pms->clean_up();
exit(0);
