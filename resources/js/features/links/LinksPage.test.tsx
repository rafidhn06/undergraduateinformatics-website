import { Suspense } from 'react';

import { QueryClient, QueryClientProvider } from '@tanstack/react-query';

import { render, screen } from '@testing-library/react';
import axios from 'axios';
import { beforeEach, describe, expect, it, vi } from 'vitest';

import { LinksPage } from './LinksPage';
import { type LinksPayload } from './types';

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

const linksPayload: LinksPayload = {
    status: 'success',
    data: [
        {
            id: 1,
            name: 'Kumpulan Link MBKM',
            order_number: 1,
            links: [
                {
                    id: 3,
                    name: 'Angkatan 2020',
                    link: 'http://bit.ly/MBKM2020',
                    updated_at: '2026-09-03T10:00:00.000000Z',
                },
                {
                    id: 4,
                    name: 'Panduan Pendaftaran',
                    link: 'https://example.com/daftar',
                    updated_at: '2026-09-04T10:00:00.000000Z',
                },
            ],
        },
        {
            id: 2,
            name: 'Akademik',
            order_number: 2,
            links: [],
        },
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
                <LinksPage />
            </Suspense>
        </QueryClientProvider>
    );
}

describe('LinksPage', () => {
    beforeEach(() => {
        vi.mocked(axios.get).mockResolvedValue({ data: linksPayload });
        delete window.__INITIAL_DATA__;
    });

    it('renders the heading, description, a section heading per section, and its links', async () => {
        renderPage();

        expect(
            await screen.findByRole('heading', { name: 'Tautan Penting' }, { timeout: 3000 })
        ).toBeInTheDocument();
        expect(
            screen.getByText(
                'Jelajahi tautan penting pendukung perkuliahan peserta didik Program Studi Sarjana Informatika Telkom University.'
            )
        ).toBeInTheDocument();
        expect(screen.getByRole('heading', { name: 'Kumpulan Link MBKM' })).toBeInTheDocument();
        expect(screen.getByText('Angkatan 2020')).toBeInTheDocument();
        expect(screen.getByText('Panduan Pendaftaran')).toBeInTheDocument();
        expect(screen.getByRole('heading', { name: 'Akademik' })).toBeInTheDocument();
    });

    it('renders each link as an anchor with its href and opens in a new tab', async () => {
        renderPage();

        await screen.findByRole('heading', { name: 'Tautan Penting' });

        const link = screen.getByRole('link', { name: 'Angkatan 2020' });
        expect(link).toHaveAttribute('href', 'http://bit.ly/MBKM2020');
        expect(link).toHaveAttribute('target', '_blank');
        expect(link).toHaveAttribute('rel', 'noopener noreferrer');
    });

    it('shows an empty message for a section without links', async () => {
        renderPage();

        await screen.findByRole('heading', { name: 'Tautan Penting' });

        expect(screen.getByText('Belum ada tautan pada section ini.')).toBeInTheDocument();
    });

    it('shows an empty state with the intro, a desktop Daftar Isi heading, and a message when there are no sections', async () => {
        vi.mocked(axios.get).mockResolvedValue({ data: { status: 'success', data: [] } });

        renderPage();

        expect(await screen.findByRole('heading', { name: 'Tautan Penting' })).toBeInTheDocument();
        expect(
            screen.getByText(
                'Jelajahi tautan penting pendukung perkuliahan peserta didik Program Studi Sarjana Informatika Telkom University.'
            )
        ).toBeInTheDocument();
        expect(screen.getAllByText('Daftar Isi')).toHaveLength(1);
        expect(screen.getAllByText('Belum ada bagian.')).toHaveLength(1);
        expect(screen.getByText('Belum ada tautan penting.')).toBeInTheDocument();
    });

    it('renders a Daftar Isi navigation in the mobile flow and the desktop sidebar', async () => {
        renderPage();

        await screen.findByRole('heading', { name: 'Tautan Penting' });

        const navigations = screen.getAllByRole('navigation', { name: 'Daftar Isi' });
        expect(navigations).toHaveLength(2);
        expect(navigations.filter((navigation) => navigation.closest('aside'))).toHaveLength(1);
        expect(navigations.filter((navigation) => navigation.closest('.lg\\:hidden'))).toHaveLength(
            1
        );
        expect(screen.getAllByRole('button', { name: 'Kumpulan Link MBKM' })).toHaveLength(2);
        expect(screen.getAllByRole('button', { name: 'Akademik' })).toHaveLength(2);
    });

    it('gives each section heading an id and scroll margin for anchor navigation', async () => {
        renderPage();

        await screen.findByRole('heading', { name: 'Tautan Penting' });

        const mbkmHeading = screen.getByRole('heading', { name: 'Kumpulan Link MBKM' });
        expect(mbkmHeading).toHaveAttribute('id', 'link-section-1');
        expect(screen.getByRole('heading', { name: 'Akademik' })).toHaveAttribute(
            'id',
            'link-section-2'
        );
    });

    it('scrolls to the section when a table of contents item is clicked', async () => {
        const scrollIntoView = vi.mocked(Element.prototype.scrollIntoView);
        scrollIntoView.mockClear();

        renderPage();

        await screen.findByRole('heading', { name: 'Tautan Penting' });

        screen.getAllByRole('button', { name: 'Kumpulan Link MBKM' })[0].click();

        expect(scrollIntoView).toHaveBeenCalledTimes(1);
        expect(scrollIntoView).toHaveBeenCalledWith({ behavior: 'smooth', block: 'start' });
        expect(scrollIntoView.mock.instances[0]).toBe(document.getElementById('link-section-1'));
    });
});
