#!/usr/bin/perl
# *****************************************************************************************
# build_3rdparty_unsub.pl
#
# Batch program that runs from cron to build daily unsub log 
#
# History
# Jim Sobeck,   01/25/06,   Created
# Jim Sobeck,   10/05/07,   Added logic to produce total unsub file
# *****************************************************************************************

use strict;
use Net::FTP;
use util;

my $util = util->new;
my $sth;
my $sth1;
my $sth2;
my $sth3;
my $dbh;
my $sql;
my $rows;
my $cdate = localtime();
my $program = "build_unsub_daily.pl";
my $errmsg;
my $cname;
my $file_date;
my $id;
my $mailer_name;
my $ftp_ip;
my $ftp_username;
my $ftp_password;
my $list_path;
my $client_id;
my $email_addr;
my $filename;
my ($id,$mailer_name,$ftp_ip,$ftp_username,$ftp_password,$list_path);
my $got_rec;

# connect to the util database
my $dbhq;
my $dbhu;
($dbhq,$dbhu)=$util->get_dbh();
my $dbh3 = DBI->connect("DBI:mysql:supp:sv-db-4p.routename.com","db_user","sp1r3V");
$| = 1;
#
$sql = "select DATE_FORMAT(date_sub(curdate(),INTERVAL 1 DAY),'%m%d%Y')";
$sth = $dbhq->prepare($sql);
$sth->execute();
($file_date) = $sth->fetchrow_array();
$sth->finish();

open (LOG, ">> /data4/data/unsubs/uns_${file_date}.txt");
$sql="select email_addr from unsub_log where unsub_date >= date_sub(curdate(),interval 1 day) and unsub_date < curdate()";
$sth2 = $dbhq->prepare($sql);
$sth2->execute();
while (($email_addr) = $sth2->fetchrow_array())
{
	print LOG "$email_addr\n";
}
$sth2->finish();
close(LOG);
my @args = ("/var/www/html/newcgi-bin/copy_unsubs.sh $file_date");
system(@args) == 0 or die "system @args failed: $?";

sub ftp_file
{
    my ($ftp_ip,$ftp_username,$ftp_password,$remote_path,$mydir,$tfilename) = @_
;
    my $filename;
    my $to_dir;
	my $mesg;

    $filename=$mydir . $tfilename;
	$mesg="";
    my $ftp = Net::FTP->new("$ftp_ip", Timeout => 120, Debug => 0, Passive => 0)
 or $mesg="Cannot connect to $ftp_ip: $@";
    if ($ftp)
    {
        $ftp->login($ftp_username,$ftp_password) or $mesg="Cannot login $ftp_ip"
;
        $ftp->cwd($remote_path);
        $ftp->binary();
        $ftp->put($filename) or $mesg="put failed for $filename to $remote_path on $ftp_ip";
    }
    $ftp->quit;
    print "Return from ftp to $ftp_ip $tfilename <$remote_path> $mesg\n";
}
