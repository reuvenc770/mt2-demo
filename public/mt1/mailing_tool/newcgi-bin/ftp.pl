#!/usr/bin/perl

use Net::FTP;
use lib "/var/www/html/newcgi-bin";
use util;
use Sys::Hostname;

my $hostname=hostname();
my $util = util->new;
my $dbh;
my $sql;
my $sth;
my $some_dir="/var/www/util/tmpmailfiles";
my $server;
my $ip;
my $username;
my $to_dir;

my $dbhq;
my $dbhu;
($dbhq,$dbhu)=$util->get_dbh();
#
# Handle special case inv1a
#
$server="inv1a";
$_ = $server;
$username="backend";
$to_dir="/var/www/util1/mailfiles";
opendir(DIR, $some_dir);
@dots = grep { /\_$server\_/ } readdir(DIR);
closedir DIR;
print "$server - $#dots\n";
if ($#dots >= 0)
{    
     $ftp = Net::FTP->new("$server", Timeout => 20, Debug => 0, Passive => 0)
      or print "Cannot connect to $server: $@\n";
	if ($ftp)
	{
    	$ftp->login($username,'!noacce$$') or print "Cannot login ", $ftp->message;
    	$ftp->cwd($to_dir);
		$ftp->ascii();
		$i=0;
		while ($i <= $#dots)
		{
    		$ftp->put("/var/www/util/tmpmailfiles/$dots[$i]") or print "put failed ", $ftp->message;
			print "Sent $dots[$i]\n";
    		unlink("/var/www/util/tmpmailfiles/$dots[$i]");
			$i++;
		}
		$ftp->quit;
	}
}

### use private IP if DB is on SD (for mailer: ymx/ispire/imail)
################################################################

my $quer=qq|SELECT colo FROM server_config WHERE name=?|;
my $s=$dbhu->prepare($quer);
$s->execute($hostname);
my $colo=$s->fetchrow;
$s->finish;

if ($colo eq 'SD') {
	$sql = "select server,ipPri from server_config where inService=1 AND type='mailer' order by server";
}
else {
	$sql=qq|SELECT server,ipPub FROM server_config WHERE inService=1 AND type='mailer' ORDER BY server|;
}
$sth = $dbhu->prepare($sql);
$sth->execute();
while (($server,$ip) = $sth->fetchrow_array()) {
	$_ = $server;
	if (($server eq "interland3") || ($server eq "interland4") || ($server eq "interland5")) {
	##if (($server eq "interland") || ($server eq "interland2") || ($server eq "interland3") || ($server eq "interland4") || ($server eq "interland5")) {
		$username="backend1";
	}
	else {
		$username="backend";
	}

	$to_dir="/var/www/util/mailfiles";
	if (/inv1a/) {
		$to_dir="/var/www/util1/mailfiles";
	}
	opendir(DIR, $some_dir);
	@dots = grep { /\_$server\_/ } readdir(DIR);
	closedir DIR;
	print "$server - $#dots\n";

	if ($#dots >= 0) {    
    	 $ftp = Net::FTP->new("$ip", Timeout => 20, Debug => 0, Passive => 0) or print "Cannot connect to $server: $@\n";
		if ($ftp) {
    		if ($ftp->login($username,'!noacce$$')) {
	    		$ftp->cwd($to_dir);
				$ftp->ascii();
				$i=0;
				while ($i <= $#dots) {
	    			if ($ftp->put("/var/www/util/tmpmailfiles/$dots[$i]")) {
						print "Sent $dots[$i]\n";
						unlink("/var/www/util/tmpmailfiles/$dots[$i]");
						$i++;
					}
					else {
						print "put failed". $ftp->message."\n";
						my $msg="I AM $hostname, problem putting files(FTP) on $server";
#						send_mail('ftperror@spirevision.com','techalerts@spirevision.com','FTP error',$msg);
					}
				}
				$ftp->quit;
			}
			else {
				print "Cannot login the ftp: ".$ftp->message."\n";
				my $msg="I AM $hostname, problem login(FTP) to $server";
#				send_mail('ftperror@spirevision.com','techalerts@spirevision.com','FTP error',$msg);
			}
		}
	}
}
$sth->finish();
exit;

sub send_mail {
    my ($from, $to, $subject, $msg)=@_;
    return 0 unless ($from && $to && $subject);

    open(MAIL, "| /usr/sbin/sendmail -t") || die "can't open sendmail";
    print MAIL "To: $to\n";
    print MAIL "From: $from\n";
    print MAIL "Subject: $subject\n\n";
    print MAIL "$msg";
    close(MAIL);
    return 1;
}
