#!/usr/bin/perl
#===============================================================================
# Name   : add_article.cgi 
#
#--Change Control---------------------------------------------------------------
# 10/31/06  Jim Sobeck  Creation
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
my $dbh;
my $cid;
my $cname;
my $content_name;
my $content_html;
my $inactive_date;
my $content_date;
my $author;
my $headline;
my $oldsid;
my $article_font;
my $datatype_id;

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}
#------  connect to the util database -----------
my ($dbhq,$dbhu)=$util->get_dbh();
my $content_id=$query->param('cid');
if ($content_id eq '')
{
	$content_id=0;
	$content_name="";
	$content_html="";
	$inactive_date="";
	$author="";
	$headline="";
	$article_font="";
	$oldsid=0;
	$datatype_id=1;
	$sql="select date_format(curdate(),'%m/%d/%y')";
	$sth = $dbhq->prepare($sql) ;
	$sth->execute();
	($content_date) = $sth->fetchrow_array();
	$sth->finish();
}
else
{
	$sql="select article_name,date_format(date_of_content,'%m/%d/%y'),date_format(inactive_date,'%m/%d/%y'),html_code,author,headline,article_font,datatype_id,source_id from article where article_id=$content_id";
	$sth = $dbhq->prepare($sql) ;
	$sth->execute();
	($content_name,$content_date,$inactive_date,$content_html,$author,$headline,$article_font,$datatype_id,$oldsid) = $sth->fetchrow_array();
	$sth->finish();
	if ($inactive_date eq "00/00/00")
	{
		$inactive_date="";
	}
}
#--------------------------------
        print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Edit Newsletter Article Content</title>
</head>

<body>

<table align="center" id="table9" cellSpacing="0" cellPadding="0" align="left" bgColor="#ffffff" border="0">
	<tr vAlign="top">
		<td noWrap align="left">
		<table id="table12" cellSpacing="0" cellPadding="10" width="100%" bgColor="#ffffff" border="0">
			<tr>
				<td vAlign="top" align="left" bgColor="#ffffff" colSpan="10">

	<table width="100%" bgcolor="#FFFFFF" border="0" align="center" cellpadding="8">
	<tr>
		<td align="center" colspan="2" bgcolor="#DFDFDF">
		<font style="font-family: Trebuchet MS, Arial; font-size: 16px;">Edit Newsletter Article Content</td>
	</tr>
	</table>

<font style="font-family: Trebuchet MS, Arial; font-size: 12px; ">

<SCRIPT language=JavaScript>
        function SaveFunc(btn)
        {
            document.campform.nextfunc.value = btn;
            document.campform.submit();
        }
