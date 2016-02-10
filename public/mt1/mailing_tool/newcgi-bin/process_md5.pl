#!/usr/bin/perl

use strict;
use File::stat;
use Archive::Extract;
use Net::FTP;
use lib "/var/www/html/newcgi-bin";
use util;

my $util = util->new;
my $sth;
my $dbh;
my $aid;
my $i;
my $outdir="/tmp";
my $reccnt_tot=0;

# connect to the util database
$| = 1;
my ($prog, $xtra)=split(' ', $0);
my ($filename)=($prog=~/\/([^\/]+?)$/);
if ($filename eq "")
{
    $filename=$prog;
}
my $check_string="/bin/ps -elf | /bin/grep -v grep | /bin/grep -v $$ | /bin/grep -v vi | grep -v pipe_w | /bin/grep -c $filename";
my $alreadyRunning=`$check_string`;
print "Already running; $alreadyRunning\n";
chomp($alreadyRunning);
exit if $alreadyRunning > 0;

$util->db_connect();
$dbh = $util->get_dbh;
my $dbh1 = DBI->connect("DBI:mysql:supp:suppressp.routename.com","db_user","sp1r3V");

my $some_dir="/home/supp/MD5";
opendir(DIR, $some_dir);
chdir($some_dir);
my @files = grep { /\.zip$/ || /\.txt$/ || /\.csv$/ || /\.TXT$/ } sort { -M $b <=> -M $a } (readdir(
DIR)); 
closedir DIR;
print "Files - $#files\n";

