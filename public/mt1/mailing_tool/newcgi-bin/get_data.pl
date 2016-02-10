#!/usr/bin/perl
use strict;

use Sys::Hostname;
use Getopt::Long;
use Data::Dumper;
use File::Path;
use DBI;
use FileHandle;
use Mail::RFC822::Address qw(valid);
use Net::DNS;

use util;
use Lib::Database::Perl::Interface::GenderData;
use App::Mail::TrueKnowledgeApi;

## this is global for now (caching reasons)
my $tk = App::Mail::TrueKnowledgeApi->new();

##set options to check MX/A records
my $resolver;
#my $resolver = Net::DNS::Resolver->new();
#$resolver->tcp_timeout(5);
#$resolver->udp_timeout(5);
#$resolver->persistent_tcp(1);
#$resolver->persistent_udp(1);
#$resolver->debug(0);

my $util = util->new;
	
my $rows;
my $PROFDOMAIN={};
my $LIST={};
my $LISTCNT={};
my $cnt;
my $username;
my $checkedMxDomains = {};
my $processedSourceUrls = {};
my $emailExists = {};
my $urltotal = {};
my $startTime = time;
#my $emailsByCaptureDate = {};

#do not buffer output	
$|++;
	
#get parameters
my $params = setParameters();
	
#set lock type
my $type = 'general';



my $handles = getDBHandles();
	
my $dbh  = $handles->{'write'};
my $dbh1 = $handles->{'query'};
		
#if lock granted and we are supposed to process flatfiles
if(
	checkFlatFileStatus() 
	&& 
	checkProcesses($params)
) 
{

	#show help 
	if ($params->{'help'}) 
	{
		displayUsage();
	} 
		
	#run script
	else
	{
		
		my $host 	   = hostname();	
		my $cdate 	   = localtime();
		my $got_client = 0;
		
		#user may have passed in client id
		while (!$got_client)
		{
			#get next client to process
			if(!$params->{'client_id'}){
			
				my $clientData = getClientToProcess($params);
					
				$params->{'client_id'} = $clientData->{'client_id'};
	
			} 
			
			else
			{
				$got_client++;
			}
			
			if ($params->{'skip_client'})
			{
				$params->{'skip_client'} = $params->{'skip_client'}.",".$params->{'client_id'};
			}
			
			else
			{
				$params->{'skip_client'} = $params->{'client_id'};
			}
			
			print "$$ : processing client : $params->{'client_id'}\n";
				
			## try to get list lock
			if(getListLock($params))
			{
				
				getCurrentMonthListName($params);
					
				$got_client++;
				
				#get email class info
				## TODO: Cache this data locally
				my ($emailClass, $emailDomainData) = getClassNames();
				
				#get all lists for that client to process
				my $listQuery = getListToProcess($params);
				
				my $sth = $dbh->prepare($listQuery);
				$sth->execute();
				
				my $listArrayData = [];

				while(my $data = $sth->fetchrow_hashref())
				{
					push(@{$listArrayData}, $data);					
				}	
				
				foreach my $listData (@{$listArrayData})
				{
					##only proceed if the profile is setup correctly
					if($listData->{'profile_id'})
					{
															
						$LIST	 = {};
						$LISTCNT = {};
					
						#print "Processing class name : $class_name \n";
						getProfileDomains($listData->{'profile_id'});
								
						$listData->{'FLATFILE_SRC'}    = $ENV{'FLATFILE_SRC'};
						$listData->{'FLATFILE_DEST'}   = $ENV{'FLATFILE_DEST'};		
						$listData->{'emailClass'}      = $emailClass;
						$listData->{'emailDomainData'} = $emailDomainData;			
						$listData->{'type'}		       = $type;	
									
						#get list id and name, etc to build email files
						$urltotal = getListInfo($params, $listData, $urltotal);
								
					}
				}
				
				_setTotalDataCounts($params);
				
				#update clients updated time
				updateClientUpdatedDateTime($params);
				
				#unlock the client
				$params->{'unlock'} = 1;
				updateListLock($params);
						
					
			}
			
			else
			{
				$params->{'client_id'} = 0; 
			}
				
		}
	}
		
	$dbh->disconnect();
	$dbh1->disconnect();



} 


sub updateGenders
{

	my ($genderData) = @_;
	
	#print Dumper($genderData);
	
	#$ENV{'DEBUG'}++;
	
	my $gdInterface = Lib::Database::Perl::Interface::GenderData->new(('write' => 1));
	
	foreach my $gender (keys %{$genderData})
	{
		if($gender ne '')
		{
			$gdInterface->updateEmailListGender(
			{
				'genderLabel' => $gender,
				'emailUserID' => $genderData->{$gender}
			});
		}
	}
	
	#$ENV{'DEBUG'} = 0;
}

