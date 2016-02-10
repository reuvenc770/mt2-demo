#!/usr/bin/perl
use strict;
use Sys::Hostname;
use Getopt::Long;
use Lib::Util::ExecutionLock;
use Data::Dumper;
use util;
use File::Path;
use Net::Domain::TLD qw(tlds);
use DBI;
	
my $util = util->new;
	
my $dbh;
my $dbh1;
my $rows;
my $PROFDOMAIN={};
my $CLIENTTYPES={};
my $CLIENTS={};
my $LIST={};
my $LISTCNT={};
my $cnt;
my $username;
	
#get list of known valid TLDs
my $tldList = tlds;
	
#do not buffer output
$| = 1;
	
#get parameters
my $userParams = setParameters();
	
#set lock type
my $type = 'sv_general';
	
#get lock on file
my $lock = getLock($type);
		
#lock granted so proceed
if( $lock->lockStatusGranted() && $lock->hasNoErrors() ) {
	
	# connect to the util database
	#$util->db_connect();
	#$dbh = $util->get_dbh;

	my $handles = getDBHandles();
	
	$dbh  = $handles->{'write'};
	$dbh1 = $handles->{'query'};
			
	#show help 
	if ($userParams->{'help'}) {
		displayUsage();
	} #end if
		
	#run script
	else{
		
		my $host = hostname();	
		my $cdate = localtime();
		
		#get email class info
		my $emailClass = getClassNames();
		
		#set run type (by default get all records)
		my $record_db = 'overall_record_db';
		my $update_fld = 'overall_updated';
		
		my $list_type = 'all';
		
		print "Starting at $cdate\n";
									
		my $sql = "select id from server_config where name = ?";
		my $sth = $dbh->prepare($sql);
		$sth->execute($host);
		my ($server_id) = $sth->fetchrow_array();
		$sth->finish();
			
		my $checkAolFlag = 0;
			
		#get list of clients whose overall record db is set to this server
		$sql = qq|select user_id,username from user where $record_db = $server_id and status='A' and user_id = 276 order by $update_fld|;

		$sth = $dbh->prepare($sql);
		$sth->execute();
		
		my $userData = $sth->fetchall_arrayref();
				
		#loop through each client
		for my $row ( @$userData ) {
			
			my $urltotal={};
			my $client_id     = $row->[0];
			$username = $row->[1];
			$LIST={};
			$LISTCNT={};
			my $profile_query = getProfileInfoQuery($client_id);
			my $profile_sth = $dbh1->prepare($profile_query);
			$profile_sth->execute();
				
			if ( my $data = $profile_sth->fetchrow_hashref()) {
				my $client_str=getProfileClients($data->{'profile_id'});
							
				getProfileDomains($data->{'profile_id'});
					
				$data->{'FLATFILE_SRC'}  = $ENV{'FLATFILE_SRC'};
				$data->{'FLATFILE_DEST'} = $ENV{'FLATFILE_DEST'};		
				$data->{'client_id'} 	 = $client_id;
				$data->{'emailClass'}    = $emailClass;
				$data->{'type'}		     = $type;
					
				#set list type
				$data->{'list_type'} = $list_type;
				
				#get list id and name, etc to build email files
				#getDupe($client_str,$data);
				$urltotal = getListInfo($data,$urltotal,$client_str);
					
			} #end while
				
			$sql="update user set overall_updated=now() where user_id=$client_id";
			unless ($dbh && $dbh->ping) {
				my $handles = getDBHandles({'write' => 1});
				$dbh = $handles->{'write'};
   			} #end unless
   			
   			print "$sql \n";
			
			$rows=$dbh->do($sql);
			
			if ($userParams->{'newest'}) 
			{
			}
			{
				my $url;
				my $reccnt;
				foreach (keys %{$urltotal})
				{
					$url=$_;
					$reccnt=$urltotal->{$url};
					$sql="update source_url set reccnt=$reccnt where client_id=$client_id and url='$url'";
					unless ($dbh && $dbh->ping) {
			
						my $handles = getDBHandles({'write' => 1});
			
						$dbh = $handles->{'write'};
						#$util->db_connect();
						#$dbh = $util->get_dbh;
   					} #end unless
					$rows=$dbh->do($sql);
					print "<$sql>\n";
				}
			}
		} #end for loop
			
		$sth->finish();
	
	} #end else
		
	$dbh->disconnect();
	$dbh1->disconnect();

	#release file lock
	releaseLock($lock);

} #end if

#locking errors
else {
  if($lock->hasErrors) {
	print "Lockfile has errors: ", Dumper($lock->getErrors());
	}
} #end else

exit(0);

