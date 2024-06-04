<?php

namespace App\Web3;

use Illuminate\Support\Fluent;
use Illuminate\Support\Facades\Validator;
use SleepFinance\Eip712;
use kornrunner\Secp256k1;

class Prize extends Fluent
{
    public function __construct(array $attributes)
    {
        $attributes['version'] = (string) $attributes['version'] ?? '1';
        $validator = Validator::make($attributes, [
            'to' =>  ['string', function ($attribute, $value, $fail) {
                if (AddressValidator::isValid($value) == AddressValidator::ADDRESS_INVALID) {
                    $fail('The ' . $attribute . ' is invalid.');
                }
            }],
            'amount' => 'required|numeric',
            'uuid' => 'required|numeric',
            'chainId' => 'required|numeric',
            'contract' => ['string', function ($attribute, $value, $fail) {
                if (AddressValidator::isValid($value) == AddressValidator::ADDRESS_INVALID) {
                    $fail('The ' . $attribute . ' is invalid.');
                }
            }],
            'name' => 'required|string',
            'version' => 'required|string',
            'pvk' => 'required|string'
        ]);
        if ($validator->fails()) throw new \Exception('Invalid Attributes');
        parent::__construct($attributes);
    }


    public function getSignature()
    {
        $data = $this->eip712Data();
        $eip712 = new Eip712($data);
        $hashToSign = $eip712->hashTypedDataV4();
        $secp256k1 = new Secp256k1();
        $signed   = $secp256k1->sign($hashToSign, $this->pvk);
        $r   = $this->hexup(gmp_strval($signed->getR(), 16));
        $s   = $this->hexup(gmp_strval($signed->getS(), 16));
        $v   = dechex((int) $signed->getRecoveryParam() + 27);
        return "0x$r$s$v";
    }

    function hexup(string $value): string
    {
        return strlen($value) % 2 === 0 ? $value : "0{$value}";
    }

    function eip712Data()
    {
        return [
            "types" => [
                "EIP712Domain" => [
                    [
                        "name" => "name",
                        "type" => "string"
                    ],
                    [
                        "name" => "version",
                        "type" => "string"
                    ],
                    [
                        "name" => "chainId",
                        "type" => "uint256"
                    ],
                    [
                        "name" => "verifyingContract",
                        "type" => "address"
                    ]
                ],
                "PRIZE" => [
                    [
                        "name" => "to",
                        "type" => "address"
                    ],
                    [
                        "name" => "amount",
                        "type" => "uint256"
                    ],
                    [
                        "name" => "uuid",
                        "type" => "uint256"
                    ]
                ]
            ],
            "primaryType" => "PRIZE",
            "domain" => [
                "name" => $this->name,
                "version" => $this->version,
                "chainId" => $this->chainId,
                "verifyingContract" => $this->contract,
            ],
            "message" => [
                'to' => $this->to,
                'amount' => $this->amount,
                'uuid' => $this->uuid
            ]
        ];
    }
}