sub getListLock
{
	
	my ($params) = @_;
	
	my $status = 0;
	
	print "$$ : trying to get lock for client:$params->{'client_id'} \n";
	
	#try to lock the client
	updateListLock($params);

	if(confirmListLock($params))
	{
		$status++;			
		print "$$ : successfully locked client:$params->{'client_id'} \n";
			
	}
	else
	{
			print "$$ : didnt get lock for client:$params->{'client_id'} \n";
	}

	return($status);
	
}


sub updateClientUpdatedDateTime {
	
	my ($params) = @_;
	
	my $sql = qq|update user set overall_updated=now() where user_id=$params->{'client_id'}|;
		
#	if ($params->{'newest'}) 
#	{
#		$sql="update user set newest_updated=now() where user_id=$params->{'client_id'}";
#	}
#	else
#	{
#		$sql="update user set overall_updated=now() where user_id=$params->{'client_id'}";
#	}
	
	unless ($dbh && $dbh->ping) {
					
		my $handles = getDBHandles({'write' => 1});
				
		$dbh = $handles->{'write'};

	}
	   			
	#print "$sql \n";
				
	#only update date and time if we really processed a file
	if(checkFlatFileStatus()){
		$dbh->do($sql);
	}
	
}


sub checkProcesses {
	
	my($params) = @_;
	
	$params->{'overallConcurrentProcesses'} ||= 5;

	my $status = 0;
		
	my $processCount = `ps aux | grep get_data.sh | grep -v grep | grep -v "dev/null" | wc -l`;
	
	if($processCount < $params->{'overallConcurrentProcesses'})
	{
		$status++;
	}

	return($status);
	
}

	
sub setParameters {

	my $options = {};
		
    GetOptions (
    $options,
    
    'newest',
    'help',
    'client_id:i',
    'recordLimit:i',
    
    ); 
        
    $options->{'recordLimit'} ||= 60000;
    
    return($options);

}

sub displayUsage {

	print qq|
Usage: $0 [options]

Options:

Only one of the following:
	--newest		Gets newest records list
	--help			Display this usage message.
	--client_id		process specific client
	--recordLimit	sets number of records to limit main query (default 60000)

|;

}

sub getDays {
	
	my ($day_flag) = @_;
	
	my $wait_days = 0;
	
	my %dayMap	= (
		'Y'	=> 60 + $wait_days,
		'7'	=> 7 + $wait_days,
		'F'	=> 15 + $wait_days,
		'M'	=> 30 + $wait_days,
		'9'	=> 90 + $wait_days,
		'3'	=> 120 + $wait_days,
		'5'	=> 150 + $wait_days,
		'0'	=> 180 + $wait_days,
	);

	my $days	= $dayMap{$day_flag} || 0;

	return($days);
	
}

sub getClassNames 
{
			
	my $classSql = qq|
	SELECT
		ec.class_name,
		ed.domain_class,
		ed.domain_name,
		ed.domain_id
	FROM
		email_domains ed
		JOIN email_class ec on ec.class_id = ed.domain_class
	WHERE
		ec.class_id != 4
		and ed.suppressed = 0
		and ec.status = 'Active'
	|;

	unless ($dbh1 && $dbh1->ping) {
		
		my $handles = getDBHandles({'query' => 1});
			
		$dbh1 = $handles->{'query'};

   	} 
	
	my $sth = $dbh1->prepare($classSql);
	$sth->execute();
	
	#my $emailClass = {};
	my $emailDomainData = {};
	my $emailClassData = {};
	
	while ( my $data = $sth->fetchrow_hashref()) {
			
		my $className = lc($data->{'class_name'});

		$emailClassData->{$className} = {};
		
		$emailDomainData->{$data->{'domain_name'}}->{'domain_id'} = $data->{'domain_id'};
		$emailDomainData->{$data->{'domain_name'}}->{'className'} = $className;

	} #end while
	
	#add others
	$emailClassData->{'others'} = {};

	return($emailClassData, $emailDomainData);
		
}

