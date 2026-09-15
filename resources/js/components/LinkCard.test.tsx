import { render, screen } from '@testing-library/react';
import { describe, expect, it, vi } from 'vitest';

import { type LinkSummary } from '../types/link';
import { LinkCard } from './LinkCard';

vi.mock('@tanstack/react-router', async () => {
    const { routerModuleMock } = await import('@/test/mocks');

    return routerModuleMock();
});

const link: LinkSummary = {
    id: 7,
    name: 'Portal Akademik',
    link: 'https://portal.telkomuniversity.ac.id/',
    updated_at: '2026-09-05T12:00:00.000Z',
    section: { id: 2, name: 'Akademik' },
};

describe('LinkCard', () => {
    it('renders name, section, and date without an image', () => {
        render(<LinkCard link={link} />);

        expect(screen.getByRole('heading', { name: 'Portal Akademik' })).toBeInTheDocument();
        expect(screen.getByText('Akademik')).toBeInTheDocument();
        expect(screen.getByText('5 Sep 2026')).toBeInTheDocument();
        expect(screen.queryByRole('img')).not.toBeInTheDocument();
    });

    it('places the date below the section', () => {
        render(<LinkCard link={link} />);

        const article = screen.getByRole('article');
        expect(article.lastElementChild).toBe(screen.getByText('5 Sep 2026'));
    });

    it('links the name to the external URL via an underline link', () => {
        render(<LinkCard link={link} />);

        const nameLink = screen.getByRole('link', { name: 'Portal Akademik' });
        expect(nameLink).toHaveAttribute('href', 'https://portal.telkomuniversity.ac.id/');
        expect(nameLink).toHaveAttribute('target', '_blank');
        expect(nameLink).toHaveAttribute('rel', 'noopener noreferrer');
        expect(nameLink).toHaveClass('text-blue-600', 'line-clamp-2', 'whitespace-normal');
    });

    it('clamps the name to two lines', () => {
        render(<LinkCard link={link} />);

        const name = screen.getByRole('link', { name: 'Portal Akademik' });
        expect(name).toHaveClass('line-clamp-2');
        expect(name).not.toHaveClass('block');
    });

    it('renders the section as an inline link to its anchor in a single-line truncating row', () => {
        render(<LinkCard link={link} />);

        const sectionRow = screen.getByRole('link', { name: 'Akademik' }).parentElement;
        expect(sectionRow).toHaveClass('truncate', 'space-x-2');

        const sectionLink = screen.getByRole('link', { name: 'Akademik' });
        expect(sectionLink).toHaveAttribute('href', '/links#link-section-2');
        expect(sectionLink).toHaveClass('inline', 'text-muted-foreground', 'hover:text-foreground');
    });

    it('isolates the card from the typeset typography styles', () => {
        render(<LinkCard link={link} />);

        const article = screen.getByRole('article');
        expect(article).toHaveClass('not-typeset');
    });
});
