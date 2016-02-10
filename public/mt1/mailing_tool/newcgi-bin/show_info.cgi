#!/usr/bin/perl
#===============================================================================
# Purpose: Add vendor suppression list (eg 'user' table).
# Name   : supplist_add.cgi 
#
#--Change Control---------------------------------------------------------------
# 01/26/04  Jim Sobeck  Creation
#===============================================================================

#-----  include Perl Modules ---------
use strict;
use CGI;
use util;

#------  get some objects to use later ---------
my $util = util->new;
my $query = CGI->new;
my $sth;
my $sth1;
my $sql;
my $dbh;
my $errmsg;
my ($birth_date,$gender,$sub_date,$sub_time);
my ($status, $max_names, $max_mailings, $username);
my $password;
my $internal_email_addr;
my $physical_addr;
my $cstatus;
my $list_name;
my $action;
my $action_date;
my $etype;
my $company;
my $eid;
my ($puserid, $pmesg);
my $website_url;
my $company_phone;
my $images = $util->get_images_url;
my $privacy_policy_url;
my $account_type;
my $unsub_option;
my $name;
my $puserid; 
my $pmode;
my $pmesg;
my $this_user_type;
my $cemail;
my ($fname,$lname,$addr,$city,$state,$zip,$source_url,$cdate,$cip,$unsub_date,$list_name); 
my $reccnt;

#------  connect to the util database -----------

my ($dbhq,$dbhu)=$util->get_dbh();
my $dbh3 = DBI->connect("DBI:mysql:supp:suppressp.routename.com","db_user","sp1r3V");

## build suppression detail radio buttons
my $suppressionDetailRadios = $util->buildSuppressionReasonDetails();

#-----  check for login  ------

my $user_id = util::check_security();
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
	$util->clean_up();
    exit(0);
}
$cemail = $query->param('name');

$sql = "select user_type from user where user_id = $user_id";
$sth = $dbhq->prepare($sql);
$sth->execute();
($this_user_type) = $sth->fetchrow_array() ;
$sth->finish();

util::header("User Record");
	
print << "end_of_html";
</TD>
</TR>
<TR>
<TD vAlign=top align=left bgColor=#FFFFFF>

    <TABLE cellSpacing=0 cellPadding=10 bgColor=#FFFFFF border=0 width="100%">
    <TBODY>
    <TR>
    <TD vAlign=top align=left bgColor=#ffffff colSpan=10>

<!--        <TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
        <TBODY>
        <TR>
        <TD vAlign=center align=left><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 
            size=3><B>Find User Information</B> </FONT></TD>
		</TR>
        <TR>
        <TD><IMG height=3 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE> 

        <TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
        <TBODY>
        <TR>
        <TD colSpan=10><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2> -->
end_of_html

print << "end_of_html" ;
<!--			</FONT></TD>
		</TR>
		</TBODY>
		</TABLE> -->


        <TABLE cellSpacing=0 cellPadding=0 width="100%" bgColor=#ffffff border=0>
        <TBODY>
        <TR>
        <TD>
            <TABLE cellSpacing=0 cellPadding=5 width="100%" border=0>
            <TBODY>
            <TR>
            <TD align=middle>

                <TABLE cellSpacing=0 cellPadding=0 width="70%" bgColor=#E3FAD1 border=0>
                <TBODY>
                <TR align=top bgColor=#509C10 height=18>
                <TD vAlign=top align=left height=15><IMG src="$images/blue_tl.gif" 
					border=0 width="7" height="7"></TD>
                <TD height=15><IMG height=1 src="$images/spacer.gif" width=3 border=0></TD>
                <TD align=middle height=15>

                    <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
                    <TBODY>
                    <TR bgColor=#509C10 height=15>
                    <TD align=middle width="100%" height=15><FONT 
						face=Verdana,Arial,Helvetica,sans-serif color=white size=2>
						<B>User Information</B></FONT></TD>
					</TR>
					</TBODY>
					</TABLE>
				</TD>
                <TD height=15><IMG height=1 src="$images/spacer.gif" width=3 border=0></TD>
                <TD vAlign=top align=right bgColor=#509C10 height=15><IMG 
                    src="$images/blue_tr.gif" border=0 width="7" height="7"></TD>
				</TR>
                <TR bgColor=#E3FAD1>
                <TD colSpan=5><IMG height=3 src="$images/spacer.gif" width=1 border=0></TD>
				</TR>
                <TR bgColor=#E3FAD1>
                <TD><IMG height=3 src="$images/spacer.gif" width=3></TD>
                <TD align=middle><IMG height=3 src="$images/spacer.gif" width=3></TD>
                <TD align=middle>
                    <TABLE cellSpacing=0 cellPadding=0 width="100%" border=1>
                    <TBODY>
					
