#!/usr/bin/perl
# *****************************************************************************************
# unsubscribe_save.cgi
#
# this page saves the users "unsubscribe" requests
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
my $eid = $query->param('eid');
my $global = $query->param('global');
my $list_id;
my $mesg;
my $iopt;
my $rows;
my $email_addr;
my $images = $util->get_images_url;
my $cstatus;
my $sth1;
my $tid;

# connect to the util database

$util->db_connect();
$dbh = $util->get_dbh;

$sql = "select email_addr,status from member_list where email_user_id = $eid";
$sth = $dbh->prepare($sql);
$sth->execute();
($email_addr,$cstatus) = $sth->fetchrow_array();
$sth->finish;
unless ($email_addr) {
	$sql = "select email_addr,status from email_list where email_user_id = $eid";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	($email_addr,$cstatus) = $sth->fetchrow_array();
	$sth->finish;
}
	
if ($email_addr) {
	$mesg = "Email Address " . $email_addr. " Removed";
	if ($global eq "Y")
	{
    	$sql = "select distinct user_id from list where list_id in (select list_id from member_list where email_addr='$email_addr' and status='A')";
        $sth1 = $dbh->prepare($sql) ;
        $sth1->execute();
        while (($tid) = $sth1->fetchrow_array())
        {
        	$sql = "insert into manual_removal(email_addr,removal_date,client_id) values('$email_addr',now(),$tid)";
            $rows = $dbh->do($sql) ;
        }
        $sth1->finish();	
	}
	if ($cstatus eq "A")
	{
		$sql = "update member_list set status = 'U', unsubscribe_datetime = now() where email_user_id = $eid";
    	$rows = $dbh->do($sql);
		$sql = "update email_list set status = 'U', unsubscribe_date=now(),unsubscribe_time=now() where email_user_id = $eid";
    	$rows = $dbh->do($sql);
    	$sql = "insert into unsub_log(email_addr,unsub_date) values('$email_addr',curdate())";
    	$rows = $dbh->do($sql);
	}
#
	if (($global eq "Y") or ($global eq "D"))
	{
		$sql = "update member_list set status='U',unsubscribe_datetime=now() where email_addr='$email_addr' and status='A'";
    	$rows = $dbh->do($sql);
		$sql = "update email_list set status='U',unsubscribe_date=now(),unsubscribe_time=now() where email_addr='$email_addr' and status='A'";
    	$rows = $dbh->do($sql);
        $sql = "insert into suppress_list values('$email_addr')";
        $rows = $dbh->do($sql) ;
		$mesg = "Email Address " . $email_addr. " added to Suppression List";
	}
    if ($global eq "D")
    {
my $caddr;
my $cdomain;
            ($caddr,$cdomain) = split("@",$email_addr);
## deprecating suppress_domain in favor of email_domains - jp Thu Jan  5 10:27:14 EST 2006
## insert b4 update so we don't override a previous dateSupp setting if suppressed already = 1
			$sql=qq^INSERT IGNORE INTO email_domains (domain_id, domain_class, domain_name, suppressed, dateSupp) VALUES (NULL, '4', '$cdomain', 1, NOW())^;
			$rows=$dbh->do($sql);
			if ($rows == 0) {
				$sql=qq^UPDATE email_domains SET suppressed='1', dateSupp=NOW() WHERE domain_name='$cdomain' AND suppressed=0^;
				$rows=$dbh->do($sql);
			}
##            $sql = "insert into suppress_domain values('$cdomain',now())";
##            $rows = $dbh->do($sql) ;
			$mesg = "Domain " . $cdomain . " added to Domain Suppression List";
        open (MAIL,"| /usr/sbin/sendmail -t");
        my $from_addr = "Domain added to Global Suppression <info\@spirevision.com>";
        print MAIL "From: $from_addr\n";
        print MAIL "To: setup\@spirevision.com\n";
        print MAIL "Subject: Domain Added to Global Suppression\n";
        my $date_str = $util->date(6,6);
        print MAIL "Date: $date_str\n";
        print MAIL "X-Priority: 1\n";
        print MAIL "X-MSMail-Priority: High\n";
        print MAIL "$cdomain added\n";
        close MAIL;
    }
}
$sth->finish();

# print out the html page

print "Location: /cgi-bin/show_info_id.cgi?name=$eid&mesg=$mesg\n\n";
