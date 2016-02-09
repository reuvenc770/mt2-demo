use WWW::Curl::easy;

$url2="http://www.affiliateimages.com/fimg/477_2.jpg";
            my $curl = WWW::Curl::easy->new();
            $curl->setopt(CURLOPT_NOPROGRESS, 1);
            $curl->setopt(CURLOPT_MUTE, 0);
            $curl->setopt(CURLOPT_FOLLOWLOCATION, 1);
            $curl->setopt(CURLOPT_TIMEOUT, 30);
            open HEAD, ">head.out";
            $curl->setopt(CURLOPT_WRITEHEADER, *HEAD);
            open BODY, "> /var/www/util/tmpimg/a.a";
            $curl->setopt(CURLOPT_FILE,*BODY);
            $curl->setopt(CURLOPT_URL, $url2);
            my $retcode=$curl->perform();
            if ($retcode == 0)
            {
            }
            else
            {
    print "$retcode / ".$curl->errbuf."\n";
            }
            close HEAD;
