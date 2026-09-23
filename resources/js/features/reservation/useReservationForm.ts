import { useMsFormLogic } from '@/hooks/useMsFormLogic';
import type { MsFormQuestion, MsFormSection, ReservationMetadata } from '@/types/ms-forms';

import { checkReservationAvailability, dateExtraError, translateReservationError } from './intake';

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
            fieldExtraErrors: (values) => dateExtraError(reservation, values),
            availabilityCheck: {
                dateQuestionId: reservation.dateQuestionId,
                shiftQuestionId: reservation.shiftQuestionId,
                check: (date, shift) => checkReservationAvailability(reservation, date, shift),
            },
        },
    });
}
