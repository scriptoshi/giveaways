<?php

namespace App\Enums;

enum TokenType: string
{
    case STANDARD = 'standard';
    case TAXTOKEN = 'taxtoken';
    case CROSSCHAIN = 'crosschain';

    function getFactory()
    {
        return match ($this) {
            TokenType::TAXTOKEN =>  FactoryType::TAXFACTORY,
            TokenType::STANDARD =>  FactoryType::STANDARDTOKENFACTORY,
            TokenType::CROSSCHAIN =>  FactoryType::LZFACTORY,
        };
    }
}
