import type { Story, StoryDefault } from '@ladle/react';

import { ChartCard } from './ChartCard';
import { barDataset, dashboardPieDataset, lineDataset, pieDataset } from './chart-fixtures';
import { LineChart as LineChartView } from './line-chart';
import { PieChart as PieChartView } from './pie-chart';

export default {
    title: 'Charts/ChartCard',
} satisfies StoryDefault;

export const Bar: Story = () => (
    <div className="mx-auto w-full max-w-xl p-4">
        <ChartCard dataset={barDataset} />
    </div>
);
Bar.meta = { width: 'large' };

export const Pie: Story = () => (
    <div className="mx-auto w-full max-w-xl p-4">
        <ChartCard dataset={pieDataset} />
    </div>
);
Pie.meta = { width: 'large' };

export const Line: Story = () => (
    <div className="mx-auto w-full max-w-xl p-4">
        <ChartCard dataset={lineDataset} />
    </div>
);
Line.meta = { width: 'large' };

export const LineChart: Story = () => (
    <LineChartView labels={lineDataset.labels} values={lineDataset.values} />
);
LineChart.meta = { width: 'large' };

export const LineChartMobile: Story = () => (
    <LineChartView labels={lineDataset.labels} values={lineDataset.values} />
);
LineChartMobile.meta = { width: 'small' };

export const PieChart: Story = () => (
    <PieChartView labels={dashboardPieDataset.labels} values={dashboardPieDataset.values} />
);
PieChart.meta = { width: 'large' };

export const PieChartMobile: Story = () => (
    <PieChartView labels={dashboardPieDataset.labels} values={dashboardPieDataset.values} />
);
PieChartMobile.meta = { width: 'small' };
