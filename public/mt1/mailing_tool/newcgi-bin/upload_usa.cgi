#!/usr/bin/perl
#===============================================================================
# File   : upload_usa.cgi 
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
my $cnt;
my $alt_light_table_bg = $util->get_alt_light_table_bg;
my $iret;
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

#----- Pass control to PROCESS_FILE  or  PROCESS_LIST  -------
if ( $upload_file ne "" ) 
{
	&process_file() ;
}
else
{
	print "Location: unique_advertiser_main.cgi\n\n";
}
exit(0) ;


sub process_file 
{
	my ($file_name, $file_handle, $file_problem, $file_in, $line, @rest_of_line);
	my $sth;
	my $sth1;
	my $upload_dir_unix;
	my ($usa_id,$usa_name,$aname,$c_code,$f_code,$s_code,@rest_of_line); 
	my $old_usa_id;
	my $tid;
	my $aid;

	print "Content-type: text/html\n\n";
	print<<"end_of_html";
<html><head><title>USA Upload Results</title></head>
<body>
<center>
<table>
end_of_html
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

	$file_in = "${upload_dir_unix}usa.${user_id}" ;
	open(SAVED,">$file_in") || &logerror("Error - could NOT open Output SAVED file: $file_in");
	$file_handle = $upload_file ;
	print SAVED <$file_handle> ;
	close SAVED;

    my ($sec, $min, $hr, $day, $month, $year, $wkdy, $yrdy, $isDST)=localtime();
    $month+=1; $year+=1900;
	$old_usa_id=0;
	my $last_usaname="";
	open(LOG,">>/tmp/upload_usa_$month$day$year.log");
	print LOG "$hr:$sec - $user_id\n";
	open(SAVED,"<$file_in") || &logerror("Error - could NOT open Input SAVED file: $file_in");
	while (<SAVED>) 
	{
		chomp;                       # remove Carriage Return (if exists)
		$line = $_;
		$line =~ s///g ;      # remove ^M from Email Addr (if exists)
		$line =~ s/\t/|/g ;
		$line =~ s/,/|/g ;
		print LOG "$hr:$sec - $user_id - <$line>\n";
		($usa_id,$usa_name,$aname,$c_code,$f_code,$s_code,@rest_of_line) = split('\|', $line) ;
		if ($usa_name eq "USA Name")
		{
			next;
		}
		if ($usa_id == 0)
		{
			if ($usa_name ne $last_usaname)
			{
				$sql="select usa_id from UniqueScheduleAdvertiser where name=?";
				$sth=$dbhu->prepare($sql);
				$sth->execute($usa_name);
				if (($tid)=$sth->fetchrow_array())
				{
					print "<tr><td><font color=red>USA $usa_name not added because one with that name already exists</font></td></tr>\n";
					$sth->finish();
					next;
				}
				$sth->finish();
				$sql="select advertiser_id from advertiser_info where advertiser_name=? and status='A' and test_flag='N'";
				$sth=$dbhu->prepare($sql);
				$sth->execute($aname);
				if (($aid)=$sth->fetchrow_array())
				{
					$sth->finish();
				}
				else
				{
					$sth->finish();
					print "<tr><td><font color=red>USA $usa_name not added because advertiser $aname not found</font></td></tr>\n";
					next;
				}
				$iret=checkAsset($usa_id,$aid,$c_code,$f_code,$s_code,1);
				if (!$iret)
				{
					print "<tr><td><font color=red>USA $usa_name not added because one of the assets was missing or for wrong advertiser. ($line)</font></td></tr>\n";
					next;
				}
				$last_usaname=$usa_name;
				$usa_name=~s/'/''/g;
				$sql="insert into UniqueScheduleAdvertiser(advertiser_id,name,creative_id,subject_id,from_id,lastUpdated) values($aid,'$usa_name',$c_code,$s_code,$f_code,curdate())";
				$rows=$dbhu->do($sql);
				$sql="select LAST_INSERT_ID()";
				$sth=$dbhu->prepare($sql);
				$sth->execute();
				($tid)=$sth->fetchrow_array();
				$sth->finish();
				$sql="insert ignore into UniqueAdvertiserCreative(usa_id,creative_id) values($tid,$c_code)";
				$rows=$dbhu->do($sql);
				$sql="insert ignore into UniqueAdvertiserSubject(usa_id,subject_id) values($tid,$s_code)";
				$rows=$dbhu->do($sql);
				$sql="insert ignore into UniqueAdvertiserFrom(usa_id,from_id) values($tid,$f_code)";
				$rows=$dbhu->do($sql);
				print "<tr><td>USA $last_usaname added</font></td></tr>\n";
			}
			else
			{
				$iret=checkAsset($tid,$aid,$c_code,$f_code,$s_code,0);
			}
		}
		else
		{
			if ($usa_id != $old_usa_id)
			{
				$sql="select advertiser_id from UniqueScheduleAdvertiser where usa_id=?";
				$sth=$dbhu->prepare($sql);
				$sth->execute($usa_id);
				if (($aid)=$sth->fetchrow_array())
				{
				}
				else
				{
					print "<tr><td><font color=red>USA $usa_id not found</font></td></tr>\n";
					next;
				}
				$sth->finish();
				$old_usa_id=$usa_id;
				$sql="delete from UniqueAdvertiserCreative where usa_id=$usa_id";
				$rows=$dbhu->do($sql);
				$sql="delete from UniqueAdvertiserSubject where usa_id=$usa_id";
				$rows=$dbhu->do($sql);
				$sql="delete from UniqueAdvertiserFrom where usa_id=$usa_id";
				$rows=$dbhu->do($sql);
				$sql="update UniqueScheduleAdvertiser set creative_id=0,from_id=0,subject_id=0,lastUpdated=curdate() where usa_id=$usa_id";
				$rows=$dbhu->do($sql);
				if (($c_code eq "") and ($f_code eq "") and ($s_code eq ""))
				{
					$sql="delete from UniqueScheduleAdvertiser where usa_id=$usa_id";
					$rows=$dbhu->do($sql);
					print "<tr><td>>USA $usa_name($usa_id) deleted because no assets specified<font></td></tr>\n";
					next;
				}	
				$iret=checkAsset($usa_id,$aid,$c_code,$f_code,$s_code,1);
				if (!$iret)
				{
					print "<tr><td><font color=red>USA $usa_name($usa_id) first line must contain valid assets. ($line)</font></td></tr>\n";
					next;
				}
			}
			else
			{
				$iret=checkAsset($usa_id,$aid,$c_code,$f_code,$s_code,0);
			}
		}

	} 
	close SAVED;
	close LOG;
	unlink($file_in) || &logerror("Error - could NOT Remove file: $file_in");  # del file_in
	print<<"end_of_html";
</table>
<br><a href="unique_advertiser_main.cgi">Back to Unique Schedule Advertiser</a>
</center>
</body>
</html>
end_of_html
} # end of sub