end_of_html

my $emailData = [];

my $suppressionQuery = qq|
SELECT 
	s.dateTimeSuppressed,
	sr.suppressionReasonDetails
FROM 
	suppress_list s
	JOIN SuppressionReason sr on s.suppressionReasonID = sr.suppressionReasonID 
WHERE 
	email_addr = '$cemail'
|;
	 
$sth1 = $dbh3->prepare($suppressionQuery);
$sth1->execute();
my ($suppDateTime, $suppReason) = $sth1->fetchrow_array();
$sth1->finish();

## do the same thing for 2 different DB connections
## we do this because we have archived the email_list data

my $archiveConnection = DBI->connect("DBI:mysql:new_mail:dbarchive.i.routename.com","db_readuser","Tr33Wat3r");

my $dbConnections = 
[
	{
		'connection' => $dbhq,
		'table' => 'email_list',
		'isArchive' => 0,
		'isListID' => 0 
	},
	{
		'connection' => $archiveConnection,
		'table' => 'email_list_2013',
		'isArchive' => 1,
		'isListID' => 0 
	},
	
	{
		'connection' => $archiveConnection,
		'table' => 'email_list_2011',
		'isArchive' => 1,
		'isListID' => 1 
	},
	
	{
		'connection' => $archiveConnection,
		'table' => 'email_list_new_2011',
		'isArchive' => 1,
		'isListID' => 1
	},
];

foreach my $dbData (@{$dbConnections})
{
	my $dbhq = $dbData->{'connection'};
	my $emailList = $dbData->{'table'};
	my $isArchive = 'No';
			
	## this is for email list calls with list ID and no client ID
	my $isOldEmailList = $dbData->{'isListID'};

	if($dbData->{'isArchive'})
	{
		$isArchive = 'Yes';
	}
	
	my $sql;
	
	if(!$isOldEmailList)
	{
		$sql = qq|
		select 
			email_list.first_name,email_list.last_name,email_list.address,email_list.city,
			email_list.state,email_list.zip,email_list.source_url,email_list.capture_date,inet_ntoa(member_source),
			concat_ws(' ', unsubscribe_date, unsubscribe_time),user.first_name,dob,gender,email_list.status,
			subscribe_date,email_list.subscribe_time,email_user_id,user.username,emailUserActionName,emailUserActionDate 
		from 
			$emailList email_list,
			user,
			EmailUserActionType euat 
		where 
			email_list.client_id=user.user_id 
			and email_list.emailUserActionTypeID=euat.emailUserActionTypeID 
			and email_list.email_addr = '$cemail'|;
	}
		
	else
	{
		$sql = qq|
		select 
			email_list.first_name,email_list.last_name,email_list.address,email_list.city,
			email_list.state,email_list.zip,email_list.source_url,email_list.capture_date,member_source,
			concat_ws(' ', unsubscribe_date, unsubscribe_time),u.first_name,dob,gender,email_list.status,
			subscribe_date,email_list.subscribe_time,email_user_id,u.username,'unknown','unknown' 
		from 
			$emailList email_list
			JOIN list l on email_list.list_id = l.list_id
			JOIN user u on l.user_id = u.user_id 
		where 
			email_list.email_addr = '$cemail'|;			
	}
	
	my $sth = $dbhq->prepare($sql);
	$sth->execute();
	while (($fname,$lname,$addr,$city,$state,$zip,$source_url,$cdate,$cip,$unsub_date,
	$list_name,$birth_date,$gender,$cstatus,$sub_date,$sub_time,$eid,$company,$action,$action_date) = $sth->fetchrow_array())
	{
		my $hashData = 
		{
			'company' => $company,
			'action' => $action,
			'actionDate' => $action_date,
			'eid' => $eid,
			'emailAddress' => $cemail,
			'lastName' => $lname,
			'firstName' => $fname,
			'address' => $addr,
			'city' => $city,
			'state' => $state,
			'zip' => $zip,
			'sourceUrl' => $source_url,
			'ip' => $cip,
			'captureDate' => $cdate,
			'dob' => $birth_date,
			'gender' => $gender,
			'subscribeDate' => $sub_date,
			'subscribeTime' => $sub_time,
			'status' => $cstatus,
			'unsubDate' => $unsub_date,
			'isArchived' => $isArchive,
		};
		
		## apparently people are confused by a record being archived or not archived 
		## so set the status to 'archived' manually if its archived.
		if($dbData->{'isArchive'})
		{
			$hashData->{'status'} = 'ARCHIVED';
		}
		
		push(@{$emailData}, $hashData);
		
	#	my $caddr;
	#	my $cdomain;
	#	($caddr,$cdomain) = split("@",$cemail);
	          
	}
	
	$sth->finish();

}