sub getFlatFileQuery
{
	my ($list_id, $day_flag) = @_;
	
	my $dayClause = '';
			
	if ($day_flag ne "N") 
	{			
		my $days = getDays($day_flag);
				
		$dayClause = " AND subscribe_date >= date_sub(curdate(), interval $days day)";		
	}
	
	my $emailListTables  = ['email_list_new', 'email_list'];
	my $emailListQueries = [];
	
	my $unionStatus++;
	
	foreach my $emailList (@{$emailListTables})
	{
		
		if($unionStatus)
		{
			my $sql = qq|					
			SELECT 
				email_addr,
				email_user_id,
				state,
				first_name,
				last_name,
				address, 
				address2,
				city,
				zip,
				e.domain_id,
				coalesce(LOWER(ec.class_name), 'others') as className,
				coalesce(ec.class_id, 4) as classID,
				datediff(curdate(),subscribe_date) as subscribe_date,
				source_url,
				capture_date,
				member_source,
				DATE_FORMAT(subscribe_date, '\%Y-\%m-15') as listBucket,
				subscribe_date as sdate,
				TIME_TO_SEC(subscribe_time) as subscribe_time,
				e.gender,
				e.phone,
				case e.dob when '0000-00-00' then '' else e.dob end as dob,
				case e.dob when '0000-00-00' then '' else (YEAR(CURDATE()) - YEAR(e.dob)) - (RIGHT(CURDATE(), 5) < RIGHT(e.dob, 5)) end as age,
				
				CASE WHEN dob != '0000-00-00' THEN 1 ELSE 0 end as dobCount,
				CASE WHEN gender = 'M' THEN 1 ELSE 0 end as genderMaleCount,
				CASE WHEN gender = 'F' THEN 1 ELSE 0 end as genderfemaleCount,
				CASE WHEN
				(
					(first_name != '')
					and
					(last_name != '')
				)
				THEN 1 ELSE 0 end as firstNameLastNameCount,
				
				CASE WHEN
				(
					(address != '')
					and
					(city != '')
					and
					(zip != '')
				)
				THEN 1 ELSE 0 end as fullPostalCount,
				
				CASE WHEN
				(
					(first_name != '')
					and
					(last_name != '')
					and
					(address != '')
					and
					(city != '')
					and
					(zip != '')
				)
				THEN 1 ELSE 0 end as fullPostalFirstNameLastNameCount,
							
				CASE WHEN
				(
					(first_name != '')
					and
					(last_name != '')
					and
					(address != '')
					and
					(city != '')
					and
					(zip != '')
					and
					(phone != '')
				)
				THEN 1 ELSE 0 end as fullPostalFirstNameLastNamePhoneCount,
				
				CASE WHEN phone != '' THEN 1 ELSE 0 end as phoneCount,
				
				CASE
					WHEN l.list_name = 'Openers'  THEN 'opener'
					WHEN l.list_name = 'Clickers' THEN 'clicker'
					ELSE 'deliverable'
				END as actionType
				
			FROM 
				$emailList e
				JOIN list l on e.list_id = l.list_id
				LEFT OUTER JOIN email_domains ed ON e.domain_id = ed.domain_id
				LEFT OUTER JOIN email_class ec ON ed.domain_class = ec.class_id		
			WHERE 	
				e.list_id = $list_id
				AND e.status = 'A'
				AND (ed.suppressed = 0 or ed.suppressed is null)
				$dayClause
			|; 
			
			push(@{$emailListQueries}, $sql);
			
			$unionStatus = 0;
		}

		## email_list was originally defined with a list_id as a smallint and we hit that value
		## so we dont need to union to the old table if the list_id is greater than 65535
		if($list_id <= 65535)
		{
			$unionStatus++;
		}
			
	}
	
	my $sql = join(' UNION ', @{$emailListQueries});
	
	return($sql);
			
}
	
