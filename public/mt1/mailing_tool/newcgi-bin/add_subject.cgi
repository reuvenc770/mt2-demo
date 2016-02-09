#!/usr/bin/perl

# *****************************************************************************************
# upd_contact.cgi
#
# this page updates information in the advertiser_subject table
#
# History
# Jim Sobeck, 12/16/04, Creation
# Jim Sobeck, 02/02/05, Modifed to handle unique id
# Jim Sobeck, 07/17/07, Modified to allow deletion of multiple subjects
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
my $i;
my $catcnt;
my $class;
my $errmsg;
my $images = $util->get_images_url;
my $csubject;
my $idate;
my $pmesg="";
my $tpmesg="";
my @subject_array;

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
# Remove old subject information
#
my $aid = $query->param('aid');
	$sql="select count(*) from advertiser_info ai, category_info ci where advertiser_id=? and ai.category_id=ci.category_id and ci.category_name='FR'";
	my $sth1=$dbhq->prepare($sql);
	$sth1->execute($aid);
	($catcnt)=$sth1->fetchrow_array();
	$sth1->finish();

my @delsub= $query->param('delsubject');
my $iaction = $query->param('iaction');
$idate = $query->param('idate');
if ($idate ne "" && $idate ne '00/00/00')
{
    my $temp_str;
    $temp_str = $idate;
    $idate= "20" . substr($temp_str,6,2) . "-" . substr($temp_str,0,2) . "-" . substr($temp_str,3,2);
}

foreach my $sid (@delsub)
{
	if ($iaction eq "Delete")
	{
		$sql="delete from advertiser_subject where subject_id=$sid";
    	my $rows=$dbhu->do($sql);
		$i=1;
		while ($i <= 30)
		{
			$sql = "update advertiser_setup set subject${i}=0 where subject${i}=$sid and advertiser_id=$aid";
			$sth = $dbhu->do($sql);
			$i++;
		}
	}
	elsif ($iaction eq "Activate")
	{
		$sql="update advertiser_subject set status='A' where subject_id=$sid";
    	my $rows=$dbhu->do($sql);
	}
	elsif ($iaction eq "Inactivate")
	{
		$sql="update advertiser_subject set inactive_date='$idate' where subject_id=$sid";
    	my $rows=$dbhu->do($sql);
	}
	elsif ($iaction eq "Approve")
	{
		$sql="update advertiser_subject set approved_flag='Y',approved_by='SpireVision',date_approved=now() where subject_id=$sid";
    	my $rows=$dbhu->do($sql);
	}
}
#
# Move up subject stuff
#
my @cids;
my @tcids;
my $class_id;
my $sth9;
$sql="select class_id from email_class where status='Active' order by class_id";
$sth9 = $dbhq->prepare($sql);
$sth9->execute();
while (($class_id) = $sth9->fetchrow_array())
{
	$sql = "select subject1,subject2,subject3,subject4,subject5,subject6,subject7,subject8,subject9,subject10,subject11,subject12,subject13,subject14,subject15,subject16,subject17,subject18,subject19,subject20,subject21,subject22,subject23,subject24,subject25,subject26,subject27,subject28,subject29,subject30 from advertiser_setup where advertiser_id=$aid and class_id=$class_id";
	$sth = $dbhq->prepare($sql);
	$sth->execute();
	(@cids) = $sth->fetchrow_array();
	$sth->finish();
	$i=0;
	my $j=1;
	while ($j <= $#cids)
	{
		$tcids[$j] = 0;
		$j++;
	}
	$j=1;
	while ($i < $#cids)
	{
		if ($cids[$i] != 0)
		{
			$tcids[$j] = $cids[$i];
			$j++;
		}
		$i++;
	}
	$i=1;
	while ($i <= $#cids)
	{
		$sql = "update advertiser_setup set subject${i}=$tcids[$i] where advertiser_id=$aid and class_id=$class_id";
		$sth = $dbhu->do($sql);
		$i++;
	}
	if ($tcids[1] == 0)
	{
		#$pmesg="No subjects setup for advertiser rotation now!";
	}
}
$sth9->finish();

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
my $copywriter_name= $query->param('copywriter_name');
my $copywriter= $query->param('copywriter');
if ($copywriter eq "")
{
	$copywriter= "N";
}
if ($copywriter eq "N")
{
	$copywriter_name= "";
}
#
# Get the information about the user from the form 
#
my $subject_list = $query->param('csubject');
$subject_list =~ s/[\n\r\f\t]/\|/g ;
$subject_list =~ s/\|{2,999}/\|/g ;
@subject_array = split '\|', $subject_list;
foreach $csubject (@subject_array)
{
	$csubject=~ s//'/g;
	$csubject=~ s/\x60/\x27/g;
	$csubject=~ s/'/''/g;
    $csubject =~ s/\//g;
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
	if ($catcnt== 0)
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
if ($pmesg eq "")
{
	#
	# Insert record into advertiser_subject
	#
	if ($aflag eq "Y")
	{
	$sql = "insert into advertiser_subject(advertiser_id,advertiser_subject,approved_flag,original_flag,status,approved_by,date_approved,inactive_date,copywriter,copywriter_name) values($aid,'$csubject','$aflag','$oflag','A','SpireVision',now(),'$idate','$copywriter','$copywriter_name')";
	}
	else
	{
	$sql = "insert into advertiser_subject(advertiser_id,advertiser_subject,approved_flag,original_flag,status,inactive_date,copywriter,copywriter_name) values($aid,'$csubject','$aflag','$oflag','A','$idate','$copywriter','$copywriter_name')";
	}
	$sth = $dbhu->do($sql);
	if ($dbhu->err() != 0)
	{
		my $errmsg = $dbhu->errstr();
	    util::logerror("Updating advertiser subject info record for $user_id: $errmsg");
	}
    $sql="select LAST_INSERT_ID()";
    $sth=$dbhu->prepare($sql);
    $sth->execute();
    my $sid;
    ($sid)=$sth->fetchrow_array();
    $sth->finish();
	if ($util->getConfigData("AUTO_APPROVE"))
	{
		$sql="update advertiser_subject set internal_approved_flag='Y',internal_date_approved=curdate(),interval_approved_by='AUTO' where subject_id=$sid";
		$sth = $dbhu->do($sql);
	}
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
