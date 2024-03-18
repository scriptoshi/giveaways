<?php

namespace App\Support;

use Hash;
use Str;

class Faker
{
    public static function lookUp($type)
    {
        $dbToFaker = [
            'string' => 'words:2,true',
            'char' => 'words:6,true',
            'varchar' => 'sentence:30',
            'text' => 'sentence:50',
            'mediumtext' => 'sentence:100',
            'mediumText' => 'sentence:100',
            'tinyText' => 'word',
            'longtext' => 'sentence:150',
            'json' => '$this->fakeJson()',
            'jsonb' => '$this->fakeJson()',
            'binary' => '$this->randBytes()',
            'password' => '$this->hash()',
            'email' => 'safeEmail|unique',
            'number' => 'randomNumber:5,false',
            'integer' => 'randomNumber:5,false',
            'bigint' => 'randomNumber:13,false',
            'bigInteger' => 'randomNumber:5,false',
            'mediumint' => 'randomNumber:8,false',
            'mediumInteger' => 'randomNumber:8,false',
            'tinyint' => 'randomNumber:2,false',
            'tinyInteger' => 'randomNumber:2,false',
            'smallint' => 'randomNumber:3,false',
            'smallInteger' => 'randomNumber:3,false',
            'decimal' => 'randomFloat',
            'double' => 'randomFloat',
            'float' => 'randomFloat',
            'date' => 'date',
            'datetime' => 'now()',
            'timestamp' => 'unixTime',
            'timeTz' => 'now()',
            'timestampTz' => 'now()',
            'timestampsTz' => 'now()',
            'time' => 'time',
            'radio' => 'boolean',
            'boolean' => 'boolean',
            'enum' => 'randomElement',
            'select' => 'randomElement',
            'file' => 'url',
            'uuid' => 'uuid',
            'dateTimeTz' => 'now()',
            'dateTime' => 'now()',
            'foreignUuid' => 'uuid',
            'ipAddress' => 'ipv4',
            'macAddress' => 'macAddress',
            'multiLineString' => 'sentence:100',
            'nullableTimestamps' => 'now()',
            'rememberToken' => '\Str::random(10)',
            'unsignedBigInteger' => 'randomNumber:10,false',
            'unsignedDecimal' => 'randomFloat',
            'unsignedInteger' => 'randomNumber:10,false',
            'unsignedMediumInteger' => 'randomNumber:5,false',
            'unsignedSmallInteger' => 'randomNumber:3,false',
            'unsignedTinyInteger' => 'randomNumber:2,false',
            'year' => "year|'+10 years'",
        ];
        return $dbToFaker[$type] ?? null;
    }
}
