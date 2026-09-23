import { describe, expect, it, vi } from 'vitest';

import { httpGet } from '@/lib/http';

import {
    allowedDayMessage,
    checkReservationAvailability,
    dateExtraError,
    isReservationDate,
} from './intake';

vi.mock('@/lib/http', () => ({ httpGet: vi.fn() }));

const reservation = {
    dateQuestionId: 'tanggal',
    shiftQuestionId: 'sesi',
    allowedDays: [1, 2, 4, 5],
};

describe('reservation intake', () => {
    it('rejects malformed dates and accepts YYYY-MM-DD', () => {
        expect(isReservationDate('2026-09-08')).toBe(true);
        expect(isReservationDate('08-09-2026')).toBe(false);
        expect(isReservationDate('')).toBe(false);
    });

    it('names the allowed days in Indonesian', () => {
        expect(allowedDayMessage([1, 2, 4, 5])).toBe(
            'Reservasi hanya dapat dilakukan di hari Senin, Selasa, Kamis, dan Jumat.'
        );
    });

    it('flags a disallowed weekday on the date field only', () => {
        expect(dateExtraError(reservation, { tanggal: '2026-09-13' })).toEqual({
            tanggal: 'Reservasi hanya dapat dilakukan di hari Senin, Selasa, Kamis, dan Jumat.',
        });
        expect(dateExtraError(reservation, { tanggal: '2026-09-08' })).toEqual({
            tanggal: null,
        });
    });

    it('short-circuits the availability check for disallowed weekdays', async () => {
        await expect(checkReservationAvailability(reservation, '2026-09-13', 'pagi')).resolves.toBe(
            true
        );
        expect(httpGet).not.toHaveBeenCalled();

        vi.mocked(httpGet).mockResolvedValue({ data: { available: false } });

        await expect(checkReservationAvailability(reservation, '2026-09-08', 'pagi')).resolves.toBe(
            false
        );
        expect(httpGet).toHaveBeenCalledWith('/api/reservation-form/availability', {
            date: '2026-09-08',
            shift: 'pagi',
        });
    });
});
