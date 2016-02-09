#!/usr/bin/perl
#===============================================================================
# File   : sm_upload_brand.cgi 
#
#--Change Control---------------------------------------------------------------
#===============================================================================

#-----------------------
# include Perl Modules
#-----------------------
use strict;
use CGI;
use util;
use Date::Manip;

$|=1 ;   # set OUTPUT_AUTOFLUSH to true

my $util = util->new;
my $query = CGI->new;
my $dbh;
my $sth;
my $rows;
my $sql;
my $alt_light_table_bg = $util->get_alt_light_table_bg;
my $images = $util->get_images_url;

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
	print "Location: sm_brand.cgi\n\n";
}
exit(0) ;


sub process_file 
{
	my ($file_name, $file_handle, $file_problem, $file_in, $line, @rest_of_line);
	my ($brandName,$clientName,$block,$templateName,$unsub_footer_image,$domain,$rdns,$useFuture,$includeWiki,$numDomains,$purpose,$generateSpf);
	my $enableAuto;
	my $client_id;
	my $block_id;
	my $template_id;
	my $exclude_wiki;

	print "Content-type: text/html\n\n";
	print<<"end_of_html";
<html><head><title>Brand Upload Results</title></head>
<body>
<center>
<table>
end_of_html
	# get upload subdir
	my $upload_dir_unix;
	$sql = "select parmval from sysparm where parmkey = 'UPLOAD_DIR_UNIX'";
	my $sth1 = $dbhq->prepare($sql) ;
	$sth1->execute();
	($upload_dir_unix) = $sth1->fetchrow_array();
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

	$file_in = "${upload_dir_unix}brand.${user_id}" ;
	open(SAVED,">$file_in") || &logerror("Error - could NOT open Output SAVED file: $file_in");
	$file_handle = $upload_file ;
	print SAVED <$file_handle> ;
	close SAVED;

    my ($sec, $min, $hr, $day, $month, $year, $wkdy, $yrdy, $isDST)=localtime();
    $month+=1; $year+=1900;
	open(LOG,">>/tmp/upload_brand_$month$day$year.log");
	print LOG "$hr:$sec - $user_id\n";
	open(SAVED,"<$file_in") || &logerror("Error - could NOT open Input SAVED file: $file_in");
	while (<SAVED>) 
	{
		chomp;                       # remove Carriage Return (if exists)
		$line = $_;
		$line =~ s///g ;      # remove ^M from Email Addr (if exists)
		$line =~ s/\t/|/g ;
		$line =~ s/,/|/g ;
		print LOG "$hr:$sec - $user_id - <$line>\n";
		($brandName,$clientName,$block,$templateName,$unsub_footer_image,$domain,$rdns,$enableAuto,$useFuture,$includeWiki,$numDomains,$purpose,$generateSpf)=split('\|',$line);
		if ($brandName eq "Brand Name:")
		{
			next;
		}
		# 
		# check for client name
		#
		$sql="select user_id from user where status='A' and company=?";
		$sth=$dbhu->prepare($sql);
		$sth->execute($clientName);
		if (($client_id)=$sth->fetchrow_array())
		{
		}
		else
		{
            print "<tr><td><font color=red>Brand $brandName not added because invalid client: $clientName</font></td></tr>\n";
            next;
		}
		$sth->finish();
		$sql="select block_id from block where status='A' and block_name=?";
		$sth=$dbhu->prepare($sql);
		$sth->execute($block);
		if (($block_id)=$sth->fetchrow_array())
		{
		}
		else
		{
            print "<tr><td><font color=red>Brand $brandName not added because invalid block: $block</font></td></tr>\n";
            next;
		}
		$sth->finish();
		$sql="select template_id from brand_template where status='A' and template_name=?";
		$sth=$dbhu->prepare($sql);
		$sth->execute($templateName);
		if (($template_id)=$sth->fetchrow_array())
		{
		}
		else
		{
            print "<tr><td><font color=red>Brand $brandName not added because invalid template: $templateName</font></td></tr>\n";
            next;
		}
		$sth->finish();
		if ($domain eq "")
		{
            print "<tr><td><font color=red>Brand $brandName not added because Website Domain is blank</font></td></tr>\n";
            next;
		}
		$domain = lc $domain;
    	if ($domain =~ /[^a-zA-Z0-9\.\-]/)
    	{
            print "<tr><td><font color=red>Brand $brandName not added because Website Domain has invalid characters: $domain</font></td></tr>\n";
            next;
		}
		if ($rdns eq "")
		{
            print "<tr><td><font color=red>Brand $brandName not added because Rdns Urls is blank</font></td></tr>\n";
            next;
		}
		if (($enableAuto eq "Yes") or ($enableAuto eq "No"))
		{
			if ($enableAuto eq "Yes")
			{
				$enableAuto=1;
			}
			else
			{
				$enableAuto=0;
			}
		}
		else
		{
            print "<tr><td><font color=red>Brand $brandName not added because Enable Auto value is invalid: $enableAuto</font></td></tr>\n";
            next;
		}
		if (($useFuture eq "Yes") or ($useFuture eq "No"))
		{
			$useFuture=substr($useFuture,0,1);
		}
		else
		{
            print "<tr><td><font color=red>Brand $brandName not added because Use Future value is invalid: $useFuture</font></td></tr>\n";
            next;
		}
		if (($includeWiki eq "Yes") or ($includeWiki eq "No"))
		{
			$includeWiki=substr($includeWiki,0,1);
			$exclude_wiki="Y";
			if ($includeWiki eq "Y")
			{
				$exclude_wiki="N";
			}
		}
		else
		{
            print "<tr><td><font color=red>Brand $brandName not added because Include Wiki value is invalid: $includeWiki</font></td></tr>\n";
            next;
		}
		if ($numDomains eq "")
		{
            print "<tr><td><font color=red>Brand $brandName not added because Domain Count value is empty </font></td></tr>\n";
            next;
		}
		if (($purpose ne "Normal") and ($purpose ne "Daily") and ($purpose ne "Trigger"))
		{
            print "<tr><td><font color=red>Brand $brandName not added because Purpose value is invalid: $purpose</font></td></tr>\n";
            next;
		}
		if (($generateSpf eq "Yes") or ($generateSpf eq "No"))
		{
			$generateSpf=substr($generateSpf,0,1);
		}
		else
		{
            print "<tr><td><font color=red>Brand $brandName not added because Generate Spf value is invalid: $generateSpf</font></td></tr>\n";
            next;
		}
		# Add brand
		$brandName=~tr/[0-9][a-z][A-Z]\-_/ /c;
		$brandName=~s/ //g;
		my $ns1="ns1.".$domain;
		my $ns2="ns2.".$domain;
		my $whois_email="info@".$domain;
		my $abuse_email="abuse@".$domain;
		my $personal_email="john@".$domain;
		my $vid;
		my $tdir;
		my $mailing_addr1;
		my $mailing_addr2;
		my $block_host;
		$sql="select block_host,variation_id,mailing_addr1,mailing_addr2 from block where block_id=$block_id";
		$sth=$dbhu->prepare($sql);
		$sth->execute();
		($block_host,$vid,$mailing_addr1,$mailing_addr2)=$sth->fetchrow_array();
		$sth->finish(); 
		#
		my $footer_font_id;
		my $footer_color_id;
		my $footer_bg_color_id;
		$sql = "select font_id from fonts order by rand() limit 1";
		$sth = $dbhq->prepare($sql) ;
		$sth->execute();
		($footer_font_id)=$sth->fetchrow_array();
		$sth->finish();
		$sql = "select color_id from colors where color_type='F' order by rand() limit 1"; 
		$sth = $dbhq->prepare($sql) ;
		$sth->execute();
		($footer_color_id)=$sth->fetchrow_array();
		$sth->finish();
		$footer_bg_color_id=8; # White
		#
		# Insert record into client_brand_info 
		#
		$sql="insert into client_brand_info(client_id,brand_name,others_ns1,others_ns2,yahoo_ns1,yahoo_ns2,mailing_addr1,mailing_addr2,whois_email,abuse_email,personal_email,footer_variation,status,footer_font_id,footer_color_id,footer_bg_color_id,brand_type,third_party_id,exclude_subdomain,template_id,replace_domain,num_domains_rotate,exclude_wiki,purpose,generateSpf) values($client_id,'$brandName','$ns1','$ns2','$ns1','$ns2','$mailing_addr1','$mailing_addr2','$whois_email','$abuse_email','$personal_email',$vid,'A',$footer_font_id,$footer_color_id,$footer_bg_color_id,'3rd Party',10,'Y',$template_id,$enableAuto,$numDomains,'$exclude_wiki','$purpose','$generateSpf')";
		print LOG "$hr:$sec - $user_id - <$sql>\n";
		$sth = $dbhu->do($sql);
		#
		#	Get Brand Id
		#
		my $bid;
		$sql="select max(brand_id) from client_brand_info where client_id=$client_id and brand_name='$brandName' and status='A'";
		$sth=$dbhu->prepare($sql);
		$sth->execute();
		($bid)=$sth->fetchrow_array();
		$sth->finish();
		my $unsub_img= $query->param('unsub_img');
		if ($unsub_img ne "")
		{
    		$sql="update client_brand_info set unsub_img='$unsub_img' where brand_id=$bid";
    		my $rows=$dbhu->do($sql);
		}
		#
		# Set category brand info
		#
		my $from_bid;
		#$sql="select brand_id from client_brand_info where brand_name='DirectEmailSolution' and status='A'";
		$sql="select max(brand_id) from client_brand_info where brand_name='DirectEmailSolution'";
		$sth=$dbhu->prepare($sql);
		$sth->execute();
		($from_bid)=$sth->fetchrow_array();
		$sth->finish();
		$sql = "insert into category_brand_info(brand_id,subdomain_id) select $bid,subdomain_id from category_brand_info where brand_id=$from_bid";
		$rows=$dbhu->do($sql);

		$sql="insert into brand_rdns_info(brand_id,rdns_domain) values($bid,'$rdns')";
		$rows=$dbhu->do($sql);

#		if ($useFuture eq "N")
#		{
#			$sql="insert into brand_url_info(brand_id,url_type,url) values($bid,'O','$domain')";
#			$rows=$dbhu->do($sql);
#			$sql="insert into brand_url_info(brand_id,url_type,url) values($bid,'Y','$domain')";
#			$rows=$dbhu->do($sql);
#			$sql="insert into brand_url_info(brand_id,url_type,url) values($bid,'OI','$domain')";
#			$rows=$dbhu->do($sql);
#			$sql="insert into brand_url_info(brand_id,url_type,url) values($bid,'YI','$domain')";
#			$rows=$dbhu->do($sql);
#		}
#		elsif ($useFuture eq "Y")
#		{
#			$sql="insert into brand_available_domains(brandID,domain,type,rank,inService) values($bid,'$domain','O',1,1)";
#			$rows=$dbhu->do($sql);
#		}
#
#	Send email
#
        open (MAIL,"| /usr/sbin/sendmail -t");
        my $from_addr = "Strongmail Brand Added<info\@zetainteractive.com>";
        print MAIL "From: $from_addr\n";
        print MAIL "To: group.operations\@zetainteractive.com\n";
        print MAIL "CC: andrew\@zetainteractive.com\n";
        print MAIL "Subject: Strongmail Brand Added\n";
        my $date_str = $util->date(6,6);
        print MAIL "Date: $date_str\n";
        print MAIL "X-Priority: 1\n";
        print MAIL "X-MSMail-Priority: High\n";
        print MAIL "\nBrand Name: $brandName\n";
        print MAIL "Brand ID: $bid\n";
        print MAIL "Block Host: $block_host\n";
        print MAIL "Main Website Domain: $domain\n";
        print MAIL "Mailing/Image Domains: \n";
		print MAIL "\t$domain\n"; 
        print MAIL "\nRdns URLs: \n";
		print MAIL "\t$rdns\n"; 
        close(MAIL);
        print "<tr><td>Brand <b>$brandName</b> - $bid Added</td></tr>\n";
	} 
	close SAVED;
	close LOG;
	unlink($file_in) || &logerror("Error - could NOT Remove file: $file_in");  # del file_in
	print<<"end_of_html";
</table>
<br><a href="sm_brand.cgi">Back to SM Brand</a>
</center>
</body>
</html>
end_of_html
} # end of sub

