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
my ($cdate,$ctime,$userID,$username,$aid,$aname,$faname,$cstatus,$cake_creativeID,$unsub_link);
my $notify_email_addr="dpezas\@zetainteractive.com";
#my $notify_email_addr="jsobeck\@zetainteractive.com";
my $mail_mgr_addr="info\@zetainteractive.com";
open (MAIL,"| /usr/sbin/sendmail -t");
print MAIL "From: $mail_mgr_addr\n";
print MAIL "To: $notify_email_addr\n";
print MAIL "Subject: Advertiser Changes\n\n";
#------  connect to the util database -----------
my $dbhq=DBI->connect('DBI:mysql:new_mail:masterdb.routename.com', 'db_user', 'sp1r3V');
$sql="select changeDate,changeTime,ai.userID,username,advertiser_id,advertiser_name,friendly_advertiser_name,ai.status,ai.cake_creativeID,unsub_link from advertiser_infoChangeLog ai join UserAccounts ua on ua.user_id=ai.userID where changeDate=date_sub(curdate(),interval 1 day)";
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($cdate,$ctime,$userID,$username,$aid,$aname,$faname,$cstatus,$cake_creativeID,$unsub_link) = $sth->fetchrow_array())
{
	print MAIL "$cdate,$ctime,$userID,$username,$aid,$aname,$faname,$cstatus,$cake_creativeID,$unsub_link\n";
}
$sth->finish();
close(MAIL);
