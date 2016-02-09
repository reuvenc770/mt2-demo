#!/usr/bin/perl

use strict;
use File::stat;
use Archive::Extract;
use lib "/var/www/html/newcgi-bin";
use util;

my $sth;
my $dbh;
my $dbh1;
my $dbhr;
my $lid;
my $i;
my @files;
my $outfile;

if($ARGV[0])
{
	main();
}

else
{
	print qq|Please specify directory to process. Do it!\n|;
}

sub main 
{
	$|++;
	
	## a processing directory must be specified for this script to work
	## file name also must be named suppressionListID_file.txt
	## where suppressionListID is from vendor_supp_list_info
	my $processingDirectory = $ARGV[0];
	my $logCnt=0;
	if ($processingDirectory eq "espUnsubscribes")
	{
		$logCnt=1;
	}
	print "LogCnt; $logCnt\n";
	my $check_string="/bin/ps -elf | /bin/grep -v grep | /bin/grep -v $$ | /bin/grep -v vi | grep -v pipe_w | /bin/grep perl | /bin/grep -c $processingDirectory";
	my $alreadyRunning=`$check_string`;
	chomp($alreadyRunning);
	exit if $alreadyRunning > 0;

	# connect to the util database
	my $util = util->new;
	
	$util->db_connect();
	$dbh = $util->get_dbh;
		
	my $some_dir="/home/supp/$processingDirectory";
	$outfile=$some_dir."/vendor_supp_list.txt";
	opendir(DIR, $some_dir);
	my @files = grep { /\.txt$/ || /\.csv$/ || /\.TXT$/ } readdir(DIR);
	closedir DIR;
	print "Files - $#files\n";
	my $loadfile=0;
	
	if ($#files >= 0)
	{
	    $i=0;
	    while ($i <= $#files)
	    {
	        print "File: $files[$i]\n";
	        $lid=get_list_id($files[$i]);
	        if ($lid == 0)
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
	        sleep(1);
			if (-e $tfile)
			{
		        my $filesize1=stat($tfile)->size;
		        print "<$tfile> Size: $filesize1\n";
		        if ($filesize != $filesize1)
		        {
		        	print "Still uploading: $tfile\n";
		            $i++;
		            next;
		        }
		    	
		    	my $reccnt_tot = process_file($some_dir, $files[$i],$lid,$logCnt);
		    	
				if ($reccnt_tot > 0)
				{
					if (($lid != 5502) and ($lid != 999999))
					{
						$loadfile=1;
					}
					else
					{
						unlink($outfile);
					}
					my $sql="update vendor_supp_list_info set last_updated=now() where list_id=$lid";
					my $rows=$dbh->do($sql);
				}
				unlink($infile);
			}
			$i++;
		}
		if ($loadfile)
		{
			loadFile();
		}
	}
}

sub process_file
{
	my ($dir,$tfile,$aid,$logCnt)=@_;
	
	my $start_cnt;
	my $end_cnt;
	my $reccnt_tot = 0;
	my $initial_file;
	my $t1;
	my @arr;

	print "LogCnt; $logCnt Aid: $aid\n";
	my $infile=qq|$dir/$tfile|;
	my $outfile1=$dir."/suppress_list.txt";
	open(OUT,">>$outfile");
    if (($aid == 5502) or ($aid == 999999))
    {
		($t1,$t1,@arr)=split("_",$tfile);
		$initial_file="";
		foreach my $i (@arr)
		{
        	$initial_file=$initial_file.$i."_";
		}
		chop($initial_file);
		print "Initial file: $initial_file\n";
		open(OUT1,">$outfile1");
	}
	open(IN,"<$infile");
	while (<IN>)
	{
		#if (/@/)
		#{
		#	next;
		#}
		
		chomp($_);
		
		$reccnt_tot++;
		
		## add suppression reason #25 for global suppression for NA001 lists
		## it is ignored on the vendor suppression loads
		#|25 | NA001 | Suppression because of ESP - AllInclusive |
		print OUT "$aid|$_|25\n";
    	if ($aid == 5502) 
    	{
			print OUT1 "$aid|$_|25\n";
		}
    	elsif ($aid == 999999) 
    	{
			print OUT1 "$aid|$_|27\n";
		}
	}
	close(IN);
	close(OUT);
   	if (($aid == 5502) or ($aid == 999999))
   	{
		close(OUT1);
	}
	unlink($infile);
	
    unless ($dbh1 && $dbh1->ping) {
		$dbh1 = DBI->connect("DBI:mysql:supp:suppressp.routename.com","db_user","sp1r3V");
    } #end unless
	if ($logCnt)
	{
		my $sql="select max(sid) from suppress_list_orange";
		my $sth1=$dbh1->prepare($sql);
		$sth1->execute();
		($start_cnt)=$sth1->fetchrow_array();
		$sth1->finish();
		print "Start sid: $start_cnt\n";
	}
	
    ## load NA001 into global suppression too
    if (($aid == 5502) or ($aid == 999999))
    {
		my $stmt = qq~LOAD DATA LOCAL INFILE '$outfile1' IGNORE INTO TABLE suppress_list 
			FIELDS TERMINATED BY "|" 
			LINES TERMINATED BY "\\n"
			(\@dummy, email_addr, suppressionReasonID)~;
	
	 	$dbh1->do($stmt);
		
		if ($dbh1->err() != 0)
		{
			my $errmsg = $dbh1->errstr();
			print "<$stmt> - <$errmsg>\n";
		}
		my $stmt = qq~LOAD DATA LOCAL INFILE '$outfile1' IGNORE INTO TABLE suppress_list_orange
			FIELDS TERMINATED BY "|" 
			LINES TERMINATED BY "\\n"
			(\@dummy, email_addr, suppressionReasonID)~;
	
	 	$dbh1->do($stmt);
		
		if ($dbh1->err() != 0)
		{
			my $errmsg = $dbh1->errstr();
			print "<$stmt> - <$errmsg>\n";
		}
		
		my $dbh = DBI->connect("DBI:mysql:new_mail:masterdb.routename.com","db_user","sp1r3V");
		
		open(FH, $outfile);
		
		while(my $line = <FH>)
		{
			chomp($line);
			
			my ($listID, $emailAddress, $suppressionReasonID) = split(/\|/, $line);
			$dbh->do(qq|insert into unsub_log(email_addr,unsub_date,client_id) select '$emailAddress',curdate(),client_id from email_list where email_addr='$emailAddress' and status='A'|);
			
			$dbh->do(qq|update email_list set status = 'U', unsubscribe_date = current_date(), unsubscribe_time = now() 
			where email_addr = '$emailAddress' and status = 'A'|);
		}
		
		close(FH);
    	unlink($outfile1);
		if ($logCnt)
		{
			my $sql="select max(sid) from suppress_list_orange";
			my $sth1=$dbh1->prepare($sql);
			$sth1->execute();
			($end_cnt)=$sth1->fetchrow_array();
			$sth1->finish();
			print "End sid: $end_cnt\n";
			my $added_cnt=$end_cnt-$start_cnt;
    		unless ($dbhr && $dbhr->ping) {
				$dbhr = DBI->connect("DBI:mysql:Reporting:reportingmasterdb.i.routename.com","db_user","sp1r3V");
    		} #end unless
			$sql="update EspUnsubscribeData set globalUnsubCnt=$added_cnt where effectiveDate=curdate() and espFileName='$initial_file'";
			print "$sql\n";
			$dbhr->do($sql);
		}
    }
    
        
	return($reccnt_tot);
}

sub loadFile
{
	my $table="vendor_supp_list";
    unless ($dbh1 && $dbh1->ping) {
		$dbh1 = DBI->connect("DBI:mysql:supp:suppressp.routename.com","db_user","sp1r3V");
    } #end unless
	
 	my $stmt = qq~LOAD DATA LOCAL INFILE '$outfile' IGNORE INTO TABLE $table 
		FIELDS TERMINATED BY "|" 
		LINES TERMINATED BY "\\n"
		(list_id, email_addr, \@dummy)~;

 	$dbh1->do($stmt);
        if ($dbh1->err() != 0)
        {
            my $errmsg = $dbh1->errstr();
            print "<$stmt> - <$errmsg>\n";
        }
	unlink($outfile);
}
sub get_list_id
{
    my ($filename)=@_;
    my $lid;
    my $rest_str;

    ($lid,$rest_str)=split("_",$filename);
    $_=$lid;
    if (/[0-9]/)
    {
    	print "list id is $lid\n";
        return $lid;
    }
    else
    {
        print "Bad list id: $lid\n";
        return 0;
    }
}
