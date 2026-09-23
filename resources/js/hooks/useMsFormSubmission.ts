import { useCallback, useState } from 'react';

import { useMutation } from '@tanstack/react-query';

import axios from 'axios';

import { httpPost } from '../lib/http';
import { buildMsFormAnswers } from '../lib/ms-form-answers';
import { type MsFormQuestion, type MsFormSection, type MsFormValues } from '../types/ms-forms';

function submitErrorMessage(status: number | undefined, errors: unknown): string | null {
    if (status === 404) {
        return 'Formulir sedang tidak tersedia.';
    }

    if (errors) {
        return null;
    }

    return 'Gagal mengirim jawaban. Silakan coba beberapa saat lagi.';
}

export function useMsFormSubmission(
    submitUrl: string,
    sections: MsFormSection[] | undefined,
    questions: MsFormQuestion[]
) {
    const [submitError, setSubmitError] = useState<string | null>(null);
    const [fieldErrors, setFieldErrors] = useState<Record<string, string[]> | null>(null);

    const submitForm = useMutation({
        mutationFn: async (values: MsFormValues) => {
            await httpPost(submitUrl, {
                answers: buildMsFormAnswers(sections, questions, values),
            });
        },
        onError: (error) => {
            const status = axios.isAxiosError(error) ? error.response?.status : undefined;
            const data = axios.isAxiosError(error)
                ? (error.response?.data as Record<string, unknown> | undefined)
                : undefined;
            const errors = data?.errors as Record<string, string[]> | undefined;

            setFieldErrors(errors && typeof errors === 'object' ? errors : null);

            setSubmitError(submitErrorMessage(status, errors));
        },
    });

    const resetSubmitError = useCallback(() => {
        setSubmitError(null);
        setFieldErrors(null);
    }, []);

    return { submitForm, submitError, fieldErrors, resetSubmitError };
}
