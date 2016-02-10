#!/usr/bin/perl
#===============================================================================
# Purpose: Update client info - (eg table 'user' data).
# Name   : client_upd.cgi (update_client_info.cgi)
#
#--Change Control---------------------------------------------------------------
# 08/03/01  Jim Sobeck  Creation
# 08/15/01  Mike Baker  Change to allow 'Admin' User to Update other fields.
#===============================================================================

# include Perl Modules
use strict;
use CGI;
use pma;

# get some objects to use later
my $pms = pma->new;
my $query = CGI->new;
my ($sth, $sql, $dbh, $errmsg ) ;
my ($fname,$lname,$address,$address2,$city,$state,$zip,$phone,$email_addr);
my ($user_type, $max_names, $max_mailings, $status, $pmode, $puserid);
my ($password, $username, $old_username);
my ($pmesg, $old_email_addr) ;
my $company;
my $website_url;
my $company_phone;
my $images = $pms->get_images_url;
my $admin_user;
my $account_type;
my $privacy_policy_url;
my $unsub_option;

#------ connect to the pms database ------------------
$pms->db_connect();
$dbh = $pms->get_dbh;

#----------- check for login ------------------
my $user_id = pma::check_security();
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
	$pms->clean_up();
    exit(0);
}

my $client_name= $query->param('client_name');
my $ftp_dir = $query->param('ftp_dir');
$sql="insert into tranzact_file_layout(client_name,ftp_dir) values('$client_name','$ftp_dir')";
my $rows=$dbh->do($sql);
print "Location: mainmenu.cgi\n\n";
$pms->clean_up();
exit(0);

