#!/usr/bin/perl
#################################################################
####   fix_list_member.pl      ####
#################################################################

use strict;
use lib "/home/bannerconcepts/pms/src";
use pms;
use pms_mail;

my $pms = pms->new;
my $sth;
my $sth2;
my $dbh;
my $sql;
my $rows;
my $email_user_id;
my $errmsg;
my ($list_id, $email_addr, $subscribe_datetime, $unsubscribe_datetime, $email_type, $status);
my $count;

my $cdate = localtime();
print "fix_list_member.pl starting at $cdate\n";

# connect to the pms database

$pms->db_connect();
$dbh = $pms->get_dbh;

# create new list_member table

$sql = "create table list_member_new (
  list_id int(11) NOT NULL,
  email_user_id int(11) NOT NULL,
  subscribe_datetime datetime NOT NULL,
  unsubscribe_datetime datetime,
  status char(1) NOT NULL)";
$dbh->do($sql);
if ($dbh->err() != 0)
{
    $errmsg = $dbh->errstr();
    print "Error creating new table: $sql : $errmsg";
    exit(0);
}

# read all records from old list_member table

$sql = "select list_id, email_addr, subscribe_datetime, unsubscribe_datetime, email_type,
	status from list_member";
$sth = $dbh->prepare($sql);
$sth->execute();
while (($list_id, $email_addr, $subscribe_datetime, $unsubscribe_datetime, 
	$email_type, $status) = $sth->fetchrow_array())
{
	print "found $email_addr from old list_member table\n";

	# lookup this member in the email_user table	
	
	$sql = "select count(*) from email_user where email_addr = '$email_addr'";
	$sth2 = $dbh->prepare($sql);
	$sth2->execute();
	($count) = $sth2->fetchrow_array();
	$sth2->finish();

	if ($count > 0)
	{
		print "Found in email_user\n";

		# lookup this users email_user_id

		$sql = "select email_user_id from email_user where email_addr = '$email_addr'";
		$sth2 = $dbh->prepare($sql);
		$sth2->execute();
		($email_user_id) = $sth2->fetchrow_array();
		$sth2->finish();

	}
	else
	{

		print "Did not find in email_user\n";

		$sql = "insert into email_user (email_addr, email_type, create_datetime, status, user_id) 
			values ('$email_addr', '$email_type', now(), 'A', 0)";
		$rows = $dbh->do($sql);

		$sql = "select last_insert_id()";
		$sth2 = $dbh->prepare($sql);
		$sth2->execute();
		($email_user_id) = $sth2->fetchrow_array();
		$sth2->finish();
	}

	print "Adding to new list_member table\n";

	$sql = "insert list_member_new (list_id, email_user_id, subscribe_datetime, 
		unsubscribe_datetime, status) values ($list_id, $email_user_id, '$subscribe_datetime',
		'$unsubscribe_datetime', '$status')";
	$rows = $dbh->do($sql);
}	
$sth->finish();

$cdate = localtime();
print "finished at $cdate\n";

$pms->clean_up();
exit(0);
