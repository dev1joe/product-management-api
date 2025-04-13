<?php
declare(strict_types=1);

namespace App\Services;

class CurrencyService
{
    public function dollarsToCents(float $dollars): int {
        $cents = $dollars * 100;
        return (int) $cents;
    }

    public function centsToDollars(int $cents): float {
        $dollars = $cents / 100.0;
        return $dollars;
    }
}