sub process_list  
{
	
	my ($params, $data, $urltotal) = @_;
	
	my $list_cnt 	= 0;
	my $aol_cnt		= 0;
	my $hotmail_cnt = 0;
	my $yahoo_cnt	= 0;
	my $msn_cnt		= 0;
	my $comcast_cnt	= 0;
	
	my $day_flag   = $data->{'day_flag'};
	my $list_id    = $data->{'list_id'};
	my $client_id  = $data->{'client_id'};
	my $runType    = $data->{'type'};
	my $emailList  = $data->{'email_list'} || 'email_list';
	
	my $FLATFILE_SRC  = $data->{'FLATFILE_SRC'};
	my $FLATFILE_DEST = $data->{'FLATFILE_DEST'};
	
	#hash of email classes not including 'Others'
	my $emailClassData = $data->{'emailClass'};

	my $sourceDirectory 	 = "$FLATFILE_SRC/temp/$runType/$client_id";
	my $flatfile_destination = "$FLATFILE_DEST/${client_id}/";

	makeDirectory($sourceDirectory);
	
	my $listProcessingCount   = 1;
	my $listRefreshSuccessful = 0;
	
	while(
		($listProcessingCount < 3) 
		&& 
		!$listRefreshSuccessful
	)
	{
		
		print "$$ : trying to process list $list_id $listProcessingCount time(s)\n";
		
		## we needed to rerun list so refresh DB connections
		if($listProcessingCount)
		{
			my $handles = getDBHandles();
			$dbh1 = $handles->{'query'};
		}

		my $recordCount = 0;

		my $email_sql = getFlatFileQuery($list_id, $day_flag);
		#print "$email_sql \n";
			
		my $sth = $dbh1->prepare($email_sql);
		$sth->{"mysql_use_result"}++;
	
		$sth->execute();
			
		$emailClassData = openFileHandles($data);
	
		my $genderData = {};
		my $dataCount = {};
			
		while (my $userInfo = $sth->fetchrow_hashref()) 
		{
						
			$userInfo->{'email_list'} = $emailList;
			$userInfo->{'list_id'}    = $list_id;
			_fixWeirdChars($userInfo);
																	
			#only process domains selected in the profile
			if($PROFDOMAIN->{ $userInfo->{'className'} })
			{
	
				#by default allow writes to the flatfile
				my $writeStatus = 1;
						

		
				if($writeStatus)
				{
			
					my $data = qq($userInfo->{'email_addr'}|$userInfo->{'email_user_id'}|$userInfo->{'state'}|$userInfo->{'first_name'}|$userInfo->{'last_name'}|$userInfo->{'city'}|$userInfo->{'zip'}|$userInfo->{'subscribe_date'}|$userInfo->{'source_url'}|$userInfo->{'capture_date'}|$userInfo->{'member_source'}|$userInfo->{'subscribe_time'}|$userInfo->{'gender'}|$userInfo->{'age'}|$userInfo->{'phone'}|$userInfo->{'address'}|$userInfo->{'address2'}|$userInfo->{'dob'});
					
					$data =~ s/\r\n//g;
						
					print {$emailClassData->{ $userInfo->{'className'} }->{'fileHandle'}} "$data \n";
					
					## keep counts on the record
					$userInfo->{'clientID'} = $client_id;
					$dataCount = _dataCounts($userInfo, $dataCount); 
						
				}
							
				## keep count of source url 
				$urltotal->{$userInfo->{'source_url'}}++;
				
				$list_cnt++;
								
				if ($userInfo->{'className'} eq "aol")
				{
					$aol_cnt++;
				}
				elsif ($userInfo->{'className'} eq "hotmail")
				{
					$hotmail_cnt++;
				}
				elsif ($userInfo->{'className'} eq "yahoo")
				{
					$yahoo_cnt++;
				}
				elsif ($userInfo->{'className'} eq "comcast")
				{
					$comcast_cnt++;
				}					
			} 		
		} 	
		
		#close all open file handles
		closeFileHandles($emailClassData);
	
		## only continue if there are no DB errors otherwise reprocess
		if(dbErrorCheck($dbh1))
		{
					
			## update all eids with new gender		
			updateGenders($genderData);
		
			unless ($dbh && $dbh->ping) 
			{
				my $handles = getDBHandles({'write' => 1});	
				$dbh = $handles->{'write'};
			} 
		   
		   my $listSql = qq|
		   update list 
		   set 
			   member_cnt  = $list_cnt,
			   aol_cnt     = $aol_cnt,
			   hotmail_cnt = $hotmail_cnt,
			   msn_cnt	   = $msn_cnt,
			   yahoo_cnt   = $yahoo_cnt,
			   comcast_cnt = $comcast_cnt,
			   foreign_cnt = 0 
		   where 
				list_id = $list_id
		   |;
		   
		   $dbh->do($listSql);
		   
		   _updateDataCounts($dataCount);
		   			
			my $cdate = localtime();
			print "$$ : Finished list Client $client_id List $list_id ($list_cnt) at $cdate\n";
		
			my $dest_host = $ENV{'FLATFILE_DEST_SERVER'} || 'mtadata03.routename.com';
				
		   	system("/usr/bin/scp -o StrictHostKeyChecking=no $sourceDirectory/*.dat $dest_host:$flatfile_destination");
		   	system("rm -rf $sourceDirectory/*.dat");
		   	
		   	$listRefreshSuccessful++;
		   	
		   	print "$$ : we succesfully processed list $list_id \n";
			
		}
		
		else
		{
			$listProcessingCount++;	
		}
		
	}

	return ($urltotal, $listRefreshSuccessful);

}

sub _emailListUpdateColumns
{
	my $columns = 
	[
		'state',
		'first_name',
		'last_name',
		'address', 
		'address2',
		'city',
		'zip',
		'source_url',
		'capture_date',
		'member_source',
		'sdate',
		'gender',
		'phone'
	];
	
	return($columns);	
}

sub _fixWeirdChars
{
	my ($data) = @_;
	
	my $totalCount = 0;
	
	my $columns = _emailListUpdateColumns();
	
	foreach my $column (@{$columns})
	{
		my $count = chomp( $data->{$column} );
		$totalCount += $count;  
		
		$count = $data->{$column} =~ s/\cM//g;
		$totalCount += $count; 
		
	}

	if($totalCount > 0)
	{
		updateEmailListData($data);	
	}
}

