#!/bin/perl
@var=("{{NAME}}","{{LOC}}","{{EMAIL_USER_ID}}","{{EMAIL_ADDR}}","{{URL}}",{{IMG_DOMAIN}},"{{DOMAIN}}","{{CID}}","{{FID}}","{{CRID}}","{{FOOTER_SUBDOMAIN}}","{{FOOTER_DOMAIN}}","{{FROMADDR}}","{{MAILDATE}}","{{FOOTER_TEXT}}");
$string="Hi {{NAME}}, this is a test {{FOOTER_TEXT}} {!";
$first = index($string, "{");
while ($first > 0)
{
	$end=index($string,"}}",$first+1);
	if ($end > 0)
	{
		$tstr=substr($string,$first,$end-$first+2);
		$i=0;
		$notfound=0;
		while (($i <= $#var) && ($notfound == 0))
		{
			if ($tstr eq $var[$i])
			{
				$notfound=1;
			}
			$i++;
		}
		if ($notfound == 0)
		{
			$pmesg="One or more bad Variables - $tstr";
		}
		$first = index($string,"{",$end+1);
	}
	else
	{
		$tstr=substr($string,$first);
		$pmesg="One or more bad Variables - $tstr";
		$first=index($string,"{",$first+1);
	}
}
print "Pmsg $pmesg\n";
