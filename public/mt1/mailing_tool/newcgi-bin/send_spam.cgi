#!/usr/bin/perl

# *****************************************************************************************
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $sth1;
my $sql;
my $dbh;
my $rows;
my $errmsg;
my $html_template;
my $sth1;
my $BASE_DIR;

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

# get the fields from the form 

my $campaign_id = $query->param('cid');
my $to_addr = $query->param('cemail');
my $returnto = $query->param('returnto');
my $format = $query->param('format');
my $cdraft = $query->param('cdraft');
if ($cdraft eq "")
{
	$cdraft="N";
}
my $qaID=qq|SELECT advertiser_id AS aid FROM creative WHERE creative_id=?|;
my $sthaID=$dbhq->prepare($qaID);
$sthaID->execute($campaign_id);
my $aid=$sthaID->fetchrow;
$sthaID->finish;
$sql = "select parmval from sysparm where parmkey='BASE_DIR'";
$sth1 = $dbhq->prepare($sql);
$sth1->execute();
($BASE_DIR) = $sth1->fetchrow_array();
$sth1->finish;
my @args = ("${BASE_DIR}newcgi-bin/test_spam.sh","$campaign_id","$aid","$to_addr","$cdraft");
system(@args) == 0 or die "system @args failed: $?";
if ($returnto eq "preview")
{
	if ($cdraft eq "Y")
	{ 
		print "Location: camp_draft_preview.cgi?campaign_id=$campaign_id&format=$format\n\n";
	}
	else
	{
		print "Location: camp_preview.cgi?campaign_id=$campaign_id&format=$format\n\n";
	}
}
else
{
	if ($cdraft eq "Y")
	{ 
		print "Location: edit_draft_creative.cgi?cid=$campaign_id&aid=$aid\n\n";
	}
	else
	{
		print "Location: edit_creative.cgi?cid=$campaign_id&aid=$aid\n\n";
	}
}
exit(0);
