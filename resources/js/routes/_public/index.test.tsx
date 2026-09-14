import { type ReactNode, Suspense } from 'react';

import { QueryClient, QueryClientProvider } from '@tanstack/react-query';

import { render, screen } from '@testing-library/react';
import axios from 'axios';
import { beforeEach, describe, expect, it, vi } from 'vitest';

import { HomePage } from '@/features/home/HomePage';
import { type DashboardDataset, type HomePayload } from '@/features/home/types';
import { seoPage } from '@/lib/seo';
import { type LinkSummary } from '@/types/link';
import { type PostSummary } from '@/types/post';

import { Route } from './index';

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

const dashboardFixture: DashboardDataset = {
    id: 1,
    title: 'Mahasiswa per Angkatan',
    chart_type: 'bar',
    x_label: 'Angkatan',
    y_label: 'Jumlah',
    labels: ['2022', '2023'],
    values: [240, 255],
};

const postFixture: PostSummary = {
    id: 7,
    slug: 'pengumuman-beasiswa-2026',
    title: 'Pengumuman Beasiswa 2026',
    subtitle: 'Pendaftaran beasiswa dibuka hingga akhir bulan.',
    updated_at: '2026-09-05T12:00:00.000Z',
    tags: [{ id: 1, slug: 'beasiswa', name: 'Beasiswa' }],
};

const linkFixture: LinkSummary = {
    id: 7,
    name: 'Portal Akademik',
    link: 'https://portal.telkomuniversity.ac.id/',
    updated_at: '2026-09-05T12:00:00.000Z',
    section: { id: 2, name: 'Akademik' },
};

function homePayload(data: Partial<HomePayload['data']> = {}): HomePayload {
    return {
        status: 'success',
        data: {
            latest_posts: [],
            latest_links: [],
            dashboard: [],
            ...data,
        },
    };
}

function renderHome() {
    const Component = Route.options.component as () => ReactNode;

    return render(
        <QueryClientProvider
            client={
                new QueryClient({
                    defaultOptions: {
                        queries: { retry: false },
                    },
                })
            }
        >
            <Suspense fallback={null}>
                <Component />
            </Suspense>
        </QueryClientProvider>
    );
}

describe('HomePage route', () => {
    beforeEach(() => {
        delete (window as any).__INITIAL_DATA__;
    });

    it('renders the greeting and all home sections once the data loads', async () => {
        vi.mocked(axios.get).mockResolvedValue({
            data: homePayload(),
        });

        renderHome();

        expect(
            await screen.findByRole('heading', {
                name: 'Portal Informasi Sarjana Informatika',
            })
        ).toBeInTheDocument();
        expect(screen.getByRole('heading', { name: 'Informasi Terbaru' })).toBeInTheDocument();
        expect(screen.getByRole('heading', { name: 'Tautan Terbaru' })).toBeInTheDocument();
        expect(
            await screen.findByRole('heading', { name: 'Statistik Mahasiswa' })
        ).toBeInTheDocument();
    });

    it('renders latest posts, latest links, and dashboard charts from the payload', async () => {
        vi.mocked(axios.get).mockResolvedValue({
            data: homePayload({
                latest_posts: [postFixture],
                latest_links: [linkFixture],
                dashboard: [dashboardFixture],
            }),
        });

        renderHome();

        expect(
            await screen.findByRole('heading', { name: 'Pengumuman Beasiswa 2026' })
        ).toBeInTheDocument();
        expect(screen.getByRole('link', { name: 'Portal Akademik' })).toHaveAttribute(
            'href',
            'https://portal.telkomuniversity.ac.id/'
        );
        expect(screen.getByRole('link', { name: 'Informasi Terbaru' })).toHaveAttribute(
            'href',
            '/posts/search'
        );
        expect(screen.getByRole('link', { name: 'Tautan Terbaru' })).toHaveAttribute(
            'href',
            '/links'
        );
        expect(
            await screen.findByRole('heading', { name: 'Mahasiswa per Angkatan' })
        ).toBeInTheDocument();
    });

    it('sets the page title via the head option', () => {
        const head = Route.options.head as unknown as (context: unknown) => {
            meta?: { title?: string }[];
        };

        expect(head).toBeDefined();

        const result = head({});
        expect(result.meta?.[0]?.title).toBe(seoPage('home').title);
    });

    it('sets the page description via the head option', () => {
        const head = Route.options.head as unknown as (context: unknown) => {
            meta?: { title?: string; name?: string; content?: string }[];
        };

        const result = head({});
        const description = result.meta?.find((entry) => entry.name === 'description');
        expect(description?.content).toBe(seoPage('home').description);
    });

    it('renders the home page as its component', () => {
        expect(Route.options.component).toBe(HomePage);
    });
});
