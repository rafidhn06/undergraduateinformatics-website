import { type ReactNode } from 'react';

import { QueryClient, QueryClientProvider } from '@tanstack/react-query';

import { render, renderHook, screen, waitFor } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { beforeEach, describe, expect, it, vi } from 'vitest';

import { MsFormField } from '../components/MsFormField';
import { type MsFormQuestion } from '../types/ms-forms';
import { useMsFormLogic } from './useMsFormLogic';

const questions: MsFormQuestion[] = [
    {
        id: 'tanggal',
        title: { text: 'Tanggal' },
        subtitle: null,
        type: 'date',
        required: true,
        multiple: false,
        choices: [],
    },
    {
        id: 'shift',
        title: { text: 'Shift' },
        subtitle: null,
        type: 'choice',
        required: true,
        multiple: false,
        choices: [
            { value: 'pagi', label: { text: 'Pagi' }, branchTargetId: null },
            { value: 'siang', label: { text: 'Siang' }, branchTargetId: null },
        ],
    },
    {
        id: 'nama',
        title: { text: 'Nama' },
        subtitle: null,
        type: 'text',
        required: true,
        multiple: false,
        choices: [],
    },
];

const wrapper = ({ children }: { children: ReactNode }) => {
    const queryClient = new QueryClient({
        defaultOptions: {
            mutations: { retry: false },
            queries: { retry: false },
        },
    });

    return <QueryClientProvider client={queryClient}>{children}</QueryClientProvider>;
};

const dateQuestion = questions[0];
const shiftQuestion = questions[1];

function AvailabilityHarness({
    check,
}: {
    check: (date: string, shift: string) => Promise<boolean>;
}) {
    const logic = useMsFormLogic({
        questions,
        submitUrl: '/api/feedback',
        extension: {
            availabilityCheck: {
                dateQuestionId: dateQuestion.id,
                shiftQuestionId: shiftQuestion.id,
                check,
            },
        },
    });

    return (
        <form>
            <MsFormField question={dateQuestion} control={logic.control} />
            <MsFormField question={shiftQuestion} control={logic.control} />
            <span data-testid="unavailable">{String(logic.availabilityUnavailable)}</span>
        </form>
    );
}

describe('useMsFormLogic', () => {
    beforeEach(() => {
        vi.clearAllMocks();
    });

    it('computes extra field errors and activates the error flag', async () => {
        const extension = {
            fieldExtraErrors: (values: Record<string, string | string[]>) => ({
                nama: values.nama ? null : 'Nama harus diisi.',
            }),
        };

        const { result } = renderHook(
            () => useMsFormLogic({ questions, submitUrl: '/api/feedback', extension }),
            { wrapper }
        );

        expect(result.current.extraFieldErrors.nama).toBe('Nama harus diisi.');
        expect(result.current.extraErrorActive).toBe(true);
    });

    it('marks availability as unavailable when the check resolves false', async () => {
        const check = vi.fn().mockResolvedValue(false);
        const user = userEvent.setup();

        render(<AvailabilityHarness check={check} />, { wrapper });

        await user.type(screen.getByLabelText('Tanggal'), '2026-09-08');
        await user.click(screen.getByRole('radio', { name: 'Pagi' }));

        await waitFor(() => {
            expect(check).toHaveBeenCalledWith('2026-09-08', 'pagi');
        });

        await waitFor(() => {
            expect(screen.getByTestId('unavailable')).toHaveTextContent('true');
        });
    });
});
