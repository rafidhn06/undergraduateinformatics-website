import type { ReactNode } from 'react';

import { QueryClient, QueryClientProvider } from '@tanstack/react-query';

import { render, screen, waitFor } from '@testing-library/react';
import { describe, expect, it, vi } from 'vitest';

import { UNAVAILABLE_MESSAGE, useMsFormAvailability } from './useMsFormAvailability';

const wrapper = ({ children }: { children: ReactNode }) => (
    <QueryClientProvider
        client={new QueryClient({ defaultOptions: { queries: { retry: false } } })}
    >
        {children}
    </QueryClientProvider>
);

function Harness({ check }: { check: (date: string, shift: string) => Promise<boolean> }) {
    const { availabilityUnavailable } = useMsFormAvailability({
        dateValue: '2026-09-08',
        shiftValue: 'pagi',
        check,
    });

    return <span data-testid="flag">{String(availabilityUnavailable)}</span>;
}

describe('useMsFormAvailability', () => {
    it('flags unavailable when the check resolves false and skips the query when empty', async () => {
        const check = vi.fn().mockResolvedValue(false);

        render(<Harness check={check} />, { wrapper });

        await waitFor(() => expect(check).toHaveBeenCalledWith('2026-09-08', 'pagi'));
        expect(await screen.findByTestId('flag')).toHaveTextContent('true');
        expect(UNAVAILABLE_MESSAGE).toBe('Jadwal pada tanggal dan sesi ini sudah terisi.');
    });
});