sub checkAsset
{
	my ($usa_id,$aid,$c_code,$f_code,$s_code,$first)=@_;
	my $iret=1;
	my $sth1;
	my $sql;
	my $rows;
	
	if (($first == 1) and (($c_code eq "") or ($f_code eq "") or ($s_code eq "")))
	{
		return 0;
	}
	if (($c_code ne "") and ($c_code > 0))
	{
		$sql="select count(*) from creative where advertiser_id=? and creative_id=? and status='A'";
		$sth1=$dbhu->prepare($sql);
		$sth1->execute($aid,$c_code);
		($cnt)=$sth1->fetchrow_array();
		$sth1->finish();
		if (($cnt > 0) and ($usa_id > 0))
		{
			$sql="insert ignore into UniqueAdvertiserCreative(usa_id,creative_id) values($usa_id,$c_code)";
			$rows=$dbhu->do($sql);
			if ($first)
			{
				$sql="update UniqueScheduleAdvertiser set creative_id=$c_code,lastUpdated=curdate() where usa_id=$usa_id";
				$rows=$dbhu->do($sql);
			}
		}
		elsif (($usa_id > 0) and ($cnt == 0))
		{
			print "<tr><td><font color=red>Creative $c_code not added because doesnt exist, not active, or not for specified advertiser</font></td></tr>\n";
			if ($first)
			{
				return 0;
			}
		}
		elsif (($usa_id == 0) and ($cnt == 0))
		{
			return 0;
		}
	}
	if (($f_code ne "") and ($f_code > 0))
	{
		$sql="select count(*) from advertiser_from where advertiser_id=? and from_id=? and status='A'";
		$sth1=$dbhu->prepare($sql);
		$sth1->execute($aid,$f_code);
		($cnt)=$sth1->fetchrow_array();
		$sth1->finish();
		if (($cnt > 0) and ($usa_id > 0))
		{
			$sql="insert ignore into UniqueAdvertiserFrom(usa_id,from_id) values($usa_id,$f_code)";
			$rows=$dbhu->do($sql);
			if ($first)
			{
				$sql="update UniqueScheduleAdvertiser set from_id=$f_code,lastUpdated=curdate() where usa_id=$usa_id";
				$rows=$dbhu->do($sql);
			}
		}
		elsif ($usa_id > 0)
		{
			print "<tr><td><font color=red>From $f_code not added because doesnt exist, not active, or not for specified advertiser</font></td></tr>\n";
			if ($first)
			{
				return 0;
			}
		}
		elsif (($usa_id == 0) and ($cnt == 0))
		{
			return 0;
		}
	}
	if (($s_code ne "") and ($s_code > 0))
	{
		$sql="select count(*) from advertiser_subject where advertiser_id=? and subject_id=? and status='A'";
		$sth1=$dbhu->prepare($sql);
		$sth1->execute($aid,$s_code);
		($cnt)=$sth1->fetchrow_array();
		$sth1->finish();
		if (($cnt > 0) and ($usa_id > 0))
		{
			$sql="insert ignore into UniqueAdvertiserSubject(usa_id,subject_id) values($usa_id,$s_code)";
			$rows=$dbhu->do($sql);
			if ($first)
			{
				$sql="update UniqueScheduleAdvertiser set subject_id=$s_code,lastUpdated=curdate() where usa_id=$usa_id";
				$rows=$dbhu->do($sql);
			}
		}
		elsif ($usa_id > 0)
		{
			print "<tr><td><font color=red>Subject $s_code not added because doesnt exist, not active, or not for specified advertiser</font></td></tr>\n";
			if ($first)
			{
				return 0;
			}
		}
		elsif (($usa_id == 0) and ($cnt == 0))
		{
			return 0;
		}
	}
	return 1;
}