## display suppression info since the record may be pruned from email_list
if($suppDateTime ne '')
{
	print qq|
	<tr><td colspan=16>
	<strong>SUPPRESSION INFO:</strong><br/>
	<br/>
	<strong>Date Time Suppressed :</strong> $suppDateTime <br/>
	<strong>Suppression Reason :</strong> $suppReason 
	<br/>
	</td></tr>|;
}

## output email details
if(@$emailData)
{
	## print header
	print qq|<tr>
	<td><b>Network</b></td>
	<td><b>Action</b></td>
	<td><b>Action Date</b></td>
	<td><b>EID</b></td>
	<td><b>Email Addr</b>
	<td><b>First Name</b></td>
	<td><b>Last Name</b></td>
	<td><b>Address</b></td>
	<td><b>Source</b></td>
	<td><b>IP</b></td>
	<td><b>Date</b></td>
	<td><b>Birth Date</b></td>
	<td><b>Gender</b></td>
	<td><b>Subscribe Date/Time</b></td>
	<td><b>Status</b></td>
	<td><b>Archived</b></td>
	<td><b>Removal Date</b></td></tr>\n|;
	
	foreach my $data (@{$emailData})
	{	
		print qq|<tr>
		<td>$data->{'company'}</td>
		<td>$data->{'action'}</td>
		<td>$data->{'actionDate'}</td>
		<td>$data->{'eid'}</td>
		<td>$data->{'emailAddress'}</td>
		<td>$data->{'firstName'}</td>
		<td>$data->{'lastName'}</td>
		<td>$data->{'address'}<br>$data->{'city'} $data->{'state'} $data->{'zip'}</td>
		<td>$data->{'sourceUrl'}</td>
		<td>$data->{'ip'}</td>
		<td>$data->{'caputre_date'}</td>
		<td>$data->{'dob'}</td>
		<td>$data->{'gender'}</td>
		<td>$data->{'subscribeDate'} $data->{'subscribeTime'}</td>
		<td>$data->{'status'}</td>
		<td>$data->{'isArchived'}</td>
		<td>$data->{'unsubDate'}</td></tr>\n|;
	}
}

## add radio buttons for users to check suppression reason
print qq|<tr><td colspan=16>

<form action="/cgi-bin/remove_user.cgi" method="get">
<input type="hidden" name='emailAddress' value='$cemail' \>
<input type="hidden" name='global' value='Y' \>

<strong>Please select a suppression reason:</strong><br/>
$suppressionDetailRadios

<input type="submit" value="Add to Global Suppression">
</form>

</td></tr>|;

print<< "end_of_html";
                    <TR>
                    <TD align=middle><IMG height=3 src="$images/spacer.gif" width=3></TD>
					</TR>
					</TBODY>
					</TABLE>
				</TD>
                <TD align=middle><IMG height=3 src="$images/spacer.gif" width=3></TD>
                <TD><IMG height=3 src="$images/spacer.gif" width=3></TD>
				</TR>
                <TR bgColor=#E3FAD1>
                <TD colSpan=5><IMG height=3 src="$images/spacer.gif" width=1 border=0></TD>
				</TR>
                <TR bgColor=#E3FAD1 height=10>
                <TD vAlign=bottom align=left><IMG height=7 src="$images/lt_purp_bl.gif" 
					width=7 border=0></TD>
                <TD><IMG height=3 src="$images/spacer.gif" width=1 border=0></TD>
                <TD align=middle bgColor=#E3FAD1><IMG height=3 src="$images/spacer.gif" 
					width=1 border=0>
					<IMG height=3 src="$images/spacer.gif" width=1 border=0></TD>
                <TD><IMG height=3 src="$images/spacer.gif" width=1 border=0></TD>
                <TD vAlign=bottom align=right><IMG height=7 src="$images/lt_purp_br.gif" 
					width=7 border=0></TD>
				</TR>
				</TBODY>
				</TABLE>
			</TD>
			</TR>
			</TBODY>
			</TABLE>
		</TD>
		</TR>
        <TR>
        <TD>
            <TABLE cellSpacing=0 cellPadding=7 width="100%" border=0>
            <TBODY>
            <TR>
			<td align="center">
				<A HREF="mainmenu.cgi">
				<IMG src="$images/home_blkline.gif" border=0></A></TD>	
			<td align="center"><A HREF="/cgi-bin/find_info.cgi">Return to Search</a></td>
			</tr>
			</table>

		</TD>
		</TR>
		</TBODY>
		</TABLE>


	</TD>
	</TR>
	</TBODY>
	</TABLE>

</TD>
</TR>
<TR>
<TD noWrap align=left height=17>
end_of_html

$util->footer();
$util->clean_up();
exit(0);


