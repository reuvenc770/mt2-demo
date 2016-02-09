#!/usr/bin/perl

# *****************************************************************************************
# upd_tempalte.cgi
#
# this page updates information in the brand_template table
#
# History
# Jim Sobeck, 06/04/07, Creation
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;
use HTML::LinkExtor;
use WWW::Curl::easy;
use URI::Split qw(uri_split uri_join);
use File::Basename;
use App::WebAutomation::ImageHoster;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $sql;
my $dbh;
my $errmsg;
my $images = $util->get_images_url;
my $camp_id;
my $pmesg;
my $rid;
my $img_dir;
my $img_cnt;
my $global_text;
my ($scheme, $auth, $path, $frag);
my $name;
my $new_name;
my $suffix;

# connect to the util database
my ($dbhq,$dbhu)=$util->get_dbh();
$pmesg="";

# check for login
my $user_id = util::check_security();
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
	$util->clean_up();
    exit(0);
}

$util->getUserData({'userID' => $user_id});

my $userDataRestrictionWhereClause = '';

if($util->getUserData()->{'isExternalUser'} == 1)
{
	$userDataRestrictionWhereClause = qq|
        userID = $user_id AND
    |;
}

my $data={};
$data->{'imageCollectionID'}="000000000000001";
$ENV{'IMAGE_HOSTER_SSH_KEY'}="/var/www/.ssh/images.key";
my $imageHoster = App::WebAutomation::ImageHoster->new($data);
#
# Get the information about the user from the form 
#
my $temp_id = $query->param('temp_id');
my $backto = $query->param('backto');
my $template_name = $query->param('template_name');
my $template_type = $query->param('template_type');
my $host_images = $query->param('host_images');
$template_name=~ s/'/''/g; 
my $nl_template= $query->param('nl_template');
my $notes = $query->param('notes');
if ($nl_template ne "")
{
	$_=$nl_template;
	if (/{{FOOTER}}/)
	{
	}
	else
	{
		$pmesg="No {{FOOTER}} tag in template.  The template was not updated.";
		print "Location: /cgi-bin/template_list.cgi?pmesg=$pmesg\n\n";
		exit();
	}
}
else
{
	$nl_template='';
}
if ($host_images eq "Y")
{
    $nl_template=~ s/src = "/src="/g;
    $nl_template=~ s/src= "/src="/g;
    $nl_template =~ s/src ="/src="/g;
    $global_text = $nl_template;
    $img_dir = util::get_name() . "_" . $temp_id;
    my $p = HTML::LinkExtor->new(\&cb);
    $p->parse($nl_template);
    $nl_template= $global_text;
}
# check to see if template changed
if ($temp_id > 0)
{
	my $notifyChanges;
	my $temp_code;
	my $cdate;
	$sql="select notifyChanges,html_code,now() from brand_template where $userDataRestrictionWhereClause template_id=$temp_id";
	my $sth1a=$dbhu->prepare($sql);
	$sth1a->execute();
	($notifyChanges,$temp_code,$cdate)=$sth1a->fetchrow_array();
	$sth1a->finish();
	if (($notifyChanges eq "Y") and ($temp_code ne $nl_template))
	{
        open (MAIL,"| /usr/sbin/sendmail -t");
        my $from_addr = "Template Changed <info\@zetainteractive.com>";
        print MAIL "From: $from_addr\n";
        print MAIL "To: mailops\@zetainteractive.com\n";
        print MAIL "Subject: Template Changed: $template_name\n";
        my $date_str = $util->date(6,6);
        print MAIL "Date: $date_str\n";
        print MAIL "X-Priority: 1\n";
        print MAIL "X-MSMail-Priority: High\n";
        print MAIL "Template ID: $temp_id ($template_name) changed at $cdate\n";
        close MAIL;
	}
}
$nl_template=~ s/'/''/g; 
if ($notes ne "")
{
}
else
{
	$notes='';
}
$notes=~ s/'/''/g; 
#
#
$nl_template=~s/\\/\\\\/g;
if ($temp_id > 0)
{
	$sql = "update brand_template set template_name='$template_name',html_code='$nl_template',notes='$notes',mailingTemplateTypeID=$template_type  where $userDataRestrictionWhereClause template_id=$temp_id"; 
}
else
{
	$sql="insert into brand_template(userID, template_name,date_added,status,html_code,notes,mailingTemplateTypeID) values($user_id, '$template_name',curdate(),'A','$nl_template','$notes',$template_type)"; 
}
$sth = $dbhu->do($sql);
$_=$nl_template;
if (((/\.com/) || (/\.COM/)) && ($pmesg eq ""))
{
	$pmesg = "Error - .com still in template";
}
if ($dbhu->err() != 0)
{
	my $errmsg = $dbhu->errstr();
   	util::logerror("Updating brand_template record for $sql $user_id: $errmsg");
}
else
{
	if ($temp_id == 0)
	{
		$sql="select max(template_id) from brand_template where $userDataRestrictionWhereClause template_name='$template_name' and status='A'";
		$sth=$dbhu->prepare($sql);
		$sth->execute();
		($temp_id)=$sth->fetchrow_array();
		$sth->finish();
	}
	if ($backto eq "")
	{
		print "Location: /cgi-bin/template_list.cgi?pmesg=$pmesg\n\n";
	}
	else
	{
		$_ = $backto;
		if (/preview.cgi/)
		{
    print qq {
    <script language="Javascript">
    var newwin = window.open("$backto", "Preview", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,width=900,height=500,left=25,top=50");
    newwin.focus();
    </script> \n };
#    $pmesg="";
print<<"end_of_html";
<head>
<body>
<script language="JavaScript">
document.location="/cgi-bin/template_disp.cgi?nl_id=$temp_id&mode=U";
</script>
</body></html>
end_of_html
		}
		else
		{
			print "Location: $backto\n\n";
		}
	}
}
$util->clean_up();
exit(0);

sub cb 
{
     my($tag, $url1, $url2, %links) = @_;
	my $query1;
	my $temp_id;
	my $sql;
	my $sth1;
	my $link_id;
	my $ext;
	my $cdir;

     if (($tag eq "img") or ($tag eq "background") or ($url1 eq "background") or (($tag eq "input") and ($url1 eq "src")))
     {
        $_ = $url2;
        if ((/DOMAIN/) || (/IMG_DOMAIN/))
        {
            my $nomove= 1;
        }
        else
        {
            #
            # Get directory and filename
            #
            ($scheme, $auth, $path, $query1, $frag) = uri_split($url2);
            ($name,$frag,$suffix) = fileparse($path);
            my $repl_url = $scheme . "://" . $auth . $frag;
            $img_cnt++;
			my $time_str = time();
            if ($query1 ne "")
            {
                $repl_url = $repl_url . $name . "?" . $query1;
            }
			my $params={};
			$params->{'image'}=$url2;
			my ($imageHostingErrors, $newImageName, $imageExtension, $allImageProperties) = $imageHoster->setUpImageHosting($params);
			$new_name=$newImageName;
           	$repl_url =~ s/\?/\\?/g;
           	$repl_url =~ s/\&/\\&/g;
           	$repl_url =~ s/\(/\\(/g;
           	$repl_url =~ s/\)/\\)/g;
			if ($query1 eq "")
			{
           		$global_text =~ s/$repl_url${name}/http:\/\/{{IMG_DOMAIN}}\/${img_dir}\/$new_name/gi;
			}
			else
			{
           		$global_text =~ s/$repl_url/http:\/\/{{IMG_DOMAIN}}\/${img_dir}\/$new_name/gi;
			}
        }
	}
}

sub log_img
{
    my ($url2,$new_name,$img_dir)=@_;
    my $filename="/tmp/img_upload.log";
    open(LOG,">>$filename");
    print LOG "Uploading <$url2> to <$new_name> directory <$img_dir>\n";
    close(LOG);
}
