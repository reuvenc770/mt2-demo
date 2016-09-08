<?php

namespace App\Services;
use App\Repositories\DeployRepo;
use App\Exceptions\ValidationException;
use App\Services\CakeEncryptedLinkService;
use App\Services\UrlFormatService;
use App\Services\LinkService;
use App\Services\CakeRedirectDomainRepo;
use League\Flysystem\Filesystem;
use League\Flysystem\ZipArchive\ZipArchiveAdapter;
use Storage;

class PackageZipCreationService {

    private $deployRepo;
    private $encryptionService;
    private $deploy;

    const STORAGE_PATH_BASE = './files';

    const IMAGE_REPLACEMENT_ACCOUNTS = [
        'BH001', 'BH002', 'BH003', 'BH004', 'BH005', 'BH006', 
        'BH007', 'BH008', 'BH009', 'BH010', 'BH011', 'BH012', 
        'BH013', 'BH014', 'BH015', 'BH016', 'BH017', 'BH018',
        'MAR1', 'MAR2', 'MAR3', 'MAR4', 'MAR5', 'MAR6', 'MAR7', 
    ];

    private $saveDirectory;

    public function __construct(
            DeployRepo $deployRepo, 
            CakeEncryptedLinkService $encryptionService, 
            UrlFormatService $urlFormatter, 
            LinkService $linkService,
            CakeRedirectDomainRepo $cakeRedirectRepo) {

        $this->deployRepo = $deployRepo;
        $this->encryptionService = $encryptionService;
        $this->urlFormatter = $urlFormatter;
        $this->linkService = $linkService;
        $this->cakeRedirectRepo = $cakeRedirectRepo;

    }

