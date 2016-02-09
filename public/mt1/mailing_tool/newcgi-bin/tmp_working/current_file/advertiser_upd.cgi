#!/usr/bin/perl
#===============================================================================
# Purpose: Update advertiser info - (eg table 'user' data).
# Name   : advertiser_upd.cgi (update_advertiser_info.cgi)
#
#--Change Control---------------------------------------------------------------
# 01/04/04  Jim Sobeck  Creation
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
    $email_addr = $query->param('email_addr');
    $physical_addr = $query->param('address');
    $internal_email_addr = $query->param('internal_email_addr');
    $pmode = $query->param('pmode');
	if ($pmode eq "U")
	{
		$cstatus = $query->param('cstatus');
	}
    $puserid = $query->param('puserid');

    #---- Set to Upper Case ----------
    $pmode = uc($pmode) ;
    open (LOG, "> /tmp/util.log");
    # make sure logfile is not buffered
    my $curhandle = select(LOG);
    $| = 1;
    select($curhandle);
    my $cdate = localtime();
    print LOG "starting at $cdate\n";
	print LOG "Name $name\n";
	print LOG "Mode $pmode\n";

&validate_modes();
	print LOG "Mode $pmode\n";

#------ connect to the util database ------------------
$util->db_connect();
$dbh = 0;
while (!$dbh)
{
print LOG "Connecting to db\n";
$dbh = $util->get_dbh;
}
print LOG "Connected to db - $dbh\n";
$dbh->{mysql_auto_reconnect}=1;
if ($pmode eq "A" )
{
	&insert_advertiser();
}
else
{
	print LOG "updating record\n";
	&update_advertiser();
}
	close LOG;

# go to next screen

$util->clean_up();
print "Cache-Control: no-cache\n";
print "Pragma: no-cache\n";
print "Expires: 0\n";
print "Location: /cgi-bin/advertiser_disp2.cgi?pmode=$pmode&puserid=$puserid&pmesg=$pmesg&rid=$rid\n\n";
exit(0);


#
#
#===============================================================================
# Sub: validate_modes - Valid modes are 'A' Add, and 'U' Update - else Stop
#===============================================================================
sub validate_modes
{
	my($go_home, $go_back, $mesg) ;
	#--------------------------------
	# get CGI Form fields
	#--------------------------------
#	my $puserid = $query->param('puserid');
#	$pmode = uc($pmode);
	if ( $pmode ne "A"  and  $pmode ne "U" ) 
	{	#---- Invalid MODE - Mode MUST = 'A' (add)  or  'U' (update)  ---------
		$go_back = qq{<br><a href="$ENV{'HTTP_REFERER'}">Back</a>\n };
 		$go_home = qq{&nbsp;&nbsp;<a href="mainmenu.cgi?userid=$user_id">Home</a>\n };
		$mesg = qq{<br><br><b>Invalid</b> Mode: <b>$pmode</b> - The Mode MUST equal 'A' or 'U'.} ;
		$mesg = $mesg . $go_back . $go_home ;
		util::logerror($mesg) ;
	}

} # end sub - validate_modes


#===============================================================================
# Sub: update_advertiser
#===============================================================================
sub update_advertiser
{
	my $rows;

	$sql = "update advertiser_info set advertiser_name='$name', physical_addr='$physical_addr',email_addr='$email_addr',internal_email_addr='$internal_email_addr',status='$cstatus' where advertiser_id = $puserid";
	$rows = $dbh->do($sql);
	print LOG "$sql\n";
#	if ($dbh->err() != 0)
#	{
#		my $errmsg = $dbh->errstr();
#		print LOG "Error: $errmsg\n";
#	    $pmesg = "Error - Updating user record for AdvertiserID: $puserid $errmsg";
#	}
#	else
#	{
	    $pmesg = "Successful UPDATE of Advertiser Info!" ;
#	}

}  # end sub - update_advertiser


#===============================================================================
# Sub: insert_advertiser
#===============================================================================
sub insert_advertiser
{
	my $rows;

	# add user to database

	$sql = "insert into advertiser_info(advertiser_name,email_addr,internal_email_addr,physical_addr,status) values('$name','$email_addr','$internal_email_addr','$physical_addr','A')"; 
	$sth = $dbh->do($sql);
	if ($dbh->err() != 0)
	{
	    $pmesg = "Error - Inserting advertiser record: $sql - $errmsg";
	}
	else
	{
	    $pmesg = "Successful INSERT of Advertiser Info!" ;
	}

	# get id of client just inserted 

	$sql = "select max(advertiser_id) from advertiser_info";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	($puserid) = $sth->fetchrow_array() ;
	$sth->finish();
	#
	$sql = "insert into advertiser_contact_info(advertiser_id) values($puserid)";
	$sth = $dbh->do($sql);
	$sql="insert into advertiser_from(advertiser_id,advertiser_from) values($puserid,'{{FOOTER_SUBDOMAIN}}')";
	$sth = $dbh->do($sql);

	$pmode = "U" ;

}  # end sub - insert_advertiser
