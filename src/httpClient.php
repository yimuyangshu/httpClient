<?php

namespace Yimu\PlugLib;

class httpClient
{
    private $curl = null;

    private $cookie = null;

    private $reip = null;

    private $url = null;

    private $referer = null;

    private $header = null;

    private $param = null;

    private $body = null;

    private $gzip = false;

    private $nobody = false;

    private $useragent = null;

    private $proxy = null;

    private $proxypwd = null;

    private $sslpem = null;

    private $timeout = null;

    public function __construct($url)
    {
        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_HEADER, 0);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($this->curl, CURLOPT_TIMEOUT, 60);
        $this->url = $url;
    }

    private function init()
    {
        $header = [];
        if ($this->reip) {
            $header[] = 'X-FORWARDED-FOR: ' . $this->reip;
            $header[] = 'CLIENT-IP: ' . $this->reip;
        }
        $this->proxy && curl_setopt($this->curl, CURLOPT_PROXY, $this->proxy);
        $this->proxypwd && curl_setopt($this->curl, CURLOPT_PROXYUSERPWD, $this->proxypwd);
        !$this->referer && $this->referer = $this->url;
        $header[] = 'Referer: ' . $this->referer;
        curl_setopt($this->curl, CURLOPT_REFERER, $this->referer);
        !$this->useragent && $this->useragent = 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.132 Safari/537.36';
        $header[] = 'User-Agent: ' . $this->useragent;
        if ($this->cookie) {
            $header[] = 'Cookie: ' . $this->cookie;
            curl_setopt($this->curl, CURLOPT_COOKIE, $this->cookie);
        }
        $this->timeout && curl_setopt($this->curl, CURLOPT_TIMEOUT, $this->timeout);
        if ($this->gzip) {
            $header[] = 'Accept-Encoding:gzip, deflate, sdch';
            curl_setopt($this->curl, CURLOPT_ENCODING, 'gzip,deflate,sdch');
        }
        if ($this->header) {
            foreach ($this->header as $key => $val)
                $header[] = $key . ': ' . $val;
        }
        $this->nobody && curl_setopt($this->curl, CURLOPT_NOBODY, 1);
        if ($this->sslpem) {
            $sslpem = explode(',', $this->sslpem);
            curl_setopt($this->curl, CURLOPT_SSLCERTTYPE, 'PEM');
            curl_setopt($this->curl, CURLOPT_SSLCERT, $sslpem[0]);
            curl_setopt($this->curl, CURLOPT_SSLKEYTYPE, 'PEM');
            curl_setopt($this->curl, CURLOPT_SSLKEY, $sslpem[1]);
        }
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $header);
    }

    public function set_timeout($time)
    {
        $this->timeout = $time;
        return $this;
    }

    public function set_gzip($bol)
    {
        $this->gzip = $bol;
        return $this;
    }

    public function set_nobody($bol)
    {
        $this->nobody = $bol;
        return $this;
    }

    public function set_proxypwd($str)
    {
        $this->proxypwd = $str;
        return $this;
    }

    public function set_proxy($str)
    {
        $this->proxy = $str;
        return $this;
    }

    public function set_header($ary)
    {
        $this->header = $ary;
        return $this;
    }

    public function set_useragent($str)
    {
        $this->useragent = $str;
        return $this;
    }

    public function set_param($ary)
    {
        $this->param = $ary;
        return $this;
    }

    public function set_body($str)
    {
        $this->body = $str;
        return $this;
    }

    public function set_cookie($str)
    {
        $this->cookie = $str;
        return $this;
    }

    public function set_reip($str)
    {
        $this->reip = $str;
        return $this;
    }

    public function set_referer($str)
    {
        $this->referer = $str;
        return $this;
    }

    public function set_sslpem($str)
    {
        $this->sslpem = $str;
        return $this;
    }

    public function getlocation()
    {
        $this->init();
        $this->param && curl_setopt($this->curl, CURLOPT_URL, $this->url . '?' . http_build_query($this->param));
        curl_exec($this->curl);
        $headers = curl_getinfo($this->curl);
        curl_close($this->curl);
        return $headers['url'];
    }

    public function postlocation()
    {
        $this->init();
        curl_setopt($this->curl, CURLOPT_POST, 1);
        $this->param && curl_setopt($this->curl, CURLOPT_POSTFIELDS, http_build_query($this->param));
        $this->body && curl_setopt($this->curl, CURLOPT_POSTFIELDS, $this->body);
        curl_exec($this->curl);
        $headers = curl_getinfo($this->curl);
        curl_close($this->curl);
        return $headers['url'];
    }

    public function getheader()
    {
        $this->init();
        $this->param && curl_setopt($this->curl, CURLOPT_URL, $this->url . '?' . http_build_query($this->param));
        curl_setopt($this->curl, CURLOPT_HEADER, 1);
        $content = curl_exec($this->curl);
        $headers = curl_getinfo($this->curl);
        curl_close($this->curl);
        return 'Url:' . $headers['url'] . ";\r\n" . $content;
    }

    public function postheader()
    {
        $this->init();
        curl_setopt($this->curl, CURLOPT_POST, 1);
        curl_setopt($this->curl, CURLOPT_HEADER, 1);
        $this->param && curl_setopt($this->curl, CURLOPT_POSTFIELDS, http_build_query($this->param));
        $this->body && curl_setopt($this->curl, CURLOPT_POSTFIELDS, $this->body);
        $content = curl_exec($this->curl);
        $headers = curl_getinfo($this->curl);
        curl_close($this->curl);
        return 'Url:' . $headers['url'] . ";\r\n" . $content;
    }

    public function get()
    {
        $this->init();
        $this->param && curl_setopt($this->curl, CURLOPT_URL, $this->url . '?' . http_build_query($this->param));
        $content = curl_exec($this->curl);
        curl_close($this->curl);
        return $content;
    }

    public function post()
    {
        $this->init();
        curl_setopt($this->curl, CURLOPT_POST, 1);
        $this->param && curl_setopt($this->curl, CURLOPT_POSTFIELDS, http_build_query($this->param));
        $this->body && curl_setopt($this->curl, CURLOPT_POSTFIELDS, $this->body);
        $content = curl_exec($this->curl);
        curl_close($this->curl);
        return $content;
    }
}