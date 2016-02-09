#!/usr/bin/perl
#===============================================================================
# File: add_subscribers.pl
#
# Batch job that adds Subscribers
#
# History
# Grady Nash, 10/30/2001, Creation
# Jim Sobeck, 05/20/2002, Added Date Captured and Member Source
# Jim Sobeck, 05/22/2002, Add Special EDealsDirect Processing
# Jim Sobeck, 09/10/2002, Added moving of data to HotList
#===============================================================================

# include Perl Modules

use strict;
use pms;
use Email::Valid;
use File::Copy;

# declare variables
my $EDEALSDIRECT_LIST = 48;
my $pms = pms->new;
my $dbh;
my $add_sub_dir;
my $file;
my $user_id;
my $sql;
my $sth;
my $sth1;
my $temp_id;
my $errmsg;
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
my $cdate;

$| = 1;    # don't buffer output for debugging log

# connect to the pms database 

$pms->db_connect();
$dbh = $pms->get_dbh;

# get some parameters from sysparm table

$sql = "select parmval from sysparm where parmkey = 'LIST_UPLOAD_MGR_ADDR'";
$sth = $dbh->prepare($sql);
$sth->execute();
($mail_mgr_addr) = $sth->fetchrow_array();
$sth->finish();

$sql = "select parmval from sysparm where parmkey = 'ADD_SUB_DIR'";
$sth = $dbh->prepare($sql);
$sth->execute();
($add_sub_dir) = $sth->fetchrow_array();
$sth->finish();

# open the Add Subscribers directory looking for files
opendir(DIR, $add_sub_dir);
while (defined($file = readdir(DIR)))
{
    if ($file eq "." || $file eq ".." || $file eq "working")
    {
        # skip files . and .. and the working directory
        next;
    }

	# found a file to upload

	$cdate = localtime();
	print "Processing file $file starting at $cdate\n";

	process_file($add_sub_dir, $file);

	# close up everything and exit - only process one file

	$cdate = localtime();
	print "Finished processing $file at $cdate\n";

	closedir(DIR);
	$pms->clean_up();   
	exit(0) ;
}
# if got to here, did not find any files to process

