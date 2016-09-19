<?php

namespace App\Services;
use App\Repositories\DeployRepo;
use App\Exceptions\ValidationException;
use App\Services\CakeEncryptedLinkService;
use App\Services\UrlFormatService;
use App\Services\LinkService;
use App\Repositories\CakeRedirectDomainRepo;
use App\Repositories\OfferRepo;
use App\Repositories\OfferTrackingLinkRepo;
use App\Repositories\EspApiAccountRepo;
use League\Flysystem\Filesystem;
use League\Flysystem\ZipArchive\ZipArchiveAdapter;
use Storage;
use DOMDocument;
use App\Services\Url;

class PackageZipCreationService {

    private $deployRepo;
    private $encryptionService;
    private $deploy;

    const STORAGE_PATH_BASE = './files';

    private $serveGentLinks = [];

    private $saveDirectory;

    public function __construct(
            DeployRepo $deployRepo, 
            CakeEncryptedLinkService $encryptionService, 
            UrlFormatService $urlFormatter, 
            LinkService $linkService,
            CakeRedirectDomainRepo $cakeRedirectRepo,
            OfferRepo $offerRepo,
            OfferTrackingLinkRepo $offerTrackingLinkRepo,
            EspApiAccountRepo $espAccountRepo) {

        $this->deployRepo = $deployRepo;
        $this->encryptionService = $encryptionService;
        $this->urlFormatter = $urlFormatter;
        $this->linkService = $linkService;
        $this->cakeRedirectRepo = $cakeRedirectRepo;
        $this->offerRepo = $offerRepo;
        $this->offerTrackingLinkRepo = $offerTrackingLinkRepo;
        $this->espAccountRepo = $espAccountRepo;

    }

