#!/usr/bin/perl
#===============================================================================
# Purpose: Update advertiser info - (eg table 'user' data).
# Name   : advertiser_upd.cgi (update_advertiser_info.cgi)
#
#--Change Control---------------------------------------------------------------
# 01/04/04  Jim Sobeck  Creation
#===============================================================================

# include Perl Modules
use strict;
use CGI;
use util;

# get some objects to use later
my $util = util->new;
my $query = CGI->new;
my ($sth, $sql, $dbh, $errmsg ) ;
my ($fname,$lname,$address,$address2,$city,$state,$zip,$phone,$email_addr);
my ($user_type, $max_names, $max_mailings, $status, $pmode, $puserid);
my ($password, $username, $old_username);
my ($pmesg, $old_email_addr) ;
my $company;
my $website_url;
my $company_phone;
my $images = $util->get_images_url;
my $admin_user;
my $account_type;
my $privacy_policy_url;
my $unsub_option;
my $name;
my $internal_email_addr;
my $physical_addr;
my $comp_name;
my $adv_url;

$pmesg="";
srand();
my $rid=rand();
my $cstatus;
my $priority;

#----------- check for login ------------------
my $user_id = util::check_security();
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}

# for testing
##my $user_id = 129; # type N
#my $user_id = 8; # type A

    #---------------------------------------------------
    # Get the information about the user from the form
    #---------------------------------------------------
    $name = $query->param('name');
    my $company_id = $query->param('company_id');
    my $contact_id = $query->param('contact_id');
    my $manager_id = $query->param('manager_id');
	if ($manager_id eq "")
	{
		$manager_id=0;
	}
	if ($contact_id eq "")
	{
		$contact_id=0;
	}
    my $website_id= $query->param('website_id');
	if ($website_id eq "")
	{
		$website_id=0;
	}
    $pmode = $query->param('pmode');
	$cstatus = $query->param('cstatus');
	$priority= $query->param('priority');
    $puserid = $query->param('puserid');

    #---- Set to Upper Case ----------
    $pmode = uc($pmode) ;
    open (LOG, "> /tmp/util.log");
    # make sure logfile is not buffered
    my $curhandle = select(LOG);
    $| = 1;
    select($curhandle);
    my $cdate = localtime();
    print LOG "starting at $cdate\n";
	print LOG "Name $name\n";
	print LOG "Mode $pmode\n";

&validate_modes();
	print LOG "Mode $pmode\n";


my ($dbhq,$dbhu)=$util->get_dbh();

# for testing
#my ($dbhq,$dbhu);
#$dbhq = DBI->connect("DBI:mysql:new_mail", "edan", "sYX867");
#$dbhu = $dbhq;

if ($pmode eq "A" )
{
	&insert_advertiser();
}
else
{
	print LOG "updating record\n";
	&update_advertiser();
}
	close LOG;

# go to next screen

$util->clean_up();
print "Cache-Control: no-cache\n";
print "Pragma: no-cache\n";
print "Expires: 0\n";
print "Location: /cgi-bin/advertiser_disp2.cgi?pmode=$pmode&puserid=$puserid&pmesg=$pmesg&rid=$rid\n\n";
exit(0);


#
#
#===============================================================================
# Sub: validate_modes - Valid modes are 'A' Add, and 'U' Update - else Stop
#===============================================================================
sub validate_modes
{
	my($go_home, $go_back, $mesg) ;
	#--------------------------------
	# get CGI Form fields
	#--------------------------------
#	my $puserid = $query->param('puserid');
#	$pmode = uc($pmode);
	if ( $pmode ne "A"  and  $pmode ne "U" ) 
	{	#---- Invalid MODE - Mode MUST = 'A' (add)  or  'U' (update)  ---------
		$go_back = qq{<br><a href="$ENV{'HTTP_REFERER'}">Back</a>\n };
 		$go_home = qq{&nbsp;&nbsp;<a href="mainmenu.cgi?userid=$user_id">Home</a>\n };
		$mesg = qq{<br><br><b>Invalid</b> Mode: <b>$pmode</b> - The Mode MUST equal 'A' or 'U'.} ;
		$mesg = $mesg . $go_back . $go_home ;
		util::logerror($mesg) ;
	}

} # end sub - validate_modes


