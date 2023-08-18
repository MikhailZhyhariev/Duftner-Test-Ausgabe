<?php

declare(strict_types=1);

require_once "src/train.php";

use PHPUnit\Framework\TestCase;

final class TrainTest extends TestCase
{
    private function get_data()
    {
        $l1 = new Locomotive(
            tractive_force: 100, // tons
            empty_weight: 10, // tons
            length: 15, // meter
            type: Locomotive::TYPE_ELEKTRISCH,
            max_passengers_capacity: 2,
            max_load_capacity: 15,
            manufacturer: 'Test Loco El',
            release_year: 1997
        );
        $l2 = new Locomotive(
            tractive_force: 200, //tons
            empty_weight: 20, // tons
            length: 25, // meter
            type: Locomotive::TYPE_DIESEL,
            max_passengers_capacity: 3,
            max_load_capacity: 25,
            manufacturer: 'Test Loco Di',
            release_year: 1998
        );

        $c1 = new Carriage(
            empty_weight: 10, // tons
            length: 15, // meter
            type: Carriage::TYPE_PERSONEN,
            max_passengers_capacity: 20,
            max_load_capacity: 10,
            manufacturer: 'Test Carriage P',
            release_year: 1998
        );
        $c2 = new Carriage(
            empty_weight: 20, // tons
            length: 20, // meter
            type: Carriage::TYPE_SCHLAFEN,
            max_passengers_capacity: 50,
            max_load_capacity: 15,
            manufacturer: 'Test Carriage Sc',
            release_year: 1998
        );
        $c3 = new Carriage(
            empty_weight: 20, // tons
            length: 20, // meter
            type: Carriage::TYPE_SCHLAFEN,
            max_passengers_capacity: 50,
            max_load_capacity: 15,
            manufacturer: 'Test Carriage Sc',
            release_year: 1998
        );
        $c4 = new Carriage(
            empty_weight: 10, // tons
            length: 15, // meter
            type: Carriage::TYPE_SPEISE,
            max_passengers_capacity: 0,
            max_load_capacity: 0,
            manufacturer: 'Test Carriage Sp',
            release_year: 1998
        );
        $c5 = new Carriage(
            empty_weight: 5, // tons
            length: 15, // meter
            type: Carriage::TYPE_GUETER,
            max_passengers_capacity: 0,
            max_load_capacity: 100,
            manufacturer: 'Test Carriage Sp',
            release_year: 2003
        );

        return [
            'l1' => $l1,
            'l2' => $l2,

            'c1' => $c1,
            'c2' => $c2,
            'c3' => $c3,
            'c4' => $c4,
            'c5' => $c5,

            'locomotives' => [$l1, $l2],
            'carriages' => [$c1, $c2, $c3, $c4, $c5]
        ];
    }

    public function testTrainPartsInARightOrder(): void
    {
        $data = $this->get_data();

        $train = new Train(50, 20, $data['locomotives'], $data['carriages']);
        $train->form_train();

        $this->assertEquals(
            $train->train_parts,
            // Locomotive, Personen carriage, Speise carriage, Two Schlafen carriages, Locomotive
            [$data['l2'], $data['c1'], $data['c4'], $data['c2'], $data['c3'], $data['c5'], $data['l1']]
        );
    }

    public function testLocomotivesInARightOrder(): void
    {
        $data = $this->get_data();

        $train = new Train(50, 20, $data['locomotives'], $data['c1']);
        $l3 = new Locomotive(
            tractive_force: 50, //tons
            empty_weight: 50, // tons
            length: 30, // meter
            type: Locomotive::TYPE_DAMPF,
            max_passengers_capacity: 3,
            max_load_capacity: 25,
            manufacturer: 'Test Loco Dam',
            release_year: 1998
        );
        $train->add_locomotives($l3);
        $train->form_train();

        $this->assertEquals(
            $train->train_parts,
            // Locomotive-200, Locomotive-100, Personen carriage, Locomotive-50
            [$data['l2'], $data['l1'], $data['c1'], $l3]
        );
    }

    public function testAddTrainPart(): void
    {
        $data = $this->get_data();
        $train = new Train(50, 20, $data['l1']);
        $train->form_train();

        $train->add_carriages($data['c1']);
        $train->form_train();

        $this->assertEquals(
            $train->train_parts,
            [$data['l1'], $data['c1']]
        );
    }

    public function testRemoveTrainPart(): void
    {
        $data = $this->get_data();
        $train = new Train(50, 20, $data['l1'], $data['c1']);
        $train->form_train();

        $train->remove_carriages($data['c1']);
        $train->form_train();

        $this->assertEquals(
            $train->train_parts,
            [$data['l1']]
        );
    }

