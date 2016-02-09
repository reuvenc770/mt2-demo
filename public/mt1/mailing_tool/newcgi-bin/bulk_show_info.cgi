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
my $filename;
my $errmsg;
my ($birth_date,$gender,$sub_date);
my ($status, $max_names, $max_mailings, $username);
my $client_id;
my $password;
my $internal_email_addr;
my $physical_addr;
my $cstatus;
my $list_name;
my $action;
my $action_date;
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
my $em;
my $reccnt;

#------  connect to the util database -----------

my ($dbhq,$dbhu)=$util->get_dbh();

my $dbh3 = DBI->connect("DBI:mysql:supp:suppressp.routename.com","db_user","sp1r3V");


#-----  check for login  ------

my $user_id = util::check_security();
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
	$util->clean_up();
    exit(0);
}
$cemail = $query->param('name');
my $export= $query->param('export');
my $btype= $query->param('btype');
my $btypestr;
if ($btype eq "")
{
	$btypestr="email_addr";
}
else
{
	$btypestr=$btype;
}
if ($export eq "")
{
	$export=0;
}
if ($export ==1)
{
    $filename=$user_id."_multieid.csv";
    open(LOG,">/data3/3rdparty/$filename");
}

if ($export == 0)
{
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

end_of_html

print << "end_of_html" ;
        <TABLE cellSpacing=0 cellPadding=0 width="100%" bgColor=#ffffff border=0>
        <TBODY>
        <TR>
        <TD>
            <TABLE cellSpacing=0 cellPadding=5 width="100%" border=0>
            <TBODY>
            <TR>
            <TD align=middle>

                <TABLE cellSpacing=0 cellPadding=0 width="100%" bgColor=#E3FAD1 border=0>
                <TBODY>
                <TR align=top bgColor=#509C10 height=18>
                <TD vAlign=top align=left height=15><IMG src="$images/blue_tl.gif" 
					border=0 width="7" height="7"></TD>
                <TD height=15><IMG height=1 src="$images/spacer.gif" width=3 border=0></TD>
                <TD align=middle height=15>

                    <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
                    <TBODY>
					<tr><td align=center><form method=post action=bulk_show_info.cgi><input type=hidden name=name value="$cemail"><input type=hidden name=export value=1><input type=submit value="Export Data"></form></td></tr>
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
	<tr><th>Username</th><th>ClientID</th><th>Action</th><th>ActionDate</th><th>Eid</th><th>Email</th><th>FirstName</th><th>LastName</th><th>Address</th><th>City</th><th>State</th><th>Zip</th><th>URL</th><th>IP</th><th>Capture<br>Date</th><th>Subscribe<br>Date</th><th>Status</th><th>Archived</th><th>Unsub Date</th><th>Gender</th><th>Suppressed Date Time</th><th>Suppressed Reason</th><th>Function</th></tr>
end_of_html
}
else
{
	print LOG "Username,ClientID,Action,ActionDate,Eid,Email,FirstName,LastName,Address,City,State,Zip,URL,IP,Capture<br>Date,Subscribe<br>Date,Status,Archived,Unsub Date,Gender,Suppressed\n";
}
$cemail =~ s/[\n\r\f\t]/\|/g ;
$cemail =~ s/\|{2,999}/\|/g ;
my @em_array = split '\|', $cemail;

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

