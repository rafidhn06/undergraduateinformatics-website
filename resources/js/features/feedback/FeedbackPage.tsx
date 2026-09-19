import { MsForm } from '@/components/MsForm';
import { MsFormUnavailable } from '@/components/MsFormStates';
import { useMsForm } from '@/hooks/useMsForm';

export function FeedbackPage() {
    const { data } = useMsForm('/api/feedback-form');

    if (!data.isValid) {
        return <MsFormUnavailable />;
    }

    return (
        <MsForm
            title={data.title}
            description={data.description}
            sections={data.sections}
            questions={data.questions}
            submitUrl="/api/feedback-submissions"
        />
    );
}
