#!/usr/bin/perl

# *****************************************************************************************
# upd_from.cgi
#
# this page updates information in the advertiser_from table
#
# History
# Jim Sobeck, 12/16/04, Creation
# Jim Sobeck, 02/02/05, Modifed to handle unique id
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
my $sid;
my $idate;
my $errmsg;
my $images = $util->get_images_url;
my $cfrom;
my $catcnt;
my @from_array;
my $pmesg="";

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
#
# Remove old from information
#
my $aid = $query->param('aid');
my $sid = $query->param('sid');
my $aflag = $query->param('aflag');
if ($aflag eq "")
{
	$aflag = "N";
}
my $oflag = $query->param('oflag');
if ($oflag eq "")
{
	$oflag = "N";
}
my $copywriter_name = $query->param('copywriter_name');
my $copywriter = $query->param('copywriter');
if ($copywriter eq "")
{
	$copywriter = "N";
}
if ($copywriter eq "N")
{
	$copywriter_name = "";
}
$idate = $query->param('idate');
if ($idate ne "" && $idate ne '00/00/00')
{
    my $temp_str;
    $temp_str = $idate;
    $idate= "20" . substr($temp_str,6,2) . "-" . substr($temp_str,0,2) . "-" . substr($temp_str,3,2);
}
	$sql="select count(*) from advertiser_info ai, category_info ci where advertiser_id=? and ai.category_id=ci.category_id and ci.category_name='FR'";
	my $sth1=$dbhq->prepare($sql);
	$sth1->execute($aid);
	($catcnt)=$sth1->fetchrow_array();
	$sth1->finish();
#
# Get the information about the user from the form 
#
my $cfrom = $query->param('cfrom');
	$cfrom=~ s//'/g;
	$cfrom=~ s/\x60/\x27/g;
	$cfrom=~ s/'/''/g;
#
if ($pmesg eq "")
{
	if ($catcnt == 0)
	{
	if (util::isValidFromChars($cfrom))
	{
	}
	else
	{
		$pmesg="From contains invalid characters - please fix";
	}
	}
}
if ($pmesg eq "")
{
# Insert record into advertiser_from
#
$sql = "update advertiser_from set advertiser_from='$cfrom',approved_flag='$aflag',original_flag='$oflag',inactive_date='$idate',copywriter='$copywriter',copywriter_name='$copywriter_name' where from_id=$sid and advertiser_id=$aid";
$sth = $dbhu->do($sql);
if ($dbhu->err() != 0)
{
	my $errmsg = $dbhu->errstr();
    util::logerror("Updating advertiser from info record for $user_id: $errmsg");
}
if ($aflag eq "Y")
{
$sql = "update advertiser_from set date_approved=now(),approved_by='Web' where from_id=$sid and advertiser_id=$aid and date_approved is null";
$sth = $dbhu->do($sql);
}
}
if ($idate ne "" && $idate ne '00/00/00')
{
	my $dcnt;
	my $cstatus;
    $sql="select status,datediff(curdate(),'$idate') from advertiser_from where from_id=?";
    $sth1 = $dbhq->prepare($sql);
    $sth1->execute($sid);
    ($cstatus,$dcnt) = $sth1->fetchrow_array();
    $sth1->finish;
    if (($cstatus eq "A") and ($dcnt > 0))
    {
    	$sql="update advertiser_from set status='I' where from_id=$sid";
        $sth = $dbhu->do($sql);
$sql="select from_id from advertiser_from where advertiser_id=$aid and status='D'";
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($sid)=$sth->fetchrow_array())
{
	$sql="delete from UniqueAdvertiserSubject where from_id=$sid";
	my $rows= $dbhu->do($sql);

	$sql="select uc.unq_id from unique_campaign uc, UniqueFrom where uc.unq_id=UniqueFrom.unq_id and UniqueFrom.from_id=$sid and uc.send_date >= curdate() and uc.status in ('START','PRE-PULLING')";
	my $sth1 = $dbhu->prepare($sql);
	$sth1->execute();
	my $uid;
	while (($uid)=$sth1->fetchrow_array())
	{
		$sql="delete from UniqueFrom where unq_id=$uid and from_id=$sid";
		my $rows= $dbhu->do($sql);
	}
	$sth1->finish();
}
$sth->finish();
    }
}
#
# Display the confirmation page
#
print "Location: /cgi-bin/advertiser_disp2.cgi?puserid=$aid&pmesg=$pmesg\n\n";
$util->clean_up();
exit(0);
