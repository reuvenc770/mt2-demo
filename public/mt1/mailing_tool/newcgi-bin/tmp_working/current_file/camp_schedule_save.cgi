#!/usr/bin/perl

# *****************************************************************************************
# camp_schedule_save.cgi
#
# this page saves the schedule info
#
# History
# Jim Sobeck	08/08/2001		Creation 
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
my $lists;
my @list_ids;
my $email_addr;
my $rows;
my $errmsg;
my $campaign_id;
my $old_campaign_id;
my $id;
my $campaign_name;
my $subject;
my $image_url;
my $title;
my $subtitle;
my $date_str;
my $greeting;
my $introduction;
my $k;
my $status;
my %checked = ( 'on' => 'Y', '' => 'N' );
my $day_cnt;

# connect to the util database

$util->db_connect();
$dbh = $util->get_dbh;

# check for login

my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}

my $schedule_flag = $query->param('schedule');
$campaign_id = $query->param('campaign_id');
my $max_emails = $query->param('max_emails');
my $clast60 = $query->param('clast60');
my $openflag = $query->param('copen');
my $aolflag = $query->param('aolflag');
my $hotmailflag = $query->param('hotmailflag');
my $yahooflag = $query->param('yahooflag');
my $otherflag = $query->param('otherflag');
my $opener_catid = $query->param('open_catid');
if ($aolflag eq "")
{
	$aolflag="N";
}
if ($hotmailflag eq "")
{
	$hotmailflag="N";
}
if ($yahooflag eq "")
{
	$yahooflag="N";
}
if ($opener_catid eq "")
{
	$opener_catid = 0;
}
if ($otherflag eq "")
{
	$otherflag="N";
}

# Update the information in the tables
# Get status
	
$sql = "select status from campaign where campaign_id=$campaign_id";
$sth = $dbh->prepare($sql);
$sth->execute();
($status) = $sth->fetchrow_array();
$sth->finish;
	
if ($status ne "C")
{
	if ($schedule_flag eq "D")
	{
		$sql = "update campaign set status='D',scheduled_date=NULL,max_emails=$max_emails,last60_flag='$clast60',aol_flag='$aolflag',open_flag='N',hotmail_flag='$hotmailflag',yahoo_flag='$yahooflag',other_flag='$otherflag',open_category_id=$opener_catid where campaign_id=$campaign_id";
	}
	else
	{
		my $sdate=$query->param('sdate');
		my @date_parts = split(/\//,$sdate);
		my $date_str = $date_parts[2] . "-" . $date_parts[0] . "-" . $date_parts[1];
		$sql = "select to_days('$date_str')-to_days(curdate())";
		$sth = $dbh->prepare($sql);
		$sth->execute();
		($day_cnt) = $sth->fetchrow_array();
		$sth->finish;
		if ($day_cnt >= 0)
		{
			$sql = "update campaign set status='S', scheduled_date='$date_str', max_emails=$max_emails,last60_flag='$clast60',aol_flag='$aolflag',open_flag='N',hotmail_flag='$hotmailflag',yahoo_flag='$yahooflag',other_flag='$otherflag' where campaign_id=$campaign_id";
		}
	}
	$rows = $dbh->do($sql);
}

print "Location: mainmenu.cgi\n\n";
$util->clean_up();
exit(0);