sub cleanData {

	my ($emailAddress) = @_;

	#get count to know if the email address should be updated
	my $count = 0;

	$count  = $emailAddress =~ s/ [^ \w \@ \. \- ] //xg;
	$count += $emailAddress =~ s/ \s+ //xg;
	$count += $emailAddress =~ s/ ' / '' /xg;
	$count += $emailAddress =~ s/ [ 0-9 ]+$ //xg;
	$count += $emailAddress =~ s/ [^ \w ]$ //xg;
	
	#fix bad email domains
	my ($fixedEmailAddress, $domainCount) = fixEmailDomain($emailAddress);
	
	$count += $domainCount;
	
	return($fixedEmailAddress, $count);	
	
}

sub fixEmailDomain {
	
	my ($emailAddress) = @_;
	
	my %emailReplacements = (
	'@aaol.com'		  => '@aol.com',
    '.com.com'        => '.com',
	'@ail.com'	=> '@aol.com',
	'@aim.com'	=> '@aol.com',
	'@al.com'	=> '@aol.com',
	'@alo.com'	=> '@aol.com',
	'@ao.com'	=> '@aol.com',
	'@aoi.com'	=> '@aol.com',
	'@aolo.com'	=> '@aol.com',
	'@aool.com'	=> '@aol.com',
	'@aql.com'	=> '@aol.com',
	'@arthlink.com'	=> '@earthlink.com',
	'@awol.com'	=> '@aol.com',
	'ahoo.com'	=> 'yahoo.com',
	'ayahoo.com'	=> 'yahoo.com',
	'ayhoo.com'	=> 'yahoo.com',
	'gmil.com'	=> 'gmail.com',
	'gotmail.com'	=> 'hotmail.com',
	'hahoo.com'	=> 'yahoo.com',
	'hatmail.com'	=> 'hotmail.com',
	'hiotmail.com'	=> 'hotmail.com',
	'hjotmail.com'	=> 'hotmail.com',
	'hormail.com'	=> 'hotmail.com',
	'hotmaiil.com'	=> 'hotmail.com',
	'hotmaik.com'	=> 'hotmail.com',
	'hotmailk.com'	=> 'hotmail.com',
	'hotmal.com'	=> 'hotmail.com',
	'hotmasil.com'	=> 'hotmail.com',
	'hotmial.com'	=> 'hotmail.com',
	'hotmnail.com'	=> 'hotmail.com',
	'hotmsil.com'	=> 'hotmail.com',
	'hotnail.com'	=> 'hotmail.com',
	'hotymail.com'	=> 'hotmail.com',
	'hoymail.com'	=> 'hotmail.com',
	'htmail.com'	=> 'hotmail.com',
	'hyahoo.com'	=> 'yahoo.com',
	'jyahoo.com'	=> 'yahoo.com',
	'lyahoo.com'	=> 'yahoo.com',
	'mn.rr.com'	=> 'yahoo.com',
	'sbcglobal.com'	=> 'sbcglobal.net',
	'tyahoo.com'	=> 'yahoo.com',
	'www.yahoo.com'	=> 'yahoo.com',
	'wyahoo.com'	=> 'yahoo.com',
	'yaahoo.com'	=> 'yahoo.com',
	'yaghoo.com'	=> 'yahoo.com',
	'yah00.com'	=> 'yahoo.com',
	'yahaoo.com'	=> 'yahoo.com',
	'yahgoo.com'	=> 'yahoo.com',
	'yahh.com'	=> 'yahoo.com',
	'yahho.com'	=> 'yahoo.com',
	'yahhoo.com'	=> 'yahoo.com',
	'yahioo.com'	=> 'yahoo.com',
	'yahjoo.com'	=> 'yahoo.com',
	'yaho0.com'	=> 'yahoo.com',
	'yahoo.com>'	=> 'yahoo.com',
	'yahoo.net'	=> 'yahoo.com',
	'yahoo1.com'	=> 'yahoo.com',
	'yahooh.com'	=> 'yahoo.com',
	'yahool.com'	=> 'yahoo.com',
	'yahoom.com'	=> 'yahoo.com',
	'yahoomail.com'	=> 'yahoo.com',
	'yahooo.com'	=> 'yahoo.com',
	'yahoop.com'	=> 'yahoo.com',
	'yahopo.com'	=> 'yahoo.com',
	'yajoo.com'	=> 'yahoo.com',
	'yanoo.com'	=> 'yahoo.com',
	'yaoo.com'	=> 'yahoo.com',
	'yaooh.com'	=> 'yahoo.com',
	'yaqhoo.com'	=> 'yahoo.com',
	'yashoo.com'	=> 'yahoo.com',
	'yayoo.com'	=> 'yahoo.com',
	'yhaoo.com'	=> 'yahoo.com',
	'yhoo.com'	=> 'yahoo.com',
	'yhool.com'	=> 'yahoo.com',
	'yhyahoo.com'	=> 'yahoo.com',
	'yohoo.com'	=> 'yahoo.com',
	'yqhoo.com'	=> 'yahoo.com',
	'yqyahoo.com'	=> 'yahoo.com',
	'yshoo.com'	=> 'yahoo.com',
	'yyahoo.com'	=> 'yahoo.com',
    'yaho.com'  => 'yahoo.com',
    'gmail.info'    => 'gmail.com',
    'hotmil.com'    => 'hotmail.com',
    'hotmail.net'   => 'hotmail.com',
                         );
                         
	my $string = '((' . join(')|(\b', (keys(%emailReplacements))) . '\b))';

	my $substitutions   = qr{$string};

	#get count to know if the email address should be updated
	my $count = $emailAddress =~ s/$substitutions/$emailReplacements{$1}/;
	
	if($count > 0){
		print "$emailAddress was fixed \n";
	}
	
	return($emailAddress, $count);
	
}
	
