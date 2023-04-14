<?php

namespace Slimfony\ORM;

class DBTypeMapper
{
    /**
     * @return array<string, array{from: \Closure($value): mixed, to: \Closure($value): mixed}: mixed>
     */
    public static function types(): array
    {
        return [
            'date' => [
                'from' => static function (string $value) {
                    return new \DateTime($value);
                },
                'to' => static function (\DateTime $dt) {
                    return $dt->format('Y-m-d');
                },
            ],
            'timestamp' => [
                'from' => static function (string $value) {
                    return new \DateTime($value);
                },
                'to' => static function (\DateTime $dt) {
                    return $dt->format('Y-m-d H:i:s');
                },
            ],
        ];
    }
}
