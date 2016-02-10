#!/usr/bin/perl
#-----  include Perl Modules ---------
use strict;
use CGI;
use lib "/var/www/html/newcgi-bin";
use util;

#------  get some objects to use later ---------
my $util = util->new;
my $query = CGI->new;
my $cmd=qq^ssh -i /home/svadmin/.ssh/sv-db root\@ftp.simplyremove.net "ls /home/ftp" > /tmp/s.s^;
system($cmd);
my $notify_email_addr="setup.mailing\@zetainteractive.com";
my $mail_mgr_addr="info\@zetainteractive.com";
open (MAIL,"| /usr/sbin/sendmail -t");
print MAIL "From: $mail_mgr_addr\n";
print MAIL "To: $notify_email_addr\n";
print MAIL "Subject: Suppression Files - Daily Email\n";
open(IN,"</tmp/s.s");
while (<IN>)
{
	print MAIL "$_";
}
close(MAIL);
