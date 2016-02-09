#!/usr/bin/perl

# *****************************************************************************************
# sm_brand_save.cgi
#
# this page inserts records into client_brand_info, brand_url_info, and brand_available_domains 
#
# History
# Jim Sobeck, 06/18/07, Creation
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use App::WebAutomation::ImageHoster;
use util;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $sql;
my $dbh;
my $sid;
my $rows;
my $errmsg;
my $sth1;
my $images = $util->get_images_url;
my $upload_dir = "/var/www/util/creative";

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
my $data={};
$data->{'imageCollectionID'}="000000000000001";
$ENV{'IMAGE_HOSTER_SSH_KEY'}="/var/www/.ssh/images.key";
my $imageHoster = App::WebAutomation::ImageHoster->new($data);
my $brand_name = $query->param('brand_name');
$brand_name=~tr/[0-9][a-z][A-Z]\-_/ /c;
$brand_name=~s/ //g;
my $client = $query->param('client');
my $block = $query->param('block');
my $template_id = $query->param('template_id');
my $website_domain = $query->param('website_domain');
my $mailing_list = $query->param('mailing_domain');
my $rdns_urls= $query->param('rdns_urls');
my $replace_domain = $query->param('enable_replace');
my $use_future= $query->param('use_future');
if ($use_future eq "")
{
	$use_future="Y";
}
my $purpose= $query->param('purpose');
my $generateSpf= $query->param('generateSpf');
if ($generateSpf eq "")
{
	$generateSpf="Y";
}
my $use_wiki= $query->param('use_wiki');
my $exclude_wiki="Y";
if ($use_wiki eq "")
{
	$exclude_wiki="N";
}
if ($use_wiki eq "Y")
{
	$exclude_wiki="N";
}
my $num_domains_rotate= $query->param('num_domains_rotate');
if ($num_domains_rotate eq "")
{
	$num_domains_rotate=1;
}
$mailing_list =~ s/[\n\r\f\t]/\|/g ;
$mailing_list =~ s/\|{2,999}/\|/g ;
$rdns_urls=~ s/[\n\r\f\t]/\|/g ;
$rdns_urls=~ s/\|{2,999}/\|/g ;
my @mailing_domain = split '\|', $mailing_list;
my @rdns= split '\|', $rdns_urls;
my $ns1="ns1.".$website_domain;
my $ns2="ns2.".$website_domain;
my $whois_email="info@".$website_domain;
my $abuse_email="abuse@".$website_domain;
my $personal_email="john@".$website_domain;
my $vid;
my $tdir;
my $mailing_addr1;
my $mailing_addr2;
my $block_host;
$sql="select block_host,variation_id,mailing_addr1,mailing_addr2 from block where block_id=$block";
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
#$sql = "select color_id from colors where color_type='B' order by rand() limit 1"; 
#$sth = $dbhq->prepare($sql) ;
#sth->execute();
#$footer_bg_color_id)=$sth->fetchrow_array();
#sth->finish();
$footer_bg_color_id=8; # White
#
# Insert record into client_brand_info 
#
$sql="insert into client_brand_info(client_id,brand_name,others_ns1,others_ns2,yahoo_ns1,yahoo_ns2,mailing_addr1,mailing_addr2,whois_email,abuse_email,personal_email,footer_variation,status,footer_font_id,footer_color_id,footer_bg_color_id,brand_type,third_party_id,exclude_subdomain,template_id,replace_domain,num_domains_rotate,exclude_wiki,purpose,generateSpf) values($client,'$brand_name','$ns1','$ns2','$ns1','$ns2','$mailing_addr1','$mailing_addr2','$whois_email','$abuse_email','$personal_email',$vid,'A',$footer_font_id,$footer_color_id,$footer_bg_color_id,'3rd Party',10,'Y',$template_id,$replace_domain,$num_domains_rotate,'$exclude_wiki','$purpose','$generateSpf')";
$sth = $dbhu->do($sql);
#
#	Get Brand Id
#
my $bid;
$sql="select max(brand_id) from client_brand_info where client_id=$client and brand_name='$brand_name' and status='A'";
$sth=$dbhu->prepare($sql);
$sth->execute();
($bid)=$sth->fetchrow_array();
$sth->finish();
my $unsub_img= $query->param('unsub_img');
if ($unsub_img ne "")
{
	$unsub_img =~ s/.*[\/\\](.*)/$1/;
	if ($unsub_img ne "")
	{
    	my $upload_filehandle = $query->upload("unsub_img");
		my $params={};
		$params->{'image'}=$upload_filehandle;
		my ($imageHostingErrors, $newImageName, $imageExtension, $allImageProperties) = $imageHoster->setUpImageHosting($params);
		$unsub_img=$newImageName;
	}
    $sql="update client_brand_info set unsub_img='$unsub_img' where brand_id=$bid";
    my $rows=$dbhu->do($sql);
}
#
# Set category brand info
#
my $from_bid;
$sql="select brand_id from client_brand_info where brand_name='DirectEmailSolution' and status='A'";
$sth=$dbhu->prepare($sql);
$sth->execute();
($from_bid)=$sth->fetchrow_array();
$sth->finish();
$sql = "insert into category_brand_info(brand_id,subdomain_id) select $bid,subdomain_id from category_brand_info where brand_id=$from_bid";
$rows=$dbhu->do($sql);

