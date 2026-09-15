import type { Story, StoryDefault } from '@ladle/react';

import { ErrorState } from '@/components/ErrorState';
import { RouterHarness } from '@/components/RouterHarness';

import { HomeContent } from './HomeContent';
import { HomeSkeleton } from './HomeStates';
import { type HomeData } from './types';

const homeFixture: HomeData = {
    latest_posts: [
        {
            id: 1,
            slug: 'pengumuman-beasiswa-2026',
            title: 'Pengumuman Beasiswa 2026',
            subtitle: 'Pendaftaran beasiswa dibuka hingga akhir bulan.',
            updated_at: '2026-09-05T12:00:00.000Z',
            tags: [{ id: 1, slug: 'beasiswa', name: 'Beasiswa' }],
        },
        {
            id: 2,
            slug: 'registrasi-ganjil-2026-2027',
            title: 'Registrasi Ganjil 2026/2027',
            subtitle: 'Periode registrasi dan tata cara pembayaran.',
            updated_at: '2026-09-03T12:00:00.000Z',
            tags: [{ id: 2, slug: 'registrasi', name: 'Registrasi' }],
        },
        {
            id: 3,
            slug: 'jadwal-wawancara-mbkm',
            title: 'Jadwal Wawancara MBKM',
            subtitle: 'Pengumuman jadwal wawancara peserta MBKM.',
            updated_at: '2026-08-28T12:00:00.000Z',
            tags: [{ id: 3, slug: 'mbkm', name: 'MBKM' }],
        },
    ],
    latest_links: [
        {
            id: 7,
            name: 'Portal Akademik',
            link: 'https://portal.telkomuniversity.ac.id/',
            updated_at: '2026-09-05T12:00:00.000Z',
            section: { id: 2, name: 'Akademik' },
        },
        {
            id: 8,
            name: 'SIAKAD',
            link: 'https://siakad.telkomuniversity.ac.id',
            updated_at: '2026-09-04T10:00:00.000Z',
            section: { id: 2, name: 'Akademik' },
        },
        {
            id: 9,
            name: 'Portal MBKM',
            link: 'https://mbkm.kemdikbud.go.id',
            updated_at: '2026-09-03T10:00:00.000Z',
            section: { id: 3, name: 'MBKM' },
        },
    ],
    dashboard: [
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
    ],
};

const emptyFixture: HomeData = {
    latest_posts: [],
    latest_links: [],
    dashboard: [],
};

export default {
    title: 'Home',
} satisfies StoryDefault;

export const Desktop: Story = () => (
    <RouterHarness>
        <HomeContent data={homeFixture} />
    </RouterHarness>
);
Desktop.meta = { width: 'large' };

export const Mobile: Story = () => (
    <RouterHarness>
        <HomeContent data={homeFixture} />
    </RouterHarness>
);
Mobile.meta = { width: 'medium' };

export const Empty: Story = () => (
    <RouterHarness>
        <HomeContent data={emptyFixture} />
    </RouterHarness>
);
Empty.meta = { width: 'large' };

export const Loading: Story = () => <HomeSkeleton />;
Loading.meta = { width: 'large' };

export const LoadingMobile: Story = () => <HomeSkeleton />;
LoadingMobile.meta = { width: 'medium' };

export const Error: Story = () => <ErrorState />;
Error.meta = { width: 'large' };
