#!/usr/bin/perl

# *****************************************************************************************
# emailreach_creative.cgi
#
# writes a record to emailreach_emails 
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
my $sth1;
my $temp_subject;
my $sql;
my $dbh;
my $rows;
my $errmsg;
my $user_id;
my $aid= $query->param('aid');
my $header_tag= $query->param('header_tag');
my $header_id= $query->param('headID');
my $footer_id= $query->param('footID');
my $body_id=$query->param('bodyID');
my $style_id=$query->param('styleID');
my $creative_id= $query->param('cid');
my $sel_subj_id=$query->param('subjID');

if ($creative_id eq "")
{
	$creative_id=0;
}
my $subject= $query->param('subject');
if ($subject eq "")
{
	$subject="N";
}
my $add_adv= $query->param('add_adv');
if ($add_adv eq "")
{
	$add_adv="N";
}
my $ctype=$query->param('ctype');
if ($ctype eq "")
{
	$ctype="E";
}
$header_id||=0;
$footer_id||=0;
$body_id||=0;

my $max_emails;
my $url="routename.com";
my $brand_id=7;
my $sname="sv-db";
my $creative_name;
my @habeas_email = ( "sa_eneuner\@score.habeas.com" );
my @comp_mail = ( "compliance\@zetainteractive.com" );
my @others_email = ( "emailreach\@earthlink.net","emailreach\@gmail.com","emailreach\@netzero.net","emailreach\@excite.com","test\@emailreach.com","pkadish\@zetainteractive.com",'raymond@zetainteractive.com','li.raymond@gmail.com');
my @yahoo_email = ( "emailreach\@yahoo.com","emailreach2\@yahoo.com");
my @hotmail_email = ( "emailreach2\@hotmail.com","emailreach\@hotmail.com",'al_ina_123@hotmail.com','ina.125@hotmail.com','inna.5@hotmail.com','nana55125@hotmail.com','alla4545@hotmail.com','l_ala_889@hotmail.com','k_i_ki556@hotmail.com','nunu8989@hotmail.com','anna565656@hotmail.com','li.li.55@hotmail.com');
my @aol_email = ( "emailreach2\@aol.com");
my @aol_deliver_email = (
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
my @hotmail_deliver_email = ( 
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
my @yahoo_deliver_email=(
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
my $i;
my $csubject;
my $subject_id;

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
elsif ($ctype eq "H")
{
	print "<html><head><title>Habeas Results</title></head>\n";
}
else
{
	print "<html><head><title>Delivery Monitor Results</title></head>\n";
}
print<<"end_of_html";
<body>
<center><table width=100%>
<tr><th>Creative</th><th>URL</th><th>Email</th><th>Subject</th></tr>
end_of_html
#
if ($creative_id > 0)
{
	$sql="select creative_id,default_subject,creative_name from creative where creative_id=$creative_id"; 
}
else
{
	$sql="select creative_id,default_subject,creative_name from creative where advertiser_id=$aid and status='A'"; 
}
$sth=$dbhq->prepare($sql);
$sth->execute();
while (($creative_id,$subject_id,$creative_name) = $sth->fetchrow_array())
{
	if ($subject eq "Y")
	{
		$sql="select advertiser_subject from advertiser_subject where advertiser_id=$aid and status='A'";
	}
	else
	{
		if ($sel_subj_id) {
			$sql="SELECT advertiser_subject FROM advertiser_subject WHERE subject_id=$sel_subj_id";
		}
		else {
			$sql="select advertiser_subject from advertiser_subject where subject_id=$subject_id";
		}
	}
	$sth1=$dbhq->prepare($sql);
	$sth1->execute();
	while (($csubject)=$sth1->fetchrow_array())
	{
#
		my $db_subject=$dbhq->quote($csubject);
		$i= 0;
		$temp_subject=$db_subject;
		if ($ctype eq "E")
		{
			while ($i <= $#others_email)
			{
				print "<tr><td>$creative_name</td><td>$url</td><td>$others_email[$i]</td><td>$csubject</td></tr>\n";
				$sql="insert into emailreach_emails(hostname,creative_id,email_addr,subject,url,submit_datetime,include_adv,brand_id,footer_id,header_id,body_id,style_id) values('$sname',$creative_id,'$others_email[$i]',$temp_subject,'$url',now(),'$add_adv',$brand_id,$footer_id,$header_id,$body_id,'$style_id')";
				$rows=$dbhu->do($sql);
				$i++;
			}
			$i= 0;
			$temp_subject=$db_subject;
			while ($i <= $#hotmail_email)
			{
				print "<tr><td>$creative_name</td><td>$url</td><td>$hotmail_email[$i]</td><td>$csubject</td></tr>\n";
				$sql="insert into emailreach_emails(hostname,creative_id,email_addr,subject,url,submit_datetime,include_adv,brand_id,footer_id,header_id,body_id,style_id) values('$sname',$creative_id,'$hotmail_email[$i]',$temp_subject,'$url',now(),'$add_adv',$brand_id,$footer_id,$header_id,$body_id,'$style_id')";
				$rows=$dbhu->do($sql);
				$i++;
			}
			$i= 0;
			$temp_subject=$db_subject;
			while ($i <= $#yahoo_email)
			{
				print "<tr><td>$creative_name</td><td>$url</td><td>$yahoo_email[$i]</td><td>$csubject</td></tr>\n";
				$sql="insert into emailreach_emails(hostname,creative_id,email_addr,subject,url,submit_datetime,include_adv,brand_id,footer_id,header_id,body_id,style_id) values('$sname',$creative_id,'$yahoo_email[$i]',$temp_subject,'$url',now(),'$add_adv',$brand_id,$footer_id,$header_id,$body_id,'$style_id')";
				$rows=$dbhu->do($sql);
				$i++;
			}
			$i= 0;
			$temp_subject=$db_subject;
			while ($i <= $#aol_email)
			{
				print "<tr><td>$creative_name</td><td>$url</td><td>$aol_email[$i]</td><td>$csubject</td></tr>\n";
				$sql="insert into emailreach_emails(hostname,creative_id,email_addr,subject,url,submit_datetime,include_adv,brand_id,footer_id,header_id,body_id,style_id) values('$sname',$creative_id,'$aol_email[$i]',$temp_subject,'$url',now(),'$add_adv',$brand_id,$footer_id,$header_id,$body_id,'$style_id')";
				$rows=$dbhu->do($sql);
				$i++;
			}
		}
		elsif ($ctype eq "H")
		{
			while ($i <= $#habeas_email)
			{
				print "<tr><td>$creative_name</td><td>$url</td><td>$habeas_email[$i]</td><td>$csubject</td></tr>\n";
				$sql="insert into emailreach_emails(hostname,creative_id,email_addr,subject,url,submit_datetime,include_adv,brand_id,footer_id,header_id,body_id,style_id) values('$sname',$creative_id,'$habeas_email[$i]',$temp_subject,'$url',now(),'$add_adv',$brand_id,$footer_id,$header_id,$body_id,'$style_id')";
				$rows=$dbhu->do($sql);
				$i++;
			}
		}
		else 
		{
			while ($i <= $#others_deliver_email)
			{
				print "<tr><td>$creative_name</td><td>$url</td><td>$others_deliver_email[$i]</td><td>$csubject</td></tr>\n";
				$sql="insert into emailreach_emails(hostname,creative_id,email_addr,subject,url,submit_datetime,include_adv,brand_id,header_tag,footer_id,header_id,body_id,style_id) values('$sname',$creative_id,'$others_deliver_email[$i]',$temp_subject,'$url',now(),'$add_adv',$brand_id,'$header_tag',$footer_id,$header_id,$body_id,'$style_id')";
				$rows=$dbhu->do($sql);
				$i++;
			}
			$i= 0;
			$temp_subject=$db_subject;
			while ($i <= $#hotmail_deliver_email)
			{
				print "<tr><td>$creative_name</td><td>$url</td><td>$hotmail_deliver_email[$i]</td><td>$csubject</td></tr>\n";
				$sql="insert into emailreach_emails(hostname,creative_id,email_addr,subject,url,submit_datetime,include_adv,brand_id,header_tag,footer_id,header_id,body_id,style_id) values('$sname',$creative_id,'$hotmail_deliver_email[$i]',$temp_subject,'$url',now(),'$add_adv',$brand_id,'$header_tag',$footer_id,$header_id,$body_id,'$style_id')";
				$rows=$dbhu->do($sql);
				$i++;
			}
			$i= 0;
			$temp_subject=$db_subject;
			while ($i <= $#yahoo_deliver_email)
			{
				print "<tr><td>$creative_name</td><td>$url</td><td>$yahoo_deliver_email[$i]</td><td>$csubject</td></tr>\n";
				$sql="insert into emailreach_emails(hostname,creative_id,email_addr,subject,url,submit_datetime,include_adv,brand_id,header_tag,footer_id,header_id,body_id,style_id) values('$sname',$creative_id,'$yahoo_deliver_email[$i]',$temp_subject,'$url',now(),'$add_adv',$brand_id,'$header_tag',$footer_id,$header_id,$body_id,'$style_id')";
				$rows=$dbhu->do($sql);
				$i++;
			}
			$i= 0;
			$temp_subject=$db_subject;
			while ($i <= $#aol_deliver_email)
			{
				print "<tr><td>$creative_name</td><td>$url</td><td>$aol_deliver_email[$i]</td><td>$csubject</td></tr>\n";
				$sql="insert into emailreach_emails(hostname,creative_id,email_addr,subject,url,submit_datetime,include_adv,brand_id,header_tag,footer_id,header_id,body_id,style_id) values('$sname',$creative_id,'$aol_deliver_email[$i]',$temp_subject,'$url',now(),'$add_adv',$brand_id,'$header_tag',footer_id,header_id,$body_id,'$style_id')";
				$rows=$dbhu->do($sql);
				$i++;
			}
		}
	}
	$sth1->finish();
}
$sth->finish();
print "</body></html>\n";
#
$util->clean_up();
exit(0);
