#!/usr/bin/perl

#-----  include Perl Modules ---------
use strict;
use CGI;
use util;

#------  get some objects to use later ---------
my $util = util->new;
my $sql;
my $sth;
my @str;
my $t1;
my $t2;
my $t3;
my $tsize;

#------  connect to the util database -----------
my $dbhq;
my $dbhu;
($dbhq,$dbhu)=$util->get_dbh();
$sql = "show table status like 'record_processing'";
$sth = $dbhq->prepare($sql);
$sth->execute();
(@str) = $sth->fetchrow_array();
$sth->finish();
#
($t1,$t2,$tsize,$t3) = split(' ',$str[17]);
print "Free space <$tsize>\n";
if ($tsize < 200000)
{
	open (MAIL,"| /usr/sbin/sendmail -t");
    my $from_addr = "Innodb below 200k free <info\@spirevision.com>";
    print MAIL "From: $from_addr\n";
    print MAIL "To: jim\@spirevision.com,thota@spirevision.com,eneuner\@spirevision.com,david\@spirevision.com,raymond\@spirevision.com,jpark\@spirevision.com\n";
    print MAIL "Subject: Master Db has less than 200000 Kb free\n";
    my $date_str = $util->date(6,6);
    print MAIL "Date: $date_str\n";
    print MAIL "X-Priority: 1\n";
    print MAIL "X-MSMail-Priority: High\n";
    close MAIL;
}
