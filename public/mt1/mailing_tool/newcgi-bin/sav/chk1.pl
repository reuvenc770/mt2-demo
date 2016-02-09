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
my $email_addr;
my $status;

# connect to the pms database

$pms->db_connect();
$dbh = $pms->get_dbh;

	$sql = "select email_user_id,email_addr from email_user where user_id = 5 and status = 'D'";
	$sth2 = $dbh->prepare($sql);
	$sth2->execute();
	while (($email_id,$email_addr) = $sth2->fetchrow_array())
	{
		$sql = "select count(*) from list_member where email_user_id=$email_id and status='A' and list_id=9";
		$sth3 = $dbh->prepare($sql);
		$sth3->execute();
		($counter) = $sth3->fetchrow_array();
		if ($counter > 0)
		{
			print "$email_id|$email_addr\n";
		}
		$sth3->finish();
	}
	$sth2->finish();

$pms->clean_up();
exit(0);
