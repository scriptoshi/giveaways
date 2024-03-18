<?php
namespace App\Actions\Web3;
use Elliptic\EC;
use kornrunner\Keccak;
class Signature {
	private function pubKeyToAddress($pubkey) {
		return "0x" . substr(Keccak::hash(substr(hex2bin($pubkey->encode("hex")), 1), 256), 24);
	}
	
	function verify($message, $signature, $address) {
		$msglen = strlen($message);
		$hash   = Keccak::hash("\x19Ethereum Signed Message:\n{$msglen}{$message}", 256);
		$sign   = ["r" => substr($signature, 2, 64), 
				   "s" => substr($signature, 66, 64)];
        if (str($signature)->endsWith('00')) {
            $signature = str($signature)->replaceLast('00', '1B')->value();
        }
        if (str($signature)->endsWith('01')) {
            $signature = str($signature)->replaceLast('01', '1C')->value();
        }
		$recid  = ord(hex2bin(substr($signature, 130, 2))) - 27; 
		if ($recid != ($recid & 1)) 
			return false;
		$ec = new EC('secp256k1');
		$pubkey = $ec->recoverPubKey($hash, $sign, $recid);
        $signer = $this->pubKeyToAddress($pubkey);
        if(is_array($address))
        return in_array(strtolower($signer), array_map('strtolower', $address));
        return strtolower($address) == strtolower($signer); 
	}

}