    // useful name for now
    public function createPackage($id) {

        $deploy = $this->deployRepo->getDeploy($id);
        $this->deploy = $deploy;

        // 1. validate package
        $this->validate($deploy);

        // Prepare some required values
        $this->deployId = $deploy->id;

        $this->saveDirectory = self::STORAGE_PATH_BASE . '/' . $deploy->id . '/';

        $affiliateId = $deploy->cake_affiliate_id;

        $offer = $deploy->offer;
        $offerTypeId = $offer->offer_payout_type_id;
        #$this->contentDomain = $deploy->contentDomain->main_site;
        $this->contentDomain = 'tmrsupdatesjbs3.com';

        $this->espAccountName = $deploy->espAccount->account_name;

        $espId = $deploy->espAccount->id;

        $fieldOptions = $deploy->espAccount->esp->fieldOptions;

        $this->emailIdField = $fieldOptions->email_id_field;
        $emailAddressField = $fieldOptions->email_address_field;

        $templateId = $deploy->template_id; // "tid"
        $fromId = $deploy->from_id; // "fid"
        $subjectId = $deploy->subject_id; // "sid"
        $creativeId = $deploy->creative_id; // "crid"
        

        // 2. assign espCakeDomain based off of offer type and affiliate id
        // will be used to remove cake domain in offer tracking url and elsewhere
        $defaultCakeDomain = $this->cakeRedirectRepo->getDefaultRedirectDomain();
        $this->espCakeDomain = $this->cakeRedirectRepo->getRedirectDomain($affiliateId, $offerTypeId);

        // 3. process redir1.cgi and ccID links
        $creative = $deploy->creative;
        $creativeHtml = $creative->creative_html;

        // hopefully we can remove this in the near future due to redoing how links are handled
        if ( preg_match('/redir1\.cgi/', $creativeHtml) && preg_match('/\&ccID\=/', $creativeHtml) ) {

            $creativeHtml = preg_replace('/\&sub\=/', '&XXX=', $creativeHtml);
            $creativeHtml = preg_replace('/\&amp;/', '&', $creativeHtml);

            // parse "extra" links that use the ccID parameter
            $creativeHtml = $this->parseCCIDLinks($creativeHtml);
        }

        // 4. Format offer unsub link (merged with 10)
        $offerRealUnsubLink = $offer->unsub_link;

        if ('' !== $offerRealUnsubLink) {
            $offerUnsubLinkId = $this->linkService->getLinkId($offerRealUnsubLink);
            $this->validateLink($offerRealUnsubLink);
            $offerUnsubLink = $this->formatUrl('ADVUNSUB', $deploy->url_format, $offerUnsubLinkId, $this->emailIdField);
        }
        else {
            $offerUnsubLinkId = 0;
        }
        
        /* 5-9 MERGED WITH 11 */ /* 10 MERGED WITH 4 */

        // 11. replacing tokens in the full html

        $fullHtml = $deploy->mailingTemplate()->first()->template_html;

        // remove doctype, html, & body from creative
        $dom = new DOMDocument();
        $dom->loadHTML($creativeHtml);
        $dom->doctype->parentNode->removeChild($dom->doctype);
        $this->stripEnclosingElement($dom, "html");
        $this->stripEnclosingElement($dom, "body");
        $creativeHtml = urldecode($dom->saveHTML());

        // n used to be clientId - removed, should be safe
        $tracking = "<IMG SRC='http://{$this->contentDomain}/cgi-bin/open.cgi?eid={$this->emailIdField}&cid=1&em=$emailAddressField&n=0&f=$fromId&s=$subjectId&c=$creativeId&did=&binding=&tid=$templateId&openflag=1&nod=1&espID=$espId&subaff={$deploy->id}' border=0 height=1 width=1>";

        $fullHtml = str_replace("{{CREATIVE}}", $creativeHtml, $fullHtml);
        $fullHtml = str_replace("{{TRACKING}}", $tracking, $fullHtml);
        $fullHtml = str_replace("{{TIMESTAMP}}", strftime('%Y%m%d%H%M%S'), $fullHtml);

        // Need to get random strings for image domains $img_prefix
        $random1 = $this->urlFormatter->getDefinedRandomString();
        $random2 = $this->urlFormatter->getDefinedRandomString();
        $imgPrefix = "{$this->contentDomain}/$random1/$random2"; // this is used in a little bit

        $unsubText = $this->createUnsubHtml($offer, $imgPrefix, $offerUnsubLinkId);
        $fullHtml = str_replace("{{ADV_UNSUB}}", $unsubText, $fullHtml);

        foreach($offer->trackingLinks()->get() as $link) {

            $linkNumber = $link->link_num;
            $url = $link->url;

            $token = $linkNumber === 1 ? "{{URL}}" : "{{URL$linkNumber}}";

            if (strpos($fullHtml, $token) !== false) {
                $url = $this->offerTrackingLinkRepo->getOfferTrackingLink($offer->id, $linkNumber);

                $url = str_replace("{{CID}}", $this->espAccountName, $url);
                $url = str_replace("{{FOOTER}}", "{{FOOTER}}_{$deploy->send_date}", $url);
                $url = preg_replace('/a=\d+/', "a=$affiliateId", $url); // old affiliate id
                $url = str_replace("up.gravitypresence.com", $this->espCakeDomain, $url); // maybe {{CAKE_DOMAIN}}?
                $url = str_replace($defaultCakeDomain, $this->espCakeDomain, $url);
                $url = str_replace('a=13', "a=$affiliateId", $url);
                $url = str_replace("{{DEPLOY_ID}}", $this->deployId, $url); // used at least for 1 ...

                if ($deploy->encrypt_cake) {
                    $url = $this->encryptionService->encryptCakeLink($url);
                }
                if ($deploy->fully_encrypt) {
                    $url = $offerTrackingUrl = $this->encryptionService->fullEncryptLink($url);
                }

                $this->validateLink($url);

                $linkId = $this->linkService->getLinkId($url);

                $redirectLink = $this->formatUrl('REDIRECT', $deploy->url_format, $linkId, $this->emailIdField) 
                                ?: "http://{$this->contentDomain}/cgi-bin/redir1.cgi?eid={$this->emailIdField}&cid=1&em=$emailAddressField&id=$linkId&n=0&f=$fromId&s=$subjectId&c=$creativeId&tid=$templateId&footerid=0&ctype=R";

                $fullHtml = str_replace($token, $redirectLink, $fullHtml);
            }    
        }

        #$fullHtml = str_replace("{{NID}}", replace, $fullHtml); // this is the esp "client" id ... trash?
        $fullHtml = str_replace("{{ADV_UNSUB_URL}}", $offerUnsubLink, $fullHtml); // keeping this for legacy reasons
        $fullHtml = str_replace("{{OFFER_UNSUB_URL}}", $offerUnsubLink, $fullHtml);
        $fullHtml = str_replace("{{CRID}}", $creativeId, $fullHtml);
        $fullHtml = str_replace("{{F}}", $fromId, $fullHtml);
        $fullHtml = str_replace("{{S}}", $subjectId, $fullHtml);
        $fullHtml = str_replace("{{TID}}", $templateId, $fullHtml);
        $fullHtml = str_replace("{{EMAIL_ADDR}}", $emailAddressField, $fullHtml);
        $fullHtml = str_replace("{{EMAIL_USER_ID}}", $this->emailIdField, $fullHtml);

        $fullHtml = $this->presetChanges($fullHtml);

        $fullHtml = str_replace("{{IMG_DOMAIN}}", $this->contentDomain, $fullHtml);
        $fullHtml = str_replace("{{DOMAIN}}", $this->contentDomain, $fullHtml);

        $fullHtml = $this->parseImageLinks($fullHtml);

        $fullHtml =  html_entity_decode($fullHtml);

        echo $fullHtml . PHP_EOL;

        // Test the above functionality first before building out the ZIP
        /*
        // 12. Create files and zip

        $offerName = $deploy->offer->name;
        $creativeName = $deploy->creative->file_name;

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

        */
    }


