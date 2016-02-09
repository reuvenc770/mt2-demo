#!/usr/bin/perl
#===============================================================================
#--Change Control---------------------------------------------------------------
# 05/02/05  Jim Sobeck  Creation
#===============================================================================

#-----  include Perl Modules ---------
use strict;
use CGI;
use util;

#------  get some objects to use later ---------
my $util = util->new;
my $query = CGI->new;
my $name;
my $sql;
my $sth;
my $sth1;
my $sth2;
my $dbh;
my $phone;
my $email;
my $company;
my $id;
my $aim;
my $website;
my $username;
my $password;
my $notes;
my $url;
my $code;
my $mid;
my $client_id;
my $rows;
my $aid = $query->param('aid');
my $ctype = $query->param('ctype');
$ctype="2";

#-----  check for login  ------
my ($dbhq,$dbhu)=$util->get_dbh();
if ($ctype eq "N")
{
	$sql = "select mediactivate_id from user where user_id=1";
}
elsif ($ctype eq "I")
{
	$sql = "select intela_mediactive_id from user where user_id=1";
}
elsif ($ctype eq "R")
{
	$sql = "select raut_mediactive_id from user where user_id=1";
}
elsif ($ctype eq "D")
{
	$sql = "select dosmonos_mediactive_id from user where user_id=1";
}
elsif ($ctype eq "2")
{
	$sql = "select dosmonos2_mediactive_id from user where user_id=1";
}
elsif ($ctype eq "S")
{
	$sql = "select dosmonos_mediactive_id from user where user_id=1";
}
$sth = $dbhq->prepare($sql);
$sth->execute();
($code) = $sth->fetchrow_array();
$sth->finish();
#
$sql="select distinct advertiser_id from advertiser_tracking where daily_deal = '$ctype' order by advertiser_id";
my $s=$dbhq->prepare($sql);
$s->execute();
while (($aid)=$s->fetchrow_array())
{
	print "Processing $aid\n";
$sql="select url from advertiser_tracking where advertiser_tracking.client_id=1 and advertiser_id=$aid and daily_deal='N'";
$sth = $dbhq->prepare($sql);
$sth->execute();
if (($url) = $sth->fetchrow_array())
{
	$_ = $url;
	if (/mediactivate.com/)
	{
		# Remove old URLS
		$sql = "delete from advertiser_tracking where client_id > 1 and advertiser_id=$aid and daily_deal='$ctype'";
		$rows = $dbhu->do($sql);
		#
		if ($ctype eq "N")
		{
			$sql = "select mediactivate_id,user_id from user where mediactivate_id != '' and user_id > 1 and status='A'";
		}
		elsif ($ctype eq "I")
		{
			$sql = "select intela_mediactive_id,user_id from user where intela_mediactive_id != '' and user_id > 1 and status='A'";
		}
		elsif ($ctype eq "R")
		{
			$sql = "select raut_mediactive_id,user_id from user where raut_mediactive_id != '' and user_id > 1 and status='A'";
		}
		elsif ($ctype eq "D")
		{
			$sql = "select dosmonos_mediactive_id,user_id from user where dosmonos_mediactive_id != '' and user_id > 1 and status='A'";
		}
		elsif ($ctype eq "2")
		{
			$sql = "select dosmonos2_mediactive_id,user_id from user where dosmonos2_mediactive_id != '' and user_id > 1 and status='A'";
		}
		elsif ($ctype eq "S")
		{
			$sql = "select slks_mediactive_id,user_id from user where slks_mediactive_id != '' and user_id > 1 and status='A'";
		}
		$sth2 = $dbhq->prepare($sql);
		$sth2->execute();
		while (($mid,$client_id) = $sth2->fetchrow_array())
		{
			my $temp_url = $url;
			$temp_url =~ s/\/$code\//\/$mid\//;
			$sql="insert into links(refurl,date_added) values('$temp_url',now())";
			$rows = $dbhu->do($sql);
			#
			# Get the id just added
			#
my $lid;
$sql="select max(link_id) from links where refurl='$temp_url'";
$sth1 = $dbhq->prepare($sql) ;
$sth1->execute();
($lid) = $sth1->fetchrow_array();
$sth1->finish();
#
# Insert record into advertiser_tracking
#
$sql="insert into advertiser_tracking(advertiser_id,url,code,date_added,client_id,link_id,daily_deal) values($aid,'$temp_url','$mid',curdate(),$client_id,$lid,'$ctype')";
$rows = $dbhu->do($sql);
   $sql = "update advertiser_info set url_count=(select count(*) from advertiser_tracking where advertiser_tracking.advertiser_id=advertiser_info.advertiser_id and advertiser_tracking.advertiser_id=$aid)";
   $rows = $dbhu->do($sql);
		}
		$sth2->finish();
	}
	}
$sth->finish();
}
$s->finish();
my $sth3;
my $BASE_DIR;
my $link_id;
my $refurl;
$sql = "select parmval from sysparm where parmkey='BASE_DIR'";
$sth3 = $dbhq->prepare($sql);
$sth3->execute();
($BASE_DIR) = $sth3->fetchrow_array();
$sth3->finish;
#open(FILE,"> ${BASE_DIR}logs/redir.tmp") or die "can't open file : $!";
#$sql = "select link_id,refurl from links order by link_id";
#$sth3 = $dbhq->prepare($sql);
#$sth3->execute();
#while (($link_id,$refurl) = $sth3->fetchrow_array())
#{
#    print FILE "$link_id|$refurl\n";
#}
#$sth3->finish();
#close(FILE);
#rename "${BASE_DIR}logs/redir.tmp", "${BASE_DIR}logs/redir.dat";
#my @args = ("${BASE_DIR}newcgi-bin/cp_redir_tmp.sh");
#system(@args) == 0 or die "system @args failed: $?";
