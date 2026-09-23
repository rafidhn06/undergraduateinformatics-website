import { fireEvent, render, screen } from '@testing-library/react';
import { describe, expect, it, vi } from 'vitest';

import { useMediaQuery } from '@/hooks/useMediaQuery';

import { ChartTooltip } from './chart-tooltip';
import { useChartTooltip } from './useChartTooltip';

vi.mock('@/hooks/useMediaQuery', () => ({
    useMediaQuery: vi.fn(),
}));

function renderHookHarness() {
    function Harness() {
        const tooltip = useChartTooltip();
        const { containerRef, chartProps } = tooltip;

        return (
            <div ref={containerRef} data-testid="container">
                <span data-testid="trigger">{tooltip.trigger}</span>
                <span data-testid="active">{String(tooltip.active)}</span>
                <button type="button" data-testid="chart" {...chartProps}>
                    chart
                </button>
            </div>
        );
    }

    render(<Harness />);

    return {
        get trigger() {
            return screen.getByTestId('trigger').textContent;
        },
        get active() {
            const value = screen.getByTestId('active').textContent;
            return value === 'undefined' ? undefined : value === 'true';
        },
    };
}

describe('useChartTooltip', () => {
    it('uses hover trigger on desktop', () => {
        vi.mocked(useMediaQuery).mockReturnValue(false);

        const harness = renderHookHarness();

        expect(harness.trigger).toBe('hover');
        expect(harness.active).toBeUndefined();
    });

    it('uses click trigger on mobile', () => {
        vi.mocked(useMediaQuery).mockReturnValue(true);

        const harness = renderHookHarness();

        expect(harness.trigger).toBe('click');
    });

    it('dismisses the tooltip when tapping outside on mobile', () => {
        vi.mocked(useMediaQuery).mockReturnValue(true);

        const harness = renderHookHarness();

        const chartButton = screen.getByTestId('chart');
        fireEvent.click(chartButton);
        expect(harness.active).toBeUndefined();

        fireEvent.click(document.body);
        expect(harness.active).toBe(false);
    });

    it('resets the tooltip state on chart interaction', () => {
        vi.mocked(useMediaQuery).mockReturnValue(true);

        const harness = renderHookHarness();

        const chartButton = screen.getByTestId('chart');
        fireEvent.click(chartButton);
        fireEvent.click(document.body);
        expect(harness.active).toBe(false);

        fireEvent.mouseMove(chartButton);
        expect(harness.active).toBeUndefined();
    });
});

describe('ChartTooltip', () => {
    it('renders nothing when inactive', () => {
        const { container } = render(<ChartTooltip active={false} />);
        expect(container).toBeEmptyDOMElement();
    });

    it('renders label and value when active', () => {
        render(
            <ChartTooltip
                active
                label="2024"
                payload={[{ name: 'Mahasiswa', value: 120 }]}
                unit=" orang"
            />
        );

        expect(screen.getByText('2024')).toBeInTheDocument();
        expect(screen.getByText('Mahasiswa: 120 orang')).toBeInTheDocument();
    });
});
