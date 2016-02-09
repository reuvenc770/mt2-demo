#!/usr/bin/perl
# *****************************************************************************************
# listprofile_ins.cgi
#
# this page writes a record to the list_profile table 
#
# History
# Jim Sobeck, 5/31/05, Creation
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
my $errmsg;
my $user_id;
my $list_name;
my %checkit = ( 'Y' => 'CHECKED', 'N' => '' );
my $images = $util->get_images_url;

# connect to the util database

my ($dbhq,$dbhu)=$util->get_dbh();

# check for login

$user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}
my $network_id = $query->param('network_id');
my $tflag= $query->param('tflag');
if ($tflag eq "")
{
	$tflag="N";
}
my $alllist= $query->param('alllist');
if ($alllist eq "")
{
	$alllist="N";
}

my @DOM=$query->param('domains');
my $profilename = $query->param('profilename');
my $clast60 = $query->param('clast60');

my $i=0;

my $aolflag 	= "N";
my $yahooflag 	= "N";
my $hotmailflag = "N";
my $otherflag 	= "N";
my $comcastflag = "N";

#set flags for list_profile
while ($i <= $#DOM) {
	
	if ($DOM[$i] == 1){
		 $aolflag 	= "Y";
	}
	
	if ($DOM[$i] == 2){
		$hotmailflag = "Y";
	}	
	
	if ($DOM[$i] == 3){
		$yahooflag 	= "Y";
		
	}	
	
	if ($DOM[$i] == 4){
		$otherflag 	= "Y";
	}	
	
	if ($DOM[$i] == 6){
		$comcastflag = "Y";
	}	

	$i++;
} #end while

#my $aolflag = $query->param('AOL');
#if ($aolflag eq "")
#{
#	$aolflag = "N";
#}
#my $yahooflag = $query->param('yahooflag');
#if ($yahooflag eq "")
#{
#	$yahooflag = "N";
#}
#my $yahooflag1 = $query->param('yahooflag1');
#if ($yahooflag1 eq "M")
#{
#	$yahooflag = "M";
#}
#my $otherflag = $query->param('otherflag');
#if ($otherflag eq "")
#{
#	$otherflag = "N";
#}
#my $hotmailflag = $query->param('hotmailflag');
#if ($hotmailflag eq "")
#{
#	$hotmailflag = "N";
#}
#my $comcastflag = $query->param('comcastflag');
#if ($comcastflag eq "")
#{
#	$comcastflag = "N";
#}

my $nl_id = $query->param('nl_id');
if ($nl_id eq "")
{
	$nl_id=0;
}
my $nl_send = $query->param('nl_send');
if ($nl_send eq "")
{
	$nl_send="ALL";
}
my $open_click_flag= $query->param('open_click_ignore');
if ($open_click_flag eq "")
{
	$open_click_flag="Y";
}
my $type_str;
if ($tflag eq "N")
{
	$type_str="NORMAL";
}
elsif ($tflag eq "3")
{
	$type_str="3RDPARTY";
}
elsif ($tflag eq "C")
{
	$type_str="CHUNK";
}
elsif ($tflag eq "L")
{
	$type_str="NEWSLETTER";
}

$sql = "insert into list_profile(profile_name,client_id,day_flag,aol_flag,yahoo_flag,other_flag,
hotmail_flag,profile_type,nl_id,nl_send,comcast_flag,open_clickers_ignore_date) 
values('$profilename',$network_id,'$clast60','$aolflag','$yahooflag','$otherflag','$hotmailflag',
'$type_str',$nl_id,'$nl_send','$comcastflag','$open_click_flag')";
my $rows=$dbhu->do($sql);

#
#	 Get the id just added
#
my $profile_id;
$sql = "select max(profile_id) from list_profile where profile_name='$profilename' and client_id=$network_id";
$sth = $dbhq->prepare($sql) ;
$sth->execute();
($profile_id) = $sth->fetchrow_array();
$sth->finish();

