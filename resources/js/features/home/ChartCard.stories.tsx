import type { Story, StoryDefault } from '@ladle/react';

import { ChartCard } from './ChartCard';
import { LineChart as LineChartView } from './line-chart';
import { PieChart as PieChartView } from './pie-chart';
import { type DashboardDataset } from './types';

const barDataset: DashboardDataset = {
    id: 1,
    title: 'Mahasiswa per Angkatan',
    chart_type: 'bar',
    x_label: 'Angkatan',
    y_label: 'Jumlah',
    labels: ['2022', '2023', '2024', '2025'],
    values: [240, 265, 289, 312],
};

const pieDataset: DashboardDataset = {
    id: 2,
    title: 'Mahasiswa per Provinsi',
    chart_type: 'pie',
    x_label: 'Provinsi',
    y_label: 'Jumlah',
    labels: ['Jawa Barat', 'Jawa Tengah', 'Jawa Timur', 'Banten'],
    values: [320, 148, 122, 76],
};

const lineDataset: DashboardDataset = {
    id: 3,
    title: 'Pertumbuhan Mahasiswa per Tahun',
    chart_type: 'line',
    x_label: 'Tahun',
    y_label: 'Jumlah',
    labels: ['2019', '2020', '2021', '2022', '2023', '2024', '2025'],
    values: [180, 205, 228, 240, 265, 289, 312],
};

const lineChartLabels = ['2019', '2020', '2021', '2022', '2023', '2024', '2025'];
const lineChartValues = [180, 205, 228, 240, 265, 289, 312];

const pieChartLabels = ['Jawa Barat', 'Jawa Tengah', 'Jawa Timur', 'Banten', 'DKI Jakarta'];
const pieChartValues = [320, 148, 122, 76, 58];

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
    <LineChartView labels={lineChartLabels} values={lineChartValues} />
);
LineChart.meta = { width: 'large' };

export const LineChartMobile: Story = () => (
    <LineChartView labels={lineChartLabels} values={lineChartValues} />
);
LineChartMobile.meta = { width: 'small' };

export const PieChart: Story = () => (
    <PieChartView labels={pieChartLabels} values={pieChartValues} />
);
PieChart.meta = { width: 'large' };

export const PieChartMobile: Story = () => (
    <PieChartView labels={pieChartLabels} values={pieChartValues} />
);
PieChartMobile.meta = { width: 'small' };