#===============================================================================
# Sub: update_advertiser
#===============================================================================
sub update_advertiser
{
	my $rows;

	$sql = "update advertiser_info set advertiser_name='$name', status='$cstatus',company_id=$company_id,contact_id=$contact_id,website_id=$website_id where advertiser_id = $puserid";
	$rows = $dbhu->do($sql);
	if ($cstatus eq "I")
	{
        open (MAIL,"| /usr/sbin/sendmail -t");
        my $from_addr = "Advertiser Set Inactive<info\@zetainteractive.com>";
        print MAIL "From: $from_addr\n";
        print MAIL "To: alerts\@zetainteractive.com\n";
        print MAIL "Subject: Advertiser $name set inactive\n";
        my $date_str = $util->date(6,6);
        print MAIL "Date: $date_str\n";
        print MAIL "X-Priority: 1\n";
        print MAIL "X-MSMail-Priority: High\n";
        print MAIL "\nAdvertiser $name($puserid) has been set inactivate in the tool.\n";
        close(MAIL);
	}
	print LOG "$sql\n";
	
#	if ($dbhu->err() != 0)
#	{
#		my $errmsg = $dbhu->errstr();
#		print LOG "Error: $errmsg\n";
#	    $pmesg = "Error - Updating user record for AdvertiserID: $puserid $errmsg";
#	}
#	else
#	{
	    $pmesg = "Successful UPDATE of Advertiser Info!" ;
#	}

}  # end sub - update_advertiser


#===============================================================================
# Sub: insert_advertiser
#===============================================================================
sub insert_advertiser
{
	my $rows;
	my $test_flag;

	# add user to database

	$test_flag="N";
	if ($cstatus eq "T")
	{
		$cstatus="A";
		$test_flag="Y";
	}
	elsif ($cstatus eq "P")
	{
		$cstatus="I";
		$test_flag="P";
	}
	elsif ($cstatus eq "U")
	{
		$cstatus="A";
		$test_flag="U";
	}
	
	if (($cstatus eq "I") or ($cstatus eq "A" and $test_flag eq "N") or ($cstatus eq "A" and $test_flag eq "P"))
	{
		$priority=1;
	}
	else
	{
		$sql="update advertiser_info set priority=priority+1 where priority >= $priority and ((status not in ('A','I')) or (status='A' and test_flag in ('Y','U'))) and manager_id in (select m1.manager_id from CampaignManager m1,CampaignManager m2 where m1.MemberGroup=m2.MemberGroup and m2.manager_id=$manager_id)";

		$rows=$dbhu->do($sql);
	}
	$sql = "insert into advertiser_info(advertiser_name,status,company_id,contact_id,website_id,active_date,priority,test_flag,manager_id,pass_tracking,sourceInternal,countryID) values('$name','$cstatus',$company_id,$contact_id,$website_id,curdate(),$priority,'$test_flag',$manager_id,'Y','Y',1)"; 
	$sth = $dbhu->do($sql);
	if ($dbhu->err() != 0)
	{
	    $pmesg = "Error - Inserting advertiser record: $sql - $errmsg";
	}
	else
	{
	    $pmesg = "Successful INSERT of Advertiser Info!" ;
	}

	# get id of client just inserted 

	$sql = "select max(advertiser_id) from advertiser_info";
	$sth = $dbhq->prepare($sql);
	$sth->execute();
	($puserid) = $sth->fetchrow_array() ;
	$sth->finish();

	if ($cstatus eq "C")
	{
		$sql="update advertiser_info set pending_date=curdate() where advertiser_id=$puserid";
		$rows = $dbhu->do($sql);
	}
	if (($cstatus eq "A") and ($test_flag eq "N"))
	{
		$sql="update advertiser_info set active_date=curdate() where advertiser_id=$puserid";
		$rows = $dbhu->do($sql);
	}
	if (($cstatus eq "A") and ($test_flag eq "Y")) 
	{
		$sql="update advertiser_info set testing_date=curdate() where advertiser_id=$puserid";
		$rows = $dbhu->do($sql);
	}
	if ($cstatus eq "I")
	{
		$sql="update advertiser_info set inactive_date_set=curdate() where advertiser_id=$puserid";
		$rows = $dbhu->do($sql);
	}
	recalc_priority();
	#
	#$sql="insert into advertiser_from(advertiser_id,advertiser_from) values($puserid,'{{FOOTER_SUBDOMAIN}}')";
	#$sth = $dbhu->do($sql);

	$pmode = "U" ;

}  # end sub - insert_advertiser

sub recalc_priority
{
	my $aid;
	my $priority;
	my $mgroup;

	$sql="select distinct MemberGroup from CampaignManager";
	my $sth2=$dbhu->prepare($sql);
	$sth2->execute();
	while (($mgroup)=$sth2->fetchrow_array())
	{
		$sql="select advertiser_id,priority from advertiser_info where ((status not in ('A','I')) or (status='A' and test_flag in ('Y','U'))) and manager_id in (select manager_id from CampaignManager where MemberGroup='".$mgroup."' order by priority";
		my $sth=$dbhu->prepare($sql);
		$sth->execute();
		my $i=1;
		while (($aid,$priority)=$sth->fetchrow_array())
		{
			if ($priority != $i)
			{
				$sql="update advertiser_info set priority=$i where advertiser_id=$aid";
				my $rows=$dbhu->do($sql);
			}
			$i++;
		}
		$sth->finish();
	}
	$sth2->finish();
}
