#!/usr/bin/perl
#===============================================================================
# Purpose: Update category triggers to set best performing creative,from, and
#			subject 
# File   : update_cat_triggers.cgi
#
#--Change Control---------------------------------------------------------------
# 10/31/2007	Jim Sobeck	Created 
# 11/16/2007	Jim Sobeck	Modified to use calc_trigger flags
#===============================================================================

#-----------------------
# include Perl Modules
#-----------------------
use strict;
use CGI;
use lib "/var/www/html/newcgi-bin";
use util;

$|=1 ;   # set OUTPUT_AUTOFLUSH to true

my $util = util->new;
#my $query = CGI->new;
my $dbh;
my $rows;
my $sql;
my ($cid,$catid,$t1aid,$t2aid,$altaid,$t1,$t2,$altt);
my $new_cid;
my $new_subject;
my $new_from;
my $calc_val;
my $sth1;
my $sl;
my $cidstr;
my $calc_trigger1;
my $calc_trigger2;
my $calc_alt;

# ----- connect to the util database -------
my $dbhq;
my $dbhu;
($dbhq,$dbhu)=$util->get_dbh();

#
# Get all the advertisers for category_triggers
#
$sql="select client_id,category_id,trigger1_aid,trigger2_aid,alt_trigger_aid,trigger1,trigger2,alt_trigger,calc_trigger1,calc_trigger2,calc_alt from category_trigger where trigger_type='CLICK'"; 
my $sth=$dbhq->prepare($sql);
$sth->execute();
while (($cid,$catid,$t1aid,$t2aid,$altaid,$t1,$t2,$altt,$calc_trigger1,$calc_trigger2,$calc_alt)=$sth->fetchrow_array())
{
	$new_cid=0;
	if (($t1aid > 0) and ($calc_trigger1 eq "Y"))
	{
		print "Calculating Best Creative for $t1aid\n";
		$new_cid=get_best_creative($t1aid);
		print "Calculating Best Subject for $t1aid\n";
		$new_subject=get_best_subject($t1aid);
		print "Calculating Best From for $t1aid\n";
		$new_from=get_best_from($t1aid);
		print "1 - <$cid> <$catid> <$t1aid> <$new_cid> <$new_subject> <$new_from>\n";
		$sql="update category_trigger set trigger1=$new_cid,trigger1_subject_id=$new_subject,trigger1_from_id=$new_from where client_id=$cid and category_id=$catid and trigger_type='CLICK'";
		$rows=$dbhu->do($sql);
	}
	if (($t2aid > 0) and ($calc_trigger2 eq "Y"))
	{
		if (($t2aid == $t1aid) && ($new_cid > 0))
		{
		}
		else
		{
			$new_cid=get_best_creative($t2aid);
			$new_subject=get_best_subject($t2aid);
			$new_from=get_best_from($t2aid);
		}
		print "2 - <$cid> <$catid> <$t1aid> <$new_cid> <$new_subject> <$new_from>\n";
		$sql="update category_trigger set trigger2=$new_cid,trigger2_subject_id=$new_subject,trigger2_from_id=$new_from where client_id=$cid and category_id=$catid and trigger_type='CLICK'";
		$rows=$dbhu->do($sql);
	}
	if (($altaid > 0) and ($calc_alt eq "Y"))
	{
		if (($altaid == $t2aid) && ($new_cid > 0))
		{
		}
		else
		{
			$new_cid=get_best_creative($altaid);
			$new_subject=get_best_subject($altaid);
			$new_from=get_best_from($altaid);
		}
		print "Alt - <$cid> <$catid> <$t1aid> <$new_cid> <$new_subject> <$new_from>\n";
		$sql="update category_trigger set alt_trigger=$new_cid,alt_trigger_subject_id=$new_subject,alt_trigger_from_id=$new_from where client_id=$cid and category_id=$catid and trigger_type='CLICK'";
		print "<$sql>\n";
		$rows=$dbhu->do($sql);
	}
}
$sth->finish();
#
# Get all the advertisers for category_triggers
#
$sql="select client_id,category_id,trigger1_aid,trigger2_aid,alt_trigger_aid,trigger1,trigger2,alt_trigger,calc_trigger1,calc_trigger2,calc_alt from category_trigger where trigger_type='OPEN'"; 
my $sth=$dbhq->prepare($sql);
$sth->execute();
while (($cid,$catid,$t1aid,$t2aid,$altaid,$t1,$t2,$altt,$calc_trigger1,$calc_trigger2,$calc_alt)=$sth->fetchrow_array())
{
	$new_cid=0;
	if (($t1aid > 0) and ($calc_trigger1 eq "Y"))
	{
		print "Calculating Best Creative for $t1aid\n";
		$new_cid=get_best_creative($t1aid);
		print "Calculating Best Subject for $t1aid\n";
		$new_subject=get_best_subject($t1aid);
		print "Calculating Best From for $t1aid\n";
		$new_from=get_best_from($t1aid);
		print "1 - <$cid> <$catid> <$t1aid> <$new_cid> <$new_subject> <$new_from>\n";
		$sql="update category_trigger set trigger1=$new_cid,trigger1_subject_id=$new_subject,trigger1_from_id=$new_from where client_id=$cid and category_id=$catid and trigger_type='OPEN'";
		$rows=$dbhu->do($sql);
	}
	if (($t2aid > 0) and ($calc_trigger2 eq "Y"))
	{
		if (($t2aid == $t1aid) && ($new_cid > 0))
		{
		}
		else
		{
			$new_cid=get_best_creative($t2aid);
			$new_subject=get_best_subject($t2aid);
			$new_from=get_best_from($t2aid);
		}
		print "2 - <$cid> <$catid> <$t1aid> <$new_cid> <$new_subject> <$new_from>\n";
		$sql="update category_trigger set trigger2=$new_cid,trigger2_subject_id=$new_subject,trigger2_from_id=$new_from where client_id=$cid and category_id=$catid and trigger_type='OPEN'";
		$rows=$dbhu->do($sql);
	}
	if (($altaid > 0) and ($calc_alt eq "Y"))
	{
		if (($altaid == $t2aid) && ($new_cid > 0))
		{
		}
		else
		{
			$new_cid=get_best_creative($altaid);
			$new_subject=get_best_subject($altaid);
			$new_from=get_best_from($altaid);
		}
		print "Alt - <$cid> <$catid> <$t1aid> <$new_cid> <$new_subject> <$new_from>\n";
		$sql="update category_trigger set alt_trigger=$new_cid,alt_trigger_subject_id=$new_subject,alt_trigger_from_id=$new_from where client_id=$cid and category_id=$catid and trigger_type='OPEN'";
		print "<$sql>\n";
		$rows=$dbhu->do($sql);
	}
}
$sth->finish();
exit(0) ;

