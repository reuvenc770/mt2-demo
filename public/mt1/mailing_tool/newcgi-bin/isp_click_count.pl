#!/usr/bin/perl
# *****************************************************************************************
# isp_click_count.pl
#
# History
# *****************************************************************************************

use strict;
use lib "/var/www/html/newcgi-bin";
use util;

my $util = util->new;
my $sth;
my $sth1;
my $sth2;
my $dbh;
my $sql;
my $rows;
my $reccnt;
my $i;
my $j;
my $others_open_cnt;
my $others_click_cnt;
my $aol_open_cnt;
my $hotmail_open_cnt;
my $yahoo_open_cnt;
my $aol_click_cnt;
my $hotmail_click_cnt;
my $yahoo_click_cnt;
my $cdate = localtime();

# connect to the util database
my $dbhq;
my $dbhu;
my $class_id;
my $ctype;
my $email_addr;
($dbhq,$dbhu)=$util->get_dbh();
my $dbh2 = DBI->connect("DBI:mysql:new_mail:update2.routename.com","db_user","sp1r3V");

$sql="select campaign_id from campaign where sent_datetime >= date_sub(curdate(),interval 5 day) and sent_datetime <= curdate()";
$sth=$dbhu->prepare($sql);
$sth->execute();
my $table=$sth->fetchall_arrayref();
$sth->finish();
for $i ( 0 .. $#{$table} )
{
    my $cid= $table->[$i][0];
	$sql="select campaign_type from campaign where campaign_id=?";
	$sth=$dbhq->prepare($sql);
	$sth->execute($cid);
	($ctype)=$sth->fetchrow_array();
	$sth->finish();
	$aol_open_cnt=0;
	$aol_click_cnt=0;
	$hotmail_open_cnt=0;
	$hotmail_click_cnt=0;
	$yahoo_open_cnt=0;
	$yahoo_click_cnt=0;
	$others_open_cnt=0;
	$others_click_cnt=0;
	$sql="select email_user_id from open_history where campaign_id=? and open_date >= date_sub(curdate(),interval 1 day) and open_date < curdate()";
unless ($dbh2 && $dbh2->ping) {
$dbh2 = DBI->connect("DBI:mysql:new_mail:update2.routename.com","db_user","sp1r3V");
   }
	$sth=$dbh2->prepare($sql);
	$sth->execute($cid);
	my $eid=$sth->fetchall_arrayref();
	$sth->finish();
	for $j ( 0 .. $#{$eid} )
	{
        	my $email_user_id= $eid->[$j][0];
		$sql="select domain_class,email_addr from email_list el,email_domains ed where el.email_user_id=? and el.domain_id=ed.domain_id";
		$sth=$dbhq->prepare($sql);
		$sth->execute($email_user_id);
		($class_id,$email_addr)=$sth->fetchrow_array();
		$sth->finish();
		if ($class_id == 1)
		{
			$aol_open_cnt++;	
		}
		elsif ($class_id == 2)
		{
			$hotmail_open_cnt++;	
		}
		elsif ($class_id == 3)
		{
			$yahoo_open_cnt++;	
		}
		else
		{
			$others_open_cnt++;
		}
	}	
	$sql="select email_user_id from click_history where campaign_id=? and click_date >= date_sub(curdate(),interval 1 day) and click_date < curdate()";
unless ($dbh2 && $dbh2->ping) {
$dbh2 = DBI->connect("DBI:mysql:new_mail:update2.routename.com","db_user","sp1r3V");
   }
	$sth=$dbh2->prepare($sql);
	$sth->execute($cid);
	my $eid=$sth->fetchall_arrayref();
	$sth->finish();
	for $j ( 0 .. $#{$eid} )
	{
        	my $email_user_id= $eid->[$j][0];
		$sql="select domain_class,email_addr from email_list el,email_domains ed where el.email_user_id=? and el.domain_id=ed.domain_id";
		$sth=$dbhq->prepare($sql);
		$sth->execute($email_user_id);
		($class_id,$email_addr)=$sth->fetchrow_array();
		$sth->finish();
		if ($class_id == 1)
		{
			$aol_click_cnt++;	
		}
		elsif ($class_id == 2)
		{
			$hotmail_click_cnt++;	
		}
		elsif ($class_id == 3)
		{
			$yahoo_click_cnt++;	
		}
		else
		{
			$others_click_cnt++;
		}
	}	
	if (($aol_open_cnt > 0) or ($aol_click_cnt > 0) or ($hotmail_click_cnt > 0) or ($hotmail_open_cnt > 0) or ($yahoo_open_cnt > 0) or ($yahoo_click_cnt > 0) or ($others_click_cnt > 0) or ($others_open_cnt > 0))
	{
		$sql="update campaign_log set aol_click_cnt=aol_click_cnt+$aol_click_cnt,hotmail_click_cnt=hotmail_click_cnt+$hotmail_click_cnt,yahoo_click_cnt=yahoo_click_cnt+$yahoo_click_cnt,aol_open_cnt=aol_open_cnt+$aol_open_cnt,hotmail_open_cnt=hotmail_open_cnt+$hotmail_open_cnt,yahoo_open_cnt=yahoo_open_cnt+$yahoo_open_cnt,others_click_cnt=others_click_cnt+$others_click_cnt,others_open_cnt=others_open_cnt+$others_open_cnt where campaign_id=$cid";
		print "<$sql>\n";
unless ($dbhu && $dbh2->ping) {
($dbhq,$dbhu)=$util->get_dbh();
   }
		$rows=$dbhu->do($sql);
	}
		if ($ctype eq "DAILY")
		{
			$sql="update campaign_daily_log set aol_click_cnt=aol_click_cnt+$aol_click_cnt,hotmail_click_cnt=hotmail_click_cnt+$hotmail_click_cnt,yahoo_click_cnt=yahoo_click_cnt+$yahoo_click_cnt,aol_open_cnt=aol_open_cnt+$aol_open_cnt,hotmail_open_cnt=hotmail_open_cnt+$hotmail_open_cnt,yahoo_open_cnt=yahoo_open_cnt+$yahoo_open_cnt,others_click_cnt=others_click_cnt+$others_click_cnt,others_open_cnt=others_open_cnt+$others_open_cnt where campaign_id=$cid and date_sent=curdate()";
			print "<$sql>\n";
			$rows=$dbhu->do($sql);
		}
		if ($ctype eq "TRIGGER")
		{
			$sql="update campaign_trigger_log set aol_click_cnt=aol_click_cnt+$aol_click_cnt,hotmail_click_cnt=hotmail_click_cnt+$hotmail_click_cnt,yahoo_click_cnt=yahoo_click_cnt+$yahoo_click_cnt,aol_open_cnt=aol_open_cnt+$aol_open_cnt,hotmail_open_cnt=hotmail_open_cnt+$hotmail_open_cnt,yahoo_open_cnt=yahoo_open_cnt+$yahoo_open_cnt,others_click_cnt=others_click_cnt+$others_click_cnt,others_open_cnt=others_open_cnt+$others_open_cnt where campaign_id=$cid and date_sent=curdate()";
			print "<$sql>\n";
			$rows=$dbhu->do($sql);
		}
}
