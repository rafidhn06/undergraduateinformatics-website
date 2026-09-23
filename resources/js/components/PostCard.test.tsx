import { render, screen } from '@testing-library/react';
import { describe, expect, it, vi } from 'vitest';

import { type PostSummary } from '../types/post';
import { PostCard } from './PostCard';

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
    tags: [
        { id: 1, slug: 'beasiswa', name: 'Beasiswa' },
        { id: 2, slug: 'akademik', name: 'Akademik' },
    ],
};

describe('PostCard', () => {
    it('renders title, subtitle, date, and tag links without an image', () => {
        render(<PostCard post={post} />);

        expect(
            screen.getByRole('heading', { name: 'Pengumuman Beasiswa 2026' })
        ).toBeInTheDocument();
        expect(
            screen.getByText('Pendaftaran beasiswa dibuka hingga akhir bulan.')
        ).toBeInTheDocument();
        expect(screen.getByText('5 Sep 2026')).toBeInTheDocument();
        expect(screen.queryByRole('img')).not.toBeInTheDocument();
        expect(screen.getByRole('link', { name: 'Beasiswa' })).toHaveAttribute(
            'href',
            '/tags/beasiswa'
        );
        expect(screen.getByRole('link', { name: 'Akademik' })).toHaveAttribute(
            'href',
            '/tags/akademik'
        );
    });

    it('links the title to the post detail page via an underline link', () => {
        render(<PostCard post={post} />);

        const titleLink = screen.getByRole('link', { name: 'Pengumuman Beasiswa 2026' });
        expect(titleLink).toHaveAttribute('href', '/posts/pengumuman-beasiswa-2026');
    });
});