    // useful name for now
    public function createPackage($id) {

        // Prepare some required values
        $deploy = $this->deployRepo->getDeploy($id);
        $this->deployId = $deploy->id;

        $this->saveDirectory = self::STORAGE_PATH_BASE . '/' . $deploy->id . '/';

        $affiliateId = $deploy->cake_affiliate_id;

        $offer = $deploy->offer;
        $offerTypeId = $offer->offer_payout_type_id;
        $contentDomain = $deploy->contentDomain()->first()->main_site;

        // 1. validate package
        $this->validate($deploy);

        // 2. assign espCakeDomain based off of offer type and affiliate id
        // will be used to remove cake domain in offer tracking url and elsewhere
        $defaultCakeDomain = $this->cakeRedirectRepo->getDefaultRedirectDomain();
        $espCakeDomain = $this->cakeRedirectRepo->getRedirectDomain($affiliateId, $offerTypeId);

        // 3. process redir1.cgi and ccID links
        $creative = $deploy->creative;
        $creativeHtml = $creative->creative_html;

        // hopefully we can remove this in the near future due to redoing how links are handled
        if ( preg_match('/redir1\.cgi/', $creativeHtml) && preg_match('/\&ccID\=/', $creativeHtml) ) {

            $creativeHtml = preg_replace('/\&sub\=/', '&XXX=', $creativeHtml);
            $creativeHtml = preg_replace('/\&amp;/', '&', $creativeHtml);

            // parse "extra" links that use the ccID parameter
            $creativeHtml = $this->parseCCIDLinks($html);
        }

        // 4. Format offer unsub link (merged with 10)
        $offerUnsubLink = $offer->unsub_link;
        $offerUnsubLinkId = $this->linkService->getLinkId($offerUnsubLink);
        $this->linkService->checkLink($offerUnsubLink); // do something with this result

        /* 5-9 MERGED WITH 11 */ /* 10 MERGED WITH 4 */

        // 11. replacing tokens in the full html

        $templateHtml = $deploy->mailingTemplate()->first()->template_html;

        // remove doctype, html, & body from creative
        $dom = new DOMDocument();
        $dom->loadHTML($creativeHtml);
        $dom->doctype->parentNode->removeChild($dom->doctype);
        $this->stripEnclosingElement($dom, "html");
        $this->stripEnclosingElement($dom, "body");

        $creativeHtml = $dom->saveHTML();


        $fullHtml = str_replace("{{CREATIVE}}", $creativeHtml, $fullHtml);
        $fullHtml = str_replace("{{TRACKING}}", replace, $fullHtml);
        $fullHtml = str_replace("{{TIMESTAMP}}", strftime('%Y%m%d%H%M%S'), $fullHtml);

        // Need to get random strings for image domains $img_prefix
        $random1 = $this->urlFormatter->getDefinedRandomString();
        $random2 = $this->urlFormatter->getDefinedRandomString();
        $imgPrefix = "$contentDomain/$random1/$random2"; // this is used in a little bit

        $unsubText = $this->createUnsubHtml($offer, $imgPrefix);
        $fullHtml = str_replace("{{ADV_UNSUB}}", $unsubText, $fullHtml);

        foreach($offer->trackingLinks()->get() as $link) {

            $linkNumber = $link->link_num;
            $url = $link->url;

            $token = $linkNumber === 1 ? "{{URL}}" ; "{{URL$i}}";

            if (strpos($fullHtml, $token) !== false) {

                $url = $this->getOfferTrackingLink($offerId, $linkNumber);

                $url = str_replace("{{CID}}", $esp ...., $url);
                $url = str_replace("{{FOOTER}}", "{{FOOTER}}_{$sendDate}", $url);
                $url = preg_replace('/a=\d+/', "a=$affiliateId", $url); // old affiliate id
                $url = str_replace("up.gravitypresence.com", $espCakeDomain, $url);
                $url = str_replace($defaultCakeDomain, $espCakeDomain, $url);
                $url = str_replace('a=13', "a=$affiliateId", $url);

                $creativeLink = str_replace("{{DEPLOY_ID}}", $this->deployId, $url); // used at least for 1 ...

                if ($deploy->encrypt_cake) {
                    $url = $this->encryptionService->encryptCakeLink($url);
                }
                if ($deploy->fully_encrypt) {
                    $url = $offerTrackingUrl = $this->encryptionService->fullEncryptLink($url);
                }

                $linkValid = $this->linkService->checkLink($url);

                $linkId = $this->linkService->getLinkId($url);

                $redirectLink = $this->formatUrl($deploy->urlFormat, $linkId) 
                                ?: "http://$content_domain/cgi-bin/redir1.cgi?eid=$eidfield&cid=1&em=$emailfield&id=$linkId&n=$clientID&f=$fid&s=$sid&c=$crid&tid=$template_id&footerid=$footer_id&ctype=R";

                $fullHtml = str_replace($token, $redirectLink);
            }    
        }

        #$fullHtml = str_replace("{{URL}}", $redirectLink, $fullHtml);
        $fullHtml = str_replace('{{CID}}', 1, $fullHtml); // all other instances not caught replaced with "1"
        $fullHtml = str_replace("{{ADV_UNSUB_URL}}", $offerUnsubLink, $fullHtml);
        $fullHtml = str_replace("{{CRID}}", $creativeId, $fullHtml);
        $fullHtml = str_replace("{{FID}}", replace, $fullHtml);
        $fullHtml = str_replace("{{NID}}", replace, $fullHtml);
        $fullHtml = str_replace("{{F}}", replace, $fullHtml);
        $fullHtml = str_replace("{{S}}", replace, $fullHtml);
        $fullHtml = str_replace("footerid={{FOOTER}}", replace, $fullHtml);
        $fullHtml = str_replace("{{FOOTER}}", replace, $fullHtml);
        $fullHtml = str_replace("{{TID}}", replace, $fullHtml);
        $fullHtml = str_replace("{{EMAIL_ADDR}}", replace, $fullHtml);
        $fullHtml = str_replace("{{EMAIL_USER_ID}}", replace, $fullHtml);

        $fullHtml = $this->presetChanges($fullHtml);

        $fullHtml = str_replace("{{IMG_DOMAIN}}", $contentDomain, $fullHtml);
        $fullHtml = str_replace("{{DOMAIN}}", $contentDomain, $fullHtml);

        $fullHtml = $this->parseImageLinks($fullHtml);

        // 12. Create files and zip

        $deployId = $deploy->id;
        $espAccountName = $deploy->espAccount()->first()->account_name;
        $offerName = $deploy->offer()->first()->name;
        $creativeName = $deploy->creative()->first()->file_name;
        $creativeId = $deploy->creative_id;

        // Make the temporary directory
        $dir = $deployId;
        Storage::createDir($dir);

        // store the HTML 
        $fileName = "{$sendDate}_{$offerName}_{$creativeName}_{$creativeId}_{$offerTrackingUrlLinkId}";
        Storage::disk("local")->put($this->saveDirectory . "$fileName.html", $fullHtml);

        // Store just the text and links in a file
        [...]
        Storage::disk("local")->put($this->saveDirectory . "$fileName.txt", $fullHtml);

        // create the asset.txt file
        $assetText = $this->createAssetText($deploy);
        Storage::disk("local")->put($this->saveDirectory . "asset.txt", $assetText);
        
        $zipName = "$deployId_$espAccountName_"
        $zipper = new FileSystem(new ZipArchiveAdapter("/files/$dir/"));

        // send this file to the esp

        // 13. Email Dimitri and JHecht
        // 14. Callbacks
    }


