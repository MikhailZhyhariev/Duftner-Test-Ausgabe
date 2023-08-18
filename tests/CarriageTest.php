<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class CarriageTest extends TestCase
{
    public function testCannotBeCreatedWithWrongType(): void
    {
        $this->expectException(UnknownTypeException::class);

        $c = new Carriage(
            empty_weight: 10, // tons
            length: 15, // meter
            type: "WrongType",
            max_passengers_capacity: 20,
            max_load_capacity: 2,
            manufacturer: 'Test Carriage',
            release_year: 1998
        );
    }

    public function testSerialNumberMatchesFormat(): void
    {
        $c = new Carriage(
            empty_weight: 10, // tons
            length: 15, // meter
            type: Carriage::TYPE_PERSONEN,
            max_passengers_capacity: 20,
            max_load_capacity: 2,
            manufacturer: 'Test Carriage',
            release_year: 1998
        );

        $this->assertStringMatchesFormat('%s_%s-%d_%s', $c->serial_number);
    }

    public function testSerialNumberHasAllInfo(): void
    {
        $c = new Carriage(
            empty_weight: 10, // tons
            length: 15, // meter
            type: Carriage::TYPE_PERSONEN,
            max_passengers_capacity: 20,
            max_load_capacity: 2,
            manufacturer: 'Test Carriage',
            release_year: 1998
        );

        $this->assertStringStartsWith(
            strtoupper(Carriage::TYPE_PERSONEN) . "_TEST-CARRIAGE-1998_",
            $c->serial_number
        );
    }

    public function testSerialNumberHasOnlyUniqueValues(): void
    {
        $carriage_serial_numbers = [];
        for ($i = 0; $i < 1000; $i++) {
            $c = new Carriage(
                empty_weight: 10, // tons
                length: 15, // meter
                type: Carriage::TYPE_PERSONEN,
                max_passengers_capacity: 20,
                max_load_capacity: 2,
                manufacturer: 'Test Carriage',
                release_year: 1998
            );

            array_push($carriage_serial_numbers, $c->serial_number);
        }

        $this->assertEquals(
            count($carriage_serial_numbers),
            count(array_unique($carriage_serial_numbers))
        );
    }

    public function testSortByType(): void
    {
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

        $sorted = Carriage::get_sorted_by_type([$c1, $c2, $c3, $c4, $c5]);

        $this->assertEquals($sorted, [$c1, $c4, $c2, $c3, $c5]);
    }

    public function testSortByTypeReverse(): void
    {
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

        $sorted = Carriage::get_sorted_by_type([$c1, $c2, $c3, $c4, $c5], reverse: true);

        $this->assertEquals($sorted, [$c5, $c2, $c3, $c4, $c1]);
    }
}
