<?php

declare(strict_types=1);

require_once "train_part.php";
require_once "carriage.php";
require_once "locomotive.php";
require_once "exceptions.php";

class Train
{
    const PASSENGERS_FOR_ONE_CONDUCTOR = 50;
    const AVERAGE_HUMAN_WEIGHT = 0.075; // tons

    public $train_parts = array();

    public int $number_of_passengers = 0;
    public int $load_capacity = 0;

    public $locomotives = array();
    public $carriages = array();

    function __construct(
        int $number_of_passengers,
        int $load_capacity,
        array|Locomotive $locomotives,
        array|Carriage $carriages = [],
    ) {
        $this->number_of_passengers = $number_of_passengers;
        $this->load_capacity = $load_capacity;

        // Checking if $locomotives isn't an array transform it to array
        if (!is_array($locomotives)) {
            $locomotives = array($locomotives);
        }

        if (!is_array($carriages)) {
            $carriages = array($carriages);
        }

        $this->check_busy_parts(array_merge($locomotives, $carriages));

        $this->locomotives = $locomotives;
        $this->carriages = $carriages;
    }

    function __destruct()
    {
        // Reset connection for all train parts before destruct the train
        foreach ($this->train_parts as $part) {
            $part->connected_to_train = false;
        }
    }

    /**
     * This function is used to form the composition of the train.
     * You need to use it after the initialization of the train and every time after 
     * adding/removing locomotives and wagons
     */
    public function form_train()
    {
        /**
         * The idea is that the locomotives are at the head and tail of the train, 
         * and the cars are located between them. 
         * 
         * The cars are arranged in the following order (from head to tail): 
         * passenger cars, dining car, sleeping cars, freight cars.
         * 
         * I don't know if the locomotive attached to the tail of the train can move the train,
         * but I assumed that yes, and therefore, when calculating the total traction force, 
         * I also take into account the locomotives standing at the tail of the train.
         */
        $locomotives = Locomotive::get_sorted_by_traction_force($this->locomotives, reverse: true);
        $carriages = Carriage::get_sorted_by_type($this->carriages);

        $pivot = (int)ceil(count($locomotives) / 2);
        $result = [...$locomotives];

        array_splice($result, $pivot, 0, $carriages);

        $this->train_parts = $result;
    }

    private function check_busy_parts(array|Carriage|Locomotive $items)
    {
        $busy_train_parts = [];
        foreach ($items as $item) {
            if (!$item->connected_to_train) {
                $item->connected_to_train = true;
            } else {
                array_push($busy_train_parts, $item->serial_number);
            }
        }

        if (!empty($busy_train_parts)) {
            throw new UnableConnectPartToTheTrain(
                "Unable to connect parts: " . implode(", ", $busy_train_parts) . " to the train"
            );
        }
    }

    private function add_train_parts(array $arr, array|Carriage|Locomotive $items)
    {
        if (!is_array($items)) {
            $items = [$items];
        }

        $this->check_busy_parts($items);

        array_push($arr, ...$items);

        return $arr;
    }

    private function remove_train_parts(array $arr, array|Carriage|Locomotive $items)
    {
        if (!is_array($items)) {
            $items = [$items];
        }

        foreach ($items as $item) {
            $idx = array_search($item, $arr);

            if ($idx === false) {
                continue;
            }

            $item->connected_to_train = false;

            array_splice($arr, $idx, 1);
        }

        return $arr;
    }

    public function add_locomotives(array|Locomotive $items)
    {
        $this->locomotives = $this->add_train_parts($this->locomotives, $items);
    }

    public function remove_locomotives(array|Locomotive $items)
    {
        if (count($this->locomotives) <= 1) {
            throw new NoLocomotivesLeftException("You cannot delete locomotive when it's only one left");
        }

        $this->locomotives = $this->remove_train_parts($this->locomotives, $items);
    }

    public function add_carriages(array|Carriage $items)
    {
        $this->carriages = $this->add_train_parts($this->carriages, $items);
    }

    public function remove_carriages(array|Carriage $items)
    {
        $this->carriages = $this->remove_train_parts($this->carriages, $items);
    }

    private function get_train_sum_for(string $property)
    {
        // 0 is default value here
        return array_reduce($this->train_parts, function ($carry, $item) use ($property) {
            if (!property_exists($item::class, $property)) {
                return $carry;
            }

            return $carry + $item->$property;
        }, 0);
    }

    public function get_empty_weight()
    {
        return $this->get_train_sum_for('empty_weight');
    }

    public function get_max_passengers_capacity()
    {
        return $this->get_train_sum_for('max_passengers_capacity');
    }

    public function get_max_load_capacity()
    {
        return $this->get_train_sum_for('max_load_capacity');
    }

    public function get_max_payload()
    {
        // = maximale Anzahl der Passagiere im Zug x 75kg + 
        // maximales Zuladungsgewicht für Güter
        $passengers_weight = $this->number_of_passengers * self::AVERAGE_HUMAN_WEIGHT; // tons
        $max_load_capacity = $this->get_max_load_capacity();

        return $passengers_weight + $max_load_capacity;
    }

    public function get_max_total_weight()
    {
        $empty_weight = $this->get_empty_weight();
        $max_payload = $this->get_max_payload();

        return $empty_weight + $max_payload;
    }

    public function get_full_length()
    {
        return $this->get_train_sum_for('length');
    }

    public function is_drivable()
    {
        $total_weight = $this->get_max_total_weight();
        $total_tractive_force = $this->get_train_sum_for('tractive_force');

        return $total_tractive_force >= $total_weight;
    }

    public function get_number_of_conductors()
    {
        if ($this->number_of_passengers > 0 && $this->number_of_passengers <= self::PASSENGERS_FOR_ONE_CONDUCTOR) {
            return 1;
        }

        return intdiv($this->number_of_passengers, self::PASSENGERS_FOR_ONE_CONDUCTOR) + 1;
    }
}