sub _tempStatsTableQuery
{
	my ($tempTable) = @_;
	
	## its a temp table but the DB connections are unreliable in this process and we need to keep the data
	## the table is dropped later
	my $query = qq|CREATE TABLE IF NOT EXISTS $tempTable LIKE ClientMailableRecordDetailTotals|;
	
	return($query);
}

sub _setTotalDataCounts
{
	my ($params) = @_;

	my $tempTable = 'ClientMailableRecordDetailTotalsTemp_' . $startTime . '_' . $$;
	
	$dbh->do(qq|delete from ClientMailableRecordDetailTotals where clientID = $params->{'client_id'}|);
	$dbh->do(qq|insert into ClientMailableRecordDetailTotals select * from $tempTable|);
	$dbh->do(qq|drop table if exists $tempTable|);
}

sub _updateDataCounts
{
	my ($dataCount) = @_;

	my $tempTable = 'ClientMailableRecordDetailTotalsTemp_' . $startTime . '_' . $$;
	
	## create temp table
	$dbh->do(_tempStatsTableQuery($tempTable));
	
	foreach my $clientID (keys %{$dataCount})
	{	
		foreach my $actionType (keys %{$dataCount->{$clientID}})
		{
			foreach my $classID (keys %{$dataCount->{$clientID}->{$actionType}})
			{
				foreach my $subscribeDate (keys %{$dataCount->{$clientID}->{$actionType}->{$classID}})
				{	
					my $sql = qq|
					INSERT INTO $tempTable
					(
						emailUserActionTypeID,
						clientID, 
						classID, 
						totalRecords, 
						fullPostalCount, 
						fullPostalFirstNameLastNameCount, 
						phoneCount, 
						fullPostalFirstNameLastNamePhoneCount,
						dobCount,
						firstNameLastNameCount,
						genderMaleCount,
						genderFemaleCount,
						subscribeDate
					)
					SELECT
						emailUserActionTypeID,
						"$clientID",
						"$classID",
						"$dataCount->{$clientID}->{$actionType}->{$classID}->{$subscribeDate}->{ 'totalRecords' }", 
						"$dataCount->{$clientID}->{$actionType}->{$classID}->{$subscribeDate}->{ 'fullPostalCount' }", 
						"$dataCount->{$clientID}->{$actionType}->{$classID}->{$subscribeDate}->{ 'fullPostalFirstNameLastNameCount' }",
						"$dataCount->{$clientID}->{$actionType}->{$classID}->{$subscribeDate}->{ 'phoneCount' }",
						"$dataCount->{$clientID}->{$actionType}->{$classID}->{$subscribeDate}->{ 'fullPostalFirstNameLastNamePhoneCount' }",
						"$dataCount->{$clientID}->{$actionType}->{$classID}->{$subscribeDate}->{ 'dobCount' }",
						"$dataCount->{$clientID}->{$actionType}->{$classID}->{$subscribeDate}->{ 'firstNameLastNameCount' }",
						"$dataCount->{$clientID}->{$actionType}->{$classID}->{$subscribeDate}->{ 'genderMaleCount' }",
						"$dataCount->{$clientID}->{$actionType}->{$classID}->{$subscribeDate}->{ 'genderFemaleCount' }",
						"$subscribeDate"
					FROM
						EmailUserActionType
					WHERE 
						emailUserActionLabel = "$actionType"
					|;
					
					$dbh->do($sql);	
				}	
			}
		}
	}
	
}

