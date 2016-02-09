#!/usr/bin/perl

# *****************************************************************************************
# camp_edit_lists_save.cgi
#
# this page saves the list selection
#
# History
# Grady Nash	08/26/2001		Creation 
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
my $list_id;
my $iopt;
my $rows;
my $errmsg;
my $campaign_id;
my $id;
my $campaign_name;
my $k;
my $cname;
my $status;
my $aid;
my %checked = ( 'on' => 'Y', '' => 'N' );
my $list_cnt;

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

$campaign_id = $query->param('campaign_id');
my $max_emails = $query->param('max_emails');
my $clast60 = $query->param('clast60');
my $openflag = $query->param('copen');
my $aolflag = $query->param('aolflag');
if ($aolflag eq "")
{
	$aolflag="N";
}
my $yahooflag = $query->param('yahooflag');
if ($yahooflag eq "")
{
	$yahooflag = "N";
}
my $hotmailflag = $query->param('hotmailflag');
if ($hotmailflag eq "")
{
	$hotmailflag = "N";
}
my $otherflag = $query->param('otherflag');
if ($otherflag eq "")
{
	$otherflag = "N";
}

# campaign id is passed in, then update records 

# Update the information in the tables
# Get campaign_name and status
	
$sql = "select campaign_name,status,advertiser_id from campaign where campaign_id=$campaign_id";
$sth = $dbhq->prepare($sql);
$sth->execute();
($cname,$status,$aid) = $sth->fetchrow_array();
$sth->finish;
	
if ($status ne "C")
{
    $sql = "update campaign set max_emails=$max_emails,last60_flag='$clast60',aol_flag='$aolflag',open_flag='$openflag',yahoo_flag='$yahooflag',hotmail_flag='$hotmailflag',other_flag='$otherflag' where campaign_id=$campaign_id";
	$rows = $dbhu->do($sql);

	# Update lists of campaigns
	# read all lists for this user to check for the checkbox field checked
	# on the previous screen.  If they are checked, add to campaign_list table

	$sql = "delete from campaign_list where campaign_id=$campaign_id";
	$rows = $dbhu->do($sql);

	$sql = "select list_id from list where status='A'";
	$sth = $dbhq->prepare($sql);
	$sth->execute();
	while (($list_id) = $sth->fetchrow_array())
	{
    	$iopt = $query->param("list_$list_id");
    	if ($iopt)
    	{
			$sql = "insert into campaign_list (campaign_id, list_id) values ($campaign_id,$list_id)";
			$rows = $dbhu->do($sql);
			if ($dbhu->err() != 0)
			{
				$errmsg = $dbhu->errstr();
				util::logerror("Inserting campaign_list record for $id: $errmsg");
				exit(0);
			}
		}
	}
	$sth->finish();
    $sql = "select count(*) from campaign_list where campaign_id=$campaign_id";
    $sth = $dbhq->prepare($sql) ;
    $sth->execute();
    ($list_cnt) = $sth->fetchrow_array();
    $sth->finish();
    $sql = "update campaign set list_cnt=$list_cnt where campaign_id=$campaign_id";
    $rows = $dbhu->do($sql);
}
print "Location: show_campaign.cgi?campaign_id=$campaign_id&aid=$aid&mode=U\n\n";
$util->clean_up();
exit(0);
