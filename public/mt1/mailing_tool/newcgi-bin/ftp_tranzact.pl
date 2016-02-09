#!/usr/bin/perl

use Net::FTP;
use lib "/var/www/html/newcgi-bin";
use util;

my $util = util->new;
my $dbh;
my $sql;
my $sth;
my $server;
my $ip;
my $username;
my $to_dir;

my $dbhq;
my $dbhu;
my $cdate;
my @FINFO = (
["NetBlue","netblue","sWUt4utHa"],
["ClickPromise","primeq","prxx#1m3q"],
["CuttingEdge","cuttingedge","vbgf\$%ng3dg3"],
["LeadClick","leadclick","ha9a6Pu7a"],
["grdm","grdm","grdmp\@ss"],
["Monetizeit","monetizeit","Swuqu53sp"],
["oneonone","oneonone","0ncdf\@#n0n3"],
["PermissionData","permissiondata","JEtE9eRec"],
["SLWorks","slworkstwo","slwDSA4w0"],
["Vendare","vendare","v3nd\@re"],
["BrandInteractive","brand09","zUCaga2At"],
["VirtualWorks","virtualworks","v1rtu\@lw0rks"],
["TalonLeads","talon09","4upreGuda"],
["eSolutionsMedia","esolutions","3s0lut10ns"],
["DMiPartners","dmipartners","ru8HAx7f7"],
["RevenueLoop","revloop","7rU7rEphu"],
["GMBDirect","gmbdirect","YDFE43%\$e"],
["ARMLexLaw","lexlaw","hubeTR8wa"],
["ActionResults","ActionR","b8trEtHES"],
["SWVentures","swventures","6RUNawa7U"]
);

($dbhq,$dbhu)=$util->get_dbh();
#$server="66.109.21.234";
#$username="spirevision";
#$password="yqCgPAUH";
$server="data.bloosky.com";

if ($ARGV[0] eq ""){

    $sql="select date_format(date_sub(curdate(),interval 1 day),'%m.%d.%Y')";
    $sth = $dbhq->prepare($sql);
    $sth->execute();
    ($cdate)=$sth->fetchrow_array();
    $sth->finish();

}

else{
    $cdate=$ARGV[0];
}

print "Cdate $cdate\n";

my $some_dir="/var/www/util/data/new";
opendir(DIR, $some_dir);
@dots = grep { /$cdate/ } readdir(DIR);
closedir DIR;
print "$cdate - $#dots\n";

if ($#dots >= 0)
{
        $i=0;
        while ($i <= $#dots)
        {
                my $tname = $dots[$i];

                if($tname =~ /ProAds/){

                        my ($proAds, $clientID, $date) = split('_', $tname);

                        $sql="select replace(first_name,' ','') from user where user_id = $clientID";

                        $sth = $dbhq->prepare($sql);
                        $sth->execute();
                        if (($tname) = $sth->fetchrow_array())
                        {
                                ftp_file($tname,$dots[$i]);
                                print "Sent $dots[$i]\n";
                        }
                        else
                        {
                                print "Removing $dots[$i]\n";
                                unlink("/var/www/util/data/new/$dots[$i]");
                        }
                        $sth->finish();

                } #end if
                $i++;
        }
}

sub ftp_file
{
        my ($tname,$infile)=@_;
        my $j=0;
        my $username="";
        my $password="";
        while ($j <= $#FINFO)
        {
                if ($tname eq $FINFO[$j][0])
                {
                        $username=$FINFO[$j][1];
                        $password=$FINFO[$j][2];
                        break;
                }
                $j++;
        }
        print "<$tname> <$username> <$password>\n";
        if ($username eq "")
        {
                return;
        }
    my $ftp = Net::FTP->new("$server", Timeout => 30, Debug => 0, Passive => 0);
        if ($ftp)
        {
        $ftp->login($username,$password) or print "Cannot login ", $ftp->message;
                $ftp->ascii();
        $ftp->put("/var/www/util/data/new/$infile") or print "put failed ", $ftp->message;

                rename("/var/www/util/data/new/$infile","/mailfiles/sav/$infile");

                $ftp->quit;
        }
}
