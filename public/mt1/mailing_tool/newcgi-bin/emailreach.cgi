#!/usr/bin/perl
# ******************************************************************************
# emailreach.cgi
#
# writes a record to emailreach_emails 
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
my $rows;
my $errmsg;
my $user_id;
my $bid= $query->param('bid');
my $uid= $query->param('uid');
my $header_tag= $query->param('header_tag');
my $ctype=$query->param('ctype');
if ($ctype eq "")
{
	$ctype="E";
}
if ($uid eq "")
{
	$uid=0;
}
my $max_emails;
my $url;
my $sname;
my $subject;
my $TEST_CID=182;
my @comp_email = ( "compliance\@zetainteractive.com" );
my @others_email = ( "test\@emailreach.com","emailreach\@earthlink.net","emailreach\@gmail.com","emailreach\@netzero.net","emailreach\@excite.com");
my @yahoo_email = ( "test\@emailreach.com","emailreach\@yahoo.com","emailreach2\@yahoo.com");
my @hotmail_email = ( "test\@emailreach.com","emailreach2\@hotmail.com","emailreach\@hotmail.com");
my @aol_email = ( "test\@emailreach.com","emailreach2\@aol.com");
#
my @aol_deliver_email = ("0000.transit\@zzzz.deliverymonitor.com",
"aaaa.transit\@deliverymonitor.com",
"9999.transit\@aaaa.deliverymonitor.com",
"zzzz.transit\@0000.deliverymonitor.com",
"0000.transit\@deliverymonitor.com",
"aaaa.transit\@9999.deliverymonitor.com",
"9999.transit\@deliverymonitor.com",
"zzzz.transit\@deliverymonitor.com",
"aaaalexander45\@aol.com",
"carlmalongy\@aol.com",
"edwardjeffry\@aol.com",
"howardjohnson123\@aol.com",
"jeffrichiardson\@aol.com",
"markmattjohn123\@aol.com",
"onlyrichards73\@aol.com",
"sammysmith123\@aol.com",
"uvagrendhelm\@aol.com",
"zzzfred97\@aol.com",
"aaaarielfont\@cs.com",
"carveyanderson9\@cs.com",
"ellenjonsey\@cs.com",
"harvardysinger\@cs.com",
"jenniferclem6806\@cs.com",
"martingrishm\@cs.com",
"zzz0anet\@cs.com");
#
my @hotmail_deliver_email = ( "0000.transit\@zzzz.deliverymonitor.com",
"aaaa.transit\@deliverymonitor.com",
"9999.transit\@aaaa.deliverymonitor.com",
"zzzz.transit\@0000.deliverymonitor.com",
"0000.transit\@deliverymonitor.com",
"aaaa.transit\@9999.deliverymonitor.com",
"9999.transit\@deliverymonitor.com",
"zzzz.transit\@deliverymonitor.com",
"alexalexander96\@hotmail.com",
"carianderson9\@hotmail.com",
"eidijones\@hotmail.com",
"harveysinger12\@hotmail.com",
"jeneidi492\@hotmail.com",
"martyfoldss\@hotmail.com",
"olgafriedrick\@hotmail.com",
"sandicraigs\@hotmail.com",
"waynegesse\@hotmail.com",
"zzzarpa\@hotmail.com",
"aaalexander5\@msn.com",
"carianderson9\@msn.com",
"eidijones\@msn.com",
"harveysinger12\@msn.com",
"jeneidi4\@msn.com",
"martyfold\@msn.com",
"oolfriedrick\@msn.com",
"sandicraigs\@msn.com",
"zzz0arpa\@msn.com");
#
my @yahoo_deliver_email=("0000.transit\@zzzz.deliverymonitor.com",
"aaaa.transit\@deliverymonitor.com",
"9999.transit\@aaaa.deliverymonitor.com",
"zzzz.transit\@0000.deliverymonitor.com",
"0000.transit\@deliverymonitor.com",
"aaaa.transit\@9999.deliverymonitor.com",
"9999.transit\@deliverymonitor.com",
"zzzz.transit\@deliverymonitor.com",
"aaaalexand21\@yahoo.com",
"carlyjones21\@yahoo.com",
"eddyfonsi2003\@yahoo.com",
"howierichyy\@yahoo.com",
"jenclubbin\@yahoo.com",
"martinclosed\@yahoo.com",
"otowork\@yahoo.com",
"samtrenders\@yahoo.com",
"umaviolets\@yahoo.com",
"zzzzcraig\@yahoo.com",
"aaaavriel\@sbcglobal.net",
"carverknight\@sbcglobal.net",
"ecudowert\@sbcglobal.net",
"hevenilsp\@sbcglobal.net",
"jeffjensen3\@sbcglobal.net",
"millerheles\@sbcglobal.net",
"overthelithill\@sbcglobal.net",
"soundload\@sbcglobal.net",
"wickedaddy\@sbcglobal.net",
"zzzzfour\@sbcglobal.net");
#
my @others_deliver_email=("0000.transit\@zzzz.deliverymonitor.com",
"aaaa.transit\@deliverymonitor.com",
"9999.transit\@aaaa.deliverymonitor.com",
"zzzz.transit\@0000.deliverymonitor.com",
"0000.transit\@deliverymonitor.com",
"aaaa.transit\@9999.deliverymonitor.com",
"9999.transit\@deliverymonitor.com",
"zzzz.transit\@deliverymonitor.com",
"aaaa00jef\@worldnet.att.net",
"aaalexander54\@mail.com",
"aaalexander5\@netzero.com",
"aaalexander\@bellsouth.net",
"aaalexander\@earthlink.net",
"aaalexander\@mac.com",
"aaalexheather\@excite.com",
"aaalexheather\@usa.net",
"carlandi\@earthlink.net",
"carlihoward\@worldnet.att.net",
"carlygifts\@netzero.com",
"edfolds\@earthlink.net",
"edithjones3\@netzero.com",
"elvinjackson\@worldnet.att.net",
"hectorfried\@earthlink.net",
"henryclod\@netzero.com",
"hevenilsp\@sbcglobal.net",
"hongflued\@worldnet.att.net",
"jasonfrank5\@earthlink.net",
"jasonfrank5\@mac.com",
"jasonfrank\@bellsouth.net",
"jeffjackson6\@netzero.com",
"mandorzippy\@earthlink.net",
"markquincy\@netzero.com",
"medfordrul\@worldnet.att.net",
"olivertrundel\@netzero.com",
"pauloklapton\@earthlink.net",
"richardcheese5\@earthlink.net",
"richardcheese\@bellsouth.net",
"richardcheese\@mac.com",
"samueldepper\@netzero.com",
"victorfrank5\@mac.com",
"victorfrank\@bellsouth.net",
"victorfrank\@earthlink.net",
"vze5b543\@verizon.net",
"wickytripper\@netzero.com",
"zzz0anet\@worldnet.att.net",
"zzz9young\@excite.com",
"zzz9young\@usa.net",
"zzzippy10\@bellsouth.net",
"zzzippy\@earthlink.net",
"zzzippy\@mac.com",
"zzzyoung\@netzero.com");
#
my $i;
my $server_id;
my $vsgID;
my $server_name;

