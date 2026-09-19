import { MsFormUnavailable } from '@/components/MsFormStates';
import { useMsForm } from '@/hooks/useMsForm';
import { type ReservationFormPayload } from '@/types/ms-forms';

import { ReservationForm } from './ReservationForm';

export function ReservationPage() {
    const { data } = useMsForm<ReservationFormPayload>('/api/reservation-form');

    if (!data.isValid) {
        return <MsFormUnavailable />;
    }

    return (
        <ReservationForm
            title={data.title}
            description={data.description}
            sections={data.sections}
            questions={data.questions}
            submitUrl="/api/reservation-submissions"
            reservation={data.reservation}
        />
    );
}
