#!/usr/bin/perl
#-----  include Perl Modules ---------
use strict;
use CGI;
use Net::FTP;
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
my $pcnt;
my $t1;
my $t2;
my $t3;
my $E;
my $CNT;
my $lastmonth;
my $curmonth;
$| = 1;
#------  connect to the util database -----------
my $dbhu=DBI->connect('DBI:mysql:new_mail:slavedb.routename.com', 'db_readuser', 'Tr33Wat3r');

$sql="select date_format(date_sub(curdate(),interval 30 day),'%Y-%m'),date_format(date_sub(curdate(),interval 1 day),'%Y-%m'),date_format(curdate(),'%Y_%m_%d')";
$sth = $dbhu->prepare($sql);
$sth->execute();
($lastmonth,$curmonth,$cdate)=$sth->fetchrow_array();
$sth->finish();
my $filename="Malvern_Spire_".$cdate.".txt";
open(LOG,">$filename");

$sql="select list_id from list where user_id in (124) and status='A' and list_name in ('Newest Records','Openers','Clickers','$lastmonth','$curmonth')"; 
$sth = $dbhu->prepare($sql);
$sth->execute();
while (($lid)=$sth->fetchrow_array())
{
	print "Processing $lid \n";
	$sql="select email_addr,source_url,member_source,capture_date,subscribe_date,subscribe_time,first_name,last_name,address,address2,city,state,zip,gender,phone,dob from email_list where list_id=? and status='A' and subscribe_date = date_sub(curdate(),interval 1 day) and phone !=''";
    unless ($dbhu && $dbhu->ping) {
		$dbhu=DBI->connect('DBI:mysql:new_mail:slavedb.routename.com', 'db_readuser', 'Tr33Wat3r');
    }
	$sth1=$dbhu->prepare($sql);
	$sth1->execute($lid);
	while (($em,$url,$ip,$cdate,$sdate,$stime,$fname,$lname,$address,$addrss2,$city,$state,$zip,$gender,$phone,$dob)=$sth1->fetchrow_array())
	{
		my $turl=$url;
		$turl=~tr/[A-Z]/[a-z]/;
		$_=$turl;
		if ((/yourrewardinside.com/) or (/brandnameprizes.com/) or (/freegiftcenter.com/))
		{
		$_=$cdate;
		if (/^0000-00-00/)
		{
			$cdate=$sdate." ".$stime;
		}
		$_=$phone;
		if (/-/)
		{
			($t1,$t2,$t3)=split('-',$phone);
		}
		else
		{
			$t1=substr($phone,0,3);
			$t2=substr($phone,3,3);
			$t3=substr($phone,6,4);
		}
		print "<$t1> <$t2> <$t3>\n";
		$address=~s/,//g;
		$addrss2=~s/,//g;
		$city=~s/,//g;
		print LOG "$em,$fname,$lname,$url,$ip,$cdate,$address,$city,$state,$zip,$t1,$t2,$t3\n";
		}
	}
	$sth1->finish(); 
}
$sth->finish();
close(LOG);
    my $ftp = Net::FTP->new("ftp.zetainteractive.com", Timeout => 60, Debug => 0, Passive => 0) or print "Cannot connect to ftp.zetainteractive.com: $@\n";
    if ($ftp)
    {
        $ftp->login("spirevision","w4cSyAJO") or print "Cannot login ", $ftp->message;
        $ftp->ascii();
		$ftp->put($filename) or print "put failed\n";
	}
