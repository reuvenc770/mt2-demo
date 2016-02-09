#!/usr/bin/perl
#===============================================================================
# File   : upload_datacleanse_save.cgi 
#
#--Change Control---------------------------------------------------------------
#===============================================================================

#-----------------------
# include Perl Modules
#-----------------------
use strict;
use CGI;
use Net::FTP;
use util;
use Date::Manip;
use File::Type;
use HTML::LinkExtor;
use HTML::FormatText::WithLinks;
use WWW::Curl::easy;
use URI::Split qw(uri_split uri_join);
use File::Basename;
use MIME::Base64;
use URI::Escape;

$|=1 ;   # set OUTPUT_AUTOFLUSH to true

my $util = util->new;
my $query = CGI->new;
my $ft = File::Type->new();
my $dbh;
my $rows;
my $sql;
my $crid;
my $cnt;
my $E;
my $alt_light_table_bg = $util->get_alt_light_table_bg;
my $images = $util->get_images_url;
my $host = "ftp.aspiremail.com";
my $ftpuser="espkenaspiremail";
my $ftppass="pHAquv2f";
my @remote_files;


# ----- check for login -------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}
my $ftp = Net::FTP->new("$host", Timeout => 120, Debug => 0, Passive => 1) or print "Cannot connect to $host: $@\n";
if ($ftp)
{
	$ftp->login($ftpuser,$ftppass) or print "Cannot login ", $ftp->message;
    $ftp->cwd("Incoming");
    @remote_files = $ftp->ls();
    $ftp->quit;
}
my $upload_file = $query->param('upload_file');
my $test= $query->param('test');
if ($test eq "")
{
	$test=0;
}
my ($dbhq,$dbhu)=$util->get_dbh();

#----- Pass control to PROCESS_FILE  or  PROCESS_LIST  -------
if ( $upload_file ne "" ) 
{
	&process_file($test) ;
}
else
{
	print "Location: upload_datacleanse.cgi\n\n";
}
exit(0) ;


