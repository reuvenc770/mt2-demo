#!/usr/bin/perl
#===============================================================================
# File   : client_exclusion_upload.cgi 
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
my $images = $util->get_images_url;


# ----- check for login -------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}
my $upload_file = $query->param('upload_file');
my $utype = $query->param('utype');
if ($utype eq "")
{
	$utype="category";
}
my ($dbhq,$dbhu)=$util->get_dbh();

#----- Pass control to PROCESS_FILE  or  PROCESS_LIST  -------
if ( $upload_file ne "" ) 
{
	&process_file() ;
}
else
{
	print "Location: client_exclusion.cgi\n\n";
}
exit(0) ;


sub process_file 
{
	my ($file_name, $file_handle, $file_problem, $file_in, $line, @rest_of_line);
	my $sql;
	my $sth;
	my $sth1;
	my $upload_dir_unix;
	my $CID;
	my $CAT;
	my $ADV;
	my $uid;
	my $catid;
	my $cname;
	my $aid;
	my ($clientID,$uname,$clientType,$category);

	$sql="select user_id from user";
	$sth=$dbhq->prepare($sql);
	$sth->execute();
	while (($uid)=$sth->fetchrow_array())
	{
		$CID->{$uid}=1;
	}
	$sth->finish();

	if ($utype eq "category")
	{
		$sql="select category_id,category_name from category_info";
		$sth=$dbhq->prepare($sql);
		$sth->execute();
		while (($catid,$cname)=$sth->fetchrow_array())
		{
			$CAT->{$cname}=$catid;
		}
		$sth->finish();
	}
	elsif ($utype eq "advertiser")
	{
		$sql="select advertiser_id from advertiser_info";
		$sth=$dbhq->prepare($sql);
		$sth->execute();
		while (($aid)=$sth->fetchrow_array())
		{
			$ADV->{$aid}=1;
		}
		$sth->finish();
	}

	print "Content-type: text/html\n\n";
	print<<"end_of_html";
<html><head><title>Client Exclusion Upload Results $utype</title></head>
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
	$file_in = "${upload_dir_unix}unique.${user_id}" ;
	open(SAVED,">$file_in") || &logerror("Error - could NOT open Output SAVED file: $file_in");
	$file_handle = $upload_file ;
	print SAVED <$file_handle> ;
	close SAVED;

    my ($sec, $min, $hr, $day, $month, $year, $wkdy, $yrdy, $isDST)=localtime();
    $month+=1; $year+=1900;
	open(LOG,">>/tmp/upload_exclusion_$month$day$year.log");
	print LOG "$hr:$sec - $user_id\n";
	open(SAVED,"<$file_in") || &logerror("Error - could NOT open Input SAVED file: $file_in");
	while (<SAVED>) 
	{
		chomp;                       # remove Carriage Return (if exists)
		$line = $_;
		$line =~ s///g ;      # remove ^M from Email Addr (if exists)
		$line =~ s/\t/|/g ;
		$line =~ s/,/|/g ;
		$line =~ s/  / /g ;
		print LOG "$hr:$sec - $user_id - <$line>\n";
		if ($utype eq "category")
		{
			($clientID,$uname,$clientType,$category,@rest_of_line) = split('\|', $line) ;
		}
		else
		{
			($clientID,$uname,$clientType,$aid,@rest_of_line) = split('\|', $line) ;
		}
		if ($CID->{$clientID})
		{
		}
		else
		{
			print "<tr><td><font color=red>Exclusion not added because couldnt find Client ID $clientID</font></td></tr>\n";
			next;
		}
		if ($utype eq "category")
		{
			$sql="delete from client_category_exclusion where client_id=$clientID";
		}
		else
		{
			$sql="delete from client_advertiser_exclusion where client_id=$clientID";
		}
		$rows=$dbhu->do($sql);
		#
		if ($utype eq "category")
		{
			my @C=split(':',$category);
			foreach my $c1 (@C)
			{
				if ($c1 eq "")
				{
					next;
				}
				if ($CAT->{$c1})
				{
					$sql="insert ignore into client_category_exclusion(client_id,category_id) values($clientID,$CAT->{$c1})";
					$rows=$dbhu->do($sql);
				}
				else
				{
					print "<tr><td><font color=red>Category <b>$c1</b> not added to Client <b>$clientID</b> because couldnt find Category</font></td></tr>\n";
				}
			}
		}
		else
		{
			my @C=split(':',$aid);
			foreach my $c1 (@C)
			{
				if ($c1 eq "")
				{
					next;
				}
				if ($ADV->{$c1})
				{
					$sql="insert ignore into client_advertiser_exclusion(client_id,advertiser_id) values($clientID,$c1)";
					$rows=$dbhu->do($sql);
				}
				else
				{
					print "<tr><td><font color=red>Advertiser <b>$c1</b> not added to Client <b>$clientID</b> because couldnt find Advertiser</font></td></tr>\n";
				}
			}
		}
	} 
	close SAVED;
	close LOG;
	unlink($file_in) || &logerror("Error - could NOT Remove file: $file_in");  # del file_in
	print<<"end_of_html";
</table>
<br><a href="client_exclusion.cgi">Back to Exclusion List</a>
</center>
</body>
</html>
end_of_html
} # end of sub

