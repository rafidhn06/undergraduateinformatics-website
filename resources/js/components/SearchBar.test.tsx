import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { describe, expect, it, vi } from 'vitest';

import { SearchBar } from './SearchBar';

describe('SearchBar', () => {
    it('renders a text input with a search placeholder', () => {
        render(<SearchBar />);

        expect(screen.getByPlaceholderText('Cari...')).toBeInTheDocument();
    });

    it('forwards props to the input', () => {
        render(<SearchBar defaultValue="program" />);

        expect(screen.getByPlaceholderText('Cari...')).toHaveValue('program');
    });

    it('calls onSubmit with the current value when Enter is pressed', async () => {
        const user = userEvent.setup();
        const onSubmit = vi.fn();

        render(<SearchBar onSubmit={onSubmit} />);

        await user.type(screen.getByPlaceholderText('Cari...'), 'program{enter}');

        expect(onSubmit).toHaveBeenCalledWith('program');
    });
});