## do the same thing for 2 different DB connections
## we do this because we have archived the email_list data
foreach my $dbData (@{$dbConnections})
{
	my $dbhq = $dbData->{'connection'};
	my $emailList = $dbData->{'table'};
	
	## this is for email list calls with list ID and no client ID
	my $isOldEmailList = $dbData->{'isListID'};
	
	my $isArchive = 'No';

	if($dbData->{'isArchive'})
	{
		$isArchive = 'Yes';
	}
	
	foreach $cemail(@em_array)
	{
		if(!$isOldEmailList)
		{
			$sql = qq|
			select 
				email_list.email_addr,email_list.first_name,
				email_list.last_name,email_list.address,
				email_list.city,email_list.state,email_list.zip,email_list.source_url,
				email_list.capture_date,inet_ntoa(member_source),
				concat_ws(' ', unsubscribe_date, unsubscribe_time),user.first_name,dob,gender,
				email_list.status,subscribe_date,email_user_id,user.username,
				user.user_id,emailUserActionName,emailUserActionDate 
			from 
				$emailList email_list,
				user,
				EmailUserActionType euat 
			where 
				email_list.client_id=user.user_id 
				and email_list.emailUserActionTypeID=euat.emailUserActionTypeID 
				and email_list.$btypestr = '$cemail'|;
		}
		
		else
		{
			$sql = qq|
			select 
				email_list.email_addr,email_list.first_name,
				email_list.last_name,email_list.address,
				email_list.city,email_list.state,email_list.zip,email_list.source_url,
				email_list.capture_date,member_source,
				concat_ws(' ', unsubscribe_date, unsubscribe_time),u.first_name,dob,gender,
				email_list.status,subscribe_date,email_user_id,u.username,
				u.user_id,'unknown','unknown' 
			from 
				$emailList email_list
				JOIN list l on email_list.list_id = l.list_id
				JOIN user u on l.user_id = u.user_id 
			where 
				email_list.$btypestr = '$cemail'|;			
		}
		
		$sth = $dbhq->prepare($sql);
		$sth->execute();
		while (($em,$fname,$lname,$addr,$city,$state,$zip,$source_url,$cdate,$cip,$unsub_date,$list_name,$birth_date,$gender,$cstatus,$sub_date,$eid,$list_name,$client_id,$action,$action_date) = $sth->fetchrow_array())
		{
			## apparently people are confused by a record being archived or not archived 
			## so set the status to 'archived' manually if its archived.
			if($dbData->{'isArchive'})
			{
				$cstatus = 'ARCHIVED';
			}
			
			if ($export)
			{
				print LOG "$list_name,$client_id,$action,$action_date,$eid,$em,$fname,$lname,$addr,$city,$state,$zip,$source_url,$cip,$cdate,$sub_date,$cstatus,$isArchive,$unsub_date,$gender,";
			}
			else
			{
				print "<tr><td>$list_name</td><td>$client_id</td><td>$action</td><td>$action_date</td><td>$eid</td><td>$em</td><td>$fname</td><td>$lname</td><td>$addr</td><td>$city</td><td>$state</td><td>$zip</td><td>$source_url</td><td>$cip</td><td>$cdate</td><td>$sub_date</td><td>$cstatus</td><td>$isArchive</td><td>$unsub_date</td><td>$gender</td><td align=center>";
			}
			
			my $suppressionQuery = qq|
			SELECT 
				s.dateTimeSuppressed,
				sr.suppressionReasonDetails
			FROM 
				suppress_list s
				JOIN SuppressionReason sr on s.suppressionReasonID = sr.suppressionReasonID 
			WHERE 
				email_addr = '$em'
			|;
			 
		$sth1 = $dbh3->prepare($suppressionQuery);
		$sth1->execute();
		my ($suppDateTime, $suppReason) = $sth1->fetchrow_array();
		$sth1->finish();
	
			if ($export)
			{
				print LOG "$suppDateTime,$suppReason\n";
			}
			else
			{
				print "$suppDateTime</td><td>$suppReason</td><td>";
			}
			
			$_=$source_url;
			if (/^http/)
			{
			}
			else
			{
				$source_url="http://".$source_url;
			}
			if (!$export)
			{
				print "<a href=$source_url target=_blank>Test Domain</a></td></tr>\n";
			}
		}
		$sth->finish();
	}
}
if (!$export)
{
print<< "end_of_html";
                    <TR>
                    <TD align=middle><IMG height=3 src="$images/spacer.gif" width=3></TD>
					</TR>
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
			<td align="center">
				&nbsp;&nbsp;&nbsp;<A HREF="/cgi-bin/bulk_find_info.cgi?btype=$btype">Return to Search</a></td>
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
}
else
{
	print "Content-type: text/html\n\n";
print<<"end_of_html";
<html>
<body>
<center>
<h4><a href="/downloads/$filename">Click here</a> to download file</h4>
<br>
<A HREF="mainmenu.cgi"><IMG src="$images/home_blkline.gif" border=0></A>&nbsp;&nbsp;&nbsp;<A HREF="/cgi-bin/bulk_find_info.cgi">Return to Search</a>
</center>
<br>
</body>
</html>
end_of_html
}

$util->clean_up();
exit(0);