    // can't be this simple - we need quite a bit more, as seen from the signature
    // we also need to switch emailId and emailAddress for emailIdToken and emailAddressToken -

    private function formatUrl($type, $urlFormat, $linkId, $emailIdFormat) {
        if ('new' === $urlFormat) {
            return $this->urlFormatter->formatNewUrl($type, $this->contentDomain, $emailIdFormat, $linkId);
        }
        elseif ('gmail' === $urlFormat) {
            return $this->urlFormatter->formatGmailUrl($type, $this->contentDomain, $emailIdFormat, $linkId);
        }
        else {
            return '';
        }
    }

    private function validateLink($url) {
        $result = $this->linkService->checkLink($url);

        if (false === $result) {
            // populate data for Dimitri
            $this->serveGentLinks []= $url;
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
            $fragment->appendChild($element->childNodes->item(0));
        }

        $element->parentNode->replaceChild($fragment, $element);
    }


    private function createUnsubHtml($offer, $imageUrlPrefix, $offerUnsubLinkId) {
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

        $internalErrors = libxml_use_internal_errors(true); // suppress minor HTML validation errors
        $dom->loadHTML($html);

        #$urls = [];

        $dom = $this->parseImageLinksLoop($dom, 'img');
        $dom = $this->parseImageLinksLoop($dom, 'input');
        libxml_use_internal_errors($internalErrors);
        return urldecode($dom->saveHTML());

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
            $url = $link->getAttribute('src');

            if (strpos($url, 'open.cgi') === false) {

                echo "Running inside parse image links loop with $url" . PHP_EOL;

                $url = preg_replace('/\{\{DOMAIN\}\}/', 'contentstaging-01.mtroute.com', $url);
                $url = preg_replace('/\{\{IMG_DOMAIN\}\}/', 'contentstaging-01.mtroute.com', $url);
                $urlContents = parse_url($url);

                $scheme = $urlContents['scheme'];
                $host = $urlContents['host'];
                $path = isset($urlContents['path']) ? $urlContents['path'] : '';

                // path is the part between the domain and the query string:
                // e.g. http://www.my-site.com/dir1/dir2/test.php?s1=test
                // the path is "/dir1/dir2/test.php"
                $pathArray = explode('/', $path);
                $pathArrayLength = sizeof($pathArray);
                $fileName = $pathArray[$pathArrayLength - 1];

                $testForExtension = explode('.', $fileName);

                $fileName = sizeof($testForExtension) > 1 ? $fileName : $fileName . '.jpg';

                echo "file name is $fileName" . PHP_EOL;

                $location = $this->getSaveDirectory() . 'images/';

                echo "Save directory is: $location" . PHP_EOL;

                $fileName = $this->saveFileGetName($url, $location, $fileName);

                $imageLinkFormat = $this->espAccountRepo->getImageLinkFormat($this->deploy->espAccount->id);
                $urlFormat = $imageLinkFormat->url_format;

                $newUrl = str_replace('{{CONTENT_DOMAIN}}', $this->contentDomain, $urlFormat);

                if (1 === (int)$imageLinkFormat->remove_file_extension) {
                    $fileName = str_replace('.jpg', '', $fileName);
                }

                $newUrl = str_replace('{{FILE_NAME}}', $fileName, $newUrl);

                echo "New URL is : $newUrl" . PHP_EOL;
                
            }

            $link->setAttribute('src', $newUrl);
        }

