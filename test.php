<?php
function loadHtmlFromUrl(string $url): DOMXPath|string
{
    sleep(0.1);
    try {
        $htmlContent = file_get_contents($url);
    } catch (ErrorException) {
        return '';
    }

    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML($htmlContent);
    return new DOMXPath($dom);
}
$xpath = loadHtmlFromUrl("https://klopotenko.com/perevireni-chasom-reczept-hrumkyh-konservovanyh-ogirkiv-na-zymu/");

$node = $xpath->query("/div[@class='item-description']/p[position() <= 4]")->item(0);
echo $node->textContent ?? '';
