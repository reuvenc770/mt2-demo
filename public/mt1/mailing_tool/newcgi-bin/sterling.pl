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
$| = 1;
#------  connect to the util database -----------
my $dbhu=DBI->connect('DBI:mysql:new_mail:update.pcposts.com', 'db_user', 'sp1r3V');

$sql="select user_id,list_id,list_name,date_format(curdate(),'%m%d') from list where user_id in (124) and status='A' and list_name in ('Newest Records')"; 
$sth = $dbhu->prepare($sql);
$sth->execute();
if (($uid,$lid,$lname,$cdate) = $sth->fetchrow_array())
{
	my $filename="xlmarketing_".$cdate.".csv";
	open(LOG,">$filename");
	print "Processing $lid - $lname\n";
	$sql="select email_addr,source_url,member_source,capture_date,subscribe_date,subscribe_time,first_name,last_name,address,address2,city,state,zip,gender,phone,dob from email_list where list_id=? and status='A' and subscribe_date = date_sub(curdate(),interval 1 day) and phone !=''";
    unless ($dbhu && $dbhu->ping) {
		$dbhu=DBI->connect('DBI:mysql:new_mail:update.pcposts.com', 'db_user', 'sp1r3V');
    }
	$sth1=$dbhu->prepare($sql);
#	$sth1->{mysql_use_result}=1;
	$sth1->execute($lid);
	while (($em,$url,$ip,$cdate,$sdate,$stime,$fname,$lname,$address,$addrss2,$city,$state,$zip,$gender,$phone,$dob)=$sth1->fetchrow_array())
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
		print LOG "$em,$fname,$lname,$ip,$address,$addrss2,$city,$state,$zip,$gender,$t1,$t2,$t3,$dob,$url\n";
	}
	$sth1->finish(); 
	close(LOG);
#    my $ftp = Net::FTP->new("ftp.aspiremail.com", Timeout => 60, Debug => 0, Passive => 0) or print "Cannot connect to ftp.aspiremail.com: $@\n";
    my $ftp = Net::FTP->new("ftp.zetainteractive.com.com", Timeout => 60, Debug => 0, Passive => 0) or print "Cannot connect to ftp.zetainteractive.com: $@\n";
    if ($ftp)
    {
#        $ftp->login("educationmedia","dUMjGqBo") or print "Cannot login ", $ftp->message;
        $ftp->login("winniemagic","tICjrzZV") or print "Cannot login ", $ftp->message;
        $ftp->ascii();
		$ftp->put($filename) or print "put failed\n";
	}
}
$sth->finish();
