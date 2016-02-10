#!/usr/bin/perl

# *****************************************************************************************
# creative_update.cgi
#
#
# History
# *****************************************************************************************

# include Perl Modules

use strict;
use util_mail;
use util;

my $util = util->new;
my $query = CGI->new;
my $dbh;
my $sth;
my $sth1;
my $sql;
my $rows;
my $s= $query->param('s');
my @chkbox= $query->param('chkbox');
my $function= $query->param('function');
my $cadv= $query->param('cadv');
my $asset = $query->param('asset');
my $find_str = $query->param('find_str');
my $replace_str = $query->param('replace_str');
my $the_rest;
my ($dbhq,$dbhu)=$util->get_dbh();

# check for login
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}

foreach my $cid (@chkbox)
{
	if ($function eq "FIND")
	{
		if ($asset eq "Creative")
		{
			$sql="update creative set html_code=replace(html_code,'$find_str','$replace_str') where creative_id=$cid"; 
		}
		elsif ($asset eq "Subject")
		{
			$sql="update advertiser_subject set advertiser_subject=replace(advertiser_subject,'$find_str','$replace_str') where subject_id=$cid"; 
		}
		elsif ($asset eq "From")
		{
			$sql="update advertiser_from set advertiser_from=replace(advertiser_from,'$find_str','$replace_str') where from_id=$cid"; 
		}
		$rows=$dbhu->do($sql);
	}
	elsif ($function eq "Active")
	{
		if ($asset eq "Creative")
		{
			$sql="update creative set status='A',inactive_date='0000-00-00' where creative_id=$cid and status!='A'";
		}
		elsif ($asset eq "Subject")
		{
			$sql="update advertiser_subject set status='A',inactive_date='0000-00-00' where subject_id=$cid and status!='A'";
		}
		elsif ($asset eq "From")
		{
			$sql="update advertiser_from set status='A',inactive_date='0000-00-00' where from_id=$cid and status!='A'";
		}
		$rows=$dbhu->do($sql);
	}
	elsif ($function eq "Delete")
	{
		if ($asset eq "Creative")
		{
			$sql="update creative set status='D' where creative_id=$cid and status!='D'";
		}
		elsif ($asset eq "Subject")
		{
			$sql="update advertiser_subject set status='D' where subject_id=$cid and status!='D'";
		}
		elsif ($asset eq "From")
		{
			$sql="update advertiser_from set status='D' where from_id=$cid and status!='D'";
		}
		$rows=$dbhu->do($sql);
	}
	elsif ($function eq "Inactive")
	{
		my $dcnt;
		if ($find_str eq "0000-00-00")
		{
			$dcnt=-1;
		}
		else
		{
        	$sql="select datediff(curdate(),'$find_str')"; 
        	$sth1 = $dbhq->prepare($sql);
        	$sth1->execute($cid);
        	($dcnt) = $sth1->fetchrow_array();
        	$sth1->finish;
		}
		if ($asset eq "Creative")
		{
			if ($dcnt >= 0)
			{
				$sql="update creative set status='I',inactive_date='$find_str' where creative_id=$cid and status != 'I'";
			}
			else
			{
				$sql="update creative set inactive_date='$find_str' where creative_id=$cid";
			}
		}
		elsif ($asset eq "Subject")
		{
			if ($dcnt >= 0)
			{
				$sql="update advertiser_subject set status='I',inactive_date='$find_str' where subject_id=$cid and status != 'I'";
			}
			else
			{
				$sql="update advertiser_subject set inactive_date='$find_str' where subject_id=$cid";
			}
		}
		elsif ($asset eq "From")
		{
			if ($dcnt >= 0)
			{
				$sql="update advertiser_from set status='I',inactive_date='$find_str' where from_id=$cid and status != 'I'";
			}
			else
			{
				$sql="update advertiser_from set inactive_date='$find_str' where from_id=$cid";
			}
		}
		$rows=$dbhu->do($sql);
	}
	elsif ($function eq "ADD_NAME")
	{
		if ($asset eq "Creative")
		{
			$sql="update creative set creative_name=concat('$find_str',creative_name) where creative_id=$cid"; 
		}
		elsif ($asset eq "Subject")
		{
			$sql="update advertiser_subject set advertiser_subject=concat('$find_str',advertiser_subject) where subject_id=$cid"; 
		}
		elsif ($asset eq "From")
		{
			$sql="update advertiser_from set advertiser_from=concat('$find_str',advertiser_from) where from_id=$cid"; 
		}
		$rows=$dbhu->do($sql);
	}
	elsif ($function eq "CHG_NAME")
	{
		if ($asset eq "Creative")
		{
			$sql="update creative set creative_name=replace(creative_name,'$find_str','$replace_str') where creative_id=$cid"; 
		}
		elsif ($asset eq "Subject")
		{
			$sql="update advertiser_subject set advertiser_subject=replace(advertiser_subject,'$find_str','$replace_str') where subject_id=$cid"; 
		}
		elsif ($asset eq "From")
		{
			$sql="update advertiser_from set advertiser_from=replace(advertiser_from,'$find_str','$replace_str') where from_id=$cid"; 
		}
		$rows=$dbhu->do($sql);
	}
	elsif ($function eq "COPY_ADV")
	{
		if ($asset eq "Creative")
		{
			$sql="insert into creative(advertiser_id,status,creative_name,original_flag,trigger_flag,approved_flag,creative_date,inactive_date,unsub_image,thumbnail,html_code,content_id,header_id,body_content_id,style_id,replace_flag,mediactivate_flag,hitpath_flag,comm_wizard_c3,comm_wizard_cid,comm_wizard_progid,cr,landing_page,internal_approved_flag,copywriter,copywriter_name,original_html,host_images,needsProcessing) select $cadv,status,concat(creative_name,'D'),original_flag,trigger_flag,'N',curdate(),inactive_date,unsub_image,thumbnail,html_code,content_id,header_id,body_content_id,style_id,replace_flag,mediactivate_flag,hitpath_flag,comm_wizard_c3,comm_wizard_cid,comm_wizard_progid,cr,landing_page,'N',copywriter,copywriter_name,original_html,host_images,needsProcessing from creative where creative_id=$cid";
		}
		elsif ($asset eq "Subject")
		{
			$sql="insert into advertiser_subject(advertiser_id,advertiser_subject,approved_flag,original_flag,status,date_approved,approved_by,inactive_date,internal_approved_flag,internal_date_approved,internal_approved_by,copywriter,copywriter_name) select $cadv,advertiser_subject,approved_flag,original_flag,status,date_approved,approved_by,inactive_date,internal_approved_flag,internal_date_approved,internal_approved_by,copywriter,copywriter_name from advertiser_subject where subject_id=$cid";
		}
		elsif ($asset eq "From")
		{
			$sql="insert into advertiser_from(advertiser_id,advertiser_from,approved_flag,original_flag,status,date_approved,approved_by,inactive_date,internal_approved_flag,internal_date_approved,internal_approved_by,copywriter,copywriter_name) select $cadv,advertiser_from,approved_flag,original_flag,status,date_approved,approved_by,inactive_date,internal_approved_flag,internal_date_approved,internal_approved_by,copywriter,copywriter_name from advertiser_from where from_id=$cid";
		}
		$rows=$dbhu->do($sql);
	}
}
print "Location: /cgi-bin/creative_findreplace_save.cgi?s=$s&asset=$asset\n\n";