if ($#files >= 0)
{
    $i=0;
    while ($i <= $#files)
    {
        print "File: $files[$i]\n";
        $aid=get_advertiser_id($files[$i]);
        if ($aid == 0)
        {
            $i++;
            next;
        }
		my $infile=$some_dir."/".$files[$i];
        my $tfile=$some_dir."/".$files[$i];
        print "<$tfile>\n";
        my $filesize=stat($tfile)->size;
        print "<$tfile> Size: $filesize\n";
        if ($filesize == 0)
        {
        	print "Filesize is zero - skipping: $tfile\n";
            $i++;
            next;
        }
        sleep(5);
        my $filesize1=stat($tfile)->size;
        print "<$tfile> Size: $filesize1\n";
        if ($filesize != $filesize1)
        {
        	print "Still uploading: $tfile\n";
            $i++;
            next;
        }
		#sendSupp($tfile,$aid);
		$_=$files[$i];
        if (/\.zip$/)
        {
        	print "zip file\n";
            my $zipfile=$some_dir."/".$files[$i];
            my $bad_zip=0;
            my $ae = Archive::Extract->new(archive => $zipfile) or $bad_zip=1;
            my $ok = $ae->extract(to => $outdir);
            if ($ok == 0)
            {
            	$tfile=$zipfile.".BAD";
                system("mv \"$zipfile\" \"$tfile\"");
                $i++;
                next;
			}
            my $unzipfiles   = $ae->files;
            my $cnt=1;
            for (@{$unzipfiles})
            {
				$infile=$_;
				my $taid=$aid."_";
				$infile=$outdir."/".$infile;
				my @args = ("/var/www/html/newcgi-bin/split_md5.sh \"$infile\" $taid");
				system(@args) == 0 or die "system @args failed: $?";
				my $add_sub_dir="/var/lib/mysql/tmp";
				my @files1;
				chomp (@files1 = `ls -tr $add_sub_dir/`);
				my $findex= 0;
				while ($findex <= $#files1)
				{
					print "$files1[$findex]\n";
    				process_file("/var/lib/mysql/tmp/", $files1[$findex],$aid);
					$findex++;
				}
				my $sql="update advertiser_info set md5_last_updated=now() where advertiser_id=$aid";
				unless ($dbh && $dbh->ping) {
					$util->db_connect();
					$dbh = $util->get_dbh;
   				}
				my $rows=$dbh->do($sql);
				$sql="update advertiser_info set md5_last_updated=now() where md5_suppression='Y' and vendor_supp_list_id=$aid";
				unless ($dbh && $dbh->ping) {
					$util->db_connect();
					$dbh = $util->get_dbh;
   				}
				$rows=$dbh->do($sql);
				unlink($infile);
				$i++;
			}
			unlink($zipfile);
			next;
		}
		my $taid=$aid."_";
		my @args = ("/var/www/html/newcgi-bin/split_md5.sh \"$infile\" $taid");
		system(@args) == 0 or die "system @args failed: $?";
#
		my $add_sub_dir="/var/lib/mysql/tmp";
		my @files1;
		chomp (@files1 = `ls -tr $add_sub_dir/`);
		my $findex= 0;
		while ($findex <= $#files1)
		{
			print "$files1[$findex]\n";
    		process_file("/var/lib/mysql/tmp/", $files1[$findex],$aid);
			$findex++;
		}
		if ($reccnt_tot > 0)
		{
			my $sql="update advertiser_info set md5_last_updated=now() where advertiser_id=$aid";
				unless ($dbh && $dbh->ping) {
					$util->db_connect();
					$dbh = $util->get_dbh;
   				}
			my $rows=$dbh->do($sql);
			$sql="update advertiser_info set md5_last_updated=now() where md5_suppression='Y' and vendor_supp_list_id=$aid";
				unless ($dbh && $dbh->ping) {
					$util->db_connect();
					$dbh = $util->get_dbh;
   				}
			$rows=$dbh->do($sql);
		}
		else
		{
    		open (MAIL,"| /usr/sbin/sendmail -t");
        	print MAIL "From: No Valid Suppression Records <info\@zetainteractive.com>\n";
        	print MAIL "To: setup\@zetainteractive.com\n";
        	print MAIL "Subject: No Valid MD5 Suppression Records for Advertiser $aid\n";
        	print MAIL "Content-Type: text/plain\n\n";
        	print MAIL "Your File processing has completed\n";
        	print MAIL "The file $infile contained $reccnt_tot valid records\n";
    		close MAIL;
		}
		unlink($infile);
		$i++;
	}
}

sub process_file
{
	my ($dir,$tfile,$aid)=@_;
	my $table="MD5AdvertiserSuppressList";
	my $infile=$dir.$tfile;
	my $outfile=$dir."MD5AdvertiserSuppressList.txt";
	open(OUT,">$outfile");
	open(IN,"<$infile");
	while (<IN>)
	{
		my $line=$_;
		chop($line);
		$_=$line;
		if (/@/)
		{
			next;
		}
        $line=~s/"//g;
        $line=~s/'//g;
        $line=~s///g;
		$line=~tr/A-Z/a-z/;
		if (length($line) != 32)
		{
			print "Skipping <$line> ".length($line)."\n";
			next;
		}
		if ($line =~ /^[a-z0-9]+$/)
		{
			$reccnt_tot++;
			print OUT "$aid|$line\r\n";
		}
		else
		{
			print "non a-z0-9 Skipping <$line> ".length($line)."\n";
		}
	}
	close(IN);
	close(OUT);
	unlink($infile);
	
 	my $stmt = qq~LOAD DATA LOCAL INFILE ? IGNORE INTO TABLE $table 
                           FIELDS TERMINATED BY "|" 
                           LINES TERMINATED BY "\r\n"
			   (advertiser_id,md5sum)~;
 	my @bind = ("$outfile");
 	$dbh1->do($stmt, undef, @bind);
        if ($dbh1->err() != 0)
        {
            my $errmsg = $dbh1->errstr();
            print "<$stmt> - <$errmsg>\n";
        }
}
sub get_advertiser_id
{
    my ($filename)=@_;
    my $aid;
    my $rest_str;

    ($aid,$rest_str)=split("_",$filename);
    $_=$aid;
    if (/[0-9]/)
    {
        return $aid;
    }
    else
    {
        print "Bad advertiser id: $aid\n";
        return 0;
    }
}
sub sendSupp
{
	my ($tfile,$aid)=@_;
    my $ftp = Net::FTP->new("54.186.245.168", Timeout => 20, Debug => 0, Passive => 0) or print "Cannot connect to 54.186.245.168: $@\n";
	if ($ftp)
	{
    	$ftp->login('mailingtool','1nt3l@') or print "Cannot login ", $ftp->message;
		$ftp->cwd("advertiser_suppressions");
		$ftp->mkdir($aid);
		$ftp->cwd($aid);
		$_=$tfile;
		if ((/\.zip$/) or (/\.xls$/))
		{
			$ftp->binary();
		}
		else
		{
			$ftp->ascii();
		}
    	$ftp->put($tfile) or print "put failed ", $ftp->message;
		print "Sent $tfile\n";
	}
	$ftp->quit;
}