sub _dataCounts
{
	my ($userRecord, $dataCount) = @_;

	## keep track of full postal ONLY
	$dataCount->{ $userRecord->{'clientID'} }->{ $userRecord->{'actionType'} }->{ $userRecord->{'classID'} }->{ $userRecord->{'listBucket'} }->{ 'fullPostalCount' } 
		+= $userRecord->{'fullPostalCount'};
		
	## keep track of full postal with first and last name
	$dataCount->{ $userRecord->{'clientID'} }->{ $userRecord->{'actionType'} }->{ $userRecord->{'classID'} }->{ $userRecord->{'listBucket'} }->{ 'fullPostalFirstNameLastNameCount' } 
		+= $userRecord->{'fullPostalFirstNameLastNameCount'};
	
	## keep track of phone
	$dataCount->{ $userRecord->{'clientID'} }->{ $userRecord->{'actionType'} }->{ $userRecord->{'classID'} }->{ $userRecord->{'listBucket'} }->{ 'phoneCount' } 
		+= $userRecord->{'phoneCount`'};	
	
	## keep track of full postal with phone
	$dataCount->{ $userRecord->{'clientID'} }->{ $userRecord->{'actionType'} }->{ $userRecord->{'classID'} }->{ $userRecord->{'listBucket'} }->{ 'fullPostalFirstNameLastNamePhoneCount' } 
		+= $userRecord->{'fullPostalFirstNameLastNamePhoneCount`'};
		
	## keep track of DOB
	$dataCount->{ $userRecord->{'clientID'} }->{ $userRecord->{'actionType'} }->{ $userRecord->{'classID'} }->{ $userRecord->{'listBucket'} }->{ 'dobCount' } 
		+= $userRecord->{'dobCount'};
		
	## keep track of first and last name
	$dataCount->{ $userRecord->{'clientID'} }->{ $userRecord->{'actionType'} }->{ $userRecord->{'classID'} }->{ $userRecord->{'listBucket'} }->{ 'firstNameLastNameCount' } 
		+= $userRecord->{'firstNameLastNameCount'};	

	## keep track of genders
	$dataCount->{ $userRecord->{'clientID'} }->{ $userRecord->{'actionType'} }->{ $userRecord->{'classID'} }->{ $userRecord->{'listBucket'} }->{ 'genderMaleCount' } 
		+= $userRecord->{'genderMaleCount'};	
		
	$dataCount->{ $userRecord->{'clientID'} }->{ $userRecord->{'actionType'} }->{ $userRecord->{'classID'} }->{ $userRecord->{'listBucket'} }->{ 'genderFemaleCount' } 
		+= $userRecord->{'genderFemaleCount'};	

	## keep record counts
	$dataCount->{ $userRecord->{'clientID'} }->{ $userRecord->{'actionType'} }->{ $userRecord->{'classID'} }->{ $userRecord->{'listBucket'} }->{ 'totalRecords' }++;
	
	return($dataCount);
}

sub dbErrorCheck
{
	my ($dbh) = @_;
	
	my $status = 1;
	
	if($dbh->errstr)
	{
		$status = 0;		
	}

	return($status);
}


sub openFileHandles {
	
	my ($data) = @_;

	#hash of email classes
	my $emailClassData = $data->{'emailClass'};

	foreach my $emailClass (keys %{$PROFDOMAIN}) {
					
		#set list id
		$emailClassData->{ $emailClass }->{'listId'} = $data->{'list_id'};

		#set file name				
		$emailClassData->{ $emailClass }->{'fileName'} = "$data->{'FLATFILE_SRC'}/temp/$data->{'type'}/$data->{'client_id'}/$data->{'list_id'}" . "_" . $emailClass . ".dat";
				
		#set file handle
		$emailClassData->{ $emailClass }->{'fileHandle'} = new FileHandle "> " . $emailClassData->{ $emailClass }->{'fileName'};
		
	}
	
	return($emailClassData);
	
}

sub closeFileHandles 
{
	
	my ($emailClassData) = @_;

	foreach my $emailClass (keys %{$emailClassData}){
		
		if($emailClassData->{$emailClass}->{'fileHandle'}){
			$emailClassData->{$emailClass}->{'fileHandle'}->close;
			delete $emailClassData->{$emailClass}->{'fileHandle'};
		}
		
	}
	
}

sub checkFlatFileStatus 
{

	my $host = hostname();	

	my $sql = qq|
	select 1 
	from 
		Server s 
		join ServerTypeJoin stj on stj.serverID=s.serverID 
		join ServerType st on st.serverTypeID=stj.serverTypeID 
	where st.serverTypeLabel='flatFilePuller' and s.hostname like "\%$host\%"|;
	
	my $sth = $dbh->prepare($sql);
	$sth->execute();
	
	my ($flatFileStatus) = $sth->fetchrow_array();
	$sth->finish();	
	
	#print "status is $flatFileStatus \n";

	return($flatFileStatus || 0);
	
}


	
sub makeDirectory {
	
	my ($directory) = @_;

	#make directory if it doesnt exist
	unless(-d $directory)
	{
		mkpath( $directory, {verbose => 1} );
	} 	
	
}

	

sub updateListStatus {

	my ($listID, $status) = @_;
	
	#this function is hacky but this is all changing soon
	
	my $columns = [ "listUpdatedStatus = $status" ];	
	
	#only update date time if list was actually updated
	if($status == 0){
		#push(@{$columns}, "listLocked = null", "listLastUpdated = now()");
		push(@{$columns}, "listLastUpdated = now()");
	}
	
	my $columnData = join(',', @{$columns});

	my $update_query = qq|
		
	UPDATE
		list
	SET 
		$columnData
	WHERE 
		list_id = $listID
	
	|;	
	
	#print "$update_query \n";
	
	unless ($dbh && $dbh->ping) {
		
		my $handles = getDBHandles({'write' => 1});		
		$dbh = $handles->{'write'};
		
   	} 

	$dbh->do($update_query);
	
}


