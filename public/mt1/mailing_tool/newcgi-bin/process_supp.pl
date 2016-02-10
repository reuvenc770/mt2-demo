#!/usr/bin/perl

use strict;
use File::stat;
use lib "/var/www/html/newcgi-bin";
use util;

my $util = util->new;
my $sth;
my $dbh;
my $vid;
my $i;
my $cdate;
my $mail_mgr_addr;
my $sql;
my $reccnt_tot = 0;

# connect to the util database
$| = 1;    # don't buffer output for debugging log
$cdate = localtime();
##  check for concurrent upload
my ($prog, $xtra)=split(' ', $0);
my ($filename)=($prog=~/\/([^\/]+?)$/);
if ($filename eq "")
{
    $filename=$prog;
}
my $check_string="/bin/ps -elf | /bin/grep -v grep | /bin/grep -v $$ | /bin/grep -v vi | grep -v pipe_w | /bin/grep -c $filename";
my $alreadyRunning=`$check_string`;
chomp($alreadyRunning);
exit if $alreadyRunning > 0;

my $dbh1 = DBI->connect("DBI:mysql:supp:suppressp.routename.com","db_user","sp1r3V");
my $dbhu;
$dbhu = DBI->connect("DBI:mysql:new_mail:update.routename.com","db_user","sp1r3V");

my $some_dir="/var/www/html/new_supplist";
opendir(DIR, $some_dir);
my @files = grep { /\.txt$/ || /\.csv$/ || /\.TXT$/ } readdir(DIR);
closedir DIR;
print "Files - $#files\n";