    public function testNotPossibleRemoveLastLocomotive(): void
    {
        $this->expectException(NoLocomotivesLeftException::class);

        $data = $this->get_data();
        $train = new Train(50, 20, $data['l1']);
        $train->form_train();

        $train->remove_locomotives($data['l1']);
    }

    public function testNotPossibleToConnectPartsToATrainTwice(): void
    {
        $this->expectException(UnableConnectPartToTheTrain::class);

        $data = $this->get_data();
        $train_1 = new Train(50, 20, $data['locomotives'], $data['carriages']);
        $train_1->form_train();

        $train_2 = new Train(50, 20, $data['locomotives'], $data['carriages']);
        $train_2->form_train();
    }

    public function testNotPossibleToConnectPartsToATrainTwiceUsingAddFunction(): void
    {
        $this->expectException(UnableConnectPartToTheTrain::class);

        $data = $this->get_data();
        $train_1 = new Train(50, 20, $data['l1'], $data['carriages']);
        $train_1->form_train();

        $train_2 = new Train(50, 20, $data['l2']);
        $train_2->add_carriages($data['c1']);
        $train_2->form_train();
    }

    public function testTrainSummaryEmptyWeight(): void
    {
        $data = $this->get_data();
        $train = new Train(50, 20, $data['locomotives'], $data['carriages']);
        $train->form_train();

        // 10 + 20 + 10 + 20 + 20 + 10 + 5 = 95
        $result = $train->get_empty_weight();

        $this->assertEquals($result, 95);
    }

    public function testTrainMaxPassengersCapacity(): void
    {
        $data = $this->get_data();
        $train = new Train(50, 20, $data['locomotives'], $data['carriages']);
        $train->form_train();

        // 2 + 3 + 20 + 50 + 50 + 0 = 125
        $result = $train->get_max_passengers_capacity();

        $this->assertEquals($result, 125);
    }

    public function testTrainMaxLoadCapacity(): void
    {
        $data = $this->get_data();
        $train = new Train(50, 20, $data['locomotives'], $data['carriages']);
        $train->form_train();

        // 15 + 25 + 10 + 15 + 15 + 0 + 100 = 180
        $result = $train->get_max_load_capacity();

        $this->assertEquals($result, 180);
    }

    public static function maxPayloadProvider(): array
    {
        return [
            [50, 20, 183.75],
            [100, 25, 187.5],
            [150, 30, 191.25],
            [200, 35, 195],
        ];
    }

    /**
     * @dataProvider maxPayloadProvider
     */
    public function testMaxTrainPayload(int $number_of_passengers, int $load_capacity, float $expected): void
    {
        $data = $this->get_data();
        $train = new Train($number_of_passengers, $load_capacity, $data['locomotives'], $data['carriages']);
        $train->form_train();

        // 50 * 0.075 + 180 = 183.75 tons
        $result = $train->get_max_payload();

        $this->assertEquals($result, $expected);
    }

    public function testMaxTotalWeight(): void
    {
        $data = $this->get_data();
        $train = new Train(50, 20, $data['locomotives'], $data['carriages']);
        $train->form_train();

        // 95 + 183.75 = 278.75
        $result = $train->get_max_total_weight();

        $this->assertEquals($result, 278.75);
    }

    public function testTrainFullLength(): void
    {
        $data = $this->get_data();
        $train = new Train(50, 20, $data['locomotives'], $data['carriages']);
        $train->form_train();

        // 15 + 25 + 15 + 20 + 20 + 15 + 15
        $result = $train->get_full_length();

        $this->assertEquals($result, 125);
    }

    public function testTrainIsDrivable(): void
    {
        $data = $this->get_data();
        $train = new Train(50, 20, $data['locomotives'], $data['carriages']);
        $train->form_train();

        // 300 > 173.5
        $result = $train->is_drivable();

        $this->assertEquals($result, true);
    }

    public static function numberOfConductorsProvider(): array
    {
        return [
            [50, 1],
            [89, 2],
            [101, 3],
            [251, 6],
        ];
    }

    /**
     * @dataProvider numberOfConductorsProvider
     */
    public function testTrainNumberOfConductors(int $number_of_passengers, int $expected): void
    {
        $data = $this->get_data();
        $train = new Train($number_of_passengers, 20, $data['locomotives'], $data['carriages']);
        $train->form_train();

        // 1
        $result = $train->get_number_of_conductors();

        $this->assertEquals($result, $expected);
    }
}
