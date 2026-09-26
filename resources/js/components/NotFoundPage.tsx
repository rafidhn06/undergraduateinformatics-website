import { useNavigate } from '@tanstack/react-router';

import { TextButton } from './TextButton';

export function NotFoundPage() {
    const navigate = useNavigate();

    return (
        <div className="box-border">
            <div className="mx-auto mt-[calc(14vh+119px)] box-border w-full max-w-[600px] max-[701px]:px-[10%] max-[421px]:px-[5%] max-[421px]:portrait:mt-[calc(7vh+127px)] max-[421px]:portrait:mb-3 max-[421px]:portrait:px-6 [@media(max-height:560px)]:mt-[calc(7vh+127px)] [@media(max-height:560px)]:mb-3 [@media(max-height:560px)]:px-6 [@media(min-height:650px)]:max-[415px]:portrait:mt-[calc(10vh+127px)]">
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
