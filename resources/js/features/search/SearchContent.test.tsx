import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { describe, expect, it, vi } from 'vitest';

import { type PostListMeta } from '../post/page-data';
import { SearchContent } from './SearchContent';

const { navigateMock } = vi.hoisted(() => ({ navigateMock: vi.fn() }));

vi.mock('@tanstack/react-router', async () => {
    const { routerModuleMock } = await import('@/test/mocks');

    return routerModuleMock({ useNavigate: () => navigateMock });
});

const posts = [
    {
        id: 8,
        slug: 'beasiswa-luar-negeri',
        title: 'Beasiswa Luar Negeri',
        subtitle: 'Daftar sekarang',
        updated_at: '2026-09-01T12:00:00.000Z',
        tags: [{ id: 1, slug: 'beasiswa', name: 'Beasiswa' }],
    },
    {
        id: 7,
        slug: 'pendaftaran-beasiswa-2026',
        title: 'Pendaftaran Beasiswa 2026',
        subtitle: 'Periode baru dibuka',
        updated_at: '2026-04-05T12:00:00.000Z',
        tags: [{ id: 1, slug: 'beasiswa', name: 'Beasiswa' }],
    },
];

const meta: PostListMeta = { currentPage: 2, perPage: 10, total: 2, lastPage: 5 };

describe('SearchContent', () => {
    it('renders a search bar seeded with the query and the total result count', () => {
        render(
            <SearchContent
                q="beasiswa"
                result={{ posts, meta }}
                onSearch={vi.fn()}
                onPageChange={vi.fn()}
            />
        );

        expect(screen.getByPlaceholderText('Cari...')).toHaveValue('beasiswa');
        expect(screen.getByText('2 hasil')).toBeInTheDocument();
    });

    it('calls onSearch with the typed query when Enter is pressed', async () => {
        const user = userEvent.setup();
        const onSearch = vi.fn();
        render(
            <SearchContent
                q="beasiswa"
                result={{ posts, meta }}
                onSearch={onSearch}
                onPageChange={vi.fn()}
            />
        );

        const input = screen.getByPlaceholderText('Cari...');
        await user.clear(input);
        await user.type(input, 'kurikulum{enter}');

        expect(onSearch).toHaveBeenCalledWith('kurikulum');
    });

    it('renders each post as a link to its detail page', () => {
        render(
            <SearchContent
                q="beasiswa"
                result={{ posts, meta }}
                onSearch={vi.fn()}
                onPageChange={vi.fn()}
            />
        );

        expect(screen.getByRole('link', { name: 'Beasiswa Luar Negeri' })).toHaveAttribute(
            'href',
            '/posts/beasiswa-luar-negeri'
        );
        expect(screen.getByRole('link', { name: 'Pendaftaran Beasiswa 2026' })).toHaveAttribute(
            'href',
            '/posts/pendaftaran-beasiswa-2026'
        );
    });

    it('shows the empty-query prompt and lists recent posts when q is empty', () => {
        render(
            <SearchContent
                q=""
                result={{ posts, meta }}
                onSearch={vi.fn()}
                onPageChange={vi.fn()}
            />
        );

        expect(screen.getByText('Masukkan kata kunci untuk mencari.')).toBeInTheDocument();
        expect(screen.getByRole('link', { name: 'Beasiswa Luar Negeri' })).toBeInTheDocument();
    });

    it('shows a no-results message when the query matches nothing', () => {
        render(
            <SearchContent
                q="tidakada"
                result={{ posts: [], meta: { ...meta, total: 0 } }}
                onSearch={vi.fn()}
                onPageChange={vi.fn()}
            />
        );

        expect(screen.getByText('Tidak ada hasil untuk “tidakada”')).toBeInTheDocument();
        expect(
            screen.queryByRole('link', { name: 'Beasiswa Luar Negeri' })
        ).not.toBeInTheDocument();
    });

    it('shows the page indicator', () => {
        render(
            <SearchContent
                q="beasiswa"
                result={{ posts, meta }}
                onSearch={vi.fn()}
                onPageChange={vi.fn()}
            />
        );

        expect(screen.getByText('2 dari 5')).toBeInTheDocument();
    });

    it('disables the previous button on the first page and the next button on the last page', () => {
        const { rerender } = render(
            <SearchContent
                q="beasiswa"
                result={{ posts, meta: { ...meta, currentPage: 1 } }}
                onSearch={vi.fn()}
                onPageChange={vi.fn()}
            />
        );

        expect(screen.getByRole('button', { name: 'Kembali' })).toBeDisabled();
        expect(screen.getByRole('button', { name: 'Lanjut' })).toBeEnabled();

        rerender(
            <SearchContent
                q="beasiswa"
                result={{ posts, meta: { ...meta, currentPage: 5 } }}
                onSearch={vi.fn()}
                onPageChange={vi.fn()}
            />
        );

        expect(screen.getByRole('button', { name: 'Kembali' })).toBeEnabled();
        expect(screen.getByRole('button', { name: 'Lanjut' })).toBeDisabled();
    });

    it('calls onPageChange with the next page when Lanjut is clicked', () => {
        const onPageChange = vi.fn();
        render(
            <SearchContent
                q="beasiswa"
                result={{ posts, meta }}
                onSearch={vi.fn()}
                onPageChange={onPageChange}
            />
        );

        screen.getByRole('button', { name: 'Lanjut' }).click();

        expect(onPageChange).toHaveBeenCalledWith(3);
    });
});