    // can't be this simple - we need quite a bit more, as seen from the signature
    // we also need to switch emailId and emailAddress for emailIdToken and emailAddressToken -

    private function formatUrl($urlFormat, $linkId) {
        if ('New' === $urlFormat) {
            return $this->urlFormatter->formatNewUrl($type, $contentDomain, $emailId, $linkId);
        }
        elseif ('GMail' === $urlFormat) {
            return $this->urlFormatter->formatGmailUrl($type, $contentDomain, $emailId, $linkId);
        }
        else {
            return '';
        }
    }

    private function createAssetText($deploy) {
        // do we still need CLIENT?
        $text = <<<TXT
FROM: {$deploy->from()->first()->from}
SUBJECT: {$deploy->subject()->first()->subject}
TEMPLATE: {$deploy->mailingTemplate()->first()->template_name}
CLIENT: {$deploy->espAccount()->first()->esp()->first()->name}
FOOTER: 
TXT;

        return $text;
    }


    /*
        Method used to 
    */
    private function stripEnclosingElement(&$dom, $elementName) {

        $element = $dom->getElementsByTagName($elementName)->item(0);
        $fragment = $dom->createDocumentFragment();

        while ($element->childNodes->length > 0) {
            $frament->appendChild($html->childNodes->item(0))
        }

        $element->parentNode->replaceChild($fragment, $element);
    }


    private function createUnsubHtml($offer, $imageUrlPrefix) {
        if ($offer) {
            if ('TEXT' === $offer->unsub_use) {
                $unsubText = $offer->unsub_text;
            }
            elseif ('' !== $offer->unsub_img) {
                $regex = '/\.(jpg|jpeg|gif|bmp|png)/i';
                $unsubImg = preg_replace($regex, '', $offer->unsub_img);

                if (preg_match('/\//', $unsubImg)) {
                    if (0 === $offerUnsubLinkId) {
                        $unsubText = "<img src=\"http://$imageUrlPrefix/$unsub_img\" border=0><br><br>";
                    }
                    else {
                        $unsubText = "<a href=\"{{ADV_UNSUB_URL}}\"><img src=\"http://$imageUrlPrefix/{$offer->unsub_img}\" border=0></a><br><br>";
                    }
                }
                else {
                    if (0 === $offerUnsubLinkId) {
                        $unsubText = "<img src=\"http://$imageUrlPrefix/images/unsub/{$offer->unsub_img}\" border=0><br><br>";
                    }
                    else {
                        $unsubText = "<a href=\"{{ADV_UNSUB_URL}}\"><img src=\"http://$imageUrlPrefix/images/unsub/{$offer->unsub_img}\" border=0></a><br><br>";
                    }
                }
            }
            else {
                throw new ValidationException('Deploy has offer set but no unsub information');
            }
        }
    }

    /**
     *  Link Processor 1: Process Image links
     *
     *
     *
     *
     *
     */

    private function parseImageLinks($html) {
        $dom = new DOMDocument();
        $dom->loadHTML($html);

        #$urls = [];

        $dom = $this->parseImageLinksLoop($dom, 'img');
        $dom = $this->parseImageLinksLoop($dom, 'background');
        $dom = $this->parseImageLinksLoop($dom, 'input');

        return $dom->saveHTML();

        /*
        $urls = array_unique($urls);

        foreach($urls as $map) {
            $old = $map['from'];
            $new = $map['to'];

            $html = str_replace($old, $new, $html);
        }

        return $html;
        */
    }

    // what we could do instead is return an array of updates to make
    // and then run through them once and do a global update on the updated html
    // array unique on the key
    // the array is formatted as follows:
    // [['from' => $oldText, 'to' => $newText, 'type' => regular|regex]]