sub updateEmailListData 
{
	my ($userInfo) = @_;

	my $emailListTable = 'email_list';

	if($userInfo->{'list_id'} > 65535)
	{
		$emailListTable = 'email_list_new';	
	}
	
	my $update_query = qq|		
	UPDATE
		$emailListTable
	SET 
		state = "$userInfo->{'state'}",
		first_name = "$userInfo->{'first_name'}",
		last_name = "$userInfo->{'last_name'}",
		address  = "$userInfo->{'address'}",
		address2 = "$userInfo->{'address2'}",
		city = "$userInfo->{'city'}",
		zip = "$userInfo->{'zip'}",
		source_url = "$userInfo->{'source_url'}",
		capture_date = "$userInfo->{'capture_date'}",
		member_source = "$userInfo->{'member_source'}",
		gender = "$userInfo->{'gender'}",
		phone = "$userInfo->{'phone'}"
	WHERE 
		email_user_id = $userInfo->{'email_user_id'}
	|;	
	
	unless ($dbh && $dbh->ping) 
	{
		my $handles = getDBHandles({'write' => 1});
			
		$dbh = $handles->{'write'};
   	} 

	$dbh->do($update_query);
	
}


sub getCurrentMonthListName
{
	
	my ($params) = @_;

    my ($sec, $min, $hour, $mday, $mon, $year, $wday, $yday, $isdst) = localtime(time);
    
    $year += 1900;
    $mon++;

	$params->{'currentMonthListName'} = sprintf("%04d-%02d", ($year, $mon));
		
}




sub getListInfo 
{
	
	my ($params, $listData, $urltotal) = @_;
		
	if (($listData->{'list_type'} eq 'OPEN') || ($listData->{'list_type'} eq 'CLICK')) 
	{
		if ($listData->{'open_click_flag'} eq "Y") 
		{
			$listData->{'day_flag'} = "N";
		} 
	} 
		
	if ($listData->{'list_name'} eq "DM Seeds") 
	{
		$listData->{'day_flag'} = "N";
	} 
		
	#get emails from international table
	if($listData->{'list_type'} eq 'INTERNATIONAL')
	{
		$listData->{'email_list'} = 'international_email_list'
	} 

	#process list info if the status allows it
	if(checkFlatFileStatus() && checkListStatus($listData))
	{
			
		print "$$ : Starting at " . localtime() . " client:$listData->{'client_id'} list:$listData->{'list_id'} \n";
			
		my ($urltotalNew, $listRefreshSuccessful) = process_list($params, $listData, $urltotal);
		
		$urltotal = $urltotalNew;
		
		#set list to processed
		if($listRefreshSuccessful)
		{
			updateListStatus($listData->{'list_id'}, 0);
		}
			
	} 
	
	#set status to 1 to be processed again later
	else
	{
		updateListStatus($listData->{'list_id'}, 1);	
	} 

	return $urltotal;
}

sub checkListStatus {
	
	my ($listData) = @_;

	my $flatFileStatus = 0;

	if($listData->{'listUpdatedStatus'} || ($listData->{'type'} eq 'newest')){	
		$flatFileStatus = 1;
	}

	return($flatFileStatus);
	
}

sub getClientToProcess {

	my ($params) = @_;
	
	my $skip_clients = '1';
	
	if ($params->{'skip_client'})
	{
		$skip_clients .= $params->{'skip_client'};
	}

	my $query = qq|
	
	SELECT
		u.user_id as client_id,
		u.username,
		u.overall_updated,
		u.newest_updated
	FROM
		user u
	WHERE
		u.status = 'A'
		AND u.user_id not in ($skip_clients)
	order by overall_updated
	limit 1
	
	|;

	#print "$query \n";

	my $sth = $dbh->prepare($query);
	$sth->execute();
		
	my $clientData = $sth->fetchrow_hashref();
	
	return($clientData);
	
}

