<?php

declare(strict_types=1);

require_once "train_part.php";

class Carriage extends TrainPart
{
    public const TYPE_PERSONEN = 'Personen';
    public const TYPE_SCHLAFEN = 'Schlafen';
    public const TYPE_SPEISE = 'Speise';
    public const TYPE_GUETER = 'Güter';


    public const TYPES = [
        self::TYPE_PERSONEN,
        self::TYPE_SCHLAFEN,
        self::TYPE_SPEISE,
        self::TYPE_GUETER
    ];

    function __construct(...$params)
    {
        parent::__construct(...$params);

        // Checking type and throw an error if type is unknown
        $this->check_type($this->type, self::TYPES);
    }

    static public function get_sorted_by_type(array $carriages, $reverse = False)
    {
        // Firstly place personen carriages, then speise-restaurant carriages,
        // then schlafen carriages and then gueter carriages
        $types_order = [
            self::TYPE_PERSONEN => 1,
            self::TYPE_SPEISE => 2,
            self::TYPE_SCHLAFEN => 3,
            self::TYPE_GUETER => 4,
        ];

        usort($carriages, function (self $a, self $b) use ($reverse, $types_order) {
            return $reverse
                ? $types_order[$b->type] - $types_order[$a->type]
                : $types_order[$a->type] - $types_order[$b->type];
        });

        return $carriages;
    }
}
