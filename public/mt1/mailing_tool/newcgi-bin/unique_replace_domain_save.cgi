#!/usr/bin/perl

#******************************************************************************
# unique_replace_domain_save.cgi
#
# this page updates domains for deploy 
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
my $aid;
my $rows;
my $errmsg;
my $tracking_id;
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
my $uidstr = $query->param('uidstr');
my $gsm= $query->param('gsm');
my $sord= $query->param('sord');
my @domain= $query->param('domainid');
my @cdomain= $query->param('cdomainid');
my $pastedomain=$query->param('pastedomainid');
my $cpastedomain=$query->param('cpastedomainid');
my $savepaste=$pastedomain;
if ($pastedomain ne '')
{
    $pastedomain =~ s/[ \n\r\f\t]/\|/g ;
    $pastedomain =~ s/\|{2,999}/\|/g ;
    @domain= split '\|', $pastedomain;
}
if ($cpastedomain ne '')
{
    $cpastedomain =~ s/[ \n\r\f\t]/\|/g ;
    $cpastedomain =~ s/\|{2,999}/\|/g ;
    @cdomain= split '\|', $cpastedomain;
}
my @U=split('\|',$uidstr);

foreach my $uid (@U)
{
	# check to see if uid is active or not
	my $cstatus;
	$sql="select status from unique_campaign where unq_id=?";
	$sth=$dbhu->prepare($sql);
	$sth->execute($uid);
	($cstatus)=$sth->fetchrow_array();
	$sth->finish();

	if (($cstatus eq "START") or ($cstatus eq "PENDING"))
	{
		$sql="update unique_campaign set mailing_domain='$domain[0]',pasted_domains='$savepaste' where unq_id=$uid";
		$rows=$dbhu->do($sql);
		$sql="delete from UniqueDomain where unq_id=$uid";
		$rows=$dbhu->do($sql);
		my $i=0;
		while ($i <= $#domain)
		{
    		$domain[$i]=~s/,//g;
    		$sql="insert ignore into UniqueDomain(unq_id,mailing_domain) values($uid,'$domain[$i]')";
    		$rows=$dbhu->do($sql);
    		$i++;
		}
		$sql="delete from UniqueContentDomain where unq_id=$uid";
		$rows=$dbhu->do($sql);
		my $i=0;
		while ($i <= $#cdomain)
		{
    		$cdomain[$i]=~s/,//g;
    		$sql="insert ignore into UniqueContentDomain(unq_id,domain_name) values($uid,'$cdomain[$i]')";
    		$rows=$dbhu->do($sql);
    		$i++;
		}
	}
}

print "Location: /cgi-bin/unique_deploy_list.cgi?gsm=$gsm&sord=$sord\n\n";
