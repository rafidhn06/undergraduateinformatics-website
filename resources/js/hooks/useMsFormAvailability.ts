import { useQuery } from '@tanstack/react-query';

export const UNAVAILABLE_MESSAGE = 'Jadwal pada tanggal dan sesi ini sudah terisi.';

interface AvailabilityOptions {
    dateValue: string | undefined;
    shiftValue: string | undefined;
    check: (date: string, shift: string) => Promise<boolean>;
}

export function useMsFormAvailability({ dateValue, shiftValue, check }: AvailabilityOptions) {
    const enabled = Boolean(dateValue && shiftValue);
    const query = useQuery({
        queryKey: ['reservation-availability', dateValue, shiftValue],
        enabled,
        staleTime: 10000,
        retry: false,
        queryFn: async () => {
            if (!dateValue || !shiftValue) {
                return true;
            }
            return check(dateValue, shiftValue);
        },
    });

    return {
        availabilityUnavailable: Boolean(enabled && query.isSuccess && query.data === false),
    };
}