sub setParameters {

	my $options = {};
		
    GetOptions (
    $options,
    
    'newest',
    'help'
    
    ); 
    
    return($options);

}

sub displayUsage {

	print qq|
Usage: $0 [options]

Options:

Only one of the following:
	--newest		Gets newest records list
	--help			Display this usage message.

|;

}

sub getDays {
	
	my ($day_flag) = @_;
	
	my $wait_days = 0;
	my $days = 0;
	

	if ($day_flag eq "Y") {
		$days = 60 + $wait_days;
	}
	
	elsif ($day_flag eq "7") {
		$days = 7 + $wait_days;
	}
	
	elsif ($day_flag eq "F") {
		$days = 15 + $wait_days;
	}
	
	elsif ($day_flag eq "M") {
		$days = 30 + $wait_days;
	}
	
	elsif ($day_flag eq "9") {
		$days = 90 + $wait_days;
	}
	
	elsif ($day_flag eq "3") {
		$days = 120 + $wait_days;
	}
	
	elsif ($day_flag eq "5") {
		$days = 150 + $wait_days;
	}
	
	elsif ($day_flag eq "O") {
		$days = 180 + $wait_days;
	}	

	return($days);
	
}

sub getClassNames {
			
	my $classSql = qq|

	SELECT
		ec.class_name, 
		ed.domain_id
	FROM 
		email_class ec,
		email_domains ed
	WHERE 
		ec.class_id = ed.domain_class
	AND
		ec.class_id != 4 and ed.suppressed=0
		
	|;

	unless ($dbh1 && $dbh1->ping) {
		
		my $handles = getDBHandles({'query' => 1});
			
		$dbh1 = $handles->{'query'};
		
		#$util->db_connect1();
		#$dbh1 = $util->get_dbh1;
   	} #end unless
	
	my $sth = $dbh1->prepare($classSql);
	$sth->execute();
	
	my $emailClass = {};
	
	while ( my $data = $sth->fetchrow_hashref()) {
		
		#$emailClass->{$data->{'domain_id'}} = lc($data->{'class_name'});
		
		my $className = lc($data->{'class_name'});
		
		push(@{$emailClass->{$className}}, $data->{'domain_id'});
		push(@{$emailClass->{'others'}}, $data->{'domain_id'});
		
	} #end while

	return($emailClass);
		
}
	