sub process_file 
{
	my ($test)=@_;
	my ($file_name, $file_handle, $file_problem, $file_in, $line, @rest_of_line);
	my $sth;
	my $sth1;
	my $upload_dir_unix;
	my $sheetind;
	my $test_str;
	my ($infile,$outfile,$suppressfile,$cat,$country,$advstr,@rest_of_line);
	my $confirmemail;

	if ($test)
	{
		$test_str="Test";
	}
	print "Content-type: text/html\n\n";
	print<<"end_of_html";
<html><head><title>Data Cleanse $test_str Data Upload Results</title></head>
<body>
<center>
<table>
end_of_html
	# get upload subdir
	$sql = "select parmval,date_format(now(),'%Y%m%d%H%i%s') from sysparm where parmkey = 'UPLOAD_DIR_UNIX'";
	$sth1 = $dbhq->prepare($sql) ;
	$sth1->execute();
	($upload_dir_unix,$sheetind) = $sth1->fetchrow_array();
	$sth1->finish();

	# deal with filename passed to this script
	if ( $upload_file =~ /([^\/\\]+)$/ ) 
	{
		$file_name = $1;                # set file_name to $1 var - (file-name no path)
		$file_name =~ s/^\.+//;         # say what...
		$file_name =~ s/\s/_/g;         # replace WhiteSpace with UnderScore global
		$file_handle = $upload_file ;
	}
	else 
	{
		$file_problem = $query->param('upfile');
		&error("Bad File Name: $file_problem, File name can't have a slash in it!\n Rename it and try again!" ) ;
		exit(0);
	}

	#---- Open file and save File to Unix box ---------------------------

	$file_in = "${upload_dir_unix}datacleanse.${user_id}" ;
	open(SAVED,">$file_in") || &logerror("Error - could NOT open Output SAVED file: $file_in");
	$file_handle = $upload_file ;
	print SAVED <$file_handle> ;
	close SAVED;

    my ($sec, $min, $hr, $day, $month, $year, $wkdy, $yrdy, $isDST)=localtime();
    $month+=1; $year+=1900;
	if (length($month) == 1)
	{
		$month="0".$month;
	}
	if (length($day) == 1)
	{
		$day="0".$day;
	}
	open(LOG,">>/tmp/upload_datacleanse_$month$day$year.log");
	print LOG "$hr:$sec - $user_id\n";
	open(SAVED,"<$file_in") || &logerror("Error - could NOT open Input SAVED file: $file_in");
	while (<SAVED>) 
	{
		chomp;                       # remove Carriage Return (if exists)
		$line = $_;
		$line =~ s///g ;      # remove ^M from Email Addr (if exists)
		$line =~ s/\t/,/g ;
		print LOG "$hr:$sec - $user_id - <$line>\n";
		($infile,$outfile,$suppressfile,$cat,$country,$advstr,$confirmemail,@rest_of_line) = split(",", $line) ;
		chomp($advstr);
		$advstr=~s///g;
		if ($infile eq "Filename")
		{
			next;
		}
		my $iret=checkFile($infile,@remote_files);
		if (!$iret)	
		{
			print "<tr><td><font color=red>File $infile not added because couldnt find file on ftp.</font></td></tr>\n";
			next;
		}
		if ($outfile eq "")
		{
			$outfile=$infile;
		}

		my @CATSUPP;
		my $cind=0;
		if ($cat ne "")
		{
			my $caterr=0;
			my (@C)=split('\|',$cat);
			foreach my $co (@C)
			{
				my $cid;
				$sql="select category_id from category_info where category_name='$co'";
				$sth=$dbhu->prepare($sql);
				$sth->execute();
				if (($cid)=$sth->fetchrow_array())
				{
					$CATSUPP[$cind]=$cid;
					print LOG "$hr:$sec - $user_id - CAT: <$cind> <$cid>\n";
					$cind++;
				}
				else
				{
					print "<tr><td><font color=red>File $infile not added because Category $co is not legal.</font></td></tr>\n";
					$caterr=1;
				}
			}
			if ($caterr)
			{
				next;
			}
		}
		my @CSUPP;
		my $cind=0;
		if ($country ne "")
		{
			my $countryerr=0;
			my (@C)=split('\|',$country);
			foreach my $co (@C)
			{
				my $cid;
				$sql="select countryID from Country where countryCode='$co'";
				$sth=$dbhu->prepare($sql);
				$sth->execute();
				if (($cid)=$sth->fetchrow_array())
				{
					$CSUPP[$cind]=$cid;
					print LOG "$hr:$sec - $user_id - COuntry: <$cind> <$cid>\n";
					$cind++;
				}
				else
				{
					print "<tr><td><font color=red>File $infile not added because Country $co is not legal.</font></td></tr>\n";
					$countryerr=1;
				}
			}
			if ($countryerr)
			{
				next;
			}
		}
		my @ADVSUPP;
		my $cind=0;
		if ($advstr ne "")
		{
			print LOG "$hr:$sec - $user_id - $advstr\n";
			my $adverr=0;
			my (@C)=split('\|',$advstr);
			foreach my $co (@C)
			{
				my $cid;
				$sql="select advertiser_id from advertiser_info where advertiser_name='$co' and status='A' and test_flag='N'";
				$sth=$dbhu->prepare($sql);
				$sth->execute();
				if (($cid)=$sth->fetchrow_array())
				{
					$ADVSUPP[$cind]=$cid;
					print LOG "$hr:$sec - $user_id - ADV: $co $cid $cind\n";
					$cind++;
				}
				else
				{
					print "<tr><td><font color=red>File $infile not added because Advertiser $co was not found or is not active.</font></td></tr>\n";
					$adverr=1;
				}
			}
			if ($adverr)
			{
				next;
			}
		}
		else
		{
			print "<tr><td><font color=red>File $infile not added because Advertiser Name is empty.</font></td></tr>\n";
			next;
		}
		$confirmemail=~tr/A-Z/a-z/;
		if (($confirmemail ne "alphateam\@zetainteractive.com") and ($confirmemail ne "betateam\@zetainteractive.com"))
		{
			print "<tr><td><font color=red>File $infile not added because Confirmation Email($confirmemail) is invalid.</font></td></tr>\n";
			next;
		}
		if ($test)
		{
			next;
		}
		my $aidcnt=$#ADVSUPP;
		$aidcnt++;
		$sql = "insert into DataExport(fileName,client_group_id,profile_id,advertiser_id,fieldsToExport,exportType,ftpFolder,includeHeaders,otherField,otherValue,ftpServer,ftpUser,ftpPassword,SendToImpressionwiseDays,outputFilename,sendBluehornet,SendToEmail,NumberOfFiles,fullPostalOnly,suppressedFilename,addressOnly,doubleQuoteFields,loadedBySheet,ConfirmEmail,user_id) values('$infile',0,0,$aidcnt,'email_addr','Cleanse','Outgoing','N','','','ftp.aspiremail.com','espkenaspiremail','pHAquv2f','N','$outfile','N','',1,'N','$suppressfile','N','N',$sheetind,'$confirmemail',$user_id)";
		my $rows=$dbhu->do($sql);

		my $pid;
    	$sql="select LAST_INSERT_ID()";
    	my $sth=$dbhu->prepare($sql);
    	$sth->execute();
    	($pid)=$sth->fetchrow_array();
    	$sth->finish();

		foreach my $c (@CATSUPP)
		{
		    $sql="insert into DataExportCategory(exportID,categoryID) values($pid,$c)";
		    $rows=$dbhu->do($sql);
		}
		foreach my $c (@CSUPP)
		{
		    $sql="insert into DataExportCountry(exportID,countryID) values($pid,$c)";
		    $rows=$dbhu->do($sql);
		}
		foreach my $a (@ADVSUPP)
		{
		    $sql="insert into DataExportAdvertiser(exportID,advertiser_id) values($pid,$a)";
		    $rows=$dbhu->do($sql);
		}
		print "<tr><td>Data Cleanse created for $infile.</td></tr>\n";
	} 
	close SAVED;
	close LOG;
	unlink($file_in) || &logerror("Error - could NOT Remove file: $file_in");  # del file_in
	print<<"end_of_html";
</table>
<br><a href="upload_datacleanse.cgi?test=$test">Back to Upload Data Cleanse</a>&nbsp;&nbsp;<a href="mainmenu.cgi" target="_top"><img src="/mail-images/home_blkline.gif" border=0>
</center>
</body>
</html>
end_of_html
}

sub checkFile
{
	my ($infile,@remote_files)=@_;
	my $iret=0;
	foreach my $file (@remote_files)
	{
		if ($file eq $infile)
		{
			return 1;
		}
	}
	return $iret;
}