sub getListToProcess 
{

	my ($params) = @_;
	
	my $additionalWhere = "
	(l.list_name != 'Newest Records' or l.list_name is null)
	AND (l.listUpdatedStatus = 1 or l.listUpdatedStatus is null)
	AND u.masterProfileAlertSent = 0";
	
	my $orderBy = 'overall_updated';
	
#	if($params->{'newest'}){
#		$additionalWhere = "l.list_name = 'Newest Records'";
#		$orderBy = 'newest_updated';
#	}

	if($params->{'client_id'}){
		$additionalWhere .= " AND u.user_id = $params->{'client_id'}";
	}
	
	my $query = qq|
	SELECT
		u.user_id as client_id,
		u.username,
		u.overall_updated,
		u.newest_updated,
		lpl.list_id,
		l.listUpdatedStatus,
		l.list_name,
		l.list_type,
		l.listLocked,
		lp.profile_id,
		lp.day_flag,
		lp.open_clickers_ignore_date as open_click_flag,
		lp.aol_flag,
		lp.hotmail_flag,
		lp.yahoo_flag,
		lp.other_flag,
		u.masterProfileAlertSent
	FROM
		user u
		LEFT OUTER JOIN list_profile lp ON u.user_id = lp.client_id AND lp.master = 'Y' AND lp.status = 'A'
		LEFT OUTER JOIN list_profile_list lpl ON lp.profile_id = lpl.profile_id
		LEFT OUTER JOIN list l ON l.list_id = lpl.list_id
	WHERE
		$additionalWhere
	order by 
		$orderBy
	|;

#		u.status = 'A'
#	AND 
#		u.user_id not in (1,276)
#	AND 
#		l.listLocked is null

	#print "$query \n";
	
	return($query);
	
}

sub confirmListLock {

	my ($params) = @_;
		
	my $lock = hostname() . ':' . $$;
			
	my $query = qq|
			
	SELECT
		count(*) as count
	FROM 
		list
	WHERE 
		user_id = $params->{'client_id'}
	AND
		listLocked = '$lock'
			
	|;
			
	#print "$query \n";
		
	my $sth = $dbh->prepare($query);
	$sth->execute();
				
	my $listLockData = $sth->fetchrow_hashref();	
	
	return($listLockData->{'count'});
	
}

sub updateListLock {

	my ($params) = @_;

	#set lock name to be hostname and pid
	my $pid = "'" . hostname() . ':' . $$ . "'";
	my $lock = $pid;
	
	my $listLockClause  = "listLocked is null";
	my $lockAction 		= 'lock';
	my $listNameClause  = "list_name != 'Newest Records'";
	my $lockDateTime 	= 'now()';
	
	#unlock list
	if($params->{'unlock'} || $params->{'removeStaleLock'})
	{
		
		$lock = 'NULL';
		$lockDateTime = 'NULL';
		
		$listLockClause = "listLocked = $pid";
		
		## there is no clause
		if($params->{'removeStaleLock'})
		{
			$listLockClause = '1=1';
		}
		
		
		$lockAction = 'unlock';
		
	}
	
	#this is a newest records run so only lock/unlock newest records
#	if($params->{'newest'}){
#		$listNameClause = "list_name = 'Newest Records'";
#	}
	
	print "$$ : trying to $lockAction client : $params->{'client_id'} \n";
	
	my $query = qq|
	
	UPDATE 
		list
	SET 
		listLocked = $lock,
		listLockedDateTime = $lockDateTime
	WHERE 
		user_id = $params->{'client_id'}
	AND
		$listLockClause
	AND
		$listNameClause
	
	|;
	
	#print "$query \n";
	$dbh->do($query);	
	
}

sub getProfileDomains
{
	my ($profile_id)=@_;
	my $cname;

	my $sql="select lower(class_name) from email_class ec,list_profile_domain lpd where lpd.profile_id=? and lpd.domain_id=ec.class_id";
	my $sth=$dbh1->prepare($sql);
	$sth->execute($profile_id);
	while (($cname)=$sth->fetchrow_array())
	{
		$PROFDOMAIN->{$cname}++;
	}
	$sth->finish();
}




sub getQueryDBHandles 
{
	
	my ($params) = @_;
	
	#set manually
	my $database = 'pullerdb.i.routename.com';
	#my $database = 'slavedb.i.routename.com';

	return(DBI->connect("DBI:mysql:new_mail:$database", 'db_readuser', 'Tr33Wat3r'));
	
}

sub getWriteDBHandles 
{
	
	my $dbhWrite = DBI->connect('DBI:mysql:new_mail:masterdb.i.routename.com', 'db_user', 'sp1r3V');

	#we want to use local IPs for connections
	return($dbhWrite);
	
}

sub getDBHandles 
{
	
	my ($params) = @_;
	
	my $handles = {};

	my ($dbhWrite) = getWriteDBHandles();
	
	my $dbhQuery = getQueryDBHandles();
	
	#get both db handles
	if(!$params->{'query'} && !$params->{'write'})
	{
		$handles->{'write'} = $dbhWrite;
		$handles->{'query'} = $dbhQuery;
	} 
	
	#only return query handle and disconnect new connection to master
	elsif($params->{'query'})
	{
		$handles->{'query'} = $dbhQuery;
		$dbhWrite->disconnect();
	}
	
	#only return write handle
	else 
	{
		$handles->{'write'} = $dbhWrite;	
	} 
	
	return($handles);
	
}