my $rurl;
foreach $rurl (@rdns)
{
	$sql="insert into brand_rdns_info(brand_id,rdns_domain) values($bid,'$rurl')";
	$rows=$dbhu->do($sql);
}
my $i=1;
my $mdomain;
foreach $mdomain (@mailing_domain)
{
	$mdomain = lc $mdomain;
    if ($mdomain =~ /[^a-zA-Z0-9\.\-]/)
    {
		$errmsg=$errmsg . "Domain <$mdomain> has invalid characters - not added.<br>";
	}
	else
	{
	if (($i == 1) or ($use_future eq "N"))
	{
		$sql="insert into brand_url_info(brand_id,url_type,url) values($bid,'O','$mdomain')";
		$rows=$dbhu->do($sql);
		$sql="insert into brand_url_info(brand_id,url_type,url) values($bid,'Y','$mdomain')";
		$rows=$dbhu->do($sql);
		$sql="insert into brand_url_info(brand_id,url_type,url) values($bid,'OI','$mdomain')";
		$rows=$dbhu->do($sql);
		$sql="insert into brand_url_info(brand_id,url_type,url) values($bid,'YI','$mdomain')";
		$rows=$dbhu->do($sql);
		$i++;
		if ($use_future eq "Y")
		{
			$sql="insert into brand_available_domains(brandID,domain,type,rank,inService) values($bid,'$mdomain','O',1,1)";
			$rows=$dbhu->do($sql);
		}
	}
	else
	{
		$sql="insert into brand_available_domains(brandID,domain,type,rank,inService) values($bid,'$mdomain','O',$i,0)";
		$rows=$dbhu->do($sql);
		$i++;
	}
	}
}
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
        print MAIL "\nBrand Name: $brand_name\n";
        print MAIL "Brand ID: $bid\n";
        print MAIL "Block Host: $block_host\n";
        print MAIL "Main Website Domain: $website_domain\n";
        print MAIL "Mailing/Image Domains: \n";
foreach my $mdomain (@mailing_domain)
{
		print MAIL "\t$mdomain\n"; 
}
        print MAIL "\nRdns URLs: \n";
foreach my $rurl (@rdns)
{
		print MAIL "\t$rurl\n"; 
}
        close(MAIL);
#
# Display the confirmation page
#
print "Content-type: text/html\n\n";
print<<"end_of_html";
<html>
<head></head>
<body>
<center>
<h2>Brand <b>$brand_name ($bid)</b> has been added.</h2>
end_of_html
if ($errmsg ne "")
{
	print "<br><h3>$errmsg</h3></br>\n";
}
print<<"end_of_html";
<br>
<a href="sm_brand.cgi">Add Another Strongmail Brand</a>&nbsp;&nbsp;&nbsp;<a href="mainmenu.cgi">Home</a>
</center>
</body></html>
end_of_html
$util->clean_up();
exit(0);
