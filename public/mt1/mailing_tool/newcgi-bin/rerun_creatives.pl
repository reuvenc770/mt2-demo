#!/usr/bin/perl -w
use strict;
use DBI;

my $dbh=DBI->connect('DBI:mysql:new_mail:', 'root', '483298');
my $qSel=qq^SELECT creative_id FROM creative WHERE status='A'^;
my $sth=$dbh->prepare($qSel);
$sth->execute;
my $rws=$sth->rows();
while (my ($creative_id)=$sth->fetchrow) {
	print "$creative_id\n";
	system("/var/www/html/newcgi-bin/get_camp_new.pl $creative_id");
	system("/var/www/html/newcgi-bin/get_camp_3rdparty.pl $creative_id");
}
$sth->finish;

print "$rws\n";
exit;
