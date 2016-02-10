#!/usr/bin/perl

# *****************************************************************************************
# category_triggers_params.cgi
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
my $userid = $query->param('userid');
my $trigger_cnt= $query->param('trigger_cnt');
my $trigger_delay= $query->param('trigger_delay');
if ($userid == 0)
{
	$sql="update sysparm set parmval='$trigger_cnt' where parmkey='".$trigger_type."_TRIGGER_CNT'";
	$rows=$dbhu->do($sql);
	$sql="update sysparm set parmval='$trigger_delay' where parmkey='".$trigger_type."_TRIGGER_DELAY'";
	$rows=$dbhu->do($sql);
}
else
{
	my $lctype = lc $trigger_type;
	my $fld=$trigger_type."_trigger_cnt";
	my $fld1=$trigger_type."_trigger_delay";
	$sql="update user set $fld=$trigger_cnt,$fld1=$trigger_delay where user_id=$userid";
	$rows=$dbhu->do($sql);
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
