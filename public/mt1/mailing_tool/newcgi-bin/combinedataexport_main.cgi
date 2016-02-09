#!/usr/bin/perl

# *****************************************************************************************
# combinedataexport_main.cgi
#
# this page displays allows edit/add of DataExportCombine 
#
# History
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $pms = util->new;
my $query = CGI->new;
my $sth;
my $sql;
my $dbh;
my $errmsg;
my $user_id;
my $bgcolor;
my $reccnt=0;
my $images = $pms->get_images_url;
my $alt_light_table_bg = $pms->get_alt_light_table_bg;
my $light_table_bg = $pms->get_light_table_bg;
my $table_text_color = $pms->get_table_text_color;
my ($combineID,$fileName,$lastUpdated,$lastUpdatedTime,$recordCount);
my $ftpFolder;
my $cstr="Update";
my @OLDFILES;
my $EXPORTS;
my $pid=$query->param('pid');
if ($pid eq "")
{
	$pid=0;
}
if ($pid == 0)
{
	$cstr="Add";
}

# connect to the pms database
my ($dbhq,$dbhu)=$pms->get_dbh();

# check for login
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $pms->clean_up();
    exit(0);
}
$OLDFILES[0]=0;	
$OLDFILES[1]=0;	
$OLDFILES[2]=0;	
$OLDFILES[3]=0;	
if ($pid > 0)
{
	$sql = "select fileName,ftpFolder from DataExportCombine where combineID=?";
	$sth = $dbhq->prepare($sql);
	$sth->execute($pid);
	($fileName,$ftpFolder) = $sth->fetchrow_array();
	$sth->finish();
	#
	$sql="select exportID from DataExportCombineJoin where combineID=?";
	$sth = $dbhq->prepare($sql);
	$sth->execute($pid);
	my $exportID;
	my $i=0;
	while (($exportID)=$sth->fetchrow_array())
	{
		$OLDFILES[$i]=$exportID;
		$i++;
	}
	$sth->finish();
}
else
{
	$fileName="";
	$ftpFolder="";
}
$sql="select exportID,fileName from DataExport where status='Active' order by fileName";
$sth = $dbhq->prepare($sql);
$sth->execute();
my $exportID;
my $efile;
while (($exportID,$efile)=$sth->fetchrow_array())
{
	$EXPORTS->{$exportID}=$efile;
}
$sth->finish();

# print out the html page

util::header("Data Export Combine");

print << "end_of_html";
</TD>
</TR>
<TR>
<TD vAlign=top align=left bgColor=#999999>

	</form>
		<form method=post name="campform" action=combinedataexport_upd.cgi target=_top>
		<input type=hidden name=pid value=$pid>
		<TABLE cellSpacing=0 cellPadding=0 width=1000 bgColor=#ffffff border=0>
		<TBODY>
		<tr>
		<td colspan=2><br>Filename(use {{date}} in name for unique date): <input type=text name=pname size=60 maxlength=255 value="$fileName"></td></tr>
		<tr>
		<td colspan=2>Ftp Folder: <input type=text name=ftpFolder size=20 maxlength=20 value="$ftpFolder"></td></tr>
end_of_html
my $i=0;
my $filecnt=4;
while ($i < $filecnt)
{
	my $j=$i+1;
	print "<tr><td colspan=2>File $j: <select name=efile><option value=0>None</option>";
	foreach my $value (sort {$EXPORTS->{$a} cmp $EXPORTS->{$b}} (keys %{$EXPORTS}))
	{
		my $exportID=$value;
		my $efile=$EXPORTS->{$exportID};
		if ($exportID == $OLDFILES[$i])
		{
			print "<option value=$exportID selected>$efile</option>";
		}
		else
		{
			print "<option value=$exportID>$efile</option>";
		}
	}
	print "</select></td></tr>";
	$i++;
}
print<<"end_of_html";
<tr><td colspan=2 align=center><input type=submit value="$cstr Data Export Combine"></td></tr>
		<tr>
<td align="center" valign="top"><br>
                <a href="combinedataexport_list.cgi" target=_top>
                <img src="$images/home_blkline.gif" border=0></a></TD>
		</tr>
		<TR>
		<TD><IMG height=15 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>
		</form>
	</TD>
	</TR>
	</TBODY>
	</TABLE>

</TD>
</TR>
<TR>
<TD noWrap align=left height=17>
end_of_html

$pms->footer();

# exit function

$pms->clean_up();
exit(0);