sub process_list  {
	
	my ($data,$urltotal,$client_str) = @_;
	
	my $list_cnt = 0;
	my $aol_cnt=0;
	my $hotmail_cnt=0;
	my $yahoo_cnt=0;
	my $msn_cnt=0;
	
	my $class_name = lc($data->{'class_name'});
	my $list_id    = $data->{'list_id'};
	my $client_id  = $data->{'client_id'};
	my $runType    = $data->{'type'};
	my $emailList  = $data->{'email_list'} || 'sv_email_list';
	my $dupes_flag		= $data->{'dupes_flag'};
	
	
	my $FLATFILE_SRC  = $data->{'FLATFILE_SRC'};
	my $FLATFILE_DEST = $data->{'FLATFILE_DEST'};
	
	#hash of email classes not including 'Others'
	my $emailClass = $data->{'emailClass'};

	my $sourceDirectory 	 = "$FLATFILE_SRC/temp/$runType/$client_id";
	my $flatfile_destination = "$FLATFILE_DEST/${client_id}/";
					
	my ($email_sql, $errnum, $errmsg, $sth);
		
	foreach my $domainClassName (keys %{$emailClass}){
		
		makeDirectory($sourceDirectory);
		
		my $flatfile_source = "$sourceDirectory/${list_id}_$domainClassName.dat";
		
		#create flatfile by default for each list / class combo
		system("touch $flatfile_source");
		
		if ($PROFDOMAIN->{$domainClassName})
		{
		}
		else
		{
			print "Skipping $domainClassName\n";
			next;
		}
		my $emailExists = {};
		
		my $domainIDs = join(',',  @{$emailClass->{$domainClassName}});
		
		my $notCondition = '';
		
		#get all domain ids except the set classes (ie AOL, Yahoo, etc)
		if ($domainClassName eq 'others'){
			$notCondition = 'not';
		} #end if
		
		my $domainIDsClause = "domain_id $notCondition in ($domainIDs)";
			
		if ($domainClassName eq 'others')
		{
			$email_sql = qq|
					
			SELECT 
				email_addr,
				email_user_id,
				state,
				first_name,
				last_name,
				city,
				zip,
				e.domain_id,
				datediff(curdate(),capture_date) as subscribe_date,
				source_url,
				capture_date,
				member_source,
				subscribe_date as sdate,
				datediff(curdate(),open_date) as open_date, 
				datediff(curdate(),click_date) as click_date
			FROM 
				$emailList e, email_domains ed
			WHERE 
				list_id = $list_id
			AND 
				status = 'A'
			AND 
				e.domain_id=ed.domain_id and ed.suppressed=0 and ed.domain_class=4	
				
			|; 
		}
		else
		{
			$email_sql = qq|
					
			SELECT 
				email_addr,
				email_user_id,
				state,
				first_name,
				last_name,
				city,
				zip,
				domain_id,
				datediff(curdate(),capture_date) as subscribe_date,
				source_url,
				capture_date,
				member_source,
				subscribe_date as sdate,
				datediff(curdate(),open_date) as open_date, 
				datediff(curdate(),click_date) as click_date
			FROM 
				$emailList e
			FORCE  
				INDEX (list_id_idx)
			WHERE 
				list_id = $list_id
			AND 
				status = 'A'
			AND 
				$domainIDsClause
				
			|; 
		}
		if (($data->{'start_date'} ne '') && ($data->{'start_date'} ne '0000-00-00'))
		{
			$email_sql=$email_sql . " and capture_date >= '".$data->{'start_date'}."'";
			if (($data->{'end_date'} ne '') && ($data->{'end_date'} ne '0000-00-00'))
			{
				$email_sql=$email_sql . " and capture_date <= '".$data->{'end_date'}."'";
			}
		}
		else
		{
			$email_sql=$email_sql . " and capture_date <= date_sub(curdate(),interval ".$data->{'start_day'}." day) and capture_date >= date_sub(curdate(),interval ".$data->{'end_day'}. " day)";;
		}
					
		unless ($dbh && $dbh->ping) {
			
			my $handles = getDBHandles({'write ' => 1});
			
			$dbh = $handles->{'write'};
		}
				
		print "$email_sql \n";
				
		$sth = $dbh->prepare($email_sql);
		$errmsg = $dbh->errstr();
		print "Prepare <$errmsg>\n";
				
		#$sth->{mysql_use_result} = 1; 
		$sth->execute();
		$errmsg = $dbh->errstr();
				
		print "Execute for class($domainClassName) : client($client_id) : list($list_id) - <$errmsg>\n";
							
		## If there are no more rows or if an error occurs, then fetchrow_hashref returns an undef. 
		## You should check $sth->err afterwards to discover if the undef returned was due to an error.
		while (my $userInfo = $sth->fetchrow_hashref()) {
					
			## if error, this will ot be run		
			$errnum = $sth->err();
			if ($errnum)
			{
				$errmsg = $sth->errstr();
				print "DB fetch error occurred: <$errnum - $errmsg>\n";
			}		
					
			$errnum = $dbh->err();
			if ($errnum)
			{
				$errmsg = $dbh->errstr();
				print "DB fetch error occurred: <$errnum - $errmsg>\n";
	
			}		
					
			$userInfo->{'email_list'} = $emailList;
			$userInfo->{'list_id'}    = $list_id;

			#check to see if email exists in list
#			if($emailExists->{$userInfo->{'email_addr'}}){
#				deleteEmail($userInfo);	
#			} #end if
		
			#check if the TLD is valid
			if(!validateTLD($userInfo->{'email_addr'})){
				unsubscribeEmail($userInfo);
			} #end elsif
			
			else {
						
				$userInfo->{'first_name'} =~ s/[\000-\037]//g;
				$userInfo->{'last_name'}  =~ s/[\000-\037]//g;
				$userInfo->{'city'}       =~ s/[\000-\037]//g;
				$userInfo->{'state'}      =~ s/[\000-\037]//g;
				$userInfo->{'source_url'} =~ s/\s+|^M//g;
									
				my $substitution_count = 0;
						
				#get rid of any bad characters in email
				($userInfo->{'email_addr'}, $substitution_count) = cleanData($userInfo->{'email_addr'});
						
				#if count is greater than 0 then the email address was cleaned so update it
				if ($substitution_count > 0) {
					updateEmailList($userInfo);
				} #end if
						
				#set email class name for file
				#my $emailClassName = $emailClass->{$userInfo->{'domain_id'}} || 'others';
								
				open(FLATFILE, ">>$flatfile_source");
				
				my $data = qq($userInfo->{'email_addr'}|$userInfo->{'email_user_id'}|$userInfo->{'state'}|$userInfo->{'first_name'}|$userInfo->{'last_name'}|$userInfo->{'city'}|$userInfo->{'zip'}|$userInfo->{'subscribe_date'}|$userInfo->{'source_url'}|$userInfo->{'capture_date'}|$userInfo->{'member_source'}|$userInfo->{'open_date'}|$userInfo->{'click_date'}|);
			
				$data =~ s/\r\n//g;
				
				print FLATFILE "$data\n";
						
				close(FLATFILE);
						
				if ($userInfo->{'source_url'} ne "")
				{
					$urltotal->{$userInfo->{'source_url'}}++;
				}
				$list_cnt++;
				if ($domainClassName eq "aol")
				{
					$aol_cnt++;
				}
				elsif ($domainClassName eq "hotmail")
				{
					$hotmail_cnt++;
				}
				elsif ($domainClassName eq "yahoo")
				{
					$yahoo_cnt++;
				}
						
				#add email to hash to check later
#				$emailExists->{$userInfo->{'email_addr'}} = $userInfo->{'email_user_id'};
					
			} #end else
				
		} #end while	
		
	} #end domain class foreach
							
	$errnum = $dbh1->err();
	if ($errnum)
	{
		$errmsg = $dbh1->errstr();
		print "DB error occurred: <$errnum - $errmsg>\n";
	}
	
	$errmsg = $dbh1->errstr();
			
#	$sth->finish();

	#print "End of Fetch for $client_id - $list_id \n";

	unless ($dbh && $dbh->ping) {
		my $handles = getDBHandles({'write' => 1});
			
		$dbh = $handles->{'write'};
		#$util->db_connect();
		#$dbh = $util->get_dbh;
   } #end unless
   my $sql="update list set member_cnt=$list_cnt,aol_cnt=$aol_cnt,hotmail_cnt=$hotmail_cnt,msn_cnt=$msn_cnt,yahoo_cnt=$yahoo_cnt,foreign_cnt=0 where list_id=$list_id";
   print "$sql \n";
   $rows=$dbh->do($sql);
	if (($LISTCNT->{$list_id}) and ($list_cnt >= 50))
	{
		my $diffcnt=(($list_cnt-$LISTCNT->{$list_id})/$LISTCNT->{$list_id})*100;
		if (($diffcnt < -25) or ($diffcnt >25))
		{
		    open (MAIL,"| /usr/sbin/sendmail -t");
		    my $from_addr = "List Size Changed <info\@spirevision.com>";
    		print MAIL "From: $from_addr\n";
    		print MAIL "To: mailops\@spirevision.com, sysadmin\@spirevision.com, jsobeck\@spirevision.com,jbutler\@spirevision.com \n";
			if ($type eq "newest")
			{
    			print MAIL "Subject: Newest Record List - Size for list $list_id for Client $username Changed\n";
			}
			else
			{
    			print MAIL "Subject: Size for list $list_id for Client $username Changed\n";
			}
    		my $date_str = $util->date(6,6);
    		print MAIL "Date: $date_str\n";
    		print MAIL "X-Priority: 1\n";
    		print MAIL "X-MSMail-Priority: High\n";
    		print MAIL "List changed by $diffcnt - from $LISTCNT->{$list_id} to $list_cnt\n\n";
    		close MAIL;

		}
	}
	
	my $cdate = localtime();
	print "Finished list Client $client_id List $list_id ($list_cnt) at $cdate\n";
	
	# scp the files to FLATFILE_DEST_SERVER 
	my $host = hostname();	
	my $dest_host  = $ENV{'FLATFILE_DEST_SERVER'};
		
	if ($host ne $dest_host) {
   		run("/usr/bin/scp -qP 22 $sourceDirectory/*.dat $dest_host:$flatfile_destination");
   		system("rm -rf $sourceDirectory/*.dat");
	} #end if
		
	else{		
		system("mv $sourceDirectory/*.dat $flatfile_destination");
	}

	#system("mv $sourceDirectory/*.dat $flatfile_destination");
	#rename("$flatfile_source", "$flatfile_destination");
	return $urltotal;

}
	
