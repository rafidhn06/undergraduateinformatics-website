import { Suspense } from 'react';

import { QueryClient, QueryClientProvider } from '@tanstack/react-query';

import { render, screen } from '@testing-library/react';
import axios from 'axios';
import { beforeEach, describe, expect, it, vi } from 'vitest';

import { type LinkSummary } from '@/types/link';
import { type PostSummary } from '@/types/post';

import { HomePage } from './HomePage';
import { type HomePayload } from './types';

vi.mock('axios', async () => {
    const actual = await vi.importActual<typeof import('axios')>('axios');

    return {
        ...actual,
        default: {
            ...actual.default,
            get: vi.fn(),
        },
    };
});

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

function homePayload(overrides: Partial<HomePayload['data']> = {}): HomePayload {
    return {
        status: 'success',
        data: {
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
        },
    };
}

function renderPage() {
    const queryClient = new QueryClient({
        defaultOptions: {
            queries: { retry: false },
        },
    });

    return render(
        <QueryClientProvider client={queryClient}>
            <Suspense fallback={null}>
                <HomePage />
            </Suspense>
        </QueryClientProvider>
    );
}

describe('HomePage', () => {
    beforeEach(() => {
        vi.mocked(axios.get).mockResolvedValue({ data: homePayload() });
        delete (window as any).__INITIAL_DATA__;
    });

    it('renders the greeting heading', async () => {
        renderPage();

        expect(
            await screen.findByRole('heading', {
                name: 'Portal Informasi Sarjana Informatika',
            })
        ).toBeInTheDocument();
    });

    it('renders the latest posts section with its posts', async () => {
        renderPage();

        expect(
            await screen.findByRole('heading', { name: 'Informasi Terbaru' })
        ).toBeInTheDocument();
        expect(
            screen.getByRole('heading', { name: 'Pengumuman Beasiswa 2026' })
        ).toBeInTheDocument();
    });

    it('renders the latest links section with its links', async () => {
        renderPage();

        expect(await screen.findByRole('heading', { name: 'Tautan Terbaru' })).toBeInTheDocument();
        expect(screen.getByRole('link', { name: 'Portal Akademik' })).toHaveAttribute(
            'href',
            'https://portal.telkomuniversity.ac.id/'
        );
    });

    it('renders the dashboard section heading and chart card titles', async () => {
        renderPage();

        expect(
            await screen.findByRole('heading', { name: 'Statistik Mahasiswa' })
        ).toBeInTheDocument();
        expect(screen.getByRole('heading', { name: 'Mahasiswa per Angkatan' })).toBeInTheDocument();
    });

    it('renders empty messages when there are no posts or links', async () => {
        vi.mocked(axios.get).mockResolvedValue({
            data: homePayload({ latest_posts: [], latest_links: [] }),
        });

        renderPage();

        expect(await screen.findByText('Belum ada berita atau pengumuman.')).toBeInTheDocument();
        expect(screen.getByText('Belum ada tautan penting.')).toBeInTheDocument();
    });
});