if ($#files >= 0)
{
	$sql = "select parmval from sysparm where parmkey = 'LIST_UPLOAD_MGR_ADDR'";
	$sth = $dbhu->prepare($sql);
	$sth->execute();
	($mail_mgr_addr) = $sth->fetchrow_array();
	$sth->finish();
#	$sql="create temporary table tmpsupp(email_addr varchar(50) not null primary key) type=innodb";
#	my $rows=$dbh1->do($sql);
    $i=0;
    while ($i <= $#files)
    {
        print "File: $files[$i]\n";
        $vid=get_supplist_id($files[$i]);
        if ($vid == 0)
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
		my $taid=$vid."_";
		my @args = ("/var/www/html/newcgi-bin/split_supp.sh $infile $taid");
		system(@args) == 0 or die "system @args failed: $?";
		unlink($infile);
#
		my $add_sub_dir="/var/lib/mysql/tmpsupp";
		my @files1;
		chomp (@files1 = `ls -tr $add_sub_dir/${taid}*`);
		my $findex= 0;
		while ($findex <= $#files1)
		{
			print "$files1[$findex]\n";
    		process_file("/var/lib/mysql/tmpsupp/", $files1[$findex],$vid);
			$findex++;
		}
		loadData($vid,$infile);
    	$cdate = localtime();
    	print "Finished processing $infile at $cdate\n";
    	exit(0) ;
	}
}

sub process_file
{
	my ($dir,$tfile,$vid)=@_;
	my $table="vendor_supp_list";
	my $em;
	my $invalid_rec;
	my $infile=$tfile;
    my $outfile=$dir."vendor_$vid.txt";
    open(OUT,">$outfile");
    open(IN,"<$infile");
    while (<IN>)
    {
		$em=$_;
		$invalid_rec=0;
		$em=~s///g;
		$em=~s/\n//g;
		$em=~tr/[A-Z]/[a-z]/;
		$_=$em;
		if (/\?/)
		{
			$invalid_rec = 1;
		}
		elsif ($em eq "")
		{
			$invalid_rec = 1;
			print "Blank Line\n";
		}
		if ($em =~ /[^a-z0-9\@\_\.\-]/)
		{
			$invalid_rec = 1;
			print "<$em> - Record has non A-Z, 0-9, @, _, . , or -\n";
		}
		if (length($em) > 50)
		{
			$invalid_rec = 1;
			print "<$em> - longer than 50 characters\n";
		}

		if ($invalid_rec)              # Record has Errors - Write Error Data
		{
		}
		else
		{
			$reccnt_tot++;
        	print OUT "$vid|$em\n";
		}
    }
    close(IN);
    close(OUT);
    unlink($infile);
	
    my $stmt = qq~LOAD DATA LOCAL INFILE ? IGNORE INTO TABLE $table
                           FIELDS TERMINATED BY "|"
               (list_id,email_addr)~;
 	my @bind = ("$outfile");
 	$dbh1->do($stmt, undef, @bind);
        if ($dbh1->err() != 0)
        {
            my $errmsg = $dbh1->errstr();
            print "<$stmt> - <$errmsg>\n";
        }
	unlink($outfile);
}
sub get_supplist_id
{
    my ($filename)=@_;
    my $vid;
    my $rest_str;

    ($vid,$rest_str)=split("_",$filename);
    $_=$vid;
    if (/[0-9]/)
    {
        return $vid;
    }
    else
    {
        print "Bad Suppression List id: $vid\n";
        return 0;
    }
}

sub loadData
{
	my ($vid,$infile)=@_;
	my $list_name;
	my $temp_filedate;
	my $em;
	my $reccnt_bad=0;
	my $reccnt_good=0;
	my $reccnt_errors=0;
	my $rows;
	my $notify_email_addr;
	my $invalid_rec;

unless ($dbhu && $dbhu->ping) {
$dbhu = DBI->connect("DBI:mysql:new_mail:update.routename.com","db_user","sp1r3V");
   }
	$sql = "select list_name,temp_filedate from vendor_supp_list_info where list_id = $vid";
	$sth = $dbhu->prepare($sql);
	$sth->execute();
	($list_name,$temp_filedate) = $sth->fetchrow_array();
	$sth->finish();
	$notify_email_addr="setup\@zetainteractive.com";
	print "notification will go to $notify_email_addr\n";
	print "List name is $list_name\n";
	
	if ($reccnt_tot > 0)
	{
		$sql = "update vendor_supp_list_info set last_updated=now(),filedate=temp_filedate,pexicom_last_upload='0000-00-00',brd_last_upload='0000-00-00',records_added=$reccnt_good where list_id=$vid";
		print "<$sql>\n";
unless ($dbhu && $dbhu->ping) {
$dbhu = DBI->connect("DBI:mysql:new_mail:update.routename.com","db_user","sp1r3V");
   }
		$rows = $dbhu->do($sql);
   		if ($dbhu->err() != 0)
   		{
			print "Error updating vendor_supp_list_info: <$sql>\n";
		}
	}
	$sql = "update vendor_supp_list_info set temp_filedate=null where list_id=$vid";
unless ($dbhu && $dbhu->ping) {
$dbhu = DBI->connect("DBI:mysql:new_mail:update.routename.com","db_user","sp1r3V");
   }
	$rows = $dbhu->do($sql);
	print "Done processing $infile\n";

	# send email notification to use
	print "now send notification to $notify_email_addr\n";
    open (MAIL,"| /usr/sbin/sendmail -t");
	if ($reccnt_tot > 0)
	{
    	print MAIL "Reply-To: $mail_mgr_addr\n";
    	print MAIL "From: $mail_mgr_addr\n";
    	print MAIL "To: $notify_email_addr\n";
   		print MAIL "Subject: Suppression List Add Status\n";
    	print MAIL "Content-Type: text/plain\n\n";
    	print MAIL "Your File processing has completed successfully\n";
		print MAIL "The file $infile contained $reccnt_tot valid records\n";
    	print MAIL "You can now use the Suppression List $list_name\n\n";
	}
	else
	{
    	print MAIL "From: No Valid Suppression Records <info\@zetainteractive.com>\n";
    	print MAIL "To: $notify_email_addr\n";
   		print MAIL "Subject: No Valid Suppression Records\n";
    	print MAIL "Content-Type: text/plain\n\n";
    	print MAIL "Your File processing has completed\n";
		print MAIL "The file $infile for $list_name contained no valid records\n";
	}
    close MAIL;
}
