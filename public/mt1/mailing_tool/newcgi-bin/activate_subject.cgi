#!/usr/bin/perl
#===============================================================================
# Purpose: Update advertiser_subject info - (eg table 'advertiser_subject data).
# Name   : activate_subject.cgi 
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
my $subject_name;;
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
my $sid;

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
    $sid = $query->param('sid');
    $puserid = $query->param('aid');

#------ connect to the util database ------------------
my ($dbhq,$dbhu)=$util->get_dbh();
&activate_subject();
# go to next screen

$util->clean_up();
print "Cache-Control: no-cache\n";
print "Pragma: no-cache\n";
print "Expires: 0\n";
print "Location: /cgi-bin/advertiser_disp2.cgi?pmode=$pmode&puserid=$puserid&pmesg=$pmesg&rid=$rid\n\n";
exit(0);

sub activate_subject
{
	my $rows;
	my $i;
	my $cname;
	my $aname;
	my $mflag;
	my $trigger_flag;
	my $reccnt;

	# add user to database
	$sql = "update advertiser_subject set status='A',inactive_date=null where subject_id=$sid and status != 'A'";
	$sth = $dbhu->do($sql);
	if ($dbhu->err() != 0)
	{
    	$pmesg = "Error - Activating subject record: $sql - $errmsg";
	}
	else
	{
    	$pmesg = "Successful Activation of Subject!" ;
	}

	$pmode = "U" ;
}
# end sub - activate_subject
