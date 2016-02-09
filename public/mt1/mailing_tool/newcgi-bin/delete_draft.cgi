#!/usr/bin/perl
#===============================================================================
# Purpose: Update creative draft info - (eg table 'draft_creative' data).
# Name   : delete_draft.cgi 
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

#------ connect to the util database ------------------
my ($dbhq,$dbhu)=$util->get_dbh();
&delete_creative();
# go to next screen

$util->clean_up();
print "Cache-Control: no-cache\n";
print "Pragma: no-cache\n";
print "Expires: 0\n";
print "Location: /draft.html\n\n";
exit(0);

sub delete_creative
{
	my $rows;
	my $i;
	my $cname;
	my $aname;
	my $mflag;
	my $trigger_flag;
	my $reccnt;

	# add user to database
	$reccnt=0;
	$sql = "update draft_creative set status='D' where creative_id=$cid";
	$sth = $dbhu->do($sql);
}
# end sub - delete_creative
