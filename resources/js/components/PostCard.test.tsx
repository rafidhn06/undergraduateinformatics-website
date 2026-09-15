import { render, screen } from '@testing-library/react';
import { describe, expect, it, vi } from 'vitest';

import { type PostSummary } from '../types/post';
import { PostCard } from './PostCard';

vi.mock('@tanstack/react-router', async () => {
    const actual =
        await vi.importActual<typeof import('@tanstack/react-router')>('@tanstack/react-router');

    return {
        ...actual,
        createLink: (Comp: any) =>
            function MockedLink({ to, params, ...props }: any) {
                const href =
                    typeof to === 'string' && params
                        ? to.replace(/\$[^/]+/g, (key: string) => params[key.slice(1)] ?? key)
                        : to;

                return <Comp href={href} {...props} />;
            },
    };
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

    it('places the date below the tags', () => {
        render(<PostCard post={post} />);

        const article = screen.getByRole('article');
        expect(article.lastElementChild).toBe(screen.getByText('5 Sep 2026'));
    });

    it('links the title to the post detail page via an underline link', () => {
        render(<PostCard post={post} />);

        const titleLink = screen.getByRole('link', { name: 'Pengumuman Beasiswa 2026' });
        expect(titleLink).toHaveAttribute('href', '/posts/pengumuman-beasiswa-2026');
        expect(titleLink).toHaveClass('text-blue-600', 'line-clamp-2', 'whitespace-normal');
    });

    it('clamps the title and subtitle to two lines', () => {
        render(<PostCard post={post} />);

        const title = screen.getByRole('link', { name: 'Pengumuman Beasiswa 2026' });
        expect(title).toHaveClass('line-clamp-2');
        expect(title).not.toHaveClass('block');

        const subtitle = screen.getByText('Pendaftaran beasiswa dibuka hingga akhir bulan.');
        expect(subtitle).toHaveClass('line-clamp-2');
    });

    it('renders tags as inline links in a single-line truncating row', () => {
        render(<PostCard post={post} />);

        const tagsRow = screen.getByRole('link', { name: 'Beasiswa' }).parentElement;
        expect(tagsRow).toHaveClass('truncate', 'space-x-2');

        const tagLink = screen.getByRole('link', { name: 'Beasiswa' });
        expect(tagLink).toHaveClass('inline', 'text-muted-foreground', 'hover:text-foreground');
    });

    it('isolates the card from the typeset typography styles', () => {
        render(<PostCard post={post} />);

        const article = screen.getByRole('article');
        expect(article).toHaveClass('not-typeset');
    });
});
