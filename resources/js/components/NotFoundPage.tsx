import { useNavigate } from '@tanstack/react-router';

import { TextButton } from './TextButton';

export function NotFoundPage() {
    const navigate = useNavigate();

    return (
        <div className="flex min-h-screen flex-col items-center justify-center px-4 supports-[min-height:100dvh]:min-h-dvh supports-[min-height:100svh]:min-h-svh">
            <div className="box-border w-full max-w-[600px]">
                <h1 className="text-foreground text-3xl leading-tight font-semibold">
                    Halaman Tidak Ditemukan
                </h1>
                <p className="text-muted-foreground mt-4.5 md:mt-4">
                    Alamat mungkin salah atau sudah tidak tersedia.
                </p>
                <div className="mt-4.5 md:mt-4">
                    <TextButton variant="underline" onClick={() => navigate({ to: '/' })}>
                        Kembali ke Beranda
                    </TextButton>
                </div>
            </div>
        </div>
    );
}
