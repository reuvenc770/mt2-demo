#!/usr/bin/perl -w

use strict;
use DBI;
use GD;
require "/var/www/html/newcgi-bin/modules/Common.pm";

# connect to the util database
my $DBHQ=DBI->connect('DBI:mysql:new_mail:updatep.routename.com','db_user','sp1r3V') or die "Can't connect to DB: $!\n";
my $args=Common::get_args();


my $quer=qq|SELECT thumbnail FROM creative WHERE creative_id='$args->{creativeID}'|;
my $sth=$DBHQ->prepare($quer);
$sth->execute;
my $thumb=$sth->fetchrow;
$sth->finish;

my $file="/var/www/html/images/affiliateimages/thumbnail/$thumb";
my $img;
my $type;
my $percent="0.5";
if ($thumb=~/\.gif$/) {
	$img=GD::Image->newFromGif($file);
	$type="gif";
}
else {
	$img=GD::Image->newFromJpeg($file);
	$type="jpg";
}
my ($src_w,$src_h)=$img->getBounds();

my $new_w=$src_w * $percent;
my $new_h=$src_h * $percent;
my $newimg=GD::Image->new($new_w,$new_h);

$newimg->copyResized($img,0,0,0,0,$new_w,$new_h,$src_w,$src_h);

if ($type eq 'jpg') {
	print "Content-type: image/jpeg\n\n";
	print $newimg->jpeg('100');
}
else {
	print "Content-type: image/gif\n\n";
	print $newimg->gif();
}
exit;
