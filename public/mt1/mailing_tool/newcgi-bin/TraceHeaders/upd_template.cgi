#!/usr/bin/perl

# *****************************************************************************************
# upd_tempalte.cgi
#
# this page updates information in the brand_template table
#
# History
# Jim Sobeck, 06/04/07, Creation
# *****************************************************************************************

# include Perl Modules
use lib('/var/www/html/newcgi-bin');
use strict;
use CGI;
use util;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $sql;
my $dbh;
my $errmsg;
my $images = $util->get_images_url;
my $camp_id;
my $pmesg;
my $rid;

# connect to the util database
my ($dbhq,$dbhu)=$util->get_dbh();

# check for login
my $user_id = util::check_security();
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
	$util->clean_up();
    exit(0);
}

$util->getUserData({'userID' => $user_id});

my $userDataRestrictionWhereClause = '';

my $externalUser = $util->getUserData()->{'isExternalUser'};

if($util->getUserData()->{'isExternalUser'} == 1)
{
	$userDataRestrictionWhereClause = qq|
        userID = $user_id AND
    |;
}



#
# Get the information about the user from the form 
#
my $temp_id = $query->param('temp_id');
my $backto = $query->param('backto');
my $template_name = $query->param('template_name');
$template_name=~ s/'/''/g; 
my $nl_template= $query->param('nl_template');
if ($nl_template ne "")
{
}
else
{
	$nl_template='';
}
$nl_template=~ s///g; 
$nl_template=~s/\n\n*/\n/g;
$nl_template=~ s/'/''/g; 
#
#
if ($temp_id > 0)
{
	$sql = "update TraceHeaders set header_name='$template_name',header_code='$nl_template' where $userDataRestrictionWhereClause header_id=$temp_id"; 
}
else
{
	$sql="insert into TraceHeaders(userID, header_name,dateAdded,status,header_code) values($user_id, '$template_name',curdate(),'A','$nl_template')"; 
}
$sth = $dbhu->do($sql);
if ($dbhu->err() != 0)
{
	my $errmsg = $dbhu->errstr();
   	util::logerror("Updating TraceHeaders record for $sql $user_id: $errmsg");
}
else
{
	if ($temp_id == 0)
	{
		$sql="select max(header_id) from TraceHeaders where $userDataRestrictionWhereClause header_name='$template_name' and status='A'";
		$sth=$dbhu->prepare($sql);
		$sth->execute();
		($temp_id)=$sth->fetchrow_array();
		$sth->finish();
	}

		print "Location: index.cgi\n\n";

}
$util->clean_up();
exit(0);
