#!/usr/bin/perl
#===============================================================================
# Purpose: Update advertiser_from info - (eg table 'advertiser_from data).
# Name   : activate_from.cgi 
#
#--Change Control---------------------------------------------------------------
#===============================================================================

# include Perl Modules
use strict;
use CGI;
use util;

# get some objects to use later
my $util = util->new;
my $query = CGI->new;
my ($sth, $sql, $dbh, $errmsg ) ;
my ($pmesg, $old_email_addr) ;
my $images = $util->get_images_url;
my $original_flag ;
my $trigger_flag ;
my $approved_flag ;
my $creative_date;
my $inactive_date ;
my $unsub_image ;
my $default_subject ;
my $default_from ;
my $image_directory ;
my $thumbnail ;
my $html_code ;
my $puserid;
my $pmode;
my $fid;

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
    $fid = $query->param('fid');
    $puserid = $query->param('aid');

#------ connect to the util database ------------------
my ($dbhq,$dbhu)=$util->get_dbh();
&activate_from();
# go to next screen

$util->clean_up();
print "Cache-Control: no-cache\n";
print "Pragma: no-cache\n";
print "Expires: 0\n";
print "Location: /cgi-bin/advertiser_disp2.cgi?pmode=$pmode&puserid=$puserid&pmesg=$pmesg&rid=$rid\n\n";
exit(0);

sub activate_from
{
	my $rows;
	my $i;
	my $cname;
	my $aname;
	my $mflag;
	my $trigger_flag;
	my $reccnt;

	# add user to database
	$sql = "update advertiser_from set status='A',inactive_date=null where from_id=$fid and status != 'A'";
	$sth = $dbhu->do($sql);
	if ($dbhu->err() != 0)
	{
    	$pmesg = "Error - Activating from record: $sql - $errmsg";
	}
	else
	{
    	$pmesg = "Successful Activation of From!" ;
	}

	$pmode = "U" ;
}
# end sub - activate_from
