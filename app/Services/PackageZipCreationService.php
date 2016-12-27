<?php

namespace App\Services;
use App\Repositories\DeployRepo;
use App\Exceptions\ValidationException;
use App\Exceptions\UrlValidationException;
use App\Services\CakeEncryptedLinkService;
use App\Services\UrlFormatService;
use App\Services\MT1Services\LinkService;
use App\Repositories\CakeRedirectDomainRepo;
use App\Repositories\OfferRepo;
use App\Repositories\OfferTrackingLinkRepo;
use App\Repositories\EspApiAccountRepo;
use League\Flysystem\Filesystem;
use League\Flysystem\ZipArchive\ZipArchiveAdapter;
use Storage;
use DOMDocument;
use DOMXPath;
use App\Services\Url;
use ZipArchive;
use Mail;

class PackageZipCreationService {

    private $deployRepo;
    private $encryptionService;
    private $deploy;

    const STORAGE_PATH_BASE = './files';

    private $serveGentLinks = [];
    private $nameLinkId = 0; // link id used in file name

    private $stripImages = false;

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

    public function createPackage($id) {
        $this->packageSetup($id);
        
        $zipName = "{$this->deploy->id}_{$this->espAccountName}_{$this->deploy->offer_id}_{$this->deploy->send_date}.zip";
        $filePath = storage_path() . '/app/files/' . $this->deploy->id . '/';
        $fullZipPath = $filePath . $zipName;
        $this->zipDir($filePath, $fullZipPath);

        return $fullZipPath;
    }

    /**
     *  Similar to the above, but uploading it to the ftp
     */

    public function uploadPackage($id) {
        $this->packageSetup($id);
        
        $zipName = "{$this->deploy->id}_{$this->espAccountName}_{$this->deploy->offer_id}_{$this->deploy->send_date}.zip";
        $filePath = storage_path() . '/app/files/' . $this->deploy->id . '/';
        $fullZipPath = $filePath . $zipName;
        $this->zipDir($filePath, $fullZipPath);

        // Upload this to ftp
        $file = fopen($fullZipPath, 'r');
        Storage::disk('dataExportFTP')->put("packages/$zipName", $file);
        fclose($file);

        return $zipName;
    }

    private function packageSetup($id) {
        // Setting value for image formatting for packages
        $this->stripImages = true;

        $html = $this->createHtml($id);
        $html =  html_entity_decode($html);

        // Email Ops regarding any errors
        $this->sendRedirectWarningEmail();

        // Make the temporary directory
        $saveDirectory = self::STORAGE_PATH_BASE . '/' . $this->deploy->id . '/';
        Storage::createDir($saveDirectory);
        
        // Create files and zip
        $offerName = $this->deploy->offer->name;
        $creativeName = $this->deploy->creative->file_name;

        // store the HTML 
        $fileName = "{$this->deploy->send_date}_{$offerName}_{$creativeName}_{$this->deploy->creative_id}_{$this->nameLinkId}";
        Storage::disk("local")->put($saveDirectory . "$fileName.html", $html);

        // create the asset.txt file
        $assetText = $this->createAssetText($this->deploy);
        Storage::disk("local")->put($saveDirectory . "asset.txt", $assetText);

        // Store just the text and links in a file
        $textFile = $this->returnNonHtmlDocument($html);
        Storage::disk("local")->put($saveDirectory . "$fileName.txt", $textFile);
    }

    private function folderToZip($folder, &$zipFile, $exclusiveLength) { 
        $handle = opendir($folder); 
        while (false !== $f = readdir($handle)) { 
            if ($f != '.' && $f != '..') { 
                $filePath = "$folder/$f"; 
                // Remove prefix from file path before add to zip. 
                $localPath = substr($filePath, $exclusiveLength); 
                if (is_file($filePath)) { 
                    $zipFile->addFile($filePath, $localPath); 
                } 
                elseif (is_dir($filePath)) { 
                    // Add sub-directory. 
                    $zipFile->addEmptyDir($localPath); 
                    $this->folderToZip($filePath, $zipFile, $exclusiveLength); 
                } 
            } 
        } 
        closedir($handle); 
    }

