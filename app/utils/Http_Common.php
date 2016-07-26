<?php

class Http_Common
{

    private $_curl;

    public function __construct($solrAuthen = false)
    {

        $this->init($solrAuthen);
    }

    public function init($solrAuthen = false)
    {

        $this->_curl = new Curl();
        if ($solrAuthen == true) {
            $this->_curl->set_credentials("admin", "123457qwer");
        }
        $this->_curl->store_cookies(storage_path() . "cookie.txt");
        $this->_curl->set_user_agent("Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.63 Safari/537.36");
    }

    public function get($url)
    {
        return $this->_curl->fetch_url($url);
    }

    public function post($url, $data)
    {
        return $this->_curl->send_post_data($url, $data);
    }

}
