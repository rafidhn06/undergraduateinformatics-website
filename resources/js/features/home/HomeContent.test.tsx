import { render, screen } from '@testing-library/react';
import { describe, expect, it, vi } from 'vitest';

import { type LinkSummary } from '@/types/link';
import { type PostSummary } from '@/types/post';

import { HomeContent } from './HomeContent';
import { type HomeData } from './types';

vi.mock('embla-carousel-react', () => {
    return {
        default: () => [vi.fn(), undefined],
    };
});

vi.mock('@tanstack/react-router', async () => {
    const { routerModuleMock } = await import('@/test/mocks');

    return routerModuleMock();
});

const post: PostSummary = {
    id: 7,
    slug: 'pengumuman-beasiswa-2026',
    title: 'Pengumuman Beasiswa 2026',
    subtitle: 'Pendaftaran beasiswa dibuka hingga akhir bulan.',
    updated_at: '2026-09-05T12:00:00.000Z',
    tags: [{ id: 1, slug: 'beasiswa', name: 'Beasiswa' }],
};

const link: LinkSummary = {
    id: 7,
    name: 'Portal Akademik',
    link: 'https://portal.telkomuniversity.ac.id/',
    updated_at: '2026-09-05T12:00:00.000Z',
    section: { id: 2, name: 'Akademik' },
};

function homeData(overrides: Partial<HomeData> = {}): HomeData {
    return {
        latest_posts: [post],
        latest_links: [link],
        dashboard: [
            {
                id: 1,
                title: 'Mahasiswa per Angkatan',
                chart_type: 'bar',
                x_label: 'Angkatan',
                y_label: 'Jumlah',
                labels: ['2022', '2023', '2024'],
                values: [240, 255, 270],
            },
        ],
        ...overrides,
    };
}

describe('HomeContent', () => {
    it('renders the greeting heading', () => {
        render(<HomeContent data={homeData()} />);

        expect(
            screen.getByRole('heading', {
                name: 'Portal Informasi Sarjana Informatika',
            })
        ).toBeInTheDocument();
    });

    it('renders the latest posts section with its posts', () => {
        render(<HomeContent data={homeData()} />);

        expect(screen.getByRole('heading', { name: 'Informasi Terbaru' })).toBeInTheDocument();
        expect(
            screen.getByRole('heading', { name: 'Pengumuman Beasiswa 2026' })
        ).toBeInTheDocument();
    });

    it('renders the latest links section with a link to all links', () => {
        render(<HomeContent data={homeData()} />);

        expect(screen.getByRole('heading', { name: 'Tautan Terbaru' })).toBeInTheDocument();
        expect(screen.getByRole('link', { name: 'Portal Akademik' })).toHaveAttribute(
            'href',
            'https://portal.telkomuniversity.ac.id/'
        );
        expect(screen.getByRole('link', { name: 'Tautan Terbaru' })).toHaveAttribute(
            'href',
            '/links'
        );
    });

    it('renders the dashboard charts', async () => {
        render(<HomeContent data={homeData()} />);

        expect(
            await screen.findByRole('heading', { name: 'Statistik Mahasiswa' })
        ).toBeInTheDocument();
        expect(screen.getByRole('heading', { name: 'Mahasiswa per Angkatan' })).toBeInTheDocument();
    });

    it('renders empty messages when there are no posts or links', () => {
        render(<HomeContent data={homeData({ latest_posts: [], latest_links: [] })} />);

        expect(screen.getByText('Belum ada berita atau pengumuman.')).toBeInTheDocument();
        expect(screen.getByText('Belum ada tautan penting.')).toBeInTheDocument();
    });

    it('renders an empty message when there is no dashboard data', async () => {
        render(<HomeContent data={homeData({ dashboard: [] })} />);

        expect(await screen.findByText('Belum ada data statistik.')).toBeInTheDocument();
    });
});
