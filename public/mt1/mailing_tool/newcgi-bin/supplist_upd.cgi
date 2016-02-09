#!/usr/bin/perl
#===============================================================================
# Purpose: Update Suppress List info - (eg table 'user' data).
# Name   : supplist_upd.cgi 
#
#--Change Control---------------------------------------------------------------
# 01/26/04  Jim Sobeck  Creation
#===============================================================================

# include Perl Modules
use strict;
use CGI;
use util;

# get some objects to use later
my $util = util->new;
my $query = CGI->new;
my ($sth, $sql, $dbh, $errmsg ) ;
my ($fname,$lname,$address,$address2,$city,$state,$zip,$phone,$email_addr);
my ($user_type, $max_names, $max_mailings, $status, $pmode, $puserid);
my ($password, $username, $old_username);
my ($pmesg, $old_email_addr) ;
my $company;
my $website_url;
my $company_phone;
my $images = $util->get_images_url;
my $admin_user;
my $account_type;
my $privacy_policy_url;
my $unsub_option;
my $name;
my $stype;
my $internal_email_addr;
my $physical_addr;

$pmesg="";
srand();
my $rid=rand();
my $cstatus;

#----------- check for login ------------------
my $user_id = util::check_security();
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}


    #---------------------------------------------------
    # Get the information about the user from the form
    #---------------------------------------------------
    $name = $query->param('name');
    $stype= $query->param('stype');

#------ connect to the util database ------------------
###$util->db_connect();

my ($dbhq,$dbhu)=$util->get_dbh();
###$dbh = $util->get_dbh;
&insert_list();
# go to next screen

$util->clean_up();
exit(0);




#===============================================================================
# Sub: insert_list
#===============================================================================
sub insert_list
{
	my $rows;

	# add user to database

	my $temp_name=$name;
	$temp_name=~ s/'/''/g;
	$sql = "insert into vendor_supp_list_info(list_name,suppressionType) values('$temp_name','$stype')"; 
	$sth = $dbhu->do($sql);
    print "Content-Type: text/html\n\n";
	if ($dbhu->err() != 0)
	{
print<< "end_of_html";
	<html><head><title>Error</title></head>
	<body>
	<h2>Error occurred trying to add list <b>$name</b></h2>
	</body>
	</html>
end_of_html
	}
	else
	{
print<< "end_of_html";
	<html><head><title>Success</title></head>
	<body>
	<h2>List <b>$name</b> successfully added.</h2>
	<p>
	<center><a href="mainmenu.cgi">Back to Main Menu</a></center>
	</body>
	</html>
end_of_html
	}

}  # end sub - insert_list
