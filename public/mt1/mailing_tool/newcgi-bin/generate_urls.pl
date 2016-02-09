#!/usr/bin/perl
#===============================================================================
#--Change Control---------------------------------------------------------------
# 05/02/05  Jim Sobeck  Creation
#===============================================================================

#-----  include Perl Modules ---------
use lib ('/tmp/repos/newcgi-bin/');
use strict;
use CGI;
use util;
use Data::Dumper;

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
my $input_client_id=$query->param('cid');
my $rows;
my $aid;
my $hitpath_id;
my @type = ( "N","D","2","S");

#------  connect to the util database -----------
my $dbhq;
my $dbhu;
($dbhq,$dbhu)=$util->get_dbh();

#my $sql_user="select user_id from user where status = 'A'";
my $sql_user="select user_id from user where user_id = 136";
my $user_sth = $dbhq->prepare($sql_user);
$user_sth->execute();
my $results = $user_sth->fetchall_arrayref();

for (my $i = 0 ; $i <= $#$results ;$i++){
	
	$input_client_id = $results->[$i][0];
	
my $lid;
my $i;
my $ctype;

$sql="select url,advertiser_tracking.advertiser_id from advertiser_tracking,advertiser_info where advertiser_tracking.client_id=1 and daily_deal='N' and advertiser_tracking.advertiser_id=advertiser_info.advertiser_id and advertiser_info.status in ('A','S')";
$sth = $dbhq->prepare($sql);
$sth->execute();

while (($url,$aid) = $sth->fetchrow_array())
{
	$_ = $url;
	if (/mediactivate.com/)
	{
		$i=0;
		while ($i <= $#type)
		{
			$ctype=$type[$i];
			if ($ctype eq "N")
			{
				$sql = "select mediactivate_id,hitpath_id from user where user_id=1";
			}
			elsif ($ctype eq "D")
			{
				$sql = "select dosmonos_mediactive_id,cbs_hitpath_id from user where user_id=1";
			}
			elsif ($ctype eq "2")
			{
				$sql = "select dosmonos2_mediactive_id,dosmonos2_hitpath_id from user where user_id=1";
			}
			elsif ($ctype eq "S")
			{
				$sql = "select dosmonos_mediactive_id,slks_hitpath_id from user where user_id=1";
			}
			my $sth2a = $dbhq->prepare($sql);
			$sth2a->execute();
			($code,$hitpath_id) = $sth2a->fetchrow_array();
			$sth2a->finish();
			# Remove old URLS
			$sql = "delete from advertiser_tracking where client_id=$input_client_id and advertiser_id=$aid and daily_deal='$ctype'";
			$rows = $dbhu->do($sql);
			#
			if ($ctype eq "N")
			{
    			$sql = "select mediactivate_id,user_id from user where mediactivate_id != '' and user_id=$input_client_id and status='A'"; 
			}
			elsif ($ctype eq "D")
			{
    			$sql = "select dosmonos_mediactive_id,user_id from user where dosmonos_mediactive_id != '' and user_id=$input_client_id and status='A'"; 
			}
			elsif ($ctype eq "2")
			{
    			$sql = "select dosmonos2_mediactive_id,user_id from user where dosmonos2_mediactive_id != '' and user_id=$input_client_id and status='A'"; 
			}
			elsif ($ctype eq "S")
			{
    			$sql = "select slks_mediactive_id,user_id from user where slks_mediactive_id != '' and user_id=$input_client_id and status='A'"; 
			}
			$sth2 = $dbhq->prepare($sql);
			$sth2->execute();
			while (($mid,$client_id) = $sth2->fetchrow_array())
			{
				my $temp_url = $url;
				$temp_url =~ s/\/$code\//\/$mid\//;
				print "<br>$aid - $temp_url\n";
				$sql="select max(link_id) from links where refurl='$temp_url'";
				$sth1 = $dbhq->prepare($sql) ;
				$sth1->execute();
				($lid) = $sth1->fetchrow_array();
				$sth1->finish();
				if ($lid eq "")
				{
					$sql="insert into links(refurl,date_added) values('$temp_url',now())";
					$rows = $dbhu->do($sql);
					#
					# Get the id just added
					#
					$sql="select max(link_id) from links where refurl='$temp_url'";
					$sth1 = $dbhq->prepare($sql) ;
					$sth1->execute();
					($lid) = $sth1->fetchrow_array();
					$sth1->finish();
				}
#
# Insert record into advertiser_tracking
#
				$sql="insert into advertiser_tracking(advertiser_id,url,code,date_added,client_id,link_id,daily_deal) values($aid,'$temp_url','$mid',curdate(),$client_id,$lid,'$ctype')";
				$rows = $dbhu->do($sql);
			}
			$sth2->finish();
			$i++;
		}
	}
	elsif (/adiclicks.com/)
	{
		$i=0;
		while ($i <= $#type)
		{
			$ctype=$type[$i];
			if ($ctype eq "N")
			{
				$sql = "select mediactivate_id,hitpath_id from user where user_id=1";
			}
			elsif ($ctype eq "D")
			{
				$sql = "select dosmonos_mediactive_id,cbs_hitpath_id from user where user_id=1";
			}
			elsif ($ctype eq "2")
			{
				$sql = "select dosmonos2_mediactive_id,dosmonos2_hitpath_id from user where user_id=1";
			}
			elsif ($ctype eq "S")
			{
				$sql = "select dosmonos_mediactive_id,slks_hitpath_id from user where user_id=1";
			}
			my $sth2a = $dbhq->prepare($sql);
			$sth2a->execute();
			($code,$hitpath_id) = $sth2a->fetchrow_array();
			$sth2a->finish();
			# Remove old URLS
			$sql = "delete from advertiser_tracking where client_id=$input_client_id and advertiser_id=$aid and daily_deal='$ctype'";
			$rows = $dbhu->do($sql);
			#
			if ($ctype eq "N")
			{
				$sql = "select hitpath_id,user_id from user where hitpath_id!= '' and user_id = $input_client_id and status='A'";
			}
			elsif ($ctype eq "D")
			{
				$sql = "select cbs_hitpath_id,user_id from user where cbs_hitpath_id!= '' and user_id = $input_client_id and status='A'";
			}
			elsif ($ctype eq "2")
			{
				$sql = "select dosmonos2_hitpath_id,user_id from user where dosmonos2_hitpath_id != '' and user_id = $input_client_id and status='A'";
			}
			elsif ($ctype eq "S")
			{
				$sql = "select slks_hitpath_id,user_id from user where slks_hitpath_id != '' and user_id = $input_client_id and status='A'";
			}
			$sth2 = $dbhq->prepare($sql);
			$sth2->execute();
			while (($mid,$client_id) = $sth2->fetchrow_array())
			{
				my $temp_url = $url;
				$temp_url =~ s/\/$hitpath_id\//\/$mid\//;
				print "<br>$aid - $temp_url\n";
				$sql="select max(link_id) from links where refurl='$temp_url'";
				$sth1 = $dbhq->prepare($sql) ;
				$sth1->execute();
				if (($lid) = $sth1->fetchrow_array())
				{
					$sth1->finish();
				}
				else
				{
					$sth1->finish();
					$sql="insert into links(refurl,date_added) values('$temp_url',now())";
					$rows = $dbhu->do($sql);
					#
					# Get the id just added
					#
					$sql="select max(link_id) from links where refurl='$temp_url'";
					$sth1 = $dbhq->prepare($sql) ;
					$sth1->execute();
					($lid) = $sth1->fetchrow_array();
					$sth1->finish();
				}
#
# Insert record into advertiser_tracking
#
				$sql="insert into advertiser_tracking(advertiser_id,url,code,date_added,client_id,link_id,daily_deal) values($aid,'$temp_url','$mid',curdate(),$client_id,$lid,'$ctype')";
				$rows = $dbhu->do($sql);
			}
			$sth2->finish();
			$i++;
		}
	}
}

} #end for

$sth->finish();

exit;
