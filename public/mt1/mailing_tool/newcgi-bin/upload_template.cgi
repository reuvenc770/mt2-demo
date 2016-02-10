#!/usr/bin/perl
#===============================================================================
# File   : upload_template.cgi 
#
#--Change Control---------------------------------------------------------------
#===============================================================================

#-----------------------
# include Perl Modules
#-----------------------
use strict;
use CGI;
use util;

$|=1 ;   # set OUTPUT_AUTOFLUSH to true

my $util = util->new;
my $query = CGI->new;
my $dbh;
my $sql;
my $cnt;
my $alt_light_table_bg = $util->get_alt_light_table_bg;
my $images = $util->get_images_url;


# ------- Get fields from html Form post -----------------


# ----- check for login -------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}
my $upload_file = $query->param('upload_file');
my ($dbhq,$dbhu)=$util->get_dbh();

#----- Pass control to PROCESS_FILE  or  PROCESS_LIST  -------
if ( $upload_file ne "" ) 
{
	&process_file() ;
}
else
{
	print "Location: template_list.cgi\n\n";
}
exit(0) ;


sub process_file 
{
	my ($file_name, $file_handle, $file_problem, $file_in, $line, @rest_of_line);
	my $sql;
	my $sth;
	my $sth1;
	my $upload_dir_unix;
	my $typeID;
	my $rows;

	print "Content-type: text/html\n\n";
	print<<"end_of_html";
<html><head><title>Template Upload Results</title></head>
<body>
<center>
<table>
end_of_html
	# get upload subdir
	$sql = "select parmval from sysparm where parmkey = 'UPLOAD_DIR_UNIX'";
	$sth1 = $dbhq->prepare($sql) ;
	$sth1->execute();
	($upload_dir_unix) = $sth1->fetchrow_array();
	$sth1->finish();
	$sql="select mailingTemplateTypeID from MailingTemplateType where mailingTemplateTypeLabel='general'";
	$sth1 = $dbhq->prepare($sql) ;
	$sth1->execute();
	($typeID) = $sth1->fetchrow_array();
	$sth1->finish();


	# deal with filename passed to this script

	if ( $upload_file =~ /([^\/\\]+)$/ ) 
	{
		$file_name = $1;                # set file_name to $1 var - (file-name no path)
		$file_name =~ s/^\.+//;         # say what...
		$file_name =~ s/\s/_/g;         # replace WhiteSpace with UnderScore global
		$file_handle = $upload_file ;
	}
	else 
	{
		$file_problem = $query->param('upfile');
		&error("Bad File Name: $file_problem, File name can't have a slash in it!\n Rename it and try again!" ) ;
		exit(0);
	}

	#---- Open file and save File to Unix box ---------------------------

	$file_in = "${upload_dir_unix}template.${user_id}" ;
	open(SAVED,">$file_in") || &logerror("Error - could NOT open Output SAVED file: $file_in");
	$file_handle = $upload_file ;
	print SAVED <$file_handle> ;
	close SAVED;

    my ($sec, $min, $hr, $day, $month, $year, $wkdy, $yrdy, $isDST)=localtime();
    $month+=1; $year+=1900;
	my $dstr=$month."_".$day;
	$cnt=1;

	my $got_style=0;
	my $template_str="";
	open(SAVED,"<$file_in") || &logerror("Error - could NOT open Input SAVED file: $file_in");
	while (<SAVED>) 
	{
		$line = $_;
		if ($got_style)
		{
			if (/<\/style>/)
			{
				my $tcnt;
				if ($cnt < 10)
				{
					$tcnt="0".$cnt;
				}
				else
				{
					$tcnt=$cnt;
				}
				$template_str.=$line;
				my $temp_name="hotmailcode_".$dstr."_".$tcnt;
				$sql="insert into brand_template(template_name,date_added,status,html_code,mailingtemplateTypeID) values('$temp_name',curdate(),'A','$template_str',$typeID)";
				$rows=$dbhu->do($sql);
				print "<tr><td>Template $temp_name added </td></tr>\n";
				$template_str="";
				$cnt++;
				$got_style=0;
			}
			else
			{
				$template_str.=$line;
			}
		}
		else
		{
			$_=$line;
			if (/<style>/)
			{
				$got_style=1;
				$template_str.=$line;
			}
		}
	} 
	close SAVED;
	close LOG;
	unlink($file_in) || &logerror("Error - could NOT Remove file: $file_in");  # del file_in
	print<<"end_of_html";
</table>
<br><a href="template_list.cgi">Back to Template List</a>
</center>
</body>
</html>
end_of_html
} # end of sub

