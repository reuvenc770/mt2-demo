#!/usr/bin/perl

# ******************************************************************************
# mta_save_settings.cgi 
#
# this page updates information in the mta_detail table
#
# History
# ******************************************************************************

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
my $i;
my $class;
my $errmsg;
my $images = $util->get_images_url;
my $pmesg="";
my @url_array;
my $cnt;
my $curl;
my $temp_cnt;
my $btype;
my $nl_id;

$cnt=0;
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
# Remove old url information
#
my $mta_id= $query->param('mta_id');
my $submit= $query->param('submit');
my $inj_qty= $query->param('inj_qty');
my $pause_time= $query->param('pause_time');
my $variance = $query->param('variance');
my $ip_rotation_type= $query->param('ip_rotation_type');
my $ip_rotation_times= $query->param('ip_rotation_times');
if ($ip_rotation_type eq "per mailing")
{
	$ip_rotation_times=1;
}
my $domain_type= $query->param('domain_type');
my $domain_times= $query->param('domain_times');
if ($domain_type eq "per mailing")
{
	$domain_times=1;
}
my $fromline_type= $query->param('fromline_type');
my $fromline_times= $query->param('fromline_times');
if ($fromline_type eq "per mailing")
{
	$fromline_times=1;
}
my $subjectline_type= $query->param('subjectline_type');
my $subjectline_times= $query->param('subjectline_times');
if ($subjectline_type eq "per mailing")
{
	$subjectline_times=1;
}
my $oneliner_type= $query->param('oneliner_type');
my $oneliner_times= $query->param('oneliner_times');
if ($oneliner_type eq "per mailing")
{
	$oneliner_times=1;
}
my $action_type= $query->param('action_type');
my $action_times= $query->param('action_times');
if ($action_type eq "per mailing")
{
	$action_times=1;
}
my @template_id=$query->param('template_id');
my @header_id=$query->param('header_id');
my @footer_id=$query->param('footer_id');
my @content_domain=$query->param('content_domain');
my $header_template_id=$query->param('header_template_id');
my $encrypt_link=$query->param('encrypt_link');
if ($encrypt_link eq "")
{
	$encrypt_link="Y";
}
my $newMailing=$query->param('newMailing');
if ($newMailing eq "")
{
	$newMailing="Y";
}
my $use_random_batch=$query->param('use_random_batch');
if ($use_random_batch eq "")
{
	$use_random_batch="N";
}
my $creative_type= $query->param('creative_type');
my $creative_type= $query->param('creative_type');
my $creative_times= $query->param('creative_times');
if ($creative_type eq "per mailing")
{
	$creative_times=1;
}
my $seed_type = $query->param('seed_type');
my $seed_times= $query->param('seed_times');
if ($seed_times eq "")
{
	$seed_times=1;
}
if ($seed_type eq "per mailing")
{
	$seed_times=1;
}
my $seedlist = $query->param('seedlist');
my $max_records_per_ip= $query->param('max_records_per_ip');
my $ramp_up= $query->param('ramp_up');
if ($ramp_up eq "")
{
	$ramp_up="N";
}
my $wiki_id= $query->param('wiki_id');
my @isps= $query->param('isps');
foreach my $isp (@isps)
{
	if ($submit eq "restore defaults")
	{
		$sql="delete from mta_detail where mta_id=$mta_id and class_id=$isp";
    	my $rows=$dbhu->do($sql);
		$sql="delete from mta_templates where mta_id=$mta_id and class_id=$isp";
    	my $rows=$dbhu->do($sql);
		$sql="insert into mta_detail select $mta_id,class_id,inj_qty,pause_time,max_records_per_ip,ip_rotation_type,ip_rotation_times,domain_type,domain_times,fromline_type,fromline_times,subjectline_type,subjectline_times,creative_type,creative_times,seed_times,seedlist,seed_type,wikID,ramp_up,encrypt_link,use_random_batch,variance,oneliner_type,oneliner_times,action_type,action_times,mailingHeaderID,userID,newMailing from mta_detail where mta_id=1 and class_id=$isp";
    	$rows=$dbhu->do($sql);
		$sql="insert into mta_templates select $mta_id,class_id,template_id from mta_templates where mta_id=1 and class_id=$isp";
    	$rows=$dbhu->do($sql);
		$sql="insert into mta_headers select $mta_id,class_id,header_id from mta_headers where mta_id=1 and class_id=$isp";
    	$rows=$dbhu->do($sql);
		$sql="insert into mta_footers select $mta_id,class_id,footer_id from mta_footers where mta_id=1 and class_id=$isp";
    	$rows=$dbhu->do($sql);
		$sql="insert into mta_content_domain select $mta_id,class_id,domain_name from mta_content_domain where mta_id=1 and class_id=$isp";
    	$rows=$dbhu->do($sql);
	}
	else
	{
		if (($ip_rotation_times <= 0) or ($domain_times <= 0) or ($fromline_times <= 0) or ($subjectline_times <= 0) or ($creative_times <= 0) or ($oneliner_times <= 0) or ($action_times<= 0))
		{
print "Content-type: text/html\n\n";
print<<"end_of_html";
<html><head></head>
<body>
<center>
<h3> One or more of the time(s) variables is less than or equal to zero which is not allow.  ISP MTA Settings have not been updated.</h3>
</center>
</body>
</html>
end_of_html
exit(0);
		}
    	$sql="update mta_detail set inj_qty=$inj_qty,max_records_per_ip=$max_records_per_ip,pause_time=$pause_time,ip_rotation_type='$ip_rotation_type',ip_rotation_times=$ip_rotation_times,domain_type='$domain_type',domain_times=$domain_times,fromline_type='$fromline_type',fromline_times=$fromline_times,subjectline_type='$subjectline_type',subjectline_times=$subjectline_times,creative_type='$creative_type',creative_times=$creative_times,seed_times=$seed_times,seedlist='$seedlist',seed_type='$seed_type',wikiID=$wiki_id,ramp_up='$ramp_up',encrypt_link='$encrypt_link',use_random_batch='$use_random_batch',variance=$variance,oneliner_type='$oneliner_type',oneliner_times=$oneliner_times,action_type='$action_type',action_times=$action_times,mailingHeaderID=$header_template_id,newMailing='$newMailing' where mta_id=$mta_id and class_id=$isp";
    	my $rows=$dbhu->do($sql);
		$sql="delete from mta_templates where mta_id=$mta_id and class_id=$isp";
    	my $rows=$dbhu->do($sql);
    	my $i=0;
    	while ($i <= $#template_id)
    	{
        	$sql="insert into mta_templates(mta_id,class_id,template_id) values($mta_id,$isp,$template_id[$i])";
        	$rows=$dbhu->do($sql);
        	$i++;
    	}
		$sql="delete from mta_headers where mta_id=$mta_id and class_id=$isp";
    	my $rows=$dbhu->do($sql);
    	my $i=0;
    	while ($i <= $#header_id)
    	{
        	$sql="insert into mta_headers(mta_id,class_id,header_id) values($mta_id,$isp,$header_id[$i])";
        	$rows=$dbhu->do($sql);
        	$i++;
    	}
		$sql="delete from mta_footers where mta_id=$mta_id and class_id=$isp";
    	my $rows=$dbhu->do($sql);
    	my $i=0;
    	while ($i <= $#footer_id)
    	{
        	$sql="insert into mta_footers(mta_id,class_id,footer_id) values($mta_id,$isp,$footer_id[$i])";
        	$rows=$dbhu->do($sql);
        	$i++;
    	}
		$sql="delete from mta_content_domain where mta_id=$mta_id and class_id=$isp";
    	my $rows=$dbhu->do($sql);
    	my $i=0;
    	while ($i <= $#content_domain)
    	{
			if ($content_domain[$i] ne "")
			{
        		$sql="insert into mta_content_domain(mta_id,class_id,domain_name) values($mta_id,$isp,'$content_domain[$i]')";
        		$rows=$dbhu->do($sql);
			}
        	$i++;
    	}
	}
}
print "Content-type: text/html\n\n";
print<<"end_of_html";
<html><head></head>
<body>
<center>
<h3> ISP(s) MTA Settings have been updated</h3>
</center>
</body>
</html>
end_of_html
exit(0);