closedir(DIR);
$pms->clean_up();   
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

	print "Processing File $file in Directory $dir\n";

	# move the file to the working directory

	$input_file = "${dir}working/$file";
	$output_file = "/var/www/pms/data/partner/" . $file;
	my @args = ("cp","$dir$file","$output_file");
    system(@args) == 0 or die "system @args failed: $?";
	rename "$dir$file", "$input_file";
	if ($!)
	{
		print "Error moving file to $input_file: $!\n";
		$pms->clean_up();   
		exit(0) ;
	}
	
	# find out what list this file is to be uploaded to.  The filename
	# starts with the list_id and an underscore like this 23_myfile.txt

	($list_id, $therest) = split("_", $file, 2);

	print "File is being added to list $list_id\n";

	# find out what user_id owns this list and get his email_addr

	$sql = "select list.user_id, email_addr, list_name
		from list, user
		where list_id = $list_id and
		list.user_id = user.user_id";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	($user_id, $notify_email_addr, $list_name) = $sth->fetchrow_array();
	$sth->finish();

	print "List is for user_id $user_id\n";
	print "notification will go to $notify_email_addr\n";
	print "List name is $list_name\n";

	# load this users file layout

	get_file_layout();   

	# open input file

	print "Opening input file $input_file\n";
	open(IN2,"<$input_file") || print "Error - could not open input file: $input_file";

	# loop reading records in input file

	while (<IN2>)
	{
		$reccnt_tot++; 
		chomp;                               # remove last char if LineFeed etc

		$line=~ tr/A-Z/a-z/;
		$line = $_ ;
		if (/\?/)
		{
			$invalid_rec = 1;
		}
		elsif (/spamcop/)
		{
			$invalid_rec = 1;
		}
		elsif (/hrsa.gov/)
		{
			$invalid_rec = 1;
		}
		elsif (/abuse@/)
		{
			$invalid_rec = 1;
		}
		else
		{
			&fmt_input_fields($line);
			$invalid_rec = &edit_rec($line);
		}

		if (($reccnt_tot%1000) == 0)
		{
			print "processing record $reccnt_tot email address = $email_addr\n";
		}


		if ($invalid_rec)                    # Record has Errors - Write Error Data
		{
			$reccnt_bad++;
		}
		else                                 # Record is Valid - do Adds/Updates as necessary
		{
	#--- Check to see if email address already exists
			if ($list_id != $EDEALSDIRECT_LIST)
			{
				$sql = "select email_user_id from email_user
					where email_addr = '$email_addr'";
			}
			else
			{
				$sql = "select email_user_id from edealsdirect_member 
					where email_addr = '$email_addr'";
			}
			$sth = $dbh->prepare($sql) ;
			$sth->execute();
        	$errmsg = $dbh->errstr();
			($email_user_id) = $sth->fetchrow_array();
			$sth->finish();
			if ($email_user_id eq "")
			{
				$reccnt_good++;
				if ($list_id != $EDEALSDIRECT_LIST)
				{
					&add_upd_list_member();
					&add_upd_email_user();
				}
				else
				{
					&add_edealsdirect_list_member();
				}
				print "Email: $email_addr added\n";
			}
			else
			{
				$reccnt_bad++;
				if ($list_id != $EDEALSDIRECT_LIST)
				{
					&add_upd_email_user();
				}
				print "Email: $email_addr already exists - not added\n";
			}
		}
	}
	close IN2;

	print "Done processing $input_file\n";
	print "record count total = $reccnt_tot\n";
	print "record count good = $reccnt_good\n";
	print "record count bad = $reccnt_bad\n";

	# send email notification to use

	print "now send notification to $notify_email_addr\n";

    open (MAIL,"| /usr/sbin/sendmail.bak -t");
    print MAIL "Reply-To: $mail_mgr_addr\n";
    print MAIL "From: $mail_mgr_addr\n";
    print MAIL "To: $notify_email_addr\n";
    print MAIL "Subject: Email File Status\n";
    print MAIL "Content-Type: text/plain\n\n";
    print MAIL "Your Email File processing has completed successfully\n";
	print MAIL "The file contained $reccnt_tot records\n";
	print MAIL "$reccnt_good members where added to the list\n";
	print MAIL "$reccnt_bad bad records were found and rejected\n";
    print MAIL "You can now use the Email List $list_name\n\n";
    close MAIL;

	# delete the file from the working directory

	unlink($input_file) || print "Error - could NOT Remove file: $input_file\n";
}

#===============================================================================
# Sub: add_upd_list_member
#===============================================================================

sub add_upd_list_member
{
	my $record_found;
	my $rows; 
	
	# ---- See if list_member rec already exists -----

#	$sql = "select count(*) from list_member 
#		where list_id = $list_id and email_user_id = $email_user_id";
	$sql = "select count(*) from member_list where email_addr = '$email_addr'";
	$sth1 = $dbh->prepare($sql) ;
	$sth1->execute();
	($record_found) = $sth1->fetchrow_array();
	$sth1->finish();

	if ($record_found == 0) 
	{
		$sql = "insert into member_list(list_id, email_addr,subscribe_datetime, 
			status) values ( $list_id, '$email_addr', now(), 'A')"; 
		$rows = $dbh->do($sql);
   		if ($dbh->err() != 0)
    	{
    		$errmsg = $dbh->errstr();
       		print "Error Inserting or Updating member_list record: $sql : $errmsg";
       		exit(0);
    	}
	}

} 

#===============================================================================
# Sub: add_edealsdirect_list_member
#===============================================================================

sub add_edealsdirect_list_member
{
	my $record_found;
	my $rows; 
	
	$sql = "insert into edealsdirect_member (list_id, subscribe_datetime, status, email_addr,email_type) values ( $list_id, now(), 'A','$email_addr','H')"; 
$rows = $dbh->do($sql);
   	if ($dbh->err() != 0)
   	{
   		$errmsg = $dbh->errstr();
      		print "Error Inserting or Updating list_member record: $sql : $errmsg";
   		exit(0);
   	}
} 

#===============================================================================
# Sub: get_file_layout 
#  - Read table: user_file_layout to get specific file layout for user
#  - Use hash to assign Field Names from the 'email_user' table
#      - %hsh_fl_pos_names{key} = value
#           a. Key = Position Number from 'user_file_layout' rec
#           b. Value = Field Name from 'email_user' table.  
#===============================================================================

