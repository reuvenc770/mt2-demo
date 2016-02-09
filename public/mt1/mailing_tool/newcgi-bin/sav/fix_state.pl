#!/usr/bin/perl

# include Perl Modules

use strict;
use pms;

# declare variables
my $pms = pms->new;
my $program;
my $cdate;
my $dbh;
my $file;
my $user_id;
my $sql;
my $sth;
my $sth1;
my $errmsg;
my $rows;
my $email_user_id;
my $email_addr;
my ($log_file, $file_name, $file_out);
my $list_id;
my $campstr;
my $uns_cnt;

$| = 1;    # don't buffer output for debugging log

# connect to the pms database 

$pms->db_connect();
$dbh = $pms->get_dbh;

process_file();
$pms->clean_up();   
exit(0) ;

# ******************************************************************
# end of main - begin subroutines
# ******************************************************************

sub process_file 
{
	my $therest;
	my $line;
	my $invalid_rec;
	my $input_file;
	my $camp_id;
	my $reccnt;
	my $list_id;

	$sql = "select SQL_BUFFER_RESULT email_user_id,email_addr from member_list where email_user_id >= 36451684 and email_user_id < 40000000 and status='A'";
	$sth1 = $dbh->prepare($sql);
	$sth1->execute();
	while (($email_user_id,$email_addr) = $sth1->fetchrow_array())
	{
		print "Email Addr = $email_addr\n";
		chk_user($email_user_id,$email_addr);
	}
	$sth1->finish();
}

# **********************************************************
# sub remove_user
# **********************************************************

sub chk_user()
{
	my ($email_user_id,$email_addr) = @_;
	my $tmp_id;
	my $cstate;
	my $cgender;
	my $czip;
	my $tmp_zip;
	my $tmp_str;

	$sql = "select email_user_id,state,zip,gender from email_user where email_addr = '$email_addr' and status='A'";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	if (($tmp_id,$cstate,$czip,$cgender) = $sth->fetchrow_array())
	{
		$sth->finish();
        my $tmp_str = substr($czip,0,5);
        if ($tmp_str =~ /^\d+$/)
        {
            if (length($tmp_str) == 0)
            {
                $tmp_zip = 0;
            }
            else
            {
                $tmp_zip = $tmp_str;
            }
        }
        else
        {
            print "Invalid US zip - $czip\n";
            $tmp_zip = 0;
        }
		$sql = "insert into user_state_info2 values($email_user_id,'$email_addr','$cstate','A',$tmp_zip,'$cgender')";
   		$rows = $dbh->do($sql);
		if ($dbh->err() != 0)
    	{
        	$errmsg = $dbh->errstr();
        	print "Error inserting user_state_info2: $sql : $errmsg";
#        	$pms->errmail($dbh,$program,$errmsg,$sql);
#        	exit(0);
    	}
	}
	else
	{
		$sth->finish();
    }
}

