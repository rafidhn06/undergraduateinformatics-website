import { render, screen } from '@testing-library/react';
import { describe, expect, it } from 'vitest';

import { ChartCard } from './ChartCard';
import { type DashboardDataset } from './types';

const baseDataset: DashboardDataset = {
    id: 1,
    title: 'Mahasiswa per Angkatan',
    chart_type: 'bar',
    labels: ['2022', '2023'],
    values: [240, 255],
};

function chartContainer(container: HTMLElement): HTMLElement | null {
    return container.querySelector('div[class*="aspect-"]');
}

describe('ChartCard', () => {
    it.each([['bar'], ['pie'], ['line']] as const)(
        'renders the chart container for wire-shaped chart_type %s',
        (chartType) => {
            const { container } = render(
                <ChartCard dataset={{ ...baseDataset, chart_type: chartType }} />
            );

            expect(screen.getByText(baseDataset.title)).toBeInTheDocument();
            expect(chartContainer(container)).not.toBeNull();
        }
    );

    it('renders title without a chart container for an unknown chart type', () => {
        const { container } = render(
            <ChartCard
                dataset={{ ...baseDataset, chart_type: undefined } as unknown as DashboardDataset}
            />
        );

        expect(screen.getByText(baseDataset.title)).toBeInTheDocument();
        expect(chartContainer(container)).toBeNull();
    });
});
