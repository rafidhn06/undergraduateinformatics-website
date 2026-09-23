import type { Story, StoryDefault } from '@ladle/react';

import { DashboardCharts } from './DashboardCharts';
import { DashboardChartsSkeleton } from './DashboardChartsStates';
import { dashboardDatasets } from './chart-fixtures';

export default {
    title: 'Charts/DashboardCharts',
} satisfies StoryDefault;

export const Desktop: Story = () => <DashboardCharts datasets={dashboardDatasets} />;
Desktop.meta = { width: 'large' };

export const Mobile: Story = () => <DashboardCharts datasets={dashboardDatasets} />;
Mobile.meta = { width: 'small' };

export const Loading: Story = () => <DashboardChartsSkeleton />;
Loading.meta = { width: 'large' };

export const LoadingMobile: Story = () => <DashboardChartsSkeleton />;
LoadingMobile.meta = { width: 'small' };
