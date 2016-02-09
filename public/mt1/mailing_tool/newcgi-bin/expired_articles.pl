#!/usr/bin/perl
#===============================================================================
# Purpose: To remove expired articles 
# Name   : expired_articles.pl
#
#--Change Control---------------------------------------------------------------
# 11/02/06  Jim Sobeck  Creation
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
my $cname;
my $aname;
my $mflag;

$pmesg="";
srand();
my $rid=rand();
my $cstatus;
my $flag;
my $aid;

#------ connect to the util database ------------------
my $dbhq;
my $dbhu;
($dbhq,$dbhu)=$util->get_dbh();
$sql="select article_id,article_name from article where inactive_date is not null and inactive_date != '0000-00-00' and status='A'";
my $sthq = $dbhq->prepare($sql);
$sthq->execute();
while (($aid,$aname) = $sthq->fetchrow_array())
{
	&delete_article($aid);
	print "Article $aid - $pmesg\n";
}
$sthq->finish();
exit(0);

sub delete_article
{
	my ($aid) = @_;
	my $rows;
	my $i;

	$sql = "update article set status='D' where article_id=$aid";
	$sth = $dbhu->do($sql);
	if ($dbhu->err() != 0)
	{
	    $pmesg = "Error - Deleting article record: $sql - $errmsg";
	}
	else
	{
	    $pmesg = "Successful Delete of Article Info!" ;
	}
}
