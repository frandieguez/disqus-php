<?php
namespace Disqus;

class DisqusAPI extends DisqusResource
{
    public static $version = '0.0.1';

    public $formats = array(
        'json' => 'dsq_json_decode'
    );

    public function __construct(
        $key = null,
        $format = 'json',
        $version = '3.0'
    ) {

        $this->key = $key;
        $this->format = $format;
        $this->version = $version;

        parent::__construct($this);
    }

    public function __invoke()
    {
        throw new \Exception('You cannot call the API without a resource.');
    }

    public function setKey($key)
    {
        $this->key = $key;
    }

    public function setFormat($format)
    {
        $this->format = $format;
    }

    public function setVersion($version)
    {
        $this->version = $version;
    }
}
