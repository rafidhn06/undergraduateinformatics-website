import axios from 'axios';
import { getISODay } from 'date-fns';

import { useMsFormLogic } from '@/hooks/useMsFormLogic';
import {
    type MsFormQuestion,
    type MsFormSection,
    type ReservationMetadata,
} from '@/types/ms-forms';

import { translateReservationError } from './reservation-errors';

const ISO_DAY_NAMES: Record<number, string> = {
    1: 'Senin',
    2: 'Selasa',
    3: 'Rabu',
    4: 'Kamis',
    5: 'Jumat',
    6: 'Sabtu',
    7: 'Minggu',
};

function allowedDayMessage(allowedDays: number[]): string {
    const names = allowedDays.map((day) => ISO_DAY_NAMES[day]).filter(Boolean);
    const joined =
        names.length > 2
            ? `${names.slice(0, -1).join(', ')}, dan ${names[names.length - 1]}`
            : names.join(' dan ');

    return `Reservasi hanya dapat dilakukan di hari ${joined}.`;
}

interface UseReservationFormOptions {
    questions: MsFormQuestion[];
    sections?: MsFormSection[];
    submitUrl: string;
    reservation: ReservationMetadata;
}

export function useReservationForm({
    questions,
    sections,
    submitUrl,
    reservation,
}: UseReservationFormOptions) {
    return useMsFormLogic({
        questions,
        sections,
        submitUrl,
        extension: {
            fieldErrorsMap: {
                date: reservation.dateQuestionId,
                shift: reservation.shiftQuestionId,
            },
            fieldErrorTranslator: translateReservationError,
            fieldExtraErrors: (values) => {
                const date = values[reservation.dateQuestionId] as string | undefined;
                if (!date || !/^\d{4}-\d{2}-\d{2}$/.test(date)) {
                    return { [reservation.dateQuestionId]: null };
                }
                const day = getISODay(new Date(`${date}T00:00:00`));
                const allowed = reservation.allowedDays.includes(day);
                return {
                    [reservation.dateQuestionId]: allowed
                        ? null
                        : allowedDayMessage(reservation.allowedDays),
                };
            },
            availabilityCheck: {
                dateQuestionId: reservation.dateQuestionId,
                shiftQuestionId: reservation.shiftQuestionId,
                check: async (date, shift) => {
                    if (/^\d{4}-\d{2}-\d{2}$/.test(date)) {
                        const day = getISODay(new Date(`${date}T00:00:00`));
                        if (!reservation.allowedDays.includes(day)) {
                            return true;
                        }
                    }
                    const response = await axios.get('/api/reservation-form/availability', {
                        params: { date, shift },
                    });
                    return response.data?.data?.available === true;
                },
            },
        },
    });
}
