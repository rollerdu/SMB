<?php
/**
 * Note: XML <--> array 转换类
 *
 * Author: Eddy
 * $id: xml.class.php
 */

namespace Extend;

class XML {
    private $parser;
    private $document;
    private $parent;
    private $stack;
    private $last_opened_tag;

    public function xml_serialize($data, $htmlon = 0, $level = 1) {
        $space = str_repeat("\r", $level);
        $cdatahead = $htmlon ? '<![CDATA[' : '';
        $cdatafoot = $htmlon ? ']]>' : '';
        $s = '';
        if(!empty($data)) {
            foreach($data as $key=>$val) {
                if(!is_array($val)) {
                    $val = "$cdatahead$val$cdatafoot";
                    if(is_numeric($key)) {
                        $s .=  "$space<item>$val</item>";
                    } elseif($key === '') {
                        $s .= '';
                    } else {
                        $s .= "$space<$key>$val</$key>";
                    }
                } else {
                    if(is_numeric($key)) {
                        $s .=  "$space<item>".$this->xml_serialize($val, $htmlon, $level+1)."$space</item>";
                    } elseif($key === '') {
                        $s .= '';
                    } else {
                        $s .= "$space<$key>".$this->xml_serialize($val, $htmlon, $level+1)."$space</$key>";
                    }
                }
            }
        }
        $s = preg_replace("/([\x01-\x09\x0b-\x0c\x0e-\x1f])+/", ' ', $s);
        return ($level == 1 ? "<?xml version=\"1.0\" encoding=\"UTF-8\"?>" : " ").$s.($level == 1 ? '' : '');
    }

    public function xml_unserialize($xml_string){
        return json_decode(json_encode((array) simplexml_load_string($xml_string)),true);
    }

}



?>