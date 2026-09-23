import { Suspense } from 'react';

import { QueryClient, QueryClientProvider } from '@tanstack/react-query';

import { render, screen } from '@testing-library/react';
import axios from 'axios';
import { beforeEach, describe, expect, it, vi } from 'vitest';

import { TagListPage } from './TagListPage';

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

vi.mock('@tanstack/react-router', async () => {
    const { routerModuleMock } = await import('@/test/mocks');

    return routerModuleMock();
});

const tagsPayload = {
    status: 'success',
    data: [
        {
            id: 1,
            slug: 'academic',
            name: 'Academic',
            description: 'Academic announcements',
            posts_count: 3,
        },
        { id: 2, slug: 'beasiswa', name: 'Beasiswa', description: null, posts_count: 0 },
    ],
};

function renderPage() {
    const queryClient = new QueryClient({
        defaultOptions: {
            queries: { retry: false },
        },
    });

    return render(
        <QueryClientProvider client={queryClient}>
            <Suspense fallback={null}>
                <TagListPage />
            </Suspense>
        </QueryClientProvider>
    );
}

describe('TagListPage', () => {
    beforeEach(() => {
        vi.mocked(axios.get).mockResolvedValue({ data: tagsPayload });
        delete window.__INITIAL_DATA__;
    });

    it('renders the heading and a row per tag with description and post count', async () => {
        renderPage();

        expect(await screen.findByRole('heading', { name: 'Daftar Topik' })).toBeInTheDocument();
        expect(
            screen.getByText(
                'Kumpulan topik informasi perkuliahan peserta didik Program Studi Sarjana Informatika Telkom University.'
            )
        ).toBeInTheDocument();
        expect(screen.getByText('Academic (3)')).toBeInTheDocument();
        expect(screen.getByText('Academic announcements')).toBeInTheDocument();
        expect(screen.getByText('Beasiswa (0)')).toBeInTheDocument();
    });

    it('omits the description paragraph for a tag without one', async () => {
        renderPage();

        await screen.findByRole('heading', { name: 'Daftar Topik' });
        expect(screen.queryByText('Beasiswa announcements')).not.toBeInTheDocument();
    });

    it('links each tag name to its detail page', async () => {
        renderPage();

        await screen.findByRole('heading', { name: 'Daftar Topik' });

        const academic = screen.getByRole('link', { name: 'Academic (3)' });
        expect(academic).toHaveAttribute('href', '/tags/academic');
        expect(screen.getByRole('link', { name: 'Beasiswa (0)' })).toHaveAttribute(
            'href',
            '/tags/beasiswa'
        );
    });

    it('shows an empty state with the intro and a message when there are no tags', async () => {
        vi.mocked(axios.get).mockResolvedValue({ data: { status: 'success', data: [] } });

        renderPage();

        expect(
            await screen.findByRole('heading', { name: 'Daftar Topik' }, { timeout: 3000 })
        ).toBeInTheDocument();
        expect(
            screen.getByText(
                'Kumpulan topik informasi perkuliahan peserta didik Program Studi Sarjana Informatika Telkom University.'
            )
        ).toBeInTheDocument();
        expect(screen.getByText('Belum ada topik.')).toBeInTheDocument();
    });
});
