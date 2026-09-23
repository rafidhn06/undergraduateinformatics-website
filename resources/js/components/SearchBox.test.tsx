import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { describe, expect, it, vi } from 'vitest';

import { SearchBox } from './SearchBar';

describe('SearchBox', () => {
    it('calls onSubmit with the current value when Enter is pressed', async () => {
        const user = userEvent.setup();
        const onSubmit = vi.fn();

        render(<SearchBox onSubmit={onSubmit} />);

        await user.type(screen.getByPlaceholderText('Cari...'), 'program{enter}');

        expect(onSubmit).toHaveBeenCalledWith('program');
    });
});
