<?php
namespace Disqus;

class DisqusResource
{
    private $apiInterfaces = array();

    public function __construct(
        $api,
        $interface = null,
        $node = null,
        $tree = array()
    ) {
        $this->apiInterfaces = $this->getApiInterfaces();
        var_dump($this->apiInterfaces);die();


        $this->api       = $api;
        $this->interface = $interface;
        $this->node      = $node;
        if ($node) {
            array_push($tree, $node);
        }
        $this->tree = $tree;
    }

    public function __get($attr)
    {
        $interface = $this->interface->$attr;
        if (!$interface) {
            throw new Exception\InterfaceNotDefined();
        }
        return new Resource($this->api, $interface, $attr, $this->tree);
    }

    public function __call($name, $args)
    {
        $resource = $this->interface->$name;
        if (!$resource) {
            throw new Exception\DisqusInterfaceNotDefined();
        }
        $kwargs = (array)$args[0];

        foreach ((array)$resource->required as $k) {
            if (empty($kwargs[$k])) {
                throw new \Exception('Missing required argument: '.$k);
            }
        }

        $api = $this->api;

        if (empty($kwargs['api_secret'])) {
            $kwargs['api_secret'] = $api->key;
        }

        // emulate a named pop
        $version = (!empty($kwargs['version']) ? $kwargs['version'] : $api->version);
        $format = (!empty($kwargs['format']) ? $kwargs['format'] : $api->format);
        unset($kwargs['version'], $kwargs['format']);

        $url = 'https://'.$this->getDisqusApiHost();
        $path = "/api/{$version}/".implode('/', $this->tree)."/{$name}.{$format}";

        if (!empty($kwargs)) {
            if ($resource->method == 'POST') {
                $post_data = $kwargs;
            } else {
                $post_data = false;
                $path .= '?'.Network::dsq_get_query_string($kwargs);
            }
        }


        $response = self::dsq_urlopen($url.$path, $post_data);

        $data = call_user_func($api->formats[$format], $response['data']);

        if ($response['code'] != 200) {
            throw new Exception\APIError($data->code, $data->response);
        }

        return $data->response;
    }

    /**
     * undocumented function
     *
     * @return void
     * @author
     **/
    private function getApiInterfaces()
    {
        $interfacesFile = file_get_contents(realpath(__DIR__ . '/../interfaces.json'));
        $apiInterfaces = json_decode($interfacesFile);
        return $apiInterfaces;
    }

    /**
     *
     *
     * @return void
     * @author
     **/
    public function getDisqusApiHost()
    {
        return 'disqus.com';
    }
}
