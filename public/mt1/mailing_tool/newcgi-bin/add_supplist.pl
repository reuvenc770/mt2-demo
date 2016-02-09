#!/usr/bin/perl
#===============================================================================
# File: add_supplist.pl
#
# Batch job that adds to suppression lists 
#
# History
# Jim Sobeck, 01/26/2004, Creation 
#===============================================================================

# include Perl Modules

use strict;
use util;
use File::Copy;
use Lib::Database::Perl::Interface::Suppression;
use Lib::Database::Perl::Basic;

# declare variables
my $util = util->new;
my $dbh;
my $AOL_LIST = 124;
my $add_sub_dir;
my $file;
my $user_id;
my $sql;
my $sth;
my $sth1;
my $temp_id;
my $errmsg;
my ($addr);
my $rows;
my %hsh_fl_pos_names;
my ($email_addr,  $email_type,       $gender);
my ($first_name,  $middle_name,      $last_name);
my ($birth_date,  $address,          $address2);
my ($city,        $state,            $zip);
my ($country,     $marital_status,   $occupation);
my ($job_status,  $household_income, $education_level);
my ($date_captured, $member_source, $phone);
my $email_user_id;
my ($log_file, $file_name, $file_out);
my ($TRUE, $FALSE);
$TRUE  = 1 ;
$FALSE = 0 ;
my $list_id;
my $notify_email_addr;
my $mail_mgr_addr;
my $list_name;
my $reccnt_tot = 0 ;
my $reccnt_good = 0 ;
my $reccnt_bad = 0 ;
my $reccnt_errors = 0;
my $cdate;
my $sql_upd;
my $do_it;
my $output_file;
my $first_5_lines;
$first_5_lines="";

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
my $check_string1="/bin/ps -elf | /bin/grep -v grep | /bin/grep -v $$ | /bin/grep -v vi | grep -iv pipe_w | /bin/grep $filename";
my $alreadyRunning=`$check_string`;
my $test1=`$check_string1`;
chomp($alreadyRunning);
exit if $alreadyRunning > 4;

# connect to the util database 
my $dbhu;
$dbhu = DBI->connect("DBI:mysql:new_mail:masterdbp.routename.com","db_user","sp1r3V");
my $dbh3 = DBI->connect("DBI:mysql:supp:suppressp.routename.com","db_user","sp1r3V");
my $suppressionInterface    = Lib::Database::Perl::Interface::Suppression->new(
(
    'MASTER_SUPPRESSION_DATABASE' => 'supp',
    'MASTER_SUPPRESSION_DATABASE_HOST' => 'suppressp.routename.com',
    'MASTER_SUPPRESSION_DATABASE_USER' => 'db_user',
    'MASTER_SUPPRESSION_DATABASE_PASSWORD' => 'sp1r3V'
)
);

$sql="set autocommit=1";
$rows = $dbhu->do($sql);
# get some parameters from sysparm table

$sql = "select parmval from sysparm where parmkey = 'LIST_UPLOAD_MGR_ADDR'";
$sth = $dbhu->prepare($sql);
$sth->execute();
($mail_mgr_addr) = $sth->fetchrow_array();
$sth->finish();

my $immed_flag=$ARGV[0];
if ($immed_flag eq "Y")
{
#	$add_sub_dir = "/var/www/html/proc_immed_supplist/"; 
	$add_sub_dir = "/var/qmail/send_supp/"; 
}
else
{
#	$add_sub_dir = "/var/www/html/proc_supplist/"; 
	$add_sub_dir = "/var/qmail/send_new_supp/"; 
}

