#!/usr/bin/perl
#===============================================================================
# Purpose: Update creative info - (eg table 'creative' data).
# Name   : activate_creative.cgi 
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
my $creative_name ;
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
my $cid;

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
    $cid = $query->param('cid');
    $puserid = $query->param('aid');

#------ connect to the util database ------------------
my ($dbhq,$dbhu)=$util->get_dbh();
&activate_creative();
# go to next screen

$util->clean_up();
print "Cache-Control: no-cache\n";
print "Pragma: no-cache\n";
print "Expires: 0\n";
print "Location: /cgi-bin/advertiser_disp2.cgi?pmode=$pmode&puserid=$puserid&pmesg=$pmesg&rid=$rid\n\n";
exit(0);

sub activate_creative
{
	my $rows;
	my $i;
	my $cname;
	my $aname;
	my $mflag;
	my $trigger_flag;
	my $reccnt;

	# add user to database
	$sql = "update creative set status='A',inactive_date=null where creative_id=$cid and status != 'A'";
	$sth = $dbhu->do($sql);
	if ($dbhu->err() != 0)
	{
    	$pmesg = "Error - Activating creative record: $sql - $errmsg";
	}
	else
	{
    	$pmesg = "Successful Activation of Creative Info!" ;
	}

	$pmode = "U" ;
}
# end sub - activate_creative
