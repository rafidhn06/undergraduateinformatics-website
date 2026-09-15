import { useEffect, useRef } from 'react';

import type { Story, StoryDefault } from '@ladle/react';

import { type MsFormQuestion, type MsFormSection, type MsRichText } from '../types/ms-forms';
import { MsForm } from './MsForm';
import { MsFormError, MsFormSuccess, MsFormUnavailable } from './MsFormStates';
import {
    SUBMIT_LABEL,
    branchingPayload,
    richPayload,
    richTextPayload,
    simplePayload,
    submitFail,
    submitOk,
    toFormProps,
} from './ms-form-fixtures';

interface AutoSubmitFormProps {
    title: MsRichText;
    description: MsRichText | null;
    sections: MsFormSection[] | undefined;
    questions: MsFormQuestion[];
    submitUrl: string;
    prefill: Record<string, string>;
}

export default {
    title: 'Microsoft Form',
} satisfies StoryDefault;

function AutoSubmitForm({ prefill, ...formProps }: AutoSubmitFormProps) {
    const containerRef = useRef<HTMLDivElement>(null);

    useEffect(() => {
        const timer = window.setTimeout(() => {
            const form = containerRef.current?.querySelector('form');

            if (!form) {
                return;
            }

            for (const [id, value] of Object.entries(prefill)) {
                const element = form.querySelector<HTMLTextAreaElement>(`#${id}`);

                if (!element) {
                    continue;
                }

                const valueSetter = Object.getOwnPropertyDescriptor(
                    window.HTMLTextAreaElement.prototype,
                    'value'
                )?.set;

                valueSetter?.call(element, value);
                element.dispatchEvent(new Event('input', { bubbles: true }));
            }

            const submitButton = Array.from(form.querySelectorAll('button')).find((button) =>
                button.textContent?.includes(SUBMIT_LABEL)
            );

            submitButton?.click();
        }, 0);

        return () => window.clearTimeout(timer);
    }, [prefill]);

    return (
        <div ref={containerRef}>
            <MsForm {...formProps} />
        </div>
    );
}

export const Form: Story = () => <MsForm {...toFormProps(richPayload)} />;
Form.msw = submitOk;

export const Branching: Story = () => <MsForm {...toFormProps(branchingPayload)} />;
Branching.msw = submitOk;

export const RichText: Story = () => <MsForm {...toFormProps(richTextPayload)} />;
RichText.msw = submitOk;

export const LoadError: Story = () => <MsFormError />;

export const Unavailable: Story = () => <MsFormUnavailable />;

export const Success: Story = () => (
    <AutoSubmitForm {...toFormProps(simplePayload)} prefill={{ isi: 'Pelayanan sudah baik.' }} />
);
Success.msw = submitOk;

export const Error: Story = () => (
    <AutoSubmitForm {...toFormProps(simplePayload)} prefill={{ isi: 'Pelayanan sudah baik.' }} />
);
Error.msw = submitFail;

export const SuccessState: Story = () => <MsFormSuccess onReset={() => undefined} />;
