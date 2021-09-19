<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Tests;

use DateInterval;
use DateTimeImmutable;
use hamburgscleanest\GuzzleAdvancedThrottle\TimeKeeper;
use PHPUnit\Framework\TestCase;

/**
 * Class TimeKeeperTests
 * @package hamburgscleanest\GuzzleAdvancedThrottle\Tests
 */
class TimeKeeperTest extends TestCase
{

    /** @test */
    public function can_be_created_statically(): void
    {
        $timeKeeper = TimeKeeper::create(60);

        static::assertInstanceOf(TimeKeeper::class, $timeKeeper);
    }

    /** @test */
    public function sets_correct_expiration_date(): void
    {
        $interval = 60;
        $timeKeeper = new TimeKeeper($interval);

        $minutesNow = + (new DateTimeImmutable())->format('i');
        $timeKeeper->start();

        $minutesExpiration = +$timeKeeper->getExpiration()->format('i');

        static::assertEquals($minutesNow + 1, $minutesExpiration);
    }

    /** @test */
    public function expiration_date_can_be_set_manually(): void
    {
        $interval = 60;
        $timeKeeper = new TimeKeeper($interval);

        $myExpiration = (new DateTimeImmutable())->add(new DateInterval('PT120S'));
        $timeKeeper->setExpiration($myExpiration);

        static::assertEquals($myExpiration, $timeKeeper->getExpiration());
    }

    /** @test */
    public function remaining_time_is_correct(): void
    {
        $interval = 60;
        $timeKeeper = new TimeKeeper($interval);
        $timeKeeper->start();
        static::assertEquals($interval, $timeKeeper->getRemainingSeconds());
    }

    /** @test */
    public function is_expired_is_correct(): void
    {
        $interval = 60;
        $timeKeeper = new TimeKeeper($interval);
        $timeKeeper->start();
        static::assertFalse($timeKeeper->isExpired());
        $timeKeeper->setExpiration((new DateTimeImmutable())->sub(new DateInterval('P1D')));
        static::assertTrue($timeKeeper->isExpired());
    }

    /** @test */
    public function remaining_time_is_correct_when_expired(): void
    {
        $interval = 60;
        $timeKeeper = new TimeKeeper($interval);
        $timeKeeper->start();
        $timeKeeper->setExpiration((new DateTimeImmutable())->sub(new DateInterval('P1D')));
        static::assertEquals(60, $timeKeeper->getRemainingSeconds());
    }

    /** @test */
    public function resets_correctly(): void
    {
        $interval = 60;
        $timeKeeper = new TimeKeeper($interval);
        $timeKeeper->start();
        $timeKeeper->reset();

        static::assertNull($timeKeeper->getExpiration());
    }
}