function add_headline()
{
    document.campform.nextfunc.value="/cgi-bin/headline.cgi?aid=$content_id";
    document.campform.submit();
}
function edit_headline()
{
    document.campform.nextfunc.value="/cgi-bin/edit_headline.cgi?aid=$content_id&sid="+document.campform.headline.value;
    document.campform.submit();
}
function delete_headline()
{
    document.campform.nextfunc.value="/cgi-bin/del_headline.cgi?aid=$content_id&sid="+document.campform.headline.value;
    document.campform.submit();
}
function add_subject()
{
    document.campform.nextfunc.value="/cgi-bin/article_subject.cgi?aid=$content_id";
    document.campform.submit();
}
function edit_subject()
{
    document.campform.nextfunc.value="/cgi-bin/edit_article_subject.cgi?aid=$content_id&sid="+document.campform.subject.value;
    document.campform.submit();
}
function delete_subject()
{
    document.campform.nextfunc.value="/cgi-bin/del_article_subject.cgi?aid=$content_id&sid="+document.campform.subject.value;
    document.campform.submit();
}
function add_blurb()
{
    document.campform.nextfunc.value="/cgi-bin/blurb.cgi?aid=$content_id";
    document.campform.submit();
}
function edit_blurb()
{
    document.campform.nextfunc.value="/cgi-bin/edit_blurb.cgi?aid=$content_id&sid="+document.campform.blurb.value;
    document.campform.submit();
}
function delete_blurb()
{
    document.campform.nextfunc.value="/cgi-bin/del_blurb.cgi?aid=$content_id&sid="+document.campform.blurb.value;
    document.campform.submit();
}
</SCRIPT>

				<form name="campform" method="post" action="/cgi-bin/article_save.cgi">
					<input style="font-size: 10px; " type="hidden" value="$content_id" name="content_id">
					<input style="font-size: 10px; " type="hidden" value="" name="nextfunc">
					<b>Article Name:</b><br>
					<input style="font-size: 10px; " maxLength="255" size="50" value="$content_name" name="content_name"><br>
					<br>
					
		<table width="100%" cellspacing="0" cellpadding="0">
			<tr>
				<td width="50%" valign="top"><font style="font-family: Trebuchet MS, Arial; font-size: 12px; ">
					<b>Date of Content: (MM/DD/YY - Default = today):</b><br>
					<input style="font-size: 10px; " maxLength="8" size="10" value="$content_date" name="content_date"></td>
				<td width="50%" valign="top"><font style="font-family: Trebuchet MS, Arial; font-size: 12px; ">
					<b>Inactive Date: (MM/DD/YY):</b><br>
					<input style="font-size: 10px; " maxLength="8" size="10" name="inactivate_date" value="$inactive_date"></td>
			</tr>
		</table>
					<b><br>
					<b>Article Font</b><br>
					<select style="font-size: 10px; " name="article_font">
end_of_html
if ($article_font eq "Georgia")
{
	print "<option value=\"Georgia\" selected>Georgia</option>";
}
else
{
	print "<option value=\"Georgia\">Georgia</option>";
}
if ($article_font eq "Times New Roman")
{
	print "<option value=\"Times New Roman\" selected>Times New Roman</option>";
}
else
{
	print "<option value=\"Times New Roman\">Times New Roman</option>";
}
if ($article_font eq "Arial")
{
	print "<option value=\"Arial\" selected>Arial</option>";
}
else
{
	print "<option value=\"Arial\">Arial</option>";
}
if ($article_font eq "Verdana")
{
	print "<option value=\"Verdana\" selected>Verdana</option>";
}
else
{
	print "<option value=\"Verdana\">Verdana</option>";
}
print<<"end_of_html";
					</select> </p>
					<b>Category</b><br>
					<select style="font-size: 10px; " name="category_id">
end_of_html
$sql="select datatype_id,type_str from datatypes order by type_str";
my $sth1=$dbhu->prepare($sql);
$sth1->execute();
my $type_id;
my $type_str;
while (($type_id,$type_str)=$sth1->fetchrow_array())
{
	if ($type_id == $datatype_id)
	{
		print "<option value=$type_id selected>$type_str</option>\n";
	}
	else
	{
		print "<option value=$type_id>$type_str</option>\n";
	}
}
$sth1->finish();
print<<"end_of_html";
</select>
					<p>
<b><a href="JavaScript:add_subject();">Default Subject Lines</a>:</b> (used only when scheduled advertiser has no subject lines in rotation)<br>

							<select name="subject" style="font-size: 10px; ">
end_of_html
$sql="select subject_id,subject from article_subject where article_id=$content_id order by subject";
$sth1=$dbhq->prepare($sql);
$sth1->execute();
my $hid;
my $tsubject;
while (($hid,$tsubject) = $sth1->fetchrow_array())
{
	print "<option value=$hid>$tsubject</option>\n";
}
$sth1->finish();
print<<"end_of_html";
							</select><input style="font-size: 10px; " onclick="add_subject();" type="button" value="Add"><input style="font-size: 10px; " onclick="edit_subject();" type="button" value="Edit"><input style="font-size: 10px; " onclick="delete_subject();" type="button" value="Delete">

							<p><b><a href="JavaScript:add_headline();">Newletter Headlines</a>:<br>

							<select name="headline" style="font-size: 10px; ">
