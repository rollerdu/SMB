<?php
namespace Extend;
class Aes
{
	private $bytes = [0xA, 1, 0xB, 5, 0xC, 4, 0xF, 7, 0xD, 9, 0x17, 3, 2, 0xE, 8, 12];

	private $_secrect_key;

	public function __construct($secKey)
	{
		$this->_secrect_key = $secKey;
	}

	public function getIv()
	{
		return implode(array_map("chr", $this->bytes));
	}

	public function encrypt($str)
	{
		$screct_key = $this->_secrect_key;
		$str = trim($str);
		$str = $this->pksc5pad($str);
		$iv = $this->getIv();
		$encrypt_str =  mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $screct_key, $str, MCRYPT_MODE_CBC, $iv);
		return base64_encode($encrypt_str);
	}

	public function decrypt($str)
	{
		$screct_key = $this->_secrect_key;
        $str = base64_decode($str);
        $iv = $this->getIv();
        $encrypt_str = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $screct_key, $str, MCRYPT_MODE_CBC, $iv);
        $encrypt_str = trim($encrypt_str);
        $encrypt_str = $this->pksc5unpad($encrypt_str);
        return $encrypt_str;
	}

	private function pksc5pad($source)
	{

		$blocksize = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
		$pad = $blocksize - (strlen($source) % $blocksize);
		return $source .= str_repeat(chr($pad), $pad); 
	}

	private function pksc5unpad($string)
	{
		$size = strlen($string);	
		$padding = ord($string[$size - 1]);
		$string = substr($string, 0, -$padding);
		return $string;
	}
}

