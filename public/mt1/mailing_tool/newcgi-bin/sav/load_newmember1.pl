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
my $subscribe_datetime;
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

$sql = "set autocommit=1";
$rows = $dbh->do($sql);
if ($dbh->err() != 0)
{
	$errmsg = $dbh->errstr();
    print "Error doing: $sql : $errmsg";
    exit(0);
}

$sql = "select list_id from list where list_id >= 61 and list_id <= 62 order by list_id";
$sth = $dbh->prepare($sql);
$sth->execute();
while (($list_id) = $sth->fetchrow_array())
{
	if ($list_id == 61)
	{
		$sql = "select SQL_BUFFER_RESULT email_addr,subscribe_datetime from member_list where list_id=$list_id and email_user_id >= 29062387 and status='A' order by email_user_id";
	}
	else
	{
		$sql = "select SQL_BUFFER_RESULT email_addr,subscribe_datetime from member_list where list_id=$list_id and status='A' order by email_user_id";
	}
	$sth1 = $dbh->prepare($sql);
	$sth1->execute();
	while (($email_addr,$subscribe_datetime) = $sth1->fetchrow_array())
	{
		$sql = "select email_user_id,first_name,last_name,address,address2,city,state,zip,country,birth_date,gender,phone from email_user where email_addr='$email_addr'";
		$sth2 = $dbh->prepare($sql);
		$sth2->execute();
		if (($tid,$cfirst,$clast,$caddr,$caddr2,$ccity,$cstate,$czip,$ccountry,$cbday,$cgender,$cphone) = $sth2->fetchrow_array())
		{
			$sth2->finish();
			$sql = "select capture_date,member_source from email_user1 where email_user_id = $tid and capture_date != '0000-00-00 00:00:00' and capture_date is not null and member_source != '' and member_source is not null";
			$sth3 = $dbh->prepare($sql);
			$sth3->execute();
			$cfirst = $dbh->quote($cfirst);
			$clast = $dbh->quote($clast);
			$caddr = $dbh->quote($caddr);
			$caddr2 = $dbh->quote($caddr2);
			$ccity = $dbh->quote($ccity);
			$cstate = $dbh->quote($cstate);
			$czip = $dbh->quote($czip);
			$ccountry = $dbh->quote($ccountry);
			$cphone = $dbh->quote($cphone);
			if (($capture_date,$member_source) = $sth3->fetchrow_array())
			{
				$sql = "insert into member_list1(list_id,email_addr,subscribe_datetime,status,first_name,last_name,address,address2,city,state,zip,country,birth_date,gender,phone,capture_date,member_source) values($list_id,'$email_addr','$subscribe_datetime','A',$cfirst,$clast,$caddr,$caddr2,$ccity,$cstate,$czip,$ccountry,'$cbday','$cgender',$cphone,'$capture_date','$member_source')";
			}
			else
			{
				$sql = "insert into member_list1(list_id,email_addr,subscribe_datetime,status,first_name,last_name,address,address2,city,state,zip,country,birth_date,gender,phone) values($list_id,'$email_addr','$subscribe_datetime','A',$cfirst,$clast,$caddr,$caddr2,$ccity,$cstate,$czip,$ccountry,'$cbday','$cgender',$cphone)";
			}
			$sth3->finish();
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
#$sql = "commit";
#$rows = $dbh->do($sql);
#$sql = "set autocommit=0";
#$rows = $dbh->do($sql);
#if ($dbh->err() != 0)
#{
#	$errmsg = $dbh->errstr();
#    print "Error doing: $sql : $errmsg";
#    exit(0);
#}
				$reccnt = 0;
			}
		}
		else
		{
			$sth2->finish();
		}
	}
	$sth1->finish();
#$sql = "commit";
#$rows = $dbh->do($sql);
#$sql = "set autocommit=0";
#$rows = $dbh->do($sql);
}
$sth->finish();
#$sql = "commit";
#$rows = $dbh->do($sql);

$pms->clean_up();
exit(0);
