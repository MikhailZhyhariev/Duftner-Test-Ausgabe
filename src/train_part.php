<?php

declare(strict_types=1);

require_once "exceptions.php";

abstract class TrainPart
{
    private const SERIAL_NUMBER_LENGTH = 6;

    // in tons
    public readonly int $empty_weight;

    // in meters
    public readonly float $length;

    public readonly string $type;

    public readonly int $max_passengers_capacity;

    // in tons
    public readonly int $max_load_capacity;

    public readonly string $manufacturer;

    public readonly int $release_year;

    public readonly string $serial_number;

    public bool $connected_to_train = false;

    function __construct(
        int $empty_weight,
        float $length,
        string $type,
        int $max_passengers_capacity,
        int $max_load_capacity,
        string $manufacturer,
        int $release_year
    ) {
        $this->empty_weight = $empty_weight;
        $this->length = $length;

        $this->type = $type;

        $this->max_passengers_capacity = $max_passengers_capacity;
        $this->max_load_capacity = $max_load_capacity;

        $this->manufacturer = $manufacturer;
        $this->release_year = $release_year;

        $this->serial_number = $this->make_serial_number();
    }

    public function make_serial_number()
    {
        $hash = md5(uniqid((string)rand(), true));
        $unique_serial_number = substr(str_shuffle($hash), 0, self::SERIAL_NUMBER_LENGTH);

        $format = '%s_%s-%d_%s';

        $manufacturer = $this->replace_german_special_letters($this->manufacturer);
        $manufacturer = str_replace(" ", "-", strtoupper($manufacturer));

        return sprintf(
            $format,
            strtoupper($this->replace_german_special_letters($this->type)),
            $manufacturer,
            $this->release_year,
            strtoupper($unique_serial_number)
        );
    }

    private function replace_german_special_letters($string)
    {
        $string = strtolower($string);
        $string = str_replace("ä", "ae", $string);
        $string = str_replace("ö", "oe", $string);
        $string = str_replace("ü", "ue", $string);

        return $string;
    }

    function check_type(string $type, array $types)
    {
        if (!in_array($type, $types)) {
            throw new UnknownTypeException("Unknown type: " . $type);
        }
    }
}
