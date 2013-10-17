<?php
/*
 * This file is part of the Behat\LogExtension.
 * (c) Gordon Franke <info@nevalon.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

 if (!isset($argv[1])) {
    die('You must provide a log path argument.' . PHP_EOL);
 }
 $logPath = $argv[1];
 if (!is_dir($logPath)) {
    die(sprintf('Log path %s argument is not a directory.', $logPath) . PHP_EOL);
 }
 
 if (!isset($argv[2])) {
    die('You must provide a output file path argument.' . PHP_EOL);
 }
 $outputPath = $argv[2];
 if (file_exists($outputPath)) {
     if (!is_writeable($outputPath)) {
        die(sprintf('Output file path %s argument is not writeable.', $outputPath) . PHP_EOL);
     }
 } else {
     touch($outputPath);
 }
 
if (!isset($argv[3])) {
    $template = __DIR__ . '/access_log.jmx';
} else {
    $template = $argv[3];
    if (!is_readable($template)) {
        die(sprintf('Template file %s is not readable.', $template) . PHP_EOL);
    }
}

$doc = new DOMDocument();
$doc->load($template);

$xpath = new domXPath($doc);

$query = '//TestPlan/following-sibling::hashTree/*';
$xpathQuery = $xpath->query($query);

$threadGroup = $xpathQuery->item(0);
$hashTree = $xpathQuery->item(1);

$parent = $threadGroup->parentNode;

$dir = new DirectoryIterator($logPath);
foreach ($dir as $fileinfo) {
    if (!$fileinfo->getExtension()) {
        continue;
    }

    $threadGroupClone = $threadGroup->cloneNode(true);
    $hashTreeClone = $hashTree->cloneNode(true);

    // modify clone
    $threadGroupClone->setAttribute('testname', basename($fileinfo->getBasename('.log')));

    $query = 'AccessLogSampler/stringProp[attribute::name="logFile"]';
    $xpathQuery = $xpath->query($query, $hashTreeClone);

    $xpathQuery->item(0)->nodeValue = realpath($fileinfo->getPathname());

    $parent->appendChild($threadGroupClone);
    $parent->appendChild($hashTreeClone);

    echo $fileinfo->getFilename() . "\n";
}

$parent->removeChild($threadGroup);
$parent->removeChild($hashTree);

file_put_contents($outputPath, $doc->saveXML());
