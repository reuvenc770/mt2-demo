#!/usr/bin/perl
use strict;
use File::stat;
use Net::FTP;
use Archive::Extract;
use Spreadsheet::ParseExcel;
use Spreadsheet::ParseExcel::Utility qw(xls2csv);
use lib "/var/www/html/newcgi-bin";
use util;

my $util = util->new;
my $dbh;
my $sql;
my $sth;
my $some_dir="/home/supp";
my $outdir="/tmp";
my $md5_outdir="/home/supp/MD5";
my $server;
my $ip;
my $username;
my $to_dir;
my $bad_zip;
my $vid;
my $i;
my $md5_suppression;

#
my $dbhq;
my $dbhu;
my $cdate;
my $aid;
($dbhq,$dbhu)=$util->get_dbh();
$sql="select date_format(curdate(),'%Y-%m-%d')";
$sth=$dbhq->prepare($sql);
$sth->execute();
($cdate)=$sth->fetchrow_array();
$sth->finish();

$some_dir=$some_dir."/".$cdate;
opendir(DIR, $some_dir);
my @files = grep { /\.zip$/ || /\.txt$/ || /\.csv$/ || /\.TXT$/ || /\.xls/} readdir(DIR);
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
		($vid,$md5_suppression)=get_vendor_lid($aid);
		print "Vid: $vid\n";
		if ($vid > 0)
		{
			$_=$files[$i];
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
				$bad_zip=0;
				my $ae = Archive::Extract->new(archive => $zipfile) or $bad_zip=1;
				my $ok = $ae->extract(to => $outdir);
				if ($ok == 0)
				{
					print "Bad zip\n";
    				open (MAIL,"| /usr/sbin/sendmail -t");
    				my $from_addr = "Bad Suppression File<info\@zetainteractive.com>";
    				print MAIL "From: $from_addr\n";
    				print MAIL "To: setup\@zetainteractive.com\n";
#    				print MAIL "To: alert.operations\@zetainteractive.com, serverops\@zetainteractive.com\n";
    				print MAIL "Subject: Bad Zip File\n";
    				my $date_str = $util->date(6,6);
    				print MAIL "Date: $date_str\n";
    				print MAIL "X-Priority: 1\n";
    				print MAIL "X-MSMail-Priority: High\n";
    				print MAIL "$zipfile could not be uncompressed - Please re-upload\n\n";
					close(MAIL);
					$tfile=$zipfile.".BAD";
					system("mv \"$zipfile\" \"$tfile\"");
					$i++;
					next;
				}	
				print "OK: $ok\n";
				my $unzipfiles   = $ae->files;
				my $cnt=1;		
				for (@{$unzipfiles})
				{
					if (/\.xls$/)
					{
						process_xls_file($outdir,$_,$vid,$cnt,$aid,1);		
					}
					else
					{
						if ($md5_suppression eq "Y")
						{
							process_md5($md5_outdir,$_,$vid,$some_dir);
						}
						else
						{
							process_file($outdir,$_,$vid,$cnt,$aid,1);		
						}
					}
					$cnt++;
				}
				unlink($zipfile);		
			}
			elsif (/\.xls$/)
			{
				my $xlsfile=$some_dir."/".$files[$i];
				process_xls_file($some_dir,$files[$i],$vid,$i,$aid,0);
				unlink($xlsfile);

			}
			else
			{
				print "txt file\n";
				if ($md5_suppression eq "Y")
				{
					process_md5($md5_outdir,$_,$vid,$some_dir);
				}
				else
				{
					process_file($some_dir,$files[$i],$vid,$i,$aid,0);
					my $txtfile=$some_dir."/".$files[$i];
					unlink($txtfile);		
				}
			}
		}
		$i++;
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

sub get_vendor_lid
{
	my ($aid)=@_;
	my $vid;
	my $md5_suppression;

	$sql="select advertiser_info.vendor_supp_list_id,vi.md5_suppression from advertiser_info,vendor_supp_list_info vi where advertiser_id=? and advertiser_info.vendor_supp_list_id=vi.list_id";
	$sth=$dbhq->prepare($sql);
	$sth->execute($aid);
	($vid,$md5_suppression)=$sth->fetchrow_array();
	$sth->finish();
	if ($vid eq "")
	{
		$vid=0;
	}
	if (($vid == 0) and ($md5_suppression eq "Y"))
	{
		$vid=$aid;
	}
	return ($vid,$md5_suppression);
}