sub get_file_layout
{
	my ($cat_id, $field_position, $table_name, $db_field);
	my ($reccnt, $field, $ftype, $flength);
	my ($db_value, $key, $value);
	my (@fl_fields, $fl_field, $i);

	#---------------------------------------------------------------------------
	# Get user_file_layout for user.  
	# - The ORDER of the fields in the SELECT stmt matter.  They are used in an 
	#   array to assign 'Field Names' to a hash based on the index count.
	#---------------------------------------------------------------------------
	$sql = qq{select 
		email_addr_pos,     email_type_pos,      gender_pos,
		first_name_pos,     middle_name_pos,     last_name_pos,
		birth_date_pos,     address_pos,         address2_pos,
		city_pos,           state_pos,           zip_pos,
		country_pos,        marital_status_pos,  occupation_pos,
		job_status_pos,     income_pos,          education_pos,
		date_capture_pos,	member_source_pos,   phone_pos
		from user_file_layout where user_id = $user_id};
	$sth = $dbh->prepare($sql) ;
	$sth->execute();
	(@fl_fields) = $sth->fetchrow_array() ;
	$sth->finish();
	
	$i = 0 ;
	foreach $fl_field (@fl_fields)
	{
		if ( $fl_field ne "" )
		{	#-------------------------------------------------------------------
			# Assign Field Names (from 'email_user' table).  Field Names are
			# assigned based on index which maps back to select statement above.
			# The Field Names are from the 'email_user' table.
			#-------------------------------------------------------------------
			if ($i == 0 ) { $hsh_fl_pos_names{$fl_field} = 'email_addr' ; } 
			if ($i == 1 ) { $hsh_fl_pos_names{$fl_field} = 'email_type' ; } 
			if ($i == 2 ) { $hsh_fl_pos_names{$fl_field} = 'gender' ; } 
			if ($i == 3 ) { $hsh_fl_pos_names{$fl_field} = 'first_name' ; } 
			if ($i == 4 ) { $hsh_fl_pos_names{$fl_field} = 'middle_name' ; } 
			if ($i == 5 ) { $hsh_fl_pos_names{$fl_field} = 'last_name' ; } 
			if ($i == 6 ) { $hsh_fl_pos_names{$fl_field} = 'birth_date' ; } 
			if ($i == 7 ) { $hsh_fl_pos_names{$fl_field} = 'address' ; } 
			if ($i == 8 ) { $hsh_fl_pos_names{$fl_field} = 'address2' ; } 
			if ($i == 9 ) { $hsh_fl_pos_names{$fl_field} = 'city' ; } 
			if ($i == 10 ) { $hsh_fl_pos_names{$fl_field} = 'state' ; } 
			if ($i == 11 ) { $hsh_fl_pos_names{$fl_field} = 'zip' ; } 
			if ($i == 12 ) { $hsh_fl_pos_names{$fl_field} = 'country' ; } 
			if ($i == 13 ) { $hsh_fl_pos_names{$fl_field} = 'marital_status' ; } 
			if ($i == 14 ) { $hsh_fl_pos_names{$fl_field} = 'occupation' ; } 
			if ($i == 15 ) { $hsh_fl_pos_names{$fl_field} = 'job_status' ; } 
			if ($i == 16 ) { $hsh_fl_pos_names{$fl_field} = 'household_income' ; } 
			if ($i == 17 ) { $hsh_fl_pos_names{$fl_field} = 'education_level' ; } 
			if ($i == 18 ) { $hsh_fl_pos_names{$fl_field} = 'date_captured' ; } 
			if ($i == 19 ) { $hsh_fl_pos_names{$fl_field} = 'member_source' ; } 
			if ($i == 20 ) { $hsh_fl_pos_names{$fl_field} = 'phone' ; } 
		}

		$i++ ;
	}

}  # end sub get_file_layout 


