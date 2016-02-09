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
#
# Get the information about the user from the form 
#
my $temp_id = $query->param('temp_id');
my $backto = $query->param('backto');
my $label = $query->param('typelabel');
my $desc = $query->param('desc');
my $name= $query->param('typename');
$name=~ s/'/''/g; 
$desc=~ s/'/''/g; 
#
#
if ($temp_id > 0)
{
	$sql = "update MailingTemplateType set mailingTemplateTypeLabel='$label',mailingTemplateTypeName='$name',description='$desc' where mailingTemplateTypeID=$temp_id"; 
}
else
{
	$sql="insert into MailingTemplateType(mailingTemplateTypeLabel,mailingTemplateTypeName,description) values('$label','$name','$desc')"; 
}
$sth = $dbhu->do($sql);
if ($dbhu->err() != 0)
{
	my $errmsg = $dbhu->errstr();
   	util::logerror("Updating Headers record for $sql $user_id: $errmsg");
}
else
{
	if ($temp_id == 0)
	{
		$sql="select max(mailingTemplateTypeID) from MailingTemplateType where label='$label'";
		$sth=$dbhu->prepare($sql);
		$sth->execute();
		($temp_id)=$sth->fetchrow_array();
		$sth->finish();
	}

		print "Location: index.cgi\n\n";

}
$util->clean_up();
exit(0);