sub get_best_creative
{
	my ($aid)=@_;
	my $sql;
	my ($revenue,$clicks,$cid,$cname);

	$sql=qq^SELECT sum(amount) as revenue, count(*) as clicks, h.creative_id, c.creative_name, ai.advertiser_id FROM HitpathApiData h LEFT OUTER JOIN advertiser_info ai ON h.sid = ai.sid LEFT OUTER JOIN creative c ON h.creative_id = c.creative_id LEFT OUTER JOIN advertiser_subject ads ON h.subject_id = ads.subject_id LEFT OUTER JOIN advertiser_from af ON h.creative_id = af.from_id where h.effectiveDate >= DATE_SUB(current_date(), INTERVAL 15 DAY) and ai.advertiser_id = ?  and c.status='A' group by h.creative_id, c.creative_name, ai.advertiser_id order by revenue desc limit 1^;
	my $sth=$dbhu->prepare($sql);
	$sth->execute($aid);
	($revenue,$clicks,$cid,$cname,$aid)=$sth->fetchrow_array();
	$sth->finish();
	if ($cid eq "")
	{
		$sql="select creative1_id from advertiser_setup where advertiser_id=? and class_id=4";
		$sth=$dbhu->prepare($sql);
		$sth->execute($aid);
		($cid)=$sth->fetchrow_array();
		$sth->finish();
	}
	return $cid;
}
sub get_best_subject
{
	my ($aid)=@_;
	my $sql;
	my ($revenue,$clicks,$cid,$cname);

	$sql=qq^SELECT sum(amount) as revenue, count(*) as clicks, h.subject_id, ads.advertiser_subject, ai.advertiser_id FROM HitpathApiData h LEFT OUTER JOIN advertiser_info ai ON h.sid = ai.sid LEFT OUTER JOIN creative c ON h.creative_id = c.creative_id LEFT OUTER JOIN advertiser_subject ads ON h.subject_id = ads.subject_id LEFT OUTER JOIN advertiser_from af ON h.creative_id = af.from_id where h.effectiveDate >= DATE_SUB(current_date(), INTERVAL 15 DAY) and ai.advertiser_id = ?  and ads.status='A' group by h.subject_id, ads.advertiser_subject, ai.advertiser_id order by revenue desc limit 1^;
	my $sth=$dbhu->prepare($sql);
	$sth->execute($aid);
	($revenue,$clicks,$cid,$cname,$aid)=$sth->fetchrow_array();
	$sth->finish();
	if ($cid eq "")
	{
		$sql="select subject1 from advertiser_setup where advertiser_id=? and class_id=4";
		$sth=$dbhu->prepare($sql);
		$sth->execute($aid);
		($cid)=$sth->fetchrow_array();
		$sth->finish();
	}
	return $cid;
}
sub get_best_from
{
	my ($aid)=@_;
	my $sql;
	my ($revenue,$clicks,$cid,$cname);

	$sql=qq^SELECT sum(amount) as revenue, count(*) as clicks, h.from_id, af.advertiser_from, ai.advertiser_id FROM HitpathApiData h LEFT OUTER JOIN advertiser_info ai ON h.sid = ai.sid LEFT OUTER JOIN creative c ON h.creative_id = c.creative_id LEFT OUTER JOIN advertiser_subject ads ON h.subject_id = ads.subject_id LEFT OUTER JOIN advertiser_from af ON h.creative_id = af.from_id where h.effectiveDate >= DATE_SUB(current_date(), INTERVAL 15 DAY) and ai.advertiser_id = ?  and af.status='A' group by h.from_id, af.advertiser_from, ai.advertiser_id order by revenue desc limit 1^;
	my $sth=$dbhu->prepare($sql);
	$sth->execute($aid);
	($revenue,$clicks,$cid,$cname,$aid)=$sth->fetchrow_array();
	$sth->finish();
	if ($cid eq "")
	{
		$sql="select from1 from advertiser_setup where advertiser_id=? and class_id=4";
		$sth=$dbhu->prepare($sql);
		$sth->execute($aid);
		($cid)=$sth->fetchrow_array();
		$sth->finish();
	}
	return $cid;
}