#===============================================================================
# Sub: add_upd_email_user
#===============================================================================
sub add_upd_email_user
{
	my ($sql, $sth, $rows, $sql_ins, $sql_upd, $key, $value);
	my ($rec_found);
    my ($email_found,$old_addr,$old_status,$old_userid);
	my $tmp_zip;


	#--- Does email_user already exist? --------------------
	$old_addr = "";
	$sql = "select email_user_id,address,status,user_id from email_user
		where email_addr = '$email_addr'";
	$sth = $dbh->prepare($sql) ;
	$sth->execute();
	($email_user_id,$old_addr,$old_status,$old_userid) = $sth->fetchrow_array();
	$sth->finish();

	if ($email_user_id eq "")
	{
		$rec_found = $FALSE;
	}
	else
	{
		$rec_found = $TRUE;
	}

	#--- If Numeric Fields are Null then set var to 'null' -----

	if ( $marital_status eq "" )    { $marital_status = "null" ; }
	if ( $occupation eq "" )        { $occupation = "null" ; }
	if ( $job_status eq "" )        { $job_status = "null" ; }
	if ( $household_income eq "" )  { $household_income = "null" ; }
	if ( $education_level eq "" )   { $education_level = "null" ; }

	if ($rec_found)
	{	
		#---- Format Update statement (only chg fields where value is present) ----
	
		$sql_upd = qq{update email_user set } ;
		if ($email_type ne "" )        { $sql_upd = $sql_upd . qq{email_type = '$email_type',}; }
		if ($gender ne "" )            { $sql_upd = $sql_upd . qq{gender = '$gender',}; }
		if ($first_name ne "" )        { $sql_upd = $sql_upd . qq{first_name = '$first_name',}; }
		if ($middle_name ne "" )       { $sql_upd = $sql_upd . qq{middle_name = '$middle_name',}; }
		if ($last_name ne "" )         { $sql_upd = $sql_upd . qq{last_name = '$last_name',}; }
		if (($birth_date ne "0000-00-00" ) and ($birth_date ne ""))        { $sql_upd = $sql_upd . qq{birth_date = '$birth_date',}; }
		if ($address ne "" )           { $sql_upd = $sql_upd . qq{address = '$address',}; }
		if ($address2 ne "" )          { $sql_upd = $sql_upd . qq{address2 = '$address2',}; }
		if ($city ne "" )              { $sql_upd = $sql_upd . qq{city = '$city',}; }
		if ($state ne "" )             { $sql_upd = $sql_upd . qq{state = '$state',}; }
		if ($zip ne "" )               { $sql_upd = $sql_upd . qq{zip = '$zip',}; }
		if ($country ne "" )           { $sql_upd = $sql_upd . qq{country = '$country',}; }
		if ($marital_status ne "" )    { $sql_upd = $sql_upd . qq{marital_status = $marital_status,}; }
		if ($occupation ne "" )        { $sql_upd = $sql_upd . qq{occupation = $occupation,}; }
		if ($job_status ne "" )        { $sql_upd = $sql_upd . qq{job_status = $job_status,}; }
		if ($household_income ne "" )  { $sql_upd = $sql_upd . qq{income = $household_income,}; }
		if ($education_level ne "" )   { $sql_upd = $sql_upd . qq{education = $education_level,}; }
		if ($phone ne "" )   { $sql_upd = $sql_upd . qq{phone = '$phone',}; }

		$sql_upd =~ s/,$//;              # remove trailing comma 
		$sql_upd = $sql_upd . qq{ where email_user_id = $email_user_id };

		# Update email_user table
#		print "SQL Stmt - $sql_upd\n";
		$rows = $dbh->do($sql_upd);
   		if ($dbh->err() != 0)
    	{
        	$errmsg = $dbh->errstr();
        	print "Error Updating email_user record: $sql_upd : $errmsg";
        	exit(0);
    	}
    	#
    	#	Check to see if need to change to HotList
    	#
    	if (($old_userid != 34) && ($old_userid != 40) && ($old_userid != 41) &&($old_userid != 42) && ($old_userid != 9) && ($old_userid != 12) & ($old_userid != 13) && ($old_status eq "A") && ($old_userid < 43))
    	{
    		print "Moving to HotList from user - $old_userid\n";
    		$sql_upd = "insert into orig_list values($email_user_id,$old_userid)"; 
			$rows = $dbh->do($sql_upd);
   			if ($dbh->err() != 0)
    		{
        		$errmsg = $dbh->errstr();
        		print "Error inserting orig_list record: $sql_upd : $errmsg";
        		exit(0);
    		}
    		$sql_upd = "update email_user set user_id=42 where email_user_id=$email_user_id";
			$rows = $dbh->do($sql_upd);
   			if ($dbh->err() != 0)
    		{
        		$errmsg = $dbh->errstr();
        		print "Error Updating email_user record: $sql_upd : $errmsg";
        		exit(0);
    		}
#    		$sql_upd = "update list_member set list_id=61,subscribe_datetime=now() where email_user_id=$email_user_id";
    		$sql_upd = "update member_list set list_id=61,subscribe_datetime=now() where email_addr='$email_addr'";
			$rows = $dbh->do($sql_upd);
   			if ($dbh->err() != 0)
    		{
        		$errmsg = $dbh->errstr();
        		print "Error Updating member_list record: $sql_upd : $errmsg";
        		exit(0);
    		}
    	}
		#
		# Add record to market_demand_list - if no old address
#		if (($old_addr eq "") && ($old_status eq "A"))
#		{
#			$sql_upd = "insert into market_demand_list(email_user_id,date_added) values($email_user_id,now())";
#			$rows = $dbh->do($sql_upd);
#   			if ($dbh->err() != 0)
#    		{
#        		$errmsg = $dbh->errstr();
#        		print "Error Updating email_user record: $sql_upd : $errmsg";
#			}
#			print "Adding $email_user_id to market_demand_list\n";
#		}
		# Write record to email_user1
		if (($date_captured ne "") || ($member_source ne ""))
		{
			$sql_ins = qq{select email_user_id from email_user1 where email_user_id=$email_user_id};
			$sth1 = $dbh->prepare($sql_ins);
			$sth1->execute();
			if (($temp_id) = $sth1->fetchrow_array())
			{
				$sth1->finish();
			}
			else
			{
				$sth1->finish();
				$sql_ins = qq{insert into email_user1 (email_user_id, capture_date,member_source) values($email_user_id,'$date_captured','$member_source') };
				$rows = $dbh->do($sql_ins);
   				if ($dbh->err() != 0)
    			{
        			$errmsg = $dbh->errstr();
        			print "Error Inserting email_user1 after delete record: $sql_ins : $errmsg";
        			exit(0);
    			}
			}
		}
	}
	else
	{	
		#---- Format Insert statement (email_user_id is auto Increment)-------------

		$sql_ins = qq{insert into email_user (user_id, status, create_datetime,
			email_addr, email_type, gender, 
			first_name, middle_name, last_name, 
			birth_date, address, address2, 
			city, state, zip, 
			country, marital_status, occupation,
			job_status, income, education, phone ) 
		values ($user_id, 'A', curdate(),
			'$email_addr', '$email_type', '$gender', 
			'$first_name', '$middle_name', '$last_name', 
			'$birth_date', '$address', '$address2', 
			'$city', '$state', '$zip', 
			'$country', $marital_status, $occupation, 
			$job_status, $household_income, $education_level,'$phone' ) };

		# Insert email_user table

		$rows = $dbh->do($sql_ins);
   		if ($dbh->err() != 0)
    	{
        	$errmsg = $dbh->errstr();
        	print "Error Inserting email_user record: $sql_ins : $errmsg";
        	exit(0);
    	}

		# Insert record into user_state_info2
		#
#		if (($gender ne "") || ($state ne "") || ($zip ne ""))
#		{
#		$sql = "select email_user_id from member_list where email_addr='$email_addr'";
#		$sth = $dbh->prepare($sql);
#		$sth->execute();
#		($email_user_id) = $sth->fetchrow_array();
#		$sth->finish();
#		my $tmp_str = substr($zip,0,5); 
#		if ($tmp_str =~ /^\d+$/)
#		{
#			$tmp_zip = $tmp_str;
#		}
#		else
#		{
#			print "Invalid US zip - $zip\n";
#			$tmp_zip = 0;
#		}
#		$sql_ins = "insert into user_state_info2(email_user_id,email_addr,state,status,zip,gender) values($email_user_id,'$email_addr','$state','A',$tmp_zip,'$gender')";
#		$rows = $dbh->do($sql_ins);
#   		if ($dbh->err() != 0)
#    	{
#        	$errmsg = $dbh->errstr();
#        	print "Error Inserting user_state_info2 record: $sql_ins : $errmsg";
#    	}
#		}
#		else
#		{
#			print "No info for user_state_info2\n";
#		}

		# after insert, then find out what id was just entered
		$sql = "select email_user_id from email_user where email_addr='$email_addr'";
		$sth = $dbh->prepare($sql);
		$sth->execute();
		$email_user_id = $sth->fetchrow_array();
		$sth->finish();

		if ($user_id != 27)
		{
		$sql_ins = qq{insert into new_member(user_id, email_user_id,status, create_datetime,
			email_addr, email_type, gender, 
			first_name, middle_name, last_name, 
			birth_date, address, address2, 
			city, state, zip, 
			country, marital_status, occupation,
			job_status, income, education, phone ) 
		values ($user_id, $email_user_id, 'A', curdate(),
			'$email_addr', '$email_type', '$gender', 
			'$first_name', '$middle_name', '$last_name', 
			'$birth_date', '$address', '$address2', 
			'$city', '$state', '$zip', 
			'$country', $marital_status, $occupation, 
			$job_status, $household_income, $education_level,'$phone' ) };

		# Insert new_member table
		$rows = $dbh->do($sql_ins);
   		if ($dbh->err() != 0)
    	{
        	$errmsg = $dbh->errstr();
        	print "Error Inserting new_member record: $sql_ins : $errmsg";
        	exit(0);
    	}
		}

		# Write record to email_user1
		if (($date_captured ne "") || ($member_source ne ""))
		{
		$sql_ins = qq{insert into email_user1 (email_user_id, capture_date,member_source) values($email_user_id,'$date_captured','$member_source') };
		$rows = $dbh->do($sql_ins);
   		if ($dbh->err() != 0)
    	{
        	$errmsg = $dbh->errstr();
        	print "Error Inserting email_user1 record: $sql_ins : $errmsg";
        	exit(0);
    	}
		}
	}

} # end sub add_upd_email_user


