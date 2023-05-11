<?php

namespace Blog\twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AssetExtension extends AbstractExtension
{
    private $request;

    public function __construct($request) {
        $this->request = $request;
    }
    public function getFunctions()
    {
        return [
            new TwigFunction('asset_url', [$this, 'getAssetUrl']),
            new TwigFunction('url', [$this, 'getUrl']),
            new TwigFunction('base_url', [$this, 'getBaseUrl'])
        ];
    }
    public function getAssetUrl($path) {
        return $this->getBaseUrl() . $path;
    }
    public function getBaseUrl() {
        $params = $this->request->getServerParams();
        return $params['REQUEST_SCHEME'] . '://' . $params['HTTP_HOST'] . '/';
    }
    public function getUrl($path) {
        return $this->getBaseUrl() . $path;
    }
}