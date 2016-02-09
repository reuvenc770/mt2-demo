#!/usr/bin/perl

# *****************************************************************************************
# adv_auto_populate.cgi
#
# this page automatically populates the advertiser rotation 
#
# History
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
my $cid;
my $open_cnt;
my $click_cnt;
my ($c1,$c2,$c3,$c4,$c5,$s1,$s2,$s3,$s4,$s5,$f1,$f2,$f3,$f4,$f5);
my ($c6,$c7,$c8,$c9,$c10,$c11,$c12,$c13,$c14,$c15);
my ($s6,$s7,$s8,$s9,$s10,$s11,$s12,$s13,$s14,$s15);
my ($s16,$s17,$s18,$s19,$s20,$s21,$s22,$s23,$s24,$s25,$s26,$s27,$s28,$s29,$s30);
my ($f6,$f7,$f8,$f9,$f10);
my ($f11,$f12,$f13,$f14,$f15,$f16,$f17,$f18,$f19,$f20);
my $aname;
my $errmsg;
my $images = $util->get_images_url;
my $cfrom;
my @from_array;
my $catid1;
my $catid2;
my $advertiser_id1;
my $advertiser_id2;
my $sid;
my $aflag;
my $internal_aflag;
my $oflag;
my $copywriter;
my $fid;

# connect to the util database
my ($dbhq,$dbhu)=$util->get_dbh();
my $aid = $query->param('aid');
my $ctype = $query->param('ctype');
my $temp_str;
my $copen;
my $cclick;
my $cindex;
my $sth1;
#
$sql="delete from advertiser_setup where advertiser_id=$aid";
my $rows=$dbhu->do($sql);
my $isql="insert into advertiser_setup(advertiser_id,date_modified,";
my $ival="values($aid,now(),";
#
$sql="select creative_id from creative where advertiser_id=$aid and ((inactive_date = '0000-00-00') or (inactive_date > curdate()) or (inactive_date is null)) and status='A' and trigger_flag='N' ";
if ($ctype eq 'I')
{
	$sql=$sql."and internal_approved_flag='Y' ";
}
elsif ($ctype eq 'E')
{
	$sql=$sql."and approved_flag='Y'";
}
$sql=$sql." order by rand() limit 15";
$sth = $dbhq->prepare($sql) ;
$sth->execute();
my $i=1;
while (($cid) = $sth->fetchrow_array())
{
	$isql.="creative".$i."_id,";
	$ival.=$cid.",";;
	$i++;
}
$sth->finish();

$i=1;
$sql="select subject_id from advertiser_subject where advertiser_id=$aid and status='A' ";
if ($ctype eq 'I')
{
	$sql=$sql."and internal_approved_flag='Y' ";
}
elsif ($ctype eq 'E')
{
	$sql=$sql."and approved_flag='Y'";
}
$sql=$sql." order by rand() limit 30";
$sth = $dbhq->prepare($sql) ;
$sth->execute();
while (($sid) = $sth->fetchrow_array())
{
	$isql.="subject".$i.",";
	$ival.=$sid.",";;
	$i++;
}
$sth->finish();
$i=1;
$sql="select from_id from advertiser_from where advertiser_id=$aid and advertiser_from != '{{FOOTER_SUBDOMAIN}}' ";
if ($ctype eq 'I')
{
	$sql=$sql."and internal_approved_flag='Y' ";
}
elsif ($ctype eq 'E')
{
	$sql=$sql."and approved_flag='Y'";
}
$sql=$sql." order by rand() limit 20";
$sth = $dbhq->prepare($sql) ;
$sth->execute();
while (($fid) = $sth->fetchrow_array())
{
	$isql.="from".$i.",";
	$ival.=$fid.",";;
	$i++;
}
$sth->finish();

$sql="select class_id from email_class where status='Active'";
$sth=$dbhu->prepare($sql);
$sth->execute();
my $classid;
while (($classid)=$sth->fetchrow_array())
{
	my $tsql=$isql."class_id) ".$ival."$classid)";
	$rows=$dbhu->do($tsql);
}
$sth->finish();

print "Location: /cgi-bin/advertiser_setup_new.cgi?aid=$aid\n\n";
$util->clean_up();
exit(0);
