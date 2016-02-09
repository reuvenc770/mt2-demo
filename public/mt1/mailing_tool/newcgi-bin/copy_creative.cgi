#!/usr/bin/perl
#===============================================================================
# Name   : copy_creative.cgi 
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
my $sth9;

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
###$util->db_connect();

my ($dbhq,$dbhu)=$util->get_dbh();
&copy_creative();
# go to next screen

$util->clean_up();
print "Cache-Control: no-cache\n";
print "Pragma: no-cache\n";
print "Expires: 0\n";
print "Location: /cgi-bin/advertiser_disp2.cgi?pmode=$pmode&puserid=$puserid&pmesg=$pmesg&rid=$rid\n\n";
exit(0);

sub copy_creative
{
	my $rows;
	my $i;
	my $creative_name;
	# add user to database

	$sql="select creative_name from creative where creative_id=$cid"; 
	$sth9 = $dbhq->prepare($sql);
	$sth9->execute();
	($creative_name) = $sth9->fetchrow_array();
	$sth9->finish();

	$i=2;
	my $got_name=1;
	my $temp_creative;
	while ($got_name == 1)
	{
		$temp_creative = $creative_name . " " . $i;
		$sql="select creative_id from creative where creative_name='$temp_creative' and advertiser_id=$puserid"; 
		$sth9 = $dbhq->prepare($sql);
		$sth9->execute();
		if (($creative_name) = $sth9->fetchrow_array())
		{
			$i++;
		}
		else
		{
			$got_name=0;
		}		
		$sth9->finish();
	}
	$sql = "insert into creative(advertiser_id,status,creative_name,original_flag,trigger_flag,approved_flag,creative_date,inactive_date,unsub_image,default_subject,default_from,image_directory,thumbnail,html_code,date_approved,approved_by,content_id) select advertiser_id,status,'$temp_creative',original_flag,trigger_flag,approved_flag,creative_date,inactive_date,unsub_image,default_subject,default_from,image_directory,thumbnail,html_code,date_approved,approved_by,content_id from creative where creative_id=$cid";
	$sth = $dbhu->do($sql);
	if ($dbhu->err() != 0)
	{
	    $pmesg = "Error - Copying creative record: $sql - $errmsg";
	}
	else
	{
	    $pmesg = "Successful Copy of Creative Info!" ;
	}

	$pmode = "U" ;
}
# end sub - copy_creative