#===============================================================================
# Sub: fmt_input_fields
#  - move array fields into vars for processing
#===============================================================================
sub fmt_input_fields
{
	my ($line) = @_ ;
	my ($i, $fpos);
	my $field;
	my @fields_array;

	# clear out all variables

	&set_fields_null();

	# Set ALL Delims to Pipe Delimiter and split into array 

	$line =~ s/\t/|/g ;                      # chg TABs   to Pipes
	$line =~ s/,//g ;                        # Remove commas 
	$line =~ s/\+//g ;                        # Remove + 
	$line =~ s/\(//g ;                        # Remove ( 
	$line =~ s/\)//g ;                        # Remove ) 
	$line =~ s/\*//g ;                        # Remove * 
	@fields_array = split '\|', $line ;      # split input line into separate fields in array

	$i = 0 ;
	$fpos = 1; 
	foreach $field (@fields_array)
	{
	    $field =~ s/'/''/g;
	    $field =~ s/\\/-/g;
		if ( $hsh_fl_pos_names{$fpos} eq "email_addr" )        { $email_addr = $field ; $email_addr =~ tr/A-Z/a-z/;}
		if ( $hsh_fl_pos_names{$fpos} eq "email_type" )        { $email_type = uc($field) ; }
		if ( $hsh_fl_pos_names{$fpos} eq "gender" )            { $gender = uc(substr($field,0,1)) ; }
		if ( $hsh_fl_pos_names{$fpos} eq "first_name" )        { $first_name = $field ; } 
		if ( $hsh_fl_pos_names{$fpos} eq "middle_name" )       { $middle_name = $field ; }
		if ( $hsh_fl_pos_names{$fpos} eq "last_name" )         { $last_name = $field ; }
		if ( $hsh_fl_pos_names{$fpos} eq "birth_date" )        { $birth_date = get_mysql_date($field) ; }
		if ( $hsh_fl_pos_names{$fpos} eq "address" )           { $address = $field ; }
		if ( $hsh_fl_pos_names{$fpos} eq "address2" )          { $address2 = $field ; }
		if ( $hsh_fl_pos_names{$fpos} eq "city" )              { $city = $field ; }
		if ( $hsh_fl_pos_names{$fpos} eq "state" )             { $state = uc($field) ; }
		if ( $hsh_fl_pos_names{$fpos} eq "zip" )               { $zip = $field ; }
		if ( $hsh_fl_pos_names{$fpos} eq "country" )           { $country = uc($field) ; }
		if ( $hsh_fl_pos_names{$fpos} eq "marital_status" )    { $marital_status = $field ; }
		if ( $hsh_fl_pos_names{$fpos} eq "occupation" )        { $occupation = $field ; }
		if ( $hsh_fl_pos_names{$fpos} eq "job_status" )        { $job_status = $field ; }
		if ( $hsh_fl_pos_names{$fpos} eq "household_income" )  { $household_income = $field ; }
		if ( $hsh_fl_pos_names{$fpos} eq "education_level" )   { $education_level = $field ; }
		if ( $hsh_fl_pos_names{$fpos} eq "date_captured" )     { $date_captured = get_mysql_datetime($field) ; }
		if ( $hsh_fl_pos_names{$fpos} eq "member_source" )   { $member_source = $field ; }
		if ( $hsh_fl_pos_names{$fpos} eq "phone" )   { $phone = $field ; }
		$i++;
		$fpos++;
	}
	
 	$email_addr  = trim_white_space($email_addr);
 	$email_type  = trim_white_space($email_type, 'G');
 	$gender      = trim_white_space($gender, 'G');
 	$first_name  = trim_white_space($first_name);
 	$middle_name = trim_white_space($middle_name);
 	$last_name   = trim_white_space($last_name);
 	$birth_date  = trim_white_space($birth_date, 'G');
 	$address     = trim_white_space($address);
 	$address2    = trim_white_space($address2);
 	$city        = trim_white_space($city);
 	$state       = trim_white_space($state, 'G');
 	$zip         = trim_white_space($zip);
 	$country     = trim_white_space($country, 'G');
 	$marital_status  = trim_white_space($marital_status, 'G');
 	$occupation  = trim_white_space($occupation, 'G');
 	$job_status  = trim_white_space($job_status, 'G');
 	$household_income = trim_white_space($household_income, 'G'); 
 	$education_level  = trim_white_space($education_level, 'G');
 	$date_captured = trim_white_space($date_captured, 'G');
 	$member_source = trim_white_space($member_source, 'G');
 	$phone = trim_white_space($phone, 'G');

} # end sub fmt_input_fields

