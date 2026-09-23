import { ArticleContainer } from '@/components/ArticleContainer';
import { TextButton } from '@/components/TextButton';

export function MsFormUnavailable() {
    return (
        <ArticleContainer>
            <p role="status" className="text-muted-foreground">
                Formulir sedang tidak tersedia. Silakan coba beberapa saat lagi.
            </p>
        </ArticleContainer>
    );
}

export function MsFormError() {
    return (
        <ArticleContainer>
            <p role="alert" className="text-muted-foreground">
                Terjadi kesalahan saat memuat formulir. Silakan coba lagi.
            </p>
        </ArticleContainer>
    );
}

interface MsFormSuccessProps {
    onReset: () => void;
}

export function MsFormSuccess({ onReset }: MsFormSuccessProps) {
    return (
        <ArticleContainer>
            <h1>Terima kasih!</h1>
            <p className="text-muted-foreground">Formulir Anda telah berhasil dikirim.</p>
            <div className="mt-4.5 md:mt-4">
                <TextButton variant="underline" onClick={onReset}>
                    Isi Formulir Lagi
                </TextButton>
            </div>
        </ArticleContainer>
    );
}
