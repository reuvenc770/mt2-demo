#!/usr/bin/perl

# *****************************************************************************************
# view_advertiser_save.cgi
#
# this page presents the user with a preview of the campaign email
#
# History
# *****************************************************************************************

# include Perl Modules

use strict;
use util_mail;
use URI::Escape;
use util;

my $util = util->new;
my $query = CGI->new;
my $dbh;
my $sth;
my $sql;
my $cnt;
my $rows;
my $errmsg;
my $aname;
my $email_addr;
my $email_user_id;
my $campaign_id;
my $format="H";
my @aidarr= $query->param('aid');
my $cids= $query->param('cids');
my $cstr=$query->param('cstr');
$cids=~s/ //g;
my $cakeids = $query->param('cakeids');
my $cake_offerID= $query->param('cake_offerID');
my $sid= $query->param('sid');
$cakeids=~s/ //g;
my $cname= $query->param('cname');
my $ctype= $query->param('ctype');
my $page= $query->param('page');
if ($page eq "")
{
	$page=1;
}
my $cdeploy = "N";
my $images = $util->get_images_url;
my $subject;
my $body_text;
my $first_part;
my $testvar;
my $pos;
my $pos2;
my $the_rest;
my $end_pos;
my $selected_bg_color = "#509C10";
my $not_selected_bg_color = "#E3FAD1";
my $selected_tl_gif = "$images/blue_tl.gif";
my $selected_tr_gif = "$images/blue_tr.gif";
my $not_selected_tl_gif = "$images/lt_purp_tl.gif";
my $not_selected_tr_gif = "$images/lt_purp_tr.gif";
my $selected_text_color = "#FFFFFF";
my $not_selected_text_color = "#509C10";
my @bg_color;
my @tl_gif;
my @tr_gif;
my @text_color;
my $k;
my $from_addr;
my $footer_color;
my $internal_flag;
my $unsub_url;
my $unsub_image;
my $cunsub_image;
my $content_id;
my $footer_content_id;
my $aidstr=$query->param('aidstr');
my $aid;

if ($aidstr eq "")
{
foreach my $a (@aidarr)
{
	$aidstr=$aidstr.$a.",";
}
chop($aidstr);
}
if ($cstr eq "")
{
	if ($cids ne "")
	{
		$cids =~ s/[ \n\r\f\t]/\|/g ;
   	 	$cids =~ s/\|{2,999}/\|/g ;
   		my @c= split '\|', $cids;
    	foreach my $c1 (@c)
    	{
    		$cstr.=$c1.",";
    	}
    	chop($cstr);
	}
}

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

$email_addr="email\@domain.com";
$email_user_id = 0; 