$i=0;
while ($i <= $#DOM) {
	$sql = "insert into list_profile_domain values($profile_id, $DOM[$i])"; 
	$dbhu->do($sql);
	$i++;
} #end while
#
# Add selected lists
#
if ($alllist eq "Y")
{
	$sql="insert into list_profile_list(profile_id,list_id) select $profile_id,list_id from list where list_name in ('Openers','Clickers') and user_id=$network_id";
	$dbhu->do($sql);
	$sql="insert into list_profile_list(profile_id,list_id) select $profile_id,list_id from list where list_name in (select type_str from datatypes) and user_id=$network_id";
	$dbhu->do($sql);
}
#
#	If newsletter then create profiles for all the other clients
#
if ($tflag eq "L")
{
	$sql="insert ignore into list_profile(profile_name,client_id,day_flag,aol_flag,yahoo_flag,other_flag,hotmail_flag,profile_type,nl_id,nl_send,comcast_flag,open_clickers_ignore_date) select '$profilename',user_id,'$clast60','$aolflag','$yahooflag','$otherflag','$hotmailflag','$type_str',$nl_id,'$nl_send','$comcastflag','$open_click_flag' from user where user.status='A'";
	my $rows=$dbhu->do($sql);
	#
	# Add selected lists
	#
	my $client_id;
	if ($alllist eq "Y")
	{
		$sql = "select profile_id,client_id from list_profile where profile_name='$profilename' and nl_id=$nl_id";
		$sth = $dbhq->prepare($sql) ;
		$sth->execute();
		while (($profile_id,$client_id) = $sth->fetchrow_array())
		{
			$sql="insert ignore into list_profile_list(profile_id,list_id) select $profile_id,list_id from list where list_name in ('Openers','Clickers') and user_id=$client_id";
			$dbhu->do($sql);
			$sql="insert into list_profile_list(profile_id,list_id) select $profile_id,list_id from list where list_name in (select type_str from datatypes) and user_id=$client_id";
			$dbhu->do($sql);
		}
	}
#
# Add domains to list_profile_domain table
#
#	$i=0;
#	while ($i <= $#DOM)
#	{
#		$sql="insert into list_profile_domain(profile_id,domain_id) select profile_id,$DOM[$i] from list_profile where profile_name='$profilename' and nl_id=$nl_id"; 
#		my $rows=$dbhu->do($sql);
#		$i++;
#	}
}
#
#	if Chunking then set domains based on flags selected
#
if ($tflag eq "C")
{
	if ($aolflag ne "N")
	{
		$sql="insert into profile_chunk_domain(profile_id,domain_id) select $profile_id,domain_id from email_domains where domain_class=1 and chunked=1";
		my $rows=$dbhu->do($sql);
	}
	if ($hotmailflag ne "N")
	{
		$sql="insert into profile_chunk_domain(profile_id,domain_id) select $profile_id,domain_id from email_domains where domain_class=2 and chunked=1";
		my $rows=$dbhu->do($sql);
	}
	if ($yahooflag ne "N")
	{
		$sql="insert into profile_chunk_domain(profile_id,domain_id) select $profile_id,domain_id from email_domains where domain_class=3 and chunked=1";
		my $rows=$dbhu->do($sql);
	}
	if ($comcastflag ne "N")
	{
		$sql="insert into profile_chunk_domain(profile_id,domain_id) select $profile_id,domain_id from email_domains where domain_class=6 and chunked=1";
		my $rows=$dbhu->do($sql);
	}
	if ($otherflag ne "N")
	{
		$sql="insert into profile_chunk_domain(profile_id,domain_id) select $profile_id,domain_id from email_domains where domain_class=4 and chunked=1";
		my $rows=$dbhu->do($sql);
	}
}
print "Location: /cgi-bin/listprofile_edit.cgi?pid=$profile_id&tflag=$tflag\n\n";
