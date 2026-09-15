import type { Story, StoryDefault } from '@ladle/react';

import { DashboardCharts } from './DashboardCharts';
import { DashboardChartsSkeleton } from './DashboardChartsStates';
import { type DashboardDataset } from './types';

const datasets: DashboardDataset[] = [
    {
        id: 1,
        title: 'Mahasiswa per Angkatan',
        chart_type: 'bar',
        x_label: 'Angkatan',
        y_label: 'Jumlah',
        labels: ['2022', '2023', '2024', '2025'],
        values: [240, 265, 289, 312],
    },
    {
        id: 2,
        title: 'Mahasiswa per Provinsi',
        chart_type: 'pie',
        x_label: 'Provinsi',
        y_label: 'Jumlah',
        labels: ['Jawa Barat', 'Jawa Tengah', 'Jawa Timur', 'Banten', 'DKI Jakarta'],
        values: [320, 148, 122, 76, 58],
    },
    {
        id: 3,
        title: 'Pertumbuhan Mahasiswa per Tahun',
        chart_type: 'line',
        x_label: 'Tahun',
        y_label: 'Jumlah',
        labels: ['2019', '2020', '2021', '2022', '2023', '2024', '2025'],
        values: [180, 205, 228, 240, 265, 289, 312],
    },
    {
        id: 4,
        title: 'Mahasiswa per Jalur Masuk',
        chart_type: 'bar',
        x_label: 'Jalur',
        y_label: 'Jumlah',
        labels: ['SNBT', 'SNBP', 'Mandiri'],
        values: [140, 95, 77],
    },
];

export default {
    title: 'Charts/DashboardCharts',
} satisfies StoryDefault;

export const Desktop: Story = () => <DashboardCharts datasets={datasets} />;
Desktop.meta = { width: 'large' };

export const Mobile: Story = () => <DashboardCharts datasets={datasets} />;
Mobile.meta = { width: 'small' };

export const Loading: Story = () => <DashboardChartsSkeleton />;
Loading.meta = { width: 'large' };

export const LoadingMobile: Story = () => <DashboardChartsSkeleton />;
LoadingMobile.meta = { width: 'small' };