my $offset=(($page-1)*10);
my $npage=$page+1;
my $ppage=$page-1;
print "Content-type: text/html\n\n";
print<<"end_of_html";
<html><head><title>Advertiser Preview</title></head>
<body>
end_of_html
if ($ppage > 0)
{
	print "<center><a href=\"view_advertiser_save.cgi?aidstr=$aidstr&cstr=$cstr&ctype=$ctype&page=$ppage&cname=$cname\">Previous</a>&nbsp;&nbsp;<a href=\"view_advertiser_save.cgi?aidstr=$aidstr&cstr=$cstr&ctype=$ctype&page=$npage&cname=$cname\">Next</a></center><br>\n";
}
else
{
	print "<center><a href=\"view_advertiser_save.cgi?aidstr=$aidstr&cstr=$cstr&ctype=$ctype&page=$npage&cname=$cname\">Next</a></center><br>\n";
}
# Get the email template and substitute all the field data
$sql = "select creative_id,creative_name,default_subject,default_from,advertiser_id,unsub_image,content_id from creative where 1=1 ";
if ($sid ne "")
{
	$sql.=" and advertiser_id in (select advertiser_id from advertiser_info where sid=$sid) ";
}
if ($cake_offerID ne "")
{
	$sql.=" and advertiser_id in (select advertiser_id from advertiser_info ai,CakeCreativeOfferJoin ccoj where ai.cake_creativeID=ccoj.creativeID and ccoj.offerID=$cake_offerID) ";
}
my $cakestr="";
if ($cakeids ne "")
{
	$cakeids =~ s/[ \n\r\f\t]/\|/g ;
	$cakeids =~ s/\|{2,999}/\|/g ;
	my @c= split '\|', $cakeids;
	my $cakestr="";
	foreach my $c1 (@c)
	{
		$c1=~s/ //g;
	    $cakestr.=$c1.",";
	}
	chop($cakestr);
	$sql.=" and advertiser_id in (select advertiser_id from advertiser_info where cake_creativeID in ($cakestr)) ";
}
if ($aidstr ne "")
{
	$sql.=" and ((advertiser_id in ($aidstr) ";
	if ($ctype ne "")
	{
		$sql.=" and status='$ctype' ";
	}
	if ($cname ne "")
	{
		$sql.=" and creative_name like '%".$cname."%') ";
	}
	else
	{
		$sql.=")";
	}
	if ($cstr eq "")
	{
		$sql.=")";
	}
}
if ($cstr ne "")
{
	if ($aidstr ne "")
	{
    	$sql.="or creative_id in ($cstr)) ";
	}
	else
	{
    	$sql.="and creative_id in ($cstr) ";
	}
}
$sql.=" order by creative_id limit $offset,10";
$sth = $dbhq->prepare($sql);
open(LOG,">>/tmp/d.log");
print LOG "$sql\n";
close(LOG);
$sth->execute();
my $cname;
while (($campaign_id,$cname,$subject,$from_addr,$aid,$cunsub_image,$footer_content_id) = $sth->fetchrow_array())
{
	#
	# Get unsub information and internal_flag
	#
	my $unsub_use;
	my $unsub_text;
	$sql = "select track_internally,unsub_link,unsub_image,unsub_use,unsub_text,advertiser_name from advertiser_info where advertiser_id=$aid";
	my $sth1a = $dbhq->prepare($sql);
	$sth1a->execute();
	($internal_flag,$unsub_url,$unsub_image,$unsub_use,$unsub_text,$aname) = $sth1a->fetchrow_array();
	$sth1a->finish();
	
	if ($cunsub_image eq "NONE")
	{
		$unsub_image="";
	}
	my $the_email = &util_mail::mail_preview($dbhq,$campaign_id,$format,$email_addr,$email_user_id,$user_id,$footer_color,$aid,$internal_flag,$unsub_url,$unsub_image,$content_id,"",$cdeploy,$unsub_use,$unsub_text);
	my $rot_str;
	print "<b>$aname - $cname - $campaign_id</b>&nbsp;&nbsp;<a href=\"/newcgi-bin/edit_creative.cgi?cid=$campaign_id&aid=$aid\" target=_blank>E</a>&nbsp;&nbsp;<a href=\"/newcgi-bin/delete_creative.cgi?cid=$campaign_id&aid=$aid\" target=_blank>D</a>&nbsp;&nbsp<b>$rot_str</b></br>\n";
	$the_email=~s/{{HEAD}}//g;
	print "$the_email\n";
	print "<hr width=100% height=3></hr><br>\n";
}
$sth->finish();
if ($ppage > 0)
{
	print "<center><a href=\"view_advertiser_save.cgi?aidstr=$aidstr&cstr=$cstr&ctype=$ctype&page=$ppage&cname=$cname\">Previous</a>&nbsp;&nbsp;<a href=\"view_advertiser_save.cgi?aidstr=$aidstr&cstr=$cstr&ctype=$ctype&page=$npage&cname=$cname\">Next</a></center><br>\n";
}
else
{
	print "<center><a href=\"view_advertiser_save.cgi?aidstr=$aidstr&cstr=$cstr&ctype=$ctype&page=$npage&cname=$cname\">Next</a></center><br>\n";
}
print<<"end_of_html";
</body>
</html>
end_of_html
$util->clean_up();
exit(0);

