import { render, screen } from '@testing-library/react';
import { describe, expect, it, vi } from 'vitest';

import { ChartCard } from './ChartCard';
import { type DashboardDataset } from './types';

vi.mock('./bar-chart', () => ({
    BarChart: () => <div data-testid="bar-chart" />,
}));

vi.mock('./pie-chart', () => ({
    PieChart: () => <div data-testid="pie-chart" />,
}));

vi.mock('./line-chart', () => ({
    LineChart: () => <div data-testid="line-chart" />,
}));

function dataset(overrides: Partial<DashboardDataset> = {}): DashboardDataset {
    return {
        id: 1,
        title: 'Mahasiswa per Angkatan',
        chart_type: 'bar',
        x_label: 'Angkatan',
        y_label: 'Jumlah',
        labels: ['2022', '2023'],
        values: [240, 255],
        ...overrides,
    };
}

describe('ChartCard', () => {
    it('renders the dataset title', () => {
        render(<ChartCard dataset={dataset()} />);

        expect(screen.getByRole('heading', { name: 'Mahasiswa per Angkatan' })).toBeInTheDocument();
    });

    it('truncates a long title to a single line', () => {
        render(<ChartCard dataset={dataset()} />);

        const heading = screen.getByRole('heading', { name: 'Mahasiswa per Angkatan' });
        expect(heading).toHaveClass('truncate', 'min-w-0');
    });

    it('renders a bar chart for chart_type bar', () => {
        render(<ChartCard dataset={dataset()} />);

        expect(screen.getByTestId('bar-chart')).toBeInTheDocument();
        expect(screen.queryByTestId('pie-chart')).not.toBeInTheDocument();
        expect(screen.queryByTestId('line-chart')).not.toBeInTheDocument();
    });

    it('renders a pie chart for chart_type pie', () => {
        render(<ChartCard dataset={dataset({ chart_type: 'pie' })} />);

        expect(screen.getByTestId('pie-chart')).toBeInTheDocument();
        expect(screen.queryByTestId('bar-chart')).not.toBeInTheDocument();
    });

    it('renders a line chart for chart_type line', () => {
        render(<ChartCard dataset={dataset({ chart_type: 'line' })} />);

        expect(screen.getByTestId('line-chart')).toBeInTheDocument();
        expect(screen.queryByTestId('bar-chart')).not.toBeInTheDocument();
    });
});
