<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class LocomotiveTest extends TestCase
{
    public function testCannotBeCreatedWithWrongType(): void
    {
        $this->expectException(UnknownTypeException::class);

        $c = new Locomotive(
            tractive_force: 100, // tons
            empty_weight: 10, // tons
            length: 15, // meter
            type: "WrongType",
            max_passengers_capacity: 20,
            max_load_capacity: 2,
            manufacturer: 'Test Locomotive',
            release_year: 1998
        );
    }

    public function testSerialNumberMatchesFormat(): void
    {
        $c = new Locomotive(
            tractive_force: 100, // tons
            empty_weight: 10, // tons
            length: 15, // meter
            type: Locomotive::TYPE_DIESEL,
            max_passengers_capacity: 20,
            max_load_capacity: 2,
            manufacturer: 'Test Locomotive',
            release_year: 1998
        );

        $this->assertStringMatchesFormat('%s_%s-%d_%s', $c->serial_number);
    }

    public function testSerialNumberHasAllInfo(): void
    {
        $c = new Locomotive(
            tractive_force: 100, // tons
            empty_weight: 10, // tons
            length: 15, // meter
            type: Locomotive::TYPE_DIESEL,
            max_passengers_capacity: 20,
            max_load_capacity: 2,
            manufacturer: 'Test Locomotive',
            release_year: 1998
        );

        $this->assertStringStartsWith(
            strtoupper(Locomotive::TYPE_DIESEL) . "_TEST-LOCOMOTIVE-1998_",
            $c->serial_number
        );
    }

    public function testSerialNumberHasOnlyUniqueValues(): void
    {
        $Locomotive_serial_numbers = [];
        for ($i = 0; $i < 1000; $i++) {
            $c = new Locomotive(
                tractive_force: 100, // tons
                empty_weight: 10, // tons
                length: 15, // meter
                type: Locomotive::TYPE_DIESEL,
                max_passengers_capacity: 20,
                max_load_capacity: 2,
                manufacturer: 'Test Locomotive',
                release_year: 1998
            );

            array_push($Locomotive_serial_numbers, $c->serial_number);
        }

        $this->assertEquals(
            count($Locomotive_serial_numbers),
            count(array_unique($Locomotive_serial_numbers))
        );
    }

    public function testSortByTractionForce(): void
    {
        $l1 = new Locomotive(
            tractive_force: 10,
            empty_weight: 10, // tons
            length: 15, // meter
            type: Locomotive::TYPE_DIESEL,
            max_passengers_capacity: 20,
            max_load_capacity: 10,
            manufacturer: 'Test Locomotive P',
            release_year: 1998
        );
        $l2 = new Locomotive(
            tractive_force: 99,
            empty_weight: 20, // tons
            length: 20, // meter
            type: Locomotive::TYPE_DIESEL,
            max_passengers_capacity: 50,
            max_load_capacity: 15,
            manufacturer: 'Test Locomotive Sc',
            release_year: 1998
        );
        $l3 = new Locomotive(
            tractive_force: 65,
            empty_weight: 20, // tons
            length: 20, // meter
            type: Locomotive::TYPE_DIESEL,
            max_passengers_capacity: 50,
            max_load_capacity: 15,
            manufacturer: 'Test Locomotive Sc',
            release_year: 1998
        );
        $l4 = new Locomotive(
            tractive_force: 33,
            empty_weight: 10, // tons
            length: 15, // meter
            type: Locomotive::TYPE_DIESEL,
            max_passengers_capacity: 0,
            max_load_capacity: 0,
            manufacturer: 'Test Locomotive Sp',
            release_year: 1998
        );
        $l5 = new Locomotive(
            tractive_force: 67,
            empty_weight: 5, // tons
            length: 15, // meter
            type: Locomotive::TYPE_DIESEL,
            max_passengers_capacity: 0,
            max_load_capacity: 100,
            manufacturer: 'Test Locomotive Sp',
            release_year: 2003
        );

        $sorted = Locomotive::get_sorted_by_traction_force([$l1, $l2, $l3, $l4, $l5]);

        $this->assertEquals($sorted, [$l1, $l4, $l3, $l5, $l2]);
    }

    public function testSortByTractionForceReverse(): void
    {
        $l1 = new Locomotive(
            tractive_force: 10,
            empty_weight: 10, // tons
            length: 15, // meter
            type: Locomotive::TYPE_DIESEL,
            max_passengers_capacity: 20,
            max_load_capacity: 10,
            manufacturer: 'Test Locomotive P',
            release_year: 1998
        );
        $l2 = new Locomotive(
            tractive_force: 99,
            empty_weight: 20, // tons
            length: 20, // meter
            type: Locomotive::TYPE_DIESEL,
            max_passengers_capacity: 50,
            max_load_capacity: 15,
            manufacturer: 'Test Locomotive Sc',
            release_year: 1998
        );
        $l3 = new Locomotive(
            tractive_force: 65,
            empty_weight: 20, // tons
            length: 20, // meter
            type: Locomotive::TYPE_DIESEL,
            max_passengers_capacity: 50,
            max_load_capacity: 15,
            manufacturer: 'Test Locomotive Sc',
            release_year: 1998
        );
        $l4 = new Locomotive(
            tractive_force: 33,
            empty_weight: 10, // tons
            length: 15, // meter
            type: Locomotive::TYPE_DIESEL,
            max_passengers_capacity: 0,
            max_load_capacity: 0,
            manufacturer: 'Test Locomotive Sp',
            release_year: 1998
        );
        $l5 = new Locomotive(
            tractive_force: 67,
            empty_weight: 5, // tons
            length: 15, // meter
            type: Locomotive::TYPE_DIESEL,
            max_passengers_capacity: 0,
            max_load_capacity: 100,
            manufacturer: 'Test Locomotive Sp',
            release_year: 2003
        );

        $sorted = Locomotive::get_sorted_by_traction_force([$l1, $l2, $l3, $l4, $l5], reverse: true);

        $this->assertEquals($sorted, [$l2, $l5, $l3, $l4, $l1]);
    }
}
