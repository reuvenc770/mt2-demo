#!/usr/bin/perl

# *****************************************************************************************
# block_upd.cgi
#
# this page updates information in the block table
#
# History
# Jim Sobeck, 06/15/07, Creation
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
my $dbh;
my $sid;
my $errmsg;
my $images = $util->get_images_url;
my $csubject;
my @subject_array;

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
my $bid = $query->param('bid');
my $vid = $query->param('vid');
my $bname= $query->param('bname');
my $bhost= $query->param('bhost');
my $mailing1= $query->param('mailing1');
my $mailing2= $query->param('mailing2');
#
# Update record into block 
#
$bname=~ s/'/''/g;
if ($bid > 0)
{
	$sql = "update block set block_name='$bname',block_host='$bhost',variation_id='$vid',mailing_addr1='$mailing1',mailing_addr2='$mailing2' where block_id=$bid";
}
else
{
	$sql="insert into block(block_name,block_host,variation_id,mailing_addr1,mailing_addr2,status) values('$bname','$bhost',$vid,'$mailing1','$mailing2','A')";
}
$sth = $dbhu->do($sql);
#
# Display the confirmation page
#
print "Content-type: text/html\n\n";
print<<"end_of_html";
<html>
<head></head>
<body>
<script language="JavaScript">
document.location="/cgi-bin/block_list.cgi";
</script>
</body></html>
end_of_html
$util->clean_up();
exit(0);
