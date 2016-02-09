#!/usr/bin/perl
#===============================================================================
# File   : upload_triggers.cgi 
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
my $sth;
my $cnt;
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

#----- Pass control to PROCESS_FILE  or  PROCESS_LIST  -------
if ( $upload_file ne "" ) 
{
	&process_file() ;
}
else
{
	print "Location: new_category_trigger_list.cgi\n\n";
}
exit(0) ;


sub process_file 
{
	my ($file_name, $file_handle, $file_problem, $file_in, $line, @rest_of_line);
	my $upload_dir_unix;
	my ($ctype,$catid,$usa_id,$orderID);
	my $client_id;
	my $aname;
	my $cname;
	my $old_usa_id;
	my $campaign_id;
	my $aid;
	my $creative_id;
	my $subject_id;
	my $from_id;

	print "Content-type: text/html\n\n";
	print<<"end_of_html";
<html><head><title>Category Trigger Upload Results</title></head>
<body>
<center>
<table>
end_of_html
	# get upload subdir
	$sql = "select parmval from sysparm where parmkey = 'UPLOAD_DIR_UNIX'";
	my $sth1 = $dbhq->prepare($sql) ;
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

	$file_in = "${upload_dir_unix}trigger.${user_id}" ;
	open(SAVED,">$file_in") || &logerror("Error - could NOT open Output SAVED file: $file_in");
	$file_handle = $upload_file ;
	print SAVED <$file_handle> ;
	close SAVED;

    my ($sec, $min, $hr, $day, $month, $year, $wkdy, $yrdy, $isDST)=localtime();
    $month+=1; $year+=1900;
	open(LOG,">>/tmp/upload_trigger_$month$day$year.log");
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
		($ctype,$cname,$aname,$orderID,$client_id,@rest_of_line) = split('\|', $line) ;
		if ($client_id eq "")
		{
			$client_id = 0;
		}
		if (($ctype ne "OPEN") and ($ctype ne "CLICK") and ($ctype ne "CONVERSION"))
		{
			print "<tr><td><font color=red>Trigger <$line> not added because invalid trigger type:  $ctype</font></td></tr>\n";
			next;
		}
		if ($orderID > 6)
		{
			print "<tr><td><font color=red>Trigger <$line> not added because trigger number too big:  $orderID</font></td></tr>\n";
			next;
		}

		$sql="select category_id from category_info where category_name=? and status='A'";
		$sth=$dbhu->prepare($sql);
		$sth->execute($cname);
		if (($catid)=$sth->fetchrow_array())
		{
			$sth->finish();
		}
		else
		{
			$sth->finish();
			print "<tr><td><font color=red>Trigger <$line> not added because couldn't find category id $cname</font></td></tr>\n";
			next;
		}
		$sql="select usa.usa_id,usa.advertiser_id,usa.creative_id,usa.subject_id,usa.from_id from UniqueScheduleAdvertiser usa,advertiser_info ai where usa.advertiser_id=ai.advertiser_id and ai.status!='I' and usa.name=?";
    	$sth=$dbhu->prepare($sql);
    	$sth->execute($aname);
    	if (($usa_id,$aid,$creative_id,$subject_id,$from_id)=$sth->fetchrow_array())
		{
		}
		else
		{
			print "<tr><td><font color=red>Trigger $line not added because couldn't find active Unique Advertiser $aname</font></td></tr>\n";
			next;
		}
    	$sth->finish();
		$sql="select usa_id,campaign_id from CategoryTrigger where category_id=? and trigger_type=? and orderID=? and client_id=?";
		$sth=$dbhu->prepare($sql);
		$sth->execute($catid,$ctype,$orderID,$client_id);
		if (($old_usa_id,$campaign_id)=$sth->fetchrow_array())
		{
			if ($old_usa_id != $usa_id)
			{
				$sql="update campaign set advertiser_id=$aid,creative1_id=$creative_id,subject1=$subject_id,from1=$from_id where campaign_id=$campaign_id";
				$rows=$dbhu->do($sql);
				$sql="update CategoryTrigger set usa_id=$usa_id where category_id=$catid and trigger_type='$ctype' and orderID=$orderID and client_id=$client_id";
				$rows=$dbhu->do($sql);
				print "<tr><td>$ctype Trigger $orderID Category $cname updated for Client $client_id</td></tr>\n";
			}
		}
		else
		{
			my $temp_str="$ctype Trigger - Category $catid - $orderID - $client_id";
			$sql = "insert into campaign(campaign_name,user_id,status,created_datetime,advertiser_id,creative1_id,subject1,from1,campaign_type) values('$temp_str',1,'T',now(),$aid,$creative_id,$subject_id,$from_id,'TRIGGER')";
			$rows=$dbhu->do($sql);
	
			$sql="select max(campaign_id) from campaign where campaign_name='$temp_str' and status='T'";
	    	$sth = $dbhq->prepare($sql);
	    	$sth->execute();
			($campaign_id)=$sth->fetchrow_array();
			$sth->finish();
			$sql="insert into CategoryTrigger(category_id,usa_id,trigger_type,campaign_id,orderID,client_id) values($catid,$usa_id,'$ctype',$campaign_id,$orderID,$client_id)";
			$rows=$dbhu->do($sql);
			print "<tr><td>Trigger $temp_str added </td></tr>\n";
		}
	} 
	close SAVED;
	close LOG;
	unlink($file_in) || &logerror("Error - could NOT Remove file: $file_in");  # del file_in
	print<<"end_of_html";
</table>
<br><a href="new_category_trigger_list.cgi">Back to Trigger List</a>
</center>
</body>
</html>
end_of_html
} # end of sub

