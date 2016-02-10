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
my $daycnt;
my $eid;
my $url;
my $LID;
my $URL;
my $did;
my $clid;
my $phone;
my $end;
my $bend;
my $CNT;
my $CNT30;
my $OC;
my $OC30;
my $end1;
my ($em,$url,$ip,$cdate,$sdate,$fname,$lname,$address,$addrss2,$city,$state,$zip,$gender);
#------  connect to the util database -----------
my $dbhq=DBI->connect('DBI:mysql:new_mail:slavedb.routename.com', 'db_readuser', 'Tr33Wat3r');
my $begin=2700672286;
$sql="select max(email_user_id) from email_list"; 
$sth = $dbhq->prepare($sql);
$sth->execute();
($end)=$sth->fetchrow_array();
$sth->finish();

while ($begin < $end)
{
	$bend=$begin+99999;
	$sql="select client_id,email_user_id,source_url,emailUserActionTypeID,datediff(curdate(),subscribe_date) from email_list where subscribe_date >= date_sub(curdate(),interval 7 day) and subscribe_date <= curdate() and email_list.status='A' and email_user_id between ? and ?"; 
    unless ($dbhq && $dbhq->ping) {
		$dbhq=DBI->connect('DBI:mysql:new_mail:slavedb.routename.com', 'db_readuser', 'Tr33Wat3r');
    } #end unless
	$sth = $dbhq->prepare($sql);
	$sth->execute($begin,$bend);
	while (($cid,$eid,$url,$lname,$daycnt) = $sth->fetchrow_array())
	{
		if ($url ne "")
		{
			my ($t1,$t2)=split('\?',$url);
			$url=$t1;
			$url=~s///g;
			$url=~s/\n//g;
			if ($daycnt <= 7)
			{
				$CNT->{$cid}{$url}++;
				if (($lname == 1) or ($lname == 2)) 
				{
					$OC->{$cid}{$url}++;
				}
			}
		}
	}
	$sth->finish(); 
	$begin=$begin+100000;
}
my $cname;
my $notify_email_addr="jherlihy\@zetainteractive.com";
my $cc_email_addr="jsobeck\@zetainteractive.com,vkethu\@zetainteractive.com";
my $mail_mgr_addr="info\@zetainteractive.com";
    open (MAIL,"| /usr/sbin/sendmail -t");
    print MAIL "From: $mail_mgr_addr\n";
    print MAIL "To: $notify_email_addr\n";
    print MAIL "Cc: $cc_email_addr\n";
    print MAIL "Subject: URL Counts - Last 7 Days\n";
my $ctype;
my $countryName;
my $listOwner;
foreach (keys %{$CNT})
{
	$cid=$_;
	$sql="select username,client_type,c.countryCode,clientStatsGroupingLabel from user u left outer join ClientStatsGrouping csg on csg.clientStatsGroupingID=u.clientStatsGroupingID
            left outer join Country c on u.countryID = c.countryID where u.user_id=?"; 
	$sth = $dbhq->prepare($sql);
	$sth->execute($cid);
	($cname,$ctype,$countryName,$listOwner)=$sth->fetchrow_array();
	$sth->finish();
	foreach (keys %{$CNT->{$cid}})
	{
    	if ($CNT->{$cid}{$_} >= 50)
    	{
			if ($OC->{$cid}{$_})
			{
			}
			else
			{
				$OC->{$cid}{$_}=0;
			}
        	print MAIL "$cid,$cname,$ctype,$countryName,$listOwner,$_,$CNT->{$cid}{$_},$OC->{$cid}{$_}\n";
		}
    }
}
close(MAIL);
