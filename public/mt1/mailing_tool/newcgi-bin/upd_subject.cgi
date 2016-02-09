#!/usr/bin/perl

# *****************************************************************************************
# upd_contact.cgi
#
# this page updates information in the advertiser_subject table
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
my $catcnt;
my $dbh;
my $sid;
my $errmsg;
my $images = $util->get_images_url;
my $csubject;
my $pmesg="";
my $tpmesg="";
my $idate;
my @subject_array;

# connect to the util database
my ($dbhq,$dbhu)=$util->get_dbh();

# check for login
my $user_id = util::check_security();
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
	$util->clean_up();
    exit(0);
}
#
# Remove old subject information
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
	$copywriter= "N";
}
if ($copywriter eq "N")
{
	$copywriter_name= "";
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
my $csubject = $query->param('csubject');
	$csubject=~ s//'/g;
	$csubject=~ s/\x60/\x27/g;
	$csubject=~ s/'/''/g;
my $first = index($csubject, "{");
my $end;
my $i;
my $tstr;
my $notfound;
while ($first >= 0)
{
	$end=index($csubject,"}}",$first+1);
	if ($end >= 0)
	{
		$tstr=substr($csubject,$first,$end-$first+2);
		if (!util::CheckTokens($tstr))
		{
			$pmesg="One or more bad Variables specified - please fix";
		}
		$first = index($csubject,"{",$end+1);
	}
	else
	{
		$tstr=substr($csubject,$first);
		$pmesg="One or more bad Variables specified - please fix";
		$first=index($csubject,"{",$first+1);
	}
}
if ($pmesg eq "")
{
	if ($catcnt == 0)
	{
	if (util::isValidChars($csubject))
	{
	}
	else
	{
		$pmesg="Subject contains invalid characters - please fix";
	}
	}
}
if ($pmesg eq "")
{
	my $temp_str=$csubject;
	$temp_str=~s/{{NAME}}//g;
	$temp_str=~tr/A-Z/a-z/;
	$_=$temp_str;
	if (/ name /)
	{
        $tpmesg="The keyword 'name' was found in subject - please fix";
        open (MAIL,"| /usr/sbin/sendmail -t");
        my $from_addr = "Subject with name in it added<info\@zetainteractive.com>";
        print MAIL "From: $from_addr\n";
        print MAIL "To: setup\@zetainteractive.com\n";
        print MAIL "Subject: Subject added with name in it\n";
        my $date_str = $util->date(6,6);
        print MAIL "Date: $date_str\n";
        print MAIL "X-Priority: 1\n";
        print MAIL "X-MSMail-Priority: High\n";
        print MAIL "$csubject added for advertiser id - $aid\n";
        close MAIL;
	}
}
#
# Insert record into advertiser_subject
#
if ($pmesg eq "")
{
if ($aflag eq "Y")
{
	$sql = "update advertiser_subject set approved_flag='$aflag',approved_by='SpireVision',date_approved=now(),inactive_date='$idate' where subject_id=$sid and advertiser_id=$aid and approved_by is null";
	$sth = $dbhu->do($sql);
}
$sql = "update advertiser_subject set advertiser_subject='$csubject',approved_flag='$aflag',original_flag='$oflag',inactive_date='$idate',copywriter='$copywriter',copywriter_name='$copywriter_name' where subject_id=$sid and advertiser_id=$aid";
$sth = $dbhu->do($sql);
if ($dbhu->err() != 0)
{
	my $errmsg = $dbhu->errstr();
    util::logerror("Updating advertiser subject info record for $user_id: $errmsg");
}
}
if ($idate ne "" && $idate ne '00/00/00')
{
	my $dcnt;
	my $cstatus;
    $sql="select status,datediff(curdate(),'$idate') from advertiser_subject where subject_id=?";
    my $sth1 = $dbhq->prepare($sql);
    $sth1->execute($sid);
    ($cstatus,$dcnt) = $sth1->fetchrow_array();
    $sth1->finish;
    if (($cstatus eq "A") and ($dcnt > 0))
    {
    	$sql="update advertiser_subject set status='I' where subject_id=$sid";
        $sth = $dbhu->do($sql);
		$sql="delete from UniqueAdvertiserSubject where subject_id=$sid";
		$sth= $dbhu->do($sql);

		$sql="select uc.unq_id from unique_campaign uc, UniqueSubject where uc.unq_id=UniqueSubject.unq_id and UniqueSubject.subject_id=$sid and uc.send_date >= curdate() and uc.status in ('START','PRE-PULLING')";
		$sth = $dbhq->prepare($sql);
		$sth->execute();
		my $uid;
		while (($uid)=$sth->fetchrow_array())
		{
			$sql="delete from UniqueSubject where unq_id=$uid and subject_id=$sid";
			my $rows= $dbhu->do($sql);
		}
		$sth->finish();
    }
}
if ($pmesg eq "")
{
	$pmesg=$tpmesg;
}
#
# Display the confirmation page
#
print "Location: /cgi-bin/advertiser_disp2.cgi?puserid=$aid&pmesg=$pmesg\n\n";
$util->clean_up();
exit(0);