end_of_html
$sql="select headline_id,headline from article_headline where article_id=$content_id order by headline";
$sth1=$dbhq->prepare($sql);
$sth1->execute();
my $hid;
my $theadline;
while (($hid,$theadline) = $sth1->fetchrow_array())
{
	print "<option value=$hid>$theadline</option>\n";
}
$sth1->finish();
print<<"end_of_html";
							</select><input style="font-size: 10px; " onclick="add_headline();" type="button" value="Add"><input style="font-size: 10px; " onclick="edit_headline();" type="button" value="Edit"><input style="font-size: 10px; " onclick="delete_headline();" type="button" value="Delete">

							<p></p><b><a href="JavaScript:add_blurb();">Newsletter Blurbs</a>:</b><br>
							<select style="font-size: 10px; " name="blurb">
end_of_html
$sql="select blurb_id,blurb from article_blurb where article_id=$content_id order by blurb";
$sth1=$dbhq->prepare($sql);
$sth1->execute();
my $bid;
my $blurb;
while (($bid,$blurb) = $sth1->fetchrow_array())
{
	print "<option value=$bid>$blurb</option>\n";
}
$sth1->finish();
print<<"end_of_html";
							</select><input style="font-size: 10px; " onclick="add_blurb();" type="button" value="Add"><input style="font-size: 10px; " onclick="edit_blurb();" type="button" value="Edit"><input style="font-size: 10px; " onclick="delete_blurb();" type="button" value="Delete"><br>
					
					<p><hr>

					<b>Article Title:</b><br>
					<input style="font-size: 10px; " style="BACKGROUND-COLOR: #ffffa0" maxLength="80" size="80" value="$headline" name="article_headline"></p>
					<p><b>Article Author:</b><br>
					<input style="font-size: 10px; " style="BACKGROUND-COLOR: #ffffa0" maxLength="30" size="30" name="article_author" value="$author"><br>
					<br>
					<b>Article Body (in HTML code):<br>
					<textarea style="font-size: 12px; font-family: Trebuchet MS, Arial, Verdana ; " name="html_code" rows="15" cols="130">$content_html</textarea> </p>
					<b>Article Source: <select name=article_source>
					<option value=0 selected>None</option>
end_of_html
					$sql="select source_id,source_name from article_source order by source_name";
					$sth=$dbhu->prepare($sql);
					$sth->execute();
					my $sid;
					my $sname;
					while (($sid,$sname)=$sth->fetchrow_array())
					{
						if ($sid == $oldsid)
						{
							print "<option selected value=$sid>$sname</option>\n";
						}
						else
						{
							print "<option value=$sid>$sname</option>\n";
						}
					}
					$sth->finish();
print<<"end_of_html";
					</select><br>
					<table id="table15" cellPadding="5" width="66%" bgColor="white">
						<tr>
							<td align="middle" width="47%">
							<a href="/cgi-bin/mainmenu.cgi">
							<img height="22" src="/images/home_blkline.gif" width="81" border="0"></a></td>
							<td align="middle" width="47%">
							<input style="font-size: 10px; " type="image" height="22" width="81" src="/images/save_rev.gif" border="0" name="I1"> <font style="font-family: Trebuchet MS, Arial; font-size: 12px; ">
							<a href="/cgi-bin/article_list.cgi">
							<img src="/images/cancel_blkline.gif" border="0"></a></font></td>
							<!--							<td align="middle" width="50%">
							<a href="/cgi-bin/footer_content_preview.cgi?cid=14 target=_blank">
							<img height="22" src="/images/preview_rev.gif" width="81" border="0"></a></td> -->
						</tr>
					</table>
				</form>
				</b>&nbsp;</P></td>
			</tr>
		</table>
		</td>
	</tr>
</table>
</font>
</body>

</html>
end_of_html