    private function zipDir($sourcePath, $outZipPath) { 
        $pathInfo = pathInfo($sourcePath); 
        $parentPath = $pathInfo['dirname']; 
        $dirName = $pathInfo['basename']; 

        $z = new ZipArchive(); 
        $z->open($outZipPath, ZIPARCHIVE::CREATE); 
        $z->addEmptyDir($dirName); 
        $this->folderToZip($sourcePath, $z, strlen("$parentPath/")); 
        $z->close(); 
    } 

    /**
     *  Public method that runs through the creation of the html itself
     *  Note that this HTML is _escaped_. To get the proper html, run html_entity_decode()
     */

    public function createHtml($id) {
        try {

            $deploy = $this->deployRepo->getDeploy($id);
            $this->deploy = $deploy;

            // Validate package
            $this->validate($deploy);

            // Prepare some required values
            $this->deployId = $deploy->id;
            $affiliateId = $deploy->cake_affiliate_id;

            $offer = $deploy->offer;
            $offerTypeId = $offer->offer_payout_type_id;
            $this->contentDomain = $deploy->contentDomain->domain_name;

            $this->espAccountName = $deploy->espAccount->account_name;

            $espId = $deploy->espAccount->id;

            $fieldOptions = $deploy->espAccount->esp->fieldOptions;

            $this->emailIdField = $fieldOptions->email_id_field;
            $emailAddressField = $fieldOptions->email_address_field;

            $templateId = $deploy->template_id;
            $fromId = $deploy->from_id;
            $subjectId = $deploy->subject_id;
            $creativeId = $deploy->creative_id;
            
            // Assign espCakeDomain based off of offer type and affiliate id
            // will be used to remove cake domain in offer tracking url and elsewhere
            $defaultCakeDomain = $this->cakeRedirectRepo->getDefaultRedirectDomain();
            $this->espCakeDomain = $this->cakeRedirectRepo->getRedirectDomain($affiliateId, $offerTypeId);

            // Process redir1.cgi and ccID links
            $creative = $deploy->creative;
            $creativeHtml = $creative->creative_html;

            // hopefully we can remove this in the near future due to redoing how links are handled
            if ( preg_match('/redir1\.cgi/', $creativeHtml) && preg_match('/\&ccID\=/', $creativeHtml) ) {

                $creativeHtml = preg_replace('/\&sub\=/', '&XXX=', $creativeHtml);
                $creativeHtml = preg_replace('/\&amp;/', '&', $creativeHtml);

                // parse "extra" links that use the ccID parameter
                $creativeHtml = $this->parseCCIDLinks($creativeHtml);
            }

            // Format offer unsub link
            $offerRealUnsubLink = $offer->unsub_link;

            if ('' !== $offerRealUnsubLink) {
                $offerUnsubLinkId = $this->linkService->getLinkId($offerRealUnsubLink);
                $this->validateLink($offerRealUnsubLink);
                $offerUnsubLink = $this->formatUrl('ADVUNSUB', $deploy->url_format, $offerUnsubLinkId, $this->emailIdField);
            }
            else {
                $offerUnsubLinkId = 0;
            }
            
            // Replacing tokens in the full html
            $fullHtml = $deploy->mailingTemplate->template_html;

            // remove doctype, html, & body from creative
            $dom = new DOMDocument();
            $errors = libxml_use_internal_errors(true); // suppressing errors on non-escaped ampersands
            $dom->loadHTML($creativeHtml);
            $dom->doctype->parentNode->removeChild($dom->doctype);
            $this->stripEnclosingElement($dom, "html");
            $this->stripEnclosingElement($dom, "body");
            $creativeHtml = urldecode($dom->saveHTML());
            libxml_use_internal_errors($errors);

            // n used to be clientId - removed, should be safe
            $openPixel = "<IMG SRC='http://{$this->contentDomain}/cgi-bin/open.cgi?eid={$this->emailIdField}&cid=1&em=$emailAddressField&n=0&f=$fromId&s=$subjectId&c=$creativeId&did=&binding=&tid=$templateId&openflag=1&nod=1&espID=$espId&subaff={$deploy->id}' border=0 height=1 width=1>";

            $fullHtml = str_replace("{{CREATIVE}}", $creativeHtml, $fullHtml);
            $fullHtml = str_replace("{{TRACKING}}", $openPixel, $fullHtml);

            $fullHtml = str_replace("{{TIMESTAMP}}", strftime('%Y%m%d%H%M%S'), $fullHtml);

            // Need to get random strings for image domains $img_prefix
            $random1 = $this->urlFormatter->getDefinedRandomString();
            $random2 = $this->urlFormatter->getDefinedRandomString();
            $imgPrefix = "{$this->contentDomain}/$random1/$random2";

            $unsubText = $this->createUnsubHtml($offer, $imgPrefix, $offerUnsubLinkId);
            $fullHtml = str_replace("{{ADV_UNSUB}}", $unsubText, $fullHtml);

            foreach($offer->trackingLinks()->get() as $link) {

                $linkNumber = $link->link_num;
                $url = $link->url;

                if (1 === $linkNumber) {
                    $token = "{{URL}}";
                    $this->nameLinkId = $link->id;
                }
                else {
                    $token = "{{URL" . $linkNumber . "}}";
                }

                if (strpos($fullHtml, $token) !== false) {
                    $url = $this->offerTrackingLinkRepo->getOfferTrackingLink($offer->id, $linkNumber);

                    $url = str_replace("{{CID}}", $this->espAccountName, $url);
                    $url = str_replace("{{FOOTER}}", "{{FOOTER}}_{$deploy->send_date}", $url);
                    $url = preg_replace('/a=\d+/', "a=$affiliateId", $url); // old affiliate id
                    $url = str_replace("up.gravitypresence.com", $this->espCakeDomain, $url); // maybe {{CAKE_DOMAIN}}?
                    $url = str_replace($defaultCakeDomain, $this->espCakeDomain, $url);
                    $url = str_replace('a=13&', "a=$affiliateId&", $url);
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

            $fullHtml = str_replace("{{ADV_UNSUB_URL}}", $offerUnsubLink, $fullHtml); // keeping this naming scheme for legacy reasons
            $fullHtml = str_replace("{{OFFER_UNSUB_URL}}", $offerUnsubLink, $fullHtml); // this should be the new one going forward
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

            // Unfortunately, BH's email id token includes an entity (%cf) that gets converted
            // We've placed another html entity (a zero-width joiner) between the % and cf
            // to prevent this from happening during the call to parseImageLinks()
            // additionally, when this gets decoded or displayed, the &zwj; - true to its name - disappears
            $fullHtml = str_replace('%%&zwj;cf', '%%cf', $fullHtml);
            return $fullHtml;
        }
        catch (UrlValidationException $e) {
            $deploy = $this->deployRepo->getDeploy($id);
            $templateId = $deploy->template_id;
            $creativeId = $deploy->creative_id;

            return "{$e->getMessage()} is not a valid URL. Please check template {$templateId} and creative {$creativeId}";
        }
        catch (\Exception $e) {
            return $e->getMessage();
        }
    }


    /**
     *  Format the url based off of the new/gmail settings
     */

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

    private function sendRedirectWarningEmail() {
        if (sizeof($this->serveGentLinks) > 0) {
            Mail::raw(implode(', ', $this->serveGentLinks), function ($message) {
                $message->to('dpezas@zetainteractive.com');
                $message->to('jhecht@zetainteractive.com');
                $message->subject('"QA HTTP Redirect Alert"');
                $message->priority(1);
            });
        }
    }


    private function createAssetText($deploy) {
        // do we still need CLIENT or FOOTER?
        $text = <<<TXT
FROM: {$deploy->from->from_line}
SUBJECT: {$deploy->subject->subject_line}
TEMPLATE: {$deploy->mailingTemplate()->first()->template_name}
ESP ACCOUNT: {$deploy->espAccount->account_name}
FOOTER: 
TXT;

        return $text;
    }

    /**
     *  Takes and HTML file and returns a text-only string with the following format:
     *  Place any text, any links, and any image title text
     *  All unique links in the document are placed and numbered in-order beneath the text
     */

    private function returnNonHtmlDocument($html) {
        $dom = new DOMDocument();
        $internalErrors = libxml_use_internal_errors(true); // suppress minor HTML validation errors
        $dom->loadHTML($html);

        $xpath = new DOMXPath($dom);
        $allNodes = $xpath->query('//node()');

        $allText = [];
        $links = [];

        $prevText = '';

        foreach ($allNodes as $node) { 
            $type = get_class($node);

            if ('DOMElement' === $type && $node->hasAttribute('href')) {
                $link = $node->getAttribute('href');

                // Determine proper text for DOMElement node.
                // If the textContent property is not empty, use that
                // Otherwise, check if this node has any children
                // and take all titles from them
                // N.B. This procedure strongly assumes that the child nodes
                // are image tags. Any changes to creatives will result in a change here.

                if ($node->textContent) {
                    $prevText = $node->textContent;
                    $text = $node->textContent . ' [' . $link . ']';
                }
                elseif ($node->hasChildNodes()) {
                    $text = '';

                    foreach ($node->childNodes as $child) {
                        $text .= $child->getAttribute('title');
                    }
                    $prevText = $text;

                    $text .= ' [' . $link . ']';
                }
                else {
                    $text = '';
                    $prevText = '';
                }
                   
            }
            elseif ('DOMElement' === $type && $node->hasAttribute('src') 
                && $node->textContent !== $prevText 
                && $node->getAttribute('title') !== $prevText) {
                // For images, suppress the link, get the image title
                $link = '';
                $text = $node->textContent ?: $node->getAttribute('title');
                $prevText = $text;
            }
            elseif ('DOMText' === $type && $node->textContent !== $prevText) {
                $text = $node->textContent;
                $link = '';
                $prevText = $text;
            } 
            else {
                continue;
            }

            if ($text !== '') {
                $allText []= $text;
            }

            if ($link !== '') {
                $links []= $link;
            }
            
        }

        libxml_use_internal_errors($internalErrors);

        $text = implode(PHP_EOL, $allText) . PHP_EOL . PHP_EOL;

        $links = array_keys(array_flip($links));
        $length = sizeof($links);

        for ($i = 0; $i < $length; $i++) {
            $text .= ($i + 1) . '. ' . $links[$i] . PHP_EOL; // links not zero-indexed in file
        }

        return $text;
    }


    /*
        Method used to remove a specified node and replace it with its children
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
                        $unsubText = "<img src=\"http://$imageUrlPrefix/$offer->unsub_img\" border=0><br><br>";
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
     *  Format them based off of per-esp rules
     */

    private function parseImageLinks($html) {
        $dom = new DOMDocument();
        $internalErrors = libxml_use_internal_errors(true); // suppress minor HTML validation errors
        $dom->loadHTML($html);

        $dom = $this->parseImageLinksLoop($dom, 'img');
        $dom = $this->parseImageLinksLoop($dom, 'input');
        libxml_use_internal_errors($internalErrors);
        return urldecode($dom->saveHTML());
    }

    
    private function parseImageLinksLoop(&$dom, $element) {
        foreach($dom->getElementsByTagName($element) as $link) {
            $parsedUrl = new Url($link->getAttribute('src'));

            if ($link->getAttribute('src') !== '' && !$parsedUrl->contains('open.cgi')) {

                if ($this->stripImages) {

                    $parsedUrl->stringReplace('{{DOMAIN}}', 'contentstaging-01.mtroute.com');
                    $parsedUrl->stringReplace('{{IMG_DOMAIN}}', 'contentstaging-01.mtroute.com');

                    $fileName = sizeof(explode('.', $parsedUrl->fileName)) > 1 ? $parsedUrl->fileName : $parsedUrl->fileName . '.jpg';

                    $location = $this->getSaveDirectory() . 'images/';
                    $fileName = $this->saveFileGetName($parsedUrl, $location, $fileName);

                    $imageLinkFormat = $this->espAccountRepo->getImageLinkFormat($this->deploy->espAccount->id);
                    $urlFormat = $imageLinkFormat->url_format;

                    $newUrl = str_replace('{{CONTENT_DOMAIN}}', $this->contentDomain, $urlFormat);

                    if (1 === (int)$imageLinkFormat->remove_file_extension) {
                        $fileName = str_replace('.jpg', '', $fileName);
                    }

                    $newUrl = str_replace('{{FILE_NAME}}', $fileName, $newUrl);

                    $link->setAttribute('src', $newUrl);
                }
            }

        }

        return $dom;
    }

    /**
     *  We need to pull the files and save them in an /images directory
     *  and also get the MIME type for the image so we can correct it in the creative.
     */
    private function saveFileGetName(Url $parsedUrl, $saveLocation, $fileName) {

        $mimeType = $parsedUrl->getMimeType();

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
        Storage::disk("local")->put($path, $parsedUrl->getContents());

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
        $urls = [];

        $dom = new DOMDocument();
        $internalErrors = libxml_use_internal_errors(true);
        $dom->loadHTML($html);

        $urls = $this->parseCCIDLinksLoop($dom, 'a');
        $urls = array_merge($urls, $this->parseCCIDLinksLoop($dom, 'area'));
        libxml_use_internal_errors($internalErrors);
        
        $dom = null;

        foreach($urls as $map) {
            $old = $map['from'];
            $new = $map['to'];

            $html = str_replace($old, urldecode($new), $html);
        }

        return $html;
    }

    /**
     *  Find and create mapping of ccID links 
     *  Format of ['from' => $oldUrl, 'to' => $newUrl]
     *  This is required (rather than directly updating the dom) because
     *  we need to keep links the same for the non-html.
     *  We also maintain the uniqueness of the froms here
     */

    private function parseCCIDLinksLoop($dom, $element) {
        $urls = [];
        $froms = [];

        foreach($dom->getElementsByTagName($element) as $link) {
            $parsedUrl = new Url($link->getAttribute('href'));

            if (!$parsedUrl->contains('{{URL}}') 
                && !$parsedUrl->contains('{{ADV_UNSUB_URL}}') 
                && $parsedUrl->contains('ccID') 
                && !in_array($parsedUrl->url, $froms)) {

                $pair = ['from' => $parsedUrl->url];

                $parsedUrl->regexReplace('/\?/', '\?');
                $parsedUrl->regexReplace('/\[/', '\[');

                $ccId = $parsedUrl->getQueryParam('ccID');

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

                if ($this->deploy->encrypt_cake) {
                    $trackingLink = $this->encryptionService->encryptCakeLink($trackingLink);
                }

                if ($this->deploy->fully_encrypt) {
                    $trackingLink = $this->encryptionService->fullEncryptLink($trackingLink);
                }

                $trackingLinkId = $this->linkService->getLinkId($trackingLink);

                $this->validateLink($trackingLink);

                $publicLink = $this->formatUrl('REDIRECT', $this->deploy->url_format, $trackingLinkId, $this->emailIdField);

                $pair['to'] = $publicLink;

                $urls []= $pair;
            }
        }

        return $urls;

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
            "footerid={{FOOTER}}" => "footerid=0", // in urls, set to 0
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
        elseif (!$deploy->contentDomain || !$deploy->contentDomain->contentDomainValidForEspAccount($deploy->esp_account_id)) {
            throw new ValidationException('Content domain not permitted. Check status, type, and esp account.');
        }
        elseif ('' === $deploy->contentDomain->domain_name) {
            throw new ValidationException('Content domain url is empty.');
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