    private function parseImageLinksLoop($dom, $element) {
        foreach($dom->getElementsByTagName($element) as $link) {
            $url = $link->getAttribute('href');

            $url = preg_replace('/\{\{DOMAIN\}\}/', 'contentstaging-01.mtroute.com', $url);
            $url = preg_replace('/\{\{IMG_DOMAIN\}\}/', 'contentstaging-01.mtroute.com', $url);
            $urlContents = parse_url($url);

            $scheme = $urlContents['scheme'];
            $host = $urlContents['host'];
            $path = $urlContents['path'];
            $query = $urlContents['query'];

            $fileName = '';
            $directories = '';
            $location = $this->getSaveDirectory() . '/images';

            if ('' === $url) {
                continue;
            }
            elseif (strpos($url, 'open.cgi') === false) {
                $filename = $this->saveFileGetType($url, $location, $fileName);

                // how should we get $contentDomain in here?
                // $this->contentDomain is easy ...
                if ($this->shouldReplaceImageUrl($espAccountName)) {
                    $newUrl = "http://{$contentDomain}/$fileName";
                }
                elseif (publicators) {
                    if ($this->shouldRemoveFileExtension($espAccountName)) {
                        $fileName = str_replace('.jpg', '', $fileName);
                    }
                    $newUrl = "http://{$contentDomain}/$fileName";
                }
                else {
                    $newUrl = "/images/$fileName";
                }
                
            }

            $link->setAttribute('href', $newUrl);
        }

        return $dom;
    }

    /**
     *  We need to pull the files and save them in an /images directory
     *  and also get the MIME type for the image so we can correct it in the creative.
     */
    private function saveFile($url, $saveLocation, $fileName) {

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $result = curl_exec($ch);
        $mimeType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        curl_close($ch);

        // give file correct file type based off of $mimeType
        if (strpos($mimeType, 'gif') !== false) {
            $fileName = str_replace('.jpg', '.gif', $fileName);
        }
        elseif (strpos($mimeType, 'png') !== false) {
            $fileName = str_replace('.jpg', '.png', $fileName);
        }
        elseif (strpos($mimeType, 'bmp') !== false) {
            $fileName = str_replace('.jpg', '.bmp', $fileName);
        }
        
        // save this file to the designated location
        $path = $saveLocation . '/' . $fileName;
        Storage::disk("local")->put($path, $result);

        return $fileName;
    }

    private function shouldReplaceImageUrl($espAccountName) {
        return in_array($espAccountName, self::IMAGE_REPLACEMENT_ACCOUNTS);
    }

    private function shouldRemoveFileExtension($espAccountName) {
        return $espAccountName not in ['PUB008', 'PUB009', 'PUB010', 'PUB014'];
    }

    private function getSaveDirectory() {
        return self::STORAGE_PATH_BASE . '/' . $this->deploy->id . '/';
    }

    /**
     *  Link processor 2: Parse ccID links
     *  - for <a> and <area> tags with ccID that are not redirect/unsub links:
     *      remove the magic ccID value and use that as the c value in the cake link
     *      add &p=c for CPC link
     *      check and encrypt as necessary
     *      preserve corresponding link id in new public link
     *      replace the first link with the second
     */

    private function parseCCIDLinks($html) {
        $dom = new DOMDocument();
        $dom->loadHTML($html);

        $dom = $this->parseCCIDLinksLoop($dom, 'a');
        $dom = $this->parseCCIDLinksLoop($dom, 'area');
        return $dom->saveHTML();
 
    }

