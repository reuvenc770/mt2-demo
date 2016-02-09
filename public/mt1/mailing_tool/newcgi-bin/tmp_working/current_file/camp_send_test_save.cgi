#!/usr/bin/perl

# *****************************************************************************************
# camp_send_test_save.cgi
#
# writes a record to test_emails
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
my $rows;
my $errmsg;
my $user_id;
my $campaign_id = $query->param('campaign_id');
my $email_addr = $query->param('email_addr');
my $hostname= $query->param('hostname');
my $ip_addr= $query->param('ip_addr');
my $profile_id= $query->param('profile_id');
my ($aolflag,$hotmailflag,$yahooflag,$otherflag);
my $max_emails;

# connect to the util database

$util->db_connect();
$dbh = $util->get_dbh;

# check for login

$user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}

if ($email_addr ne "")
{
	$sql = "insert into test_emails(campaign_id,email_addr,hostname,ip_addr,submit_datetime) values($campaign_id,'$email_addr','$hostname','$ip_addr',now())";
	$rows = $dbh->do($sql);
	if ($dbh->err() != 0)
	{
		$errmsg = $dbh->errstr();
		util::logerror("Inserting into test_emails record for $campaign_id: $errmsg");
		exit(0);
	}
}

if ($profile_id > 0)
{
	$sql = "select aol_flag,hotmail_flag,yahoo_flag,other_flag,max_emails from list_profile where profile_id=?";
    my $sth4 = $dbh->prepare($sql);
    $sth4->execute($profile_id);
    ($aolflag,$hotmailflag,$yahooflag,$otherflag,$max_emails) = $sth4->fetchrow_array();
    $sth4->finish();

	my $lrDomIDsIN=[];
	my $lrDomIDsNOTIN=[];
	for (my $i=1; $i <= 3; $i++) 
	{
		my $qSelDom=qq^SELECT domain_id FROM email_domains WHERE domain_class=?^;
        my $sthDom=$dbh->prepare($qSelDom);
		$sthDom->execute($i);
		while (my ($domID)=$sthDom->fetchrow) 
		{
			if ($i == 1) 
			{
				push @$lrDomIDsIN, $domID if $aolflag eq 'Y' && $otherflag eq 'N';
				push @$lrDomIDsNOTIN, $domID if $aolflag eq 'N' && $otherflag eq 'Y';
			}
			elsif ($i == 2) 
			{
				push @$lrDomIDsIN, $domID if $hotmailflag eq 'Y' && $otherflag eq 'N';
				push @$lrDomIDsNOTIN, $domID if $hotmailflag eq 'N' && $otherflag eq 'Y';
			}
			else 
			{
				push @$lrDomIDsIN, $domID if $yahooflag ne 'N' && $otherflag eq 'N';
				push @$lrDomIDsNOTIN, $domID if $yahooflag eq 'N' && $otherflag eq 'Y';
			}
		}
		$sthDom->finish;
	}

	my $in_domain_sql=join(', ', @$lrDomIDsIN);
	my $nin_domain_sql=join(', ', @$lrDomIDsNOTIN);
	$sql = "select email_addr from email_list,list_profile_list,email_domains where email_list.list_id=list_profile_list.list_id and email_list.status='A' and list_profile_list.profile_id=$profile_id and email_list.domain_id=email_domains.domain_id";
	$sql.=qq^ AND email_domains.domain_id IN ($in_domain_sql)^ if $in_domain_sql;
	$sql.=qq^ AND email_domains.domain_id NOT IN ($nin_domain_sql)^ if $nin_domain_sql;
	if ($max_emails != -1)
	{
		$sql = $sql . " limit $max_emails";
	}
    my $sth = $dbh->prepare($sql);
    $sth->execute();
	while (($email_addr) = $sth->fetchrow_array())
	{
		$sql = "insert into test_emails(campaign_id,email_addr,hostname,ip_addr,submit_datetime) values($campaign_id,'$email_addr','$hostname','$ip_addr',now())";
		$rows = $dbh->do($sql);
	}
	$sth->finish();
}
print "Location: mainmenu.cgi\n\n";
$util->clean_up();
exit(0);
