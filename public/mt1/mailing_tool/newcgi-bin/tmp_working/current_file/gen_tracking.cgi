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

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}
#------  connect to the util database -----------
$util->db_connect();
$dbh = $util->get_dbh;
$sql = "select mediactivate_id from user where user_id=1";
$sth = $dbh->prepare($sql);
$sth->execute();
($code) = $sth->fetchrow_array();
$sth->finish();
#
$sql="select url from advertiser_tracking where advertiser_tracking.client_id=1 and advertiser_id=$aid and daily_deal='N'";
$sth = $dbh->prepare($sql);
$sth->execute();
if (($url) = $sth->fetchrow_array())
{
	$_ = $url;
	if (/mediactivate.com/)
	{
		# Remove old URLS
		$sql = "delete from advertiser_tracking where client_id > 1 and advertiser_id=$aid and daily_deal='N'";
		$rows = $dbh->do($sql);
		#
		$sql = "select mediactivate_id,user_id from user where mediactivate_id != '' and user_id > 1 and status='A'";
		$sth2 = $dbh->prepare($sql);
		$sth2->execute();
		while (($mid,$client_id) = $sth2->fetchrow_array())
		{
			my $temp_url = $url;
			$temp_url =~ s/\/$code\//\/$mid\//;
			$sql="insert into links(refurl,date_added) values('$temp_url',now())";
			$rows = $dbh->do($sql);
			#
			# Get the id just added
			#
my $lid;
$sql="select max(link_id) from links where refurl='$temp_url'";
$sth1 = $dbh->prepare($sql) ;
$sth1->execute();
($lid) = $sth1->fetchrow_array();
$sth1->finish();
#
# Insert record into advertiser_tracking
#
$sql="insert into advertiser_tracking(advertiser_id,url,code,date_added,client_id,link_id,daily_deal) values($aid,'$temp_url','$mid',curdate(),$client_id,$lid,'N')";
$rows = $dbh->do($sql);
   $sql = "update advertiser_info set url_count=(select count(*) from advertiser_tracking where advertiser_tracking.advertiser_id=advertiser_info.advertiser_id and advertiser_tracking.advertiser_id=$aid)";
   $rows = $dbh->do($sql);
		}
		$sth2->finish();
my $sth3;
my $BASE_DIR;
my $link_id;
my $refurl;
$sql = "select parmval from sysparm where parmkey='BASE_DIR'";
$sth3 = $dbh->prepare($sql);
$sth3->execute();
($BASE_DIR) = $sth3->fetchrow_array();
$sth3->finish;
open(FILE,"> ${BASE_DIR}logs/redir.dat") or die "can't open file : $!";
$sql = "select link_id,refurl from links order by link_id";
$sth3 = $dbh->prepare($sql);
$sth3->execute();
while (($link_id,$refurl) = $sth3->fetchrow_array())
{
    print FILE "$link_id|$refurl\n";
}
$sth3->finish();
close(FILE);
my @args = ("${BASE_DIR}newcgi-bin/cp_redir_tmp.sh");
system(@args) == 0 or die "system @args failed: $?";
	}
}
$sth->finish();
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Tracking</title>
</head>

<body>
<table width=80%><tr><th align=center>Client</th><th align=center>URL</th><th align=center>Code</th></tr>
end_of_html
$sql="select url,code,company from advertiser_tracking,user where advertiser_tracking.client_id=user.user_id and advertiser_id=$aid and daily_deal='N'";
$sth = $dbh->prepare($sql);
$sth->execute();
while (($url,$code,$company) = $sth->fetchrow_array())
{
	print "<tr><td>$company</td><td>$url</td><td>$code</td></tr>\n";
}
$sth->finish();
print<<"end_of_html";
<table>
<br><br>
<a href="/cgi-bin/advertiser_disp2.cgi?puserid=$aid"><img src="/images/cancel.gif" border=0></a></p>
</body>
</html>
end_of_html
