import { render, screen } from '@testing-library/react';
import { describe, expect, it, vi } from 'vitest';

import { type Post } from '@/types/post';

import { PostContent } from './PostContent';

vi.mock('@tanstack/react-router', async () => {
    const { routerModuleMock } = await import('@/test/mocks');

    return routerModuleMock();
});

const basePost: Post = {
    id: 7,
    slug: 'pendaftaran-beasiswa-2026',
    title: 'Pendaftaran Beasiswa 2026',
    subtitle: 'Periode baru dibuka',
    body: '<p>Informasi pendaftaran beasiswa 2026.</p><p>Batas akhir 31 Oktober.</p>',
    image: 'images/placeholder.png',
    created_at: '2026-09-01T12:00:00.000Z',
    updated_at: '2026-09-01T12:00:00.000Z',
    tags: [{ id: 1, slug: 'beasiswa', name: 'Beasiswa' }],
};

describe('PostContent', () => {
    it('renders title, subtitle, body HTML, and the updated date', () => {
        render(<PostContent post={basePost} />);

        expect(
            screen.getByRole('heading', { name: 'Pendaftaran Beasiswa 2026' })
        ).toBeInTheDocument();
        expect(screen.getByText('Periode baru dibuka')).toBeInTheDocument();
        expect(screen.getByText('Informasi pendaftaran beasiswa 2026.')).toBeInTheDocument();
        expect(screen.getByText('1 Sep 2026')).toBeInTheDocument();
    });

    it('prepends the Diperbarui prefix only when the post was updated', () => {
        render(<PostContent post={{ ...basePost, updated_at: '2026-09-05T12:00:00.000Z' }} />);

        expect(screen.getByText('Diperbarui 5 Sep 2026')).toBeInTheDocument();
    });

    it('renders the hero image only when present', () => {
        const { rerender } = render(<PostContent post={basePost} />);

        expect(screen.getByRole('img', { name: 'Pendaftaran Beasiswa 2026' })).toBeInTheDocument();

        rerender(<PostContent post={{ ...basePost, image: null }} />);
        expect(screen.queryByRole('img')).not.toBeInTheDocument();
    });

    it('links each tag to its detail page', () => {
        render(<PostContent post={basePost} />);

        expect(screen.getByRole('link', { name: 'Beasiswa' })).toHaveAttribute(
            'href',
            '/tags/beasiswa'
        );
    });
});
