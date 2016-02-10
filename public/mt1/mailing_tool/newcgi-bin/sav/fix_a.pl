#!/usr/bin/perl

use strict;
use lib "/var/www/pms/src";
use pms;
use pms_mail;

my $pms = pms->new;
my $sth;
my $sth1;
my $sth2;
my $sth3;
my $dbh;
my $sql;
my $rows;
my $cdate = localtime();
my $errmsg;
my $list_id;
my $email_addr;
my $unsubscribe_datetime;
my $cfirst;
my $clast;
my $caddr;
my $caddr2;
my $ccity;
my $cstate;
my $czip;
my $ccountry;
my $cbday;
my $cgender;
my $tid;
my $capture_date;
my $member_source;
my $cphone;
my $reccnt;

# connect to the pms database

$| = 1;
$reccnt = 0;

$pms->db_connect();
$dbh = $pms->get_dbh;

$sql = "set autocommit=0";
$rows = $dbh->do($sql);
if ($dbh->err() != 0)
{
	$errmsg = $dbh->errstr();
    print "Error doing: $sql : $errmsg";
    exit(0);
}

$sql = "select list_id from list where list_id >= 60 and list_id <= 62 order by list_id";
$sth = $dbh->prepare($sql);
$sth->execute();
while (($list_id) = $sth->fetchrow_array())
{
	$sql = "select SQL_BUFFER_RESULT email_addr,unsubscribe_datetime from member_list where list_id=$list_id and status='U' and unsubscribe_datetime >= '2003-03-10' and unsubscribe_datetime <= '2003-03-14' order by email_user_id";
	$sth1 = $dbh->prepare($sql);
	$sth1->execute();
	while (($email_addr,$unsubscribe_datetime) = $sth1->fetchrow_array())
	{
		$sql = "update member_list1 set status='U',unsubscribe_datetime='$unsubscribe_datetime' where email_addr='$email_addr' and status='A'";
		$rows = $dbh->do($sql);
        if ($dbh->err() != 0)
        {
        	$errmsg = $dbh->errstr();
            print "Error Inserting member_list1 record: $sql : $errmsg";
            exit(0);
       	}
		$reccnt++;
		if ($reccnt >= 100)
		{
			$sql = "commit";
			$rows = $dbh->do($sql);
			$sql = "set autocommit=0";
			$rows = $dbh->do($sql);
			if ($dbh->err() != 0)
			{
				$errmsg = $dbh->errstr();
    			print "Error doing: $sql : $errmsg";
    			exit(0);
			}
			$reccnt = 0;
		}
	}
	$sth1->finish();
$sql = "commit";
$rows = $dbh->do($sql);
$sql = "set autocommit=0";
$rows = $dbh->do($sql);
}
$sth->finish();
$sql = "commit";
$rows = $dbh->do($sql);

$pms->clean_up();
exit(0);
