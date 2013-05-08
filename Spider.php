<?php
/**
 * User: Xin Du <duxins@gmail.com> 
 * Date: 5/8/13
 * Time: 10:10 PM
 */

class Spider{
    private $last_info = FALSE;
    protected $curl;
    protected $options = array(
        'useragent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_3) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.65 Safari/537.31',
        'referer' => 'http://www.google.com/',
        'connect_timeout' => 15,
        'follow_location' => TRUE,
    );

    public function __construct($opts = array()){
        $this->init($opts);
    }

    public function init($opts = array()){
        $this->options = array_merge($this->options, $opts);
        $this->curl = curl_init();
        return $this;
    }

    public function fetch($url){
        $this->curl = curl_init();
        $this->_prepare();
        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, $this->options['true']);

        $content = curl_exec($this->curl);

        if(($error = curl_error($this->curl))){
            throw new SpiderException($error." (url: $url)");
        }

        $info = curl_getinfo($this->curl);
        $this->last_info = $info;

        return $content;
    }

    public function last_info(){
        return $this->last_info;
    }

    protected function _prepare(){
        curl_setopt($this->curl, CURLOPT_REFERER, $this->options['referer']);
        curl_setopt($this->curl, CURLOPT_USERAGENT, $this->options['useragent']);
        curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, $this->options['connect_timeout']);
        curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, $this->options['follow_location']);
    }

}

class SpiderException extends Exception{}

