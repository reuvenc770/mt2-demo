#!/usr/bin/perl

# *****************************************************************************************
# client_signup_save.cgi
#
# this page saves the client signup form
#
# History
# Grady Nash, 8/22/01, Creation
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
my $rows;
my $errmsg;
my %checked = ( 'on' => 'Y', '' => 'N' );
my $images = $util->get_images_url;

# connect to the util database

###$util->db_connect();

my ($dbhq,$dbhu)=$util->get_dbh();
###$dbh = $util->get_dbh;

# check for login

my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}

# get fields from the form

my $list_id = $query->param('list_id');
my $show_first_name = $checked{$query->param('show_first_name')};
my $show_last_name = $checked{$query->param('show_last_name')};
my $show_address = $checked{$query->param('show_address')};
my $show_city = $checked{$query->param('show_city')};
my $show_state = $checked{$query->param('show_state')};
my $show_zip = $checked{$query->param('show_zip')};
my $show_country = $checked{$query->param('show_country')};
my $show_phone = $checked{$query->param('show_phone')};
my $show_gender = $checked{$query->param('show_gender')};
my $show_marital_status = $checked{$query->param('show_marital_status')};
my $show_occupation = $checked{$query->param('show_occupation')};
my $show_income = $checked{$query->param('show_income')};
my $show_education = $checked{$query->param('show_education')};
my $show_job_status = $checked{$query->param('show_job_status')};

# update the client signup form record

$sql = "update user_signup_form set list_id = $list_id,
    show_first_name = '$show_first_name', 
	show_last_name = '$show_last_name', 
	show_address = '$show_address', 
	show_city = '$show_city', 
	show_state = '$show_state',
    show_zip = '$show_zip', 
	show_country = '$show_country', 
	show_phone = '$show_phone',
	show_gender = '$show_gender', 
	show_marital_status = '$show_marital_status',
    show_occupation = '$show_occupation', 
	show_income = '$show_income', 
	show_education = '$show_education',
	show_job_status = '$show_job_status'
	where user_id = $user_id";
$rows = $dbhu->do($sql);
if ($dbhu->err() != 0)
{
	$errmsg = $dbhu->errstr();
	util::logerror("Updating user_signup_form record: $sql  : $errmsg");
	exit(0);
}

print "Location: signup_form.cgi\n\n";

# exit function

$util->clean_up();
exit(0);
