<?php

declare(strict_types=1);

require_once "train_part.php";

class Locomotive extends TrainPart
{
    public const TYPE_DIESEL = 'Diesel';
    public const TYPE_DAMPF = 'Dampf';
    public const TYPE_ELEKTRISCH = 'Elektrisch';

    public  const TYPES = [
        self::TYPE_DIESEL,
        self::TYPE_DAMPF,
        self::TYPE_ELEKTRISCH
    ];

    public int $tractive_force;

    function __construct(int $tractive_force, ...$params)
    {
        parent::__construct(...$params);

        $this->tractive_force = $tractive_force;

        // Checking type and throw an error if type is unknown
        $this->check_type($this->type, self::TYPES);
    }

    static public function get_sorted_by_traction_force(array $locomotives, $reverse = False)
    {
        usort($locomotives, function (self $a, self $b) use ($reverse) {
            return $reverse
                ? $b->tractive_force - $a->tractive_force
                : $a->tractive_force - $b->tractive_force;
        });

        return $locomotives;
    }
}
