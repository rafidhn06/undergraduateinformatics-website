import { render, screen } from '@testing-library/react';
import { describe, expect, it } from 'vitest';

import { DashboardChartsSkeleton } from './DashboardChartsStates';
import { HomeSkeleton } from './HomeStates';

function skeletonCount(container: HTMLElement) {
    return container.querySelectorAll('[data-slot="skeleton"]').length;
}

describe('HomeSkeleton', () => {
    it('renders the loading skeleton with a status role', () => {
        render(<HomeSkeleton />);

        expect(screen.getByRole('status', { name: 'Memuat beranda' })).toBeInTheDocument();
    });

    it('renders skeleton blocks for the greeting, five post cards, five link cards, and the charts', () => {
        const { container } = render(<HomeSkeleton />);

        expect(skeletonCount(container)).toBe(58);
    });

    it('starts with a block for the greeting heading', () => {
        const { container } = render(<HomeSkeleton />);

        expect(container.querySelector('[data-slot="skeleton"]')).toHaveClass('h-9');
    });
});

describe('DashboardChartsSkeleton', () => {
    it('renders skeleton blocks for the heading and six chart cards', () => {
        const { container } = render(<DashboardChartsSkeleton />);

        expect(skeletonCount(container)).toBe(13);
        expect(container.querySelector('[data-slot="skeleton"]')).toHaveClass('h-8');
    });
});