sub makeDirectory {
	
	my ($directory) = @_;

	#make directory if it doesnt exist
	unless(-d $directory){
		mkpath( $directory, {verbose => 1} );
	} #end unless	
	
}

sub deleteEmail {

	my ($userInfo) = @_;

	my $update_query = qq|
		
	DELETE FROM
		$userInfo->{'email_list'}
	WHERE 
		email_user_id = $userInfo->{'email_user_id'}
	
	|;	
	
	unless ($dbh && $dbh->ping) {
		my $handles = getDBHandles({'write' => 1});
			
		$dbh = $handles->{'write'};
		#$util->db_connect();
		#$dbh = $util->get_dbh;
   	} #end unless
	
	$dbh->do($update_query);
	
	
}
	
sub unsubscribeEmail {

	my ($userInfo) = @_;

	my $update_query = qq|
		
	UPDATE
		$userInfo->{'email_list'}
	SET 
		status = 'U',
		unsubscribe_date = NOW(),
		unsubscribe_time = NOW()
	WHERE 
		email_user_id = $userInfo->{'email_user_id'}
	
	|;	
	
	unless ($dbh && $dbh->ping) {
		my $handles = getDBHandles({'write' => 1});
			
		$dbh = $handles->{'write'};
		#$util->db_connect();
		#$dbh = $util->get_dbh;
   	} #end unless
	
	$dbh->do($update_query);
	
	
}

