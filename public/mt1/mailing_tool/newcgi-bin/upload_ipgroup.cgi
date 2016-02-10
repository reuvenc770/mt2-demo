#!/usr/bin/perl
#===============================================================================
# File   : upload_ipgroup.cgi 
#
#--Change Control---------------------------------------------------------------
#===============================================================================

#-----------------------
# include Perl Modules
#-----------------------
use strict;
use CGI;
use util;

$|=1 ;   # set OUTPUT_AUTOFLUSH to true

my $util = util->new;
my $query = CGI->new;
my $dbh;
my $rows;
my $sql;
my $alt_light_table_bg = $util->get_alt_light_table_bg;
my $images = $util->get_images_url;


# ------- Get fields from html Form post -----------------


# ----- check for login -------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}
my $upload_file = $query->param('upload_file');
my ($dbhq,$dbhu)=$util->get_dbh();

my $userDataRestrictionWhereClause = '';

$util->getUserData({'userID' => $user_id});

my $externalUser = $util->getUserData({'userID' => $user_id})->{'isExternalUser'};

if($util->getUserData()->{'isExternalUser'} == 1)
{
	$userDataRestrictionWhereClause = qq|
        userID = $user_id AND
    |;
}


#----- Pass control to PROCESS_FILE  or  PROCESS_LIST  -------
if ( $upload_file ne "" ) 
{
	&process_file() ;
	print "Location: ipgroup_list.cgi\n\n";
}
exit(0) ;


sub process_file 
{
	my ($file_name, $file_handle, $file_problem, $file_in, $line, @rest_of_line);
	my $gname;
	my $ip;
	my $chunkval;
	my $old_gname;
	my $group_id;
	my $colo;
	my $sql;
	my $sth;
	my $sth1;
	my $upload_dir_unix;

	# get upload subdir
	$sql = "select parmval from sysparm where parmkey = 'UPLOAD_DIR_UNIX'";
	$sth1 = $dbhq->prepare($sql) ;
	$sth1->execute();
	($upload_dir_unix) = $sth1->fetchrow_array();
	$sth1->finish();

	# deal with filename passed to this script

	if ( $upload_file =~ /([^\/\\]+)$/ ) 
	{
		$file_name = $1;                # set file_name to $1 var - (file-name no path)
		$file_name =~ s/^\.+//;         # say what...
		$file_name =~ s/\s/_/g;         # replace WhiteSpace with UnderScore global
		$file_handle = $upload_file ;
	}
	else 
	{
		$file_problem = $query->param('upfile');
		&error("Bad File Name: $file_problem, File name can't have a slash in it!\n Rename it and try again!" ) ;
		exit(0);
	}

	#---- Open file and save File to Unix box ---------------------------

	$file_in = "${upload_dir_unix}ipgroup.${user_id}" ;
	open(SAVED,">$file_in") || &logerror("Error - could NOT open Output SAVED file: $file_in");
	$file_handle = $upload_file ;
	print SAVED <$file_handle> ;
	close SAVED;

	open(SAVED,"<$file_in") || &logerror("Error - could NOT open Input SAVED file: $file_in");
	$old_gname="";
	while (<SAVED>) 
	{
		chomp;                       # remove Carriage Return (if exists)
		$line = $_;
		$line =~ s///g ;      # remove ^M from Email Addr (if exists)
		$line =~ s/\t/|/g ;
		$line =~ s/,/|/g ;
		($gname,$ip,$chunkval, @rest_of_line) = split('\|', $line) ;
		$ip=~s/ //g;
		$gname=~s/'/''/g;
		if ($gname ne $old_gname)
		{
			$sql="select group_id from IpGroup where $userDataRestrictionWhereClause status='Active' and group_name=?";
			$sth=$dbhu->prepare($sql);
			$sth->execute($gname);
			if (($group_id)=$sth->fetchrow_array())
			{
				$sth->finish();
				# Threw in the subquery to prevent external users from being able to remove IPs that are not their own.
				$sql="delete from IpGroupIps where group_id=(select group_id from IpGroup where $userDataRestrictionWhereClause group_id=$group_id limit 1)";
				$rows=$dbhu->do($sql);
			}
			else
			{
				$sth->finish();
				$colo="NAC";

				$sql="insert into IpGroup(userID, group_name,outbound_throttle,goodmail_enabled,colo,chunk,status) values($user_id, '$gname',0,'N','$colo',$chunkval,'Active')";
				$rows=$dbhu->do($sql);
				$sql="select max(group_id) from IpGroup where $userDataRestrictionWhereClause status='Active' and group_name=?";
				$sth=$dbhu->prepare($sql);
				$sth->execute($gname);
				($group_id)=$sth->fetchrow_array();
				$sth->finish();
			}
			$old_gname=$gname;
		}
		my $cnt=0;
#		if ($ip ne "")
#		{
#			$sql="select count(*) from IpGroupExclusion where ip=?";
#			$sth=$dbhu->prepare($sql);
#			$sth->execute($ip);
#			($cnt)=$sth->fetchrow_array();
#			$sth->finish();
#		}
#		else
#		{
#			$cnt=1;
#		}
		if ($ip eq "")
		{
			$cnt=1;
		}

		if ($cnt == 0)
		{
			# Threw in the subquery to prevent external users from being able to add IPs to groups that are not their own.
			# Looks like we never error check here, so this will just silently fail if someone is trying to do something we don't want.
			if($externalUser)
			{			
				$sql="insert into IpGroupIps(group_id,ip_addr) values((select group_id from IpGroup where $userDataRestrictionWhereClause group_id=$group_id limit 1),(select ip from IpAttribute where $userDataRestrictionWhereClause ip='$ip' limit 1))";
			}
			else
			{
				$sql="insert into IpGroupIps(group_id,ip_addr) values($group_id,'$ip')";
			}
			$rows=$dbhu->do($sql);
		}
	} 
	close SAVED;
	unlink($file_in); 

} # end of sub
