<?php

namespace Behat\LogExtension\Listener;

use Behat\Behat\Event\FeatureEvent;
use Behat\Mink\Mink;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/*
 * This file is part of the Behat\LogExtension.
 * (c) Gordon Franke <info@nevalon.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Access log formatter.
 *
 * @author Gordon Franke <info@nevalon.de>
 */
class LogListener implements EventSubscriberInterface
{
    private $mink;
    private $parameters;

    /**
     * Current log filename.
     *
     * @var string
     */
    protected $filename;

    /**
     * Initializes initializer.
     *
     * @param Mink  $mink
     * @param array $parameters
     */
    public function __construct(Mink $mink, array $parameters)
    {
        $this->mink       = $mink;
        $this->parameters = $parameters;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        $events = array('beforeFeature', 'afterFeature');

        return array_combine($events, $events);
    }

    /**
     * Truncate the access log file
     */
    protected function truncateAccessLog()
    {
        $fh = fopen($this->parameters['access_log'], 'w' );
        fclose($fh);
    }

    /**
     * {@inheritdoc}
     */
    public function beforeFeature(FeatureEvent $event)
    {
        $this->truncateAccessLog();

        $feature = $event->getFeature();
        $this->filename = 'TEST-' . basename($feature->getFile(), '.feature') . '.log';
    }

    /**
     * {@inheritdoc}
     */
    public function afterFeature(FeatureEvent $event)
    {
        $outputPath = rtrim($this->parameters['output_path'], DIRECTORY_SEPARATOR);

        if (is_file($outputPath)) {
            throw new Exception(sprintf(
                'Directory path expected as "output_path" parameter of %s, but got: %s',
                get_class($this),
                $outputPath
            ));
        }

        if (!is_dir($outputPath)) {
            mkdir($outputPath, 0777, true);
        }

        $filename = $outputPath . DIRECTORY_SEPARATOR . $this->filename;
        file_put_contents($filename, file_get_contents($this->parameters['access_log']));
    }
}