# connect to the util database
my ($dbhq,$dbhu)=$util->get_dbh();

# check for login
$user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}
print "Content-type: text/html\n\n";
if ($ctype eq "E")
{
	print "<html><head><title>EmailReach Results</title></head>\n";
}
elsif ($ctype eq "C")
{
	print "<html><head><title>Compliance Check Results</title></head>\n";
}
else
{
	print "<html><head><title>Delivery Monitor Results</title></head>\n";
}
print<<"end_of_html";
<body>
<center><table width=80%>
<tr><th>Server</th><th>URL</th><th>Subject</th></tr>
end_of_html
#
my $sql=qq^SELECT sc.id AS servID, sc.server, sic.ip FROM server_config sc, server_ip_config sic,brand_ip bi WHERE sc.id=sic.id AND inService=1 AND sc.type='strmail' AND bi.ip=sic.ip and bi.brandID=? and sic.ip not in (select ip from server_ip_failed) ORDER BY RAND() limit 1^;
my $sthServ=$dbhq->prepare($sql);
$sthServ->execute($bid);
($server_id,$server_name,$vsgID)=$sthServ->fetchrow_array();
$sthServ->finish();
#
if ($uid > 0)
{
	$sql="select url from brand_url_info where brand_id=? and url_type='O' and url_id=$uid";
}
else
{
	$sql="select url from brand_url_info where brand_id=? and url_type='O' limit 1";
}
$sth=$dbhq->prepare($sql);
$sth->execute($bid);
if (($url) = $sth->fetchrow_array())
{
	$subject="Create a lasting impression - " . $server_name; 	
	print "<tr><td>$server_name</td><td>$url</td><td>$subject</td></tr>\n";
	$i= 0;
	if ($ctype eq "E")
	{
    	$sql="insert into test_strongmail(creative_id,subject,url,submit_datetime,brand_id,test_type,email_addr,campaign_id,servID,vsgID) values($TEST_CID,'$subject','$url',now(),$bid,'EMAILREACH','',0,$server_id,'$vsgID')";
	}
	elsif ($ctype eq "C")
	{
		my $temp_subject=$subject . " - " . $url;
    	$sql="insert into test_strongmail(creative_id,subject,url,submit_datetime,brand_id,test_type,email_addr,campaign_id,servID,vsgID) values($TEST_CID,'$temp_subject','$url',now(),$bid,'COMPLIANCE','',0,$server_id,'$vsgID')";
	}
	else
	{
		if ($uid == 0)
		{
			my $fld="h_".$url;
			$header_tag=$query->param($fld);
		}
    	$sql="insert into test_strongmail(creative_id,subject,url,submit_datetime,brand_id,test_type,email_addr,campaign_id,servID,vsgID,header_tag) values($TEST_CID,'$subject','$url',now(),$bid,'DELIVER','',0,$server_id,'$vsgID','$header_tag')";
	}
}
$sth->finish();
my $rows=$dbhu->do($sql);
#
print "</body></html>\n";
#
$util->clean_up();
exit(0);
