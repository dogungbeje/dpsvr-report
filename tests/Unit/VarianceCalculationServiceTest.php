<?php

namespace Tests\Unit;

use App\Enums\VarianceStatus;
use App\Services\VarianceCalculationService;
use PHPUnit\Framework\TestCase;

class VarianceCalculationServiceTest extends TestCase
{
    public function test_expected_volume_sold_is_calculated_correctly(): void
    {
        $service = new VarianceCalculationService();

        $this->assertSame(6200.00, $service->expectedVolumeSold(18500.00, 0.00, 12300.00));
    }

    public function test_expected_revenue_is_calculated_correctly(): void
    {
        $service = new VarianceCalculationService();

        $this->assertSame(4333800.00, $service->expectedRevenue(6200.00, 699.00));
    }

    public function test_exact_match_returns_exact_match_status(): void
    {
        $service = new VarianceCalculationService();

        $result = $service->evaluateShiftVariance(1000000.00, 1000000.00);

        $this->assertSame(VarianceStatus::EXACT_MATCH, $result->status);
        $this->assertSame(0.0, $result->variancePct);
    }

    public function test_minor_variance_is_flagged(): void
    {
        $service = new VarianceCalculationService();

        $result = $service->evaluateShiftVariance(1000000.00, 990000.00);

        $this->assertSame(VarianceStatus::FLAGGED_MINOR, $result->status);
    }

    public function test_critical_variance_locks_shift(): void
    {
        $service = new VarianceCalculationService();

        $result = $service->evaluateShiftVariance(1000000.00, 900000.00);

        $this->assertSame(VarianceStatus::FLAGGED_CRITICAL, $result->status);
        $this->assertTrue($result->requiresShiftLock);
    }
}
