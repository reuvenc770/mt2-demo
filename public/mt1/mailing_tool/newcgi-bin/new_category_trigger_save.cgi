#!/usr/bin/perl

# *****************************************************************************************
# new_category_trigger_save.cgi
#
# this page is the save screen from the CategoryTrigger 
#
# History
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $sql;
my $catid;
my $orderID;
my $rows;
my ($aid,$creative_id,$subject_id,$from_id);
my $old_usa_id;
my $campaign_id;

my ($dbhq,$dbhu)=$util->get_dbh();

# check for login

my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}

# get the fields from the form 

my $trigger_type= $query->param('ctype');
my $userid= $query->param('userid');
my $usa_id= $query->param('usa_id');
my @CID=$query->param('cid');
my $submit=$query->param('submit');

if ($submit eq "Save")
{
	$sql="select advertiser_id,creative_id,subject_id,from_id from UniqueScheduleAdvertiser where usa_id=?";
	$sth=$dbhu->prepare($sql);
	$sth->execute($usa_id);
	($aid,$creative_id,$subject_id,$from_id)=$sth->fetchrow_array();
	$sth->finish();
}

if (($submit eq "Delete Client Settings") and ($userid != 0))
{
	$sql="select campaign_id from CategoryTrigger where client_id=?";
	$sth=$dbhu->prepare($sql);
	$sth->execute($userid);
	while (($campaign_id)=$sth->fetchrow_array())
	{
		$sql="update campaign set deleted_date=now() where campaign_id=$campaign_id";
		$rows=$dbhu->do($sql);
	}
	$sql="delete from CategoryTrigger where client_id=$userid";
	$rows=$dbhu->do($sql);
print "Content-type: text/html\n\n";
print<<"end_of_html";
<html>
<head></head>
<body>
<script Language=JavaScript>
document.location="/cgi-bin/new_category_trigger_list.cgi?ctype=$trigger_type&userid=$userid";
</script>
</body>
</html>
end_of_html
exit;
}

foreach my $val (@CID)
{
	($catid,$orderID)=split('\|',$val);
	$sql="select usa_id,campaign_id from CategoryTrigger where category_id=? and trigger_type=? and orderID=? and client_id=?";
	$sth=$dbhu->prepare($sql);
	$sth->execute($catid,$trigger_type,$orderID,$userid);
	if (($old_usa_id,$campaign_id)=$sth->fetchrow_array())
	{
		if ($submit eq "Delete")
		{
			$sql="update campaign set deleted_date=now() where campaign_id=$campaign_id";
			$rows=$dbhu->do($sql);
			$sql="delete from CategoryTrigger where category_id=$catid and trigger_type='$trigger_type' and orderID=$orderID and client_id=$userid";
			$rows=$dbhu->do($sql);
		}
		else
		{
			if ($old_usa_id != $usa_id)
			{
				$sql="update campaign set advertiser_id=$aid,creative1_id=$creative_id,subject1=$subject_id,from1=$from_id where campaign_id=$campaign_id";
				$rows=$dbhu->do($sql);
				$sql="update CategoryTrigger set usa_id=$usa_id where category_id=$catid and trigger_type='$trigger_type' and orderID=$orderID and client_id=$userid";
				$rows=$dbhu->do($sql);
			}
		}
	}
	else
	{
		if ($submit eq "Save")
		{
			my $temp_str="$trigger_type Trigger - Category $catid - $orderID - $userid";
			$sql = "insert into campaign(campaign_name,user_id,status,created_datetime,advertiser_id,creative1_id,subject1,from1,campaign_type) values('$temp_str',1,'T',now(),$aid,$creative_id,$subject_id,$from_id,'TRIGGER')";
			$rows=$dbhu->do($sql);
	
			$sql="select max(campaign_id) from campaign where campaign_name='$temp_str' and status='T'";
	    	$sth = $dbhq->prepare($sql);
	    	$sth->execute();
			($campaign_id)=$sth->fetchrow_array();
			$sth->finish();
			$sql="insert into CategoryTrigger(category_id,usa_id,trigger_type,campaign_id,orderID,client_id) values($catid,$usa_id,'$trigger_type',$campaign_id,$orderID,$userid)";
			$rows=$dbhu->do($sql);
		}
	}
}
print "Content-type: text/html\n\n";
print<<"end_of_html";
<html>
<head></head>
<body>
<script Language=JavaScript>
document.location="/cgi-bin/new_category_trigger_list.cgi?ctype=$trigger_type&userid=$userid";
</script>
</body>
</html>
end_of_html

# exit function

exit(0);