sub updateEmailList {

	my ($userInfo) = @_;

	my $update_query = qq|
		
	UPDATE
		$userInfo->{'email_list'}
	SET 
		email_addr = "$userInfo->{'email_addr'}"
	WHERE 
		email_user_id = $userInfo->{'email_user_id'}
	
	|;	
	
	unless ($dbh && $dbh->ping) {
		my $handles = getDBHandles({'write' => 1});
			
		$dbh = $handles->{'write'};
		#$util->db_connect();
		#$dbh = $util->get_dbh;
   	} #end unless
	
	$dbh->do($update_query);
	
}

sub moveRecord
{
	my ($client_id,$eid,$cdate,$sdate)=@_;
 	if ($cdate eq "0000-00-00 00:00:00")
 	{
 		$cdate=$sdate;
    }
    my $list_id = get_list_id($cdate,$client_id);
	if ($list_id == 0)
	{
	}
	else
	{
    	my $sql = "update email_list set subscribe_date=curdate(),subscribe_time=curtime(),list_id=$list_id where email_user_id=$eid and status in ('A','P')";
		unless ($dbh && $dbh->ping) {
			my $handles = getDBHandles({'write' => 1});
			
			$dbh = $handles->{'write'};
			#$util->db_connect();
			#$dbh = $util->get_dbh;
   		} #end unless
	
		$dbh->do($sql);
		print "Moving $eid <$cdate> <$sdate> to $list_id\n";
	}
}
	