sub process_file
{
	my ($cdir,$infile,$vid,$cnt,$aid,$usedate)=@_;
	my $line;
	my $rstr;
	my $filedate;
	my $daycnt;
	my $tfile=$cdir."/".$infile;
	if ($usedate == 1)
	{
		my $sb=stat($tfile);
		my $datetime_string = $sb->mtime;
		my $datetime_string1 = $sb->atime;
		$sql="select date_format(from_unixtime($datetime_string),'%Y-%m-%d'),date(from_unixtime($datetime_string)),datediff(curdate(),date(from_unixtime($datetime_string)))";
		$sth=$dbhq->prepare($sql);
		$sth->execute();
		($cdate,$filedate,$daycnt)=$sth->fetchrow_array();
		$sth->finish();
		print "Datetimestring <$datetime_string> <$datetime_string1> <$cdate> <$filedate>\n";
		if ($daycnt > 9)
		{
			my $aname;
			$sql="select advertiser_name from advertiser_info where advertiser_id=?";
			$sth=$dbhq->prepare($sql);
			$sth->execute($aid);
			($aname)=$sth->fetchrow_array();
    		open (MAIL,"| /usr/sbin/sendmail -t");
    		my $from_addr = "Bad Suppression File<info\@zetainteractive.com>";
    		print MAIL "From: $from_addr\n";
    		print MAIL "To: setup\@zetainteractive.com\n";
    		print MAIL "Subject: Bad Zip File\n";
    		my $date_str = $util->date(6,6);
    		print MAIL "Date: $date_str\n";
    		print MAIL "X-Priority: 1\n";
    		print MAIL "X-MSMail-Priority: High\n";
			print MAIL "Suppression date not updated for $aid $aname\n\n";
			print MAIL "$aid $aname had a suppression file from $filedate\n";
			print MAIL" http://mailingtool.routename.com:83/cgi-bin/advertiser_disp2.cgi?pmode=U&puserid=$aid\n\n";
			close(MAIL);
			return;
		}
		$sql="update vendor_supp_list_info set temp_filedate='$filedate' where list_id=$vid";
		my $rows=$dbhu->do($sql);
		print "Datetimestring: <$sql>\n";
        if ($dbhu->err() != 0)
        {
            my $errmsg = $dbhu->errstr();
            print "<$sql> - <$errmsg>\n";
		}
	}
	my $outfile=$some_dir."/".$aid."_".$vid."_".$cdate."_".$cnt.".complete";
	my $suppfile="/var/www/html/new_supplist/".$vid."_".$cdate."_".$cnt.".txt";

	open(IN,"<$tfile");
	open(OUT,">$outfile");
	while (<IN>)
	{
		chop();
		($line,$rstr)=split(',',$_,2);
		$line=~s///g;
		$line=~s/<//g;
		$line=~s/>//g;
		$line=~s/"//g;
		$line=~s/'//g;
		$line=~s/\?//g;
		$line=~s/;//g;
		$line=~s/ //g;
		print OUT "$line\n";
	}
	close(IN);
	close(OUT);
	unlink($tfile);
	system("cp $outfile $suppfile");
}
sub process_md5
{
	my ($cdir,$infile,$vid,$indir)=@_;
	my $tfile=$indir."/".$infile;
	my $outfile=$cdir."/".$vid.".txt";

	system("mv $tfile $outfile");
}
sub process_xls_file
{
	my ($cdir,$infile,$vid,$cnt,$aid,$usedate)=@_;
	my $line;
	my $rstr;
	my $filedate;
	my $daycnt;
	my $tfile=$cdir."/".$infile;
	if ($usedate == 1)
	{
		my $datetime_string = stat($tfile)->mtime;
		$sql="select date_format(from_unixtime($datetime_string),'%Y-%m-%d'),date(from_unixtime($datetime_string)),datediff(curdate(),date(from_unixtime($datetime_string)))";
		$sth=$dbhq->prepare($sql);
		$sth->execute();
		($cdate,$filedate,$daycnt)=$sth->fetchrow_array();
		$sth->finish();
		if ($daycnt > 9)
		{
			my $aname;
			$sql="select advertiser_name from advertiser_info where advertiser_id=?";
			$sth=$dbhq->prepare($sql);
			$sth->execute($aid);
			($aname)=$sth->fetchrow_array();
    		open (MAIL,"| /usr/sbin/sendmail -t");
    		my $from_addr = "Bad Suppression File<info\@zetainteractive.com>";
    		print MAIL "From: $from_addr\n";
    		print MAIL "To: setup\@zetainteractive.com\n";
    		print MAIL "Subject: Bad Zip File\n";
    		my $date_str = $util->date(6,6);
    		print MAIL "Date: $date_str\n";
    		print MAIL "X-Priority: 1\n";
    		print MAIL "X-MSMail-Priority: High\n";
			print MAIL "Suppression date not updated for $aid $aname\n\n";
			print MAIL "$aid $aname had a suppression file from $filedate\n";
			print MAIL" http://mailingtool.routename.com:83/cgi-bin/advertiser_disp2.cgi?pmode=U&puserid=$aid\n\n";
			close(MAIL);
			return;
		}
		$sql="update vendor_supp_list_info set temp_filedate=$filedate where list_id=$vid";
		my $rows=$dbhu->do($sql);
	}
	my $outfile=$some_dir."/".$aid."_".$vid."_".$cdate."_".$cnt.".complete";
	my $suppfile="/var/www/html/new_supplist/".$vid."_".$cdate."_".$cnt.".txt";

	my $coords="1-A1:C65535";
	my $rotate = 0;
	my $email_fld;
	my $a=xls2csv( $tfile, $coords, $rotate) ;
	my @lines=split('\n',$a);
	#
	# check to figure out what field is email address
	my @fld = split(',',$lines[1]);
	my $i=0;
	$email_fld=0;
	while ($i <= $#fld)
	{
		$_=$fld[$i];
		if (/\@/)
		{
			$email_fld=$i;
		}
		$i++;
	}

	open(OUT,">$outfile");
	$i=0;
	while ($i <= $#lines)
	{
		@fld = split(',',$lines[$i]);
		$line=$fld[$email_fld];
		$_=$line;
		if (/\@/)
		{
			$line=~s///g;
			$line=~s/<//g;
			$line=~s/>//g;
			$line=~s/"//g;
			$line=~s/'//g;
			$line=~s/\?//g;
			$line=~s/;//g;
			$line=~s/ //g;
			if (length($line) > 50)
			{
				next;
			}
			print OUT "$line\n";
		}
		$i++;
	}
	close(IN);
	close(OUT);
	unlink($tfile);
	system("cp $outfile $suppfile");
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