        return $dom;
    }

    /**
     *  We need to pull the files and save them in an /images directory
     *  and also get the MIME type for the image so we can correct it in the creative.
     */
    private function saveFileGetName($url, $saveLocation, $fileName) {

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
        
        // save this file to the designated location for the package
        $path = $saveLocation . '/' . $fileName;
        Storage::disk("local")->put($path, $result);

        return $fileName;
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
        $internalErrors = libxml_use_internal_errors(true);
        $dom->loadHTML($html);

        $dom = $this->parseCCIDLinksLoop($dom, 'a');
        $dom = $this->parseCCIDLinksLoop($dom, 'area');
        libxml_use_internal_errors($internalErrors);
        return urldecode($dom->saveHTML());
 
    }

    private function parseCCIDLinksLoop($dom, $element) {
        foreach($dom->getElementsByTagName($element) as $link) {
            /**
            // Non-optimal. Place a factory?
            */

            echo "about to parse this url: " . $link->getAttribute('href') . PHP_EOL;
            $parsedUrl = new Url($link->getAttribute('href'));

            if (!$parsedUrl->find('{{URL}}') && !$parsedUrl->find('{{ADV_UNSUB_URL}}') && $parsedUrl->find('ccID')) {

                $parsedUrl->regexReplace('/\?/', '\?');
                $parsedUrl->regexReplace('/\[/', '\[');

                $ccId = $parsedUrl->getQueryParam('ccID');

                echo "ccid: $ccId" . PHP_EOL;
                $trackingLink = "http://{$this->espCakeDomain}/?a={$this->deploy->cake_affiliate_id}&c=$ccId&s1="
                                . $this->deployId 
                                ."&s2={{EMAIL_USER_ID}}_".$this->deploy->creative_id
                                ."_".$this->deploy->from_id
                                ."_".$this->deploy->subject_id
                                ."_".$this->deploy->mailing_template_id
                                ."&s4={$this->espAccountName}&s5=0_0_0_0_"
                                .$this->deploy->send_date;

                if ('CPC' === $this->deploy->offer->payoutType->name) {
                    $trackingLink .= '&p=c';
                }

                if ($this->deploy->encrypt_cake) { // probably need to pass in deploy
                    $trackingLink = $this->encryptionService->encryptCakeLink($trackingLink);
                }

                if ($this->deploy->full_encrypt) {
                    $trackingLink = $this->encryptionService->fullEncryptLink($trackingLink);
                }

                $trackingLinkId = $this->linkService->getLinkId($trackingLink);

                $this->validateLink($trackingLink);

                $publicLink = $this->formatUrl('REDIRECT', $this->deploy->url_format, $trackingLinkId, $this->emailIdField);

                $link->setAttribute('href', $publicLink);
            }
        }

        return $dom;
    }


    /**
     *  Run replacements for tokens with static values
     */
    private function presetChanges($templateHtml) {

        $replacements = [
            "{{CONTENT_HEADER}}" => "",
            "{{CONTENT_HEADER_TEXT}}" => "",
            "{{REFID}}" => "",
            "{{CLICK}}" => "",
            "{{HEADER_TEXT}}" => "", 
            "{{FOOTER_TEXT}}" => "",
            "{{FOOTER_STR}}" => "", 
            "footerid={{FOOTER}}" => "0", // in urls, set to 0
            "{{FOOTER}}" => "", // if the above is not found, use ""
            "{{CID}}" => "1",
            "{{MID}}" => "",
            "{{CWPROGID}}" => "",
            "{{HEADER}}" => "",
            "{{BINDING}}" => "",
            "{{FID}}" => "",
            "{{BINDING}}" => "",
            "{{NID}}" => ""
        ];

        foreach ($replacements as $find => $replace) {
            $templateHtml = str_replace($find, $replace, $templateHtml);
        }

        return $templateHtml;
    }


    private function validate($deploy) {
        if (!$deploy->creative || $deploy->creative->returnApprovalAndStatus() !== 'allowed') {
            // we lose information this way, though
            throw new ValidationException('Creative is not permitted. Check approval and status.');
        }
        elseif (!$deploy->from || $deploy->from->returnApprovalAndStatus() !== 'allowed') {
            throw new ValidationException('From line is not permitted. Check approval and status.');
        }
        elseif (!$deploy->subject || $deploy->subject->returnApprovalAndStatus() !== 'allowed') {
            throw new ValidationException('Subject line is not permitted. Check approval and status.');
        }
        elseif (!$deploy->offer || $deploy->offer->returnApprovalAndStatus() !== 'allowed') {
            throw new ValidationException('Offer is not permitted. Check approval and status.');
        }
        elseif (!$deploy->espAccount) {
            throw new ValidationException('ESP Account does not exist.');
        }
        elseif (!$deploy->mailingTemplate ) {
            throw new ValidationException('Mailing template does not exist.');
        }
        elseif (!$this->offerRepo->offerCanBeMailedOnDay($deploy->offer->id, $deploy->send_date)) {
            throw new ValidationException("Offer cannot be sent on {$deploy->send_date}.");
        }
    }

}