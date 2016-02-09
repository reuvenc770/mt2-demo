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
my $shour;
my $prepull;
my $count;

my $notify_email_addr="mailops\@zetainteractive.com,abhanoori\@zetainteractive.com";
my $mail_mgr_addr="info\@zetainteractive.com";
open (MAIL,"| /usr/sbin/sendmail -t");
print MAIL "From: $mail_mgr_addr\n";
print MAIL "To: $notify_email_addr\n";
print MAIL "Subject: Deploy Count by Hour\n\n";
print MAIL "Hour\tPrepull\tCount\n";

#------  connect to the util database -----------
my $dhbu=DBI->connect('DBI:mysql:new_mail:masterdb.routename.com', 'db_user', 'sp1r3V');
$sql="select hour(send_time),prepull,count(*) from unique_campaign where send_date=date_add(curdate(),interval 1 day) group by 1,2"; 
$sth = $dhbu->prepare($sql);
$sth->execute();
while (($shour,$prepull,$count) = $sth->fetchrow_array())
{
	print MAIL "$shour\t$prepull\t$count\n";
}
$sth->finish();
close(MAIL);
