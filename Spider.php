<?php
/**
 * User: Xin Du <duxins@gmail.com> 
 * Date: 5/8/13
 * Time: 10:10 PM
 */

class Spider{
    private $last_info = FALSE;
    protected $curl;
    protected $default_options = array(
        'useragent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_3) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.65 Safari/537.31',
        'referer' => 'http://www.google.com/',
        'connect_timeout' => 15,
        'follow_location' => TRUE,
    );
    protected $options = array();

    public function __construct($opts = array()){
        $this->init($opts);
    }

    public function init($opts = array()){
        $this->options = array_merge($this->default_options, $opts);
        return $this;
    }

    public function fetch($url){
        $this->curl = curl_init();
        $this->_prepare();
        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, TRUE);

        $content = curl_exec($this->curl);

        if(($error = curl_error($this->curl))){
            throw new SpiderException($error." (url: $url)");
        }

        $info = curl_getinfo($this->curl);
        $this->last_info = $info;

        return $content;
    }

    public function download($source, $destination){
        $this->curl = curl_init();
        $this->_prepare();
        $this->_mkdir($destination);

        curl_setopt($this->curl, CURLOPT_URL, $source);
        $fp = fopen($destination, 'wb+');
        curl_setopt($this->curl, CURLOPT_FILE, $fp);
        curl_exec($this->curl);
        fclose($fp);

        if(($error = curl_error($this->curl))){
            @unlink($destination);
            throw new SpiderException($error." (url: $source)");
        }

        $info = curl_getinfo($this->curl);
        $this->last_info = $info;
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

    protected function _mkdir($destination){
        $dirname = dirname($destination);
        if(!is_dir($dirname)){
            if(!@mkdir($dirname, 0777, TRUE)){
                $error = error_get_last();
                throw new SpiderException($error['message']." (dest: $destination)");
            }
        }
        return TRUE;
    }
}

class SpiderException extends Exception{}
