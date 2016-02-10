#!/usr/bin/perl
#===============================================================================
# Name   : dbloptin_preview.cgi 
#
#--Change Control---------------------------------------------------------------
#===============================================================================

#-----  include Perl Modules ---------
use strict;
use CGI;
use util;

#------  get some objects to use later ---------
my $util = util->new;
my $query = CGI->new;
my $sql;
my $sth;
my $sth1;
my $sth2;
my $rows;
my $header_image;
my $content_str;
my $template_id;
my $content_html;
my $client_id;
my $bid;
my $bname;
my $addr1;
my $addr2;
my $redir_domain;
my $trand;

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}
#------  connect to the util database -----------
my ($dbhq,$dbhu)=$util->get_dbh();
my $id=$query->param('id');
$sql="select client_id,header_image,content_str,template_id from double_optin where id=$id";
$sth = $dbhq->prepare($sql) ;
$sth->execute();
($client_id,$header_image,$content_str,$template_id) = $sth->fetchrow_array();
$sth->finish();

$sql = "select brand_id,brand_name,mailing_addr1,mailing_addr2 from client_brand_info where client_id=$client_id and status='A' and purpose='Daily'";
$sth=$dbhq->prepare($sql);
$sth->execute();
($bid,$bname,$addr1,$addr2) = $sth->fetchrow_array();
$sth->finish();
$sql = "select url,rand() from brand_url_info where brand_id=$bid and url_type='O' order by 1";
$sth=$dbhq->prepare($sql);
$sth->execute();
($redir_domain,$trand) = $sth->fetchrow_array();
$sth->finish();
$redir_domain = lc($redir_domain);

$sql="select html_code from brand_template where template_id=?";
$sth = $dbhq->prepare($sql) ;
$sth->execute($template_id);
($content_html) = $sth->fetchrow_array();
$sth->finish();

$content_html=~ s/{{DOPTIN_CONTENT}}/$content_str/g;
if ($header_image ne "")
{
	$content_html=~ s/{{DOPTIN_HEADER}}/<img src="http:\/\/www.affilaiteimages.com\/images\/dbloptin\/${header_image}">/g;
}
else
{
	$content_html=~ s/{{DOPTIN_HEADER}}//g;
}
my $timestr = util::date(0,0);
$content_html=~ s/{{DATE}}/$timestr/g;
$content_html=~ s/{{CLIENT_BRAND}}/$bname/g;
$content_html=~ s/{{MAILING_ADDR1}}/$addr1/g;
$content_html=~ s/{{MAILING_ADDR2}}/$addr2/g;
$content_html=~ s/{{NAME}}/"TEST"/g;
$content_html=~ s/{{EMAIL_USER_ID}}/0/g;
$content_html=~ s/{{DOMAIN}}/$redir_domain/g;

#--------------------------------
        print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>

<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Double Optin Preview</title>
</head>

<body>
end_of_html
print "$content_html\n";
print<<"end_of_html";
</body>
</html>
end_of_html