    private function parseCCIDLinksLoop($dom, $element) {
        foreach($dom->getElementsByTagName($element) as $link) {
            $url = $link->getAttribute('href');

            if ('' === $url) {
                continue;
            }
            elseif (preg_match('/\{\{URL\}\}/', $url) || preg_match('/\{\{ADV_UNSUB_URL\}\}/', $url)) {
                continue;
            }
            elseif (preg_match('/ccID/', $url)) {

                $url = preg_replace('/\?/', '\?', $url);
                $url = preg_replace('/\[/', '\[', $url);

                $ccIdIndex = strpos($url, '&ccID='); 
                $ccId = substr($url, $ccIdIndex); // it appears that ccId must be the last item in the URL 

                // actually, we can remove the hard link limit altogether and then don't need these wacky callbacks .. 

                $trackingLink = "http://{$this->esp_cake_domain}/?a=$affiliateID&c=$ccID&s1="
                                . $this->deployId 
                                ."&s2={{EMAIL_USER_ID}}_".$crid."_".$fid."_".$sid."_".$template_id
                                ."&s4=$esp&s5=0_0_0_0_".$global_senddate;

                if ($offer_type === 'CPC') { // need to get this somehow
                    $trackingLink .= '&p=c';
                }

                if ($deploy->encrypt_cake) { // probably need to pass in deploy
                    $trackingLink = $this->encryptionService->encryptCakeLink($trackingUrl);
                }

                if ($deploy->full_encrypt) {
                    $trackingLink = $this->encryptionService->fullEncryptLink($trackingUrl);
                }

                $trackingLinkId = $this->linkService->getLinkId($trackingUrl);

                $this->linkService->checkLink($trackingLink);

                $publicLink = $this->formatUrl($urlFormat, $link);

                $link->setAttribute('href', $publicLink);
            }
        }

        return $dom;
    }

    private function runReplacementsOnText($templateHtml, $creativeHtml) {
        $replacements = [
            "{{CREATIVE}}" => $creativeHtml,
            "{{TRACKING}}" => "", // set to tracking url
            "{{CONTENT_HEADER}}" => "",
            "{{CONTENT_HEADER_TEXT}}" => "",
            "{{TIMESTAMP}}" => "", // set to timestamp
            "{{REFID}}" => "",
            "{{CLICK}}" => "",
            "{{ADV_UNSUB}}" => "", // set to advertiser unsub temp_str (719)
            "{{HEADER_TEXT}}" => "", 
            "{{FOOTER_TEXT}}" => "",
            "{{URL}}" => "", // set to xlink3
            "{{ADV_UNSUB_URL}}" => "", // xlink1
            "{{FOOTER_STR}}" => "", 
            "{{CID}}" => "1",
            "{{CRID}}" => "", // creative id
            "{{FID}}" => "", // fid?
            "{{NID}}" => "", // sid?
            "{{MID}}" => "",
            "{{LINK_ID}}" => "", // some link_id (see 829)
            "{{F}}" => "", // fid
            "{{S}}" => "", // sid
            "{{CWPROGID}}" => "",
            "{{HEADER}}" => "",
            "footerid={{FOOTER}}" => "footerid={$this->footerId}", // -> footerid=$footerid
            "{{FOOTER}}" => "", // footerString
            "{{BINDING}}" => "",
            "{{TID}}" => "", // template id
            "{{EMAIL_ADDR}}" => "", // $emailfield
            "{{EMAIL_USER_ID}}" => "", //$eid
        ];

        $replacements = [
            "{{CONTENT_HEADER}}" => "",
            "{{CONTENT_HEADER_TEXT}}" => "",
            "{{REFID}}" => "",
            "{{CLICK}}" => "",
            "{{HEADER_TEXT}}" => "", 
            "{{FOOTER_TEXT}}" => "",
            "{{FOOTER_STR}}" => "", 
            "{{CID}}" => "1",
            "{{MID}}" => "",
            "{{CWPROGID}}" => "",
            "{{HEADER}}" => "",
            "{{BINDING}}" => "",
        ];

        foreach ($replacements as $find => $replace) {
            $templateHtml = str_replace($find, $replace, $templateHtml);
        }

        return $templateHtml;
    }

    private function removeString($text, $string) {
        return str_replace($string, '', $text);
    }


    private function validate($deploy) {
        $this->isValid($deploy->offer, 'Offer');
        $this->isValid($deploy->send_date, 'Send date');
        $this->isValid($deploy->esp_account, 'ESP account');
        $this->isValid($deploy->creative, 'Creative');
        $this->isValid($deploy->from, 'From');
        $this->isValid($deploy->subject, 'Subject');
        $this->isValid($deploy->mailingTemplate, 'Mailing template');
        $this->validSendDate($deploy->offer->id, $deploy->send_date);
    }

    private function isValid($value, $field) {
        if ($value === null) {
            throw new ValidationException("$field is empty.");
        }
        return true;
    }

    private function validSendDate($offerId, $sendDate) {
        if (!$this->offerRepo->offerCanBeMailedOnDay($offerId, $sendDate)) {
            throw new ValidationException("Offer cannot be sent on $sendDate.");
        } 
        return true;
    }

}