#!/usr/bin/perl

# *****************************************************************************************
# deploypool_chunk.cgi
#
# this page displays the list of Deploy Pool Chunks
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
my ($poolid,$pname,$cgname,$profilename,$recs,$totalcnt);
my $chunkID;
my $actionTypeID;
my $start;
my $end;


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
my $pid=$query->param('pid');
$sql="select deployPoolName from DeployPool where deployPoolID=$pid";
$sth=$dbhu->prepare($sql);
$sth->execute();
($pname)=$sth->fetchrow_array();
$sth->finish();

# print out the html page

print "Content-type: text/html\n\n";
print << "end_of_html";
<html>
<body>
		<TABLE cellSpacing=0 cellPadding=3 width="70%" border=0>
		<TBODY>
		<tr><td colspan=5>Pool Name: <b>$pname</b></td></tr>
		<TR bgColor="#509C10" height=15>
		<td>Chunk</td><td>Converter Range</td><td>Clicker Range</td><td>Opener Range</td><td>Deliverable Range</td>
		</TR>
end_of_html

my $RANGE={};
my $oldchunkID=0;
$sql="select chunkID,actionTypeID,startDay,endDay from DeployPoolChunk where deployPoolID=$pid order by chunkID,actionTypeID"; 
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($chunkID,$actionTypeID,$start,$end) = $sth->fetchrow_array())
{
	if ($chunkID != $oldchunkID)
	{
		if ($oldchunkID != 0)
		{
			$reccnt++;
    		if ( ($reccnt % 2) == 0 )
    		{
        		$bgcolor = "$light_table_bg";
    		}
    		else
    		{
        		$bgcolor = "$alt_light_table_bg";
    		}
			print "<tr bgcolor=$bgcolor><td>$oldchunkID</td>";
			my $i=1;
			while ($i <= 4)
			{
				print "<td align=center>$RANGE->{$i}{start} - $RANGE->{$i}{end}</td>";
				$i++;
			}
			print "</tr>";
		}
		$oldchunkID=$chunkID;
	}
	$RANGE->{$actionTypeID}{start}=$start;
	$RANGE->{$actionTypeID}{end}=$end;
}
if ($oldchunkID != 0)
{
	$reccnt++;
    if ( ($reccnt % 2) == 0 )
    {
    	$bgcolor = "$light_table_bg";
    }
    else
    {
		$bgcolor = "$alt_light_table_bg";
	}
	print "<tr bgcolor=$bgcolor><td>$oldchunkID</td>";
	my $i=1;
	while ($i <= 4)
	{
		print "<td align=center>$RANGE->{$i}{start} - $RANGE->{$i}{end}</td>";
		$i++;
	}
	print "</tr>";
}
$sth->finish();

print << "end_of_html";
		</TBODY>
		</TABLE>
</body>
</html>
end_of_html
exit(0);
