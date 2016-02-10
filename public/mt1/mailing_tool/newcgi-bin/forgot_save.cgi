#!/usr/bin/perl
# *****************************************************************************************
# forgot_save.cgi
#
# this page displays the lists a member is subscribed to
#
# History
# Grady Nash, 8/17/01, Creation
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $sql;
my $dbh;
my $email_addr = $query->param('email_addr');
my $username;
my $password;
my $mail_mgr_addr;

# connect to the util database

###$util->db_connect();

my ($dbhq,$dbhu)=$util->get_dbh();
###$dbh = $util->get_dbh;

# lookup mail manager address

$sql = "select parmval from sysparm where parmkey = 'SYSTEM_MGR_ADDR'";
$sth = $dbhq->prepare($sql);
$sth->execute();
($mail_mgr_addr) = $sth->fetchrow_array();
$sth->finish();

# lookup this clients username/password

$sql = "select username,password from user where email_addr = '$email_addr' and status = 'A'";
$sth = $dbhq->prepare($sql);
$sth->execute();
($username,$password) = $sth->fetchrow_array();
$sth->finish();

if ($username eq "")
{
	util::message("The email address you entered does not exist");
}
else
{
	open (MAIL,"| /usr/lib/sendmail -t -R full -oi");
	print MAIL "Reply-To: $mail_mgr_addr\n"; 
	print MAIL "From: $mail_mgr_addr\n";
	print MAIL "To: $email_addr\n";
   	print MAIL "Subject: Your requested password\n";
	print MAIL "Content-Type: text/plain\n\n";
	print MAIL "Your username is $username\n";	
	print MAIL "Your password is $password\n\n";	
	close MAIL;

	util::message('Thanks! Your password has been emailed to you');
}

$util->clean_up();
exit(0);
