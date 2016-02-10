#!/usr/bin/perl

# ******************************************************************************
# unique_resumeall_save.cgi 
#
# this page updates information in the unique_campaign table for all Hotmail campaigns
#
# History
# ******************************************************************************

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
my $class;
my $errmsg;
my $rows;
my $uid;
my $images = $util->get_images_url;

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
# Remove old url information
#
my $template_id = $query->param('template_id');
my $mdomain= $query->param('mdomain');
my $cmdomain= $query->param('cmdomain');
$sql="select unq_id from unique_campaign where send_date=curdate() and slot_type='Hotmail' and status='PAUSED'";
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($uid)=$sth->fetchrow_array())
{
	$sql="update unique_campaign set status='START',mailing_template=$template_id where unq_id=$uid";
	$rows=$dbhu->do($sql);
	if ($mdomain ne "")
	{
		$sql="update unique_campaign set mailing_domain='$mdomain' where unq_id=$uid";
		$rows=$dbhu->do($sql);
		$sql="delete from UniqueDomain where unq_id=$uid";
		$rows=$dbhu->do($sql);
		$sql="insert into UniqueDomain(unq_id,mailing_domain) values($uid,'$mdomain')";
		$rows=$dbhu->do($sql);
	}
	if ($cmdomain ne "")
	{
		$sql="delete from UniqueContentDomain where unq_id=$uid";
		$rows=$dbhu->do($sql);
		$sql="insert into UniqueContentDomain(unq_id,domain_name) values($uid,'$cmdomain')";
		$rows=$dbhu->do($sql);
	}
}
$sth->finish();
print "Location: /cgi-bin/unique_deploy_list.cgi?gsm=2\n\n";
