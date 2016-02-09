#!/usr/bin/perl
#-----  include Perl Modules ---------
use strict;
use DBI;
use Number::Format;
my $f1=Number::Format->new();

#------  get some objects to use later ---------
my $sql;
my $sth;
my $daydiff;
my $exportID;
my $recordCount;
my $OLDCNT;

my $dbhu=DBI->connect('DBI:mysql:new_mail:masterdb.i.routename.com', 'db_user', 'sp1r3V');
#
$sql="select datediff(curdate(),parmval) from sysparm where parmkey='LAST_ESP_EMAIL'";
$sth = $dbhu->prepare($sql);
$sth->execute();
($daydiff)=$sth->fetchrow_array();
$sth->finish();
# if already sent email then exit
if ($daydiff == 0)
{
	exit();
}
#
my $cnt;
$sql="select count(*) from DataExport where status='Active' and lastUpdated < curdate()"; 
$sth = $dbhu->prepare($sql);
$sth->execute();
($cnt)=$sth->fetchrow_array();
$sth->finish();
if ($cnt > 0)
{
	exit();
}

$sql="select exportID,recordCount from DataExportLog where logDate=date_sub(curdate(),interval 1 day) and exportID > 0";
$sth = $dbhu->prepare($sql);
$sth->execute();
while (($exportID,$recordCount)=$sth->fetchrow_array())
{
	$OLDCNT->{$exportID}=$recordCount;
}
$sth->finish();
#
# Get current data
#
my $filename;
my $alert=0;
my $datastr="";
$sql="select exportID,recordCount,filename from DataExport where status='Active' order by filename";
$sth = $dbhu->prepare($sql);
$sth->execute();
while (($exportID,$recordCount,$filename)=$sth->fetchrow_array())
{
	if ($OLDCNT->{$exportID})
	{
		my $chg=(($recordCount-$OLDCNT->{$exportID})/$OLDCNT->{$exportID})*100;
		if (($chg >= 20) or ($chg <= -20))
		{
			$alert=1;
		}
		$chg=$f1->round($chg);
		$datastr.="$filename\t$recordCount\t$OLDCNT->{$exportID}\t$chg\%\n";
	}
}
$sth->finish();
my $notify_email_addr="espken\@zetainteractive.com";
my $cc_email_addr="jsobeck\@zetainteractive.com";
my $mail_mgr_addr="info\@zetainteractive.com";
open (MAIL,"| /usr/sbin/sendmail -t");
print MAIL "From: $mail_mgr_addr\n";
print MAIL "To: $notify_email_addr\n";
print MAIL "Cc: $cc_email_addr\n";
if ($alert)
{
	print MAIL "Subject: ALERT - Data Export Daily Report\n\n";  
}
else
{
	print MAIL "Subject: Data Export Daily Report\n\n";  
}
print MAIL "Filename\tToday Count\tYesterday Count\tChange\n";
print MAIL "$datastr\n";
close(MAIL);
$sql="update sysparm set parmval=curdate() where parmkey='LAST_ESP_EMAIL'";
my $rows=$dbhu->do($sql);
