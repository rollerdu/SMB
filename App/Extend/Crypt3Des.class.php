<?php
namespace Extend;
class Crypt3Des{

	private $key = "";

	private $iv = "";

    /**
     * [0] => cast-128
     * [1] => gost
     * [2] => rijndael-128
     * [3] => twofish
     * [4] => arcfour
     * [5] => cast-256
     * [6] => loki97
     * [7] => rijndael-192
     * [8] => saferplus
     * [9] => wake
     * [10] => blowfish-compat
     * [11] => des
     * [12] => rijndael-256
     * [13] => serpent
     * [14] => xtea
     * [15] => blowfish
     * [16] => enigma
     * [17] => rc2
     * [18] => tripledes
     */
    const CIPHER = MCRYPT_RIJNDAEL_128;

    /**
     * 模式
     * CBC|CFB|CTR|ECB|NCFB|NOFB
     */
    const MODE = MCRYPT_MODE_CBC;

    private $mode;

    private $cipher;
    /**
	* 构造，传递二个已经进行base64_encode的KEY与IV
	*
	* @param string $key
	* @param string $iv
	*/


    public function __construct($key, $iv,$cipher = MCRYPT_RIJNDAEL_128, $mode = MCRYPT_MODE_CBC){
        if (empty($key)) {
            echo 'key and iv is not valid';
            exit();
        }
        $key_val = pack("H48",$key);
        $iv_val = pack("H16",$iv);


        $this->key = $key;
        $this->iv = $iv;
//        $this->key = $key_val;
//        $this->iv = $iv_val;

        $this->cipher = $cipher;
        $this->mode = $mode;

    }

	/**
	*加密
	* @param <type> $value
	* @return <type>
	*/

	public function encrypt ($value) {
		$td = mcrypt_module_open($this->cipher, '', $this->mode, '');
		$iv = $this->iv;
		$value = $this->PaddingPKCS7($value);
		$key = $this->key;
		mcrypt_generic_init($td, $key, $iv);
		$ret = base64_encode(mcrypt_generic($td, $value));
		mcrypt_generic_deinit($td);
		mcrypt_module_close($td);

		return $ret;
	}

	/**
	*解密
	* @param <type> $value
	* @return <type>
	*/

	public function decrypt ($value) {
		$td = mcrypt_module_open($this->cipher, '', $this->mode, '');
		$iv = $this->iv;
		$key = $this->key;
		dump($iv.'---'.$key);
		mcrypt_generic_init($td, $key, $iv);
		$ret = trim(mdecrypt_generic($td, base64_decode($value)));
		$ret = $this->UnPaddingPKCS7($ret);
		mcrypt_generic_deinit($td);
		mcrypt_module_close($td);

		return $ret;
	}

	private function PaddingPKCS7 ($data) {
		$block_size = mcrypt_get_block_size('tripledes', 'cbc');
		$padding_char = $block_size - (strlen($data) % $block_size);
		$data .= str_repeat(chr($padding_char), $padding_char);

		return $data;
	}

	private function UnPaddingPKCS7 ($text) {
		$pad = ord($text{strlen($text) - 1});
		if ($pad > strlen($text)) {
			return false;
		}
		if (strspn($text, chr($pad), strlen($text) - $pad) != $pad) {
			return false;
		}

		return substr($text, 0, - 1 * $pad);

	}

}


	//$key_val = "010203040506070809010203040506070809010203040506";
	//$key = "010203040506070809010203040506070809010203040506";
	//$iv = "0102030405060708";


	//$test = new Crypt3Des( $key,$iv);
	//$str = $test->encrypt("XAB123");
	//echo $str."\n";
	//$de_str = $test->decrypt($str)."\n";
	//echo $de_str."\n";

	//$str = $test->encrypt("中国china");
	//echo $str."\n";
?>
