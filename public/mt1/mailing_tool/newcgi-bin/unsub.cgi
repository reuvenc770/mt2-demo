#!/usr/bin/perl
# *****************************************************************************************
# unsubscribe_save.cgi
#
# this page saves the users "unsubscribe" requests
#
# History
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;
use Data::Dumper;
use Lib::Database::Perl::Interface::Unsubscribe;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $dbh;
my $eid = $query->param('eid');
my $global = $query->param('global');
my $suppressionReasonCode = $query->param('suppressionReasonCode');
my $list_id;
my $mesg;
my $iopt;
my $rows;
my $email_addr;
my $images = $util->get_images_url;
my $cstatus;
my $sth1;
my $tid;
my $client_id;
my $teid;
my $params;
my $tlist_id;

# connect to the util database
my ($dbhq,$dbhu)=$util->get_dbh();
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}
	my $unsubscribeInterface = Lib::Database::Perl::Interface::Unsubscribe->new(('write' => 1));

	my $sql = "select email_addr,status,client_id from email_list where email_user_id = $eid"; 
	$sth = $dbhq->prepare($sql);
	$sth->execute();
	($email_addr,$cstatus,$client_id) = $sth->fetchrow_array();
	$sth->finish;
	
	## we didnt find it check the archives
	##this only matters for global suppression
	if(
		!$email_addr 
		&& 
		($global eq 'Y')
	)
	{
		my $dbh = DBI->connect("DBI:mysql:new_mail:dbarchive.i.routename.com","db_readuser","Tr33Wat3r");
		
		my $sql = "select email_addr,status,client_id from email_list_2013 where email_user_id = $eid"; 
		
		my $sth = $dbhq->prepare($sql);
		
		$sth->execute();
		
		($email_addr,$cstatus,$client_id) = $sth->fetchrow_array();
	}
	
if ($email_addr) 
{
	$mesg = "Email Address " . $email_addr. " Removed";

	if (($global eq "Y") or ($global eq "D"))
	{
    	$sql = "select client_id,email_user_id from email_list where email_addr='$email_addr' and status='A'"; 
        $sth1 = $dbhq->prepare($sql) ;
        $sth1->execute();
        while (($tid,$teid) = $sth1->fetchrow_array())
        {
        	$sql = "insert into manual_removal(email_addr,removal_date,client_id) values('$email_addr',now(),$tid)";
            $rows = $dbhu->do($sql) ;
			my $errors = $unsubscribeInterface->logUnsubscribe({'emailAddress'=>$email_addr, 'client_id' => $tid});
        }
        $sth1->finish();	
	}
	if ($cstatus eq "A")
	{
		my $errors = $unsubscribeInterface->unsubscribeEidAll({'eID'=>$eid});
		$errors = $unsubscribeInterface->logUnsubscribe({'emailAddress'=>$email_addr, 'client_id' => $client_id});
	}
#
	if (($global eq "Y") or ($global eq "D"))
	{
		my $errors = $unsubscribeInterface->unsubscribeAll({'emailAddress'=>$email_addr});
		
		util::addGlobal( { 'emailAddress' => $email_addr, 'suppressionReasonCode' =>  $suppressionReasonCode } );

		$errors = $unsubscribeInterface->unsubscribeUniques({'emailAddress'=>$email_addr});
		$mesg = "Email Address " . $email_addr. " added to Suppression List";
	}
}
$sth->finish();

# print out the html page

print "Location: /cgi-bin/show_info_id.cgi?name=$eid&mesg=$mesg\n\n";
