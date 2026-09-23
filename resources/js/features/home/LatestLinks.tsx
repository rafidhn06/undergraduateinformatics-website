import { Link } from '@/components/Link';
import { LinkCard } from '@/components/LinkCard';
import { type LinkSummary } from '@/types/link';

interface LatestLinksProps {
    links: LinkSummary[];
}

export function LatestLinks({ links }: LatestLinksProps) {
    return (
        <section aria-labelledby="latest-links-heading" className="flex flex-col">
            <h2 id="latest-links-heading">
                <Link
                    variant="fade"
                    to="/links"
                    className="font-heading text-foreground hover:text-muted-foreground text-[22.5px] leading-[1.4] font-semibold md:text-[20px]"
                >
                    Tautan Terbaru
                </Link>
            </h2>
            {links.length === 0 ? (
                <p role="status" className="text-muted-foreground mt-[22.5px] md:mt-5">
                    Belum ada tautan penting.
                </p>
            ) : (
                <ul className="mt-[22.5px] flex flex-col gap-4 md:mt-5">
                    {links.map((link) => (
                        <li key={link.id}>
                            <LinkCard link={link} />
                        </li>
                    ))}
                </ul>
            )}
        </section>
    );
}