sub get_list_id
{
	my ($date_str,$client_id)=@_;
    my $list_id=0;
	my $cyear;
	my $cmon;
	my $current_list;
    my ($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst);
	my $temp_str;
	my $list_str;
	my $rest_str;

    ($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime();
    $cyear = $cyear + 1900;
    $cmon = $cmon + 1;
    if (($cmon < 10) && (length($cmon) == 1))
    {
    	$current_list= $cyear . "-0" . $cmon;
    }
    else
    {
    	$current_list= $cyear . "-" . $cmon;
    }
    if (($date_str eq "") || ($date_str eq "0000-00-00"))
    {
        ($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime();
        $year = $year + 1900;
        $mon = $mon + 1;
    }
    else
    {
        ($temp_str,$rest_str) = split(" ",$date_str,2);
        ($year,$mon,$mday) = split("-",$temp_str,3);
    }
    if ($year < 2005)
    {
         if (($client_id == 2) && ($year == 2004))
         {
            $list_str = $year . "-" . $mon;
         }
         else
         {
         	$list_str = $year;
         }
    }
    else
    {
        if (($mon < 10) && (length($mon) == 1))
        {
            $list_str = $year . "-0" . $mon;
        }
        else
        {
            $list_str = $year . "-" . $mon;
        }
    }
	if ($LIST->{$list_str})
	{
		return $LIST->{$list_str};
	}
	else
	{
		return $LIST->{$current_list};
	}
}

sub run {
	
    my ($command) = @_;
    
    print "$command \n";
    system("$command"); 
    
}

sub getMinMaxEmailUserIDs {

	my ($listID, $emailList) = @_;
	
	my $maxMinQuery = qq|
		
		SELECT
			min(email_user_id) as minID,
			max(email_user_id) as maxID
		FROM 
			$emailList
		WHERE 
			list_id = $listID
		AND 
			status = 'A' 
	|;
	
	unless ($dbh1 && $dbh1->ping) {
		my $handles = getDBHandles({'query' => 1});
			
		$dbh1 = $handles->{'query'};
		#$util->db_connect1();
		#$dbh1 = $util->get_dbh1;
   	} #end unless
	
	my $sth = $dbh1->prepare($maxMinQuery);
	$sth->execute();
	
	my $data = $sth->fetchrow_hashref();
	
	#set default in case some lists have no records
	$data->{'minID'} ||= 0;
	$data->{'maxID'} ||= 0;

	return($data->{'minID'}, $data->{'maxID'});
	
}

sub getDomainsIDs {

	my ($data) = @_;
	
	my $domainsIDs = [];
	
	my $class_id   	 = $data->{'class_id'};
	my $class_name 	 = $data->{'class_name'};

	my $domain_sql = "select domain_id from email_domains where domain_class = $class_id and suppressed=0";
	
	#for others get all domain ids other than domain_id 4 
	#the list of domain ids is only about 30 versus thousands
	if($class_name eq 'Others') {
		
		$domain_sql = qq|
		
		SELECT 
			domain_id 
		FROM 
			email_domains 
		WHERE 
			domain_class <> 4	and suppressed=0
		|;

	} #end if
	
	#print "$domain_sql \n";

	unless ($dbh1 && $dbh1->ping) {

		my $handles = getDBHandles({'query' => 1});
			
		$dbh1 = $handles->{'query'};
		print "connecting\n";
		#$util->db_connect1();
		#$dbh1 = $util->get_dbh1;
	}
	
	my $sth = $dbh1->prepare($domain_sql);
	$sth->execute();
	
	while ( my $domain_id = $sth->fetchrow_array()) {
		
		push (@$domainsIDs, $domain_id);

	} #end while
	
	return($domainsIDs);
	
}

sub getListInfo {
	
	my ($data,$urltotal,$client_str) = @_;

	my $open_click_flag = $data->{'open_click_flag'};
	my $profile_id 		= $data->{'profile_id'};
	my $list_type 		= $data->{'list_type'};
	
	#set condition of whether or not to get newest records (off by default)
	my $equal = "!=";
	
	if ($list_type eq 'newest') {
		$equal = "=";
	} #end if

	my $list_sql = qq|
	
	SELECT 
		list_profile_list.list_id,
		list_type,
		list_name 
	FROM 
		list_profile_list,list 
	WHERE 
		profile_id = $profile_id 
	AND 
		list_profile_list.list_id = list.list_id 
	AND 
		list.list_name $equal 'Newest Records' 
	AND 
		list.status = 'A'
	|;
	
	#print "$list_sql \n";
 
	unless ($dbh1 && $dbh1->ping) {
		print "connecting\n";
		my $handles = getDBHandles({'query' => 1});
			
		$dbh1 = $handles->{'query'};
		#$util->db_connect1();
		#$dbh1 = $util->get_dbh1;
	}

	my $sth1a = $dbh1->prepare($list_sql);
	$sth1a->execute();
	
	while (my ($list_id, $list_type, $list_name) = $sth1a->fetchrow_array()) {
		
		#reassign data so we can temporarily change values if needed
		my %list_data = %{$data};
		
		if (($list_type eq 'OPEN') || ($list_type eq 'CLICK')) {
			if ($open_click_flag eq "Y") {
				$list_data{'day_flag'} = "N";
			} #end if
		} #end if
		
		if ($list_name eq "DM Seeds") {
			$list_data{'day_flag'} = "N";
		} #end if
		
		#get emails from international table
		if($list_type eq 'INTERNATIONAL'){
			$list_data{'email_list'} = 'international_email_list'
		} #end if
			
		$list_data{'list_id'}  = $list_id;

		#process list info if the status allows it
		if(checkFlatFileStatus()){
			$urltotal = process_list(\%list_data,$urltotal,$client_str);
		}
		
	} #end while
	return $urltotal;
}

sub checkFlatFileStatus {

	my $host = hostname();	
	
	my $sql = "select flatFileStatus from server_config where name = ?";
	my $sth = $dbh->prepare($sql);
	$sth->execute($host);
	my ($flatFileStatus) = $sth->fetchrow_array();
	$sth->finish();	

	return($flatFileStatus);
	
}

sub getProfileInfoQuery {
	
	my ($client_id) = @_;

	my $profile_query = qq|
	
	SELECT profile_id, open_clickers_ignore_date as open_click_flag, dupes_flag, start_day, end_day,start_date,end_date,opener_start,opener_end,clicker_start,clicker_end FROM list_profile WHERE master = 'Y' AND client_id = $client_id AND status='A' |;
	return($profile_query);
	
}

sub getProfileDomains
{
	my ($profile_id)=@_;
	my $cname;

	my $sql="select class_name from email_class ec,list_profile_domain lpd where lpd.profile_id=? and lpd.domain_id=ec.class_id";
	my $sth=$dbh1->prepare($sql);
	$sth->execute($profile_id);
	while (($cname)=$sth->fetchrow_array())
	{
		$cname = lc($cname);
		$PROFDOMAIN->{$cname}=1;
	}
	$sth->finish();
}
sub getProfileClients
{
	my ($profile_id)=@_;
	my $userid;
	my $client_str;

	my $sql="select distinct user_id from ListProfileClientType, user where profile_id=? and ListProfileClientType.client_type=user.client_type and user.status='A' UNION select distinct client_id from ListProfileClient where profile_id=?"; 
	my $sth=$dbh1->prepare($sql);
	$sth->execute($profile_id,$profile_id);
	$client_str="";
	while (($userid)=$sth->fetchrow_array())
	{
		$client_str=$client_str.$userid.",";
	}
	$sth->finish();
	chop($client_str);
	return $client_str;
}

sub getLockFileName {
	
	my ($type) = @_;

	my $lockFileName = '/tmp/get_data_' . $type . '.pid';
	
	print "$lockFileName \n";

	return($lockFileName);

}

sub getLock {
	
	my ($type) = @_;

	my $lockFilePath = getLockFileName($type);

	my $lock = Lib::Util::ExecutionLock->new('executionLockFile' => $lockFilePath);

	$lock->getExclusiveFileSystemExecutionLock({'removeStaleLocks' => 1});

	return($lock);
}

sub releaseLock {

	my ($lock) = @_;

	#$lock->releaseExclusiveFileSystemExecutionLock();
	
	if($lock->lockStatusGranted() && $lock->hasNoErrors()) {
		$lock->releaseExclusiveFileSystemExecutionLock();
		print "Releasing file lock successfully \n";
	} #end if
	
	else {
		print "Lock not released, lock never granted";
	} #end else


}	

sub validateTLD {
	
	my ($emailAddress) = @_;

	my ($address, $domain) = split('@', $emailAddress); 
	
	my(@domainData) = split(/\./, $domain);
	
	my $tld = pop(@domainData);
	
	$tld =~ tr/A-Z/a-z/;
		
	return($tldList->{$tld});	
	
}

sub getQueryDBHandles {
	
	my ($database) = @_;

	return(DBI->connect("DBI:mysql:new_mail:$database", 'db_user', 'sp1r3V'));
	
}

sub getWriteDBHandles {
	
	my $dbhWrite = DBI->connect('DBI:mysql:new_mail:sv-db.routename.com', 'db_user', 'sp1r3V');
	
	my $hostname = hostname();
	
	my $dbQuery = qq|
	
	SELECT 
		sc1.name as serverName
	FROM 
		server_config sc
	LEFT JOIN
		server_config sc1
	ON
		sc.flatFileQueryDB = sc1.id
	WHERE
		sc.name = '$hostname';
	
	|;

	my $sth = $dbhWrite->prepare($dbQuery);
	$sth->execute;
	
	my $data = $sth->fetchrow_hashref();
	
	return($dbhWrite, $data->{'serverName'});
	
}

sub getDBHandles {
	
	my ($params) = @_;
	
	my $handles = {};
	
	my ($dbhWrite, $database) = getWriteDBHandles();
	
	my $dbhQuery = getQueryDBHandles($database);
	
	#get both db handles
	if(!$params->{'query'} && !$params->{'write'}){
		$handles->{'write'} = $dbhWrite;
		$handles->{'query'} = $dbhQuery;
	} #end if
	
	#only return query handle and disconnect new connection to master
	elsif($params->{'query'}){
		$handles->{'query'} = $dbhQuery;
		$dbhWrite->disconnect();
	} #end else if
	
	#only return write handle
	else {
		$handles->{'write'} = $dbhWrite;	
	} #end else
	
	return($handles);
	
}
