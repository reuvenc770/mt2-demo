#!/usr/bin/perl
#-----  include Perl Modules ---------
use strict;
use CGI;
use lib "/var/www/html/newcgi-bin";
use util;

#------  get some objects to use later ---------
my $util = util->new;
my $query = CGI->new;
my $name;
my $sql;
my $sth;
my $sth1;
my $dbh;
my $rows;
my $sl;
my $aid;
my $fldname;
my $cid;
my $pid;
my $pname;
my $cnt;
my $cnt1;
my $lid;
my $lname;
my $eid;
my $url;
my $LID;
my $URL;
my $did;
my $clid;
my $phone;
my $dob;
my $isp;
my $uid;
my ($em,$url,$ip,$cdate,$sdate,$fname,$lname,$address,$addrss2,$city,$state,$zip,$gender);
my $stime;
my $E;
my $CNT;
my $class_id;
$| = 1;
my $domain_id;
my $address2;
my $country;
my $reccnt=6170;
#------  connect to the util database -----------
my $dbhq=DBI->connect('DBI:mysql:new_mail:slavedb.routename.com', 'db_readuser', 'Tr33Wat3r');
my $dbhu=DBI->connect('DBI:mysql:new_mail:masterdb.routename.com', 'db_user', 'sp1r3V');

open(OUT,">/tmp/clickers.csv");
$sql="select emailUserID from EmailUserAction where emailUserActionTypeID=2 and emailUserActionDate >= date_sub(curdate(),interval 30 day)"; 
my $sthq=$dbhq->prepare($sql);
$sthq->execute();
while (($eid)=$sthq->fetchrow_array())
{
	print OUT "$eid\n";
}
$sthq->finish();
close(OUT);
open(IN,"</tmp/clickers.csv");
while (<IN>)
{
	$eid=$_;
	$sql="select email_addr,el.domain_id,
                subscribe_date,
                subscribe_time,
                first_name,
                last_name,
                address,
                address2,
                city,
                state,
                zip,
                country,
                dob,
                gender,
                phone,
                capture_date,
                member_source,
                source_url from email_list el, email_domains ed where el.domain_id=ed.domain_id and el.status='A' and el.email_user_id=? and ed.domain_class=3";
    while (!$dbhq->ping)
    {
        print "Getting connection\n";
    unless ($dbhq && $dbhq->ping) {
        $dbhq=DBI->connect('DBI:mysql:new_mail:slavedb.routename.com', 'db_readuser', 'Tr33Wat3r');
    }
	}
	$sth=$dbhq->prepare($sql);
	$sth->execute($eid);
	if (($em,$domain_id,$sdate,$stime,$fname,$lname,$address,$address2,$city,$state,$zip,$country,$dob,$gender,$phone,$cdate,$ip,$url)=$sth->fetchrow_array())
	{
		if ($E->{$em})
		{
			if ($E->{$em} == 1)
			{
				$sql="select count(*) from email_list where email_addr=? and list_id in (45191,45193)";
    while (!$dbhu->ping)
    {
        print "Getting connection\n";
    unless ($dbhu && $dbhu->ping) {
        $dbhu=DBI->connect('DBI:mysql:new_mail:masterdb.routename.com', 'db_user', 'sp1r3V');
    }
	}
				my $sth1=$dbhu->prepare($sql);
				$sth1->execute($em);
				($cnt)=$sth1->fetchrow_array();
				$sth1->finish();
				if ($cnt == 0)
				{
					$fname=~s/'/''/g;
					$lname=~s/'/''/g;
					$address=~s/'/''/g;
					print "Adding $em - 45193\n";
					$sql="insert into email_list(list_id,email_addr,domain_id,subscribe_date,subscribe_time,first_name,last_name,address,address2,city,state,zip,country,dob,gender,phone,capture_date,member_source,source_url,status) values(45193,'$em',$domain_id,'$sdate','$stime','$fname','$lname','$address','$address2','$city','$state','$zip','$country','$dob','$gender','$phone','$cdate','$ip','$url','A')";
    while (!$dbhu->ping)
    {
        print "Getting connection\n";
    unless ($dbhu && $dbhu->ping) {
        $dbhu=DBI->connect('DBI:mysql:new_mail:masterdb.routename.com', 'db_user', 'sp1r3V');
    }
	}
					my $rows=$dbhu->do($sql);
					$reccnt++;
#					if ($reccnt >= 30000)
#					{
#						exit;
#					}
				}
			}
			$E->{$em}++;
		}
		else
		{
			$E->{$em}=1;
		}
	}
	$sth->finish();
}
close(IN);
