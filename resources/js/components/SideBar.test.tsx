import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { beforeEach, describe, expect, it, vi } from 'vitest';

import { SideBar } from './SideBar';

const { navigateMock } = vi.hoisted(() => ({ navigateMock: vi.fn() }));

vi.mock('@tanstack/react-router', async () => {
    const { routerModuleMock } = await import('@/test/mocks');

    return routerModuleMock({ useNavigate: () => navigateMock });
});

describe('SideBar', () => {
    beforeEach(() => {
        vi.clearAllMocks();
    });

    it('renders navigation links', () => {
        render(<SideBar isOpen onClose={() => undefined} />);

        expect(screen.getByRole('link', { name: 'Beranda' })).toHaveAttribute('href', '/');
        expect(screen.getByRole('link', { name: 'Informasi' })).toHaveAttribute('href', '/tags');
        expect(screen.getByRole('link', { name: 'Tautan' })).toHaveAttribute('href', '/links');
        expect(screen.getByRole('link', { name: 'Masukan' })).toHaveAttribute('href', '/feedback');
        expect(screen.getByRole('link', { name: 'Pertemuan' })).toHaveAttribute(
            'href',
            '/reservation'
        );
        expect(screen.getByRole('link', { name: 'Masuk' })).toHaveAttribute('href', '/admin/login');
    });

    it('calls onClose when a nav link is clicked', () => {
        const onClose = vi.fn();

        render(<SideBar isOpen onClose={onClose} />);

        screen.getByRole('link', { name: 'Beranda' }).click();

        expect(onClose).toHaveBeenCalledTimes(1);
    });

    it('navigates to the search page, clears the input, and closes the sidebar when Enter is pressed', async () => {
        const user = userEvent.setup();
        const onClose = vi.fn();

        render(<SideBar isOpen onClose={onClose} />);

        const input = screen.getByPlaceholderText('Cari...');
        await user.type(input, 'beasiswa{enter}');

        expect(navigateMock).toHaveBeenCalledWith({
            to: '/posts',
            search: { q: 'beasiswa', page: 1, perPage: 10 },
        });
        expect(input).toHaveValue('');
        expect(onClose).toHaveBeenCalledTimes(1);
    });
});
