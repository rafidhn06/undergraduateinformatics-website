import type { Story, StoryDefault } from '@ladle/react';
import { msw } from '@ladle/react';

import { branchingPayload, richPayload, submitOk } from '../../components/ms-form-fixtures';
import { FeedbackPage } from './FeedbackPage';
import { FeedbackSkeleton } from './FeedbackStates';

const formHandlers = [
    msw.http.get('/api/feedback', () =>
        msw.HttpResponse.json({ status: 'success', data: richPayload })
    ),
    ...submitOk,
];

const branchingHandlers = [
    msw.http.get('/api/feedback', () =>
        msw.HttpResponse.json({ status: 'success', data: branchingPayload })
    ),
    ...submitOk,
];

export default {
    title: 'Feedback',
} satisfies StoryDefault;

export const Form: Story = () => <FeedbackPage />;
Form.msw = formHandlers;

export const Branching: Story = () => <FeedbackPage />;
Branching.msw = branchingHandlers;

export const Loading: Story = () => <FeedbackSkeleton />;