#===============================================================================
# Sub: edit_rec
#===============================================================================
sub edit_rec
{
	my ($line) = @_ ;
	my ($cat_id, $field_position, $table_name, $db_field, $ftype, $flength);
	my ($invalid_rec, $invalid_fld, $no_match_found);
	my ($fld_len, @values_array, $value, $log_mesg);
	my ($str_usa_states) ;
	my ($str_canada_states) ;
	my ($addr);

	$invalid_rec = $FALSE;                         # assume record are valid

 	#----- Email Addr Edits -----------------------------------------
 	if ( $email_addr eq "" ) 
 	{
 		$invalid_rec = $TRUE;
 		print "Invalid!  The Email Address field is Null.  This field is MANDATORY\n";
 	}
	else
 	{

        # Email must have at least @ and .
        my $pos_at = index($email_addr, "\@");
        my $pos_dot = index($email_addr, "\.");
        if ($pos_at >= 0 && $pos_dot >= 0)
        {
		$addr = Email::Valid->address($email_addr);
        	if ($addr eq "")
        	{
 		    $invalid_rec = $TRUE;
  		    print "Invalid Email Address: $email_addr. \n"; 
		}
        	my $pos_quotes = index($email_addr, "'");
		if ($pos_quotes >= 0)
		{
 		    $invalid_rec = $TRUE;
  		    print "Invalid Email Address: $email_addr. \n"; 
		}
        }
        else
 		{
 			$invalid_rec = $TRUE;
  			print "Invalid Email Address: $email_addr.  The Email Address MUST contain at least 1 @ and . (eg period).\n";
 		}
 	}

	#----- Email Type Edits ----------------
	if ($email_type ne "" )
	{
		if ($email_type ne 'H'  &&  $email_type ne 'T'  &&  
			$email_type ne 'D'  &&  $email_type ne 'A' )
		{
 			$invalid_rec = $TRUE;
  			print "Invalid Email Type: $email_type.  Valid values are 'A', 'D', 'H', 'T' (Aol, Dont Know, Html, Text).\n";
 		}
 	}
 	else
 	{
 		$email_type = 'H' ;  # default to 'H' (eg HTML)
 	}
		
	#----- Gender Edits ----------------
	if ($gender ne "" )
	{
		if ( $gender ne 'M'  &&  $gender ne 'F' )
		{
 			$invalid_rec = $TRUE;
  			print "Invalid Gender: $gender Valid values are 'F', 'M' (Male, Female).\n";
 		}
 	}
		
	#----- Birth Date ----------------
#	if ($birth_date ne "" )
#	{
#		if ( $birth_date =~ m/\D/ )
#		{
# 			$invalid_rec = $TRUE;
#  			print "Invalid Birth Date: $birth_date\n";
# 		}
# 	}

 	#----- State/Province ----------------
	if ($state ne "" )
	{
		if ($country eq "USA" || $country eq "UNITED STATES" || $country eq "" )
		{
			$country = 'USA';
			$state = substr($state,0,2);
			$str_usa_states = qq{AE AK AL AP AR AS AZ CA CO CT DC DE FL GA GU HI IA ID IL IN KS KY LA MA MD ME MI MN MO MP MS MT NC ND NE NH NJ NM NV NY OH OK OR PA PR RI SC SD TN TX UT VA VI VT WA WI WV WY};
			$str_canada_states = qq{AB BC MB NB NF NS NT ON PE QC SK YT};
			if ( $str_usa_states =~ m/$state/ )
			{
				# Valid USA State found
			}
			elsif ( $str_canada_states =~ m/$state/ )
			{
				# Valid Canadian Provine
				$country = 'CANADA';
			}
			else
			{
 				$invalid_rec = $TRUE;
			    print "Invalid State/Province: $state Valid values are state codes for 
					USA and province codes for Canada.\n";
			}
		}
 	}
		
 	#----- Marital Status ----------------
	if ($marital_status ne "" )
	{
		if ( $marital_status < "1"  ||  $marital_status > "6"  || $marital_status =~ m/\D/  )
		{
 			$invalid_rec = $TRUE;
  			print "Invalid Marital Status: $marital_status  Valid values range 
				from 1 - 6.  See instructions for more details.\n";
 		}
 	}
		
 	#----- Occupation ----------------
	if ($occupation ne "" )
	{
		if ( $occupation < "1"  ||  $occupation > "19" || $occupation =~ m/\D/  )
		{
 			$invalid_rec = $TRUE;
  			print "Invalid Occupation: $occupation\n";
 		}
 	}
		
 	#----- Job Status ----------------
	if ($job_status ne "" )
	{
		if ( $job_status < "1"  ||  $job_status > "14"  || $job_status =~ m/\D/  )
		{
 			$invalid_rec = $TRUE;
  			print "Invalid Job Status: $job_status\n";
 		}
 	}
		
 	#----- Income ----------------
	if ($household_income ne "" )
	{
		if ( $household_income < "1"  ||  $household_income > "17"  || $household_income =~ m/\D/  )
		{
 			$invalid_rec = $TRUE;
  			print "Invalid Household Income: $household_income\n";
 		}
 	}
		
 	#----- Education ----------------
	if ($education_level ne "" )
	{
		if ( $education_level < "1"  ||  $education_level > "6"  || $education_level =~ m/\D/  )
		{
 			$invalid_rec = $TRUE;
  			print "Invalid Education Level: $education_level\n";
 		}
 	}
		
	return $invalid_rec ;

}  # end sub edit_rec


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

	if (length($date_str) > 12)
	{
		return "0000-00-00";
	}
	$_ = $date_str;
	if (/#/)
	{
		return "0000-00-00";
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
	else
	{
		$day = $str2;
		$month = $str1;
	}
	$temp_str = $year . "-" . $month . "-" . $day;
	print "Returning $temp_str\n";
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
#	print "Returning $temp_str\n";
	return $temp_str;
}
