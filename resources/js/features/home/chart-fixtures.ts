import type { DashboardDataset } from './types';

export const barDataset: DashboardDataset = {
    id: 1,
    title: 'Mahasiswa per Angkatan',
    chartType: 'bar',
    labels: ['2022', '2023', '2024', '2025'],
    values: [240, 265, 289, 312],
};

export const pieDataset: DashboardDataset = {
    id: 2,
    title: 'Mahasiswa per Provinsi',
    chartType: 'pie',
    labels: ['Jawa Barat', 'Jawa Tengah', 'Jawa Timur', 'Banten'],
    values: [320, 148, 122, 76],
};

export const lineDataset: DashboardDataset = {
    id: 3,
    title: 'Pertumbuhan Mahasiswa per Tahun',
    chartType: 'line',
    labels: ['2019', '2020', '2021', '2022', '2023', '2024', '2025'],
    values: [180, 205, 228, 240, 265, 289, 312],
};

export const dashboardPieDataset: DashboardDataset = {
    id: 2,
    title: 'Mahasiswa per Provinsi',
    chartType: 'pie',
    labels: ['Jawa Barat', 'Jawa Tengah', 'Jawa Timur', 'Banten', 'DKI Jakarta'],
    values: [320, 148, 122, 76, 58],
};

const jalurDataset: DashboardDataset = {
    id: 4,
    title: 'Mahasiswa per Jalur Masuk',
    chartType: 'bar',
    labels: ['SNBT', 'SNBP', 'Mandiri'],
    values: [140, 95, 77],
};

export const dashboardDatasets: DashboardDataset[] = [
    barDataset,
    dashboardPieDataset,
    lineDataset,
    jalurDataset,
];
