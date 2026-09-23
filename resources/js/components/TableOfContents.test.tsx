import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { describe, expect, it, vi } from 'vitest';

import { TableOfContents } from './TableOfContents';

const items = [
    { id: 'link-section-1', label: 'Akademik' },
    { id: 'link-section-2', label: 'MBKM' },
    { id: 'link-section-6', label: 'Alumni' },
];

describe('TableOfContents', () => {
    it('renders a Daftar Isi navigation with an unordered list', () => {
        render(<TableOfContents items={items} onSelect={() => undefined} />);

        const navigation = screen.getByRole('navigation', { name: 'Daftar Isi' });
        expect(navigation.querySelector('ul')).toBeInTheDocument();
    });

    it('renders one button per item labelled with the label', () => {
        render(<TableOfContents items={items} onSelect={() => undefined} />);

        expect(screen.getByRole('button', { name: 'Akademik' })).toBeInTheDocument();
        expect(screen.getByRole('button', { name: 'MBKM' })).toBeInTheDocument();
        expect(screen.getByRole('button', { name: 'Alumni' })).toBeInTheDocument();
    });

    it('calls onSelect with the item id when a button is clicked', async () => {
        const user = userEvent.setup();
        const onSelect = vi.fn();

        render(<TableOfContents items={items} onSelect={onSelect} />);

        await user.click(screen.getByRole('button', { name: 'MBKM' }));

        expect(onSelect).toHaveBeenCalledTimes(1);
        expect(onSelect).toHaveBeenCalledWith('link-section-2');
    });

    it('shows a message instead of a navigation when there are no items', () => {
        render(<TableOfContents items={[]} onSelect={() => undefined} />);

        expect(screen.getByText('Daftar Isi')).toBeInTheDocument();
        expect(screen.getByText('Belum ada bagian.')).toBeInTheDocument();
        expect(screen.queryByRole('navigation', { name: 'Daftar Isi' })).not.toBeInTheDocument();
    });
});
