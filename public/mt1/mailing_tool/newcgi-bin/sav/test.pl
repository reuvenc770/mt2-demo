#!/usr/bin/perl
#use strict;
use Qspam;
sub handlemsg()
{
	my ($mfile,$code,$resultflag,$resultmsg) = @_;
	print "starting\n";
	print "$myfile\n";
    print "Resultflag - $resultflag\n";
	print "Message - $resultmsg\n";
}
qspam_start(20,&handlemsg);
qspam_send("sobeck2000\@comcast1.net","offers\@jumpjive.com","/var/www/pms/templates/camp_334.txt");
