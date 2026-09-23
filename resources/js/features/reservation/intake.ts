import { getISODay } from 'date-fns';

import { httpGet } from '@/lib/http';
import type { MsFormValues, ReservationMetadata } from '@/types/ms-forms';

export { translateReservationError } from './reservation-errors';

export const RESERVATION_DATE_PATTERN = /^\d{4}-\d{2}-\d{2}$/;

const ISO_DAY_NAMES: Record<number, string> = {
    1: 'Senin',
    2: 'Selasa',
    3: 'Rabu',
    4: 'Kamis',
    5: 'Jumat',
    6: 'Sabtu',
    7: 'Minggu',
};

export function isReservationDate(value: unknown): value is string {
    return typeof value === 'string' && RESERVATION_DATE_PATTERN.test(value);
}

export function allowedDayMessage(allowedDays: number[]): string {
    const names = allowedDays.map((day) => ISO_DAY_NAMES[day]).filter(Boolean);
    const joined =
        names.length > 2
            ? `${names.slice(0, -1).join(', ')}, dan ${names[names.length - 1]}`
            : names.join(' dan ');

    return `Reservasi hanya dapat dilakukan di hari ${joined}.`;
}

export function dateExtraError(
    reservation: ReservationMetadata,
    values: MsFormValues
): Record<string, string | null> {
    const date = values[reservation.dateQuestionId];

    if (!isReservationDate(date)) {
        return { [reservation.dateQuestionId]: null };
    }

    const day = getISODay(new Date(`${date}T00:00:00`));

    return {
        [reservation.dateQuestionId]: reservation.allowedDays.includes(day)
            ? null
            : allowedDayMessage(reservation.allowedDays),
    };
}

export async function checkReservationAvailability(
    reservation: ReservationMetadata,
    date: string,
    shift: string
): Promise<boolean> {
    if (isReservationDate(date)) {
        const day = getISODay(new Date(`${date}T00:00:00`));

        if (!reservation.allowedDays.includes(day)) {
            return true;
        }
    }

    const body = await httpGet<{ data?: { available?: boolean } }>(
        '/api/reservation-form/availability',
        { date, shift }
    );

    return body?.data?.available === true;
}