# open the Add Subscribers directory looking for files
my @files;
chomp (@files = `ls -tr $add_sub_dir`);
my $findex= 0;
print "File count $#files\n";
while ($findex <= $#files)
{
    if ($files[$findex] eq "." || $files[$findex] eq ".." || $files[$findex] eq
"working" || $files[$findex] eq "sav")
    {
        # skip files . and .. and the working directory
        $findex++;
        next;
    }

	# found a file to upload
	$file=$files[$findex];
	$cdate = localtime();
	print "Processing file $file starting at $cdate\n";
	process_file($add_sub_dir, $file);

	# close up everything and exit - only process one file
	$cdate = localtime();
	print "Finished processing $file at $cdate\n";
	$util->clean_up();   
	exit(0) ;
}
# if got to here, did not find any files to process
$util->clean_up();   
exit(0) ;

# ******************************************************************
# end of main - begin subroutines
# ******************************************************************

sub process_file 
{
	my ($dir, $file) = @_;
	my $therest;
	my $line;
	my $invalid_rec;
	my $input_file;
	my $output_file;
	my $temp_str;
	my $temp_filedate;

	print "Processing File $file in Directory $dir\n";

	# move the file to the working directory

	$input_file = "${dir}working/$file";
	rename "$dir$file", "$input_file";
	if ($!)
	{
		print "Error moving file to $input_file: $!\n";
	}
	
	# find out what list this file is to be uploaded to.  The filename
	# starts with the list_id and an underscore like this 23_myfile.txt
	($list_id, $therest) = split("_", $file, 2);
	print "File is being appended to list $list_id\n";

	# find out what user_id owns this list and get his email_addr
	$sql = "select list_name,temp_filedate from vendor_supp_list_info where list_id = $list_id";
	$sth = $dbhu->prepare($sql);
	$sth->execute();
	($list_name,$temp_filedate) = $sth->fetchrow_array();
	$sth->finish();
	$notify_email_addr="setup\@zetainteractive.com";
	print "notification will go to $notify_email_addr\n";
	print "List name is $list_name\n";

	# open input file
	print "Opening input file $input_file\n";
	open(IN2,"<$input_file") || print "Error - could not open input file: $input_file";

	# loop reading records in input file
	while (<IN2>)
	{
		$reccnt_tot++; 
		if ($reccnt_tot <= 5)
		{
			$first_5_lines = $first_5_lines . $_;
		}
		chomp;                               # remove last char if LineFeed etc
		$line = $_ ;
		$line=~ tr/A-Z/a-z/;
		$line =~ s///;
		$line =~ s/ //g;
		$line =~ s/	//g;
		$line =~ s/'//g;
		$line =~ s/"//g;
		$line =~ s/\\//g;
		$invalid_rec = 0;
		if (/\?/)
		{
			print "Failed first test\n";
			$invalid_rec = 1;
		}
		elsif ($line eq "")
		{
			$invalid_rec = 1;
			print "Blank Line\n";
		}
		else
		{
			my $rest_line;
			($email_addr,$rest_line)=split(",",$line);
		}
		if ($email_addr =~ /[^a-z0-9\@\_\.\-]/)
		{
			$invalid_rec = 1;
			print "<$email_addr> - Record has non A-Z, 0-9, @, _, . , or -\n";
		}
		if (length($email_addr) > 50)
		{
			$invalid_rec = 1;
			print "<$email_addr> - longer than 50 characters\n";
		}

		if (($reccnt_tot%1000) == 0)
		{
			print "processing record $reccnt_tot email address = <$email_addr>\n";
		}

		if ($invalid_rec)              # Record has Errors - Write Error Data
		{
			$reccnt_errors++;
		}
		else                    # Record is Valid - do Adds/Updates as necessary
		{
  			$_ = $email_addr;
##			$sql = "insert into vendor_supp_list(list_id,email_addr) values($list_id,'$email_addr')";
			$sql = "insert ignore into vendor_supp_list(list_id,email_addr) values($list_id,'$email_addr')";
			$rows = $dbh3->do($sql);
   			if ($dbh3->err() != 0)
    		{
				$reccnt_bad++;
    		}
			else
			{
				$reccnt_good++;
        		my $errors = $suppressionInterface->insertSuppressionEmail( { 'listID' => $list_id, 'emailAddress' => $email_addr } );
			}
		}
	}
	close IN2;
	print "Reccnt <$reccnt_good>\n";
	if ($reccnt_good > 0)
	{
		$sql = "update vendor_supp_list_info set last_updated=now(),filedate=temp_filedate,pexicom_last_upload='0000-00-00',brd_last_upload='0000-00-00',records_added=$reccnt_good where list_id=$list_id";
unless ($dbhu && $dbhu->ping) {
$dbhu = DBI->connect("DBI:mysql:new_mail:update.routename.com","db_user","sp1r3V");
   }
		$rows = $dbhu->do($sql);
   		if ($dbhu->err() != 0)
    	{
			print "Error updating vendor_supp_list_info: <$sql>\n";
		}
		$sql = "update vendor_supp_list_info set temp_filedate=null where list_id=$list_id";
unless ($dbhu && $dbhu->ping) {
$dbhu = DBI->connect("DBI:mysql:new_mail:update.routename.com","db_user","sp1r3V");
   }
		$rows = $dbhu->do($sql);
	}

	print "Done processing $input_file\n";
	print "record count total = $reccnt_tot\n";
	print "record count good = $reccnt_good\n";
	print "record count duplicate = $reccnt_bad\n";
	print "record count errors = $reccnt_errors\n";

	# send email notification to use

	print "now send notification to $notify_email_addr\n";

    open (MAIL,"| /usr/sbin/sendmail -t");
    print MAIL "Reply-To: $mail_mgr_addr\n";
    print MAIL "From: $mail_mgr_addr\n";
    print MAIL "To: $notify_email_addr\n";
	if ($reccnt_good == 0)
	{
		print MAIL "Subject: $list_name had 0 records added\n";
	}
	else
	{
    	print MAIL "Subject: Suppression List Add Status\n";
	}
    print MAIL "Content-Type: text/plain\n\n";
    print MAIL "Your File processing has completed successfully\n";
	print MAIL "The file $input_file contained $reccnt_tot records\n";
	print MAIL "$reccnt_good members where added to the list\n";
	print MAIL "$reccnt_bad duplicate records were found and rejected\n";
	print MAIL "$reccnt_errors error records were found and rejected\n";
    print MAIL "You can now use the Suppression List $list_name\n\n";
	if ($reccnt_good == 0)
	{
		print MAIL "\nFirst 5 lines of file:\n";
		print MAIL "$first_5_lines\n";
	}
    close MAIL;

	# delete the file from the working directory

	unlink($input_file) || print "Error - could NOT Remove file: $input_file\n";
}



#===============================================================================
# Sub: trim_white_space - remove leading and trailing white space
#===============================================================================
sub trim_white_space
{
	my ($strIn, $global) = @_ ;

	$global = uc($global) ;
	if ($global eq "G" )
	{
		$strIn =~ s/\s//g ;    # remove ALL white space in entire string
	}
	else
	{
		$strIn =~ s/^\s*// ;    # remove leading  white space
		$strIn =~ s/\s*$// ;    # remove trailing white space
	}
	return $strIn;
} # end sub trim_white_space

#===============================================================================
# Sub: set_fields_null
#===============================================================================
sub set_fields_null
{
 	$email_addr  = "";
 	$email_type  = "";
 	$gender      = "";
 	$first_name  = "";
 	$middle_name = "";
 	$last_name   = "";
 	$birth_date  = "";
 	$address     = "";
 	$address2    = "";
 	$city        = "";
 	$state       = "";
 	$zip         = "";
 	$country     = "";
 	$marital_status  = "";
 	$occupation  = "";
 	$job_status  = "";
 	$household_income = "";
 	$education_level  = "";

} # end sub set_fields_null

sub get_mysql_date
{
	my ($date_str) = @_;	
	my ($temp_str,$rest_of_str);
	my ($str1,$str2,$str3);
	my ($month,$day,$year);

	#print "Input Date String = $date_str\n";

	if (length($date_str) == 19)
	{
		($str1,$str2) = split(" ",$date_str,2);
		return $str1;
	}

	if (length($date_str) > 12)
	{
		return "0000-00-00";
	}
	$_ = $date_str;
	if (/#/)
	{
		return "0000-00-00";
	}
	if (/\./)
	{
		$date_str =~ s/\./-/g;	
	}
	$date_str =~ s/\//-/g ;    # Replace all / with - 
	($temp_str, $rest_of_str) = split(" ", $date_str, 2);
#	print "Temp Date String = $temp_str\n";
	($str1,$str2,$str3) = split("-",$temp_str,3);
#	print "Str1 = $str1, Str2 = $str2, Str3 = $str3\n";
	if ((length($str1) == 0) && (length($str2) == 0) && (length($str3) == 0))
	{
		return "0000-00-00";
	}
	if ((length($str1) == 4) && (length($str2) > 0) && (length($str3) > 0))
	{
		$year = $str1;
		$month = $str2;
		$day = $str3;	
		$str3 = $year;
		$str1 = $month;
		$str2 = $day;
	}
	if ((length($str2) == 0) && (length($str3) == 0))
	{
		$year = $str1;
		$month = "00";
		$day = "00";
		$str3 = $year;
		$str1 = $month;
		$str2 = $day;
	}
	
	if (length($str3) == 2)
	{
		$year= "19" . $str3;
	}
	elsif (length($str3) == 4)
	{
		$year = $str3;
	}
	else
	{
		return "0000-00-00";
	}

	if (length($str2) == 3)
	{
		$str2 = uc($str2);
		$day = $str1;
		if ($str2 eq "JAN")
		{
			$month = "01";
		}
		elsif ($str2 eq "FEB")
		{
			$month = "02";
		}
		elsif ($str2 eq "MAR")
		{
			$month = "03";
		}
		elsif ($str2 eq "APR")
		{
			$month = "04";
		}
		elsif ($str2 eq "MAY")
		{
			$month = "05";
		}
		elsif ($str2 eq "JUN")
		{
			$month = "06";
		}
		elsif ($str2 eq "JUL")
		{
			$month = "07";
		}
		elsif ($str2 eq "AUG")
		{
			$month = "08";
		}
		elsif ($str2 eq "SEP")
		{
			$month = "09";
		}
		elsif ($str2 eq "OCT")
		{
			$month = "10";
		}
		elsif ($str2 eq "NOV")
		{
			$month = "11";
		}
		elsif ($str2 eq "DEC")
		{
			$month = "12";
		}
		else
		{
			$month = "00";
		}
	}
	elsif ($list_id == 129)
	{
		$day = $str1;
		$month = $str2;
	}
	else
	{
		$day = $str2;
		$month = $str1;
	}
	$temp_str = $year . "-" . $month . "-" . $day;
#	print "Returning $temp_str\n";
	return $temp_str;
}

sub get_mysql_datetime
{
	my ($date_str) = @_;	
	my ($time_str,$hour_str,$min_str,$sec_str);
	my ($temp_str,$rest_of_str);
	my ($str1,$str2,$str3);
	my ($month,$day,$year);

#	print "Input Date String = $date_str\n";
	$_ = $date_str;
	if (/#/)
	{
		return "0000-00-00";
	}
	$date_str =~ s/\//-/g ;    # Replace all / with - 
	($temp_str, $time_str) = split(" ", $date_str, 2);
#	print "Temp Date String = $temp_str\n";
#	print "Time String = $time_str\n";
	($str1,$str2,$str3) = split("-",$temp_str,3);
#	print "Str1 = $str1, Str2 = $str2, Str3 = $str3\n";
	if ((length($str1) == 0) && (length($str2) == 0) && (length($str3) == 0))
	{
		return "0000-00-00";
	}
	if (length($date_str) == 14)
	{
		$year = substr($date_str,0,4);
		$month = substr($date_str,4,2);
		$day = substr($date_str,6,2);	
		$str3 = $year;
		$str1 = $month;
		$str2 = $day;
		$time_str = substr($date_str,8,2).":".substr($date_str,10,2).":".substr($date_str,12,2);
	}
	if ((length($str1) == 4) && (length($str2) > 0) && (length($str3) > 0))
	{
		$year = $str1;
		$month = $str2;
		$day = $str3;	
		$str3 = $year;
		$str1 = $month;
		$str2 = $day;
	}
	if ((length($str2) == 0) && (length($str3) == 0))
	{
		$year = $str1;
		$month = "00";
		$day = "00";
		$str3 = $year;
		$str1 = $month;
		$str2 = $day;
	}
	
	if (length($str3) == 2)
	{
		$year= "19" . $str3;
	}
	elsif (length($str3) == 4)
	{
		$year = $str3;
	}
	else
	{
		return "0000-00-00";
	}

	if (length($str2) == 3)
	{
		$str2 = uc($str2);
		$day = $str1;
		if ($str2 eq "JAN")
		{
			$month = "01";
		}
		elsif ($str2 eq "FEB")
		{
			$month = "02";
		}
		elsif ($str2 eq "MAR")
		{
			$month = "03";
		}
		elsif ($str2 eq "APR")
		{
			$month = "04";
		}
		elsif ($str2 eq "MAY")
		{
			$month = "05";
		}
		elsif ($str2 eq "JUN")
		{
			$month = "06";
		}
		elsif ($str2 eq "JUL")
		{
			$month = "07";
		}
		elsif ($str2 eq "AUG")
		{
			$month = "08";
		}
		elsif ($str2 eq "SEP")
		{
			$month = "09";
		}
		elsif ($str2 eq "OCT")
		{
			$month = "10";
		}
		elsif ($str2 eq "NOV")
		{
			$month = "11";
		}
		elsif ($str2 eq "DEC")
		{
			$month = "12";
		}
		else
		{
			$month = "00";
		}
	}
	else
	{
		$day = $str2;
		$month = $str1;
	}
#
#	Determine time info
#
	($temp_str, $rest_of_str) = split(" ", $time_str, 2);
	$_ = $rest_of_str;
	($hour_str,$min_str,$sec_str) = split(":",$temp_str,3);
	if (/PM/) 
	{
		$hour_str = $hour_str + 12;
	}
	$temp_str = $year . "-" . $month . "-" . $day . " " . $hour_str . ":" . $min_str . ":" . $sec_str;
	print "Returning $temp_str\n";
	return $temp_str;
}
